<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Options.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_Options extends Engine_Db_Table {

  protected $_name = 'sitegroup_group_fields_options';
  protected $_rowClass = 'Sitegroup_Model_Option';

  public function getAllProfileTypes() {
    $select = $this->select()
            ->where('field_id = ?', 1);
    $result = $this->fetchAll($select);
    return $result;
  }

}

?>