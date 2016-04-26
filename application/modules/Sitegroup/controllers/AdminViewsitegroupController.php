<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminViewsitegroupController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_AdminViewsitegroupController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE GROUPS
  public function indexAction() {

    if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.task.updateexpiredgroups') + 900) <= time()) {
      Engine_Api::_()->sitegroup()->updateExpiredGroups();
    }

    //TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_viewsitegroup');

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitegroup_Form_Admin_Manage_Filter();

    //GET GROUP ID
    $group = $this->_getParam('page', 1);

    //MAKE QUERY
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');

    $tableGroup = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $tableGroupName = $tableGroup->info('name');
    
    $values = array();
      $select = $tableGroup->select()
              ->setIntegrityCheck(false)
              ->from($tableGroupName)
              ->joinLeft($tableUser, "$tableGroupName.owner_id = $tableUser.user_id", 'username');
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    foreach ($values as $key => $value) {

      if (null == $value) {
        unset($values[$key]);
      }
    }

    //SEARCHING
    $this->view->owner = '';
    $this->view->title = '';
    $this->view->sponsored = '';
    $this->view->approved = '';
    $this->view->featured = '';
    $this->view->status = '';
    $this->view->groupbrowse = '';
    $this->view->category_id = '';
    $this->view->subcategory_id = '';
    $this->view->subsubcategory_id = '';
    $this->view->package_id = '';

    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      $packageTable = Engine_Api::_()->getDbtable('packages', 'sitegroup');

      $packageselect = $packageTable->select()->from($packageTable->info("name"), array("package_id", "title"))->order("package_id DESC");
      $this->view->packageList = $packageTable->fetchAll($packageselect);
    }


    $values = array_merge(array(
        'order' => 'group_id',
        'order_direction' => 'DESC',
            ), $values);


    if (!empty($_POST['owner'])) {
      $user_name = $_POST['owner'];
    } elseif (!empty($_GET['owner'])) {
      $user_name = $_GET['owner'];
    } else {
      $user_name = '';
    }


    if (!empty($_POST['title'])) {
      $group_name = $_POST['title'];
    } elseif (!empty($_GET['title'])) {
      $group_name = $_GET['title'];
    } elseif ($this->_getParam('title', '')) {
      $group_name = $this->_getParam('title', '');
    } else {
      $group_name = '';
    }

    //SEARCHING
    $this->view->owner = $values['owner'] = $user_name;
    $this->view->title = $values['title'] = $group_name;

    if (!empty($group_name)) {
      $select->where($tableGroupName . '.title  LIKE ?', '%' . $group_name . '%');
    }

    if (!empty($user_name)) {
      $select->where($tableUser . '.displayname  LIKE ?', '%' . $user_name . '%');
    }

    if (isset($_POST['search'])) {

      if (!empty($_POST['sponsored'])) {
        $this->view->sponsored = $_POST['sponsored'];
        $_POST['sponsored']--;

        $select->where($tableGroupName . '.sponsored = ? ', $_POST['sponsored']);
      }
      if (!empty($_POST['group_status'])) {

        $this->view->group_status = $_POST['group_status'];
        switch ($this->view->group_status) {
          case 1:
            $select->where($tableGroupName . '.aprrove_date  IS NULL');
            break;
          case 2:
            $select->where($tableGroupName . '.approved = ? ', 1);
            break;
          case 3:
            $select->where($tableGroupName . '.aprrove_date  IS NOT NULL');
            $select->where($tableGroupName . '.approved = ? ', 0);
            break;
          case 4:
            $select->where($tableGroupName . '.declined  = ? ', 1);
            break;
        }
      }
      if (!empty($_POST['featured'])) {
        $this->view->featured = $_POST['featured'];
        $_POST['featured']--;
        $select->where($tableGroupName . '.featured = ? ', $_POST['featured']);
      }
      if (!empty($_POST['status'])) {
        $this->view->status = $_POST['status'];
        $_POST['status']--;
        $select->where($tableGroupName . '.closed = ? ', $_POST['status']);
      }

      if (!empty($_POST['package_id'])) {
        $this->view->package_id = $_POST['package_id'];
        $select->where($tableGroupName . '.package_id = ? ', $_POST['package_id']);
      }
      if (!empty($_POST['groupbrowse'])) {
        $this->view->groupbrowse = $_POST['groupbrowse'];
        $_POST['groupbrowse']--;
        if ($_POST['groupbrowse'] == 0) {
          $select->order($tableGroupName . '.view_count DESC');
        } elseif ($_POST['groupbrowse'] == 1) {
          $select->order($tableGroupName . '.creation_date DESC');
        } elseif ($_POST['groupbrowse'] == 2) {
          $select->order($tableGroupName . '.comment_count DESC');
        } elseif ($_POST['groupbrowse'] == 3) {
          $select->order($tableGroupName . '.like_count DESC');
        }
      }

      if (!empty($_POST['category_id']) && empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
        $this->view->category_id = $_POST['category_id'];
        $select->where($tableGroupName . '.category_id = ? ', $_POST['category_id']);
      } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
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

        $select->where($tableGroupName . '.category_id = ? ', $_POST['category_id'])
                ->where($tableGroupName . '.subcategory_id = ? ', $_POST['subcategory_id']);
      } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && !empty($_POST['subsubcategory_id'])) {

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
        $select->where($tableGroupName . '.category_id = ? ', $_POST['category_id'])
                ->where($tableGroupName . '.subcategory_id = ? ', $_POST['subcategory_id'])
                ->where($tableGroupName . '.subsubcategory_id = ? ', $_POST['subsubcategory_id']);
        ;
      }
    }

    $this->view->formValues = array_filter($values);
    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'group_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPageNumber($group);
  }

  //VIEW GROUP DETAIL
  public function detailAction() {

    $id = $this->_getParam('id');

    $tableGroup = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $tableGroupName = $tableGroup->info('name');

    $select = $tableGroup->select()
            ->setIntegrityCheck(false)
            ->from($tableGroupName)
            ->where($tableGroupName . '.group_id = ?', $id)
            ->limit(1);
    $this->view->sitegroupDetail = $detail = $tableGroup->fetchRow($select);

    $this->view->manageAdminEnabled = $manageAdminEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1);
    if (!empty($manageAdminEnabled)) {
      $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');
      $manageadminTableName = $manageadminTable->info('name');
      $select = $manageadminTable->select()
              ->from($manageadminTableName, array('COUNT(*) AS count'))
              ->where('group_id = ?', $id);
      $rows = $tableGroup->fetchAll($select)->toArray();
      $this->view->admin_total = $rows[0]['count'];
    }

    $this->view->category_id = $category_id = $detail['category_id'];
    $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($category_id);
    if (!empty($row->category_name)) {
      $this->view->category_name = $row->category_name;
    }
    $this->view->subcategory_id = $subcategory_id = $detail['subcategory_id'];
    $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($subcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subcategory_name = $row->category_name;
    }
    $this->view->subsubcategory_id = $subsubcategory_id = $detail['subsubcategory_id'];
    $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($subsubcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subsubcategory_name = $row->category_name;
    }
    //SITEGROUP-REVIEW PLUGIN IS INSTALLED OR NOT
    $this->view->isEnabledSitegroupreview = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');
  }

  //ACTION FOR MULTI-DELETE OF GROUPS
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {

      $values = $this->getRequest()->getPost();

      foreach ($values as $key => $value) {

        if ($key == 'delete_' . $value) {

          //DELETE SITEGROUPS FROM DATABASE
          $group_id = (int) $value;

          //START SUB GROUP WORK
          $getSubGroupids = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getsubGroupids($group_id);
          foreach ($getSubGroupids as $getSubGroupid) {
            Engine_Api::_()->sitegroup()->onGroupDelete($getSubGroupid['group_id']);
          }
          //END SUB GROUP WORK

          Engine_Api::_()->sitegroup()->onGroupDelete($group_id);
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  //ACTION FOR GROUP EDIT
  public function editAction() {

    //TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_viewsitegroup');

    //GET GROUP ID AND GROUP OBJECT
    $group_id = $this->_getParam('id');
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //FORM GENERATION
    $this->view->form = $form = new Sitegroup_Form_Admin_Manage_Edit();

    if (!empty($sitegroup->declined)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $status_groupOption = array();
    $approved = $sitegroup->approved;
    if (empty($sitegroup->aprrove_date) && empty($approved)) {
      $status_groupOption["0"] = "Approval Pending";
      $status_groupOption["1"] = "Approved Group";
      $status_groupOption["2"] = "Declined Group";
    } else {
      $status_groupOption["1"] = "Approved";
      $status_groupOption["0"] = "Dis-Approved";
    }
    $form->getElement("status_group")->setMultiOptions($status_groupOption);

    if (!$this->getRequest()->isPost()) {

      $form->getElement("closed")->setValue($sitegroup->closed);
      $form->getElement("status_group")->setValue($sitegroup->approved);
      $form->getElement("featured")->setValue($sitegroup->featured);
      $form->getElement("sponsored")->setValue($sitegroup->sponsored);
      $title = "<a href='" . $this->view->url(array('group_url' => $sitegroup->group_url), 'sitegroup_entry_view') . "'  target='_blank'>" . $sitegroup->title . "</a>";
      $form->title_dummy->setDescription($title);
      if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
        $form->package_title->setDescription("<a href='" . $this->view->url(array('route' => 'admin_default', 'module' => 'sitegroup', 'controller' => 'package', 'action' => 'packge-detail', 'id' => $sitegroup->package_id), 'admin_default') . "'  class ='smoothbox'>" . ucfirst($sitegroup->getPackage()->title) . "</a>");

        $package = $sitegroup->getPackage();
        if ($package->isFree()) {

          $form->getElement("status")->setMultiOptions(array("free" => "NA (Free)"));
          $form->getElement("status")->setValue("free");
          $form->getElement("status")->setAttribs(array('disable' => true));
        } else {
          $form->getElement("status")->setValue($sitegroup->status);
        }
      }
    } elseif ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //PROCESS
        $values = $form->getValues();
        if ($values['status_group'] == 2) {
          $values['declined'] = 1;
        } else {
          $approved = $values['status_group'];
        }
        $sitegroup->setFromArray($values);
        if (!empty($sitegroup->declined)) {
          Engine_Api::_()->sitegroup()->sendMail("DECLINED", $sitegroup->group_id);
        }
        $sitegroup->save();
        $db->commit();
        if ($approved != $sitegroup->approved) {

          return $this->_helper->redirector->gotoRoute(array('module' => 'sitegroup', 'controller' => 'admin', 'action' => 'approved', "id" => $group_id), "default", true);
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }
  
  //ACTION FOR EDIT CREATION DATE OF THE GROUPS.
  public function editCreationDateAction() {
  
  		//SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');
    
    //GET GROUP ID AND GROUP OBJECT
    $group_id = $this->_getParam('group_id');
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
		$groupExpiryDate = strtotime($sitegroup->expiration_date);
 
    //FORM GENERATION
    $form = $this->view->form = new Sitegroup_Form_Admin_editCreationDate();
    
    $creation_date = $sitegroup->creation_date;
		$form->populate($sitegroup->toArray());
 
    $form->populate(array(
			'starttime' => $creation_date,
			'endtime' => $sitegroup->expiration_date,
    ));

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();
      
      $modifiedCreationDate = strtotime($values['starttime']);
      if ($sitegroup->expiration_date != '2250-01-01 00:00:00') {
				if ($groupExpiryDate < $modifiedCreationDate) {
					$itemError = Zend_Registry::get('Zend_Translate')->_("Creation Date * This should be less than expiration date.");
					$form->creation_date->setValue($sitegroup->creation_date);
					$form->addError($itemError);
					return;
				}
 			}

			//BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
      
        $table = Engine_Api::_()->getDbtable('groups', 'sitegroup');
        $table->update(array('creation_date'=>  $values['starttime']), array('group_id =?' => $group_id));
        $db->commit();
        
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Creation Date of the Group has been edited successfully.'))
      ));
    }
  }
}

?>