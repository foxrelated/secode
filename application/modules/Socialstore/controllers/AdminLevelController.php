<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminLevelController.php
 * @author     Minh Nguyen
 */
class Socialstore_AdminLevelController extends Core_Controller_Action_Admin
{
	public function init() {
		parent::init();
		Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_level');

	}
	
  public function indexAction()
  {
    // Get level id
  	if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    // Make form
    $this->view->form = $form = new Socialstore_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);
    $this->view->level_id = $id;
    // Populate data
    $valueArray = array('store_pubfee','store_ftedfee','product_pubfee','product_ftedfee','product_com');
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $select = $permissionsTable->select()->from($permissionsTable->info('name'));
    $select->where('level_id = ?', $id);
    $select->where('name IN (?)', $valueArray);
    $result = $permissionsTable->fetchAll($select)->toArray();
    $mainArray = $form->getValues();
    $productArray = array();
    $storeArray = array();
   
    foreach ($mainArray as $key => $mainAr) {
    	if(!strpos($key,'_'))
    	{
    		continue;
    	}
    	$str = explode('_',$key);
    	
    	if (@$str[0]  == "product") {
    		$productArray[$key] = $mainAr;
    	}  
    	elseif (@$str[0] == "store") {
    		$storeArray[$key] = $mainAr;
    	}
    }
 
    $form->populate($permissionsTable->getAllowed('social_store', $id, array_keys($storeArray)));
	$form->populate($permissionsTable->getAllowed('social_product', $id, array_keys($productArray)));
	
	$store_comment = $permissionsTable -> getAllowed('social_store', $id, 'comment');
	$product_comment = $permissionsTable -> getAllowed('social_product', $id, 'comment');
	if($form -> getElement('store_comment'))
		$form -> getElement('store_comment') -> setValue($store_comment);
	if($form -> getElement('product_comment'))
		$form -> getElement('product_comment') -> setValue($product_comment);
    
    // Check post
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process

   // $values = $form->getValues();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
    $mainArray = $form->getValues();
	
    $productArray = array();
    $storeArray = array();
   
    foreach ($mainArray as $key => $mainAr) {
    	if(!strpos($key,'_'))
    	{
    		continue;
    	}
    	$str = explode('_',$key);
    	
    	if (@$str[0]  == "product") {
    		if ($key == "product_comment") {
    			$key = "comment";
    		}
    		$productArray[$key] = $mainAr;
    		
    	}  
    	elseif (@$str[0] == "store") {
    		if ($key == "store_comment") {
    			$key = "comment";
    		}
    		$storeArray[$key] = $mainAr;
    	}
    }

    	// Set permissions
    $permissionsTable->setAllowed('social_store', $id, $storeArray);
	    $permissionsTable->setAllowed('social_product', $id, $productArray);
      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    if ($productArray['product_pubfee'] == 3 || $productArray['product_pubfee'] == 5) {
    	$permissionsTable->update(array(
	        	'params' => $productArray['product_pubfee'],
	      		), array(
	        'name = ?' => 'product_pubfee',
	      		'level_id = ?' => $id, 
	      	 ));
    }
    if ($productArray['product_ftedfee'] == 3 || $productArray['product_ftedfee'] == 5) {
    	$permissionsTable->update(array(
	        	'params' => $productArray['product_ftedfee'],
	      		), array(
	        'name = ?' => 'product_ftedfee',
	      		'level_id = ?' => $id, 
	      	 ));
    }
    if ($productArray['product_com'] == 3 || $productArray['product_com'] == 5) {
    	$permissionsTable->update(array(
	        	'params' => $productArray['product_com'],
	      		), array(
	        'name = ?' => 'product_com',
	      		'level_id = ?' => $id, 
	      	 ));
    }
    if ($storeArray['store_pubfee'] == 3 || $storeArray['store_pubfee'] == 5) {
    	$permissionsTable->update(array(
	        	'params' => $storeArray['store_pubfee'],
	      		), array(
	        'name = ?' => 'store_pubfee',
	      		'level_id = ?' => $id, 
	      	 ));
    }
    if ($storeArray['store_ftedfee'] == 3 || $storeArray['store_ftedfee'] == 5) {
    	$permissionsTable->update(array(
	        	'params' => $storeArray['store_ftedfee'],
	      		), array(
	        'name = ?' => 'store_ftedfee',
	      		'level_id = ?' => $id, 
	      	 ));
    }
    
    $form->addNotice('Your changes have been saved.');
  }

}