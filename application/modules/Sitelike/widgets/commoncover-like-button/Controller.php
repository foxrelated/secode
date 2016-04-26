<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Widget_CommoncoverLikeButtonController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //WE CAN GET THE VIEWER ID.
    $subject = Engine_Api::_()->core()->getSubject();
    $this->view->resource_id = $resource_id = $subject->getIdentity();
    $this->view->resource_type = $subjectType = $subject->getType(); 
    
    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //HERE WE CAN FOUND THE MODULE NAME AND MODULE IS ENABLE.
    $moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		if ($moduleName == 'user') { 
			$user_id = $subject->user_id; 
		} 
    $modulesEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($moduleName);

    $this->view->hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($subjectType, $resource_id);

    $mixsettingstable = Engine_Api::_()->getDbtable('mixsettings', 'sitelike');
    //$sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?' => $subjectType,'module = ?' => $moduleName, 'enabled' => 1));
    $resource_type = $mixsettingstable->select()
										->from($mixsettingstable->info('name'), 'resource_type')
										->where('resource_type = ?', $subjectType)
										->where('module = ?', $moduleName)
										->where('enabled = ?', 1)
										->query()
										->fetchColumn();

    //CHECK FOR VIEWER ID AND MODULE NAME AND MODULE ENABLE.
    if (empty($modulesEnabled) || empty($viewer_id) || empty($moduleName) || empty($subjectType)) {
      return $this->setNoRender();
    }
    
    //FOR TIME LINE PLUGIN.
    if ($moduleName == 'timeline') {
			$moduleName = 'user';
    }

    //For Like Setitngs .
    if ($moduleName == 'user') {
      $likesetting_table = Engine_Api::_()->getDbtable('mysettings', 'sitelike');
      $select = $likesetting_table->select()->from($likesetting_table, 'user_id')->where('user_id = ?',       $viewer_id)->query()->fetchColumn();
      if (!empty($select) && $user_id == $viewer_id) {
        return $this->setNoRender();
      }
    }

  }
}