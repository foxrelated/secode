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
class Siteadvsearch_Widget_SearchContentsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->search_value = Zend_Controller_Front::getInstance()->getRequest()->getParam('query', null);
    $this->view->type = Zend_Controller_Front::getInstance()->getRequest()->getParam('type', null);
    $this->view->show_resourcetype_option = $this->_getParam('show_resourcetype_option', 0);
    $simpleArray[] = array('resource_type' => 'all', 'resource_title' => 'All Results');
    $items = Engine_Api::_()->getDbtable('contents', 'siteadvsearch')->getContentTypes(3)->toArray();

    $this->view->items = array_merge($simpleArray, $items);
    $this->view->max = $this->_getParam('advsearch_showmore', 8);
    
    $this->view->showLocationSearch = 0;
    $this->view->showLocationBasedContent = $this->_getParam('showLocationBasedContent', 0);
    $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
    $this->view->locationValue = isset($_GET['searchLocation']) ? $_GET['searchLocation'] : '';
    if ($this->view->showLocationBasedContent && !isset($_GET['searchLocation']) && isset($getMyLocationDetailsCookie['location']) && !empty($getMyLocationDetailsCookie['location'])) {
        $this->view->locationValue = $getMyLocationDetailsCookie['location'];
    }     
    if(Engine_Api::_()->hasModuleBootstrap('sitecitycontent') && $this->_getParam('showLocationSearch', 0)) {
        $this->view->showLocationSearch = 1;
        $this->view->locationspecific = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);
        if($this->view->locationspecific) {
            $locations = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getLocations(array('status' => 1));
            $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
            $locationsArray = array();
            $locationsArray[0] = $view->translate('Select Location');
            foreach ($locations as $location) {
                $locationsArray[$location->location] = $location->title;
            }
            $this->view->locationArray = $locationsArray;
        }
    }    

    $siteadvsearch_search_content = Zend_Registry::isRegistered('siteadvsearch_search_content') ? Zend_Registry::get('siteadvsearch_search_content') : null;
    if (empty($siteadvsearch_search_content))
      return $this->setNoRender();
  }

}