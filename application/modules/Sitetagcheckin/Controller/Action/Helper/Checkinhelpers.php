<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Checkinhelpers.php 6590 2010-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Controller_Action_Helper_Checkinhelpers extends Zend_Controller_Action_Helper_Abstract {

  function postDispatch() {

    //GET NAME OF MODULE, CONTROLLER AND ACTION
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    $view = $this->getActionController()->view;

    $groupSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.groupsettings');
    if (!empty($groupSettings)) {

      //ADD DOCUMENT PRIVACY FIELDS AT GROUP CREATION AND EDITION
      if (($module == 'group' || $module == 'advgroup') && ($action == 'create' || $action == 'edit') && ($controller == 'index' || $controller == 'group')) {

        $new_element = $view->form;
        if (!$new_element)
          return;

        //COUNT TOTAL ELEMENTS IN GROUP FORM
        $total_elements = Count($new_element->getElements());

        // LOCATION
        $new_element->addElement('Text', 'location', array(
            'label' => 'Location',
            'description' => 'Eg: Fairview Park, Berkeley, CA',
            'order' => $total_elements - 6,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));

        $new_element->location->getDecorator('Description')->setOption('placement', 'append');

        if ($action == 'edit') {
          $group_id = $front->getRequest()->getParam('group_id');
          $group = Engine_Api::_()->getItem('group', $group_id);
          if (!empty($group['location'])) {
            $new_element->location->setValue($group['location']);
          }
        }
      }
    }

//     $groupSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.groupsettings');
//     if (1) {
    //ADD DOCUMENT PRIVACY FIELDS AT GROUP CREATION AND EDITION
    if (($module == 'video' || $module == 'advvideo') && ($action == 'create' || $action == 'edit') && ($controller == 'index' || $controller == 'video')) {

      $new_element = $view->form;
      if (!$new_element)
        return;

      //COUNT TOTAL ELEMENTS IN GROUP FORM
      $total_elements = Count($new_element->getElements());

      // LOCATION
      $new_element->addElement('Text', 'location', array(
          'label' => 'Location',
          'description' => 'Eg: Fairview Park, Berkeley, CA',
          'order' => $total_elements - 7,
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          )
      ));
      $new_element->location->getDecorator('Description')->setOption('placement', 'append');

      if ($action == 'edit') {
        $video_id = $front->getRequest()->getParam('video_id');
        $video = Engine_Api::_()->getItem('video', $video_id);
        if (!empty($video['location'])) {
          $new_element->location->setValue($video['location']);
        }
      }
    }
    //}
    
//     $groupSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.groupsettings');
//     if (1) {
    //ADD DOCUMENT PRIVACY FIELDS AT GROUP CREATION AND EDITION
    
//    if (($module == 'album') && ($action == 'upload' || $action == 'edit') && ($controller == 'index' || $controller == 'album')) {
//
//      $new_element = $view->form;
//      if (!$new_element)
//        return;
//
//      //COUNT TOTAL ELEMENTS IN GROUP FORM
//      $total_elements = Count($new_element->getElements());
//
//      // LOCATION
//      $new_element->addElement('Text', 'location', array(
//          'label' => 'Location',
//          'description' => 'Eg: Fairview Park, Berkeley, CA',
//          'order' => $total_elements - 6,
//          'filters' => array(
//              'StripTags',
//              new Engine_Filter_Censor(),
//          )
//      ));
//      $new_element->location->getDecorator('Description')->setOption('placement', 'append');
//
//      if ($action == 'edit') {
//        $album_id = $front->getRequest()->getParam('album_id');
//        $album = Engine_Api::_()->getItem('album', $album_id);
//        if (!empty($album['location'])) {
//          $new_element->location->setValue($album['location']);
//        }
//      }
//    }
    //}
  }

}
