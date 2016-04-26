<?php

class Spamcontrol_AdminSettingsController extends Core_Controller_Action_Admin {

    public function commentAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spamcontrol_admin_main', array(), 'spamcontrol_admin_main_commentcontrol');

        $this->view->form = $form = new Spamcontrol_Form_SearchComments();

        $warnTable = Engine_Api::_()->getDbtable('warn', 'spamcontrol');



        $values = array();
        if ($form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values['item'] = 'core_comment';

        $paginator = $warnTable->getContentPaginator($values);

        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;
    }

    public function messageAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spamcontrol_admin_main', array(), 'spamcontrol_admin_main_privatemessagecontrol');


        $this->view->form = $form = new Spamcontrol_Form_SearchComments();
        $form->removeElement('plugins');

        $warnTable = Engine_Api::_()->getDbtable('warn', 'spamcontrol');



        $values = array();
        if ($form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values['item'] = 'messages_conversation';

        $paginator = $warnTable->getContentPaginator($values);

        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;
    }

    public function postAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spamcontrol_admin_main', array(), 'spamcontrol_admin_main_post');

        $this->view->form = $form = new Spamcontrol_Form_SearchComments();
        $form->removeElement('plugins');


        $values = array();
        if ($form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values['item'] = 'activity_action';
        $warnTable = Engine_Api::_()->getDbtable('warn', 'spamcontrol');
        $paginator = $warnTable->getContentPaginator($values);

        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;
    }

    public function blogAction() {
        $blog = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('blog');

        if (!$blog) {
            $this->view->error = '';
            return;
        }

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spamcontrol_admin_main', array(), 'spamcontrol_admin_main_blog');

        $this->view->form = $form = new Spamcontrol_Form_SearchComments();
        $form->removeElement('plugins');

        $values['item'] = 'blog';
        $warnTable = Engine_Api::_()->getDbtable('warn', 'spamcontrol');
        $paginator = $warnTable->getContentPaginator($values);

        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;
    }

    public function photoAction() {
        $album = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');

        if (!$album) {
            $this->view->error = '';
            return;
        }


        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spamcontrol_admin_main', array(), 'spamcontrol_admin_main_photo');


        $this->view->form = $form = new Spamcontrol_Form_SearchComments();
        $form->removeElement('plugins');
        $form->removeElement('url');

        $warnTable = Engine_Api::_()->getDbtable('warn', 'spamcontrol');



        $values = array();
        if ($form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values['item'] = 'album_photo';

        $paginator = $warnTable->getContentPaginator($values);

        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;
    }

    public function userAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spamcontrol_admin_main', array(), 'spamcontrol_admin_main_user');


        $this->view->namelegth = $items_count_username = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('spamcontrol.lengthname', 5);
        $this->view->emaillegth = $items_count_email = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('spamcontrol.lengthemail', 5);

        $table = Engine_Api::_()->getItemTable('user');
        $select = $table->select();

        $selectAll = $table->fetchAll($table->select());
        $users = array();

        foreach ($selectAll as $key => $value) {

            $email = ereg_replace("[^0-9]", "", $value->email);
            $name = ereg_replace("[^0-9]", "", $value->username);
            $displayname = ereg_replace("[^0-9]", "", $value->displayname);
            if (strlen($email) >= $items_count_email or strlen($name) >= $items_count_username or strlen($displayname) >= $items_count_username) {
                $users[$key] = $value;
            }
        }


        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('spamcontrol.page', 30);
        $paginator = Zend_Paginator::factory($users);
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;
    }

