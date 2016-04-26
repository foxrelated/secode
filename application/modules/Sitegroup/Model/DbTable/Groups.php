<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Groups.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_Groups extends Engine_Db_Table {
 protected $_rowClass = "Sitegroup_Model_Group";

//  protected $_rowClass = "Sitegroup_Model_Group";

  /**
   * Get groups to add as item of the day
   * @param string $title : search text
   * @param int $limit : result limit
   */
  public function getDayItems($title, $limit = 10,$category_id = null) {

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('group_id', 'owner_id', 'title', 'photo_id'));
            
    if(!empty($category_id)) {
       $select->where('category_id = ?',$category_id);
    }
    $select->where($this->info('name') . ".title LIKE ? OR " . $this->info('name') . ".location LIKE ? ", '%' . $title . '%')
            ->where('closed = ?', '0')
            ->where('declined = ?', '0')
            ->where('approved = ?', '1')
            ->where('draft = ?', '1')
            ->order('title ASC')
            ->limit($limit);

    //FETCH RESULTS
    return $this->fetchAll($select);
  }

  public function getGroupsSelectSql($params=array()) {
    $tableName = $this->info('name');
    $select = $this->select()
            ->where("{$tableName}.search = ?", 1)
            ->where("{$tableName}.closed = ?", '0')
            ->where("{$tableName}.approved = ?", '1')
            ->where("{$tableName}.declined = ?", '0')
            ->where("{$tableName}.draft = ?", '1');
    if (Engine_Api::_()->sitegroup()->hasPackageEnable())
      $select->where("{$tableName}.expiration_date  > ?", date("Y-m-d H:i:s"));

    if (isset($params['limit']) && !empty($params['limit']))
      $select->limit($params['limit']);
    return $select;
  }

  public function getTagCloud($limit=100, $category_id, $count_only = 0) {

    $tableTagmaps = 'engine4_core_tagmaps';
    $tableTags = 'engine4_core_tags';

    $tableSitegroups = $this->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($tableSitegroups, array(''))
            ->joinInner($tableTagmaps, "$tableSitegroups.group_id = $tableTagmaps.resource_id", array('COUNT(engine4_core_tagmaps.resource_id) AS Frequency'))
            ->joinInner($tableTags, "$tableTags.tag_id = $tableTagmaps.tag_id", array('text', 'tag_id'))
            ->where($tableSitegroups . '.approved = ?', "1");
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.status.show', 1);

    if ($stusShow == 0) {
      $select = $select->where($tableSitegroups . '.closed = ?', "0");
    }

    if (Engine_Api::_()->sitegroup()->hasPackageEnable())
      $select->where($tableSitegroups . '.expiration_date  > ?', date("Y-m-d H:i:s"));

    $select->where($tableSitegroups . ".search = ?", 1);
    $select = $select->where($tableSitegroups . '.draft = ?', "1")
            ->where($tableSitegroups . '.declined = ?', '0')
            ->where($tableTagmaps . '.resource_type = ?', 'sitegroup_group')
            ->group("$tableTags.text")
            ->order("Frequency DESC");

    if (!empty($category_id)) {
			$select = $select->where($tableSitegroups . '.	category_id =?', $category_id);
    }

    //Start Network work
    $select = $this->getNetworkBaseSql($select);
    //End Network work

		if(!empty($count_only)) {
			$total_results = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			return Count($total_results);
		}

		$select = $select->limit($limit);

    return $select->query()->fetchAll();
  }

  /**
   * Return groups which have this category and this mapping
   *
   * @param int category_id
   * @param int profile_type
   * @return Zend_Db_Table_Select
   */
  public function getCategoryGroup($category_id, $profile_type) {
    $select = $this->select()
            ->from($this->info('name'), 'group_id')
            ->where('category_id = ?', $category_id)
            ->where('profile_type != ?', $profile_type);
    return $this->fetchAll($select)->toArray();
  }

  /**
   * Return groups which can user have to choice to claim
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
  public function getSuggestClaimGroup($params) {
    //SELECT
    $select = $this->select()
            ->from($this->info('name'), array('group_id', 'title', 'userclaim', 'photo_id', 'owner_id'))
            ->where('approved = ?', '1')
            ->where('declined = ?', '0')
            ->where('draft = ?', '1');

    if (isset($params['group_id']) && !empty($params['group_id'])) {
      $select = $select->where('group_id = ?', $params['group_id']);
    }

    if (isset($params['viewer_id']) && !empty($params['viewer_id'])) {
      $select = $select->where('owner_id != ?', $params['viewer_id']);
    }

    if (isset($params['title']) && !empty($params['title'])) {
      $select = $select->where('title LIKE ? ', '%' . $params['title'] . '%');
    }

    if (isset($params['limit']) && !empty($params['limit'])) {
      $select = $select->limit($params['limit']);
    }

    if (isset($params['orderby']) && !empty($params['orderby'])) {
      $select = $select->order($params['orderby']);
    }
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.status.show', 1);
    if ($stusShow == 0) {
      $select = $select
              ->where('closed = ?', '0');
    }
    if (Engine_Api::_()->sitegroup()->hasPackageEnable())
      $select->where('expiration_date  > ?', date("Y-m-d H:i:s"));

    //FETCH
    return $this->fetchAll($select);
  }

  /**
   * Get Popular location base on city and state
   *
   */
  public function getPopularLocation($items_count,$category_id) {
    $limit = $items_count;
    $groupName = $this->info('name');
    $locationTable = Engine_Api::_()->getDbtable('locations', 'sitegroup');
    $locationName = $locationTable->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($groupName, null)
            ->join($locationName, "$groupName.group_id = $locationName.group_id", array("city", "count(city) as count_location", "state", "count(state) as count_location_state"))
            ->group("city")
            ->group("state")
            ->order("count_location DESC")
            ->limit($limit);
    if (!empty($category_id)) {
			$select = $select->where($groupName . '.	category_id =?', $category_id);
    }
    $select->where($groupName . '.approved = ?', '1')
            ->where($groupName . '.declined = ?', '0')
            ->where($groupName . '.draft = ?', '1');
    $select->where($groupName . ".search = ?", 1);
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.status.show', 1);
    if ($stusShow == 0) {
      $select = $select
              ->where($groupName . '.closed = ?', '0');
    }
    if (Engine_Api::_()->sitegroup()->hasPackageEnable())
      $select->where($groupName . '.expiration_date  > ?', date("Y-m-d H:i:s"));

    //Start Network work
    $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
    //End Network work

    return $this->fetchAll($select);
  }

  /**
   * Get Arcive Groups
   *
   * @param int $user_id
   * @return object
   */
  public function getArchiveSitegroup($user_id = null) {

    $rName = $this->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($rName, array('creation_date'))
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.declined = ?', '0')
            ->where($rName . '.draft = ?', '1');
    $select->where($rName . ".search = ?", 1);
    
    if (Engine_Api::_()->sitegroup()->hasPackageEnable())
      $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));

    if (!empty($user_id)) {
      $select->where($rName . '.owner_id = ?', $user_id);
    }

    //Start Network work
    $select = $this->getNetworkBaseSql($select);
    //END NETWORK WORK
    return $this->fetchAll($select);
  }

  /**
   * Get Groups relative to group owner
   *
   * @param int $group_id
   * @param int $owner_id
   * @return objects
   */
  public function userGroup($params = array()) {

    $rName = $this->info('name');
    $select = $this->select()
            ->from($rName, array('group_id', 'title', 'photo_id', 'group_url', 'owner_id', 'view_count', 'like_count'))
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.declined = ?', '0')
            ->where($rName . '.draft = ?', '1')
            ->where($rName . ".search = ?", 1);
    
    if (isset($params['group_id']) && !empty($params['group_id'])) {
      $select = $select->where($rName . '.	group_id !=?', $params['group_id']);
    }    
    
    if (isset($params['owner_id']) && !empty($params['owner_id'])) {
      $select = $select->where($rName . '.	owner_id =?', $params['owner_id']);
    }    

    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select = $select->where($rName . '.	category_id =?', $params['category_id']);
    }
    if (isset($params['featured']) && ($params['featured'] == '1')) {
      $select = $select->where($rName . '.	featured =?', '0');
    } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
      $select = $select->where($rName . '.	featured =?', '1');
    }

    if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
      $select = $select->where($rName . '.	sponsored =?', '0');
    } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
      $select = $select->where($rName . '.	sponsored =?', '1');
    }
    if (Engine_Api::_()->sitegroup()->hasPackageEnable())
      $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));

    if(isset($params['popularity']) && !empty($params['popularity']))
        $select->order($params['popularity'] ." DESC");
    
    $select->order("creation_date DESC");

    //Start Network work
    $select = $this->getNetworkBaseSql($select, array('setIntegrity' => 1));
    //End Network work
    $select = $select->limit($params['totalgroups']);

    return $this->fetchALL($select);
  }

  /**
   * Get Groups for links
   *
   * @param int $group_id
   * @param int $viewer_id
   * @return objects
   */
  public function getGroups($group_id, $viewer_id) {

    $sitegroupName = $this->info('name');
    $select = $this->select()
            ->where($sitegroupName . '.group_id <> ?', $group_id)
            ->where($sitegroupName . '.owner_id  =?', $viewer_id)
            ->where('NOT EXISTS (SELECT `group_id` FROM `engine4_sitegroup_favourites` WHERE `group_id_for`=' . $group_id . ' AND `group_id` = ' . $sitegroupName . '.`group_id`) ');

    return $this->fetchALL($select)->toArray();
  }

