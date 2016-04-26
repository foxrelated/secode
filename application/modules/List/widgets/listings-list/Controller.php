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
class List_Widget_ListingsListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->list_browse = $list_browse = Zend_Registry::isRegistered('list_browse') ? Zend_Registry::get('list_browse') : null;

    //GET SETTINGS
    $defaultOrder = $params['layouts_oder'] = $this->_getParam('layouts_oder', 1);
    $ShowViewArray = $params['layouts_views'] = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));
    $this->view->statistics = $params['statistics'] = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "reviewCount"));
    $defaultOrder = $params['layouts_oder'] = $this->_getParam('layouts_oder', 1);
    $this->view->is_ajax = $isAjax = $this->_getParam('isajax', 0);
    $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);

    //START VIEW MORE LINK AND AUTOSCROLL CONTENT WORK
    if (!empty($isAjax)) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    //END VIEW MORE LINK AND AUTOSCROLL CONTENT WORK

    $this->view->list_view = 0;
    $this->view->grid_view = 0;
    $this->view->map_view = 0;
    $this->view->defaultView = -1;
    if ($ShowViewArray && in_array("1", $ShowViewArray)) {
      $this->view->list_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 1)
        $this->view->defaultView = 0;
    }
    if ($ShowViewArray && in_array("2", $ShowViewArray)) {
      $this->view->grid_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 2)
        $this->view->defaultView = 1;
    }
    if ($ShowViewArray && in_array("3", $ShowViewArray)) {
      $this->view->map_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 3)
        $this->view->defaultView = 2;
    }

    if ($this->view->defaultView == -1) {
      return $this->setNoRender();
    }
    $customFieldValues = array();
    $values = array();

    $category = $params['category_id'] = $request->getParam('category_id', null);
    $category_id = $params['category'] = $request->getParam('category', null);
    $subcategory = $params['subcategory_id'] = $request->getParam('subcategory_id', null);
    $subcategory_id = $params['subcategory'] = $request->getParam('subcategory', null);
    $categoryname = $params['categoryname'] = $request->getParam('categoryname', null);
    $subcategoryname = $params['subcategoryname'] = $request->getParam('subcategoryname', null);
    $subsubcategory = $params['subsubcategory_id'] = $request->getParam('subsubcategory_id', null);
    $subsubcategory_id = $params['subsubcategory'] = $request->getParam('subsubcategory', null);
    $subsubcategoryname = $params['subsubcategoryname'] = $request->getParam('subsubcategoryname', null);

    if ($category)
      $_GET['category'] = $category;
    if ($subcategory)
      $_GET['subcategory'] = $subcategory;
    if ($categoryname)
      $_GET['categoryname'] = $categoryname;
    if ($subcategoryname)
      $_GET['subcategoryname'] = $subcategoryname;

    if ($subsubcategory)
      $_GET['subsubcategory'] = $subsubcategory;
    if ($subcategoryname)
      $_GET['subsubcategoryname'] = $subsubcategoryname;

    if ($category_id)
      $_GET['category'] = $values['category'] = $category_id;
    if ($subcategory_id)
      $_GET['subcategory'] = $values['subcategory'] = $subcategory_id;
    if ($subsubcategory_id)
      $_GET['subsubcategory'] = $values['subsubcategory'] = $subsubcategory_id;
    $values['tag'] = $params['tag'] = $request->getParam('tag', null);
    $values['tag_id'] = $params['tag_id'] = $request->getParam('tag_id', null);
    if (!empty($values['tag'])) {
      $_GET['tag'] = $values['tag'];
      $_GET['tag_id'] = $values['tag_id'];
    }

    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
      $tag = $_GET['tag'];
      $tag_id = $_GET['tag_id'];
      $page = 1;
      if (isset($_GET['page']) && !empty($_GET['page'])) {
        $page = $_GET['page'];
      }
      //unset($_GET);
      $_GET['tag'] = $tag;
      $_GET['tag_id'] = $tag_id;
      $_GET['page'] = $page;
    }

    //GET VALUE BY POST TO GET DESIRED LISTINGS
    if (!empty($_GET)) {
      $values = array_merge($values, $_GET);
    }
    
    //FORM GENERATION
    $form = new List_Form_Search(array('type' => 'list_listing'));

    if (!empty($_GET)) {
      $form->populate($_GET);
    }

    $values = array_merge($values, $form->getValues());

    if (($category) != null) {
      $this->view->category = $values['category'] = $category;
      $this->view->subcategory = $values['subcategory'] = $subcategory;
      $this->view->subsubcategory = $values['subsubcategory'] = $subsubcategory;
    } else {
      $values['category'] = 0;
      $values['subcategory'] = 0;
      $values['subsubcategory'] = 0;
    }

    if (($category_id) != null) {
      $this->view->category_id = $values['category'] = $category_id;
      $this->view->subcategory_id = $values['subcategory'] = $subcategory_id;
      $this->view->subsubcategory_id = $values['subsubcategory'] = $subsubcategory_id;
    } else {
      $values['category'] = 0;
      $values['subcategory'] = 0;
      $values['subsubcategory'] = 0;
    }

    if (isset($params['page']) && !empty($params['page']))
      $values['page'] = $params['page'];
    else
      $values['page'] = 1;

    $values['type'] = 'browse';

    //GET LISITNG FPR PUBLIC PAGE SET VALUE
    if (@$values['show'] == 2) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }

      $values['users'] = $ids;
    }

    $this->view->assign($values);

    //CORE API
    $this->view->settings = $settings = Engine_Api::_()->getApi('settings', 'core');

    //PAGE-RATING IS ENABLED OR NOT
    $this->view->ratngShow = (int) $settings->getSetting('list.rating', 1);

    //CUSTOM FIELD WORK
    $customFieldValues = array_intersect_key($values, $form->getFieldElements());
    if ($form->show->getValue() == 3 && !isset($_GET['show'])) {
      @$values['show'] = 3;
    }

    // GET LISTINGS
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('listings', 'list')->getListsPaginator($values, $customFieldValues);

    $items_count = (int) $settings->getSetting('list.page', 10);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($items_count);
    $values['page'] = $params['page'] = $request->getParam('page', null);
    $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
    $this->view->list_generic = Zend_Registry::get('list_generic');

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->list()->enableLocation();
    $this->view->flageSponsored = 0;
    $this->view->params = $params;

    if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {
      $ids = array();
      $sponsored = array();
      foreach ($paginator as $listing) {
        $id = $listing->getIdentity();
        $ids[] = $id;
        $listing_temp[$id] = $listing;
      }
      $values['listing_ids'] = $ids;
      $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'list')->getLocation($values);

      foreach ($locations as $location) {
        if ($listing_temp[$location->listing_id]->sponsored) {
          $this->view->flageSponsored = 1;
          break;
        }
      }
      $this->view->list = $listing_temp;
    } else {
      $this->view->enableLocation = 0;
    }
    if (empty($categoryname)) {
      $_GET['category'] = $this->view->category_id = 0;
      $_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
      $_GET['categoryname'] = 0;
      $_GET['subcategoryname'] = 0;
      $_GET['subsubcategoryname'] = 0;
    }

    //GET CATEGORY TABLE
    $this->view->categoryTable = $categoryTable = Engine_Api::_()->getDbtable('categories', 'list');

    $this->view->category_name = '';
    $this->view->subcategory_name = '';
    $this->view->subsubcategory_name = '';

    $this->view->category_id = $bread_crumb_category_id = !empty($this->view->category_id) ? $this->view->category_id : $this->view->category;
    if (!empty($bread_crumb_category_id)) {
      $this->view->category_name = $categoryTable->getCategory($bread_crumb_category_id)->category_name;
    }

    $this->view->subcategory_id = $bread_crumb_subcategory_id = !empty($this->view->subcategory_id) ? $this->view->subcategory_id : $this->view->subcategory;
    if (!empty($bread_crumb_subcategory_id)) {
      $this->view->subcategory_name = $categoryTable->getCategory($bread_crumb_subcategory_id)->category_name;
    }

    $this->view->subsubcategory_id = $bread_crumb_subsubcategory_id = !empty($this->view->subsubcategory_id) ? $this->view->subsubcategory_id : $this->view->subsubcategory;
    if (!empty($bread_crumb_subsubcategory_id)) {
      $this->view->subsubcategory_name = $categoryTable->getCategory($bread_crumb_subsubcategory_id)->category_name;
    }

    //SEND FORM VALUES TO TPL
    $this->view->formValues = $values;

    if (((isset($values['tag']) && !empty($values['tag']) && isset($values['tag_id']) && !empty($values['tag_id'])))) {
      $current_url = $request->getRequestUri();
      $current_url = explode("?", $current_url);
      if (isset($current_url[1])) {
        $current_url1 = explode("&", $current_url[1]);
        foreach ($current_url1 as $key => $value) {
          if (strstr($value, "tag=") || strstr($value, "tag_id=")) {
            unset($current_url1[$key]);
          }
        }
        $this->view->current_url2 = implode("&", $current_url1);
      }
    }

    $this->view->search = 0;
    if (!empty($_GET)) {
      $this->view->search = 1;
    }

    //CAN CREATE PAGES OR NOT
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('list_listing', $viewer, 'create');

    if (empty($list_browse)) {
      return $this->setNoRender();
    }
  }

}