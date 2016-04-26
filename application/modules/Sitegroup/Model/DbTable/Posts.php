<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Posts.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_Posts extends Engine_Db_Table {

  protected $_rowClass = 'Sitegroup_Model_Post';

	/**
   * Gets all post for topic
   *
   * @param int $group_id
   * @param int $topic_id 
   * @return Zend_Db_Table_Select
   */		  
  public function getPost($group_id, $topic_id, $order) {
    $select = $this->select()
            ->where('group_id = ?', $group_id)
            ->where('topic_id = ?', $topic_id);
   if($order == 1) {
			$select->order('creation_date DESC');
   } else {
      $select->order('creation_date ASC');
   }
    return Zend_Paginator::factory($select);        
  }
  
}

?>