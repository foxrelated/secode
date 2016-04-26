<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: AdminViewlistController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_AdminViewlistController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE LISTINGS
  public function indexAction() {

		//GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('list_admin_main', array(), 'list_admin_main_viewlist');

		//MAKE FORM
    $this->view->formFilter = $formFilter = new List_Form_Admin_Manage_Filter();

		//GET PAGE NUMBER
    $page = $this->_getParam('page', 1);

		//GET USER TABLE NAME
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

		//GET CATEGORY TABLE
		$this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'list');

		//GET LISTING TABLE
    $tableListing = Engine_Api::_()->getDbtable('listings', 'list');
    $listingTableName = $tableListing->info('name');

		//MAKE QUERY
    $select = $tableListing->select()
            ->setIntegrityCheck(false)
            ->from($listingTableName)
            ->joinLeft($tableUserName, "$listingTableName.owner_id = $tableUserName.user_id", 'username');

    $values = array();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    foreach ($values as $key => $value) {

      if (null == $value) {
        unset($values[$key]);
      }
    }

    // searching
    $this->view->owner = '';
    $this->view->title = '';
    $this->view->sponsored = '';
    $this->view->approved = '';
    $this->view->featured = '';
    $this->view->status = '';
    $this->view->listingbrowse = '';
    $this->view->category_id = '';
    $this->view->subcategory_id = '';
    $this->view->subsubcategory_id = '';

    if (isset($_POST['search'])) {

      if (!empty($_POST['owner'])) {
        $this->view->owner = $_POST['owner'];
        $select->where($tableUserName . '.username  LIKE ?', '%' . $_POST['owner'] . '%');
      }

      if (!empty($_POST['title'])) {
        $this->view->title = $_POST['title'];
        $select->where($listingTableName . '.title  LIKE ?', '%' . $_POST['title'] . '%');
      }

      if (!empty($_POST['sponsored'])) {
        $this->view->sponsored = $_POST['sponsored'];
        $_POST['sponsored']--;

        $select->where($listingTableName . '.sponsored = ? ', $_POST['sponsored']);
      }

      if (!empty($_POST['approved'])) {
        $this->view->approved = $_POST['approved'];
        $_POST['approved']--;
        $select->where($listingTableName . '.approved = ? ', $_POST['approved']);
      }

      if (!empty($_POST['featured'])) {
        $this->view->featured = $_POST['featured'];
        $_POST['featured']--;
        $select->where($listingTableName . '.featured = ? ', $_POST['featured']);
      }

      if (!empty($_POST['status'])) {
        $this->view->status = $_POST['status'];
        $_POST['status']--;
        $select->where($listingTableName . '.closed = ? ', $_POST['status']);
      }

      if (!empty($_POST['listingbrowse'])) {
        $this->view->listingbrowse = $_POST['listingbrowse'];
        $_POST['listingbrowse']--;
        if ($_POST['listingbrowse'] == 0) {
          $select->order($listingTableName . '.view_count DESC');
        } else {
          $select->order($listingTableName . '.creation_date DESC');
        }
      }

      if (!empty($_POST['category_id']) && empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
        $this->view->category_id = $_POST['category_id'];
        $select->where($listingTableName . '.category_id = ? ', $_POST['category_id']);
      } 
			elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
        $this->view->category_id = $_POST['category_id'];
        $this->view->subcategory_id = $_POST['subcategory_id'];
        $this->view->subcategory_name = $tableCategory->getCategory($this->view->subcategory_id)->category_name;
               
        $select->where($listingTableName . '.category_id = ? ', $_POST['category_id'])
            ->where($listingTableName . '.subcategory_id = ? ', $_POST['subcategory_id']);
      }
			elseif(!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && !empty($_POST['subsubcategory_id'])) {
        $this->view->category_id = $_POST['category_id'];
        $this->view->subcategory_id = $_POST['subcategory_id'];
        $this->view->subsubcategory_id = $_POST['subsubcategory_id'];
        $this->view->subcategory_name = $tableCategory->getCategory($this->view->subcategory_id)->category_name;
        $this->view->subsubcategory_name = $tableCategory->getCategory($this->view->subsubcategory_id)->category_name;
               
        $select->where($listingTableName . '.category_id = ? ', $_POST['category_id'])
            ->where($listingTableName . '.subcategory_id = ? ', $_POST['subcategory_id'])
						->where($listingTableName . '.subsubcategory_id = ? ', $_POST['subsubcategory_id']);
			}
			
    }

    $values = array_merge(array(
                'order' => 'listing_id',
                'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'listing_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR
    $this->view->paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(40);
    $this->view->paginator = $this->view->paginator->setCurrentPageNumber($page);
  }

  //ACTION FOR VIEWING LIST DETAILS
  public function detailAction() {

    //GET THE LIST ITEM
    $this->view->listDetail = Engine_Api::_()->getItem('list_listing',(int) $this->_getParam('id'));
  }

	//ACTION FOR MULTI-DELETE LISTINGS
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          Engine_Api::_()->getItem('list_listing', (int) $value)->delete();
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

}