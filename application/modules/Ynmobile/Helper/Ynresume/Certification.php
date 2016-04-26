<?php

class Ynmobile_Helper_Ynresume_Certification extends Ynmobile_Helper_Base{

    public function field_id(){
        $this->data['iCertificationId'] = $this->entry->getIdentity();
    }

    public function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_timestamp();
        $this->field_resume_photos();
        $this->field_resume_period();

        $item = $certification = $this->entry;
        $this->data['sTitle'] = $item->name;
        $this->data['sAuthority'] = $item->authority;
        $this->data['sUrl'] = $item->url;
        $this->data['sLicense'] = $item->license_number;
    }
}
