<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Chanelfollows.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Model_DbTable_Chanelfollows extends Engine_Db_Table {

  protected $_rowClass = "Sesvideo_Model_Chanelfollow";
  protected $_name = 'video_chanelfollows';

  public function checkFollow($userId, $chanelId = null) {

    $followId = $this->select()->from($this->info('name'), 'chanelfollow_id')->where('chanel_id =?', $chanelId)->where('owner_id =?', $userId)->query()->fetchColumn();
    if ($followId)
      return $followId;
    else
      return 0;
  }

  public function getChanelFollowers($chanelId = null,$paginator = true ,$owner_id = false) {
    if (!$chanelId)
      return;
    $select = $this->select()->from($this->info('name'))
            ->where('chanel_id =?', $chanelId);
		if($owner_id)
			$select->where('owner_id !=?',$owner_id);
		if(!$paginator)
			return $this->fetchAll($select);
    return Zend_Paginator::factory($select);
  }

}
