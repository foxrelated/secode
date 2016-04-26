<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Bootstrap.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/

class Birthday_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);

  }

  protected function _initFrontController() {
    $this->initActionHelperPath();
    //Initialize Groupdocuments helper
    Zend_Controller_Action_HelperBroker::addHelper(new Birthday_Controller_Action_Helper_Birthday());
  }
}