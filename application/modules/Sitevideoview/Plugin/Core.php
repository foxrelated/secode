<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Sitevideoview
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: 2012-06-028 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideoview_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function routeShutdown(Zend_Controller_Request_Abstract $request) {
    $lightbox_type = $request->getParam('lightbox_type', null);
    if (!empty($lightbox_type) && $lightbox_type == 'sitevideoview') {
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $sitevideoMenuType = $settings->getSetting('sitevideoview.menu.type', null);
      if (!empty($sitevideoMenuType)) {
        $module_name = $request->getModuleName();
        $request->setModuleName('sitevideoview');
        $request->setControllerName('index');
        $actionName = 'index';
        if ($module_name === 'videofeed') {
          $actionName = 'videofeed-profile';
        }
        $request->setActionName($actionName);
        $request->setParam("module_name", $module_name);
      }
    }
  }

}

?>