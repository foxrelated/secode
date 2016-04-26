<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_GroupsSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    if (isset($params['is_ajax_load']))
      unset($params['is_ajax_load']);

    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if ($this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if (!$this->_getParam('detactLocation', 0)) {
        $this->view->is_ajax_load = true;
      } else {
        $this->getElement()->removeDecorator('Title');
        $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
      }
    }

    if (empty($this->view->is_ajax_load)) {
      $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
      if (isset($cookieLocation['location']) && !empty($cookieLocation['location'])) {
        $this->view->is_ajax_load = 1;
      }
    }   
 

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->sitegroup_browse = $sitegroup_browse = Zend_Registry::isRegistered('sitegroup_browse') ? Zend_Registry::get('sitegroup_browse') : null;
    $ShowViewArray = $params['layouts_views'] = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));
    $this->view->is_ajax = $isAjax = $this->_getParam('isajax', 0);

    //START VIEW MORE LINK AND AUTOSCROLL CONTENT WORK
    if(!empty($isAjax)) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    //END VIEW MORE LINK AND AUTOSCROLL CONTENT WORK

    $defaultOrder = $params['layouts_oder'] = $this->_getParam('layouts_oder', 1);
    $this->view->columnWidth = $params['columnWidth'] = $this->_getParam('columnWidth', 188);
    $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', 350);
    $this->view->showlikebutton = $params['showlikebutton'] = $this->_getParam('showlikebutton', 1);
    $this->view->showfeaturedLable = $params['showfeaturedLable'] = $this->_getParam('showfeaturedLable', 1);
    $this->view->showsponsoredLable = $params['showsponsoredLable'] = $this->_getParam('showsponsoredLable', 1);
    $this->view->showlocation = $params['showlocation'] = $this->_getParam('showlocation', 1);
    $this->view->showprice = $params['showprice'] = $this->_getParam('showprice', 1);
    $this->view->showpostedBy = $params['showpostedBy'] = $this->_getParam('showpostedBy', 1);
    $this->view->showdate = $params['showdate'] = $this->_getParam('showdate', 1);
    $this->view->turncation = $params['turncation'] = $this->_getParam('turncation', 15);
    $this->view->showContactDetails = $params['showContactDetails'] = $this->_getParam('showContactDetails', 1);
    $this->view->showgetdirection = $params['showgetdirection'] = $this->_getParam('showgetdirection', 1);
    
    $this->view->showProfileField = $params['showProfileField'] = $this->_getParam('showProfileField', 0);
    $this->view->customFieldCount = $params['customFieldCount'] = $this->_getParam('customFieldCount', 2);
    $this->view->custom_field_title = $params['custom_field_title'] = $this->_getParam('custom_field_title', 0);
    $this->view->custom_field_heading = $params['custom_field_heading'] = $this->_getParam('custom_field_heading', 0);
    $this->view->showContent = $this->_getParam('show_content', 2);
    
    $statisticsElement = array("likeCount" , "followCount", "viewCount" , "commentCount");
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
			$statisticsElement['']="reviewCount";
		}
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
			$statisticsElement['']="memberCount";
			//$this->view->membercalled = $params['membercalled'] = $this->_getParam('membercalled', 1);
		}
    $this->view->statistics = $params['statistics'] = $this->_getParam('statistics', $statisticsElement);

    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1);
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
    $select_category = $params['category_id'] = $this->_getParam('category_id',0);
    if(!empty($select_category) && empty($_GET['category'])) {
      $category = $select_category;
      $category_id = $select_category;
    }
    else {
			$category = $params['category_id'] = $request->getParam('category_id', null);
      $category_id = $params['category'] = $request->getParam('category', null);
    }
    $subcategory = $params['subcategory_id'] = $request->getParam('subcategory_id', null);
    $subcategory_id = $params['subcategory'] = $request->getParam('subcategory', null);
    $categoryname = $params['categoryname'] = $request->getParam('categoryname', null);
    $subcategoryname = $params['subcategoryname'] = $request->getParam('subcategoryname', null);
    $subsubcategory = $params['subsubcategory_id'] = $request->getParam('subsubcategory_id', null);
    $subsubcategory_id = $params['subsubcategory'] = $request->getParam('subsubcategory', null);
    $subsubcategoryname = $params['subsubcategoryname'] = $request->getParam('subsubcategoryname', null);

    if ($category)
      $this->view->category = $_GET['category'] = $category;
    if ($subcategory)
      $this->view->subcategory = $_GET['subcategory'] = $subcategory;
    if ($categoryname)
      $this->view->categoryname = $_GET['categoryname'] = $categoryname;
    if ($subcategoryname)
      $this->view->subcategoryname = $_GET['subcategoryname'] = $subcategoryname;

    if ($subsubcategory)
       $this->view->subsubcategory = $_GET['subsubcategory'] = $subsubcategory;
    if ($subcategoryname)
     $this->view->subsubcategoryname =  $_GET['subsubcategoryname'] = $subsubcategoryname;

    if ($category_id)
      $this->view->category =  $_GET['category'] = $values['category'] = $category_id;
    if ($subcategory_id)
      $this->view->subcategory = $_GET['subcategory'] = $values['subcategory'] = $subcategory_id;
    if ($subsubcategory_id)
     $this->view->subsubcategory = $_GET['subsubcategory'] = $values['subsubcategory'] = $subsubcategory_id;
    $this->view->tag = $values['tag'] = $params['tag'] = $request->getParam('tag', null);
    if (!empty($values['tag']))
     $this->view->tag = $_GET['tag'] = $values['tag'];
    
    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
      $this->view->tag =$tag = $_GET['tag'];
      $group = 1;
      if (isset($_GET['page']) && !empty($_GET['page'])) {
        $group = $_GET['page'];
      }
      unset($_GET);
      $this->view->tag = $_GET['tag'] = $tag;
      $_GET['page'] = $group;
    }

		$this->view->sitegroup_location = $values['sitegroup_location'] = $params['sitegroup_location'] = $request->getParam('sitegroup_location', null);
    if (!empty($values['sitegroup_location']))
     $this->view->sitegroup_location = $_GET['sitegroup_location'] = $values['sitegroup_location'];
    
    if (isset($_GET['sitegroup_location']) && !empty($_GET['sitegroup_location'])) {
      $this->view->sitegroup_location =$sitegroup_location = $_GET['sitegroup_location'];
      $this->view->sitegroup_location = $_GET['sitegroup_location'] = $sitegroup_location;
    }

    //GET VALUE BY POST TO GET DESIRED SITEGROUPS
    if (!empty($_GET)) {
      $values = $_GET;
    }

    $this->view->params = $params;
    
    //FORM GENERATION
    $form = new Sitegroup_Form_Search(array('type' => 'sitegroup_group'));
    //$form = Zend_Registry::isRegistered('Sitegroup_Form_Search') ? Zend_Registry::get('Sitegroup_Form_Search') : new Sitegroup_Form_Search(array('type' => 'sitegroup_group')); 

    if (!empty($_GET))
      $form->populate($_GET);
    $values = $form->getValues();

		//GROUP OFFER IS INSTALLED OR NOT
		$this->view->sitegroupOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer');

    //BADGE CODE
    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
      if (isset($_POST['badge_id']) && !empty($_POST['badge_id'])) {
        $values['badge_id'] = $_POST['badge_id'];
      }
    }

    if (isset($params['page']) && !empty($params['page']))
      $values['page'] = $params['page'];
    else
      $values['page'] = 1;
    
    //GET LISITNG FPR PUBLIC GROUP SET VALUE
    $values['type'] = 'browse';
    $values['type_location'] = 'browseGroup';

    if (@$values['show'] == 2) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }

      $values['users'] = $ids;
    }
    
    if ($request->getParam('titleAjax')) { 
      $values['search'] = $request->getParam('titleAjax');
    }      

    //GEO-LOCATION WORK
    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupgeolocation') && isset($values['has_currentlocation']) && !empty($values['has_currentlocation'])) {

      $session = new Zend_Session_Namespace('Current_location');
      if (!isset($session->latitude) || !isset($session->longitude)) {
        $locationResult = null;
        $apiType = Engine_Api::_()->getApi('core', 'sitegroupgeolocation')->getGeoApiType();
        if ($apiType == 1) {
          $locationResult = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getMaxmindCurrentLocation();
        } elseif ($apiType == 2) {
          $locationResult = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getMaxmindGeoLiteCountry();
        }
        if (($apiType == 1 || $apiType == 2) && !empty($locationResult)) {
          $this->view->latitude = $values['latitude'] = $session->latitude = $locationResult['latitude'];
          $this->view->longitude = $values['longitude'] = $session->longitude = $locationResult['longitude'];
        }
      } else {
        $this->view->latitude = $values['latitude'] = $session->latitude;
        $this->view->longitude = $values['longitude'] = $session->longitude;
      }
    }
    $this->view->assign($values);

    //GROUP-RATING IS ENABLED OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');

    //CUSTOM FIELD WORK
    $customFieldValues = array_intersect_key($values, $form->getFieldElements());
    $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitegroup', 'show');
    if ($viewer->getIdentity() && !empty($row) && !empty($row->display) && $form->show->getValue() == 3 && !isset($_GET['show'])) {
      @$values['show'] = 3;
    }

		//DON'T SEND CUSTOM FIELDS ARRAY IF EMPTY
		$has_value = 0;
		foreach($customFieldValues as $customFieldValue) {
			if(!empty($customFieldValue)) {
				$has_value = 1;
				break;
			}
		}

		if(empty($has_value)) {
			$customFieldValues = null;
		}

		$values['browse_group'] = 1;    

    
    
    $this->view->detactLocation = $values['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->defaultLocationDistance = $values['defaultLocationDistance'] = $values['locationmiles'] = $this->_getParam('defaultLocationDistance', 1000);
      $values['latitude'] = $this->_getParam('latitude', 0);
      $values['longitude'] = $this->_getParam('longitude', 0);
    }

        if(isset($_GET['locationmiles'])) {
         $values['locationmiles'] =  $_GET['locationmiles'];
    } elseif(Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000)) {
        $values['locationmiles'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); 
    }
    
    if (!$this->view->detactLocation && empty($_GET['sitegroup_location']) && empty($_GET['locationSearch'])) {
      unset($values['sitegroup_location']);
      if (empty($_GET['latitude']) && isset($values['latitude'])) {
       unset($values['latitude']);
      }

      if (empty($_GET['longitude']) && isset($values['longitude'])) {
        unset($values['longitude']);
      }

      if (empty($_GET['Latitude']) && isset($values['Latitude'])) {
        unset($values['Latitude']);
      }

      if (empty($_GET['Longitude']) && isset($values['Longitude'])) {
        unset($values['Longitude']);
      }
    }  
  
    
    // GET SITEGROUPS
    $this->view->paginator = $paginator = Engine_Api::_()->sitegroup()->getSitegroupsPaginator($values, $customFieldValues);

    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.group', 10);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($items_count);
    $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
    $this->view->sitegroup_generic = Zend_Registry::isRegistered('sitegroup_generic') ? Zend_Registry::get('sitegroup_generic') : null;

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitegroup()->enableLocation();
    $this->view->flageSponsored = 0;

    if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {
      $ids = array();
      $sponsored = array();
      foreach ($paginator as $sitegroup) {
        $id = $sitegroup->getIdentity();
        $ids[] = $id;
        $sitegroup_temp[$id] = $sitegroup;
      }
      $values['group_ids'] = $ids;
      $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocation($values);

      foreach ($locations as $location) {
        if ($sitegroup_temp[$location->group_id]->sponsored) {
          $this->view->flageSponsored = 1;
          break;
        }
      }
      $this->view->sitegroup = $sitegroup_temp;
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

    $this->view->search = 0;
    if (!empty($_GET)) {
      $this->view->search = 1;
    }
    
    $this->view->paramsLocation = array_merge($_GET, $this->_getAllParams());
    $this->view->paramsLocation = array_merge($request->getParams(), $this->view->paramsLocation);
 
    if (isset($_GET['search']) || isset($_POST['search'])) {
      $this->view->detactLocation = 0;
    } else {
      $this->view->detactLocation = $this->_getParam('detactLocation', 0);
    }
    
    
    //CAN CREATE GROUPS OR NOT
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, 'create');
    $this->view->formValues = $values;
    if (empty($sitegroup_browse)) {
      return $this->setNoRender();
    }
  }

}

?>
