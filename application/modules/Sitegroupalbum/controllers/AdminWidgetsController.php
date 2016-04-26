<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminWidgetController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupalbum_AdminWidgetsController extends Core_Controller_Action_Admin {

  //ACTION FOR WIDGET SETTINGS
  public function indexAction() {
    
    //TABS CREATION
    $this->view->navigationGroup = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_album');       
    
    //TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupalbum_admin_main', array(), 'sitegroupalbum_admin_widget_settings');

    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupalbum_admin_submain', array(), 'sitegroupalbum_admin_submain_album_tab');

    //GET WIDGET SETTING FORM
    //$this->view->form = $form = new Sitegroupalbum_Form_Admin_Widget();

    //CHECK FORM VALIDATION
    //if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
     // $values = $form->getValues();
//				foreach ($values as $key => $value) {
//					if ($key != 'submit') {
//						Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
//					}   
//				} 
    //}
  }

}

?>