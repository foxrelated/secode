<?php

class Ynmobile_Helper_Ynbusinesspages_Photo extends Ynmobile_Helper_AlbumPhoto{
	function getYnmobileApi(){
		return Engine_Api::_()->getApi('ynbusinesspages','ynmobile');
	}

//	function field_parent(){
//		$this->data['iParentId'] = $this->entry->business_id;
//		$this->data['sParentType'] = 'ynbusinesspages_business';
//	}

	function field_canDelete() {
		$this->data['bCanDelete'] = $this->entry->getBusiness()->isAllowed('album_delete', null, $this->entry)? 1 : 0;
	}
}
