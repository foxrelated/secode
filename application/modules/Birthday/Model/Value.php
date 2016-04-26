<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Value.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Birthday_Model_Value extends Core_Model_Item_Abstract {

  // Properties
  protected $_parent_type = 'value';
  protected $_searchColumns = array('item_id', 'field_id');
  protected $_parent_is_owner = true;

}
