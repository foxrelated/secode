<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_AdminCheckinController extends Core_Controller_Action_Admin {

  //ACTION FOR SAVE THE GOLBAL SETTINGS
  public function indexAction() {

    //GET NAVIGATION 
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitetagcheckin_admin_main', array(), 'sitetagcheckin_admin_main_checkin');

    //GET CORE SETTING TABLE
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //MAKE FORM
    $this->view->form = $form = new Sitetagcheckin_Form_Admin_Checkin();

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //UNSET THE DUMMY FIELD
      if (array_key_exists("sitetagcheckin_update", $values)) {
        unset($values['sitetagcheckin_update']);
      }
      
      include APPLICATION_PATH . '/application/modules/Sitetagcheckin/controllers/license/license2.php';

      $form->addNotice('Your changes have been saved.');
    }
  }

}
