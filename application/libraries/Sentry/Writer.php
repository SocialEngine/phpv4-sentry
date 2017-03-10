<?php

class Sentry_Writer extends Zend_Log_Writer_Abstract
{
    private static $sentry;

    public static $message;

    public function __construct($config)
    {
        if (!self::$sentry) {
            if (!class_exists('Raven_Client', false)) {
                require(__DIR__ . '/vendor/autoload.php');
            }

            self::$sentry = new Raven_Client($config['dsn']);
            $errorHandler = new Raven_ErrorHandler(self::$sentry);
            $errorHandler->registerExceptionHandler();

            register_shutdown_function(array($this, 'setErrorHandler'));
        }
    }

    static public function factory($config)
    {
        return new self($config);
    }

    public function setErrorHandler()
    {
        if (self::$message) {

            if (!class_exists('Raven_Stacktrace')) {
                spl_autoload_call('Raven_Stacktrace');
            }

            $message = self::$message['message'];
            $stack = array();

            if (class_exists('Error', false)) {
                $backtrace = self::$message['backtrace'];
                foreach ($backtrace as $file) {
                    if (isset($file['function']) && $file['function'] == 'handleException' && isset($file['args'])) {
                        foreach ($file['args'] as $item) {
                            if ($item instanceof Error) {
                                $message = $item->getMessage();
                                $trace = array_merge(array(array(
                                    'file' => $item->getFile(),
                                    'line' => $item->getLine()
                                )), $item->getTrace());

                                $stack = array(
                                    'frames' => Raven_Stacktrace::get_stack_info($trace)
                                );

                                break 1;
                            }
                        }
                    }
                }

                if (isset(self::$message['exception'])) {
                    $e = self::$message['exception'];
                    if ($e instanceof Exception) {
                        $trace = array_merge(array(array(
                            'file' => $e->getFile(),
                            'line' => $e->getLine()
                        )), $e->getTrace());

                        $stack = array(
                            'frames' => Raven_Stacktrace::get_stack_info($trace)
                        );
                    }
                }
            }

            self::$sentry->captureMessage($message, array(), array(
                'stacktrace' => $stack
            ));
            //  self::$sentry->sendUnsentErrors();
        }
    }

    protected function _write($event)
    {
        $lines = explode("\n", trim($event['message']));
        if (preg_match('/^Error Code: (.*?)$/i', $lines[0])) {
            unset($lines[0]);
        }

        $message = implode("\n", $lines);
        self::$message = array(
            'message' => $message,
            'backtrace' => debug_backtrace(),
            'exception' => (isset($event['exception']) ? $event['exception'] : false)
        );
    }
}