//   public function sitegroupselect($group_id) {
//     $sitegroupselect = $this->select()->where('group_id =?', $group_id);
//     return $userGroups = $this->fetchALL($sitegroupselect);
//   }

  /**
   * Get Discussed Groups
   *
   * @return all discussed groups
   */
  public function getDiscussedGroup($params = array()) {
    $sitegroup_tableName = $this->info('name');
    $topic_tableName = Engine_Api::_()->getDbTable('topics', 'sitegroup')->info('name');
    $select = $this->select()->setIntegrityCheck(false)
            ->from($sitegroup_tableName, array('group_id', 'title', 'photo_id', 'group_url', 'owner_id'))
            ->join($topic_tableName, $topic_tableName . '.group_id = ' . $sitegroup_tableName . '.group_id', array('count(*) as counttopics', '(sum(post_count) - count(*) ) as total_count'))
            ->where($sitegroup_tableName . '.closed = ?', '0')
            ->where($sitegroup_tableName . '.approved = ?', '1')
            ->where($sitegroup_tableName . '.draft = ?', '1')
            ->where($topic_tableName . '.post_count > ?', '1')
            ->group($topic_tableName . '.group_id');
    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select = $select->where($sitegroup_tableName . '.	category_id =?', $params['category_id']);
    }
    if (isset($params['featured']) && ($params['featured'] == '1')) {
      $select = $select->where($sitegroup_tableName . '.	featured =?', '0');
    } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
      $select = $select->where($sitegroup_tableName . '.	featured =?', '1');
    }

    if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
      $select = $select->where($sitegroup_tableName . '.	sponsored =?', '0');
    } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
      $select = $select->where($sitegroup_tableName . '.	sponsored =?', '1');
    }
    $select->order('total_count DESC')
            ->order('counttopics DESC')
            ->limit($params['totalgroups']);
    if (Engine_Api::_()->sitegroup()->hasPackageEnable())
      $select->where($sitegroup_tableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    $select->where($sitegroup_tableName . ".search = ?", 1);

    //START NETWORK WORK
    $select = $this->getNetworkBaseSql($select);
    //END NETWORK WORK
    return $this->fetchALL($select);
  }

  /**
   * Get groups based on category
   * @param string $title : search text
   * @param int $category_id : category id
   * @param char $popularity : result sorting based on views, reviews, likes, comments
   * @param char $interval : time interval
   * @param string $sqlTimeStr : Time durating string for where clause 
   */
  public function groupsByCategory($category_id, $popularity, $interval, $sqlTimeStr, $totalGroups) {
    $groupBy = 1; 
    $groupTableName = $this->info('name');

    if ($interval == 'overall' || $popularity == 'view_count') {
      $groupBy = 0;
      $select = $this->select()
              ->from($groupTableName, array('group_id', 'title', 'photo_id', 'group_url', 'owner_id', "$popularity AS populirityCount"))
              ->where($groupTableName . '.category_id = ?', $category_id)
              ->where($groupTableName . '.closed = ?', '0')
              ->where($groupTableName . '.approved = ?', '1')
              ->where($groupTableName . '.declined = ?', '0')
              ->where($groupTableName . '.search = ?', '1')
              ->where($groupTableName . '.draft = ?', '1')
              ->order("$popularity DESC")
              ->order("creation_date DESC")
              ->limit($totalGroups);
      if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
        $select->where($groupTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    } elseif ($popularity == 'review_count' && $interval != 'overall') {

      $popularityTable = Engine_Api::_()->getDbtable('reviews', 'sitegroupreview');
      $popularityTableName = $popularityTable->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($groupTableName, array('group_id', 'title', 'photo_id', 'group_url', 'owner_id', "$popularity AS populirityCount"))
              ->joinLeft($popularityTableName, $popularityTableName . '.group_id = ' . $groupTableName . '.group_id', array("COUNT(review_id) as total_count"))
              ->where($groupTableName . '.category_id = ?', $category_id)
              ->where($groupTableName . '.closed = ?', '0')
              ->where($groupTableName . '.approved = ?', '1')
              ->where($groupTableName . '.declined = ?', '0')
              ->where($groupTableName . '.draft = ?', '1')
              ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
              ->group($groupTableName . '.group_id')
              ->order("total_count DESC")
              ->order($groupTableName . ".creation_date DESC")
              ->limit($totalGroups);
      if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
        $select->where($groupTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    } elseif ($popularity == 'member_count' && $interval != 'overall') {

      $popularityTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
      $popularityTableName = $popularityTable->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($groupTableName, array('group_id', 'title', 'photo_id', 'group_url', 'owner_id', "$popularity AS populirityCount"))
              ->joinLeft($popularityTableName, $popularityTableName . '.group_id = ' . $groupTableName . '.group_id', array("COUNT(member_id) as total_count"))
              ->where($groupTableName . '.category_id = ?', $category_id)
              ->where($groupTableName . '.closed = ?', '0')
              ->where($groupTableName . '.approved = ?', '1')
              ->where($groupTableName . '.declined = ?', '0')
              ->where($groupTableName . '.draft = ?', '1')
              ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
              ->group($groupTableName . '.group_id')
              ->order("total_count DESC")
              ->order($groupTableName . ".creation_date DESC")
              ->limit($totalGroups);
      if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
        $select->where($groupTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    }
    elseif ($popularity != 'view_count' && $popularity != 'review_count' && $popularity != 'member_count' && $interval != 'overall') {

      if ($popularity == 'like_count') {
        $popularityType = 'like';
      } else {
        $popularityType = 'comment';
      }

      $id = $popularityType . "_id";

      $popularityTable = Engine_Api::_()->getDbtable("$popularityType" . "s", 'core');
      $popularityTableName = $popularityTable->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($groupTableName, array('group_id', 'title', 'photo_id', 'group_url', 'owner_id', "$popularity AS populirityCount"))
              ->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $groupTableName . '.group_id', array("COUNT($id) as total_count"))
              ->where($groupTableName . '.category_id = ?', $category_id)
              ->where($groupTableName . '.closed = ?', '0')
              ->where($groupTableName . '.approved = ?', '1')
              ->where($groupTableName . '.declined = ?', '0')
              ->where($groupTableName . '.draft = ?', '1')
              ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
              ->group($groupTableName . '.group_id')
              ->order("total_count DESC")
              ->order($groupTableName . ".creation_date DESC")
              ->limit($totalGroups);
      if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
        $select->where($groupTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    }
    //Start Network work
    $select = $this->getNetworkBaseSql($select, array('not_groupBy' => $groupBy));
    //End Network work
    
    return $this->fetchAll($select);
  }

  public function getItemOfDay() {

    //$sitegroupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $sitegroupTableName = $this->info('name');

    $itemofthedaytable = Engine_Api::_()->getDbtable('itemofthedays', 'sitegroup');
    $itemofthedayName = $itemofthedaytable->info('name');

    $select = $this->select();
    $select = $select->setIntegrityCheck(false)
            ->from($sitegroupTableName, array('group_id', 'title', 'photo_id', 'group_url', 'owner_id'))
            ->join($itemofthedayName, $sitegroupTableName . ".group_id = " . $itemofthedayName . '.resource_id', array('start_date'))
            ->where($sitegroupTableName . '.closed = ?', '0')
            ->where($sitegroupTableName . '.declined = ?', '0')
            ->where($sitegroupTableName . '.approved = ?', '1')
            ->where($sitegroupTableName . '.draft = ?', '1')
            ->where($itemofthedayName . '.resource_type=?', 'sitegroup_group')
            ->where($itemofthedayName . '.start_date <=?', date('Y-m-d'))
            ->where($itemofthedayName . '.end_date >=?', date('Y-m-d'))
            ->order('RAND()');
    return $this->fetchRow($select);
  }

  public function getNetworkBaseSql($select, $params=array()) {
    if (empty($select))
      return;
    $sitegroup_tableName = $this->info('name');
    //START NETWORK WORK
    $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.network', 0);
    if (!empty($enableNetwork) || (isset($params['browse_network']) && !empty($params['browse_network']))) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      if (!Engine_Api::_()->getApi('subCore', 'sitegroup')->groupBaseNetworkEnable()) {
        $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

        if (!empty($viewerNetwork)) {
          if (isset($params['setIntegrity']) && !empty($params['setIntegrity'])) {
            $select->setIntegrityCheck(false)
                    ->from($sitegroup_tableName);
          }
          $networkMembershipName = $networkMembershipTable->info('name');
          $select
                  ->join($networkMembershipName, "`{$sitegroup_tableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                  ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                  ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity());
          if (!isset($params['not_groupBy']) || empty($params['not_groupBy'])) {
            $select->group($sitegroup_tableName . ".group_id");
          }
          if (isset($params['extension_group']) && !empty($params['extension_group'])) {
            $select->group($params['extension_group']);
          }
        }
      } else {
        $viewerNetwork = $networkMembershipTable->getMembershipsOfInfo($viewer);
        $str = array();
        $columnName = "`{$sitegroup_tableName}`.networks_privacy";
        foreach ($viewerNetwork as $networkvalue) {
          $network_id = $networkvalue->resource_id;
          $str[] = "'" . $network_id . "'";
          $str[] = "'" . $network_id . ",%'";
          $str[] = "'%," . $network_id . ",%'";
          $str[] = "'%," . $network_id . "'";
        }
        if (!empty($str)) {
          $likeNetworkVale = (string) ( join(" or $columnName  LIKE ", $str) );
          $select->where($columnName . ' LIKE ' . $likeNetworkVale . ' or ' . $columnName . " IS NULL");
        } else {
          $select->where($columnName . " IS NULL");
        }
      }
    } else {
      $select = $this->addPrivacyGroupsSQl($select, $this->info('name'));
    }
    //END NETWORK WORK
    return $select;
  }

  public function countUserGroups($user_id) {
    $count = 0;
    $select = $this
            ->select()
            ->from($this->info('name'), array('count(*) as count'))
            ->where("owner_id = ?", $user_id);

    return $select->query()->fetchColumn();
  }

  /**
   * Return group is existing or not.
   *
   * @return Zend_Db_Table_Select
   */
  public function checkGroup() {

    //MAKE QUERY
    $hasGroup = $this->select()
                    ->from($this->info('name'), array('group_id'))
                    ->query()
                    ->fetchColumn();

    //RETURN RESULTS
    return $hasGroup;
  }
  
  // get lising according to requerment
  public function getListing($sitegrouptype = '', $params = array()) {

    $limit = 10;
    $table = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $sitegroupTableName = $table->info('name');
    $coreTable = Engine_Api::_()->getDbtable('likes', 'core');
    $coreName = $coreTable->info('name');
    $columns = array('group_id', 'title','photo_id', 'group_url', 'owner_id','view_count', 'like_count', 'comment_count', 'follow_count', 'category_id', 'sponsored', 'featured');
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
			$columns[]="review_count";
      $columns[]="rating";
		}
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
			$columns[]="member_count";
			$columns[]="member_title";
		}    

    $select = $table->select()->from($sitegroupTableName, $columns)
            ->where($sitegroupTableName . '.closed = ?', '0')
            ->where($sitegroupTableName . '.approved = ?', '1')
            ->where($sitegroupTableName . '.draft = ?', '1')
            ->where($sitegroupTableName . ".search = ?", 1);

    //$select = $this->expirySQL($select);

    if (isset($params['group_id']) && !empty($params['group_id'])) {
      $select->where($sitegroupTableName . '.group_id != ?', $params['group_id']);
    }

    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select->where($sitegroupTableName . '.category_id = ?', $params['category_id']);
    }

    if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
      $select->where($sitegroupTableName . '.subcategory_id = ?', $params['subcategory_id']);
    }

    if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
      $select->where($sitegroupTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
    }

    if (isset($params['popularity']) && !empty($params['popularity'])) {
      $select->order($params['popularity'] . " DESC");
    }

    if (isset($params['featured']) && !empty($params['featured']) || $sitegrouptype == 'featured') {
      $select->where("$sitegroupTableName.featured = ?", 1);
    }

    if (isset($params['sponsored']) && !empty($params['sponsored']) || $sitegrouptype == 'sponsored') {
      $select = $select->where($sitegroupTableName . '.sponsored = ?', '1');
    }

    //Start Network work
    $select = $table->getNetworkBaseSql($select, array('setIntegrity' => 1));
    //End Network work

    if ($sitegrouptype == 'most_popular') {
      $select = $select->order($sitegroupTableName . '.view_count DESC');
    }

    if ($sitegrouptype == 'most_reviews' || $sitegrouptype == 'most_reviewed') {
      $select = $select->order($sitegroupTableName . '.review_count DESC');
    }

    if(isset($params['similar_items_order']) && !empty($params['similar_items_order'])) {
      if(isset($params['ratingType']) && !empty($params['ratingType']) && $params['ratingType'] != 'rating_both') {
        $ratingType = $params['ratingType'];
        $select->order($sitegroupTableName . ".$ratingType DESC");
      }
      else {
        $select->order($sitegroupTableName . '.rating_avg DESC');
      }
      $select->order('RAND()');
      
    }else {
      $select->order($sitegroupTableName . '.group_id DESC');
    }

    if (isset($params['limit']) && !empty($params['limit'])) {
      $limit = $params['limit'];
    }

    $select->group($sitegroupTableName . '.group_id');

    if (isset($params['start_index']) && $params['start_index'] >= 0) {
      $select = $select->limit($limit, $params['start_index']);
      return $table->fetchAll($select);
    } else {

      $paginator = Zend_Paginator::factory($select);
      if (!empty($params['group'])) {
        $paginator->setCurrentPageNumber($params['group']);
      }

      if (!empty($params['limit'])) {
        $paginator->setItemCountPerPage($limit);
      }

      return $paginator;
    }
  }  

    /**
     * Get Groups listing according to requerment
     *
     * @param string $sitegrouptype
     * @param array $params
     * @return objects
     */
    public function getListings($sitegrouptype, $params = array(), $interval = NULL, $sqlTimeStr = NULL, $columnsArray = array()) {

        $limit = 10;
        $tempNum = 63542;
        $table = Engine_Api::_()->getDbtable('groups', 'sitegroup');
        $rName = $table->info('name');


        if(empty($columnsArray) || (!empty($columnsArray) && Count($columnsArray) <= 0)) {
            $columnsArray = array('group_id', 'title', 'group_url', 'body', 'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'modified_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'closed', 'email', 'website', 'phone', 'package_id', 'follow_count');
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                $columnsArray[] = 'member_count';
            }
            $columnsArray[] = 'member_title';
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge'))
                $columnsArray[] = 'badge_id';

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer'))
                $columnsArray[] = 'offer';

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
                $columnsArray[] = 'review_count';
                $columnsArray[] = 'rating';
            }             
        }
        $coreTable = Engine_Api::_()->getDbtable('likes', 'core');
        $coreName = $coreTable->info('name');
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
            $tempGetHost = $tempMemberLsetting = 0;
            $getHost = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
            $memberLsetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupmember.lsettings', null);
            for ($check = 0; $check < strlen($getHost); $check++) {
                $tempGetHost += @ord($getHost[$check]);
            }

            for ($check = 0; $check < strlen($memberLsetting); $check++) {
                $tempMemberLsetting += @ord($memberLsetting[$check]);
            }
            $tempPageMemberValues = $tempGetHost + $tempMemberLsetting + $tempNum;
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroupmember.join.type', $tempPageMemberValues);
        }

        $select = $table->select()
                ->setIntegrityCheck(false)
                ->from($rName, $columnsArray)
                ->where($rName . '.closed = ?', '0')
                ->where($rName . '.approved = ?', '1')
                ->where($rName . '.declined = ?', '0')
                ->where($rName . '.draft = ?', '1')
                ->where($rName . ".search = ?", 1);
        if (Engine_Api::_()->sitegroup()->hasPackageEnable())
            $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));

        //Start Network work
        $select = $table->getNetworkBaseSql($select, array());
        //End Network work
        if ($sitegrouptype == 'Most Viewed') {
            $select = $select->where($rName . '.view_count <> ?', '0')->order($rName . '.view_count DESC');
        } elseif ($sitegrouptype == 'Most Viewed List') {
            $select = $select->where($rName . '.view_count <> ?', '0');
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if ($interval != 'overall') {
                $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
            }
            $select->order($rName . '.view_count DESC');

            if (isset($params['totalgroups'])) {
                $limit = $params['totalgroups'];
            }
        } elseif ($sitegrouptype == 'Recently Posted List') {
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            $select = $select->order($rName . '.creation_date DESC');
            if (isset($params['totalgroups'])) {
                $limit = $params['totalgroups'];
            }
        } elseif ($sitegrouptype == 'Random List') {
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            $select->order('RAND() DESC ');
            if (isset($params['totalgroups'])) {
                $limit = $params['totalgroups'];
            }
        } elseif ($sitegrouptype == 'Most Commented') {
            $select = $select->where($rName . '.comment_count <> ?', '0');
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if ($interval != 'overall') {
                $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
            }
            $select->order($rName . '.comment_count DESC');
            if (isset($params['totalgroups'])) {
                $limit = $params['totalgroups'];
            }
        } elseif ($sitegrouptype == 'Top Rated') {
            $select = $select->where($rName . '.rating <> ?', '0')->order($rName . '.rating DESC');
            $limit = $params['itemCount'];
        } elseif ($sitegrouptype == 'Recently Posted') {
            $select = $select->order($rName . '.creation_date DESC');
        } elseif ($sitegrouptype == 'Featured') {
            $select = $select->where($rName . '.featured = ?', '1');
        } elseif ($sitegrouptype == 'Sponosred') {
            $select = $select->where($rName . '.sponsored = ?', '1');
        } elseif ($sitegrouptype == 'Sponsored Sitegroup') {
            $select = $select->where($rName . '.sponsored = ?', '1');
            if (isset($params['totalgroups'])) {
                $limit = $params['totalgroups'];
            }
        } elseif ($sitegrouptype == 'Total Sponsored Sitegroup') {
            $select = $select->where($rName . '.sponsored = ?', '1');
        } elseif ($sitegrouptype == 'Sponsored Sitegroup AJAX') {
            $select = $select->where($rName . '.sponsored = ?', '1');
            if (isset($params['totalgroups'])) {
                $limit = (int) $params['totalgroups'] * 2;
            }
        } elseif ($sitegrouptype == 'Featured Slideshow') {
            $select = $select->where($rName . '.featured = ?', '1');
            if (isset($params['totalgroups'])) {
                $limit = $params['totalgroups'];
            }
            $select->order('RAND() DESC ');
        } elseif ($sitegrouptype == 'Sponosred Slideshow') {
            $select = $select->where($rName . '.sponsored = ?', '1');
            if (isset($params['totalgroups'])) {
                $limit = $params['totalgroups'];
            }
            $select->order('RAND() DESC ');
        } elseif ($sitegrouptype == 'Most Joined') {
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if (isset($params['totalgroups'])) {
                $limit = (int) $params['totalgroups'];
            }
            $select->order($rName . '.member_count DESC');
        } elseif ($sitegrouptype == 'Most Active Groups') {
            if (isset($params['active_groups'])) {
                if ($params['active_groups'] == 'member_count') {
                    $select->order($rName . '.member_count DESC');
                } elseif ($params['active_groups'] == 'comment_count') {
                    $select->order($rName . '.comment_count DESC');
                } elseif ($params['active_groups'] == 'like_count') {
                    $select->order($rName . '.like_count DESC');
                } elseif ($params['active_groups'] == 'view_count') {
                    $select->order($rName . '.view_count DESC');
                }
            }
        } elseif ($sitegrouptype == 'Most Followers') {
            $select = $select->where($rName . '.follow_count <> ?', '0');
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if ($interval != 'overall') {
                $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
            }
            $select->order($rName . '.follow_count DESC');
            if (isset($params['totalgroups'])) {
                $limit = (int) $params['totalgroups'];
            }
        } elseif ($sitegrouptype == 'Most Likes') {
            $select = $select->where($rName . '.like_count <> ?', '0');
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if ($interval != 'overall') {
                $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
            }
            $select->order($rName . '.like_count DESC');

            if (isset($params['totalgroups'])) {
                $limit = (int) $params['totalgroups'];
            }
        } elseif ($sitegrouptype == 'Pin Board') {
            if (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['locationmiles']) && $params['locationmiles']) {
                $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitegroup');
                $locationName = $locationsTable->info('name');
                $radius = $params['locationmiles']; //in miles
                $latitude = $params['latitude'];
                $longitude = $params['longitude'];
                $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.proximity.search.kilometer', 0);
                if (!empty($flage)) {
                    $radius = $radius * (0.621371192);
                }
              //  $latitudeRadians = deg2rad($latitude);
                
								$latitudeSin = "sin(radians($latitude))";
								$latitudeCos = "cos(radians($latitude))";
                $select->join($locationName, "$rName.group_id = $locationName.group_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance", $locationName . '.location AS locationName'));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);
                
                $select->order("distance");
                $select->group("$rName.group_id");
            }
            $popularity = $params['popularity'];
            $interval = $params['interval'];
            //MAKE TIMING STRING
            $sqlTimeStr = '';
            $current_time = date("Y-m-d H:i:s");
            if ($interval == 'week') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
            } elseif ($interval == 'month') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
            }


            if ($interval != 'overall' && $popularity == 'like_count') {

                $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
                $popularityTableName = $popularityTable->info('name');

                $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.group_id', array("COUNT(like_id) as total_count"))
                        ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                        ->order("total_count DESC");
            } elseif ($interval != 'overall' && $popularity == 'follow_count') {

                $popularityTable = Engine_Api::_()->getDbtable('follows', 'seaocore');
                $popularityTableName = $popularityTable->info('name');

                $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.group_id', array("COUNT(follow_id) as total_count"))
                        ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                        ->order("total_count DESC");
            } elseif ($interval != 'overall' && $popularity == 'member_count') {

                if ($interval == 'week') {
                    $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                    $sqlTimeStr = ".join_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
                } elseif ($interval == 'month') {
                    $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                    $sqlTimeStr = ".join_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
                }
                $popularityTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
                $popularityTableName = $popularityTable->info('name');

                $select = $select->join($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.group_id', array("COUNT(member_id) as total_count"))
                        ->where($popularityTableName . $sqlTimeStr)
                        ->where($popularityTableName . ".active =?", 1)
                        ->group($popularityTableName . '.resource_id')
                        ->order("total_count DESC");
            } elseif ($interval != 'overall' && $popularity == 'comment_count') {

                $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
                $popularityTableName = $popularityTable->info('name');

                $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.group_id', array("COUNT(comment_id) as total_count"))
                        ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                        ->order("total_count DESC");
            } else {


                if (isset($popularity) && !empty($popularity)) {
                    $select->order("$rName.$popularity DESC");
                }

                if (isset($params['featured']) && ($params['featured'] == '1')) {
                    $select = $select->where($rName . '.	featured =?', '1');
                }

                if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                    $select = $select->where($rName . '.	sponsored =?', '1');
                }
                if ($interval != 'overall') {
                    $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
                }
            }

            if (isset($params['totalgroups'])) {
                $limit = (int) $params['totalgroups'];
            }
        } elseif ($sitegrouptype == 'Random') {
            $select->order('RAND() DESC ');
        } else if ($sitegrouptype == 'Featured Slideshow') {
            $select->order('RAND() DESC ');
        } else if ($sitegrouptype == 'Sponosred Slideshow') {
            $select->order('RAND() DESC ');
        } else {
            $select->order($rName . '.group_id DESC');
        }

        if (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance']) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitegroup');
            $locationName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
           // $latitudeRadians = deg2rad($latitude);

            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationName, "$rName.group_id = $locationName.group_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance", $locationName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);

            $select->order("distance");
        }
        $select->group("$rName.group_id");

        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select = $select->where($rName . '.	category_id =?', $params['category_id']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $limit = $params['limit'];
        }

        if (($sitegrouptype == 'Sponsored Sitegroup AJAX' || $sitegrouptype == 'Sponsored Sitegroup' ) && !empty($params['start_index'])) {
            $select = $select->limit($limit, $params['start_index']);
        } else {
            if ($sitegrouptype != 'Total Sponsored Sitegroup') {
                $select = $select->limit($limit);
            }
        }
        if (isset($params['paginator']) && !empty($params['paginator'])) {
            return $paginator = Zend_Paginator::factory($select);
        }
        return $table->fetchALL($select);
    }


 /**
   * Return Location Base Groups
   * 
   * @return $groups
   */
  public function getLocationBaseContents($params=array()) {

    if(empty($params['search']))  
    return;
    $limit = 5;
    if (isset($params['limit']) && !empty($params['limit'])) {
      $limit = $params['limit'];
    } else {
      $limit = 5;
    }
    $select = $this->getGroupsSelectSql(array("limit" => $limit));
    //Start Network work
    $select = $this->getNetworkBaseSql($select);
    //End Network work
    $groupName = $this->info('name');
    $locationTable = Engine_Api::_()->getDbtable('locations', 'sitegroup');
    $locationName = $locationTable->info('name');
    $select
            ->setIntegrityCheck(false)
            ->from($groupName, array('title', 'group_id', 'location', 'photo_id', 'category_id'))
            ->join($locationName, "$groupName.group_id = $locationName.group_id", array("latitude", "longitude", "formatted_address"));

    if (isset($params['search']) && !empty($params['search'])) {
			$select->where("`{$groupName}`.title LIKE ? or `{$groupName}`.location LIKE ? or `{$locationName}`.city LIKE ?", "%" . $params['search'] . "%");
    }
    
    if (isset($params['resource_id']) && !empty($params['resource_id'])) {
			$select->where($locationName . '.group_id not in (?)', new Zend_Db_Expr(trim($params['resource_id'], ',')));
    }

    $select->order('creation_date DESC');

    return $this->fetchAll($select);
  }

  /* Return Location Base Groups
   * 
   * @return $groups
   */
  public function getPreviousLocationBaseContents($params=array()) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->getGroupsSelectSql(array("limit" => 5));

    //START NETWORK WORK
    $select = $this->getNetworkBaseSql($select);
    //END NETWORK WORK
   
    //GET GROUP TABLE NAME
    $groupTableName = $this->info('name');

    //LOCATION TABLE NAME
    $locationTableName = Engine_Api::_()->getDbtable('locations', 'sitegroup')->info('name');

    //GET ADD LOCATION TABLE NAME
    $addlocationsTableName =  Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin')->info('name');

    $select =  $select
            ->setIntegrityCheck(false)
            ->from($groupTableName, array('title', 'group_id', 'location', 'photo_id', 'category_id'))
            ->join($addlocationsTableName, "$addlocationsTableName.object_id = $groupTableName.group_id", null)
            ->join($locationTableName, "$locationTableName.location_id = $addlocationsTableName.location_id", array("latitude", "longitude", "formatted_address"))
            ->where("$addlocationsTableName.object_type =?", "sitegroup_group")
            ->where("$addlocationsTableName.owner_id =?", $viewer_id)
            ->group("$addlocationsTableName.object_id")
            ->order("$groupTableName.creation_date DESC");

    return $this->fetchAll($select);
  }
  
