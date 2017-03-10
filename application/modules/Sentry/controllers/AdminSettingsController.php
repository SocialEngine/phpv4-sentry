<?php
/**
 * SocialEngine
 *
 * @category   Module_Sentry
 * @package    Sentry
 * @copyright  Copyright 2006-2017 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */

class Sentry_AdminSettingsController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        $this->view->form = $form = new Sentry_Form_Admin_Settings_Global();

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $form->populate($settings->sentry);

        if ($form->getClass() == 'Sentry_Writer') {
            $form->populate(array_filter(array(
                'enabled' => $form->getConfig('enabled', false),
                'dsn' => $form->getConfig('dsn'),
            )));
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $values = $form->getValues();
            $settings->sentry = $values;

            if (isset($values['enabled']) && $values['enabled']) {
                $form->writeLogFile(array(
                    'class' => 'Sentry_Writer',
                    'config' => array(
                        'dsn' => $values['dsn']
                    )
                ));
            } else {
                $form->deleteLogFile();
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

        $form->addNotice('Your changes have been saved.');
    }
}
