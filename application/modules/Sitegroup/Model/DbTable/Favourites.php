<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Favourites.php.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegroup_Model_DbTable_Favourites extends Engine_Db_Table {

  protected $_rowClass = "Sitegroup_Model_Favourite";

  //THIS IS FOR ADD fAVOURITE  LINK IS NOT SHOW
  public function isShow($groupId) {

   $favouriteTableName = $this->info('name');
   $select = $this->select()
										->from($favouriteTableName, array('favourite_id'))
										->where('group_id = ?', $groupId)
										->limit(1);
    $findResults = $select->query()->fetchALL();
    if ( !empty($findResults) ) {
      return 1;
    }
    else {
      return 0;
    }
  }

  //THIS FOR DELETE fAVOURITE  LINK IS NOT SHOW
  public function isnotShow($groupId)
  {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $favouriteTableName = $this->info('name');
    $select = $this->select()
										->from($favouriteTableName, array('favourite_id'))
										->where('group_id_for = ?', $groupId)
                    ->where('	owner_id = ?', $viewer_id)
										->limit(1);
    $findResults = $select->query()->fetchALL();

    if ((count($findResults) >= 1)) {
      return 1;
    }
    else {
			return 0;
    }
  }

  // DELETE LINK.
  public function deleteLink($group_id, $viewer_id) {

    $favouritesName = $this->info('name');
    $grouptable = Engine_Api::_()->getDbTable('groups', 'sitegroup');
    $groupTableName = $grouptable->info('name');
    $select = $this->select();
    $select = $select
            ->setIntegrityCheck(false)
            ->from($favouritesName, null)
            ->join($groupTableName, $favouritesName . '.group_id = ' . $groupTableName . '.group_id', array('group_id', 'title'))
            ->where($favouritesName . '.group_id_for = ?', $group_id)
            ->where($favouritesName . '.owner_id = ?', $viewer_id);
    return $this->fetchALL($select);
  }

	/**
   * Return linked groups result
   *
   * @param int $sitegroup_id
	 * @param int $LIMIT
   */
  public function linkedGroups($sitegroup_id, $LIMIT,$params = array(), $flag = null) {

		$groupstable = Engine_Api::_()->getDbTable('groups', 'sitegroup');
    $groupsName = $groupstable->info('name');
    $favouritesName = $this->info('name');
    $select = $groupstable->select()
						->setIntegrityCheck(false);
						
		if (empty($flag)) {
			$select->from($groupstable, array('group_id', 'title','photo_id'))
			      ->join($favouritesName, $favouritesName . '.group_id_for = ' . $groupsName . '.group_id')
			      ->where($favouritesName . '.group_id =?', $sitegroup_id);
		}
		
		//START WORK FOR PARENT GROUP AND SUB GROUP.
		if ($flag == 'subgroup') {
			$select->from($groupstable, array('group_id', 'title','photo_id', 'owner_id'))
			->where($groupsName . '.	parent_id =?', $sitegroup_id)
			->where($groupsName . '.	subgroup =?', '1');
		}
		
	  if ($flag == 'parentgroup') {
			$select->from($groupstable, array('group_id', 'title','photo_id', 'owner_id'))
			->where($groupsName . '.	group_id =?', $sitegroup_id)
			->where($groupsName . '.	parent_id =?', '0')
			->where($groupsName . '.	subgroup =?', '0');
		}
		//END WORK FOR PARENT GROUP AND SUB GROUP.
		
		$select->limit($LIMIT);
		if ( isset($params['category_id']) && !empty($params['category_id']) ) {
			$select = $select->where($groupsName . '.	category_id =?', $params['category_id']);
		}
		if ( isset($params['featured']) && ($params['featured'] == '1') ) {
			$select = $select->where($groupsName . '.	featured =?', '0');
		}
		elseif ( isset($params['featured']) && ($params['featured'] == '2') ) {
			$select = $select->where($groupsName . '.	featured =?', '1');
		}

		if ( isset($params['sponsored']) && ($params['sponsored'] == '1') ) {
			$select = $select->where($groupsName . '.	sponsored =?', '0');
		}
		elseif ( isset($params['sponsored']) && ($params['sponsored'] == '2') ) {
			$select = $select->where($groupsName . '.	sponsored =?', '1');
		}

    if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      return Zend_Paginator::factory($select);
    }
    return $userListings = $groupstable->fetchAll($select);
	}
}
?>