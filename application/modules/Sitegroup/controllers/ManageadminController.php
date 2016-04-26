<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_ManageadminController extends Seaocore_Controller_Action_Standard {

	//ACTION FOR SHOWING GROUPS FOR WHICH I AM ADMIN
	public function myGroupsAction() {

    //USER VALIDATION
		if (!$this->_helper->requireUser()->isValid())
			return;

    //MANAGE ADMIN IS ALLOWED OR NOT BY ADMIN
		$manageAdminEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1);
		if (empty($manageAdminEnabled)) {
			return $this->_forwardCustom('requireauth', 'error', 'core');
		}

    //GETTING THE VIEWER AND VIEWER ID AND PASS VALUE .TPL FILE.
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->owner_id = $viewer_id = $viewer->getIdentity();

    //CHEKC FOR MEMBER LEVEL SETTINGS FOR EDIT AND DELETE AND CREATE.
		$this->view->can_create = $this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'create')->checkRequire();
		$this->view->can_edit = $this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'edit')->checkRequire();
		$this->view->can_delete = $this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'delete')->checkRequire();

		$this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1);
		$this->view->enableLocation = $checkLocation = Engine_Api::_()->sitegroup()->enableLocation();

		//GET NAVIGATION
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main', array(), 'sitegroup_main_manage');

		//GET QUICK NAVIGATION
		$this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
						->getNavigation('sitegroup_quick');

		$this->view->form = $form = new Sitegroup_Form_Myadmingroups();

		//PROCESS FORM
		$values = array();
		if ($form->isValid($this->_getAllParams())) {
			$values = $form->getValues();
		}

		//RATING ENABLE / DISABLE
		$this->view->ratngShow = $ratingShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');

		//GET GROUPS
		$admingroups = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdminGroups($viewer_id);

		//GET STUFF
		$ids = array();
		foreach ($admingroups as $admingroup) {
			$ids[] = $admingroup->group_id;
		}
		$values['admingroups'] = $ids;
		$values['orderby'] = 'creation_date';
		
    //$values['notIncludeSelfGroups'] = $viewer_id;

		//GET PAGINATOR.
		$this->view->paginator = $paginator = Engine_Api::_()->sitegroup()->getSitegroupsPaginator($values, null);
		$items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.group', 10);

		$paginator->setItemCountPerPage($items_count);
		$this->view->paginator = $paginator->setCurrentPageNumber($values['page']);

		$this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

		//MAXIMUN ALLOWED GROUPS.
		$this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitegroup_group', 'max');
		$this->view->current_count = $paginator->getTotalItemCount();

    if(Engine_Api::_()->seaocore()->isSitemobileApp()) {  
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $this->view->page = $this->_getParam('page', 1);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalCount) /$items_count);
    if (!$isappajax) {
      $this->_helper->content/*->setNoRender()*/->setEnabled();
    }
  }

  //MANAGE ADMINS ACTION FOR THE GROUPS.
  public function indexAction() {

    //CHECK PERMISSION FOR VIEW.
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION.
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_main');

    $this->view->sitegroups_view_menu = 11;

    //GETTING THE VIEWER AND VIEWER ID.
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    //GETTING THE OBJECT AND GROUP ID.
    $this->view->group_id = $group_id = $this->_getParam('group_id', null);
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    $this->view->owner_id = $sitegroup->owner_id;
    $this->view->is_ajax = $this->_getParam('is_ajax', '');

    //EDIT PRIVACY
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');

    $manageAdminAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1);
    if (empty($isManageAdmin) || empty($manageAdminAllowed)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

		$manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');

		//FETCH DATA
    $this->view->manageHistories = $manageadminsTable->getManageAdminUser($group_id);

    if ($this->getRequest()->isPost()) {

      $values = $this->getRequest()->getPost();
      $selected_user_id = $values['user_id'];
      
      if(empty($selected_user_id))
          return;      

      $row = $manageadminsTable->createRow();
      $row->user_id = $selected_user_id;
      $row->group_id = $group_id;
      $row->save();

			//START SITEGROUPMEMBER PLUGIN WORK
			$sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
			
			if ($sitegroupMemberEnabled) {
				$membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
				$membersTableName = $membersTable->info('name');
				
				$select = $membersTable->select()
								->from($membersTableName)
								->where('user_id = ?', $selected_user_id)
								->where($membersTableName . '.resource_id = ?', $group_id);
				$select = $membersTable->fetchRow($select);
				
				if(empty($select)) {
					$row = $membersTable->createRow();
					$row->resource_id = $group_id;
					$row->group_id = $group_id;
					$row->user_id = $selected_user_id;
					$row->save();
				}
			}
			//END SITEGROUPMEMBER PLUGIN WORK

      $newManageAdmin = Engine_Api::_()->getItem('user', $selected_user_id);

      $sitegroup_title = $sitegroup->title;
      $group_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id)), 'sitegroup_entry_view') . ">$sitegroup_title</a>";

      $host = $_SERVER['HTTP_HOST'];
      $group_url = $host . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id)), 'sitegroup_entry_view');

      Engine_Api::_()->getApi('mail', 'core')->sendSystem($newManageAdmin->email, 'SITEGROUP_MANAGEADMIN_EMAIL', array(
              'group_title_with_link' => $group_title_with_link,
              'sender' => $viewer->toString(),
              'group_url' => $group_url,
              'queue' => true
      ));

      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      $notifyApi->addNotification($newManageAdmin, $viewer, $sitegroup, 'sitegroup_manageadmin');

      //INCREMENT MESSAGE COUNTER.
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
    }
  }

	//ACTINO FOR USER AUTO-SUGGEST LIST
  public function manageAutoSuggestAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GETTING THE GROUP ID.
    $group_id = $this->_getParam('group_id', $this->_getParam('id', null));

		//FETCH DATA
		$user_idarray = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->linkedGroups($group_id);

    $user_id_array = '';
    if (!empty($user_idarray)) {
      foreach ($user_idarray as $key => $user_ids) {
        $user_id_array = $user_ids['user_id'] . ',' . $user_id_array;
      }
    }
    $user_id_array = $user_id_array . '0';
    $noncreate_owner_level = array();
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
      $can_create = 0;
      if ($level->type != "public") {
        $can_create = Engine_Api::_()->authorization()->getPermission($level->level_id, 'sitegroup_group', 'create');
        if (empty($can_create)) {
          $noncreate_owner_level[] = $level->level_id;
        }
      }
    }
    
    $membershiptable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
    $membershiptableName = $membershiptable->info('name');
    
    $usertable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $usertable->info('name');
    $select = $usertable->select()
    
    //start: special condition for Group plugin only.
              ->from($userTableName)
              ->setIntegrityCheck(false)
              ->join($membershiptableName, "$membershiptableName.user_id = $userTableName.user_id", null)
              ->where($membershiptableName . '.group_id = ?', $group_id)
    //end: special condition for Group plugin only.
    
            ->where('displayname  LIKE ? ', '%' . $this->_getParam('text') . '%')
            ->where($userTableName . '.user_id NOT IN (' . $user_id_array . ')')
            ->order('displayname ASC')
            ->limit($this->_getParam('limit', 40));

    if (!empty($noncreate_owner_level)) {
      $str = (string) ( is_array($noncreate_owner_level) ? "'" . join("', '", $noncreate_owner_level) . "'" : $noncreate_owner_level );
      $select->where($userTableName . '.level_id not in (?)', new Zend_Db_Expr($str));
    }

    //FETCH ALL RESULT.
    $userlists = $usertable->fetchAll($select);
    $data = array();

		foreach ($userlists as $userlist) {
			$content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
			$data[] = array(
							'id' => $userlist->user_id,
							'label' => $userlist->displayname,
							'photo' => $content_photo
						);
    }

    if ($this->_getParam('sendNow', true)) {

      //RETURN TO THE RETRIVE RESULT.
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  //THIS ACTION FOR DELETE MANAGE ADMIN AND CALLING FROM THE CORE.JS FILE.
  public function deleteAction() {

    $manageadmin_id = (int) $this->_getParam('managedelete_id');
    $owner_id = (int) $this->_getParam('owner_id');
    $group_id = (int) $this->_getParam('group_id');
    $manageAdmintable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');
    $manageAdmintable->delete(array('manageadmin_id = ?' => $manageadmin_id));

//		//START SITEBUISNESSMEMBER PLUGIN WORK
//		$sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
//		if ($sitegroupMemberEnabled) {
// 			$membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
////       $membersTable->delete(array('resource_id = ?' => $page_id, 'user_id = ?' => $owner_id));
//      //$manageAdmintable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');
//      $manageAdmintable->delete(array('group_id = ?' => $group_id, 'user_id = ?' => $owner_id));
//      $membersTable->delete(array('group_id = ?' => $group_id, 'user_id = ?' => $owner_id));
//		}
//		//END SITEBUISNESSMEMBER PLUGIN WORK

    //STAR WORK SITEGROUP INTREGRATION.
		$sitegroupintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration');
    if(!empty($sitegroupintegrationEnabled)) {
			$contentsTable = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration');
			$contentsTable->delete(array('resource_owner_id = ?' => $owner_id, 'group_id = ?' => $group_id ));
    }
    //END WORK OF SITEGROUP INTREGRATION.
  }

	//ACTION FOR FEATURED ADMIN
  public function listAction() {

		//SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //GET GROUP ID AND GROUP OBJECT
    $group_id = $this->_getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //EDIT PRIVACY
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $manageTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');

		//FETCH DATA
    $this->view->owners = $manageTable->getManageAdmin($group_id);

    //CHECK POST
    if ($this->getRequest()->isPost()) {

      //GET VALUES FROM FORM
      $values = $this->getRequest()->getPost();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $manageTable->update(array('featured' => 0), array('group_id = ?' => $group_id));

        foreach ($values as $key => $value) {
          $manageTable->update(array('featured' => 1), array('user_id = ?' => $key, 'group_id = ?' => $group_id));
        }
        $db->commit();
        $this->_forwardCustom('success', 'utility', 'core', array(
                'smoothboxClose' => 500,
                'parentRedirect' => $this->_helper->url->url(array('action' => 'featured-owners', 'group_id' => $group_id), 'sitegroup_dashboard'),
                'parentRedirectTime' => '1',
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Featured admins of your group have been updated successfully.'))
        ));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }
}
?>