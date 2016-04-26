<?php

class Ynmobile_Helper_Ynresume_Experience extends Ynmobile_Helper_Base{

    public function field_id(){
        $this->data['iExperienceId'] = $this->entry->getIdentity();
    }

    public function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_timestamp();
        $this->field_resume_photos();
        $this->field_resume_period(true);

        $item = $experience = $this->entry;

        // get company name
        $companyName = '';
        $business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
        $business = null;
        if ($item->business_id) {
            $business = ($business_enable) ? Engine_Api::_()->getItem('ynbusinesspages_business', $item->business_id) : null;
        }
        if ($business && !$business->deleted) {
            $companyName = $business->getTitle();
            $companyDetail = Ynmobile_AppMeta::getInstance()->getModelHelper($business)->toArray(array('imgNormal'));
            $companyThumb = $companyDetail['sPhotoUrl'];
            $this->data['iBusinessId'] = $business->getIdentity();
        }else{
            $companyName = $item -> company;
            $companyThumb = '';
        }

        $this->data['sCompany'] = $companyName;
        $this->data['sCompanyPhoto'] = $companyThumb;
        $this->data['sLocation'] = $item->location;
        $this->data['sDescription'] = $item->description;
    }
}
