<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: AdminItemsController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_AdminItemsController extends Core_Controller_Action_Admin {

	//ACTION FOR MANAGING THE ADDED ITEMS
  public function manageAction()
  {  
		//GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('list_admin_main', array(), 'list_admin_main_widget');

		//MAKE FORM
		$this->view->formFilter = $formFilter = new List_Form_Admin_Filter();
		$page = $this->_getParam('page',1);
    
		//GET ITEM OF THE DAY TABLE
    $itemTable = Engine_Api::_()->getDbtable('itemofthedays', 'list');
    $itemTableName = $itemTable->info('name');

		//GET LISTING TABLE
    $listTable = Engine_Api::_()->getDbtable('listings', 'list');
    $listTableName = $listTable->info('name');

		//MAKE QUERY 
		$select = $listTable->select()
						->setIntegrityCheck(false)
						->from($listTableName, array('listing_id', 'owner_id', 'title'))
						->join($itemTableName, $listTableName . '.listing_id = ' . $itemTableName . '.listing_id');

		$values = array();

    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null == $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'date',
      'order_direction' => 'DESC',
    ), $values);
    
    $this->view->assign($values); 

		$select->order(( !empty($values['order']) ? $values['order'] : 'date' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

		//GET PAGINATOR
    $this->view->paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator = $this->view->paginator->setCurrentPageNumber($page);
  }    
   
	//ACTION FOR ADDING AN ITEM
  public function addItemAction()
  {
    //OPEN IN SMOOTHBOX
    $this->_helper->layout->setLayout('admin-simple');

    //MAKE FORM
    $form = $this->view->form = new List_Form_Admin_Item();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
			
    //CHECK POST
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {     
      $values = $form->getValues();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try 
			{      
				$table = Engine_Api::_()->getDbtable('itemofthedays', 'list');
				$select = $table->select()->where('listing_id = ?', $values["listing_id"]);
 				$result = $table->fetchRow($select);

				if(empty($result)) {

					//CREATE ROW
					$row = $table->createRow();
					$row->listing_id = $values["listing_id"];
					$row->date = $values["starttime"];
          $row->endtime = $values["endtime"];
					$row->save();  
				}
				else 
				{ 
					$table->update(array('date' => $values["starttime"], 'endtime' => $values["endtime"]), array('listing_id = ?' => $values["listing_id"]));
				}
        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 300,
          'parentRefresh' => 300,
          'messages' => array('The Listing of the Day has been added successfully.')
      ));
    }
  } 

	//ACTION FOR GET LISTINGS FOR ITEM OF THE DAY
  public function getListingsAction() {

    //GET LISTING TABLE
    $listTable = Engine_Api::_()->getDbtable('listings', 'list');
    $listTableName = $listTable->info('name');

    //MAKE QUERY
    $select = $listTable->select()
												->where('title  LIKE ? ', '%' . $this->_getParam('text') . '%') 
                        ->where($listTableName . '.closed = ?', '0')
												->where($listTableName . '.approved = ?', '1')
												->where($listTableName . '.draft = ?', '1')
												->where($listTableName . '.search = ?', '1')
												->order('title ASC')
												->limit($this->_getParam('limit', 40));

    //FETCH RESULTS
    $userlists = $listTable->fetchAll($select);
    $data = array();
    $mode = $this->_getParam('struct');

    if ($mode == 'text') {
      foreach ($userlists as $userlist) {
      $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array(
                'id' => $userlist->listing_id,
                'label' => $userlist->title,
                'photo' => $content_photo
        );
      }
    } else {
      foreach ($userlists as $userlist) {
       $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array(
                'id' => $userlist->listing_id,
                'label' => $userlist->title,
                'photo' => $content_photo
        );
      }
    }
    return $this->_helper->json($data);
  }

	//ACTION FOR ITEM DELETION
  public function deleteItemAction()
  {
    //GET ID
    $id = $this->_getParam('id');

    //CHECK POST
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
				Engine_Api::_()->getDbtable('itemofthedays', 'list')->delete(array('itemoftheday_id = ?' => $id));
				$db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }

    //OUTPUT
    $this->renderScript('admin-items/delete-item.tpl');
  }

  //ACTION FOR MULTI DELETE OFFERS
  public function multiDeleteAction()
  {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
        	//DELETE OFFERS FROM DATABASE AND SCRIBD
          $list_itemofthedays = Engine_Api::_()->getItem('list_itemofthedays', (int)$value);

					//FINALLY DELETE OFFER MODEL
					if (!empty($list_itemofthedays)) {
						$list_itemofthedays->delete();
					}
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
  }
}