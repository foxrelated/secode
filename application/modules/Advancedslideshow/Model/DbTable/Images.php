<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Images.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_Model_DbTable_Images extends Engine_Db_Table {

  protected $_rowClass = 'Advancedslideshow_Model_Image';

  /**
   * Return number of slides
   *
   * @param int $advancedslideshow_id
   * @return number of slides
   */
  public function getTotalSlides($advancedslideshow_id) {
    //GET NUMBER OF SLIDES
    $totalSlides = $this->select()
                    ->from($this->info('name'), array('COUNT(*) AS count'))
                    ->where('advancedslideshow_id = ?', $advancedslideshow_id)
                    ->query()
                    ->fetchColumn();

    //RETURN NUMBER OF SLIDES
    return $totalSlides;
  }

  /**
   * Return preview images data
   *
   * @param int advancedslideshow_id
   * @return Zend_Db_Table_Select
   */
  public function getPreviewImages($advancedslideshow_id) {

    //MAKE QUERY
    $select = $this->select()
                    ->from($this->info('name'))
                    ->where('advancedslideshow_id = ?', $advancedslideshow_id)
                    ->where('enabled = ?', 1)
                    ->order('order ASC')
                    ->order('image_id DESC');

    //RETURN DATA
    return Zend_Paginator::factory($select);
  }

  /**
   * Return images data
   *
   * @param int advancedslideshow_id
   * @return Zend_Db_Table_Select
   */
  public function getImages($advancedslideshow_id) {

    //MAKE QUERY
    $select = $this->select()
                    ->from($this->info('name'), array('image_id'))
                    ->where('advancedslideshow_id = ?', $advancedslideshow_id);

    //RETURN DATA
    return $this->fetchAll($select);
  }

  /**
   * Return slides data query for widgets
   *
   * @param int page_id
   * @param int widget_position
   * @return data query
   */
  public function getSlides($page_id, $widget_position) {

    //GET SLIDESHOW TABLE
    $tableSlideshow = Engine_Api::_()->getDbTable('advancedslideshows', 'advancedslideshow');
    $tableSlideshowName = $tableSlideshow->info('name');

    //GET SLIDES TABLE NAME
    $tableImageName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($tableImageName)
                    ->join($tableSlideshowName, "$tableSlideshowName.advancedslideshow_id = $tableImageName.advancedslideshow_id", array())
                    ->where($tableSlideshowName . '.widget_position = ?', $widget_position)
                    ->where($tableSlideshowName . '.widget_page = ?', $page_id)
                    ->where($tableImageName . '.enabled = ?', 1)
                    ->order($tableImageName . '.order ASC');

    //RETURN QUERY
    return $select;
  }

  /**
   * Return images data
   *
   * @param array advancedslideshow
   * @param string select
   * @return Zend_Db_Table_Select
   */
  public function getQuery($advancedslideshow, $select) {

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET SLIDES TABLE NAME
    $tableImageName = $this->info('name');

    //GET VIEWER ID
    if (!empty($viewer_id)) {
      $level_id = Engine_Api::_()->user()->getViewer()->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    if (!empty($advancedslideshow->level)) {
      $select->where($tableImageName . ". level  LIKE ?", '%"' . $level_id . '"%');
    } elseif(empty($viewer_id)) {
      $select->where($tableImageName . '.show_public = ?', 1);
    }

    if (!empty($advancedslideshow->network)) {
      $tableNetwork = Engine_Api::_()->getDbTable('membership', 'network');
      $tableNetworkName = $tableNetwork->info('name');
      $selectId = $tableNetwork->select()
                      ->from($tableNetworkName, 'resource_id')
                      ->where('user_id = ?', $viewer_id);
      $networkIdArray = $tableNetwork->fetchAll($selectId)->toArray();
      $query = '';
      if (!empty($networkIdArray)) {
        $count = 0;
        foreach ($networkIdArray as $key => $image_ids) {
          $id = strval($image_ids['resource_id']);
          $network_id = '"' . $id . '"';

          if ($count == 0) {
            $query .= "(engine4_advancedslideshow_images.network  LIKE '%$network_id%')";
          } else {
            $query .= " OR (engine4_advancedslideshow_images.network  LIKE '%$network_id%')";
          }
          $count++;
        }
      }
      if (!empty($query)) {
        $select->where($query);
      }
    }

    //RETURN DATA
    return $this->fetchAll($select);
  }

}
?>
