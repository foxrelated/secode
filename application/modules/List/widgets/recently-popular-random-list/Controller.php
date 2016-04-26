<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Controller.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Widget_RecentlyPopularRandomListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET CORE API
		$this->view->settings = $settings = Engine_Api::_()->getApi('settings', 'core');

    $showTabArray = $settings->getSetting('list.ajax.widgets.list', array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => "5"));
    $ShowViewArray = $settings->getSetting('list.ajax.widgets.layout', array("0" => "1", "1" => "2", "2" => "3"));
    $defaultOrder = $settings->getSetting('list.ajax.layouts.oder', 1);
    $this->view->list_view = 0;
    $this->view->grid_view = 0;
    $this->view->map_view = 0;
    $this->view->defaultView = -1;
    if (in_array("1", $ShowViewArray)) {
      $this->view->list_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 1)
        $this->view->defaultView = 0;
    }
    if (in_array("2", $ShowViewArray)) {
      $this->view->grid_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 2)
        $this->view->defaultView = 1;
    }
    if (in_array("3", $ShowViewArray)) {
      $this->view->map_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 3)
        $this->view->defaultView = 2;
    }

    $listRecently = array();
    $listViewed = array();
    $listRandom = array();
    $listFeatured = array();
    $listSponosred = array();
    if (in_array("1", $showTabArray)) {
      $listRecently = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Recently Posted', array('limit' => "1"));
    }

    if (in_array("2", $showTabArray)) {
      $listViewed = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Most Viewed', array('limit' => "1"));
    }

    if (in_array("3", $showTabArray)) {
      $listRandom = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Random', array('limit' => "1"));
    }

    if (in_array("4", $showTabArray)) {
      $listFeatured = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Featured', array('limit' => "1"));
    }

    if (in_array("5", $showTabArray)) {
      $listSponosred = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Sponosred', array('limit' => "1"));
    }

    if ((!(count($listRecently) > 0) && !(count($listViewed) > 0) && !(count($listRandom) > 0 ) && !(count($listFeatured) > 0 ) && !(count($listSponosred) > 0 ) ) || ($this->view->defaultView == -1)) {
      return $this->setNoRender();
    }

    $this->view->active_tab1 = 0;
    $this->view->active_tab2 = 0;
    $this->view->active_tab3 = 0;
    $this->view->active_tab4 = 0;
    $this->view->active_tab5 = 0;
    $this->view->active_tab_list = 0;
    $this->view->active_tab_image = 0;

    if (count($listRecently) > 0) {
      $this->view->listings = $list = $listRecently = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Recently Posted');
      $this->view->active_tab1 = 1;
      $this->view->active_tab_list = (int) $settings->getSetting('list.recent.widgets', 10);
      $this->view->active_tab_image = (int) $settings->getSetting('list.recent.thumbs', 15);
    } else if (count($listViewed) > 0) {
      $this->view->listings = $list = $listViewed = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Most Viewed');
      ;
      $this->view->active_tab2 = 1;
      $this->view->active_tab_list = (int) $settings->getSetting('list.popular.widgets', 10);
      $this->view->active_tab_image = (int) $settings->getSetting('list.popular.thumbs', 15);
    } else if (count($listRandom) > 0) {
      $this->view->listings = $list = $listRandom = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Random');
      $this->view->active_tab3 = 1;
      $this->view->active_tab_list = (int) $settings->getSetting('list.random.widgets', 10);
      $this->view->active_tab_image = (int) $settings->getSetting('list.random.thumbs', 15);
    } else if (count($listFeatured) > 0) {
      $this->view->listings = $list = $listFeatured = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Featured');
      $this->view->active_tab4 = 1;
      $this->view->active_tab_list = (int) $settings->getSetting('list.featured.list', 10);
      $this->view->active_tab_image = (int) $settings->getSetting('list.featured.thumbs', 15);
    } else if (count($listSponosred) > 0) {
      $this->view->listings = $list = $listSponosred = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Sponosred');
      $this->view->active_tab5 = 1;
      $this->view->active_tab_list = (int) $settings->getSetting('list.sponsored.list', 10);
      $this->view->active_tab_image = (int) $settings->getSetting('list.sponsored.thumbs', 15);
    }

    if (count($listRecently) > 0) {
      $this->view->tab1_show = 1;
    }
    if (count($listViewed) > 0) {
      $this->view->tab2_show = 1;
    }
    if (count($listRandom) > 0) {
      $this->view->tab3_show = 1;
    }

    if (count($listFeatured) > 0) {
      $this->view->tab4_show = 1;
    }

    if (count($listSponosred) > 0) {
      $this->view->tab5_show = 1;
    }

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->list()->enableLocation();

    if (!empty($this->view->map_view)) {
      $this->view->flageSponsored = 0;
      if (!empty($checkLocation)) {
        $ids = array();
        $sponsored = array();
        foreach ($list as $list_listing) {
          $id = $list_listing->getIdentity();
          $ids[] = $id;
          $list_temp[$id] = $list_listing;
        }
        $values['listing_ids'] = $ids;

        $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'list')->getLocation($values);
        foreach ($locations as $location) {
          if ($list_temp[$location->listing_id]->sponsored) {
            $this->view->flageSponsored = 1;
            break;
          }
        }
        $this->view->list = $list_temp;
      }
    }

		//RATING IS ENABLED OR NOT
    $this->view->ratngShow = (int) $settings->getSetting('list.rating', 1);

    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
  }

}