    public function multiModifyAction() {
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'message_' . $value) {

                    $messageTable = Engine_Api::_()->getDbtable('messages', 'messages');
                    if ($values['submit_button'] == 'delete') {
                        $db = $messageTable->getAdapter();
                        $db->beginTransaction();
                        try {

                            $recipients = Engine_Api::_()->getItem('messages_conversation', $value)->getRecipientsInfo();

                            foreach ($recipients as $r) {
                                $r->inbox_deleted = true;
                                $r->outbox_deleted = true;
                                $r->save();
                            }
                            $db->commit();
                        } catch (Exception $e) {
                            $db->rollback();
                            throw $e;
                        }
                    }
                }
            }
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'message'));
    }

    public function commentModifyAction() {
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'item_' . $value) {
                    $item = Engine_Api::_()->getItem($values['type'], $value);

                    if ($values['submit_button'] == 'delete') {
                        $item->delete();
                    }
                }
            }
        }
        if ($values['type'] == 'core_comment') {
            return $this->_helper->redirector->gotoRoute(array('action' => 'comment'));
        } elseif ($values['type'] == 'activity_action') {
            return $this->_helper->redirector->gotoRoute(array('action' => 'post'));
        } elseif ($values['type'] == 'blog') {
            return $this->_helper->redirector->gotoRoute(array('action' => 'blog'));
        } elseif ($values['type'] == 'album_photo') {
            return $this->_helper->redirector->gotoRoute(array('action' => 'photo'));
        } elseif ($values['type'] == 'activity_comment') {
            return $this->_helper->redirector->gotoRoute(array('action' => 'post'));
        }
    }

    public function messagewarnAction() {
        $item_id = $this->_getParam('item_id');
        $item_type = $this->_getParam('item_type');

        $item = Engine_Api::_()->getItem($item_type, $item_id);

        $this->_helper->layout->setLayout('default-simple');

        if (!$item) {
            return;
        }

        $this->view->form = $form = new Spamcontrol_Form_Warn(array(
                    'resource_id' => $item_id,
                    'resource_type' => $item_type
                        )
        );

        $user = $item->getOwner();

        $warnTable = Engine_Api::_()->getDbtable('warn', 'spamcontrol');

        if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost()))
            return;
        $values = array();

        $values = $form->getValues();
        $values['user_id'] = $user->getIdentity();
        $values['resource_id'] = $item_id;
        $values['resource_type'] = $item_type;

        $table = $item->getTable();


        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            if (!empty($values['action'])) {
                switch ($values['action']) {
                    case 'delete':
                        $item->delete();
                        $this->view->message = sprintf(Zend_Registry::get('Zend_Translate')->translate('The selected %s have been deleted.'), $item->getShortType());
                        
                        

                        break;
                    case 'deleteall':
                        $warnTable->deleteAll($item);

                        $this->view->message = sprintf(Zend_Registry::get('Zend_Translate')->translate('Delete all %s this User.'), $item->getShortType());
                        break;
                    case 'warn':
                        $warnTable->setWarn($values);
                        $this->view->message = sprintf(Zend_Registry::get('Zend_Translate')->translate('User was warn!'));
                        break;
                    case 'warndelete':
                        $warnTable->setWarn($values, 'delete');
                        $this->view->message = sprintf(Zend_Registry::get('Zend_Translate')->translate('User was warn and delete all %s this user!'), $item->getShortType());
                        break;
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
                    'messages' => array($this->view->message),
                    'parentRefresh' => true,
                ));
    }

    function recaptchaAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spamcontrol_admin_main', array(), 'spamcontrol_admin_main_recaptcha');

        $this->view->form = $form = new Spamcontrol_Form_Key();



        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        foreach ($values as $key => $value) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
    }

    function settingsAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spamcontrol_admin_main', array(), 'spamcontrol_admin_main_settings');

        $this->view->form = $form = new Spamcontrol_Form_Setting();


        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        foreach ($values as $key => $value) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
    }

    function postcommentAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spamcontrol_admin_main', array(), 'spamcontrol_admin_main_post');

        $this->view->action_id = $action_id = $this->_getParam('action_id');




        $this->view->paginator = Engine_Api::_()->getItem('activity_action', $action_id)->getComments();
    }

    public function messageViewAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spamcontrol_admin_main', array(), 'spamcontrol_admin_main_privatemessagecontrol');

        $id = $this->_getParam('id');

        $this->view->conversation = $conversation = Engine_Api::_()->getItem('messages_conversation', $id);

        if (!$conversation) {
            return;
        }

        if (!empty($conversation->resource_type) &&
                !empty($conversation->resource_id)) {
            $resource = Engine_Api::_()->getItem($conversation->resource_type, $conversation->resource_id);
            if (!($resource instanceof Core_Model_Item_Abstract)) {
                return;
            }
            $this->view->resource = $resource;
        }
        // Otherwise get recipients
        else {
            $this->view->recipients = $recipients = $conversation->getRecipients();
        }

        $user = $conversation->getOwner();

        $this->view->messages = $messages = $conversation->getMessages($user);
    }

}