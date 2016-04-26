<?php

class Ynmobile_Helper_Ynlisting_Review extends Ynmobile_Helper_Base{
	
	public function field_id(){
		$this->data['iReviewId'] = $this->entry->getIdentity();
	}
	
	public function field_listing(){
		$this->field_id();
		$this->field_type();
		$this->field_user();
		$this->field_timestamp();
		$this->data['iListingId'] =  $this->entry->listing_id;
		$this->data['sContent'] =  $this->entry->body;
		$this->data['iRateValue'] =  intval($this->entry->rate_number);
		
	}
	
	public function field_infos(){
		$this->field_listing();
	}
	
	public function field_edit(){
		$this->field_infos();
	}
}