//    /**
//    * Return Count Location Base Groups
//    * 
//    * @return $groups
//    */
// 	public function getLocationCount() {
// 	
// 	  $groupTableName = $this->info('name');
// 		$select = $this->select()->from($groupTableName, array('location'))
// 						->where($groupTableName . '.approved = 1')
// 						->where($groupTableName . '.draft = ?', '1')
// 						->where($groupTableName . '.location != ?', '')
// 						->where($groupTableName . '.closed = ?', '0');
// 		return $select->query()->fetchColumn();
// 	}

  /**
   * Return groups which have this category and this mapping
   *
   * @param int category_id
   * @return Zend_Db_Table_Select
   */
  public function getCategorySitegroup($category_id) {

    //RETURN IF CATEGORY ID IS NULL
    if (empty($category_id)) {
      return null;
    }

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), 'group_id')
            ->where('category_id = ?', $category_id);

    //GET DATA
    $categoryData = $this->fetchAll($select);

    if (!empty($categoryData)) {
      return $categoryData->toArray();
    }

    return null;
  }
  
  public function getGroupName($group_id)
  {
    $select = $this->select()
            ->from($this->info('name'), 'title')
            ->where('group_id = ?', $group_id);
    return $select->query()->fetchColumn();
  }
  
  public function getGroupId($owner_id)
  {
    $select = $this->select()
            ->from($this->info('name'), 'group_id')
            ->where('owner_id = ?', $owner_id);
    return $select->query()->fetchAll();
  }
  
  public function getsubGroupids($group_id)	{
  
    $select = $this->select()
            ->from($this->info('name'), 'group_id')
            ->where('subgroup = ?', '1')
            ->where('parent_id = ?', $group_id);
    return $select->query()->fetchAll();
  }
  
  public function getGroupObject($group_id)
  {
    $select = $this->select()
            ->from($this->info('name'), array('group_id', 'title', 'group_url'))
            ->where("group_id IN ($group_id)");
    return $this->fetchAll($select);
  }
  
  public function getLikeCounts($params = array()) {
    
		//GETTING THE CURRENT USER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $coreLikeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $coreLikeTableName = $coreLikeTable->info( 'name' ) ;
    $moduleTable = Engine_Api::_()->getItemTable( 'sitegroup_group' ) ;
    $moduleTableName = $moduleTable->info( 'name' ) ;

    $like_select = $moduleTable->select()
            ->setIntegrityCheck( false )
						->from( $coreLikeTableName, null)
						->join($moduleTableName, "$coreLikeTableName.resource_id = $moduleTableName.group_id", array("COUNT(group_id) as likeCount"))
						->where( $coreLikeTableName . '.resource_type = ?' , 'sitegroup_group' )
						->where($moduleTableName . '.approved = ?', '1')
						->where($moduleTableName . '.declined = ?', '0')
						->where($moduleTableName . '.draft = ?', '1')
						->where($moduleTableName . ".search = ?", 1)
						->where( $coreLikeTableName . '.poster_id = ?' , $user_id );
		if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.status.show', 1) == 0) {
			$like_select = $like_select->where($moduleTableName . '.closed = ?', '0');
		}
    
    return $like_select->query()->fetchColumn();    
  }
