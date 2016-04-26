<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Menus.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_Plugin_Sitemobile {
  
  protected $_pagesTable;
  protected $_contentTable;

  public function onIntegrated() {
    $this->_pagesTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    //Page Plugin Main
    $this->addPokePage();
  }

  public function addPokePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('poke_index_index');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'poke_index_index',
          'displayname' => 'Poke List Page',
          'title' => 'Poke List Page',
          'description' => "This page will shows the users who have poked recently and add the friend's to whom you want to poke.",
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'poke.pokeusers',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
          'module' => 'poke'
      ));
    }
  }
}
