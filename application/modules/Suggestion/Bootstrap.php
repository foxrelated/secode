<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Suggestion_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
	public function __construct($application)
  {
    parent::__construct($application);
    
		$this->initViewHelperPath();    

		include APPLICATION_PATH . '/application/modules/Suggestion/controllers/license/license.php';

   
  }
	
	protected function _initFrontController()
  {
		$this->initActionHelperPath();
    // Initialize FriendPopups helper
    Zend_Controller_Action_HelperBroker::addHelper(new Suggestion_Controller_Action_Helper_SuggestionPopups());  	
  }
}