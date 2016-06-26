<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heevent_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {

    if (!(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('apptouch') &&
        Engine_Api::_()->apptouch()->isApptouchMode()) &&
      (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('touch') &&
        Engine_Api::_()->touch()->isTouchMode() ||
        Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('mobile') &&
        Engine_Api::_()->mobile()->isMobileMode())
    ) {
      return false;
    }

    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();
    /**
     * @var $settings Core_Api_Settings
     */
    if ($module == 'event' && ($controller == 'index' || $controller == 'event' || $controller == 'profile')) {
      if(!Engine_Api::_()->hasModuleBootstrap('apptouch') || !Engine_Api::_()->apptouch()->isApptouchMode()){
        $request->setModuleName('heevent');
        return;
      }
    }
  }
}
?>