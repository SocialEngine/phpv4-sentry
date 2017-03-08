<?php

class Sentry_AdminSettingsController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        $this->view->form = $form = new Sentry_Form_Admin_Settings_Global();

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $form->populate($settings->sentry);

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

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

        $form->addNotice('Your changes have been saved.');
    }
}
