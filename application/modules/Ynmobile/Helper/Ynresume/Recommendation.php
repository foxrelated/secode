<?php

class Ynmobile_Helper_Ynresume_Recommendation extends Ynmobile_Helper_Base{

    public function field_id(){
        $this->data['iRecommendationId'] = $this->entry->getIdentity();
    }

    public function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_timestamp();
        $item = $recommendation = $this->entry;
        $this->data['sContent'] = $item->content;
        $this->data['sGivenDate'] = date("M, d, Y,", $recommendation->getGivenDate()->getTimestamp());
    }
}
