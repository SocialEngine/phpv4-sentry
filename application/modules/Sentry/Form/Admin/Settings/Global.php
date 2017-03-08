<?php

class Sentry_Form_Admin_Settings_Global extends Engine_Form
{
    public function init()
    {
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
}
