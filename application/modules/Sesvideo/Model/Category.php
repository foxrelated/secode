<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Category.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Model_Category extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;
  public function getTitle() {
    return $this->category_name;
  }

  public function getTable() {
    if (is_null($this->_table)) {
      $this->_table = Engine_Api::_()->getDbtable('categories', 'sesvideo');
    }

    return $this->_table;
  }

  public function getHref($params = array()) {
    if ($this->slug == '') {
      return;
    }
    $params = array_merge(array(
        'route' => 'sesvideo_category_view',
        'reset' => true,
        'category_id' => $this->slug,
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  public function getBrowseCategoryHref($params = array()) {
    $params = array_merge(array(
        'route' => 'sesvideo_chanel_category',
        'reset' => true,
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }
	 public function getBrowseVideoHref($params = array()) {
    $params = array_merge(array(
        'route' => 'sesvideo_general',
				'action'=>'browse',
        'reset' => true,
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }
  public function getUsedCount() {
    $table = Engine_Api::_()->getDbTable('videos', 'sesvideo');
    $rName = $table->info('name');
    $select = $table->select()
            ->from($rName)
            ->where($rName . '.category_id = ?', $this->category_id);
    $row = $table->fetchAll($select);
    $total = count($row);
    return $total;
  }

}
