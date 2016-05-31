<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Widget_SearchBoxController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->widgetName = $this->_getParam('widgetName', null);

        //ENABLED CONTENT TYPES WILL BE SHOWING IN MAIN SEARCH BOX WHEN CLICK IN BOX
        $this->view->defaultContents = $defaultContents = Engine_Api::_()->getDbtable('contents', 'siteadvsearch')->getContentTypes(4);

        //LIMIT OF CONTENT TYPES WHICH WILL COME ON SEARCHING OF TEXT IN SEARCH BOX 
        $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteadvsearch.showmore', 5);
        $width = '275';
        if(Engine_Api::_()->hasModuleBootstrap('spectacular')) {
            $tableMenusItem = Engine_Api::_()->getDbTable('menuItems', 'core');
            $menuCartId = $tableMenusItem->select()
                    ->from($tableMenusItem->info('name'), 'id')
                    ->where('name like (?)', '%sitemenu_mini_cart%')
                    ->where('enabled =?', 1)
                    ->query()
                    ->fetchColumn();

            $menuTicketId = $tableMenusItem->select()
                    ->from($tableMenusItem->info('name'), 'id')
                    ->where('name like (?)', '%core_mini_siteeventticketmytickets%')
                    ->where('enabled =?', 1)
                    ->query()
                    ->fetchColumn();

            if ($menuCartId && $menuTicketId) {
                $width = '255';
            } 
        } 
        $this->view->searchbox_width = $this->_getParam('advsearch_search_box_width', $width);
        $this->view->advsearch_search_box_width_for_nonloggedin = $this->_getParam('advsearch_search_box_width_for_nonloggedin', $width);
        $this->view->totalLimit = $limit + count($defaultContents) + 1;

        $this->view->showLocationSearch = 0;
        $this->view->showLocationBasedContent = $this->_getParam('showLocationBasedContent', 0);
        $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
        $this->view->locationValue = '';
        if (isset($getMyLocationDetailsCookie['location']) && !empty($getMyLocationDetailsCookie['location'])) {
            $this->view->locationValue = $getMyLocationDetailsCookie['location'];
        }
        if (Engine_Api::_()->hasModuleBootstrap('sitecitycontent') && $this->_getParam('showLocationSearch', 0)) {
            $this->view->showLocationSearch = 1;
            $this->view->locationspecific = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);
            if ($this->view->locationspecific) {
                $locations = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getLocations(array('status' => 1));
                $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                $locationsArray = array();
                $locationsArray[0] = $view->translate('Select Location');
                foreach ($locations as $location) {
                    $locationsArray[$location->location] = $location->title;
                }
                $this->view->locationArray = $locationsArray;
            }

//        $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
//        $this->view->locationValue = '';
//        if (isset($getMyLocationDetailsCookie['location']) && !empty($getMyLocationDetailsCookie['location'])) {
//            $this->view->locationValue = $getMyLocationDetailsCookie['location'];
//        }
        }

        $siteadvsearch_searchbox = Zend_Registry::isRegistered('siteadvsearch_searchbox') ? Zend_Registry::get('siteadvsearch_searchbox') : null;
        $showSearchBox = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteadvsearch.show.search.box', 1);
        $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (empty($siteadvsearch_searchbox) || (empty($showSearchBox) && empty($viewerId)))
            return $this->setNoRender();
    }

}
