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
class Sitegroup_Widget_PinboardBrowseController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->params = $this->_getAllParams();
    $this->view->params['noOfTimes'] = 10000;
    if (!isset($this->view->params['itemWidth'])) {
      $this->view->params['itemWidth'] = 237;
    }
    $this->view->autoload = true;
    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      $this->view->autoload = false;
      if ($this->_getParam('contentpage', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      //  $this->view->layoutColumn = $this->_getParam('layoutColumn', 'middle');
      $this->getElement()->removeDecorator('Title');
      //return;
    }


  //  if (!$this->view->is_ajax_load) {
      $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
      $this->view->params = array_merge($this->view->params, $p);
  //  }
    // Make form
    $valueArray = array('street' => 1, 'city' => 1, 'country' => 1, 'state' => 1);
    $sitegroup_street = serialize($valueArray);

    // Make form
    $this->view->form = $form = new Sitegroup_Form_Locationsearch(array('value' => $sitegroup_street, 'type' => 'sitegroup_group'));
    $form->populate($this->view->params);
    $form->isValid($this->view->params);
    $values = $form->getValues();
    $customFieldValues = array_intersect_key($values, $form->getFieldElements());

    unset($values['or']);
    //$this->view->formValues = array_filter($values);
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
    $this->view->current_group = $group = $this->_getParam('contentpage', 1);
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

    $values['category_id'] = isset($values['category_id']) ? $values['category_id'] : $this->_getParam('category_id', 0);
    $result = Engine_Api::_()->sitegroup()->getSitegroupsSelect($values, $customFieldValues);
    $this->view->paginator = $paginator = Zend_Paginator::factory($result);
    $paginator->setItemCountPerPage($this->_getParam('itemCount', 12));
    $this->view->paginator = $paginator->setCurrentPageNumber($group);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $this->view->countGroup = $paginator->count();

    if ($group > $this->view->countGroup && $this->view->totalCount) {
      $this->view->noMoreContent = 1;
    }
    $this->view->currentgroup = $group;
    $this->view->membersEnabled = $membersEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');

    $this->view->postedby = $this->_getParam('postedby', 1);
    $this->view->showOptions = $this->_getParam('showoptions', array("likeCount"));
    $this->view->truncationDescription = $this->_getParam('truncationDescription', 100);
    $this->view->defaultlocationmiles = $this->_getParam('defaultlocationmiles', 1000);
	  $this->view->showfeaturedLable = $this->_getParam('showfeaturedLable', 1);
		$this->view->showsponsoredLable = $this->_getParam('showsponsoredLable', 1);
    $this->view->commentSection = $this->_getParam('commentSection', 1);
    if ($this->view->params['noOfTimes'] > $this->view->countGroup)
      $this->view->params['noOfTimes'] = $this->view->countGroup;

    $this->view->show_buttons = $this->_getParam('show_buttons', array("wishlist", "compare", "comment", "like", 'share', 'facebook', 'twitter', 'pinit'));

    if (isset($_GET['search'])) {
      $this->view->detactLocation = 0;
    } else {
      $this->view->detactLocation = $this->_getParam('detactLocation', 0);
    }
  }

}
