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
class List_Widget_LocationListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET LOCATION SETTING
    $check_location = Engine_Api::_()->list()->enableLocation();

		//DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject('list_listing') || !$check_location) {  
			return $this->setNoRender(); 
		}

    //GET SUBJECT
    $this->view->list = $list = Engine_Api::_()->core()->getSubject('list_listing');

		//GET LOCATION
    $value['id'] = $list->getIdentity();
    $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'list')->getLocation($value);

		//DONT RENDER IF LOCAITON IS EMPTY
    if (empty($location)) {  
			return $this->setNoRender(); 
		}

    //RATING IS ALLOWED BY ADMIN OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.rating', 1);
  }
}