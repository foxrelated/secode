<?php

class Ynmobile_Helper_Ynresume_Skill extends Ynmobile_Helper_Base{

    public function field_id(){
        $this->data['iSkillId'] = $this->entry->getIdentity();
    }

    public function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_timestamp();
        $item = $skill = $this->entry;
    }
}
