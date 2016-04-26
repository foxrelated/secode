<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Categories.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_DbTable_Categories extends Engine_Db_Table {

  protected $_rowClass = 'Sesmusic_Model_Category';

  public function getCategory($params = array()) {

    $select = $this->select()
            ->from($this->info('name'), $params['column_name'])
            ->where('subcat_id = ?', 0)
            ->where('subsubcat_id = ?', 0);

    if (isset($params['category_id']) && !empty($params['category_id']))
      $select = $select->where('subcat_id = ?', $params['category_id']);

    if (isset($params['image']) && !empty($params['image']))
      $select = $select->where('cat_icon !=?', '');

    if (isset($params['param']) && !empty($params['param']))
      $select = $select->where('param =?', $params['param']);

    return $this->fetchAll($select);
  }

  public function getCategoriesAssoc($params = array()) {

    $select = $this->select()
            ->from($this->info('name'), array('category_id', 'category_name'))
            ->where('subcat_id = ?', 0)
            ->where('subsubcat_id = ?', 0);

    if (isset($params['module']))
      $select = $select->where('resource_type = ?', $params['module']);

    $select = $select->order('category_name ASC')
            ->query()
            ->fetchAll();

    $data = array();
    if (isset($params['module']) && $params['module'] == 'group') {
      $data[] = '';
    }

    foreach ($select as $category) {
      $data[$category['category_id']] = $category['category_name'];
    }

    return $data;
  }

  public function getColumnName($params = array()) {

    $select = $this->select()
            ->from($this->info('name'), $params['column_name']);

    if (isset($params['category_id']))
      $select = $select->where('category_id = ?', $params['category_id']);

    return $select = $select->query()->fetchColumn();
  }

  public function getModuleSubcategory($params = array()) {

    $select = $this->select()
            ->from($this->info('name'), $params['column_name']);

    if (isset($params['category_id']))
      $select = $select->where('subcat_id = ?', $params['category_id']);

    if (isset($params['param']))
      $select = $select->where('param = ?', $params['param']);

    return $this->fetchAll($select);
  }

  public function getModuleSubsubcategory($params = array()) {

    $select = $this->select()
            ->from($this->info('name'), $params['column_name']);

    if (isset($params['category_id']))
      $select = $select->where('subsubcat_id = ?', $params['category_id']);

    if (isset($params['param']))
      $select = $select->where('param = ?', $params['param']);

    return $this->fetchAll($select);
  }

}

