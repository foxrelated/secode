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
class Suggestion_Widget_SuggestionMixController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $this->view->isAjaxEnabled = $isAjaxEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('recomended.ajax.enabled', 1);
        $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sugg.mix.wid', null);

        $loadFlage = 0;

        $isSuggEnabled = Engine_Api::_()->getApi('modInfo', 'suggestion')->isModEnabled('suggestion');
        $isContent = Engine_Api::_()->suggestion()->isSuggestion('mix');
        if (empty($isSuggEnabled) || empty($isContent)) {
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
            $this->view->ModInfoArray = array('init_div_id' => 'suggestion_mix_widgets', 'widget_name' => 'suggestion-mix');
            if (!empty($_GET['loadFlage'])) {
                $loadFlage = 1;
                $this->view->modArray = Engine_Api::_()->suggestion()->mix_suggestions($limit, 'mix');
            }
        } else {
            $modArray = Engine_Api::_()->suggestion()->mix_suggestions($limit, 'mix');
            $this->view->modArray = $modArray;
        }
        $this->view->loadFlage = $loadFlage;
    }

}
?>
