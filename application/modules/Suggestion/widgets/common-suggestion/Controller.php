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
class Suggestion_Widget_CommonSuggestionController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $loadFlage = 0;
        $this->view->mod_type = $modType = $this->_getParam('resource_type', 'friend');
        $this->view->getWidLimit = $limit = $this->_getParam('getWidLimit', 5);
        $this->view->isAjaxEnabled = $isAjaxEnabled = $this->_getParam('getWidAjaxEnabled', 1);
    if( strstr($modType, 'magentoint') ) {
            $limit = 20;
        }

        $init_div_id = 'suggestion_' . $modType . '_widgets';
        $widget_name = 'suggestion-' . $modType;

        $isSuggEnabled = Engine_Api::_()->getApi('modInfo', 'suggestion')->isModEnabled('suggestion');
        $isModEnabled = Engine_Api::_()->getApi('modInfo', 'suggestion')->isModEnabled($modType);
        $isSuggestionEnabled = Engine_Api::_()->getApi('modInfo', 'suggestion')->isSuggestionEnabled($modType);

        $isContent = Engine_Api::_()->suggestion()->isSuggestion($modType);

    if ( empty($isSuggestionEnabled) || empty($modType) || empty($isContent) || empty($isSuggEnabled) || empty($isModEnabled) ) {
            return $this->setNoRender();
        }

        //Sitemobile Ajax variable
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $limit = $this->_getParam('getWidLimit', '3');
            $this->view->isAjaxEnabled = $isAjaxEnabled = 0;
            $this->view->recommendationView = $this->_getParam('recommendationView', 'list');
            $this->view->carouselView = $this->_getParam('carouselView', '0');
        }//End Sitemobile code.

        if (!empty($isAjaxEnabled)) {
            $ModInfoArray = array('init_div_id' => $init_div_id, 'widget_name' => $widget_name);
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
?>
