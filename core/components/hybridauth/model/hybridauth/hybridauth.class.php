<?php

use Hybridauth\Adapter\OAuth2 as OAuth2;

class HybridAuth
{
    /** @var modX $modx */
    public $modx;
    /** @var array $config */
    public $config = [];
    /** @var array $adapters */
    public $adapters = [];
    /** @var array $initialized */
    public $initialized = [];


    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;

        $corePath = MODX_CORE_PATH . 'components/hybridauth/';
        $assetsUrl = MODX_ASSETS_URL . 'components/hybridauth/';
        $this->modx->lexicon->load('hybridauth:default', 'core:user');

        $this->config = array_merge([
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
        ], $config);

        $this->loadHybridAuth();
        $_SESSION['HybridAuth'][$this->modx->context->key] = $this->config;
    }


    /**
     * Custom exception handler for Hybrid_Auth
     *
     * @param Throwable $e
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
    public function initialize($ctx = 'web', $scriptProperties = [])
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
     * Loads HybridAuth adapters
     */
    public function loadHybridAuth()
    {
        if (!class_exists('OAuth2')) {
            require_once MODX_CORE_PATH . 'components/hybridauth/vendor/autoload.php';
        }

        if (!empty($this->config['providers'])) {
            $providers = array_map('trim', explode(',', $this->config['providers']));
        } else {
            $providers = [];
            foreach ($this->modx->config as $option => $value) {
                if (strpos($option, 'ha.keys.') === 0) {
                    $providers[] = substr($option, 8);
                }
            }
        }
        foreach ($providers as $provider) {
            if ($config = $this->modx->getOption('ha.keys.' . $provider)) {
                $config = json_decode($config, true);
                $class = '\Hybridauth\Provider\\' . $provider;
                if (is_array($config) && class_exists($class)) {
                    if (!isset($config['keys'])) {
                        $config = [
                            'keys' => [
                                'id' => isset($config['key'])
                                    ? $config['key']
                                    : $config['id'],
                                'secret' => $config['secret'],
                            ],
                        ];
                    }

                    try {
                        $config['callback'] = $this->modx->getOption('site_url') . '?hauth.done=' . $provider;
                        $this->adapters[$provider] = new $class($config);
                    } catch (Exception $e) {
                        $this->exceptionHandler($e);
                    }
                }
            }
        }
        $_SESSION['HA'] = [];
    }


    /**
     * Checks and login user. Also creates/updated user services profiles
     *
     * @param string $provider Remote service to login
     */
    public function Login($provider)
    {
        try {
            if (isset($this->adapters[$provider])) {
                /** @var OAuth2 $adapter */
                $adapter = $this->adapters[$provider];
                $adapter->authenticate();
            }
        } catch (Exception $e) {
            $this->exceptionHandler($e);
        }

        if (empty($adapter) || !$adapter->isConnected()) {
            $this->Refresh('login');
            return;
        }

        unset($_SESSION['HybridAuth']['error']);
        try {
            if ($profile = $adapter->getUserProfile()) {
                $profile = json_decode(json_encode($profile), true);
            }
        } catch (Exception $e) {
            $this->exceptionHandler($e);
            $this->Refresh('login');
        }
        $profile['provider'] = $provider;


        /** @var haUserService $service */
        $service = $this->modx->getObject('haUserService', [
            'identifier' => $profile['identifier'],
            'provider' => $profile['provider'],
        ]);
        if (!$service) {
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
                    $_SESSION['HybridAuth']['error'] = $msg;
                }
            } else {
                // Create a new user and add this record to him
                $username = !empty($profile['identifier']) ?
                    trim($profile['identifier'])
                    : md5(rand(8, 10));
                if ($exists = $this->modx->getCount('modUser', ['username' => $username])) {
                    for ($i = 1; $i <= 10; $i++) {
                        $tmp = $username . $i;
                        if (!$this->modx->getCount('modUser', ['username' => $tmp])) {
                            $username = $tmp;
                            break;
                        }
                    }
                }
                $arr = [
                    'username' => $username,
                    'fullname' => !empty($profile['lastName'])
                        ? $profile['firstName'] . ' ' . $profile['lastName']
                        : $profile['firstName'],
                    'dob' => !empty($profile['birthDay']) && !empty($profile['birthMonth']) && !empty($profile['birthYear'])
                        ? $profile['birthYear'] . '-' . $profile['birthMonth'] . '-' . $profile['birthDay']
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
                ];
                if (!$this->modx->getOption('ha.register_users', null, true)) {
                    $_SESSION['HybridAuth']['error'] = $this->modx->lexicon('ha_register_disabled');
                } else {
                    $response = $this->runProcessor('web/user/create', $arr);
                    if ($response->isError()) {
                        $msg = implode(', ', $response->getAllErrors());
                        $this->modx->log(modX::LOG_LEVEL_ERROR,
                            '[HybridAuth] Unable to create user ' . print_r($arr, 1) . '. Message: ' . $msg
                        );
                        $_SESSION['HybridAuth']['error'] = $msg;
                    } else {
                        $login_data = [
                            'username' => $response->response['object']['username'],
                            'password' => md5(rand()),
                            'rememberme' => $this->config['rememberme'],
                        ];
                        $uid = $response->response['object']['id'];
                        $profile['internalKey'] = $uid;
                        $response = $this->runProcessor('web/service/create', $profile);
                        if ($response->isError()) {
                            $msg = implode(', ', $response->getAllErrors());
                            $this->modx->log(modX::LOG_LEVEL_ERROR,
                                '[HybridAuth] unable to save service profile for user ' . $uid . '. Message: ' . $msg
                            );
                            $_SESSION['HybridAuth']['error'] = $msg;
                        }
                    }
                }
            }
        } else {
            // Find and use connected MODX user
            if ($this->modx->user->isAuthenticated($this->modx->context->key)) {
                $uid = $this->modx->user->id;
            } else {
                $uid = $service->get('internalKey');
            }

            /** @var modUser $user */
            if ($user = $this->modx->getObject('modUser', $uid)) {
                $login_data = [
                    'username' => $user->get('username'),
                    'password' => md5(rand()),
                    'rememberme' => $this->config['rememberme'],
                ];
                $profile['id'] = $service->get('id');
                $profile['internalKey'] = $uid;
                $response = $this->runProcessor('web/service/update', $profile);
                if ($response->isError()) {
                    $msg = implode(', ', $response->getAllErrors());
                    $this->modx->log(modX::LOG_LEVEL_ERROR,
                        '[HybridAuth] unable to update service profile for user ' . $uid . '. Message: ' . $msg);
                    $_SESSION['HybridAuth']['error'] = $msg;
                }
            } else {
                $service->remove();
                $this->Login($provider);
            }
        }

        $this->modx->error->reset();
        if (empty($_SESSION['HybridAuth']['error']) && !$this->modx->user->isAuthenticated($this->modx->context->key) && !empty($login_data)) {
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
                $_SESSION['HybridAuth']['error'] = $msg;
            }
        }

        $this->Refresh('login');
    }


    /**
     * Destroys all sessions
     *
     * @return void
     */
    public function Logout()
    {
        try {
            /** @var OAuth2 $adapter */
            foreach ($this->adapters as $adapter) {
                $adapter->disconnect();
            }
        } catch (Exception $e) {
            $this->exceptionHandler($e);
        }

        $logout_data = [];
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
            $_SESSION['HybridAuth']['error'] = $msg;
        }
        $this->Refresh('logout');
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
                        if (preg_match('#(action|provider|hauth.action|hauth.done|state|code|error|error_description)+#i', $v, $matches)) {
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
        if (empty($this->adapters)) {
            return '';
        }

        $output = '';
        $url = $this->getUrl();
        $active = [];

        if ($this->modx->user->isAuthenticated($this->modx->context->key)) {
            $c = $this->modx->newQuery('haUserService', ['internalKey' => $this->modx->user->id]);
            $c->select('provider');
            if ($c->prepare() && $c->stmt->execute()) {
                while ($row = $c->stmt->fetch(PDO::FETCH_COLUMN)) {
                    $active[] = strtolower($row);
                };
            }
        }

        foreach ($this->adapters as $provider => $adapter) {
            $pls = [
                'login_url' => $url . 'login',
                'logout_url' => $url . 'logout',
                'unbind_url' => $url . 'unbind',
                'provider' => strtolower($provider),
                'title' => $provider,
            ];

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
    public function runProcessor($action = '', $scriptProperties = [])
    {
        $this->modx->error->reset();

        return $this->modx->runProcessor($action, $scriptProperties, [
                'processors_path' => $this->config['processorsPath'],
            ]
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
        array $array = [],
        $plPrefix = '',
        $prefix = '[[+',
        $suffix = ']]',
        $uncacheable = true
    ) {
        $result = ['pl' => [], 'vl' => []];

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
    public function getChunk($tpl, $params = [])
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
        $this->config['user_id'] = $user->id;
        $controller->addCss($this->config['cssUrl'] . 'mgr/main.css');
        $controller->addCss($this->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/hybridauth.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/utils.js');
        $controller->addJavascript($this->config['jsUrl'] . 'mgr/widgets/service.grid.js');
        $controller->addHtml('<script>HybridAuth.config=' . json_encode($this->config) . '</script>');
        $controller->addLexiconTopic('hybridauth:default');
    }
}
