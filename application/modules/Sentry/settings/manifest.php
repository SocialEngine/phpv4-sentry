<?php

return array(
    'package' => array(
        'type' => 'module',
        'name' => 'Sentry',
        'version' => '1.0.0',
        'path' => 'application/modules/Sentry',
        'title' => 'Sentry',
        'description' => 'Sentry',
        'author' => 'Webligo Developments',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sentry/settings/install.php',
            'class' => 'Sentry_Installer',
        ),
        'directories' => array(
            'application/modules/Sentry',
        )
    )
);
