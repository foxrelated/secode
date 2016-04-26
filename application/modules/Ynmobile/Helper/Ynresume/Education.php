<?php

class Ynmobile_Helper_Ynresume_Education extends Ynmobile_Helper_Base{

    public function field_id(){
        $this->data['iEducationId'] = $this->entry->getIdentity();
    }

    public function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_timestamp();
        $this->field_desc();
        $this->field_resume_photos();

        $item = $education = $this->entry;

        $view = Zend_Registry::get('Zend_View');
        $this->data['sStudyField'] = $item->study_field;
        $this->data['iAttendFrom'] = $item->attend_from;
        $this->data['iAttendTo'] = $item->attend_to;
        $degree = Engine_Api::_()->getDbTable('degrees', 'ynresume')->getDegreeById($item->degree_id);
        $this->data['sDegree'] = ($degree) ? $degree->name : $view->translate('Unknown');
        $this->data['sGrade'] = $item->grade;
        $this->data['sActivity'] = $item->activity;
    }
}
