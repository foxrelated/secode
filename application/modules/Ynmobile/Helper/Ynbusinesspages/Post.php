<?php

class Ynmobile_Helper_Ynbusinesspages_Post extends Ynmobile_Helper_Base{
		
	function getYnmobileApi(){
       return Engine_Api::_()->getApi('ynbusinesspages','ynmobile');
   	}	
	
	public function field_id(){
		$this->data['iPostId'] =  $this->entry->getIdentity();
	}
	
	public function field_listing(){
		$this->field_id();
		$this->field_type();
		$this->field_user();
		
		$post  	=  $this->entry;
		$topic  =   $post->getParentTopic();
		$business  = $post->getParentBusiness();
		$viewer =  $this->getViewer();
		
		$body = $post->body;
		$view =  Zend_Registry::get('Zend_View');
		$body = $view->BBCode($body, array('link_no_preparse' => true));
		
		if(strip_tags($body) == $body){
			$body = nl2br($body);
		}
		
		$canPost =  $canEdit  =  $canEditPost = 0;
		
		if( !$topic->closed && Engine_Api::_()->authorization()->isAllowed($business, null, 'comment') ) 
		{
			$canPost = 1;
		}
		
		if( Engine_Api::_()->authorization()->isAllowed($business, null, 'topic.edit') ) 
		{
			$canEdit = 1;
		}
		
		if ( $post->user_id == $viewer->getIdentity() || $business->getOwner()->getIdentity() == $viewer->getIdentity() || $canEdit)
		{
			$canEditPost = 1;
		}
		
		$this->data['iTopicId'] = $post->topic_id;
		$this->data['sCreationDate'] = Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->creation_date));
		$this->data['sModifiedDate'] = Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->modified_date)); 
		$this->data['sBody'] =  $body;
		$this->data['sPhotoUrl'] = '';
		$this->data['bCanPost'] =  $canPost;
		$this->data['bCanEditPost'] =  $canEditPost;
		$this->data['bCanDeletePost'] =  $canEditPost;
	}

	public function field_infos(){
		$this->field_listing();
}
	
}
