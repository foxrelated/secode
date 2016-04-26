<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegroupmember_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE MEMBERS
  public function indexAction() {
    
    //TABS CREATION
    $this->view->navigationGroup = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_member');     
    
    //CREATE NAVIGATION TABS
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitegroupmember_admin_main', array(), 'sitegroupmember_admin_manage_member');

    //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION
    $this->view->formFilter = $formFilter = new Sitegroupmember_Form_Admin_Manage_Filter();

    //FETCH MEMEBERS DATAS
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

    $tableSitegroupName = Engine_Api::_()->getItemTable('sitegroup_group')->info('name');

    $tableGroupmember = Engine_Api::_()->getDbtable('membership', 'sitegroup');
    $tableNameGroupmember = $tableGroupmember->info('name');

    $select = $tableGroupmember->select()
						->setIntegrityCheck(false)
						->from($tableNameGroupmember, array('featured AS featured_member', 'member_id', 'user_id', 'COUNT("user_id") AS JOINP_COUNT'))
						->join($tableUserName, "$tableNameGroupmember.user_id = $tableUserName.user_id", 'username')
						->join($tableSitegroupName, "$tableNameGroupmember.resource_id = $tableSitegroupName.group_id")
						->where($tableNameGroupmember . '.active = ?', 1)
						->where($tableSitegroupName . '.closed = ?', '0')
						->where($tableSitegroupName . '.approved = ?', '1')
						->where($tableSitegroupName . '.search = ?', '1')
						->where($tableSitegroupName . '.declined = ?', '0')
						->where($tableSitegroupName . '.draft = ?', '1');

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }
    $this->view->title = '';
    $this->view->category_id = '';
    $this->view->subcategory_id = '';
    $this->view->subsubcategory_id = '';
		if(!empty($_POST['title'])) { 
			$group_name = $_POST['title']; 
		} 
		elseif(!empty($_GET['title'])) { 
			$group_name = $_GET['title']; 
		} 
		elseif($this->_getParam('title', '')) { 
			$group_name = $this->_getParam('title', '');
		} 
		else { 
			$group_name = '';
		}
		$this->view->title = $values['title'] = $group_name; 
    	if (!empty($group_name)) {
			$select->where($tableSitegroupName . '.title  LIKE ?', '%' . $group_name . '%');
		}    
    if (isset($_POST['search'])) {
      if (!empty($_POST['owner'])) {
        $this->view->owner = $_POST['owner'];
        $select->where($tableUserName . '.displayname  LIKE ?', '%' . $_POST['owner'] . '%');
      }
		
      if (!empty($_POST['featured'])) {
        $this->view->featured = $_POST['featured'];
        $_POST['featured']--;
        $select->where($tableNameGroupmember . '.featured = ? ', $_POST['featured']);
      }
      
      
      if (!empty($_POST['category_id']) && empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'] )) {
        $this->view->category_id = $_POST['category_id'];
        $select->where($tableSitegroupName . '.category_id = ? ', $_POST['category_id']);
      } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'] )) {
        $this->view->category_id = $_POST['category_id'];
        $subcategory_id = $this->view->subcategory_id = $_POST['subcategory_id'];
        $table = Engine_Api::_()->getDbtable('categories', 'sitegroup');
        $categoriesName = $table->info('name');
        $selectcategory = $table->select()->from($categoriesName, 'category_name')
                ->where("(category_id = $subcategory_id)");
        $row = $table->fetchRow($selectcategory);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $row->category_name;
        }

        $select->where($tableSitegroupName . '.category_id = ? ', $_POST['category_id'])
                ->where($tableSitegroupName . '.subcategory_id = ? ', $_POST['subcategory_id']);
      }

      elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && !empty($_POST['subsubcategory_id'])) {
        
        $this->view->category_id = $_POST['category_id'];
        $subcategory_id = $this->view->subcategory_id = $_POST['subcategory_id'];
        $subsubcategory_id = $this->view->subsubcategory_id = $_POST['subsubcategory_id'];

        $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($subcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $row->category_name;
        }
        $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($subsubcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subsubcategory_name = $row->category_name;
        }
        $select->where($tableSitegroupName . '.category_id = ? ', $_POST['category_id'])
                ->where($tableSitegroupName . '.subcategory_id = ? ', $_POST['subcategory_id'])
                ->where($tableSitegroupName . '.subsubcategory_id = ? ', $_POST['subsubcategory_id']);;
      }
    }
    
    $values = array_merge(array('order' => 'member_id', 'order_direction' => 'DESC'), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'member_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    $select->group("$tableNameGroupmember.user_id");
    
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('group', 1));
  }

  //ACTION FOR LEAVE THE MEMBER.
  public function deleteAction() {

    $this->_helper->layout->setLayout('admin-simple');
    
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    
    //GET GROUP ID.
    $group_id = $this->_getParam('group_id');
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    
    if (!empty($group_id)) {

      Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('user_id =?' => $this->_getParam('user_id'), 'group_id =?' =>  $group_id));

			//DELETE ACTIVITY FEED OF JOIN GROUP ACCORDING TO USER ID.
			$action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?'  => 'sitegroup_join', 'subject_id = ?' => $this->_getParam('user_id'), 'object_id = ?' => $group_id));
			$action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
			$action->delete();
			
			//MEMBER COUNT DECREASE WHEN MEMBER JOIN THE GROUP.
			$sitegroup->member_count--;
			$sitegroup->save();
    }
  }
  
  //ACTION FOR MULTI LEAVE MEMBER ENTRIES.
  public function multiDeleteMemberAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
				  Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('member_id =?' => (int) $value));
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
  
  //ACTION FOR MAKE MEMBERS FEATURED AND REMOVE FEATURED MEMBERS.
  public function featuredAction() {

    //GET USER ID AND GROUP ID
    $user_id = $this->_getParam('user_id');
    $group_id = $this->_getParam('group_id');

    $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
    $membershipTableName = $membershipTable->info('name');
    $select = $membershipTable->select()->from($membershipTableName, array('featured'))
              ->where('user_id = ?', $user_id);
    $sitegroupmember = $membershipTable->fetchRow($select);

		if ($sitegroupmember->featured == 0) {
			Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('featured' => 1), array('user_id = ?' => $user_id));
		}
		else {
			Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('featured'=>  '0'), array('user_id = ?' => $user_id));
		}
		
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
  
  //ACTION FOR TOTAL JOIN GROUP.
  public function groupJoinAction() {
  
		$this->view->user_id = $user_id = $this->_getParam('user_id');
		
		//GET THE FRIEND ID AND OBJECT OF USER.
    $this->view->showViewMore = $this->_getParam('showViewMore', 0);
		$this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinGroups($user_id, 'groupJoin');
		
		$paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('group', 1));
    $this->view->count = $paginator->getTotalItemCount();
  }
}