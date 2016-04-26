<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */

        if (!empty($method) && $method == 'Nestedcomment_Form_Admin_Global') {
            
        }
        return true;
    }

    public function indexAction() {
        $this->view->isModsSupport = Engine_Api::_()->nestedcomment()->isModulesSupport();
        include APPLICATION_PATH . '/application/modules/Nestedcomment/controllers/license/license1.php';
    }

    public function readmeAction() {
        
    }

    public function faqAction() {
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('nestedcomment_admin_main', array(), 'nestedcomment_admin_main_faq');
        $this->view->faq_id = $faq_id = $this->_getParam('faq_id', 'faq_1');
    }

}
