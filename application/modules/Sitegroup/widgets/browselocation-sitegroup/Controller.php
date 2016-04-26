<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:20:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_BrowselocationSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // Make form
    $this->view->form = $form = new Sitegroup_Form_Locationsearch(array('type' => 'sitegroup_group'));
    
		if(!empty($_POST)) {
			$this->view->is_ajax = $_POST['is_ajax'];
		}
		
	  if(empty($_POST['sitegroup_location'])) {
			$this->view->locationVariable = '1';
		}
		
    if (empty($_POST['is_ajax'])) {
			$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
			$form->isValid($p);
			$values = $form->getValues();
			$customFieldValues = array_intersect_key($values, $form->getFieldElements());
			$this->view->is_ajax = $this->_getParam( 'is_ajax', 0 );
    } else {
			$values = $_POST;
			$customFieldValues = array_intersect_key($values, $form->getFieldElements());
    }

    unset($values['or']);
    $this->view->assign($values);
    $viewer = Engine_Api::_()->user()->getViewer();
    if (@$values['show'] == 2) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }

      $values['users'] = $ids;
    }
    $values['type'] = 'browse';
    $values['type_location'] = 'browseLocation';
    if (isset($values['show'])) {
			if ($form->show->getValue() == 3) {
				@$values['show'] = 3;
			}
    }
    
    //$viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->current_group = $group = $this->_getParam( 'group' , 1 ) ;
    $this->view->current_totalgroups = $group * 15 ;
    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitegroup()->enableLocation();
    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1);

    //check for miles or street.
		if (isset($values['locationmiles']) && !empty($values['locationmiles'])) {
			if (isset($values['sitegroup_street']) && !empty($values['sitegroup_street'])) {
				$values['sitegroup_location'] = $values['sitegroup_street'] . ',';
				unset($values['sitegroup_street']);
			}
			
			if (isset($values['sitegroup_city']) && !empty($values['sitegroup_city'])) {
				$values['sitegroup_location'].= $values['sitegroup_city'] . ',';
				unset($values['sitegroup_city']);
			}	
			
			if (isset($values['sitegroup_state']) && !empty($values['sitegroup_state'])) {
				$values['sitegroup_location'].= $values['sitegroup_state'] . ',';
				unset($values['sitegroup_state']);
			} 
			
			if (isset($values['sitegroup_country']) && !empty($values['sitegroup_country'])) {
				$values['sitegroup_location'].= $values['sitegroup_country'];
				unset($values['sitegroup_country']);
			}
		}

    $result = Engine_Api::_()->sitegroup()->getSitegroupsSelect($values, $customFieldValues);
   	$this->view->paginator = $paginator = Zend_Paginator::factory($result); 
    $paginator->setItemCountPerPage(15);
    $this->view->paginator = $paginator->setCurrentPageNumber($group);
    $this->view->mobile = Engine_Api::_()->seaocore()->isMobile();
		if(!empty($_POST['is_ajax'])) {
			//For show location marker.
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
				$sitegroup_lat = array();
				$sitegroup_long = array();
				foreach ($locations as $location) {
					$sitegroup_lat[] = $location->latitude;
					$sitegroup_long[] = $location->longitude;
				}

				foreach ($locations as $location) {
					if ($sitegroup_temp[$location->group_id]->sponsored) {
						break;
					}
				}
				$this->view->sitegroup = $sitegroup_temp;
			}
    }
  }
}