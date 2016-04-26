<?php

class Ynmobile_Helper_Ynresume_Project extends Ynmobile_Helper_Base{

    public function field_id(){
        $this->data['iProjectId'] = $this->entry->getIdentity();
    }

    public function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_resume_photos();
        $this->field_resume_period();

        $item = $project = $this->entry;
        $this->data['sTitle'] = $project->name;
        $this->data['sUrl'] = $project->url;

        $occupationTitle = '';
        if ($item -> occupation_type && $item -> occupation_id) {
            $occupation = Engine_Api::_()->getItem($item -> occupation_type, $item -> occupation_id);
            if (!is_null($occupation)) {
                $occupationTitle = $occupation->title;
            }
        }

        // get project memebers
        $members = $item -> getMemberObjects();
        $members_arr = array();
        foreach ($members as $member) {
            if ($member->user_id > 0) {
                $user = Engine_Api::_()->getItem('user', $member->user_id);
                $members_arr[] = Ynmobile_AppMeta::_export_one($user, array('simple_array'));
            } else {
                $members_arr[] = array(
                    'id'=>0,
                    'title'=>$member->name
                );
            }
        }

        $this->data['sOccupation'] = $occupationTitle;
        $this->data['sDescription'] = $project->description;
        $this->data['aMembers'] = $members_arr;
    }
}
