<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Recentlyviewitems.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Model_DbTable_Recentlyviewitems extends Engine_Db_Table
{
	protected $_name = 'sesalbum_recentlyviewitems';
  protected $_rowClass = 'Sesalbum_Model_Recentlyviewitem';	
	public function getitem($params = array()){
		if($params['type'] == 'album_photo'){
			$itemTable = Engine_Api::_()->getItemTable('album_photo');
			$itemTableName = $itemTable->info('name');
			$fieldName = 'photo_id';
		}else{
			$itemTable = Engine_Api::_()->getItemTable('album');
			$itemTableName = $itemTable->info('name');
			$fieldName = 'album_id';
			$not = true;
		}		
		$select = $this->select()
							->from($this->info('name'),array('*'))
							->where('resource_type = ?' ,$params['type'])
							->setIntegrityCheck(false)
						  ->order('creation_date DESC')
							->where($itemTableName.'.photo_id != ?','')
							->group($this->info('name').'.resource_id')
							->limit($params['limit']);
		if($params['criteria'] == 'by_me'){
			$select->where($this->info('name').'.owner_id =?',Engine_Api::_()->user()->getViewer()->getIdentity());
		}else if($params['criteria'] == 'by_myfriend'){
		/*friends array*/
			$friendIds = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
			if(count($friendIds) == 0)
				return array();
			$select->where($this->info('name').".owner_id IN ('".implode(',',$friendIds)."')");
		}
		$select->joinLeft($itemTableName, $itemTableName . ".$fieldName =  ".$this->info('name') . '.resource_id',array('photo_id','album_id'));
	if(!isset($not)){
		$albumTable = Engine_Api::_()->getItemTable('album');
		$albumTableName = $albumTable->info('name');
		$select->joinLeft($albumTableName, $albumTableName . ".album_id =  ".$itemTableName. '.album_id',null);
		$select->where($albumTableName.'.album_id != ?','');
	}else{
		$select->where($itemTableName.'.album_id != ?','');
	}
	if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1))
			$select->where('type IS NULL');
		return $this->fetchAll($select);
	}
}