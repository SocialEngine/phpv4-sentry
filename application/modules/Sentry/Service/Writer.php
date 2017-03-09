<?php

class Module_Sentry_Service_Writer extends Zend_Log_Writer_Abstract
{
    private static $sentry;

    public function __construct($config)
    {
        if (!self::$sentry) {
            if (!class_exists('Raven_Client', false)) {
                require(__DIR__ . '/../vendor/autoload.php');
            }

            self::$sentry = new Raven_Client($config['dsn']);
            $errorHandler = new Raven_ErrorHandler(self::$sentry);
            $errorHandler->registerExceptionHandler();
            $errorHandler->registerErrorHandler();
            $errorHandler->registerShutdownFunction();
        }
    }

    static public function factory($config)
    {
        return new self($config);
    }

    protected function _write($event)
    {

    }
}
