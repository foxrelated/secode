<?php

class Ynaffiliate_AdminSettingsController extends Core_Controller_Action_Admin {

   public function init() {
      //Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_settings');
   }

   public function indexAction() {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
              ->getNavigation('ynaffiliate_admin_main', array(), 'ynaffiliate_admin_main_settings');


      $this->view->form = $form = new Ynaffiliate_Form_Admin_Global();

      $settings = Engine_Api::_()->getApi('settings', 'core');
      //   $values = $settings->ynaffiliate;


      if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
         $values = $form->getValues();

         foreach ($values as $key => $value) 
         {
         	if($key == 'ynaffiliate_baseUrl')
			{
				$settings->setSetting($key, $value);
			}
			else
			{
	            if ($value < 0) 
	            {
	               $value = 0;
	            }
	            $settings->setSetting($key, round($value, 2));
			}
         }
         
          $form->addNotice('Your changes have been saved');
      }

     
   }

}