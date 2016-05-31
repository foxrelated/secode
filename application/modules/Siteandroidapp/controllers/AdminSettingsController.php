<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminSettingsController.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteandroidapp_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */

        if (!empty($method) && $method == 'Siteandroidapp_Form_Admin_Settings') {
            
        }
        return true;
    }

    public function indexAction() {
        if (isset($_POST['browse_as_guest'])) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('siteandroidapp.browse.guest', $_POST['browse_as_guest']);
        }
        
        include_once APPLICATION_PATH . '/application/modules/Siteandroidapp/controllers/license/license1.php';
    }

    public function faqAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteandroidapp_admin_main', array(), 'siteandroidapp_admin_faq');
    }

    public function readmeAction() {
        
    }

    /*
     * Delete the old exist android app from app dashboard.
     */

    public function deleteExistingAppAction() {
        $this->_helper->layout->setLayout('admin-simple');

        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getApi('core', 'siteandroidapp')->validatePreviousMobileAPP();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deleted Successfully!'))
            ));
        }
    }
}
