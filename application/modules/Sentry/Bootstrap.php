<?php

class Sentry_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
    public function __construct($application)
    {
        parent::__construct($application);
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $vendor = __DIR__ . '/vendor/autoload.php';
        if (isset($settings->sentry)
            && file_exists($vendor)
            && !empty($settings->sentry['enabled'])
            && !empty($settings->sentry['dsn'])
        ) {
            require_once($vendor);

            $client = new Raven_Client($settings->sentry['dsn']);
            $error_handler = new Raven_ErrorHandler($client);
            $error_handler->registerExceptionHandler();
            $error_handler->registerErrorHandler();
            $error_handler->registerShutdownFunction();
        }
    }
}
