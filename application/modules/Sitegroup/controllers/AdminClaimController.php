<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminClaimController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_AdminClaimController extends Core_Controller_Action_Admin {

  //ACTION FOR GETTING THE LIST OF CLAIMABLE GROUP CREATORS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_claim');

    //GET GROUP
    $group = $this->_getParam('page', 1);

    //FILTER FORM
    $this->view->formFilter = $formFilter = new Sitegroup_Form_Admin_Filter();

    //GET LIST MEMBER CLAIM TABLE
    $tableListMemberClaim = Engine_Api::_()->getDbtable('listmemberclaims', 'sitegroup');

    //GET LIST MEMBER CLAIM TABLE NAME
    $tableListMemberClaimsName = $tableListMemberClaim->info('name');

    //GET USER TABLE NAME
    $tableUserName = Engine_Api::_()->getDbtable('users', 'user')->info('name');

    //SELECTING THE USERS WHOSE GROUP CAN BE CLAIMED
    $select = $tableListMemberClaim->select()
            ->setIntegrityCheck(false)
            ->from($tableListMemberClaimsName)
            ->join($tableUserName, $tableUserName . '.user_id = ' . $tableListMemberClaimsName . '.user_id');
    $values = array();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    //VALUES
    $values = array_merge(array(
        'order' => "$tableListMemberClaimsName.user_id",
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    //SELECT
    $select->order((!empty($values['order']) ? $values['order'] : "$tableListMemberClaimsName.user_id" ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPagenumber($group);
  }

  //ACTION FOR GETTING THE LIST OF CLAIM MEMBER
  public function listclaimmemberAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM
    $form = $this->view->form = new Sitegroup_Form_Admin_Listclaimmember();

    //SET ACTION
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET VALUES
      $values = $form->getValues();

      //GET USER ID
      $userid = $values['user_id'];

      //CHECK USER ID
      if ($userid == 0) {
        $this->view->status = false;
        $error = Zend_Registry::get('Zend_Translate')->_('This is not a valid user name. Please select a user name from the autosuggest.');
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //GET LIST MEMBER CLAIM TABLE
        $table = Engine_Api::_()->getDbTable('listmemberclaims', 'sitegroup');

        //FETCH				
        $row = $table->fetchRow($table->getClaimListMember($userid));
        if ($row === null) {
          include APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license2.php';
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 300,
          'parentRefresh' => 300,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('The claimed group creator has been added successfully.'))
      ));
    }
  }

  //ACTION FOR GETTING THE MEMBER WHICH CAN BE CLAIMED THE GROUP
  function getmemberAction() {

    //FETCH USER LIST
    $userlists = Engine_Api::_()->getDbTable('listmemberclaims', 'sitegroup')->getMembers($this->_getParam('text'), $this->_getParam('limit', 40));

    //MAKING DATA
    $data = array();
    $mode = $this->_getParam('struct');
    if ($mode == 'text') {
      foreach ($userlists as $userlist) {
        $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array('id' => $userlist->user_id, 'label' => $userlist->displayname, 'photo' => $content_photo);
      }
    } else {
      foreach ($userlists as $userlist) {
        $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array('id' => $userlist->user_id, 'label' => $userlist->displayname, 'photo' => $content_photo);
      }
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR DELETEING THE CLAIMABLE GROUP CREATORS
  public function deleteClaimableMemberAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost()) {
      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //MEMBER DELETE
        Engine_Api::_()->getDbtable('listmemberclaims', 'sitegroup')->delete(array('user_id =?' => $this->_getParam('user_id')));
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
    //OUTPUT
    $this->renderScript('admin-claim/delete-claimable-member.tpl');
  }

  //ACTION FOR DISPLAYING THE LIST OP CLAIM GROUPS
  public function processclaimAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_claim');

    //FILTER FORM         
    $this->view->formFilter = $formFilter = new Sitegroup_Form_Admin_Filter();

    //CLAIM TABLE
    $tableClaim = Engine_Api::_()->getDbtable('claims', 'sitegroup');
    $tableClaimName = $tableClaim->info('name');

    //USER TABLE NAME
    $tableUserName = Engine_Api::_()->getDbtable('users', 'user')->info('name');

    //GROUP TABLE NAME
    $tableGroupName = Engine_Api::_()->getDbTable('groups', 'sitegroup')->info('name');

    //GET GROUP
    $group = $this->_getParam('page', 1);

    //SELECT
    $select = $tableClaim->select()
            ->setIntegrityCheck(false)
            ->from($tableClaimName)
            ->join($tableUserName, $tableUserName . '.user_id = ' . $tableClaimName . '.user_id', array('displayname'))
            ->join($tableGroupName, $tableGroupName . '.group_id = ' . $tableClaimName . '.group_id', array('title', 'owner_id'))
            ->group($tableClaimName . '.claim_id');

    //VALUES     
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    //VALUES
    $values = array_merge(array(
        'order' => 'group_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : "group_id" ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR			
    $this->view->paginator = array();
    include APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license2.php';
  }

  //ACTION FOR WHAT SHOULD BE HAPPEN WITH THE GROUPS WHICH ARE CLAIMED BY THE USERS
  public function takeActionAction() {

    //GET GROUP ID
    $this->view->sitegroup_id = $groupid = $this->_getParam('group_id');

    //GET CLAIM ID
    $claimid = $this->_getParam('claim_id');

    //GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $groupid);

    //SEND SITEGROUP TITLE TO THE TPL
		$grouptitle = $this->view->sitegroup_title = $sitegroup->title;

    //GET CLAIM ITEM
    $claiminfo = Engine_Api::_()->getItem('sitegroup_claim', $claimid);

    //GET CLAIM ROW
    $this->view->claiminfo = $claiminfo;

    //COMMENTS
    $comments_mail = '';

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost()) {
      //GET STATUS
      $status = $_POST['status'];

      //GET COMMENTS    
      $comments = $_POST['comments'];

      //CHECK COMMENTS VALIDATION
      if (!empty($comments)) {
        $comments_mail .= Zend_Registry::get('Zend_Translate')->_("Administrator's Comments: ") . $comments;
      }

      //GET MODIFIED DATE
      $modified_date = new Zend_Db_Expr('NOW()');

      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      //GET GROUP URL
      $group_url = Engine_Api::_()->sitegroup()->getGroupUrl($groupid);

      //GET ADMIN EMAIL
      $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;

      try {
        //GET SITEGROUP TABLE
        $tableGroups = Engine_Api::_()->getDbTable('groups', 'sitegroup');
        $tableClaim = Engine_Api::_()->getDbtable('claims', 'sitegroup');
        //CHECK STATUS
        if ($status != 2) {
          if ($status == 1) {
            //UPDATE GROUP TABLE
            $tableGroups->update(array('owner_id' => $claiminfo->user_id, 'userclaim' => 0), array('group_id = ?' => $groupid));

            //SEND CHANGE OWNER EMAIL
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(Engine_Api::_()->getItem('user', $sitegroup->owner_id)->email, 'SITEGROUP_CHANGEOWNER_EMAIL', array(
                'group_title' => $grouptitle,
                'group_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true) . '"  >' . $grouptitle . ' </a>',
                'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true),
                'site_contact_us_link' => 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/contact',
                'email' => $email,
                'queue' => true
            ));

            //START FOR INRAGRATION WORK WITH OTHER PLUGIN. DELETE ACCORDING TO GROUP ID.
					  $sitegroupintegrationEnabled = Engine_Api::_()->getDbtable('modules',
						'core')->isModuleEnabled('sitegroupintegration');
						if(!empty($sitegroupintegrationEnabled)) {
							$contentsTable = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration');
							$contentsTable->delete(array('group_id = ?' => $groupid));
						}
						//END FOR INRAGRATION WORK WITH OTHER PLUGIN.

            //UPDATE IN CONTENT GROUP TABLE
            Engine_Api::_()->getDbtable('contentgroups', 'sitegroup')->update(array('user_id' => $claiminfo->user_id), array('group_id = ?' => $groupid));

            //UPDATE AND DELETE IN MANAGE ADMIN TABLE
            Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->delete(array('user_id = ?' => $claiminfo->user_id, 'group_id = ?' => $groupid));
            Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->update(array('user_id' => $claiminfo->user_id), array('user_id = ?' => $sitegroup->owner_id, 'group_id = ?' => $groupid));

            //UPDATE PHOTO TABLE
            Engine_Api::_()->getDbtable('photos', 'sitegroup')->update(array('user_id' => $claiminfo->user_id), array('user_id = ?' => $sitegroup->owner_id, 'group_id = ?' => $groupid));

            //UPDATE ALBUM TABLE
            Engine_Api::_()->getDbtable('albums', 'sitegroup')->update(array('owner_id' => $claiminfo->user_id), array('owner_id = ?' => $sitegroup->owner_id, 'group_id = ?' => $groupid));

            //UPDATE CLAIM TABLE
            $tableClaim->update(array('status' => $status, 'comments' => $comments, 'modified_date' => $modified_date), array('claim_id = ?' => $claimid));

            //SEND EMAIL FOR CLAIM APPROVED
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($claiminfo->email, 'SITEGROUP_CLAIM_APPROVED_EMAIL', array(
                'group_title' => $grouptitle,
                'group_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true) . '"  >' . $grouptitle . ' </a>',
                'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true),
                'comments' => $comments_mail,
                'my_claim_groups_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'my-groups'), 'sitegroup_claimgroups', true),
                'email' => $email,
                'queue' => true
            ));
          } elseif ($status == 4) {
            //UPDATE CLAIM TABLE
            $tableClaim->update(array('status' => $status, 'comments' => $comments, 'modified_date' => $modified_date), array('claim_id = ?' => $claimid));
            //SEND EMAIL FOR CLAIM HOLD
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($claiminfo->email, 'SITEGROUP_CLAIM_HOLDING_EMAIL', array(
                'group_title' => $grouptitle,
                'group_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true) . '"  >' . $grouptitle . ' </a>',
                'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true),
                'comments' => $comments_mail,
                'email' => $email,
                'queue' => true
            ));
          }
        } else {
          //UPDATE CLAIM TABLE
          $tableClaim->update(array('status' => 2, 'comments' => $comments, 'modified_date' => $modified_date), array('group_id = ?' => $groupid, 'user_id=?' => $claiminfo->user_id));
          //SEND EMAIL FOR CLAIM DECLINED
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($claiminfo->email, 'SITEGROUP_CLAIM_DECLINED_EMAIL', array(
              'group_title' => $grouptitle,
              'group_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true) . '"  >' . $grouptitle . ' </a>',
              'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true),
              'comments' => $comments_mail,
              'email' => $email,
              'queue' => true
          ));
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //REDIRECTING
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 300,
          'parentRefresh' => 300,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your action has been submitted and email successfully sent to the claimer.'))
      ));
    }
  }

  //ACTION FOR DELETING THE CLAIM REQUEST OF THE USERS
  public function requestDeleteAction() {

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost()) {
      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //GET CLAIM ITEM
        $sitegroupclaim = Engine_Api::_()->getItem('sitegroup_claim', $this->_getParam('claim_id'));
        if ($sitegroupclaim) {
          $sitegroupclaim->delete();
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //REDIRECTING
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 20,
          'parentRefresh' => 20,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    //OUTPUT
    $this->renderScript('admin-claim/request-delete.tpl');
  }

  //ACTION FOR MULTI DELETING THE CLAIM REQUEST OF THE USERS
  public function multiDeleteRequestAction() {

    //GET CLAIM IDS
    $this->view->ids = $claim_ids = $this->_getParam('ids', null);

    //CONFIRM
    $confirm = $this->_getParam('confirm', false);

    //COUNT MEMBER IDS
    $this->view->count = count(explode(",", $claim_ids));

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $confirm == true) {
      //MAKING CLAIM IDS ARRAY
      $claim_ids_array = explode(",", $claim_ids);

      //DELETE CLAIM
      foreach ($claim_ids_array as $claim_id) {
        $sitegroupclaim = Engine_Api::_()->getItem('sitegroup_claim', $claim_id);
        if ($sitegroupclaim) {
          $sitegroupclaim->delete();
        }
      }

      //REDIRECTING
      $this->_helper->redirector->gotoRoute(array('action' => 'processclaim'));
    }
  }

  //ACTION FOR MULTIDELETING THE MEMBER OF CLAIMABLE GROUP CREATORS
  public function multiDeleteClaimableMemberAction() {

    //GET MEMEBR IDS
    $this->view->ids = $member_ids = $this->_getParam('ids', null);

    //CONFIRM
    $confirm = $this->_getParam('confirm', false);

    //COUNT MEMBER IDS
    $this->view->count = count(explode(",", $member_ids));

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $confirm == true) {
      //MAKING MEMBER IDS ARRAY
      $member_ids_array = explode(",", $member_ids);

      //DELETE MEMBER
      foreach ($member_ids_array as $member_id) {
        $sitegroupclaimmember = Engine_Api::_()->getItem('sitegroup_listmemberclaims', $member_id);
        if ($sitegroupclaimmember) {
          $sitegroupclaimmember->delete();
        }
      }

      //REDIRECTING
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

}

?>