/*    
  public function getLikeCounts($params = array()) {
    
		//GETTING THE CURRENT USER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $coreLikeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $coreLikeTableName = $coreLikeTable->info( 'name' ) ;
    $moduleTable = Engine_Api::_()->getItemTable( 'sitegroup_group' ) ;
    $moduleTableName = $moduleTable->info( 'name' ) ;

    $like_select = $moduleTable->select()
            ->setIntegrityCheck( false )
						->from( $coreLikeTableName, null)
						->join($moduleTableName, "$coreLikeTableName.resource_id = $moduleTableName.group_id", array("COUNT(group_id) as likeCount"))
						->where( $coreLikeTableName . '.resource_type = ?' , 'sitegroup_group' )
						->where($moduleTableName . '.approved = ?', '1')
						->where($moduleTableName . '.declined = ?', '0')
						->where($moduleTableName . '.draft = ?', '1')
						->where($moduleTableName . ".search = ?", 1)
						->where( $coreLikeTableName . '.poster_id = ?' , $user_id );
		if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.status.show', 1) == 0) {
			$like_select = $like_select->where($moduleTableName . '.closed = ?', '0');
		}

    
    return $like_select->query()->fetchColumn();    
  }*/
   
  public function getOnlyViewableGroupsId() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $groups_ids = array();
    $cache = Zend_Registry::get('Zend_Cache');
    $cacheName = 'sitegroup_ids_user_id_' . $viewer->getIdentity();
    $data = APPLICATION_ENV == 'development' ? ( Zend_Registry::isRegistered($cacheName) ? Zend_Registry::get($cacheName) : null ) : $cache->load($cacheName);
    if ($data && is_array($data)) {
      $groups_ids = $data;
    } else {
      set_time_limit(0);
      $tableName = $this->info('name');
      $group_select = $this->select()
              ->from($this->info('name'), array('group_id', 'owner_id', 'title', 'photo_id'))
              ->where("{$tableName}.search = ?", 1)
              ->where("{$tableName}.closed = ?", '0')
              ->where("{$tableName}.approved = ?", '1')
              ->where("{$tableName}.declined = ?", '0')
              ->where("{$tableName}.draft = ?", '1');
      if (Engine_Api::_()->sitegroup()->hasPackageEnable())
        $group_select->where("{$tableName}.expiration_date  > ?", date("Y-m-d H:i:s"));

      // Create new array filtering out private albums
      $i = 0;
      foreach ($this->fetchAll($group_select) as $group) {
        if (Engine_Api::_()->authorization()->isAllowed($group, $viewer, 'view')) {
          $groups_ids[$i++] = $group->group_id;
        }
      }

      // Try to save to cache
      if (empty($groups_ids))
        $groups_ids = array(0);

      if (APPLICATION_ENV == 'development') {
        Zend_Registry::set($cacheName, $groups_ids);
      } else {
        $cache->save($groups_ids, $cacheName);
      }
    }

    return $groups_ids;
  }

  public function addPrivacyGroupsSQl($select, $tableName = null) {
    $privacybase = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.privacybase', 0);
    if (empty($privacybase))
      return $select;

    $column = $tableName ? "$tableName.group_id" : "group_id";

    return $select->where("$column IN(?)", $this->getOnlyViewableGroupsId());
  }

}