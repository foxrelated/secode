<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Widget_LocationSitetagcheckinController extends Engine_Content_Widget_Abstract {

  //ACTION FOR ADDING THE LOCATION ON THE CONTENTS
  public function indexAction() {

    //GET SUGGEST
    $this->view->showSuggest = $showSuggest = $this->_getParam('showSuggest', 0);
   
    $sitetagcheckin_location = Zend_Registry::isRegistered('sitetagcheckin_location') ? Zend_Registry::get('sitetagcheckin_location') : null;
    if (empty($sitetagcheckin_location)) {
      return $this->setNoRender();
    }

    $allwaysSubject="";
    if(Engine_Api::_()->core()->hasSubject()) {
      $this->view->subject = $allwaysSubject = $subject = Engine_Api::_()->core()->getSubject();
    }

		$moduleCore = Engine_Api::_()->getDbtable('modules', 'core');
    $getEnableModuleAdvalbum = $moduleCore->isModuleEnabled('advalbum');

		if ($showSuggest) {
      if($getEnableModuleAdvalbum) {
        $this->view->subject = $subject = Engine_Api::_()->getItem('advalbum_album', $this->_getParam('album_suggest_id', 0));
      } else {
        $this->view->subject = $subject = Engine_Api::_()->getItem('album', $this->_getParam('album_suggest_id', 0));
      }
    } else if (Engine_Api::_()->core()->hasSubject()) {
      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    } else {
      return $this->setNoRender();
    }

    $front = Zend_Controller_Front::getInstance();

    //GET MODULE NAME
    $module = $front->getRequest()->getModuleName();

    //GET ACTION NAME
    $action = $front->getRequest()->getActionName();

    //GET CONTROLLER NAME
    $controller_name = $front->getRequest()->getControllerName();

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET VIEWER INFORMATION
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    if(!$showSuggest) {
			//GET RESOURCE TYPE   
			$this->view->resource_type = $resource_type = $subject->getType();

			//GET RESOURCE ID
			$this->view->resource_id = $resource_id = $subject->getIdentity();
			//LINK SHOULB BE DSPLAY OR NOT
			$this->view->linkDisplay = 0;

			//SET LINK DISPLAY OR NOT
			$this->view->linkDisplay = $subject->getOwner()->isSelf($viewer);
    } else {
			//GET RESOURCE TYPE   
			$this->view->resource_type = $resource_type = $subject->getType();

			//GET RESOURCE ID
			$this->view->resource_id = $resource_id = $subject->getIdentity();
			//LINK SHOULB BE DSPLAY OR NOT
			$this->view->linkDisplay = 0;

			//SET LINK DISPLAY OR NOT
			$this->view->linkDisplay = $subject->getOwner()->isSelf($viewer);
    }

    //GET RESOURCE ID
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $this->view->content_page_id = $request->getParam('page_id', 0);
    $this->view->content_business_id = $request->getParam('business_id', 0);
    $this->view->content_group_id = $request->getParam('group_id', 0);
    $this->view->content_store_id = $request->getParam('store_id', 0);
		$this->view->content_event_id = $request->getParam('event_id', 0);
    //GET ADDLOCATION TABLE
    $addLocationTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

    //PARAMS
    $this->view->params = $params = "";

    //GET ROWS
    $row = $addLocationTable->getCheckinParams($resource_type, $resource_id);

    //GET PARAMS
    if (!empty($row))
      $this->view->params = $params = $row->params;

    //INITILISING DISPALY LOCATION 
    $this->view->displayLocation = "";

    //INITILISING GET LOCATION
    $this->view->getLocation = "";

    //CHECK PARAMS
    if (!empty($params)) {

      //CHECK CHECKIN PARAMS EXIST OR NOT
      if (is_array($params) && isset($params['checkin'])) {
        //GET CHECKIN
        $this->view->checkin = $checkin = $params['checkin'];

        //GET PREFIX
        if ($checkin['label'] != false) {
          $this->view->addprefix = $addPrifix = (!empty($checkin['prefixadd'])) ? $this->view->translate($checkin['prefixadd']) : $this->view->translate('at');
        } else {
          $this->view->addprefix = $addPrifix = "";
        }
        $checkinTypeArray = array('Page', 'Business');
        $customcheckinTypeArray = array('Classified', 'Listing', 'Recipe', 'Event', 'Sitereview');
        if (isset($checkin['type']) && ($checkin['type'] == 'place' || in_array($checkin['type'], $customcheckinTypeArray))) {
          $this->view->action_id = $action_id = $addLocationTable->getCheckinParams($resource_type, $resource_id)->action_id;
          if ($action_id < 0) {
            $this->view->action_id = abs($action_id);
          }
          $this->view->displayLocation = 1;
					if(isset($checkin['vicinity']) && $checkin['vicinity']) {
						if(isset($checkin['name']) && $checkin['name'] && $checkin['name'] != $checkin['vicinity']) {
							$checkin['label'] = $checkin['name'] . ', ' . $checkin['vicinity'];
						} else {
							$checkin['label'] = $checkin['vicinity'];
						}
					}
          $this->view->getLocation = $checkin['label'];
        } elseif (isset($checkin['type']) && $checkin['type'] == 'just_use') {
          $this->view->getLocation = $this->view->displayLocation = $checkin['label'];
        } else if (isset($checkin['type']) && in_array($checkin['type'], $checkinTypeArray)) {
          $item = Engine_Api::_()->getItemByGuid($checkin['resource_guid']);
          if ($item) {
            $this->view->displayLocation = $addPrifix . " " . $this->view->htmlLink($item->getHref(), $item->getTitle(), array('title' => $item->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => $item->getType() . ' ' . $item->getIdentity()));
            $this->view->getLocation = $item->getTitle();
          }
        }
      }
    }

    //INITILISING LOCATION DIV
    $this->view->locationDiv = '';

    //MAKE SWITCH CASE GETTING THE CLASSNAME FORM WHICH WE ARE MAKING THE DIV
    switch ($module) {
      case 'album':
        if ($action == 'view' && $controller_name == 'album') {
					if($showSuggest == 1) {
            $this->view->locationDiv = 'seaotagalbumsuggestlocation';
          } else {
            $this->view->locationDiv = 'album_options';
          }
        } else {
          if($showSuggest == 1) {
            $this->view->locationDiv = 'seaotagalbumsuggestlocation';
          } else {
            $this->view->locationDiv = 'albums_viewmedia_info_footer';
          }
        }
        break;
      case 'advalbum':
        if ($action == 'browse' && $controller_name == 'index') {
					if($showSuggest == 1) {
            $this->view->locationDiv = 'seaotagalbumsuggestlocation';
          } else {
            $this->view->locationDiv = 'album_options';
          }
        } else {
          if($showSuggest == 1) {
            $this->view->locationDiv = 'seaotagalbumsuggestlocation';
          } else {
            $this->view->locationDiv = 'albums_viewmedia_info_footer';
          }
        }
        break;
      case 'sitealbum' :
        if (($action == 'view' && $controller_name == 'album')) {
          if($showSuggest == 1) {
            $this->view->locationDiv = 'seaotagalbumsuggestlocation';
          } else {
            $this->view->locationDiv = 'seaotagalbumcheckinshowlocation';
          }
        } else if ($controller_name == 'photo' && ($action == 'view' || $action == 'ajax-photo-view' | $action == 'light-box-view')) {

          if($showSuggest == 1) {
            $this->view->locationDiv = 'seaotagalbumsuggestlocation';
          } else {
            $this->view->locationDiv = 'seaotagcheckinshowlocation';
          }
        }
				else if ($controller_name == 'index' && ($action == 'index')) {
          if($showSuggest == 1) {
            $this->view->locationDiv = 'seaotagalbumsuggestlocation';
          } else {
            $this->view->locationDiv = 'seaotagcheckinshowlocation';
          }
        }
        break;
      case 'seaocore':
      case 'sitepage':
        if (($action == 'view' && $controller_name == 'album')) {
          $this->view->locationDiv = 'seaotagsitepagealbumcheckinshowlocation';
        } else if (($action == 'view' && $controller_name == 'photo')) {
          $this->view->locationDiv = 'seaotagcheckinshowlocation';
        }
        break;
      case 'sitebusiness':
        if (($action == 'view' && $controller_name == 'album')) {
          $this->view->locationDiv = 'seaotagsitebusinessalbumcheckinshowlocation';
        } else if (($action == 'view' && $controller_name == 'photo')) {
          $this->view->locationDiv = 'seaotagcheckinshowlocation';
        }
        break;
      case 'sitegroup':
        if (($action == 'view' && $controller_name == 'album')) {
          $this->view->locationDiv = 'seaotagsitegroupalbumcheckinshowlocation';
        } else if (($action == 'view' && $controller_name == 'photo')) {
          $this->view->locationDiv = 'seaotagcheckinshowlocation';
        }
        break;
      case 'sitestore':
        if (($action == 'view' && $controller_name == 'album')) {
          $this->view->locationDiv = 'seaotagsitestorealbumcheckinshowlocation';
        } else if (($action == 'view' && $controller_name == 'photo')) {
          $this->view->locationDiv = 'seaotagcheckinshowlocation';
        }
        break;
      case 'siteevent':
        if (($action == 'view' && $controller_name == 'album')) {
          $this->view->locationDiv = 'seaotagsiteeventalbumcheckinshowlocation';
        } else if (($action == 'view' && $controller_name == 'photo')) {
          $this->view->locationDiv = 'seaotagcheckinshowlocation';
        }
        break;
      case 'sitebusinessnote':
      case 'sitepagenote':
      case 'sitegroupnote':
      case 'list':
      case 'recipe':  
      case 'sitereview':
        if (($action == 'view' && $controller_name == 'photo')) {
          $this->view->locationDiv = 'seaotagcheckinshowlocation';
        }
        break;
      default :
        $this->view->locationDiv = 'sitetagcheckin_autosuggest_location';
        break;
    }

  }

}

?>
