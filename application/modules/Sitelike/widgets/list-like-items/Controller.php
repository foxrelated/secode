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
class Sitelike_Widget_ListLikeItemsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    
    //GET THE LIKE BUTTON SETTING.
    $this->view->like_setting_button = $coreSettings->getSetting('like.setting.button');
    $likeBrowseShow = $coreSettings->getSetting('like.browse.auth');
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    if (empty($likeBrowseShow) && empty($viewer_id)) {
      return $this->setNoRender();
    }

    //GET THE RESOURCE TYPE.
    $resource_type = $this->_getParam('resource_type', null);

    $getResults = Engine_Api::_()->getDbtable('mixsettings', 'sitelike')->getResults(array('resource_type' => $resource_type, 'enabled' => 1, 'column_name' => array('module', 'resource_type', 'resource_id')));
    
    if (empty($getResults)) {
      return $this->setNoRender();
    }
    
    //GET THE VIEWER ID.
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->module_name = $getResults[0]['module'];
    $this->view->resource_type = $getResults[0]['resource_type'];
    $this->view->id = $getResults[0]['resource_id'];
    
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( $this->view->module_name ) ;
    if(empty($moduleEnabled)) {
			return $this->setNoRender();
    }

    $likesetting_result = Engine_Api::_()->sitelike()->allSettingsWidget($getResults[0]['resource_type']);

    //HERE WE CAN CHECK WHEN NO ROW FETCH FROM THE TABLE
    if ($likesetting_result != null) {
      //HERE WE CAN CONVERT THE RESULT IN TO THE ARRAY FORM
      $this->view->likesetting = $likesetting_array = $likesetting_result->toarray();
    }
    
    if (!empty($likesetting_array))
      $return_array = Engine_Api::_()->sitelike()->allDurationWidget($likesetting_array, $getResults[0]['resource_type'], $getResults[0]['resource_id']);
      
    if (isset($return_array['paginator']))
      $this->view->paginator = $likes_result = $return_array['paginator'];

    $tab_show_values = '';
    if (isset($return_array['tab_show_values']))
      $tab_show_values = $return_array['tab_show_values'];

    $this->view->active_tab = $tab_show_values;
    if (!empty($tab_show_values)) {
			$this->view->duration = $likesetting_result['tab'.$tab_show_values.'_duration'];
    }
    
    //CONDITION CHECK FOR PAGINATIOR
    if (!empty($likes_result)) {
      //CONDITION CHECK FOR ITEM
      if (count($likes_result) <= 0) {
        return $this->setNoRender();
      }
    } else {
      return $this->setNoRender();
    }
    $this->view->ajaxrequest = '';
  }
}