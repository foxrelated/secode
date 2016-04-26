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
class Sitegroup_MobiController extends Core_Controller_Action_Standard {

  protected $_navigation;

  //SET THE VALUE FOR ALL ACTION DEFAULT
  public function init() {

		//GROUP VIEW AUTHORIZATION
    if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'view')->isValid())
      return;

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
        ->addActionContext('rate', 'json')
        ->addActionContext('validation', 'html')
        ->initContext();

		//GET GROUP URL AND GROUP ID
    $group_url = $this->_getParam('group_url', $this->_getParam('group_url', null));
    $group_id = $this->_getParam('group_id', $this->_getParam('group_id', null));

    if ($group_url) {
      $id = Engine_Api::_()->sitegroup()->getGroupId($group_url);
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $id);
      if ($sitegroup) {
        Engine_Api::_()->core()->setSubject($sitegroup);
      }
    } elseif ($group_id) {
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
      if ($sitegroup) {
        Engine_Api::_()->core()->setSubject($sitegroup);
      }
    }
 
		//FOR UPDATE EXPIRATION
    if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.task.updateexpiredgroups') + 900) <= time()) {
      Engine_Api::_()->sitegroup()->updateExpiredGroups();
    }
  }

  //ACTION FOR SHOWING THE GROUP LIST
  public function indexAction() {

		//GROUP VIEW AUTHORIZATION
    if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'view')->isValid())
      return;

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

  //ACTION FOR SHOWING THE HOME GROUP
  public function homeAction() {

		//GROUP VIEW AUTHORIZATION
    if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'view')->isValid())
      return;

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

	//ACTION FOR VIEW PROFILE GROUP
  public function viewAction() {

		//RETURN IF SUBJECT IS NOT SET
    if (!$this->_helper->requireSubject('sitegroup_group')->isValid())
      return;

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

		//GET GROUP SUBJECT AND THEN CHECK VALIDATION
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    if (empty($sitegroup)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $group_id = $sitegroup->group_id;

    $levelHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.level.createhost', 0);

    $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.lsettings', 0);

    $memory_size = ini_get('memory_limit');
    $memory_Size_int_array = explode("M", $memory_size);
    $memory_Size_int = $memory_Size_int_array[0];
    if ($memory_Size_int <= 32)
      ini_set('memory_limit', '64M');
    $maxView = 19;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if (!Engine_Api::_()->sitegroup()->canViewGroup($sitegroup)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

		$current_date = date('Y-m-d H:i:s');
    $this->view->headScript()->appendFile($this->view->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/scripts/core.js')  
                             ->appendFile($this->view->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/scripts/hideTabs.js');
//     $sitegrouptable = Engine_Api::_()->getDbtable('vieweds', 'sitegroup');
// 
// 		//FUNCTION CALLING AND PASS GROUP ID AND VIEWER ID
// 		$sitegroupresult = Engine_Api::_()->getDbtable('groupstatistics', 'sitegroup')->getVieweds($viewer_id, $group_id);
// 
//     $count = count($sitegroupresult);
//     if (empty($count)) {
//       $row = $sitegrouptable->createRow();
//       $row->group_id = $group_id;
//       $row->viewer_id = $viewer_id;
//       $row->save();
//     } else {
//       $sitegrouptable->update(array('date' => $current_date), array('group_id = ?' => $group_id, 'viewer_id' => $viewer_id));
//     }

    //INCREMENT IN NUMBER OF VIEWS
    $owner = $sitegroup->getOwner();
   
    $sub_status_table = Engine_Api::_()->getDbTable('groupstatistics', 'sitegroup');
   
		//INCREMENT GROUP VIEWS DATE-WISE
    $values = array('group_id' => $sitegroup->group_id);

    $statObject = $sub_status_table->groupReportInsights($values);
    $raw_views = $sub_status_table->fetchRow($statObject);
    $raw_views_count = $raw_views['views'];
    if (!$owner->isSelf($viewer) || ($sitegroup->view_count == 1 && empty($raw_views_count))) {
      $sub_status_table->groupViewCount($group_id);
    }

    if (!$owner->isSelf($viewer)) {
      $sitegroup->view_count++;
    }

    $sitegroup->save();

		//CHECK TO SEE IF PROFILE STYLES IS ALLOWED
    $style_perm = 1;

    if ($style_perm) {

      //GET STYLE
      $table = Engine_Api::_()->getDbtable('styles', 'core');
      $select = $table->select()
              ->where('type = ?', $sitegroup->getType())
              ->where('id = ?', $sitegroup->getIdentity())
              ->limit();

      $row = $table->fetchRow($select);
      if (null !== $row && !empty($row->style)) {
        $this->view->headStyle()->appendStyle($row->style);
      }
    }

    if (null !== ($tab = $this->_getParam('tab'))) {
      $friend_tab_function = <<<EOF
                                        var content_id = "$tab";
                                        this.onload = function()
                                        {
      																		if(window.tabContainerSwitch) 
      																		{
                                              tabContainerSwitch($('main_tabs').getElement('.tab_' + content_id));
																					}
                                        }
EOF;
      $this->view->headScript()->appendScript($friend_tab_function);
    }    
    
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
  
		// Start: Suggestion work.
    $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    // Here we are delete this poll suggestion if viewer have.
    if (!empty($is_moduleEnabled)) {
      Engine_Api::_()->getApi('suggestion', 'sitegroup')->deleteSuggestion($viewer->getIdentity(), 'sitegroup_group', $group_id, 'group_suggestion');
    }
    // End: Suggestion work.
    
		if ($coreversion < '4.1.0') {

			$this->_helper->content->render();
		} else {
			$this->_helper->content->setNoRender()->setEnabled();
		}
  }
}
?>