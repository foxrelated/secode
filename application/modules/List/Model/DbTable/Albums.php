<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Albums.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_DbTable_Albums extends Engine_Db_Table
{
  protected $_rowClass = 'List_Model_Album';

  public function getSpecialAlbum(List_Model_Listing $list, $type) {

    $listing_id = $list->listing_id;
    $select = $this->select()
            ->where('listing_id = ?', $listing_id)
            ->order('album_id ASC')
            ->limit(1);
    $album = $this->fetchRow($select);
    return $album;
  }

}