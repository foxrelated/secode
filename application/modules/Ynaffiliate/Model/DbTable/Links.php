<?php
class Ynaffiliate_Model_DbTable_Links extends Engine_Db_Table
{
 	protected $_rowClass = "Ynaffiliate_Model_Link";
	
	public function getLink($user_id, $target_url, $affiliate_url){
		$select = $this->select()->where('user_id = ?',$user_id)->where('target_url = ?', $target_url)->where('affiliate_url = ?', $affiliate_url);
		$row  = $this->fetchRow($select);
		if(count($row) < 1){
			$row = $this->fetchNew();
			$row->setFromArray(array(
				'user_id'=>$user_id,
				'affiliate_url'=>$affiliate_url,
				'target_url'=>$target_url,
				'link_title'=>'United Title',
			));
			$row->save();
		}
		return $row;
	}
 	public function getLinkID($target, $affiliate) {
		$select = $this->select()->where('target_url = ?', $target)->where('affiliate_url = ?', $affiliate);
		return $this->fetchRow($select);
 	}
	
 	public function addLink($target, $affiliate, $user_id) {
 		$link = $this->fetchNew();
  		$link->link_title = 'Link';
  		$link->user_id = $user_id;
  		//$link->is_dynamic = 1;
  		$link->target_url = $target;
  		$link->affiliate_url = $affiliate;
  		$link->creation_date = date('Y-m-d H:i:s');
  		$link_id = $link->link_id;
  		$link->save();
  		return $link;
 	}
 	
 	public function getTargetLink($link_id) {
 		$select = $this->select()->where('link_id = ?', $link_id);
 		$result = $this->fetchRow($select);
 		return $result->target_url;
 	}
}