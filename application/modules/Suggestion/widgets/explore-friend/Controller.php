<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Widget_ExploreFriendController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $is_WelcomePage = false;
    $is_pluginEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
    if( !empty($is_pluginEnabled) ) {
      $is_WelcomePage = Engine_Api::_()->advancedactivity()->getPageObj($this->view->identity, 'welcometab');
    }

    if( empty($is_WelcomePage) ) {
      $this->view->isAjaxEnabled = $isAjaxEnabled = $this->_getParam('isAjaxEnabled', 1);
    }else {
      // $is_pluginEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
      if( !empty($is_pluginEnabled) ) {
				$this->view->isAjaxEnabled = $isAjaxEnabled = 0;
				$getCustomBlockSettings = Engine_Api::_()->advancedactivity()->getCustomBlockSettings(array('suggestion.explore-friend'));
				if( empty($getCustomBlockSettings) ) {
				  return $this->setNoRender();
				}else {
					$this->view->is_welcomeTab_enabled = true;
				}
      }
    }
    // $this->view->getLayout = $getLayout = $this->_getParam('getLayout', 0);

    $limit = $this->_getParam('itemCountPerPage', 30);
		if( empty($limit) ){ $limit = 30; }
		$this->view->limit = $limit;

    $loadFlage = 0;
    $isShowSugg = Engine_Api::_()->suggestion()->isSuggestion('explore');

    if(empty($isShowSugg))
        $this->view->noSuggestionAvailable = true;

    if (!empty($isAjaxEnabled)) {
      $this->view->ModInfoArray = array('init_div_id' => 'suggestion_explore_widgets', 'widget_name' => 'explore-friend');
      if (!empty($_GET['loadFlage'])) {
        $loadFlage = 1;
        $this->view->modArray = Engine_Api::_()->suggestion()->mix_suggestions($limit, 'explore');
      }
    } else {
      $modArray = Engine_Api::_()->suggestion()->mix_suggestions($limit, 'explore');
      $this->view->modArray = $modArray;
    }
    $this->view->loadFlage = $loadFlage;
  }

}
?>