<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  protected function _initFrontController() {
    include APPLICATION_PATH . '/application/modules/Siteadvsearch/controllers/license/license.php';
    Zend_Controller_Action_HelperBroker::addHelper(new Siteadvsearch_Controller_Action_Helper_Searchentry());
    $frontController = Zend_Controller_Front::getInstance();
    $frontController->registerPlugin(new Siteadvsearch_Plugin_Loader());
  }

}