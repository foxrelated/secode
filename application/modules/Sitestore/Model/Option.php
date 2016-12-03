<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Option.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_Option extends Core_Model_Item_Abstract {

  // Properties
  protected $_parent_type = 'option';
  protected $_searchColumns = array('option_id');
  protected $_parent_is_owner = true;

}
