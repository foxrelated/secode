<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Plugin_Core {

  public function onRenderLayoutMobileSMDefault($event) {
  
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if (Zend_Registry::isRegistered('StaticBaseUrl')) {
      $view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl')
              . 'application/modules/Sitelike/externals/scripts/sitemobile/core.js');
    } else {
      $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/scripts/sitemobile/core.js');
    }
  }

  public function onRenderLayoutDefault($event) {
  
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if (Zend_Registry::isRegistered('StaticBaseUrl')) {
      $view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl')
              . 'application/modules/Sitelike/externals/scripts/core.js');
    } else {
      $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/scripts/core.js');
    }
  }

  public function onCoreLikeDeleteBefore($event) {

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();

    if ($module == 'core' && $action == 'unlike' && $controller == 'comment') {
      $payload = $event->getPayload();
      $type = $payload->getType();
      $viewer = Engine_Api::_()->user()->getViewer();
      if ($type == 'core_like') {
        //START DELETE NOTIFICATION
        Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type = ?' => 'liked', 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'object_type = ?' => $payload->resource_type, 'object_id = ?' => $payload->resource_id));
        //END DELETE NOTIFICATION
      }
    }
  }

}