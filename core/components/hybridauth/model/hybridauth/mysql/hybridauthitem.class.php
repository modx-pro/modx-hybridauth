<?php
/**
 * @package hybridauth
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/hybridauthitem.class.php');
class HybridAuthItem_mysql extends HybridAuthItem {}