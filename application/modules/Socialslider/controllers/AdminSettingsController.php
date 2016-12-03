<?php

/**
 * Description of AdminSettingsController
 * @author Isabek Tashiev <isabek2309@gmail.com>
 */
class Socialslider_AdminSettingsController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('socialslider_admin_main', array(), 'socialslider_admin_main_buttons');

        $this->view->buttons = Engine_Api::_()->getDbtable('buttons', 'socialslider')
                ->getButtons();
    }

    public function addAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('socialslider_admin_main', array(), 'socialslider_admin_main_addbutton');

        $this->view->form = $form = new Socialslider_Form_Admin_Buttons();

        if ($this->_isFormValid($form)) {

            $table = Engine_Api::_()->getDbtable('buttons', 'socialslider');
            $row = $table->createRow();

            $db = $table->getAdapter();
            $db->beginTransaction();

            try {
                $row->setFromArray($form->getValues());
                $row->save();

                $fileName = $form->button_file->getFileName();
                $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
                $filesRow = $this->_createFile($filesTable, $fileName, $row);

                $row->picture_path = $filesRow->getIdentity();
                $row->save();

                $db->commit();
            } catch (Exception $error) {
                $db->rollBack();
                throw $error;
            }

            $form->addNotice($form->getValue('button_name') . ' is safely saved in database');
            $form->reset();
        }
    }

    public function manageAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('socialslider_admin_main', array(), 'socialslider_admin_main_settings');

        $this->view->form = $form = new Socialslider_Form_Admin_Settings(array('type' => 'user'));

        if ($this->_isFormValid($form)) {

            $values = $form->getValues();

            foreach ($values as $key => $value) {
                if ($key == 'enable' && empty($value)) {
                    $value = 0;
                }

                Engine_Api::_()->getApi('settings', 'core')->setSetting('socialslider.' . $key, $value);
            }

            $form->addNotice('Your changes have been saved.');
        }
    }

    public function editAction() {

        $button_id = $this->_getParam('id', null);
        $buttonsTable = Engine_Api::_()->getDbtable('buttons', 'socialslider');
        $button = $buttonsTable->find($button_id)->current();

        $this->view->form = $form = new Socialslider_Form_Admin_Settings_Edit();

        if ($this->_isFormValid($form) && !empty($button_id)) {

            $db = $buttonsTable->getAdapter();

            if ($form->getElement('button_file')->getValue()) {

                $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

                $file = $this->_getFile($button);
                if ($file !== NULL)
                    $file->remove();

                $filesRow = $this->_createFile($filesTable, $form->button_file, $button);
                $button->setFromArray($form->getValues());
                $button->picture_path = $filesRow->getIdentity();

                $db->beginTransaction();
                try {

                    $button->save();
                    $db->commit();
                } catch (Exception $error) {
                    $db->rollBack();
                    throw $error;
                }
            } else {

                $button->setFromArray($form->getValues());

                $db->beginTransaction();
                try {
                    $button->save();
                    $db->commit();
                } catch (Exception $error) {
                    $db->rollBack();
                    throw $error;
                }
            }

            $this->thisForward('edited');
        } else {
            $form->populate(array(
                'button_name' => $button['button_name'],
                'button_color' => $button['button_color'],
                'button_code' => $button['button_code']));
        }
    }

    private function _getFile($button) {
        return Engine_Api::_()->getItemTable('storage_file')->getFile($button->picture_path, $button->getType());
    }

    private function _createFile($filesTable, $fileName, $row) {
        return $filesTable->createFile($fileName, array(
                    'parent_id' => $row->getIdentity(),
                    'parent_type' => $row->getType()
                ));
    }

    public function deleteAction() {

        $button_id = $this->_getParam('id', null);
        $buttonsTable = Engine_Api::_()->getDbtable('buttons', 'socialslider');
        $button = $buttonsTable->find($button_id)->current();

        $this->view->form = $form = new Socialslider_Form_Admin_Settings_Delete();

        if ($this->getRequest()->isPost()) {

            $db = $buttonsTable->getAdapter();
            $db->beginTransaction();

            try {

                $file = $this->_getFile($button);
                if ($file !== NULL)
                    $file->remove();

                $button->delete();
                $db->commit();
            } catch (Exception $error) {
                $db->rollBack();
                throw $error;
            }

            $this->thisForward('deleted');
        }
    }

    public function disableAction() {

        $button_id = $this->_getParam('id', null);
        $this->view->form = new Socialslider_Form_Admin_Settings_Disable();
        $buttonTable = Engine_Api::_()->getDbtable('buttons', 'socialslider');

        if ($this->getRequest()->isPost() && !empty($button_id)) {

            $buttonTable->update(array(
                'button_show' => 0
                    ), array('button_id = ?' => $button_id));

            $this->thisForward('disabled');
        }
    }

    public function enableAction() {

        $button_id = $this->_getParam('id', null);
        $buttonsTable = Engine_Api::_()->getDbtable('buttons', 'socialslider');
        $button = $buttonsTable->find($button_id)->current();

        if ($this->_isEnable($button)) {

            $this->view->form = new Socialslider_Form_Admin_Settings_Enable();

            if ($this->getRequest()->isPost()) {

                $db = $buttonsTable->getAdapter();
                $db->beginTransaction();
                try {
                    $buttonsTable->update(array(
                        'button_show' => 1
                            ), array('button_id = ?' => $button_id));
                    $db->commit();
                } catch (Exception $error) {
                    $db->rollBack();
                    throw $error;
                }

                $this->thisForward('enabled');
            }
        } else {
            $this->editAction();

            $table = Engine_Api::_()->getDbtable('buttons', 'socialslider');
            $button = $table->find($button_id)->current();

            $db = $table->getAdapter();
            $db->beginTransaction();
            try {

                if ($this->_isEnable($button)) {
                    $table->update(array(
                        'button_show' => 1
                            ), array('button_id = ?' => $button_id));
                }

                $db->commit();
            } catch (Exception $error) {
                $db->rollBack();
                throw $error;
            }
        }
    }

    public function seditAction() {

        $this->view->form = $form = new Socialslider_Form_Admin_Settings_Stbutton();

        $button_type = $this->_getParam('sn', 'facebook');

        $buttonsTable = Engine_Api::_()->getDbtable('buttons', 'socialslider');

        $sbutton = $buttonsTable->getSbutton($button_type);

        if ($this->_isFormValid($form)) {

            $values = $form->getValues();

            $db = $buttonsTable->getAdapter();
            $db->beginTransaction();

            try {
                $buttonsTable->update(array(
                    'button_code' => $values['code']
                        ), array('button_type = ?' => $button_type));
                $buttonsTable->update(array(
                    'button_show' => 1), array(
                    'button_type = ?' => $button_type));
                $db->commit();
            } catch (Exception $error) {
                $db->rollBack();
                throw $error;
            }

            $this->thisForward('edited');
        } else {

            switch ($button_type) {
                case 'facebook':
                    $label = 'Facebook URL';
                    $desc = 'Example: http://www.facebook.com/facebook';
                    break;
                case 'twitter':
                    $label = 'Twitter Username';
                    $desc = 'Ex: twitter';
                    break;
                case 'gplus':
                    $label = 'Google Plus ID';
                    $desc = 'Ex: 111831455056621143074';
                    break;
                case 'youtube':
                    $label = 'Youtube Username';
                    $desc = 'Example: youtube';
                    break;
                default : break;
            }

            $form->getElement('code')->setLabel($label);
            $form->getElement('code')->setDescription($desc);
            $form->getElement('code')->setValue($sbutton->button_code);
            $form->getElement('hidden')->setValue($sbutton->button_type);
        }
    }

    private function _isFormValid($form) {
        return $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost());
    }

    private function thisForward($message) {
        return $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 10,
                    'parentRefresh' => 10,
                    'messages' => array($this->view->translate('This button has been successfully ' . $message . '.'))
                ));
    }

    private function _isEnable($button) {

        $button_name = $button['button_name'];
        $button_path = $button['picture_path'];
        $button_color = $button['button_color'];
        $button_code = $button['button_code'];
        $button_default = $button['button_default'];

        if (empty($button_name) || empty($button_color) || empty($button_code) || ( empty($button_path) && $button_default !== 1)) {
            return false;
        }

        return true;
    }

}

?>
