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
/* @
 * Our Email Templates Plugin is conflicting with the SEMods Loader class 
 * for that we have extended their class for resolving the conflict. We are using the object of this class 
 * in /application/modules/Sitemailtemplates/Plugin/Loader.php 
 * 
 */
class Sitemailtemplates_ConflictThirdPartySemodsLoader extends Semods_Loader {

  static $_hooked = false;
  var $_trampolines = array('Core_Api_Mail' => 'Sitemailtemplates_Api_Mail');
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

    if ($class == 'Core_Api_Mail') {
      $class = "Sitemailtemplates_Api_Mail";
    }

    if (Engine_Api::_()->hasModuleBootstrap('siteadvsearch')) {
      if ($class == 'Core_Api_Search') {
        $class = "Siteadvsearch_Api_Search";
      }
    }

    return parent::load($class);
  }

  public function addTrampoline($origin, $trampoline) {
    $this->_trampolines[$origin] = $trampoline;
  }

}