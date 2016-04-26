<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Writes.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_Hideprofilewidgets extends Engine_Db_Table {

  protected $_rowClass = "Sitegroup_Model_Hideprofilewidget";
  
  /**
   * Gets hide widgets information
   *
   * @param all widgets name which are hidden
   */
  public function hideWidgets() {
    return $this->fetchAll($this->select());
  }
  
}

?>