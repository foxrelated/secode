<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Mcard_Bootstrap extends Engine_Application_Bootstrap_Abstract 
{
	public function __construct($application)
  {
    parent::__construct($application);
		include APPLICATION_PATH . '/application/modules/Mcard/controllers/license/license.php';
  }

  protected function _initFrontController()
  {
    $this->initActionHelperPath();
    Zend_Controller_Action_HelperBroker::addHelper(new Mcard_Controller_Action_Helper_McardHelper());
  }
}