<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Loader.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_Plugin_Loader extends Zend_Controller_Plugin_Abstract {

  public function preDispatch(Zend_Controller_Request_Abstract $request) {
    $loader = Engine_Loader::getInstance();
    if (get_class($loader) == 'Engine_Loader') {
      Sitemailtemplates_Loader::hook();
    } else if (get_class($loader) == 'Semods_Loader') {
      Sitemailtemplates_ConflictThirdPartySemodsLoader::hook();
    }
  }

}
