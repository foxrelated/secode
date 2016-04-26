<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Company
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminSettingsController.php
 * @author     Minh Nguyen
 */
class Groupbuy_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('groupbuy_admin_main', array(), 'groupbuy_admin_main_settings');
  }
  public function indexAction()
  {
    $this->view->form = $form = new Groupbuy_Form_Admin_Global();
   if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();

      foreach ($values as $key => $value){
        if($value < 0)
            $value = 0;
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, round($value,2));
      }
	 
	 $current_currency =Engine_Api::_()->getApi('settings', 'core')->setSetting('groupbuy.currency', '');
	 $new_currency = $values['groupbuy_currency'];
	 
	 Engine_Api::_()->getApi('settings', 'core')->setSetting('groupbuy.currency', $new_currency);
	 //if($current_currency != $new_currency){
	 //	$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		//$db->update('engine4_groupbuy_deals',array('currency'=>$new_currency));
		//$db->update('engine4_groupbuy_payment_requests',array('request_currency'=>$new_currency));	 	
	 //}
     $form->addNotice('Your changes have been saved.');
	 $this->_redirect('/admin/groupbuy/settings');
    }
  }
}