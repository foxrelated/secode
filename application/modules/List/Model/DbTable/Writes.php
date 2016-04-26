<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Writes.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_DbTable_Writes extends Engine_Db_Table {

  protected $_rowClass = "List_Model_Write";

  public function writeContent($listing_id)
  {
    $text = $this->select()
						->from($this->info('name'), array('text'))
						->where('listing_id = ?' ,$listing_id)
						->query()
						->fetchColumn();

    return $text;
  }

  public function setWriteContent($listing_id, $text) {

    $write_id = $this->select()
						->from($this->info('name'), array('write_id'))
						->where('listing_id = ?' ,$listing_id)
						->query()
						->fetchColumn();

		if(!empty($write_id)) {
			$this->update(array('text' => $text), array('write_id = ?' => $write_id));
		}
		else {
			$write = $this->createRow();
			$write->text = $text;
			$write->listing_id = $listing_id;
			$write->save();
		}
  }

}