<?php
/**
 * SocialEngine
 *
 * @category   Module_Sentry
 * @package    Sentry
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */

/**
 * Class Sentry_Form_Admin_Settings_Global
 *
 * @codingStandardsIgnoreStart
 */
class Sentry_Form_Admin_Settings_Global extends Engine_Form
{
    // @codingStandardsIgnoreEnd

    /**
     * Path to log.php file.
     *
     * @var string
     */
    private $logFile;

    /**
     * Current log values from file.
     *
     * @var array
     */
    private $log = array();

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->logFile = APPLICATION_PATH . '/application/settings/log.php';
        if (!file_exists($this->logFile)) {
            $content = "<?php\n";
            file_put_contents($this->logFile, $content);
            chmod($this->logFile, 0666);
        }

        if (!is_writable($this->logFile)) {
            return $this->addError('In order to save changes, the following file needs to have write access: ' .
                '/application/settings/log.php');
        }

        $this->log = require($this->logFile);

        $this->setTitle('Sentry Integration')
            ->setDescription('Track errors with Sentry.');

        $this->addElement('Select', 'enabled', array(
            'label' => 'Enable Sentry Integration?',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            )
        ));

        $this->addElement('Text', 'dsn', array(
            'label' => 'Sentry DSN'
        ));


        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

    /**
     * Returns the current Log Writer class.
     *
     * @return bool|string
     */
    public function getClass()
    {
        if (!isset($this->log['class'])) {
            return false;
        }
        return $this->log['class'];
    }

    /**
     * Get a config value.
     *
     * @param string $name Name of the config key.
     * @param null $default Default value if config does not exist.
     *
     * @return mixed
     */
    public function getConfig($name, $default = null)
    {
        if (!isset($this->log['config'])) {
            return $default;
        }

        return isset($this->log['config'][$name]) ? $this->log['config'][$name] : $default;
    }

    /**
     * Path to log file.
     *
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * Write to log config file.
     *
     * @param array $data Values to write to file.
     *
     * @return int|bool
     */
    public function writeLogFile($data)
    {
        $content = "<?php\n";
        $content .= "return " . var_export($data, true) . ";";
        $content .= "\n";

        return file_put_contents($this->logFile, $content);
    }

    /**
     * Delete the log config file.
     *
     * @return bool
     */
    public function deleteLogFile()
    {
        return unlink($this->logFile);
    }
}
