<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
	public function __construct($application)
  {
    parent::__construct($application);
  }

	protected function _initFrontController()
  {
		$this->initActionHelperPath();

		//INITIALIZE GROUPPOLLS HELPER
    Zend_Controller_Action_HelperBroker::addHelper(new Grouppoll_Controller_Action_Helper_Grouppolls());
  }
}
?>