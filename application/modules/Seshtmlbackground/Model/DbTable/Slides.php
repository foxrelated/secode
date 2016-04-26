<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Seshtmlbackground
 * @package    Seshtmlbackground
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Slides.php 2015-10-22 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Seshtmlbackground_Model_DbTable_Slides extends Engine_Db_Table {
	protected $_rowClass = "Seshtmlbackground_Model_Slide";

  public function getSlides($id, $show_type = '',$status = false) {
    $tableName = $this->info('name');
    $select = $this->select()
            ->where('gallery_id =?', $id);
    if(empty($show_type))
            $select->where('enabled =?', 1);
	   $select ->order('order ASC')
            ->from($tableName);
	  if($status)
			$select = $select->where('status = 1');
    return Zend_Paginator::factory($select);
  }

}
