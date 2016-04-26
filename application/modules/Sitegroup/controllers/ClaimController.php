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
class Sitegroup_ClaimController extends Core_Controller_Action_Standard {

  protected $_navigation;

  //ACTION FOR CLAIMING THE GROUP
  public function indexAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET LEVEL ID
    $level_id = 0;
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $authorizationTable = Engine_Api::_()->getItemTable('authorization_level');
      $authorization = $authorizationTable->fetchRow(array('type = ?' => 'public', 'flag = ?' => 'public'));
      if (!empty($authorization))
        $level_id = $authorization->level_id;
    }

    //CHECK USER HAVE TO ALLOW CLAIM OR NOT
    $allow_claim = Engine_Api::_()->authorization()->getPermission($level_id, 'sitegroup_group', 'claim');
    $getPackageClaim = Engine_Api::_()->sitegroup()->getPackageAuthInfo('sitegroup');
    if (empty(Engine_Api::_()->getApi('settings', 'core')->sitegroup_claimlink) || empty($allow_claim)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //FETCH
    $params = array();
    $params['viewer_id'] = $viewer_id;
    $params['limit'] = $this->_getParam('limit', 40);
    $usersitegroups = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getSuggestClaimGroup($params);

    //IF THERE IS NO GROUPS THEN SHOWING THE TIP THERE IS NO GROUPS
    if (!empty($usersitegroups)) {
      $usersitegroup = $usersitegroups->toarray();
      if (empty($usersitegroup))
        $this->view->showtip = 1;
    }

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

    //GROUP ID
    $group_id = $this->_getParam('group_id', null);

    //FORM
    $this->view->form = $form = new Sitegroup_Form_Claim();

    //POPULATE FORM
    if (!empty($viewer_id)) {
      $value['email'] = $viewer->email;
      $value['nickname'] = $viewer->displayname;
      $form->populate($value);
    }

    //CHECK FORM VALIDAION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      if ($group_id == 0) {
        $error = $this->view->translate('This is an invalid group name. Please select a valid group name from the autosuggest given below.');
        $this->view->status = false;
        $error = Zend_Registry::get('Zend_Translate')->_($error);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }
      $values = $form->getValues();

      //GET SITEGROUP ITEM
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
      if (!empty($sitegroup)) {
        $items = array();
        $items['group_id'] = $group_id;
        $items['viewer_id'] = $viewer_id;
        $claimgroups = Engine_Api::_()->getDbtable('claims', 'sitegroup')->getClaimStatus($items);
        if (!empty($claimgroups)) {
          if ($claimgroups->status == 3 || $claimgroups->status == 4) {
            $error = $this->view->translate("You have already filed a claim for the group: \"%s\", which is either on hold or is awaiting action by administration.", $sitegroup->title);
            $this->view->status = false;
            $form->getElement("group_id")->setValue("0");
            $error = Zend_Registry::get('Zend_Translate')->_($error);
            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
          } elseif ($claimgroups->status == 2) {
            $error = $this->view->translate("You have already filed a claim for the group: \"%s\", which has been declined by the site admin.", $sitegroup->title);
            $this->view->status = false;
            $error = Zend_Registry::get('Zend_Translate')->_($error);
            $form->getDecorator('errors')->setOption('escape', false);
            $form->getElement("group_id")->setValue("0");
            $form->addError($error);
            return;
          }
        }
      }
      //GET EMAIl
      $email = $values['email'];

      //CHECK EMAIL VALIDATION
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);
      if (!$validator->isValid($email)) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter a valid email address.'));
        return;
      }
      
      //GET FROM EMAIL
       $fromEmail = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;

      //GET ADMIN EMAIL
			$coreApiSettings = Engine_Api::_()->getApi('settings', 'core');
		  $adminEmail = $coreApiSettings->getSetting('core.mail.contact', $coreApiSettings->getSetting('core.mail.from', "email@domain.com"));
			if(!$adminEmail) $adminEmail = $coreApiSettings->getSetting('core.mail.from', "email@domain.com");

      //GET CLAIM TABLE
      $tableClaim = Engine_Api::_()->getDbTable('claims', 'sitegroup');
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //SAVE VALUES
        if (!empty($getPackageClaim)) {
          //GET SITEGROUP ITEM
          $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

          //GET GROUP URL
          $group_url = Engine_Api::_()->sitegroup()->getGroupUrl($group_id);

          //SEND SITEGROUP TITLE TO THE TPL
          $group_title = $sitegroup->title;

          //SEND CLAIM OWNER EMAIL
          if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.claim.email', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup_claimlink', 1) ) {
						Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminEmail, 'SITEGROUP_CLAIMOWNER_EMAIL', array(
								'group_title' => $group_title,
								'group_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
								Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true) . '"  >' . $group_title . ' </a>',
								'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
								Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true),
								'email' => $coreApiSettings->getSetting('core.mail.from', "email@domain.com"),
								'queue' => true
						));
					}

          $row = $tableClaim->createRow();
          $row->group_id = $group_id;
          $row->user_id = $viewer_id;
          $row->about = $values['about'];
          $row->nickname = $values['nickname'];
          $row->email = $email;
          $row->contactno = $values['contactno'];
          $row->usercomments = $values['usercomments'];
          $row->status = 3;
          $row->save();
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->view->successmessage = 1;
    }
  }

  //ACTION FOR SHOW GROUPS ON WHICH I HAVE CLAIMED
  public function myGroupsAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK CLAIM IS ENABLED OR NOT
    $claimEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.claimlink', 1);
    if (empty($claimEnabled)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //CHECK THAT MEMBER HAS ALLOED FOR CLAIM OR NOT
    $canClaim = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitegroup_group', 'claim');
    if (empty($canClaim)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main', array(), 'sitegroup_main_manage');

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('claims', 'sitegroup')->getMyClaimGroups($viewer_id);

    //GET PAGINATOR
    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.group', 10);
    $paginator->setItemCountPerPage($items_count);
    $this->view->paginator = $paginator->setCurrentPageNumber(10);
  }

  //ACTION FOR CLAIM A GROUP FROM THE GROUP PROFILE GROUP
  public function claimGroupAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $authorizationTable = Engine_Api::_()->getItemTable('authorization_level');
      $authorization = $authorizationTable->fetchRow(array('type = ?' => 'public', 'flag = ?' => 'public'));
      if (!empty($authorization))
        $level_id = $authorization->level_id;
    }

    $getPackageClaim = Engine_Api::_()->sitegroup()->getPackageAuthInfo('sitegroup');
    $this->_helper->layout->setLayout('default-simple');

    //GET GROUP ID
    $group_id = $this->_getParam('group_id', null);

    //SET PARAMS
    $paramss = array();
    $paramss['group_id'] = $group_id;
    $paramss['viewer_id'] = $viewer_id;
    $inforow = Engine_Api::_()->getDbtable('claims', 'sitegroup')->getClaimStatus($paramss);

		$this->view->status = 0;
    if (!empty($inforow)) {
      $this->view->status = $inforow->status;
		}
		//GET ADMIN EMAIL
		$coreApiSettings = Engine_Api::_()->getApi('settings', 'core');
		$adminEmail = $coreApiSettings->getSetting('core.mail.contact', $coreApiSettings->getSetting('core.mail.from', "email@domain.com"));
		if(!$adminEmail) $adminEmail = $coreApiSettings->getSetting('core.mail.from', "email@domain.com");

    //CHECK STATUS
    if ($this->view->status == 2) {
      echo '<div class="global_form" style="margin:15px 0 0 15px;"><div><div><h3>' . $this->view->translate("Alert!") . '</h3>';
      echo '<div class="form-elements" style="margin-top:10px;"><div class="form-wrapper" style="margin-bottom:10px;">' . $this->view->translate("You have already send a request to claim for this group which has been declined by the site admin.") . '</div>';
      echo '<div class="form-wrapper"><button onclick="parent.Smoothbox.close()">' . $this->view->translate("Close") . '</button></div></div></div></div></div>';
    }

    $this->view->claimoption = $claimoption = Engine_Api::_()->authorization()->getPermission($level_id, 'sitegroup_group', 'claim');

    //FETCH
    $paramss = array();
    $this->view->userclaim = $userclaim = 0;
    $paramss['group_id'] = $group_id;
    $paramss['limit'] = 1;
    $groupclaiminfo = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getSuggestClaimGroup($paramss);

    if (!$claimoption || !$groupclaiminfo[0]['userclaim'] || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.claimlink', 1)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if (isset($groupclaiminfo[0]['userclaim'])) {
      $this->view->userclaim = $userclaim = $groupclaiminfo[0]['userclaim'];
    }

    if ($inforow['status'] == 3 || $inforow['status'] == 4) {
      echo '<div class="global_form" style="margin:15px 0 0 15px;"><div><div><h3>' . $this->view->translate("Alert!") . '</h3>';
      echo '<div class="form-elements" style="margin-top:10px;"><div class="form-wrapper" style="margin-bottom:10px;">' . $this->view->translate("You have already filed a claim for this group: \"%s\", which is either on hold or is awaiting action by administration.", Engine_Api::_()->getItem('sitegroup_group', $group_id)->title) . '</div>';
      echo '<div class="form-wrapper"><button onclick="parent.Smoothbox.close()">' . $this->view->translate("Close") . '</button></div></div></div></div></div>';
    }

    if (!$inforow['status'] && $claimoption && $userclaim) {
      //GET FORM
      $this->view->form = $form = new Sitegroup_Form_Claimgroup();

      //POPULATE FORM
      if (!empty($viewer_id)) {
        $value['email'] = $viewer->email;
        $value['nickname'] = $viewer->displayname;
        $form->populate($value);
      }

      if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
        //GET FORM VALUES
        $values = $form->getValues();

        //GET EMAIL
        $email = $values['email'];

        //CHECK EMAIL VALIDATION
        $validator = new Zend_Validate_EmailAddress();
        $validator->getHostnameValidator()->setValidateTld(false);
        if (!$validator->isValid($email)) {
          $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter a valid email address.'));
          return;
        }

        //GET CLAIM TABLE
        $tableClaim = Engine_Api::_()->getDbTable('claims', 'sitegroup');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
          //SAVE VALUES
          if (!empty($getPackageClaim)) {

            //GET SITEGROUP ITEM
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

            //GET GROUP URL
            $group_url = Engine_Api::_()->sitegroup()->getGroupUrl($group_id);

            //SEND SITEGROUP TITLE TO THE TPL
            $group_title = $sitegroup->title;

           //SEND CHANGE OWNER EMAIL
						if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.claim.email', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.claimlink', 1)) {
							Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminEmail, 'SITEGROUP_CLAIMOWNER_EMAIL', array(
									'group_title' => $group_title,
									'group_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
									Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true) . '"  >' . $group_title . ' </a>',
									'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
									Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true),
									'email' => $coreApiSettings->getSetting('core.mail.from', "email@domain.com"),
									'queue' => true
							));
						}

            $row = $tableClaim->createRow();
            $row->group_id = $group_id;
            $row->user_id = $viewer_id;
            $row->about = $values['about'];
            $row->nickname = $values['nickname'];
            $row->email = $email;
            $row->contactno = $values['contactno'];
            $row->usercomments = $values['usercomments'];
            $row->status = 3;
            $row->save();
          }
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefreshTime' => '60',
            'parentRefresh' => 'true',
            'format' => 'smoothbox',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your request has been send successfully. You will now receive an email confirming Admin approval of your request.'))
        ));
      }
    }
  }

  //ACTION FOR GETTING GROUPS ON WHICH USER CAN CLAIM
  public function getGroupsAction() {

    //FETCH
    $paramss = array();
    $paramss['title'] = $this->_getParam('text');
    $paramss['viewer_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
    $paramss['limit'] = $this->_getParam('limit', 40);
    $paramss['orderby'] = 'title ASC';
    $usersitegroups = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getSuggestClaimGroup($paramss);
    $data = array();
    $mode = $this->_getParam('struct');
    if ($mode == 'text') {
      foreach ($usersitegroups as $usersitegroup) {
        $content_photo = $this->view->itemPhoto($usersitegroup, 'thumb.icon');
        $data[] = array(
            'id' => $usersitegroup->group_id,
            'label' => $usersitegroup->title,
            'photo' => $content_photo
        );
      }
    } else {
      foreach ($usersitegroups as $usersitegroup) {
        $content_photo = $this->view->itemPhoto($usersitegroup, 'thumb.icon');
        $data[] = array(
            'id' => $usersitegroup->group_id,
            'label' => $usersitegroup->title,
            'photo' => $content_photo
        );
      }
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR DELETING THE CLAIM REQUEST
  public function deleteAction() {

  	//CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //GET CLAIM ID
    $claim_id = $this->_getParam('claim_id', 'null');

    //GET CLAIM ITEM
    $claim = Engine_Api::_()->getItem('sitegroup_claim', $claim_id);
    if ($claim->user_id != Engine_Api::_()->user()->getViewer()->getIdentity()) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //GET FORM
    $this->view->form = $form = new Sitegroup_Form_Deleteclaim();

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET CLAIM TABLE
      Engine_Api::_()->getDbtable('claims', 'sitegroup')->delete(array('claim_id=?' => $claim_id));
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully deleted your claim request.'))
      ));
    }
  }

  //ACTION FOR SHOWING THE TREM OF THE CLAIM
  public function termsAction() {

    $this->_helper->layout->setLayout('default-simple');
  }
}

?>
