<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitetagcheckin_Widget_UserlocationSearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		$valueArray = array('advancedsearchLink' => $this->_getParam('advancedsearchLink', 1), 'street' => $this->_getParam('street', 1), 'city' => $this->_getParam('city', 1), 'country' => $this->_getParam('country', 1),'state' => $this->_getParam('state', 1),'has_photo' => $this->_getParam('has_photo', 1));
		$locarionArray = serialize($valueArray);

    $showTabArray = $this->_getParam('form_options', array('advancedsearchLink' => "advancedsearchLink", "street" => "street", "city" => "city", "state" => "state", "country" => "country", "hasphoto" => "hasphoto", "isonline" => "isonline"));
    
    $this->view->defaultView = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.layouts.oder');
    
    $this->view->mapshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.mapshow', 1);

    // Make form
    $this->view->form = $form = new Sitetagcheckin_Form_UserLocationsearch(array('value' => $locarionArray, 'formoptions' => $showTabArray));

    if (!empty($_POST)) { 
			$this->view->advanced_search =  $_POST['advanced_search'];
    }

// 		if (!empty($_POST)) {
// 			$form->populate($_POST);
//     }

    // Process form
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $form->isValid($p);
    $values = $form->getValues();

    unset($values['or']);
    $this->view->formValues = array_filter($values);
    $this->view->assign($values);
  }
}