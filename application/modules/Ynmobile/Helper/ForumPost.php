<?php

class Ynmobile_Helper_ForumPost extends Ynmobile_Helper_Base{
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('forum','ynmobile');
    }
    
    function field_id(){
        $this->data['iPostId'] =  $this->entry->getIdentity();
    }
    
    public function field_as_attachment(){
        $this->data['iId'] = $this->entry->getIdentity();
        $this->field_id();
        $this->field_type();
        $this->field_desc();
        $this->field_imgNormal();
        $this->field_imgFull();
        $this->field_title();
        $this->field_href();

        $this->data['iId'] =  $this->entry->topic_id;
    	$this->data['sModelType'] =  'forum_topic';
    	$this->data['iParentId'] = $this->entry->topic_id; 
    	$this->data['iTopicId'] =  $this->entry->topic_id;
    }
}
