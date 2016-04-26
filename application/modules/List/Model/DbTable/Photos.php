<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Photos.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_DbTable_Photos extends Engine_Db_Table {

  protected $_rowClass = 'List_Model_Photo';

  public function getPhotoId($listing_id = null, $file_id = null) {

    $photo_id = 0;
    $photo_id = $this->select()
									->from($this->info('name'), array('photo_id'))
									->where("listing_id = ?", $listing_id)
									->where("file_id = ?", $file_id)
									->query()
									->fetchColumn();

    return $photo_id;
  }

}