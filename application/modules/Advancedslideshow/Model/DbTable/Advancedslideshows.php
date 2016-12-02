<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Advancedslideshows.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_Model_DbTable_Advancedslideshows extends Engine_Db_Table {

  protected $_name = 'advancedslideshows';
  protected $_rowClass = 'Advancedslideshow_Model_Advancedslideshow';

  public function getActiveMod() {
    global $advancedslideshow_is_mod_active;
    return $advancedslideshow_is_mod_active;
  }

  /**
   * Return slideshow status that it is created or not for that position
   *
   * @param int page_id
   * @param int widget_position
   * @return slideshow status
   */
  public function getSlideshowStatus($page_id, $widget_position) {

    //FETCH DATA
    $advancedslideshow_id = $this->select()
																	->from($this->info('name'), 'advancedslideshow_id')
																	->where('widget_position = ?', $widget_position)
																	->where('widget_page = ?', $page_id)
																	->query()
																	->fetchColumn();

    //RETURN DATA
    return $advancedslideshow_id;
  }

}
?>