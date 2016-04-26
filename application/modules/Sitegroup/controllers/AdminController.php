<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_AdminController extends Core_Controller_Action_Admin {

  //ACTION FOR MAKE THE SITEGROUP FEATURED/UNFEATURED
  public function featuredAction() {

    $group_id = $this->_getParam('id');
    if (!empty($group_id)) {
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
      $sitegroup->featured = !$sitegroup->featured;
      $sitegroup->save();
    }
    $this->_redirect('admin/sitegroup/viewsitegroup');
  }

  //ACTION FOR MAKE THE SITEGROUP OPEN/CLOSED
  public function opencloseAction() {

    $group_id = $this->_getParam('id');
    if (!empty($group_id)) {
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
      $sitegroup->closed = !$sitegroup->closed;
      $sitegroup->save();
    }
    $this->_redirect('admin/sitegroup/viewsitegroup');
  }

  //ACTION FOR MAKE SPONSORED /UNSPONSORED
  public function sponsoredAction() {

    $group_id = $this->_getParam('id');
    if (!empty($group_id)) {
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
      $sitegroup->sponsored = !$sitegroup->sponsored;
      $sitegroup->save();
    }
    $this->_redirect('admin/sitegroup/viewsitegroup');
  }

  //ACTION FOR MAKE SITEGROUP APPROVE/DIS-APPROVE
  public function approvedAction() {

    global $sitegroup_is_auth;
    $group_id = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $email = array();
    try {
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
      if (!empty($sitegroup_is_auth)) {
        $sitegroup->approved = !$sitegroup->approved;
      }

      if (!empty($sitegroup->approved)) {
        if (!empty($sitegroup->pending)) {
          $sendActiveMail = 1;
          $sitegroup->pending = 0;
        }

        if (empty($sitegroup->aprrove_date)) {
          $sitegroup->aprrove_date = date('Y-m-d H:i:s');
        }

        $diff_days = 0;
        $package = $sitegroup->getPackage();
        if (($sitegroup->expiration_date !== '2250-01-01 00:00:00' && !empty($sitegroup->expiration_date) && $sitegroup->expiration_date !== '0000-00-00 00:00:00') && date('Y-m-d', strtotime($sitegroup->expiration_date)) > date('Y-m-d')) {
          $diff_days = round((strtotime($sitegroup->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
        }


        if (($diff_days <= 0 && $sitegroup->expiration_date !== '2250-01-01 00:00:00') || empty($sitegroup->expiration_date) || $sitegroup->expiration_date == '0000-00-00 00:00:00') {
          if (!$package->isFree()) {
            if ($sitegroup->status != "active") {
              $relDate = new Zend_Date(time());
              $relDate->add((int) 1, Zend_Date::DAY);
              $sitegroup->expiration_date = date('Y-m-d H:i:s', $relDate->toValue());
            } else {

              $expirationDate = $package->getExpirationDate();
              if (!empty($expirationDate))
                $sitegroup->expiration_date = date('Y-m-d H:i:s', $expirationDate);
              else
                $sitegroup->expiration_date = '2250-01-01 00:00:00';
            }
          }else {

            $expirationDate = $package->getExpirationDate();
            if (!empty($expirationDate))
              $sitegroup->expiration_date = date('Y-m-d H:i:s', $expirationDate);
            else
              $sitegroup->expiration_date = '2250-01-01 00:00:00';
          }
        }
        if ($sendActiveMail) {
          Engine_Api::_()->sitegroup()->sendMail("ACTIVE", $sitegroup->group_id);
          if (!empty($sitegroup) && !empty($sitegroup->draft) && empty($sitegroup->pending)) {
            Engine_Api::_()->sitegroup()->attachGroupActivity($sitegroup);
          }
        } else {
          Engine_Api::_()->sitegroup()->sendMail("APPROVED", $sitegroup->group_id);
        }
      } else {
        Engine_Api::_()->sitegroup()->sendMail("DISAPPROVED", $sitegroup->group_id);
      }
      $sitegroup->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitegroup/viewsitegroup');
  }

  //ACTION FOR MAKE SITEGROUP APPROVE/DIS-APPROVE
  public function renewAction() {

    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      $group_id = $this->_getParam('id');
      if ($this->getRequest()->isPost()) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
          $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
          if (!empty($sitegroup->approved)) {
            $package = $sitegroup->getPackage();
            if ($sitegroup->expiration_date !== '2250-01-01 00:00:00') {

              $expirationDate = $package->getExpirationDate();
              $expiration = $package->getExpirationDate();

              $diff_days = 0;
              if (!empty($sitegroup->expiration_date) && $sitegroup->expiration_date !== '0000-00-00 00:00:00') {
                $diff_days = round((strtotime($sitegroup->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
              }
              if ($expiration) {
                $date = date('Y-m-d H:i:s', $expiration);

                if ($diff_days >= 1) {

                  $diff_days_expiry = round((strtotime($date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                  $incrmnt_date = date('d', time()) + $diff_days_expiry + $diff_days;
                  $incrmnt_date = date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m"), $incrmnt_date));
                } else {
                  $incrmnt_date = $date;
                }

                $sitegroup->expiration_date = $incrmnt_date;
              } else {
                $sitegroup->expiration_date = '2250-01-01 00:00:00';
              }
            }
            if ($package->isFree())
              $sitegroup->status = "initial";
            else
              $sitegroup->status = "active";
          }
          $sitegroup->search = 1;
          $sitegroup->save();
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }

        $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
        ));
      }
    }
    $this->renderScript('admin/renew.tpl');
  }

  //ACTION FOR DELETE THE SITEGROUP
  public function deleteAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->group_id = $group_id = $this->_getParam('id');

    if ($this->getRequest()->isPost()) {
    
      //START SUB GROUP WORK
			$getSubGroupids = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getsubGroupids($group_id);
			foreach($getSubGroupids as $getSubGroupid) {
				Engine_Api::_()->sitegroup()->onGroupDelete($getSubGroupid['group_id']);
			}
			//END SUB GROUP WORK

      Engine_Api::_()->sitegroup()->onGroupDelete($group_id);
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    $this->renderScript('admin/delete.tpl');
  }

  //ACTION FOR CHANGE THE OWNER OF THE GROUP
  public function changeOwnerAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET GROUP ID
    $this->view->group_id = $group_id = $this->_getParam('id');

    //FORM
    $form = $this->view->form = new Sitegroup_Form_Admin_Changeowner();

    //SET ACTION
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    //GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //OLD OWNER ID
    $oldownerid = $sitegroup->owner_id;

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
      $values = $form->getValues();

      //GET USER ID WHICH IS NOW NEW USER
      $changeuserid = $values['user_id'];

      //CHANGE USER TABLE
      $changed_user = Engine_Api::_()->getItem('user', $changeuserid);

      //OWNER USER TABLE
      $user = Engine_Api::_()->getItem('user', $sitegroup->owner_id);

      //GROUP URL
      $group_url = Engine_Api::_()->sitegroup()->getGroupUrl($sitegroup->group_id);

      //GET GROUP TITLE
      $grouptitle = $sitegroup->title;

      //GROUP OBJECT LINK
      $groupobjectlink = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view');

      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //UPDATE GROUP TABLE
        Engine_Api::_()->getDbtable('groups', 'sitegroup')->update(array('owner_id' => $changeuserid), array('group_id = ?' => $group_id));

        //GET GROUP URL
        $group_baseurl = 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true);

        //MAKING GROUP TITLE LINK
        $group_title_link = '<a href="' . $group_baseurl . '"  >' . $grouptitle . ' </a>';

        //GET ADMIN EMAIL
        $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;

        //EMAIL THAT GOES TO OLD OWNER
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'SITEGROUP_CHANGEOWNER_EMAIL', array(

            'group_title' => $grouptitle,
            'group_title_with_link' => $group_title_link,
            'object_link' => $group_baseurl,
            'site_contact_us_link' => 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/contact',
            'email' => $email,
            'queue' => true

        ));

        //EMAIL THAT GOES TO NEW OWNER
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($changed_user->email, 'SITEGROUP_BECOMEOWNER_EMAIL', array(
            'group_title' => $grouptitle,
            'group_title_with_link' => $group_title_link,
            'object_link' => $group_baseurl,
            'site_contact_us_link' => 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/contact',
            'email' => $email,
            'queue' => true

        ));


		    //START FOR INRAGRATION WORK WITH OTHER PLUGIN. DELETE ACCORDING TO GROUP ID.
				$sitegroupintegrationEnabled = Engine_Api::_()->getDbtable('modules',
				'core')->isModuleEnabled('sitegroupintegration');
			  if(!empty($sitegroupintegrationEnabled)) {
					$contentsTable = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration');
					$contentsTable->delete(array('group_id = ?' => $group_id));
				}
        //END FOR INRAGRATION WORK WITH OTHER PLUGIN.
        
				$sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules',
				'core')->isModuleEnabled('sitegroupmember');
				if(!empty($sitegroupmemberEnabled)) {
					$membershipTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
					$select = $membershipTable->select()->from($membershipTable->info('name'))->where('user_id = ?', $user->user_id)->where('resource_id = ?', $sitegroup->group_id);
          $getRow = $membershipTable->fetchRow($select);
          
					$select = $membershipTable->select()->from($membershipTable->info('name'), 'member_id')->where('user_id = ?', $changed_user->user_id)->where('resource_id = ?', $sitegroup->group_id);
          $getNewOwnerRow = (integer) $select->query()->fetchColumn();          
          
          if(!empty($getRow) && empty($getNewOwnerRow)) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
              $memberShip = $membershipTable->createRow();
              $getRowArray = $getRow->toArray();
              unset($getRowArray['member_id']);
              $memberShip->setFromArray($getRowArray);
              $memberShip->user_id = $changed_user->user_id;
              $memberShip->save();
              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }              
          }
				}              

        //UPDATE IN CONTENT GROUP TABLE
        Engine_Api::_()->getDbtable('contentgroups', 'sitegroup')->update(array('user_id' => $changeuserid), array('group_id = ?' => $group_id));

        //UPDATE PHOTO TABLE
        Engine_Api::_()->getDbtable('photos', 'sitegroup')->update(array('user_id' => $changeuserid), array('user_id = ?' => $oldownerid, 'group_id = ?' => $group_id));

        //UPDATE ALBUM TABLE
        Engine_Api::_()->getDbtable('albums', 'sitegroup')->update(array('owner_id' => $changeuserid), array('owner_id = ?' => $oldownerid, 'group_id = ?' => $group_id));

        //UPDATE AND DELETE IN MANAGE ADMIN TABLE
        Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->delete(array('user_id = ?' => $changeuserid, 'group_id = ?' => $group_id));
        Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->update(array('user_id' => $changeuserid), array('user_id = ?' => $oldownerid, 'group_id = ?' => $group_id));

        //COMMIT
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //SUCCESS
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 300,
              'parentRefresh' => 300,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The group owner has been changed succesfully.'))
      ));
    }
  }

  //ACTION FOR GETTING THE LIST OF USERS
  public function getOwnerAction() {

  	//GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $this->_getParam('group_id'));

    //USER TABLE
    $tableUser = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $tableUser->info('name');
    $noncreate_owner_level = array();
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
      $can_create = 0;
      if ($level->type != "public") {
        $can_create = Engine_Api::_()->authorization()->getPermission($level->level_id, 'sitegroup_group', 'edit');
        if (empty($can_create)) {
          $noncreate_owner_level[] = $level->level_id;
        }
      }
    }

    //SELECT
    $select = $tableUser->select()
            ->where('displayname  LIKE ? ', '%' . $this->_getParam('text') . '%')
            ->where('user_id !=?', $sitegroup->owner_id)
            ->order('displayname ASC')
            ->limit($this->_getParam('limit', 40));

    if (!empty($noncreate_owner_level)) {
      $str = (string) ( is_array($noncreate_owner_level) ? "'" . join("', '", $noncreate_owner_level) . "'" : $noncreate_owner_level );
      $select->where($userTableName . '.level_id not in (?)', new Zend_Db_Expr($str));
    }

    //FETCH
    $userlists = $tableUser->fetchAll($select);

    //MAKING DATA
    $data = array();
    $mode = $this->_getParam('struct');

    if ($mode == 'text') {
      foreach ($userlists as $userlist) {
        $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array(
                'id' => $userlist->user_id,
                'label' => $userlist->displayname,
                'photo' => $content_photo
        );
      }
    } else {
      foreach ($userlists as $userlist) {
        $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array(
                'id' => $userlist->user_id,
                'label' => $userlist->displayname,
                'photo' => $content_photo
        );
      }
    }

    return $this->_helper->json($data);
  }

  //ACTION FOR CHANGE THE CATEGORY OF THE GROUP
  public function changeCategoryAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET GROUP ID
    $this->view->group_id = $group_id = $this->_getParam('id');

    //GET SITEGROUP ITEM
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //GET CATEGORY ID
    $this->view->category_id = $previous_category_id = $sitegroup->category_id;

    //GET SUBCATEGORY
    $this->view->subcategory_id = $subcategory_id = $sitegroup->subcategory_id;

    //GET SUBSUBCATEGORY
    $this->view->subsubcategory_id = $subsubcategory_id = $sitegroup->subsubcategory_id;

    //GET ROW
    $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($subcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subcategory_name = $row->category_name;
    }

    $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($subsubcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subsubcategory_name = $row->category_name;
    }

    //FORM
    $form = $this->view->form = new Sitegroup_Form_Admin_Changecategory();

    //POPULATE
    $value['category_id'] = $sitegroup->category_id;
    $form->populate($value);

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $is_error = 0;
      //GET FORM VALUES
      $values = $form->getValues();
      if (empty($values['category_id'])) {
        $is_error = 1;
        $this->view->category_id = 0;
      }

      //SET ERROR
      if ($is_error == 1) {
        $error = $this->view->translate('Group Category * Please complete this field - it is required.');
        $this->view->status = false;
        $error = Zend_Registry::get('Zend_Translate')->_($error);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //SAVE VALUES
        $sitegroup->category_id = $values['category_id'];
        $sitegroup->subcategory_id = $values['subcategory_id'];
				$sitegroup->subsubcategory_id = $values['subsubcategory_id'];
        $sitegroup->save();
        $db->commit();

        //START SITEGROUPREVIEW CODE
        $sitegroupReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');
        if ($sitegroupReviewEnabled && $previous_category_id != $sitegroup->category_id) {
          Engine_Api::_()->getDbtable('ratings', 'sitegroupreview')->editGroupCategory($sitegroup->group_id, $previous_category_id, $sitegroup->category_id);
        }
        //END SITEGROUPREVIEW CODE
        
        //START SITEGROUPMEMBER CODE
        $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
        if ($sitegroupmemberEnabled && $previous_category_id != $sitegroup->category_id) {
          $db->query("UPDATE `engine4_sitegroup_membership` SET `role_id` = '0' WHERE `engine4_sitegroup_membership`.`group_id` = ". $sitegroup->group_id. ";");
        }
        //END SITEGROUPMEMBER CODE

        //PROFILE MAPPING WORK IF CATEGORY IS EDIT
        if ($previous_category_id != $sitegroup->category_id) {
          Engine_Api::_()->getDbtable('profilemaps', 'sitegroup')->editCategoryMapping($sitegroup);
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //SUCCESS
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 300,
              'parentRefresh' => 300,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The category has been changed successfully.'))
      ));
    }
  }

  //ACTION FOR DISPLAY/HIDE FIELDS OF SEARCH FORM
  public function diplayFormAction() {

    $field_id = $this->_getParam('id');
    $display = $this->_getParam('display');
    if (!empty($field_id)) {
      Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitegroup', 'searchformsetting_id =?' => (int) $field_id));
    }
    $this->_redirect('admin/sitegroup/settings/form-search');
  }

}
?>