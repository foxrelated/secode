<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    List
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Metas.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class List_Model_DbTable_Metas extends Engine_Db_Table {

  protected $_name = 'list_listing_fields_meta';
  protected $_rowClass = 'List_Model_Meta';

  /**
   * Get Default Profile Id
   *
   */
  public function defaultProfileId() {

    //GET DEFAULT PROFILE ID
    $defaultProfileId = $this->select()
            ->from($this->info('name'), array('field_id'))
            ->where('type = ?', 'profile_type')
            ->where('alias = ?', 'profile_type')
            ->query()
            ->fetchColumn();

    //RETURN DEFAULT PROFILE ID
    return $defaultProfileId;
  }

  public function getDefaultProfileType() {

    $field_id = $this->select()
            ->from('engine4_list_listing_fields_meta', array('field_id'))
            ->where('type = ?', 'profile_type')
            ->where('alias = ?', 'profile_type')
            ->limit(1)
            ->query()
            ->fetchColumn();
    
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    return $option_id = $db->select()
            ->from('engine4_list_listing_fields_options', array('option_id'))
            ->where('field_id = ?', $field_id)
            ->limit(1)
            ->query()
            ->fetchColumn();
  }

}