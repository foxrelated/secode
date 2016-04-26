<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Topics.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_DbTable_Topics extends Engine_Db_Table {

  protected $_rowClass = 'List_Model_Topic';

  public function getListingTopices($lisiibg_id) {

		//MAKE QUERY
    $select = $this->select()
								->where('listing_id = ?', $lisiibg_id)
								->order('sticky DESC')
								->order('modified_date DESC');

		//RETURN RESULTS
    return Zend_Paginator::factory($select);
  }

}