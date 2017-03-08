<?php

require_once __DIR__ . '/../development/cli.php';

$base = __DIR__ . '/../';
foreach (scandir($base . 'application/packages/') as $file) {
    if (preg_match('/^module-Sentry-([0-9\.]+)\.json$/i', $file) && file_exists($base . $file)) {
        unlink($base . $file);
        echo shell_exec('git rm -f ' . $base . $file);
    }
}

$package = build_package_file(
    $base . 'application/modules/Sentry/settings/manifest.php'
);

$packageFileName = APPLICATION_PATH . '/application/packages/' . $package->getKey() . '.json';
file_put_contents($packageFileName, $package->toString('json'));

echo shell_exec('git add -f ' . $packageFileName);
echo shell_exec('ls -ala');

