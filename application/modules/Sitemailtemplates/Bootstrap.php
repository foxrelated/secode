<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  public function __construct($application) {
		parent::__construct($application);
    $this->initViewHelperPath();
    $frontController = Zend_Controller_Front::getInstance();    
    $frontController->registerPlugin(new Sitemailtemplates_Plugin_Core);
    $frontController->registerPlugin( new Sitemailtemplates_Plugin_Loader() );
  }
}