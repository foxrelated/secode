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
class Siteadvsearch_Plugin_Loader extends Zend_Controller_Plugin_Abstract {

  public function preDispatch(Zend_Controller_Request_Abstract $request) {
    $loader = Engine_Loader::getInstance();
    if (get_class($loader) == 'Engine_Loader') {
      Siteadvsearch_Loader::hook();
    } else if (get_class($loader) == 'Semods_Loader') {
      Siteadvsearch_ConflictThirdPartySemodsLoader::hook();
    }
  }

}
