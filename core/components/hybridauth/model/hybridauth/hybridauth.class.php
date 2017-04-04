<?php

class HybridAuth
{

    /** @var Hybrid_Auth $Hybrid_Auth */
    var $Hybrid_Auth;
    /** @var array $initialized */
    public $initialized = array();


    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('hybridauth.core_path', $config, MODX_CORE_PATH . 'components/hybridauth/');
        $assetsUrl = $this->modx->getOption('hybridauth.assets_url', $config,
            MODX_ASSETS_URL . 'components/hybridauth/'
        );
        $this->modx->lexicon->load('hybridauth:default');
        $this->modx->lexicon->load('core:user');

        $this->config = array_merge(array(
            'corePath' => $corePath,
            'assetsUrl' => $assetsUrl,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php',

            'rememberme' => true,
            'groups' => '',
            'loginContext' => '',
            'addContexts' => '',
            'loginResourceId' => 0,
            'logoutResourceId' => 0,
            'providers' => '',
        ), $config);

        $response = $this->loadHybridAuth();
        if ($response !== true) {
            $this->modx->error->failure('[HybridAuth] ' . $response);
        }

        $this->modx->addPackage('hybridauth', $this->config['modelPath']);
        if (!empty($this->config['HA'])) {
            /** @noinspection PhpIncludeInspection */
            require_once MODX_CORE_PATH . 'components/hybridauth/vendor/autoload.php';
            try {
                $this->Hybrid_Auth = new Hybrid_Auth($this->config['HA']);
            } catch (Exception $e) {
                $this->exceptionHandler($e);
            }
        }

