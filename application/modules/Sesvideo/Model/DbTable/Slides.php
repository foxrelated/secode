<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Slides.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesvideo_Model_DbTable_Slides extends Engine_Db_Table {
	protected $_rowClass = "Sesvideo_Model_Slide";
  public function getSlides($id) {
    $tableName = $this->info('name');
    $select = $this->select()
            ->where('gallery_id =?', $id)
						->order('order ASC')
            ->from($tableName);
    return Zend_Paginator::factory($select);
  }

}
