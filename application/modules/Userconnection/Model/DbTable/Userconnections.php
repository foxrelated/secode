<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Userconnections.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_Model_DbTable_Userconnections extends Engine_Db_Table
{
  protected $_name = 'userconnection_setting';
  protected $_rowClass = 'Userconnection_Model_Userconnection';

	public function getViewerConnection() {
		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $tableName = $this->info('name');
    $select = $this->select()->where('user_id =?', $viewer_id);
    $fetchObj = $select->query()->fetchAll();
		if( !empty($fetchObj) ){ return false; }
		return true;
	}
}