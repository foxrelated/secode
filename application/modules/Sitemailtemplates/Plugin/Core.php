<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function routeShutdown(Zend_Controller_Request_Abstract $request) {
    
    $module = $request->getModuleName();
		$controller = $request->getControllerName();
		$action = $request->getActionName();  
		if ($module == "core" && $controller == "admin-mail" && $action == "templates") {
			$request->setModuleName('sitemailtemplates');      
		}
  }

}