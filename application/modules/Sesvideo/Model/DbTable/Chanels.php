<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Chanels.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Model_DbTable_Chanels extends Engine_Db_Table {

  protected $_rowClass = "Sesvideo_Model_Chanel";
  protected $_name = 'video_chanels';
	
  public function checkUrl($data, $chanel_id = false) {
    $tableName = $this->info('name');
    $select = $this->select()
            ->from($tableName)
            ->where($tableName . '.custom_url = ?', $data);
    if ($chanel_id)
      $select = $select->where($tableName . '.chanel_id != ?', $chanel_id);
    return Zend_Paginator::factory($select);
  }
	public function peopleAlsoLiked($id = 0){
		$likesTable = Engine_Api::_()->getDbtable('likes', 'core');
    $likesTableName = $likesTable->info('name');
    $select = $this->select()
            ->distinct(true)
            ->from($this->info('name'))
            ->joinLeft($likesTableName, $likesTableName . '.resource_id=chanel_id', null)
            ->joinLeft($likesTableName . ' as l2', $likesTableName . '.poster_id=l2.poster_id', null)
            ->where($likesTableName . '.poster_type = ?', 'user')
            ->where('l2.poster_type = ?', 'user')
            ->where($likesTableName . '.resource_type = ?', 'sesvideo_chanel')
            ->where('l2.resource_type = ?', 'sesvideo_chanel')
            ->where($likesTableName . '.resource_id != ?', $id)
            ->where('l2.resource_id = ?', $id)
            ->where('search = ?', true)
            ->where('chanel_id != ?', $id)
   				 //->order(new Zend_Db_Expr('COUNT(like_id)'))
    ;
			return Zend_Paginator::factory($select);
	}
  public function getChanels($params = array(), $paginator = true, $searchParams = array()) {
    $tableName = $this->info('name');
    $vcName = Engine_Api::_()->getDbtable('chanelvideos', 'sesvideo');
    $vcmName = $vcName->info('name');
    $fcName = Engine_Api::_()->getDbtable('chanelfollows', 'sesvideo');
    $fcmName = $fcName->info('name');
    $select = $this->select()
            ->from($tableName)
            ->setIntegrityCheck(false)
            ->joinLeft($vcmName, "$vcmName.chanel_id = $tableName.chanel_id", array("total_videos" => "COUNT(video_id)"))
            ->group("$vcmName.chanel_id");
    if (!empty($searchParams['tag'])){
      $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
      $tmName = $tmTable->info('name');
      $select
              ->joinLeft($tmName, "$tmName.resource_id = $tableName.chanel_id", NULL)
              ->where($tmName . '.resource_type = ?', 'sesvideo_chanel')
              ->where($tmName . '.tag_id = ?', $searchParams['tag']);
    }
		if(!empty($params['sameTag'])){
			 $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
      $tmName = $tmTable->info('name');
			$select->joinLeft($tmName, 'resource_id = '.$tableName.'.chanel_id', null)
            ->where('resource_type = ?', 'sesvideo_chanel')
						->distinct(true)
            ->where('resource_id != ?', $params['sameTagresource_id'])
            ->where('tag_id IN(?)', $params['sameTagTag_id']);
		}
		if(isset($params['video_count'])){
				 $select = $select->order("total_videos DESC");
		}
    $doneSe = false;
    if (!empty($searchParams['popularCol']) && $searchParams['popularCol'] == 'is_featured') {
      $select = $select->where($tableName . '.is_featured =?', 1);
      $doneSe = true;
    }
    if (!empty($searchParams['popularCol']) && $searchParams['popularCol'] == 'is_sponsored') {
      $select = $select->where($tableName . '.is_sponsored =?', 1);
      $doneSe = true;
    }
    if (!empty($searchParams['popularCol']) && $searchParams['popularCol'] == 'is_hot') {
      $select = $select->where($tableName . '.is_hot =?', 1);
      $doneSe = true;
    }
    if (!empty($searchParams['popularCol']) && $searchParams['popularCol'] == 'is_verified') {
      $select = $select->where($tableName . '.is_verified =?', 1);
      $doneSe = true;
    }
    if (!empty($searchParams['popularCol']) && !$doneSe) {
      $select = $select->order("$tableName." . $searchParams['popularCol'] . " DESC");
    }
    if (isset($params['order']))
      $select = $select->order("$tableName." . $params['order'] . " DESC");     
    if (isset($params['chanelphoto'])) {
      $pcName = Engine_Api::_()->getDbtable('chanelphotos', 'sesvideo');
      $pcmName = $pcName->info('name');
      $select = $select->joinLeft($pcmName, "$pcmName.chanel_id = $tableName.chanel_id", array('chanelphoto_id'));
      $select = $select->where($pcmName . '.chanelphoto_id !=?', '');
    }
	
    if (isset($params['widgetName']) && $params['widgetName'] == 'oftheday') {
      $select->where($tableName . '.offtheday =?', 1)
              ->where($tableName . '.starttime <= DATE(NOW())')
              ->where($tableName . '.endtime >= DATE(NOW())')
              ->order('RAND()');
    }
    if (isset($params['follow_id'])) {
      $select = $select->joinLeft($fcmName, "$fcmName.chanel_id = $tableName.chanel_id", array("follow_videos" => "COUNT(distinct(chanelfollow_id))"));
      $select = $select->where($fcmName . '.owner_id =?', $params['follow_id']);
    } else {
      $select = $select->joinLeft($fcmName, "$fcmName.chanel_id = $tableName.chanel_id", array("follow_videos" => "COUNT(distinct(chanelfollow_id))"));
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    if (isset($searchParams['show']) && $searchParams['show'] == 2 && $viewer->getIdentity()) {
      $users = $viewer->membership()->getMembershipsOfIds();
      if ($users)
        $select->where($tableName . '.owner_id IN (?)', $users);
      else
        $select->where($tableName . '.owner_id IN (?)', 0);
    }
    if (!empty($searchParams['search']))
      $select = $select->where($tableName . '.search =?', 1);
    if (!empty($searchParams['category_id']))
      $select = $select->where($tableName . '.category_id =?', $searchParams['category_id']);
    if (!empty($searchParams['subcat_id']))
      $select = $select->where($tableName . '.subcat_id =?', $searchParams['subcat_id']);
    if (!empty($searchParams['subsubcat_id']))
      $select = $select->where($tableName . '.subsubcat_id =?', $searchParams['subsubcat_id']);
    if (!empty($searchParams['text']))
      $select = $select->where($tableName . '.title LIKE "%' . $searchParams['text'] . '%"');
    if (!empty($params['chanel_id']))
      $select = $select->where($tableName . '.chanel_id =?', $params['chanel_id']);
		if (!empty($params['not_chanel_id']))
      $select = $select->where($tableName . '.chanel_id != ?', $params['not_chanel_id']);
    if (!empty($params['popularCol']))
      $select = $select->order($params['popularCol'] . ' DESC');
    if (!empty($params['fixedData']) && $params['fixedData'] != '')
      $select = $select->where($tableName . '.' . $params['fixedData'] . ' =?', 1);
    if (!empty($params['search']))
      $select = $select->where($tableName . '.search =?', 1);
    if (!empty($params['user_id']))
      $select = $select->where($tableName . '.owner_id =?', $params['user_id']);
    if (!empty($params['status']))
      $select = $select->where($tableName . '.status =?', 1);
    if (!empty($params['is_featured']))
      $select = $select->where($tableName . '.is_featured =?', 1);
    if (!empty($params['is_sponsored']))
      $select = $select->where($tableName . '.is_sponsored =?', 1);
    if (!empty($params['is_hot']))
      $select = $select->where($tableName . '.is_hot =?', 1);
    if (!empty($params['category_id']))
      $select = $select->where($tableName . '.category_id =?', $params['category_id']);
    if (!empty($params['subcat_id']))
      $select = $select->where($tableName . '.subcat_id =?', $params['subcat_id']);
    if (!empty($params['subsubcat_id']))
      $select = $select->where($tableName . '.subsubcat_id =?', $params['subsubcat_id']);
    if (!empty($params['search']))
      $select = $select->where($tableName . '.title LIKE "%' . $params['search'] . '%"');
    if (!empty($params['limit_data']))
      $select = $select->limit($params['limit_data']);
    if (isset($params['criteria'])) {
      if ($params['criteria'] == 1)
        $select->where($tableName . '.is_featured =?', '1');
      else if ($params['criteria'] == 2)
        $select->where($tableName . '.is_sponsored =?', '1');
      else if ($params['criteria'] == 6)
        $select->where($tableName . '.is_hot =?', '1');
      else if ($params['criteria'] == 3)
        $select->where($tableName . '.is_featured = 1 OR ' . $tableName . '.is_sponsored = 1');
      else if ($params['criteria'] == 4)
        $select->where($tableName . '.is_featured = 0 AND ' . $tableName . '.is_sponsored = 0');
    }
    if (!empty($params[0]['alphabet']) && $params[0]['alphabet'] != 'all')
      $select->where($tableName . ".title LIKE ?", $params[0]['alphabet'] . '%');
    if (isset($params['criteria'])) {
      switch ($params['info']) {
        case 'recently_created':
          $select->order('creation_date DESC');
          break;
        case 'most_viewed':
          $select->order('view_count DESC');
          break;
        case 'most_liked':
          $select->order('like_count DESC');
          break;
        case 'most_rated':
          $select->order('rating DESC');
          break;
        case 'most_favourite':
          $select->order('favourite_count DESC');
          break;
        case 'most_commented':
          $select->order('comment_count DESC');
          break;
      }
    }
		 $select = $select->order("$tableName.creation_date DESC");
		 if (isset($params['limit_data']))
      $select = $select->limit($params['limit_data']);
    if ($paginator)
      $paginator = Zend_Paginator::factory($select);
    else
      $paginator = $this->fetchAll($select);
    return $paginator;
  }
  public function countChanels() {
    $select = $this->select()
            ->from($this->info('name'), array('*'));
    return Zend_Paginator::factory($select);
  }
  public function chanelLikes($params = array()) {
    $parentTable = Engine_Api::_()->getItemTable('core_like');
    $parentTableName = $parentTable->info('name');
    $select = $parentTable->select()
            ->from($parentTableName)
            ->where('resource_type = ?', 'sesvideo_chanel')
            ->order('like_id DESC');
    if (isset($params['id']))
      $select = $select->where('resource_id = ?', $params['id']);
    if (isset($params['poster_id']))
      $select = $select->where('poster_id =?', $params['poster_id']);
    return Zend_Paginator::factory($select);
  }
	public function getFavourite($params = array()){
		$tableFav = Engine_Api::_()->getDbtable('favourites', 'sesvideo');
		$tableFav = $tableFav->info('name');
		$select = $this->select()
							->from($this->info('name'))
							->where('chanel_id = ?',$params['resource_id'])
							->setIntegrityCheck(false)
							->where('resource_type =?','sesvideo_chanel')
							->order('favourite_id DESC')
							->joinLeft($tableFav, $tableFav . '.resource_id=' . $this->info('name') . '.chanel_id',array('user_id'));
							return  Zend_Paginator::factory($select);
	}
  public function getChanelId($slug = null) {
    if ($slug) {
      $tableName = $this->info('name');
      $select = $this->select()
              ->from($tableName)
              ->where($tableName . '.custom_url = ?', $slug);
      $row = $this->fetchRow($select);
      if (empty($row)) {
        $chanel_id = $slug;
      } else
        $chanel_id = $row->chanel_id;
      return $chanel_id;
    }
    return '';
  }
}