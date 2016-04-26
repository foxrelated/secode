<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Profilemaps.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_DbTable_Profilemaps extends Engine_Db_Table {

  protected $_rowClass = "List_Model_Profilemap";

	/**
   * Get Mapping array
   *
   */
	public function getMapping() {

		//MAKE QUERY
    $select = $this->select()
                    ->from($this->info('name'), array('category_id','profile_type'))
                    ->where('category_id != ?', 0);
	
		//FETCH DATA
		$mapping = $this->fetchAll($select);

		//RETURN DATA
		if(!empty($mapping)) {
			return $mapping->toArray();
		}

    return null;
	}

	/**
   * Get profile_type corresponding to category_id
   *
   * @param int category_id
   */
	public function getProfileType($category_id) {

		//FETCH DATA
    $profile_type = $this->select()
                    ->from($this->info('name'), array('profile_type'))
                    ->where('category_id = ?', $category_id)
										->query()
										->fetchColumn();

		//RETURN DATA
		if(!empty($profile_type)) {
			return $profile_type;
		}

		return 0;
	}
}