<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminGlobalController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventinvite_AdminGlobalController extends Core_Controller_Action_Admin {

    public function globalAction() {
        if ($this->getRequest()->isPost()) {
            $siteeventKeyVeri = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.lsettings', null);
            if (!empty($siteeventKeyVeri)) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting('siteevent.lsettings', trim($siteeventKeyVeri));
            }
            if ($_POST['siteeventinvite_lsettings']) {
                $_POST['siteeventinvite_lsettings'] = trim($_POST['siteeventinvite_lsettings']);
            }
        }

        $onactive_disabled = array('yahoo_settings_temp', 'submit', 'eventinvite_show_webmail', 'siteeventinvite_manifestUrl', 'eventinvite_friend_invite_enable');

        $afteractive_disabled = array('submit_lsetting', 'environment_mode');

        //GET NAVIGATION
//    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
//                    ->getNavigation('siteeventinvite_admin_main', array(), 'siteeventinvite_admin_main_settings');
        //GET NAVIGATION
        $this->view->navigationEvent = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_invite');

        $this->view->form = $form = new Siteeventinvite_Form_Admin_Global();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            include APPLICATION_PATH . '/application/modules/Siteeventinvite/controllers/license/license2.php';
        }
    }

    //SHOWING THE PLUGIN RELETED QUESTIONS AND ANSWERS
    public function faqAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteeventinvite_admin_main', array(), 'siteeventinvite_admin_main_faq');

        //GET NAVIGATION
        $this->view->navigationEvent = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_invite');
    }

    public function readmeAction() {
        
    }

    public function appconfigsAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteeventinvite_admin_main', array(), 'siteeventinvite_admin_main_global');
    }

}

?>