<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: AdminPokehistoryController.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_AdminPokehistoryController extends Core_Controller_Action_Admin {

  public function indexAction()
  {
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
   						->getNavigation('poke_admin_main', array(), 'poke_admin_main_pokehistory');
		
    $page = $this->_getParam('page',1);
    
    //Getting the poke table.
    $pokeTable = Engine_Api::_()->getDbtable('pokeusers', 'poke');
    $pokeName = $pokeTable->info('name');	  

    //Getting the user table name.   
  	$userNameS = Engine_Api::_()->getDbtable('users', 'user')->info('name');
		$userNameR = Engine_Api::_()->getDbtable('users', 'user')->info('name');
		
		//Selecting the poke from the poke table.
	  $select = $pokeTable->select()
			    	->setIntegrityCheck(false)
			      ->from($pokeName)
			      ->join($userNameS.' as SENDER', "`SENDER`.`user_id`=`{$pokeName}`.resourceid", array('SENDER.displayname as sname', 'SENDER.username as susername'  ))
			      ->join($userNameR.' as RECEIVER', "`RECEIVER`.`user_id`=`{$pokeName}`.userid", array('RECEIVER.displayname as rname', 'RECEIVER.username as rusername'));

		//Defining the values.	       
		$values = array();
	  $this->view->formFilter = $formFilter = new Poke_Form_Admin_Manage_Filter();    
	  if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
   
	  foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    //Defining the sender,receiver and the isexpire's values.
	  $this->view->sender = '';
	  $this->view->receiver = '';
	  $this->view->isexpire = '';

	  //Checking if admin want to search.
    if (isset($_POST['search'])) { 
    	
      if (!empty($_POST['sender'])) {
        $this->view->sender = $_POST['sender'];
        $select->where('SENDER.displayname  LIKE ?', '%' . $_POST['sender'] . '%');
      }
      
      if (!empty($_POST['isexpire'])) {
        $this->view->isexpire = $_POST['isexpire'];
        $select->where('isexpire  = ?',  $_POST['isexpire'] );
      }
      
			if (!empty($_POST['receiver'])) {
        $this->view->receiver = $_POST['receiver'];
        $select->where('RECEIVER.displayname  LIKE ?', '%' . $_POST['receiver'] . '%');
      }   
      
      $this->view->searchFlag = 1;
         
    }
    
    
    $values = array_merge(array(
                'order' => 'pokeuser_id',
                'order_direction' => 'DESC',
            ), $values); 
    
    
    $this->view->assign($values);
    
    if($this->view->order_direction != null) {
			$this->view->order_direction = $values['order_direction'];
    } 
    else {
    	$this->view->order_direction ='ASC';
    }

		$select->order((!empty($values['order']) ? $values['order'] : 'pokeuser_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' )); 
		

	  $this->view->paginator =  $paginator = Zend_Paginator::factory($select);

		
	  $this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator->setCurrentPageNumber($page); 		     
  }

  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->poke_id=$id;
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $poke = Engine_Api::_()->getItem('pokeusers', $id);
				Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('user_id = ?' => $poke->userid, 'type = ?' => 'Poke', 'object_id = ?' => $poke->resourceid));
        // delete the poke entry from the database
        $poke->delete();
        $db->commit();
      }
			catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-pokehistory/delete.tpl');
  }

  public function deleteselectedAction()
  {
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $poke = Engine_Api::_()->getItem('pokeusers', $id);
        Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('user_id = ?' => $poke->userid, 'type = ?' => 'Poke', 'object_id = ?' => $poke->resourceid));
        if( $poke ) $poke->delete();
      }
			$this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
	}
}
?>