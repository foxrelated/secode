<?php

class Ynmobile_Helper_Ynresume_Award extends Ynmobile_Helper_Base{

    public function field_id(){
        $this->data['iAwardId'] = $this->entry->getIdentity();
    }

    public function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_timestamp();
        $this->field_resume_photos();

        $item = $award = $this->entry;

        // get position and occupation
        if ($item->occupation_type && $item->occupation_id) {
            $occupation = Engine_Api::_()->ynresume()->getPosition2($item->occupation_type, $item->occupation_id);
            $this->data['sPosition'] = $occupation[0];
            $this->data['sOccupation'] = $occupation[1];
        } else {
            $this->data['sPosition'] = '';
            $this->data['sOccupation'] = '';
        }

        $month = array('Month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $this->data['sIssuer'] = ($item->issuer) ? $item->issuer : '';
        $this->data['sDateYear'] = ($item->date_year) ? $item->date_year : '';
        $this->data['sDateMonth'] = ($item->date_month) ? substr($month[$item->date_month], 0, 3) : '';
    }
}
