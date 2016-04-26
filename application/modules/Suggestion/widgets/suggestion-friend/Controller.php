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
class Suggestion_Widget_SuggestionFriendController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $modType = 'friend';
        $loadFlage = 0;
        $is_activityPlugin = false;

        $this->view->getLayout = $getLayout = $this->_getParam('getLayout', 0);

        $is_WelcomePage = false;
        $is_pluginEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
    if( !empty($is_pluginEnabled) ) {
            $is_WelcomePage = Engine_Api::_()->advancedactivity()->getPageObj($this->view->identity, 'welcometab');
        }

    if( empty($is_WelcomePage) ) {
            $this->view->isAjaxEnabled = $isAjaxEnabled = $this->_getParam('getWidAjaxEnabled', 1);
            $this->view->getWidLimit = $limit = $this->_getParam('getWidLimit', 3);
    }else {
            $this->view->is_pluginEnabled = $is_pluginEnabled;
      if( !empty($is_pluginEnabled) ) {
                $this->view->isAjaxEnabled = $isAjaxEnabled = 0;
                $is_activityPlugin = true;
                $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('welcome.dis.pymk.limit', 20);
                $getCustomBlockSettings = Engine_Api::_()->advancedactivity()->getCustomBlockSettings(array('suggestion.suggestion-friend'));
				if( empty($getCustomBlockSettings) ) {
                    return $this->setNoRender();
				}else {
                    $this->view->is_welcomeTab_enabled = true;
                }
            }
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->is_activityPlugin = $is_activityPlugin;

        $isSuggEnabled = Engine_Api::_()->getApi('modInfo', 'suggestion')->isModEnabled('suggestion');

        $isContent = Engine_Api::_()->suggestion()->isSuggestion($modType);

        if (empty($viewer_id) || empty($isContent) || empty($isSuggEnabled)) {
            return $this->setNoRender();
        }

        //Sitemobile Ajax variable
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->isAjaxEnabled = $isAjaxEnabled = 0;
            //Sitemobile code, check for when to display suggestions in mobile. (When user have less friends then friendMaxLimit)
            $friendMaxLimit = $this->_getParam('friendMaxLimit', 100);
            if ($viewer->member_count > $friendMaxLimit) {
                return $this->setNoRender();
            }
            $this->view->suggestionView = $this->_getParam('suggestionView', 'list');
            $this->view->carouselView = $this->_getParam('carouselView', '0');
        }//End Sitemobile code.

        if (!empty($isAjaxEnabled)) {
            $ModInfoArray = array('init_div_id' => 'suggestion_friend_widgets', 'widget_name' => 'suggestion-friend');
            if (!empty($_GET['loadFlage'])) {
                $loadFlage = 1;
                $modArray = Engine_Api::_()->suggestion()->getSuggestions($modType, $limit);
                $ModInfoArray = array_merge($modArray, $ModInfoArray);
            }
            $this->view->modArray = $ModInfoArray;
        } else {
            $modArray = Engine_Api::_()->suggestion()->getSuggestions($modType, $limit);
            $this->view->modArray = $modArray;
        }
        $this->view->loadFlage = $loadFlage;
    }

}
