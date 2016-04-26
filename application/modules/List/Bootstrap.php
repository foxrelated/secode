<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Bootstrap.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  protected function _initFrontController() {

    $this->initActionHelperPath();
    include APPLICATION_PATH . '/application/modules/List/controllers/license/license.php';

    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new List_Plugin_Core);
  }

}