<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Role.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitegroupmember_Model_Role extends Core_Model_Item_Abstract {

	protected $_searchTriggers = false;
  /**
   * Gets Role Table
   *
   * @return table
   */	
  public function getTable() {

    if (is_null($this->_table)) {
      $this->_table = Engine_Api::_()->getDbtable('roles', 'sitegroupmember');
    }

    return $this->_table;
  }
}