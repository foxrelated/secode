<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Loader.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Loader extends Engine_Loader {

  static $_hooked = false;
  var $_loader;

  public static function hook() {
    if (self::$_hooked) {
      return;
    }
    self::$_hooked = true;

    new self();
  }

  public function __construct() {
    $this->_loader = Engine_Loader::getInstance();
    Engine_Loader::setInstance($this);

    $this->_prefixToPaths = $this->_loader->_prefixToPaths;
    $this->_components = $this->_loader->_components;
  }

  public function load($class) {
    if ($class == 'Core_Api_Search') {
      $class = "Siteadvsearch_Api_Search";
    }

    if (Engine_Api::_()->hasModuleBootstrap('sitemailtemplates')) {
      if ($class == 'Core_Api_Mail') {
        $class = "Sitemailtemplates_Api_Mail";
      }
    }

    return parent::load($class);
  }

}