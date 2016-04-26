<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrp.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Userconnection_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
	public function __construct($application)
  {
    parent::__construct($application);
		include APPLICATION_PATH . '/application/modules/Userconnection/controllers/license/license.php';
  }

	protected function _initFrontController()
  {
		$this->initActionHelperPath();
		// Initialize FriendPopups helper
    Zend_Controller_Action_HelperBroker::addHelper(new Userconnection_Controller_Action_Helper_Checkfriend());
  }
}