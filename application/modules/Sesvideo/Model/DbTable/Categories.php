<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Categories.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_Model_DbTable_Categories extends Engine_Db_Table {
  protected $_rowClass = 'Sesvideo_Model_Category';
  protected $_name = 'video_categories';
  public function getCategoryId($slug = null) {
    if ($slug) {
      $tableName = $this->info('name');
      $select = $this->select()
              ->from($tableName)
              ->where($tableName . '.slug = ?', $slug);
      $row = $this->fetchRow($select);
      if (empty($row)) {
        $category_id = $slug;
      } else
        $category_id = $row->category_id;
    }
    if (isset($category_id))
      return $category_id;
    else
      return;
  }
  public function deleteCategory($params = array()) {
    $isValid = false;
    if (count($params) > 0) {
      if ($params->subcat_id != 0) {
        $Subcategory = $this->getModuleSubsubcategory(array('column_name' => '*', 'category_id' => $params->category_id));
        if (count($Subcategory) > 0)
          $isValid = false;
        else
          $isValid = true;
      }else if ($params->subsubcat_id != 0) {
        $isValid = true;
      } else {
        $category = $this->getModuleSubcategory(array('column_name' => '*', 'category_id' => $params->category_id));
        if (count($category) > 0)
          $isValid = false;
        else
          $isValid = true;
      }
    }
    return $isValid;
  }
  public function getBreadcrumb($params = array()) {
    $category = false;
    $Subcategory = false;
    $subSubcategory = false;
    if (count($params) > 0) {
      if ($params->subcat_id != 0) {
        $category = $this->getModuleCategory(array('column_name' => '*', 'category_id' => $params->subcat_id));
        $Subcategory = $this->getModuleCategory(array('column_name' => '*', 'category_id' => $params->category_id));
      } else if ($params->subsubcat_id != 0) {
        $subSubcategory = $this->getModuleCategory(array('column_name' => '*', 'category_id' => $params->category_id));
        $Subcategory = $this->getModuleCategory(array('column_name' => '*', 'category_id' => $params->subsubcat_id));
        $category = $this->getModuleCategory(array('column_name' => '*', 'category_id' => $Subcategory[0]['subcat_id']));
      } else
        $category = $this->getModuleCategory(array('column_name' => '*', 'category_id' => $params->category_id));
    }
    return array('category' => $category, 'subcategory' => $Subcategory, 'subSubcategory' => $subSubcategory);
  }
  public function slugExists($slug = '', $id = '') {
    if ($slug != '') {
      $tableName = $this->info('name');
      $select = $this->select()
              ->from($tableName)
              ->where($tableName . '.slug = ?', $slug);
      if ($id != '') {
        $select = $select->where('id != ?', $id);
      }
      $row = $this->fetchRow($select);
      if (empty($row)) {
        return true;
      } else
        return false;
    }
    return false;
  }
  public function order($categoryType = 'category_id', $categoryTypeId) {
    // Get a list of all corresponding category, by order
    $table = Engine_Api::_()->getItemTable('sesvideo_category');
    $currentOrder = $table->select()
            ->from($table, 'category_id')
            ->order('order DESC');
    if ($categoryType != 'category_id')
      $currentOrder = $currentOrder->where($categoryType . ' = ?', $categoryTypeId);
    else
      $currentOrder = $currentOrder->where('subcat_id = ?', 0)->where('subsubcat_id = ?', 0);
    return $currentOrder->query()->fetchAll(Zend_Db::FETCH_COLUMN);
  }
  public function orderNext($params = array()) {
    $category_select = $this->select()
            ->from($this->info('name'), '*')
            ->limit(1)
            ->order('order DESC');
    if (isset($params['category_id'])) {
      $category_select = $category_select->where('subcat_id = ?', 0)->where('subsubcat_id = ?', 0);
    } else if (isset($params['subsubcat_id'])) {
      $category_select = $category_select->where('subsubcat_id = ?', $params['subsubcat_id']);
    } else if (isset($params['subcat_id'])) {
      $category_select = $category_select->where('subcat_id = ?', $params['subcat_id']);
    }
    $category_select = $this->fetchRow($category_select);
    if (empty($category_select))
      $order = 1;
    else
      $order = $category_select['order'] + 1;
    return $order;
  }
  public function getCategory($params = array(), $customParams = array(), $searchParams = array()) {
    if (isset($params['column_name'])) {
      $column = $params['column_name'];
    } else
      $column = '*';
    $tableName = $this->info('name');
    $category_select = $this->select()
            ->from($tableName, $column)
            ->where($tableName . '.subcat_id = ?', 0)
            ->where($tableName . '.subsubcat_id = ?', 0);
    if (isset($params['criteria']) && $params['criteria'] == 'alphabetical')
      $category_select->order($tableName . '.category_name');
    if ((isset($params['hasVideo']) && $params['hasVideo']) || isset($params['countVideos']) || (isset($params['criteria']) && $params['criteria'] == 'most_video')) {
			$take = true;
      $videoTable = Engine_Api::_()->getDbTable('videos', 'sesvideo')->info('name');
      $category_select = $category_select->setIntegrityCheck(false);
      $category_select = $category_select->joinLeft($videoTable, "$videoTable.category_id=$tableName.category_id", array("total_videos_categories" => "COUNT(video_id)"));
      if (empty($params['countVideos'])) {
        $category_select = $category_select->having("COUNT($videoTable.category_id) > 0")
                ->group("$videoTable.category_id");
        if (isset($params['videoDesc'])) {
          if ($params['criteria'] && $params['criteria'] == 'most_video') {
            $category_select->order('total_videos_categories DESC');
          }
        }
      } else {
        $category_select = $category_select->group("$tableName.category_id");
        if ($params['criteria'] && $params['criteria'] == 'most_video') {
          $category_select->order('total_videos_categories DESC');
        }
      }
    }
		if(isset($params['videoRequired'])){
			if(empty($take)){
				$videoTable = Engine_Api::_()->getDbTable('videos', 'sesvideo')->info('name');
				$category_select = $category_select->setIntegrityCheck(false);
				$category_select = $category_select->joinLeft($videoTable, "$videoTable.category_id=$tableName.category_id", array("total_videos_categories" => "COUNT(video_id)"));	
			}
				 $category_select = $category_select->having("COUNT($videoTable.category_id) > 0")
                ->group("$videoTable.category_id");
		}
    if ((isset($params['hasChannel']) && $params['hasChannel']) || isset($params['countChannel']) || isset($params['criteria']) && $params['criteria'] == 'most_chanel') {
      $chanelTable = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->info('name');
      $category_select = $category_select->setIntegrityCheck(false);
      $category_select = $category_select->joinLeft($chanelTable, "$chanelTable.category_id=$tableName.category_id", array("total_chanels_categories" => "COUNT(chanel_id)"));
      $viewer = Engine_Api::_()->user()->getViewer();
      if (isset($searchParams['show']) && $searchParams['show'] == 2 && $viewer->getIdentity()) {
        $users = $viewer->membership()->getMembershipsOfIds();
        if ($users)
          $category_select->where($chanelTable . '.owner_id IN (?)', $users);
        else
          $category_select->where($chanelTable . '.owner_id IN (?)', 0);
      }
      if (!empty($searchParams['tag'])) {
        $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tmName = $tmTable->info('name');
        $category_select
                ->joinLeft($tmName, "$tmName.resource_id = $chanelTable.chanel_id", NULL)
                ->where($tmName . '.resource_type = ?', 'sesvideo_chanel')
                ->where($tmName . '.tag_id = ?', $searchParams['tag']);
      }
      if (!empty($searchParams['popularCol']) && $searchParams['popularCol'] == 'is_featured')
        $category_select = $category_select->where($chanelTable . '.is_featured =?', 1);
      if (!empty($searchParams['popularCol']) && $searchParams['popularCol'] == 'is_verified')
        $category_select = $category_select->where($chanelTable . '.is_verified =?', 1);
      if (!empty($searchParams['popularCol']) && $searchParams['popularCol'] == 'is_sponsored')
        $category_select = $category_select->where($chanelTable . '.is_sponsored =?', 1);
      if (!empty($searchParams['popularCol']) && $searchParams['popularCol'] == 'is_hot')
        $category_select = $category_select->where($chanelTable . '.is_hot =?', 1);
      if (!empty($searchParams['search']))
        $category_select = $category_select->where($chanelTable . '.search =?', 1);
      if (!empty($searchParams['category_id']))
        $category_select = $category_select->where($chanelTable . '.category_id =?', $searchParams['category_id']);
      if (!empty($searchParams['subcat_id']))
        $category_select = $category_select->where($chanelTable . '.subcat_id =?', $searchParams['subcat_id']);
      if (!empty($searchParams['subsubcat_id']))
        $category_select = $category_select->where($chanelTable . '.subsubcat_id =?', $searchParams['subsubcat_id']);
      if (!empty($searchParams['text']))
        $category_select = $category_select->where($chanelTable . '.title LIKE "%' . $searchParams['text'] . '%"');
      if (empty($params['countChannel'])) {
        $category_select = $category_select->having("COUNT($chanelTable.category_id) > 0")
                ->group("$chanelTable.category_id");
        if (isset($params['chanelDesc'])) {
          if ($params['criteria'] && $params['criteria'] == 'most_chanel') {
            $category_select->order('total_chanels_categories DESC');
          }
        }
      } else {
        $category_select = $category_select->group("$tableName.category_id");
        if ($params['criteria'] && $params['criteria'] == 'most_chanel') {
          $category_select->order('total_chanels_categories DESC');
        }
      }
			 if (!empty($searchParams['alphabet']) && $searchParams['alphabet'] != 'all')
		      $category_select->where($chanelTable . ".title LIKE ?", $searchParams['alphabet'] . '%');
    }
    if (isset($params['image']) && !empty($params['image']))
      $category_select = $category_select->where($tableName . '.cat_icon !=?', '');
   // if (isset($params['param']) && !empty($params['param']))
    //  $category_select = $category_select->where('param =?', $params['param']);
    $category_select = $category_select->order('order DESC');
    if (isset($params['category_id']) && !empty($params['category_id']))
      $category_select = $category_select->where($tableName . '.category_id = ?', $params['category_id']);
    if (count($customParams)) {
      return Zend_Paginator::factory($category_select);
    }
		
		if(isset($params['limit']) && $params['limit'])
			$category_select->limit($params['limit']);
    return $this->fetchAll($category_select);
  }
  public function getMapping($params = array()) {
    $select = $this->select()->from($this->info('name'), $params);
    $mapping = $this->fetchAll($select);
    if (!empty($mapping)) {
      return $mapping->toArray();
    }
    return null;
  }
  public function getMapId($categoryId = '') {
    $tableName = $this->info('name');
    if ($categoryId) {
      $category_map_id = $this->select()
              ->from($tableName, 'profile_type')
              ->where('category_id = ?', $categoryId)
							->order('order DESC');
      $category_map_id = $this->fetchAll($category_map_id);
      if (isset($category_map_id[0]->profile_type)) {
        return $category_map_id[0]->profile_type;
      } else
        return 0;
    }
  }
  public function getSubCatMapId($subcategoryId = '') {
    $tableName = $this->info('name');
    if ($subcategoryId) {
      $category_map_id = $this->select()
              ->from($tableName, 'profile_type')
              ->where('category_id = ?', $subcategoryId)
							->order('order DESC');
      $category_map_id = $this->fetchAll($category_map_id);
      if (isset($category_map_id[0]->profile_type)) {
        return $category_map_id[0]->profile_type;
      } else
        return 0;
    }
  }
  public function getSubSubCatMapId($subsubcategoryId = '') {
    $tableName = $this->info('name');
    if ($subsubcategoryId) {
      $category_map_id = $this->select()
              ->from($tableName, 'profile_type')
              ->where('category_id = ?', $subsubcategoryId)
							->order('order DESC');
      $category_map_id = $this->fetchAll($category_map_id);
      if (isset($category_map_id[0]->profile_type)) {
        return $category_map_id[0]->profile_type;
      } else
        return 0;
    }
  }
  public function getCategoriesAssoc($params = array()) {
    $stmt = $this->select()
            ->from($this, array('category_id', 'category_name'))
            ->where('subcat_id = ?', 0)
            ->where('subsubcat_id = ?', 0);
    if (isset($params['module'])) {
      $stmt = $stmt->where('resource_type = ?', $params['module']);
    }
    $stmt = $stmt->order('order DESC')
            ->query()
            ->fetchAll();
    $data = array();
    if (isset($params['module']) && $params['module'] == 'group') {
      $data[] = '';
    }
    foreach ($stmt as $category) {
      $data[$category['category_id']] = $category['category_name'];
    }
    return $data;
  }
  public function getColumnName($params = array()) {
    $tableName = $this->info('name');
    $category_select = $this->select()
            ->from($tableName, $params['column_name']);
    if (isset($params['category_id']))
      $category_select = $category_select->where('category_id = ?', $params['category_id']);
    if (isset($params['subcat_id']))
      $category_select = $category_select->where('subcat_id = ?', $params['subcat_id']);
    return $category_select = $category_select->query()->fetchColumn();
  }
  public function getModuleSubcategory($params = array()) {
    $tableName = $this->info('name');
    $category_select = $this->select()
            ->from($this->info('name'), $params['column_name']);
    if (isset($params['category_id']))
      $category_select = $category_select->where($tableName . '.subcat_id = ?', $params['category_id']);
    if (isset($params['countVideos'])) {
      $videoTable = Engine_Api::_()->getDbTable('videos', 'sesvideo')->info('name');
      $category_select = $category_select->setIntegrityCheck(false);
      $category_select = $category_select->joinLeft($videoTable, "$videoTable.subcat_id=$tableName.category_id", array("total_videos_categories" => "COUNT(video_id)"));
      $category_select = $category_select->group("$tableName.category_id");
      $category_select->order('total_videos_categories DESC');
    }
    $category_select = $category_select->order('order DESC');
    return $this->fetchAll($category_select);
  }
  public function getModuleCategory($params = array()) {
    $category_select = $this->select()
            ->from($this->info('name'), $params['column_name']);
    if (isset($params['category_id']))
      $category_select = $category_select->where('category_id = ?', $params['category_id']);
    $category_select = $category_select->order('order DESC');
    return $this->fetchAll($category_select);
  }
  public function getModuleSubsubcategory($params = array()) {
    $tableName = $this->info('name');
    $category_select = $this->select()
            ->from($this->info('name'), $params['column_name']);
    if (isset($params['category_id']))
      $category_select = $category_select->where($tableName . '.subsubcat_id = ?', $params['category_id']);
    if (isset($params['countVideos'])) {
      $videoTable = Engine_Api::_()->getDbTable('videos', 'sesvideo')->info('name');
      $category_select = $category_select->setIntegrityCheck(false);
      $category_select = $category_select->joinLeft($videoTable, "$videoTable.subsubcat_id=$tableName.category_id", array("total_videos_categories" => "COUNT(video_id)"));

      $category_select = $category_select->group("$tableName.category_id");
      $category_select->order('total_videos_categories DESC');
    }
    $category_select = $category_select->order('order DESC');
    return $this->fetchAll($category_select);
  }
}