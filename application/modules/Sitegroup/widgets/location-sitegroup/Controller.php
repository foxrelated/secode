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
class Sitegroup_Widget_LocationSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //CHECK LOCATION MAP IS ENABLED OR NOT 
    $check_location = Engine_Api::_()->sitegroup()->enableLocation();
    if (!Engine_Api::_()->core()->hasSubject() || !$check_location) {
      return $this->setNoRender();
    }

    $this->view->multiple_location = $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.multiple.location', 0);

    $this->view->mobile = Engine_Api::_()->seaocore()->isMobile();
    //GET SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    
//     $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
//     if (empty($isManageAdmin)) {
//       return $this->setNoRender();
//     }

    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'map');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    $MainLocationId = 0;
    $this->view->MainLocationObject='';
    if (!empty($sitegroup->location)) {

      $MainLocationId = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocationId($sitegroup->group_id, $sitegroup->location);
      $this->view->MainLocationObject = Engine_Api::_()->getItem('sitegroup_location', $MainLocationId);
    }

    $value['id'] = $sitegroup->getIdentity();
    if (!empty($multipleLocation)) {
      $value['mapshow'] = 'Map Tab';
      $value['mainlocationId'] = $MainLocationId;
    }

    //DONT RENDER IF NO LOCATION
    $location = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocation($value);
    $count = 0;
    if ($multipleLocation) {
      $count = $location->getTotalItemCount();
    } else {
      $count = count($location);
    }

    if (empty($sitegroup->location) && empty($count)) {
      return $this->setNoRender();
    }

    //GET PRICE IS ENABLED OR NOT
    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1);

    //START MANAGE-ADMIN CHECK
    $this->view->isManageAdmin = $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');


    //END MANAGE-ADMIN CHECK
    $this->view->is_ajax = $this->_getParam('is_ajax', 0);
    $this->view->current_group = $group = $this->_getParam('group', 1);
    $this->view->current_totalgroups = $group * 10;

//     $value['id'] = $sitegroup->getIdentity();
//     
//     if (!empty($multipleLocation)) {
// 			$value['mapshow'] = 'Map Tab';
// 			$value['mainlocationId'] = $MainLocationId;
// 		}
    //DONT RENDER IF NO LOCATION
    $this->view->location = $location; // =  Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocation($value);

    if (!empty($multipleLocation)) {
      $location->setItemCountPerPage(10);
      $this->view->location = $location->setCurrentPageNumber($group);
// 			if ($location->getTotalItemCount() <= 0) {
// 				return $this->setNoRender();
// 			}
    } else {
      if (empty($location)) {
        return $this->setNoRender();
      }
    }

    //GROUP-RATING IS ENABLED OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');

    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
    $this->view->content_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.location-sitegroup', $sitegroup->group_id, $layout);
    $this->view->showtoptitle = Engine_Api::_()->sitegroup()->showtoptitle($layout, $sitegroup->group_id);
    $this->view->module_tabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $this->view->identity_temp = $this->view->identity;
  }

}