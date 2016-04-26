<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminGeotagController.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_AdminGeotagController extends Core_Controller_Action_Admin {

  //ACTION FOR SAVE THE GEO TAGGING SETTINGS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitetagcheckin_admin_main', array(), 'sitetagcheckin_admin_main_geotag');

    //GET CORE SETTING TABLE
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //MAKE THE FORM 
    $this->view->form = $form = new Sitetagcheckin_Form_Admin_Geotag();

    //CHECK REQUEST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
      $values = $form->getValues();

      if (isset($values['sitetagcheckin_selectable']))
        $settings->removeSetting('sitetagcheckin_selectable');

	  include APPLICATION_PATH . '/application/modules/Sitetagcheckin/controllers/license/license2.php';

      $form->addNotice('Your changes have been saved.');
    }
  }

}
