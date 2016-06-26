<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function onRenderLayoutDefault($event) {


        $view = $event->getPayload();
        $view->headTranslate(array("Forgot Password?", "Login with Twitter", "Login with Facebook", "Mark as Read", "Mark as Unread"));
        $view->headScript()
                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/scripts/core.js');
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {

      //IF SITEMENU IS ENABLED CHANGE THE MENU EDITOR PATH TO SITEMENU MENU EDITOR FOR MAIN MENU
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemenu')){
        if (substr($request->getPathInfo(), 1, 5) == "admin") {
            $module = $request->getModuleName();
            $controller = $request->getControllerName();
            $action = $request->getActionName();
            if ($module == 'core' && $controller == 'admin-menus' && $action == 'index') {
                $params = Zend_Controller_Front::getInstance()->getRequest()->getParam('name', 'core_main');
                if ($params == 'core_main') {
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $redirector->gotoRoute(array('module' => 'sitemenu', 'controller' => 'menu-settings', 'action' => 'editor'), 'admin_default', false);
                }
            }
        }
      }
    }

}