<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: MobiController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_MobiController extends Core_Controller_Action_Standard {

  protected $_navigation;

	//COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {

		//AUTHENTICATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'view')->isValid())
      return;
  }

  //ACTION FOR BROWSE PAGE
  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
          ->setNoRender()
          ->setEnabled()
      ;
    }
  }

  //ACTION FOR HOME PAGE
  public function homeAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
          ->setNoRender()
          ->setEnabled();
    }
  }

	//ACTION FOR LISTING PROFILE PAGE
  public function viewAction() {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

		//SET LISTING SUBJECT
    $listing_id = $this->_getParam('listing_id');
    $list = Engine_Api::_()->getItem('list_listing', $this->_getParam('listing_id'));
    Engine_Api::_()->core()->setSubject($list);

		//WHO CAN VIEW THE LISTINGS
    if( !$this->_helper->requireAuth()->setAuthParams($list, null, 'view')->isValid() ) {
			return $this->_forward('requireauth', 'error', 'core');
    }

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		//GET LEVEL SETTING
		$can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'list_listing', 'view');

		//AUTHORIZATION CHECK
    if($can_view != 2 && ((empty($list->draft) || empty($list->search) || empty($list->approved)) && ($list->owner_id != $viewer_id))) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if ($list) {

      //INCREASE THE VIEWS BY ONE
      $list->view_count++;
      $list->save();

			//SET VIEWS
      Engine_Api::_()->getDbtable('vieweds', 'list')->setVieweds($listing_id, $viewer_id);

			//GET SUBJECT AND OWNER
      $subject = Engine_Api::_()->core()->getSubject();
      $owner = Engine_Api::_()->getItem('user', $list->owner_id);

			//IF PROFILE STYLE IS ALLOWED
      $style_perm = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $owner->level_id, 'style');
      if ($style_perm) {

        //GET STYLE
        $table = Engine_Api::_()->getDbtable('styles', 'core');

        $select = $table->select()
                ->where('type = ?', 'list_listing')
                ->where('id = ?', $listing_id)
                ->limit();

        $row = $table->fetchRow($select);
        if (null != $row && !empty($row->style)) {
          $this->view->headStyle()->appendStyle($row->style);
        }
      }
    }

    if (null != ($tab = $this->_getParam('tab'))) {
      //provide widgties page
      $friend_tab_function = <<<EOF
                                        var content_id = "$tab";
                                        this.onload = function()
                                        {
                                                tabContainerSwitch($('main_tabs').getElement('.tab_' + content_id));
                                        }
EOF;
      $this->view->headScript()->appendScript($friend_tab_function);
    }

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
          ->setNoRender()
          ->setEnabled();
    }
  }

}