<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemenu_Widget_MenuMainController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $vars['viewer'] = $viewer = Engine_Api::_()->user()->getViewer();
        $vars['viewer_id'] = $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $viewer_level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $viewer_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $cache = Zend_Registry::get('Zend_Cache');
        $cacheName = 'main_menu_html_for_' . $viewer_level_id;

        // DON'T SHOW WIDGET, IF PLUGIN NOT ACTIVATED.
        $isPluginActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.isActivate', false);
        if (empty($isPluginActivate))
            return $this->setNoRender();

        if (!$viewer->getIdentity()) {
            $showOnLoggedOut = $this->_getParam('sitemenu_on_logged_out', 1);
            $vars['isGuest'] = 1;
            if (empty($showOnLoggedOut)) {
                return $this->setNoRender();
            }
        }

        $vars['noOfTabs'] = $this->_getParam('sitemenu_totalmenu', 6);
        $vars['truncationLimitContent'] = $this->_getParam('sitemenu_truncation_limit_content', 20);
        $vars['truncationLimitCategory'] = $this->_getParam('sitemenu_truncation_limit_category', 20);
        $vars['isMobile'] = $isMobile = Engine_Api::_()->seaocore()->isMobile();
        $vars['moreLink'] = $this->_getParam('sitemenu_is_more_link', 1);

        $isTabletDevice = Engine_Api::_()->seaocore()->isTabletDevice();
        if ($isMobile && !$isTabletDevice)
            $vars['noOfTabs'] = $this->_getParam('sitemenu_totalmenu_mobile', 4);
        elseif ($isMobile && $isTabletDevice)
            $vars['noOfTabs'] = $this->_getParam('sitemenu_totalmenu_tablet', 6);

        $vars['viewType'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu_view_type', 1);

        if (Engine_Api::_()->sitemenu()->isCurrentTheme('luminous')) {
            $vars['viewType'] = 1;
        }

        $temp_search_type = $this->_getParam('sitemenu_show_in_main_options', null);

        //WORK FOR ADVANCED SEARCH PLUGIN
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteadvsearch') && $temp_search_type == null) {
            $temp_search_type = 5;
        } elseif ($temp_search_type == null) {
            $temp_search_type = 3;
        }

        $vars['changeMyLocation'] = $isChangeMyLocation = $this->_getParam('changeMyLocation', 0);
        $vars['showOption'] = $temp_search_type;

        $show_cart = $this->_getParam('sitemenu_show_cart', 0);
        if (!empty($show_cart)) {
            $vars['sitestoreproductEnable'] = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct');
            if (!empty($vars['sitestoreproductEnable'])) {
                $vars['show_cart'] = $show_cart;
                $vars['itemCount'] = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getProductCounts();
            }
        }

        //WORK FOR CACHEING
        $isCacheEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.cache.enable', true);
        $data = $cache->load($cacheName);
//    if (!empty($isCacheEnabled) && !empty($data) && ($temp_search_type != 2) && empty($show_cart) && empty($isChangeMyLocation))
        if (!empty($isCacheEnabled) && !empty($data) && ($temp_search_type != 2) && empty($show_cart)) {
            return $this->view->response = $data;
        }

        if (!empty($isCacheEnabled))
            $getMainMenuArray = Engine_Api::_()->sitemenu()->getCachedMenus('core_main');
        else
            $getMainMenuArray = Engine_Api::_()->sitemenu()->getMainMenuArray('core_main');

        $vars['mainMenusArray'] = $getMainMenuArray;
        $vars['is_box_shadow'] = $this->_getParam('sitemenu_box_shadow', 1);
        $vars['show_extra'] = $this->_getParam('sitemenu_show_extra_on', 0);
        $vars['sitemenu_separator_style'] = $this->_getParam('sitemenu_separator_style', 3);
        $vars['sitemenu_more_link_icon'] = $this->_getParam('sitemenu_more_link_icon', 1);
        $vars['sitemenu_is_arrow'] = $this->_getParam('sitemenu_is_arrow', 1);
        $vars['sitemenu_corner_rounding'] = $this->_getParam('sitemenu_menu_corners_style', 0);
        $vars['sitemenu_main_menu_height'] = $this->_getParam('sitemenu_main_menu_height', 20);

        $sitemenu_check_main_menu = Zend_Registry::isRegistered('sitemenu_check_main_menu') ? Zend_Registry::get('sitemenu_check_main_menu') : null;
        $tempHostType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.global.view', 0);
        $sitemenuManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.manage.type', 1);
        $sitemenuInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.info.type', 1);
        $sitemenuGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.global.type', null);

        $hostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        for ($check = 0; $check < strlen($hostType); $check++) {
            $tempHostType += @ord($hostType[$check]);
        }

        if (empty($sitemenuGlobalType) && (empty($sitemenu_check_main_menu) || ($sitemenuManageType != $tempHostType)))
            return $this->setNoRender();

        $arr_values = array();
        $arr_values['sitemenu_menu_link_color'] = $this->_getParam('sitemenu_menu_link_color', '#ffffff');
        $arr_values['sitemenu_menu_background_color'] = $this->_getParam('sitemenu_menu_background_color', '#000000');
        $arr_values['sitemenu_menu_hover_color'] = $this->_getParam('sitemenu_menu_hover_color', '#000000');
        $arr_values['sitemenu_sub_link_color'] = $this->_getParam('sitemenu_sub_link_color', '#000000');
        $arr_values['sitemenu_sub_background_color'] = $this->_getParam('sitemenu_sub_background_color', '#ffffff');
        $arr_values['sitemenu_sub_hover_color'] = $this->_getParam('sitemenu_sub_hover_color', '#ffffff');
        $arr_values['sitemenu_menu_hover_background_color'] = $this->_getParam('sitemenu_menu_hover_background_color', '#ffffff');
        $arr_values['sitemenu_totalmenu'] = $this->_getParam('sitemenu_totalmenu', 6);
        $arr_values['sitemenu_is_fixed'] = $this->_getParam('sitemenu_is_fixed', 0);
        $arr_values['sitemenu_fixed_height'] = $this->_getParam('sitemenu_fixed_height', 0);
        $arr_values['sitemenu_style'] = $this->_getParam('sitemenu_style', 1);

        $vars['arr_values'] = $arr_values;
        $vars['requestAllParams'] = $requestAllParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        $vars['cssClassArray'] = $cssClassArray = array(
            '1' => 'standard_nav',
            '2' => 'multi_column',
            '3' => 'main_ContentView',
            '4' => 'mixed_menu',
        );
        if ($isMobile) {
            $vars['show_cart'] = 0;
            $vars['showOption'] = 1;
        }

        $vars['identity'] = $this->view->identity;

        $this->view->vars = $vars;
        $this->view->response = $this->view->partial(
                '_mainMenu.tpl', 'sitemenu', $vars
        );
        if (!empty($isCacheEnabled) && ($temp_search_type != 2) && empty($show_cart)) {
            $cache->setLifetime(Engine_Api::_()->sitemenu()->cacheLifeInSec());
            $cache->save($this->view->response, $cacheName);
        }
    }

}
