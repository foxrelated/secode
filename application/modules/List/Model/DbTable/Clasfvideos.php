<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Clasfvideos.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_DbTable_Clasfvideos extends Engine_Db_Table {

  protected $_rowClass = 'List_Model_Clasfvideo';

  public function getListingVideos($listing_id = 0, $pagination = 0) {

		//VIDEO TABLE NAME
    $videoTableName = $this->info('name');

		//GET CORE VIDEO TABLE
    $coreVideoTable = Engine_Api::_()->getDbtable('videos', 'video');
    $coreVideoTableName = $coreVideoTable->info('name');

		//MAKE QUERY
    $select = $coreVideoTable->select()
            ->setIntegrityCheck(false)
            ->from($coreVideoTableName)
            ->join($videoTableName, $coreVideoTableName . '.video_id = ' . $videoTableName . '.video_id', array())
            ->group($coreVideoTableName . '.video_id')
            ->where($videoTableName . '.listing_id = ?', $listing_id);

		//FETCH RESULTS
		if(!empty($pagination)) {
			return Zend_Paginator::factory($select);
		}
		else {
			return $row = $coreVideoTable->fetchAll($select);
		}
  }

}