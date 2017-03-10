<?php
/**
 * SocialEngine
 *
 * @category   Library_Sentry
 * @package    Sentry
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */

/**
 * Class Sentry_Writer
 *
 * @codingStandardsIgnoreStart
 */
class Sentry_Writer extends Zend_Log_Writer_Abstract
{
    // @codingStandardsIgnoreEnd

    /**
     * @var Raven_Client
     */
    private static $sentry;

    /**
     * Stores the last error message written to the log.
     *
     * @var array
     */
    public static $message = array();

    /**
     * Sentry_Writer constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        if (!self::$sentry) {
            if (!class_exists('Raven_Client', false)) {
                require(__DIR__ . '/vendor/autoload.php');
            }

            self::$sentry = new Raven_Client($config['dsn']);
            $errorHandler = new Raven_ErrorHandler(self::$sentry);
            $errorHandler->registerExceptionHandler();

            register_shutdown_function(array($this, 'setShutdownHandler'));
        }
    }

    /**
     * @inheritdoc
     */
    public static function factory($config)
    {
        return new self($config);
    }

    /**
     * Executes on shutdown and picks up if we have any final errors.
     */
    public function setShutdownHandler()
    {
        if (self::$message) {
            if (!class_exists('Raven_Stacktrace')) {
                spl_autoload_call('Raven_Stacktrace');
            }

            $message = self::$message['message'];
            $backtrace = self::$message['backtrace'];
            $stack = array();

            if (class_exists('Error', false)) {
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

            if (!$stack) {
                $stack = array(
                    'frames' => Raven_Stacktrace::get_stack_info($backtrace)
                );
            }

            self::$sentry->captureMessage($message, array(), array(
                'stacktrace' => $stack
            ));
        }
    }

    /**
     * @inheritdoc
     * @codingStandardsIgnoreStart
     */
    protected function _write($event)
    {
        // @codingStandardsIgnoreEnd

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
