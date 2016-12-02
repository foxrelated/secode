<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  public function __construct($application) {
    parent::__construct($application);
    $this->initViewHelperPath();
    include_once APPLICATION_PATH . '/application/modules/Sitetagcheckin/controllers/license/license.php';
  }
  
  protected function _initFrontController() {
    $this->initActionHelperPath() ;
    // Initialize FriendPopups helper
    Zend_Controller_Action_HelperBroker::addHelper( new Sitetagcheckin_Controller_Action_Helper_Checkinhelpers()) ;
  }
  
//   public function _bootstrap() { 
//     $front = Zend_Controller_Front::getInstance();
//     $front->registerPlugin(new Sitetagcheckin_Plugin_Core);
//   }

}
