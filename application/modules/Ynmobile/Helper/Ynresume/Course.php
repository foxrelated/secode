<?php

class Ynmobile_Helper_Ynresume_Course extends Ynmobile_Helper_Base{

    public function field_id(){
        $this->data['iCourseId'] = $this->entry->getIdentity();
    }

    public function field_listing(){
        $this->field_id();
        $this->field_type();

        $item = $course = $this->entry;

        $this->data['sTitle'] = $item->name;
        $this->data['sNumber'] = $item->number;

        // Associated item will be now grouped from resume helper
        // get title of Associated item
//        if ($item->associated_id && $item->associated_type){
//            $associatedType = $item->associated_type;
//            $associatedId = $item->associated_id;
//            $associatedItem = Engine_Api::_()->getItem($associatedType, $associatedId);
//            $this->data['sAssociate'] = $associatedItem->getTitle();
//        } else {
//            $this->data['sAssociate'] = Zend_Registry::get('Zend_View') -> translate("Others");
//        }
    }
}
