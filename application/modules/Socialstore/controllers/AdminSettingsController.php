<?php

class Socialstore_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
  	Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_settings');
  }
  public function indexAction()
  {
    $this->view->form = $form = new Socialstore_Form_Admin_Global();
   if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();

      foreach ($values as $key => $value){
        if ($key == 'store_pathname' || $key == 'store_sellerpolicy' || $key == 'store_buyerpolicy') {
        	Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        else {
	      	if($value < 0) {
	            $value = 0;
	        }
	        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, round($value,2));
        }
     }
	  	 
	 $current_currency =Engine_Api::_()->getApi('settings', 'core')->setSetting('store.currency', $form->getValue('store_currency'));
     $db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
			try {
				// Edit currency in the database
				$code = $form->getValue('store_currency');
				$OrderItems = new Socialstore_Model_DbTable_OrderItems;
				$OrderItems->update(array('currency' => $code),'');
				$Orders = new Socialstore_Model_DbTable_Orders;
				$Orders->update(array('currency' => $code),'');
				$Paytrans = new Socialstore_Model_DbTable_PayTrans;
				$Paytrans->update(array('currency' => $code),'');
				$Products = new Socialstore_Model_DbTable_Products;
				$Products->update(array('currency' => $code),'');
				$Reqtrans = new Socialstore_Model_DbTable_ReqTrans;
				$Reqtrans->update(array('currency' => $code),'');
				$Requests = new Socialstore_Model_DbTable_Requests;
				$Requests->update(array('currency' => $code),'');
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
	 $form->addNotice('Your changes have been saved.');
	 
    }
  }
}