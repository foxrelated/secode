<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contents.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Model_DbTable_Contents extends Engine_Db_Table {

  /**
   * Return the module item
   *
   * @param int modulename 
   * @return module Item
   * */
  public function getContentItem($moduleName, $content_id=null) {

    //MAKE CONTENT ARRAY
    $moduleArray = $this->select()
            ->from($this->info('name'), 'resource_type')
            ->where('module = ?', $moduleName);

    if (!empty($content_id)) {
      $moduleArray->where($this->info('name') . '.content_id = ?', $content_id);
    }

    $result = $moduleArray->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);

    //MANIFEST FILE PATH
    $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";

    //INITIALISE CONTENT ITEM ARRAY
    $contentItem = array();
    if (@file_exists($file_path)) {
      $ret = include $file_path;
      if (isset($ret['items'])) {
        foreach ($ret['items'] as $item) {
          if (!in_array($item, $result))
            $contentItem[$item] = $item . " ";
       }
      }
    }

    //CONTENT ITEM
    return $contentItem;
  }

  /**
   * Return content Row
   *
   * @param array $params 
   * @return content Row
   * */
  public function getContentInformation($params) {

    $select = $this->select()
            ->from($this->info('name'), '*');

    if (isset($params['resource_type'])) {
      $select->where($this->info('name') . '.resource_type = ?', $params['resource_type']);
    }

    if (isset($params['content_id'])) {
      $select->where($this->info('name') . '.content_id = ?', $params['content_id']);
    }

    if (isset($params['enabled'])) {
      $select->where($this->info('name') . '.enabled = ?', $params['enabled']);
    }

    return $this->fetchRow($select);
  }

}