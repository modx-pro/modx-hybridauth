<?php

/**
 * @param $filename
 *
 * @return string
 */
function getSnippetContent($filename)
{
    $file = trim(file_get_contents($filename));
    preg_match('#\<\?php(.*)#is', $file, $data);

    return rtrim(rtrim(trim($data[1]), '?>'));
}


/**
 * Ensure Composer dependencies exist under the component core path.
 * Transport packages must ship vendor/; git does not.
 *
 * @param string $componentCore Absolute path to core/components/hybridauth
 * @param modX $modx
 * @return void
 */
function ensureComposerVendor($componentCore, modX $modx)
{
    $componentCore = rtrim($componentCore, '/');
    $autoload = $componentCore . '/vendor/autoload.php';
    if (is_readable($autoload)) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Composer vendor already present.');
        return;
    }

    $composerJson = $componentCore . '/composer.json';
    if (!is_readable($composerJson)) {
        $modx->log(modX::LOG_LEVEL_FATAL, 'Missing composer.json at ' . $composerJson);
    }

    $composer = trim((string)shell_exec('command -v composer 2>/dev/null'));
    if ($composer === '') {
        $modx->log(
            modX::LOG_LEVEL_FATAL,
            'vendor/autoload.php is missing and composer is not on PATH. ' .
            'Run: cd ' . $componentCore . ' && composer install --no-dev'
        );
    }

    $modx->log(modX::LOG_LEVEL_INFO, 'Running composer install --no-dev in ' . $componentCore);
    $cmd = escapeshellarg($composer) . ' install --no-dev --optimize-autoloader --no-interaction 2>&1';
    $output = [];
    $exitCode = 0;
    exec('cd ' . escapeshellarg($componentCore) . ' && ' . $cmd, $output, $exitCode);
    foreach ($output as $line) {
        $modx->log(modX::LOG_LEVEL_INFO, $line);
    }

    if ($exitCode !== 0 || !is_readable($autoload)) {
        $modx->log(
            modX::LOG_LEVEL_FATAL,
            'Composer install failed; vendor/autoload.php is required in the transport package (#54).'
        );
    }
}


/**
 * Recursive directory delete
 *
 * @param $dir
 */
function removeDir($dir)
{
    $dir = rtrim($dir, '/');
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != '.' && $object != '..') {
                if (is_dir($dir . '/' . $object)) {
                    removeDir($dir . '/' . $object);
                } else {
                    unlink($dir . '/' . $object);
                }
            }
        }
        rmdir($dir);
    }
}
