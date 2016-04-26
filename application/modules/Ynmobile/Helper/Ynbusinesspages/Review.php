<?php

class Ynmobile_Helper_Ynbusinesspages_Review extends Ynmobile_Helper_Base{
	
	public function field_id(){
		$this->data['iReviewId'] = $this->entry->getIdentity();
	}
	
	public function field_listing(){
		$this->field_id();
		$this->field_type();
		$this->field_user();
		$this->field_title();
		$this->field_timestamp();

		$viewer = $this->getViewer();
		$review = $this->entry;

		$this->data['iBusinessId'] =  $review->business_id;
		$this->data['sContent'] =  $review->body;
		$this->data['iRateValue'] =  intval($review->rate_number);

		if ($viewer -> isSelf($review -> getOwner()) || $review->isDeletable()) {
			$this->data['bCanDelete'] = 1;
		} else {
			$this->data['bCanDelete'] = 0;
		}
		if ($viewer -> isSelf($review -> getOwner()) || $review->isEditable()) {
			$this->data['bCanEdit'] = 1;
		} else {
			$this->data['bCanEdit'] = 0;
		}
	}
	
	public function field_infos(){
		$this->field_listing();
	}
	
	public function field_edit(){
		$this->field_infos();
	}
}