        $_SESSION['HybridAuth'][$this->modx->context->key] = $this->config;
    }


    /**
     * Custom exception handler for Hybrid_Auth
     *
     * @param Throwable $e
     *
     * @return void;
     */
    public function exceptionHandler(Throwable $e)
    {
        $code = $e->getCode();
        if ($code <= 6) {
            $level = modX::LOG_LEVEL_ERROR;
        } else {
            $level = modX::LOG_LEVEL_INFO;
        }

        $this->modx->log($level, '[HybridAuth] ' . $e->getMessage());
        $this->Refresh();
    }


    /**
     * Initializes component into different contexts.
     *
     * @access public
     *
     * @param string $ctx The context to load. Defaults to web.
     * @param array $scriptProperties Properties for initialization.
     *
     * @return boolean
     */
    public function initialize($ctx = 'web', $scriptProperties = array())
    {
        $this->config = array_merge($this->config, $scriptProperties);
        $this->config['ctx'] = $ctx;
        if (!empty($this->initialized[$ctx])) {
            return true;
        }
        switch ($ctx) {
            case 'mgr':
                break;
            default:
                $config = $this->makePlaceholders($this->config);
                if ($css = $this->modx->getOption('ha.frontend_css')) {
                    $this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
                }
                $this->initialized[$ctx] = true;
        }

        return true;
    }


    /**
     * Loads settings for Hybrid_Auth class
     *
     * @return bool|null|string
     */
    public function loadHybridAuth()
    {
        $keys = array();
        $tmp = array_map('trim', explode(',', $this->config['providers']));
        foreach ($tmp as $v) {
            if (!empty($v)) {
                $keys[$v] = 'ha.keys.' . ucfirst($v);
            }
        }

        $providers = array();
        // Get providers settings
        foreach ($this->modx->config as $k => $v) {
            if (strpos($k, 'ha.keys.') === 0) {
                $tmp = $this->modx->fromJSON($v);
                if (!is_array($tmp)) {
                    continue;
                }
                $providers[ucfirst(substr($k, 8))] = array(
                    'enabled' => true,
                    'keys' => $tmp,
                );
            }
        }

        // Get context providers settings due to MODX bug with JSON in context settings
        $condition = !empty($keys)
            ? array('key:IN' => $keys)
            : array('key:LIKE' => 'ha.keys.%');
        $condition['context_key'] = $this->modx->context->key;
        $q = $this->modx->newQuery('modContextSetting', $condition);
        $q->select('key,value');
        $tstart = microtime(true);
        if ($q->prepare() && $q->stmt->execute()) {
            $this->modx->queryTime += microtime(true) - $tstart;
            $this->modx->executedQueries++;
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $tmp = $this->modx->fromJSON($row['value']);
                $name = ucfirst(substr($row['key'], 8));
                if (is_array($tmp)) {
                    $providers[$name] = array(
                        'enabled' => true,
                        'keys' => $tmp,
                    );
                }
            }
        }

        if (empty($providers)) {
            return $this->modx->lexicon('ha_err_no_providers');
        } // Save specified order of links
        elseif (!empty($keys)) {
            $tmp = array();
            foreach ($keys as $k => $v) {
                if (isset($providers[ucfirst($k)])) {
                    $tmp[$k] = $providers[$k];
                }
            }
            $providers = $tmp;
        }

        $this->config['HA'] = array(
            'base_url' => !empty($this->config['redirectUri'])
                ? $this->config['redirectUri']
                : $this->modx->makeUrl($this->modx->getOption('site_start'), $this->modx->context->key, '', 'full'),
            'debug_mode' => !empty($this->config['debug']),
            'debug_file' => MODX_CORE_PATH . 'cache/logs/error.log',
            'providers' => $providers,
        );

        return true;
    }


    /**
     * Process Hybrid_Auth endpoint
     *
     * @return void
     */
    public function processAuth()
    {
        if (!empty($_SESSION['HA::STORE'])) {
            if (!class_exists('Hybrid_Endpoint')) {
                /** @noinspection PhpIncludeInspection */
                require_once MODX_CORE_PATH . 'components/hybridauth/vendor/autoload.php';
            }
            try {
                Hybrid_Endpoint::process();
            } catch (Exception $e) {
                $this->exceptionHandler($e);
            }
        }
    }


    /**
     * Checks and login user. Also creates/updated user services profiles
     *
     * @param string $provider Remote service to login
     *
     * @return void
     */
    public function Login($provider = '')
    {
        try {
            $this->Hybrid_Auth->authenticate($provider);
        } catch (Exception $e) {
            $this->exceptionHandler($e);
        }
        unset($_SESSION['HA']['error']);
        /** @var Hybrid_User_Profile $service */
        if ($profile = $this->getServiceProfile($provider)) {
            $profile['provider'] = $provider;

            // Checking for existing provider record in database
            /** @var haUserService $service */
            if (!$service = $this->modx->getObject('haUserService',
                array('identifier' => $profile['identifier'], 'provider' => $profile['provider']))
            ) {
                // Adding new record to current user
                if ($this->modx->user->isAuthenticated($this->modx->context->key)) {
                    $uid = $this->modx->user->id;
                    $profile['internalKey'] = $uid;

                    $response = $this->runProcessor('web/service/create', $profile);
                    if ($response->isError()) {
                        $msg = implode(', ', $response->getAllErrors());
                        $this->modx->log(modX::LOG_LEVEL_ERROR,
                            '[HybridAuth] unable to save service profile for user ' . $uid . '. Message: ' . $msg
                        );
                        $_SESSION['HA']['error'] = $msg;
                    }
                } // Create a new user and add this record to him
                else {
                    $username = !empty($profile['identifier']) ?
                        trim($profile['identifier'])
                        : md5(rand(8, 10));
                    if ($exists = $this->modx->getCount('modUser', array('username' => $username))) {
                        for ($i = 1; $i <= 10; $i++) {
                            $tmp = $username . $i;
                            if (!$this->modx->getCount('modUser', array('username' => $tmp))) {
                                $username = $tmp;
                                break;
                            }
                        }
                    }
                    $arr = array(
                        'username' => $username,
                        'fullname' => !empty($profile['lastName'])
                            ? $profile['firstName'] . ' ' . $profile['lastName']
                            : $profile['firstName'],
                        'dob' => !empty($profile['birthday']) && !empty($profile['birthmonth']) && !empty($profile['birthyear'])
                            ? $profile['birthyear'] . '-' . $profile['birthmonth'] . '-' . $profile['birthday']
                            : '',
                        'email' => !empty($profile['emailVerified'])
                            ? $profile['emailVerified']
                            : $profile['email'],
                        'photo' => !empty($profile['photoURL'])
                            ? $profile['photoURL']
                            : '',
                        'website' => !empty($profile['webSiteURL'])
                            ? $profile['webSiteURL']
                            : '',
                        'phone' => !empty($profile['phone'])
                            ? $profile['phone']
                            : '',
                        'address' => !empty($profile['address'])
                            ? $profile['address']
                            : '',
                        'country' => !empty($profile['country'])
                            ? $profile['country']
                            : '',
                        'state' => !empty($profile['region'])
                            ? $profile['region']
                            : '',
                        'city' => !empty($profile['city'])
                            ? $profile['city']
                            : '',
                        'zip' => !empty($profile['zip'])
                            ? $profile['zip']
                            : '',
                        'active' => 1,
                        'provider' => $profile,
                        'groups' => $this->config['groups'],
                    );
                    if (!$this->modx->getOption('ha.register_users', null, true)) {
                        $_SESSION['HA']['error'] = $this->modx->lexicon('ha_register_disabled');
                    } else {
                        $response = $this->runProcessor('web/user/create', $arr);
                        if ($response->isError()) {
                            $msg = implode(', ', $response->getAllErrors());
                            $this->modx->log(modX::LOG_LEVEL_ERROR,
                                '[HybridAuth] Unable to create user ' . print_r($arr, 1) . '. Message: ' . $msg
                            );
                            $_SESSION['HA']['error'] = $msg;
                        } else {
                            $login_data = array(
                                'username' => $response->response['object']['username'],
                                'password' => md5(rand()),
                                'rememberme' => $this->config['rememberme'],
                            );
                            $uid = $response->response['object']['id'];
                            $profile['internalKey'] = $uid;
                            $response = $this->runProcessor('web/service/create', $profile);
                            if ($response->isError()) {
                                $msg = implode(', ', $response->getAllErrors());
                                $this->modx->log(modX::LOG_LEVEL_ERROR,
                                    '[HybridAuth] unable to save service profile for user ' . $uid . '. Message: ' . $msg
                                );
                                $_SESSION['HA']['error'] = $msg;
                            }
                        }
                    }
                }
            } else {
                // Find and use binded MODX user
                if ($this->modx->user->isAuthenticated($this->modx->context->key)) {
                    $uid = $this->modx->user->id;
                } else {
                    $uid = $service->get('internalKey');
                }

                /** @var modUser $user */
                if ($user = $this->modx->getObject('modUser', $uid)) {
                    $login_data = array(
                        'username' => $user->get('username'),
                        'password' => md5(rand()),
                        'rememberme' => $this->config['rememberme'],
                    );
                    $profile['id'] = $service->get('id');
                    $profile['internalKey'] = $uid;
                    $response = $this->runProcessor('web/service/update', $profile);
                    if ($response->isError()) {
                        $msg = implode(', ', $response->getAllErrors());
                        $this->modx->log(modX::LOG_LEVEL_ERROR,
                            '[HybridAuth] unable to update service profile for user ' . $uid . '. Message: ' . $msg);
                        $_SESSION['HA']['error'] = $msg;
                    }
                } else {
                    $service->remove();
                    $this->Login($provider);
                }
            }

            $this->modx->error->errors = $this->modx->error->message = null;
            if (empty($_SESSION['HA']['error']) && !$this->modx->user->isAuthenticated($this->modx->context->key) && !empty($login_data)) {
                $_SESSION['HA']['verified'] = 1;
                if (!empty($this->config['loginContext'])) {
                    $login_data['login_context'] = $this->config['loginContext'];
                }
                if (!empty($this->config['addContexts'])) {
                    $login_data['add_contexts'] = $this->config['addContexts'];
                }

                // Login
                $_SESSION['HybridAuth']['verified'] = true;
                $response = $this->modx->runProcessor('security/login', $login_data);
                if ($response->isError()) {
                    $msg = implode(', ', $response->getAllErrors());
                    $this->modx->log(modX::LOG_LEVEL_ERROR,
                        '[HybridAuth] error login for user ' . $login_data['username'] . '. Message: ' . $msg);
                    $_SESSION['HA']['error'] = $msg;
                }
            }

            $this->Refresh('login');
        }
    }


    /**
     * Destroys all sessions
     *
     * @return void
     */
    public function Logout()
    {
        if (is_object($this->Hybrid_Auth)) {
            try {
                $this->Hybrid_Auth->logoutAllProviders();
            } catch (Exception $e) {
                $this->exceptionHandler($e);
            }
        }

        $logout_data = array();
        if (!empty($this->config['loginContext'])) {
            $logout_data['login_context'] = $this->config['loginContext'];
        }
        if (!empty($this->config['addContexts'])) {
            $logout_data['add_contexts'] = $this->config['addContexts'];
        }

        $response = $this->modx->runProcessor('security/logout', $logout_data);
        if ($response->isError()) {
            $msg = implode(', ', $response->getAllErrors());
            $this->modx->log(modX::LOG_LEVEL_ERROR,
                '[HybridAuth] logout error. Username: ' . $this->modx->user->get('username') . ', uid: ' . $msg);
            $_SESSION['HA']['error'] = $msg;
        }
        unset($_SESSION['HA::STORE'], $_SESSION['HA::CONFIG']);
        $this->Refresh('logout');
    }


    /**
     * Gets user profile from service
     *
     * @param string $provider Service provider, like Google, Twitter etc.
     *
     * @return array|boolean
     */
    function getServiceProfile($provider)
    {
        try {
            $providers = $this->Hybrid_Auth->getConnectedProviders();
            $providerId = ucfirst($provider);
            if (is_array($providers) && in_array($provider, $providers)) {
                /** @var Hybrid_Provider_Model $provider */
                $provider = $this->Hybrid_Auth->getAdapter($providerId);
                $profile = $provider->getUserProfile();
                $array = json_encode($profile);

                return json_decode($array, true);
            }
        } catch (Exception $e) {
            $this->exceptionHandler($e);
        }

        return false;
    }


    /**
     * Refreshes the current page. If set, can redirects user to logout/login resource.
     *
     * @param string $action The action to do
     *
     * @return void
     */
    public function Refresh($action = '')
    {
        $url = '';
        if ($action == 'login' && !empty($this->config['loginResourceId'])) {
            /** @var modResource $resource */
            if ($resource = $this->modx->getObject('modResource', (int)$this->config['loginResourceId'])) {
                $url = $this->modx->makeUrl($resource->id, $resource->context_key, '', 'full');
            }
        } elseif ($action == 'logout' && !empty($this->config['logoutResourceId'])) {
            /** @var modResource $resource */
            if ($resource = $this->modx->getObject('modResource', (int)$this->config['logoutResourceId'])) {
                $url = $this->modx->makeUrl($resource->id, $resource->context_key, '', 'full');
            }
        }

        if (empty($url)) {
            $request = preg_replace('#^' . $this->modx->getOption('base_url') . '#', '', $_SERVER['REQUEST_URI']);
            $url = $this->modx->getOption('site_url') . ltrim($request, '/');
            if ($pos = strpos($url, '?')) {
                $arr = explode('&', substr($url, $pos + 1));
                $url = substr($url, 0, $pos);
                if (count($arr) > 1) {
                    foreach ($arr as $k => $v) {
                        if (preg_match('/(action|provider|hauth_action)+/i', $v, $matches)) {
                            unset($arr[$k]);
                        }
                    }
                    if (!empty($arr)) {
                        $url = $url . '?' . implode('&', $arr);
                    }

                }
            }
        }

        $this->modx->sendRedirect($url);
    }


    /**
     * Returns working url
     *
     * @return mixed $url
     */
    public function getUrl()
    {
        $request = preg_replace('#^' . $this->modx->getOption('base_url') . '#', '', $_SERVER['REQUEST_URI']);
        $url = $this->modx->getOption('site_url') . ltrim(rawurldecode($request), '/');
        $url = preg_replace('#["\']#', '', strip_tags($url));

        $url .= strpos($url, '?')
            ? '&amp;hauth_action='
            : '?hauth_action=';

        return $url;
    }


    /**
     * @param string $tpl1
     * @param string $tpl2
     *
     * @return string
     */
    public function getProvidersLinks($tpl1 = 'tpl.HybridAuth.provider', $tpl2 = 'tpl.HybridAuth.provider.active')
    {
        if (empty($this->config['HA']['providers'])) {
            return '';
        }

        $output = '';
        $url = $this->getUrl();
        $active = array();

        if ($this->modx->user->isAuthenticated($this->modx->context->key)) {
            $q = $this->modx->newQuery('haUserService', array('internalKey' => $this->modx->user->id));
            $q->select('provider');
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
                    $active[] = strtolower($row);
                };
            }
        }

        $providers = array_keys($this->config['HA']['providers']);
        foreach ($providers as $provider) {
            $pls = array(
                'login_url' => $url . 'login',
                'logout_url' => $url . 'logout',
                'unbind_url' => $url . 'unbind',
                'provider' => strtolower($provider),
                'title' => ucfirst($provider),
            );

            $output .= !in_array($pls['provider'], $active)
                ? $this->getChunk($tpl1, $pls)
                : $this->getChunk($tpl2, $pls);
        }

        return $output;
    }


    /**
     * Sanitizes a string
     *
     * @param string $string The string to sanitize
     * @param integer $length The length of sanitized string
     *
     * @return string The sanitized string.
     */
    public function Sanitize($string = '', $length = 0)
    {
        $expr = '#[^-_a-zа-яёЁ0-9@\s\.\,\:\/\\\]+#iu';
        $sanitized = trim(preg_replace($expr, '', $string));

        return !empty($length)
            ? substr($sanitized, 0, $length)
            : $sanitized;
    }


    /**
     * Shorthand for load and run an processor in this component
     *
     * @param string $action
     * @param array $scriptProperties
     *
     * @return mixed
     */
    public function runProcessor($action = '', $scriptProperties = array())
    {
        $this->modx->error->errors = $this->modx->error->message = null;

        return $this->modx->runProcessor($action, $scriptProperties, array(
                'processors_path' => $this->config['processorsPath'],
            )
        );
    }


    /**
     * Transform array to placeholders
     *
     * @param array $array
     * @param string $plPrefix
     * @param string $prefix
     * @param string $suffix
     * @param bool $uncacheable
     *
     * @return array
     */
    public function makePlaceholders(
        array $array = array(),
        $plPrefix = '',
        $prefix = '[[+',
        $suffix = ']]',
        $uncacheable = true
    ) {
        $result = array('pl' => array(), 'vl' => array());

        $uncached_prefix = str_replace('[[', '[[!', $prefix);
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $result = array_merge_recursive($result,
                    $this->makePlaceholders($v, $plPrefix . $k . '.', $prefix, $suffix, $uncacheable));
            } else {
                $pl = $plPrefix . $k;
                $result['pl'][$pl] = $prefix . $pl . $suffix;
                $result['vl'][$pl] = $v;
                if ($uncacheable) {
                    $result['pl']['!' . $pl] = $uncached_prefix . $pl . $suffix;
                    $result['vl']['!' . $pl] = $v;
                }
            }
        }

        return $result;
    }


    /**
     * @param $tpl
     * @param array $params
     *
     * @return mixed|string
     */
    public function getChunk($tpl, $params = array())
    {
        /** @var pdoTools $pdoTools */
        if (class_exists('pdoTools') && $pdoTools = $this->modx->getService('pdoTools')) {
            return $pdoTools->getChunk($tpl, $params);
        }

        return $this->modx->getChunk($tpl, $params);
    }


    /**
     * @param modManagerController $controller
     * @param modUser $user
     */
    public function regManagerTab($controller, $user)
    {
        $controller->addCss($this->config['cssUrl'] . 'mgr/main.css');
        $controller->addCss($this->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/hybridauth.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/utils.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/widgets/service.grid.js');
        $controller->addLexiconTopic('hybridauth:default');

        if ($this->modx->getCount('modPlugin', array('name' => 'AjaxManager', 'disabled' => false))) {
            $controller->addHtml('
            <script type="text/javascript">
                HybridAuth.config = ' . json_encode($this->config) . ';
                Ext.onReady(function() {
                    window.setTimeout(function() {
                        var tab = Ext.getCmp("modx-user-tabs");
                        if (!tab) {return;}
                        tab.add({
                            title: _("ha.services"),
                            border: false,
                            items: [{
                                layout: "anchor",
                                border: false,
                                items: [{
                                    html: _("ha.services_tip"),
                                    border: false,
                                    bodyCssClass: "panel-desc"
                                }, {
                                    xtype: "hybridauth-grid-services",
                                    anchor: "100%",
                                    cls: "main-wrapper",
                                    userId: ' . intval($user->get('id')) . '
                                }]
                            }]
                        });
                    }, 10);
                });
            </script>');
        } else {
            $controller->addHtml('
            <script type="text/javascript">
                HybridAuth.config = ' . json_encode($this->config) . ';
                Ext.ComponentMgr.onAvailable("modx-user-tabs", function() {
                    this.on("beforerender", function() {
                        this.add({
                            title: _("ha.services"),
                            border: false,
                            items: [{
                                layout: "anchor",
                                border: false,
                                items: [{
                                    html: _("ha.services_tip"),
                                    border: false,
                                    bodyCssClass: "panel-desc"
                                }, {
                                    xtype: "hybridauth-grid-services",
                                    anchor: "100%",
                                    cls: "main-wrapper",
                                    userId: ' . intval($user->get('id')) . '
                                }]
                            }]
                        });
                    });
                });
            </script>');
        }
    }
}
