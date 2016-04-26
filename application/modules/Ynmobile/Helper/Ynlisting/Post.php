<?php

class Ynmobile_Helper_Ynlisting_Post extends Ynmobile_Helper_Base{
		
	function getYnmobileApi(){
       return Engine_Api::_()->getApi('ynlisting','ynmobile');
   	}	
	
	public function field_id(){
		$this->data['iPostId'] =  $this->entry->getIdentity();
	}
	
	public function field_listing(){
		// 'iPostId' => $post->getIdentity(),
		// 'iTopicId' => $topic->getIdentity(),
		// 'iUserId' => $post->user_id,
		// 'sUserName' => $user->getTitle(),
		// 'sUserPhoto' => $sUserImageUrl,
		// 'sSignature' =>  '',
		// 'sCreationDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->creation_date)),
		// 'sModifiedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->modified_date)),
		// 'sBody' => $body,
		// 'sPhotoUrl' => '',
		// 'bCanPost' => $canPost,
		// 'bCanEditPost' => $canEditPost,
		// 'bCanDeletePost' => $canEditPost,
		$this->field_id();
		$this->field_type();
		$this->field_user();
		
		$post  	=  $this->entry;
		$topic  =   $post->getParentTopic();
		$listing  = $post->getParentListing();
		$viewer =  $this->getViewer();
		
		$body = $post->body;
		$view =  Zend_Registry::get('Zend_View');
		$body = $view->BBCode($body, array('link_no_preparse' => true));
		
		if(strip_tags($body) == $body){
			$body = nl2br($body);
		}
		
		$canPost =  $canEdit  =  $canEditPost = 0;
		
		if( !$topic->closed && Engine_Api::_()->authorization()->isAllowed($listing, null, 'comment') ) 
		{
			$canPost = 1;
		}
		
		if( Engine_Api::_()->authorization()->isAllowed($listing, null, 'topic.edit') ) 
		{
			$canEdit = 1;
		}
		
		
		if ( $post->user_id == $viewer->getIdentity() || $listing->getOwner()->getIdentity() == $viewer->getIdentity() || $canEdit)
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
