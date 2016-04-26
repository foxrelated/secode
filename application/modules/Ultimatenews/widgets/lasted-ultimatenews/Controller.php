<?php

class Ultimatenews_Widget_LastedUltimatenewsController extends Ultimatenews_Content_Widget_Base {

  protected function _getItems() {
    $table = Engine_Api::_()->getDbtable('Contents', 'ultimatenews');
    $limit = $this->_getParam('max', 5);
    if ($limit <= 0) {
      $limit = 5;
    }

    $select = $table->select('engine4_ultimatenews_contents')->setIntegrityCheck(false)
            ->joinLeft("engine4_ultimatenews_categories", "engine4_ultimatenews_categories.category_id= engine4_ultimatenews_contents.category_id", array('logo' => 'engine4_ultimatenews_categories.category_logo', 'logo_icon' => 'engine4_ultimatenews_categories.logo', 'display_logo' => 'engine4_ultimatenews_categories.display_logo', 'mini_logo' => 'engine4_ultimatenews_categories.mini_logo'))
            ->order('engine4_ultimatenews_contents.pubDate DESC')
            ->where('engine4_ultimatenews_categories.is_active= ? ', 1)
            ->where('engine4_ultimatenews_categories.approved= ? ', 1)
            ->limit($limit);

    return $table->fetchAll($select);
  }

}