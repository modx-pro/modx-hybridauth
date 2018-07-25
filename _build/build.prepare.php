<?php
$root = dirname(dirname(__FILE__)) . '/';
require_once $root . '_build/includes/functions.php';
$base = $root . 'core/components/hybridauth/vendor/hybridauth/hybridauth/';
$add_providers = $base . 'additional-providers/';
$to = $base . 'hybridauth/Hybrid/';

// Clean base dir
if ($dirs = @scandir($add_providers)) {
    foreach ($dirs as $dir) {
        if (strpos($dir, 'hybridauth-') !== 0) {
            continue;
        }

        if ($dir != 'hybridauth-google-openid') {
            $subdirs = scandir($add_providers . $dir);
            foreach ($subdirs as $subdir) {
                if ($subdir[0] == '.') {
                    continue;
                }
                $from = "{$add_providers}{$dir}/{$subdir}";
                shell_exec("cp -rf $from $to");
            }
        }
    }
}
removeDir($add_providers);

$add_providers = $root . '_build/providers/';
$to = $base . 'hybridauth/Hybrid/Providers/';
if ($files = @scandir($add_providers)) {
    foreach ($files as $file) {
        if ($file[0] != '.') {
            $from = "{$add_providers}{$file}";
            shell_exec("cp -f $from $to");
        }
    }
}