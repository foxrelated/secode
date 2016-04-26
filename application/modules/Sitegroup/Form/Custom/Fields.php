<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Fields.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Custom_Fields extends Fields_Form_Standard {

  public $_error = array();
  protected $_name = 'fields';
  protected $_elementsBelongTo = 'fields';

  public function init() {
    global $sitegroup_custom_field;
    if (!empty($sitegroup_custom_field)) {
      // custom sitegroup fields
      if (!$this->_item) {
        $sitegroup_item = new Sitegroup_Model_Group(array());
        $this->setItem($sitegroup_item);
      }
      parent::init();

      $this->removeElement('submit');
    } else {
      exit();
    }
  }

  public function loadDefaultDecorators() {
    if ($this->loadDefaultDecoratorsIsDisabled()) {
      return;
    }

    $decorators = $this->getDecorators();
    if (empty($decorators)) {
      $this->addDecorator('FormElements');
    }
  }

}

?>