<?php

class Ynmobile_Helper_Ynlisting_Topic extends Ynmobile_Helper_Base{
	
	function getYnmobileApi(){
       return Engine_Api::_()->getApi('ynlisting','ynmobile');
   	}	
	
	public function field_id(){
		$this->data['iTopicId'] =  $this->entry->getIdentity();
	}
	
	public function field_listing(){
		$this->field_id();
		$this->field_type();
		$this->field_user();
		$this->field_title();
		$this->field_desc();
		
		$topic  =  $this->entry;
		$listing  = $topic->getParentListing();
		$viewer =  $this->getViewer();
		
		$this->data['aParent'] =  array(
			'id'=>$listing->getIdentity(),
			'type'=>$listing->getType(),
			'title'=>$listing->getTitle(),
		);
		
		$lastpost = $topic->getLastPost();
		$lastposter = $topic->getLastPoster();
		$lastPostedDate = strtotime($lastpost->creation_date);
		
		$this->data['iViewCount'] = intval($topic->view_count);
		$this->data['iReplyCount']  =intval($topic->post_count-1);
		
		$this->data['aLastPoster']  = Ynmobile_AppMeta::_export_one($lastposter, array('simple_array'));
		
		$this->data['sLastPostedDate']  =Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($lastPostedDate);
		$this->data['sModifiedDate']  =Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($modifiedDate);

		$this->data['bSticky']  = $topic->sticky;
		$this->data['iReplyCount']  =intval($topic->post_count-1);
		
		$canPost = 0;
		$isWatching = 0;
		$canDelete = $canEdit = $topic->canEdit($viewer)?1:0;
		
		if( !$topic->closed && Engine_Api::_()->authorization()->isAllowed($listing, null, 'comment') ) 
		{
			$canPost = 1;
		}
		
		if( $viewer->getIdentity() ) {
			$topicWatchesTable = $this->getWorkingTable('topicWatches','ynlistings');
			
			$isWatching = $topicWatchesTable
			->select()
			->from($topicWatchesTable->info('name'), 'watch')
			->where('resource_id = ?', $listing->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('user_id = ?', $viewer->getIdentity())
			->limit(1)
			->query()
			->fetchColumn(0)
			;
			if( false === $isWatching ) {
				$isWatching = 0;
			} else {
				$isWatching = 1;
			}
		}
		
		$this->data['bCanEdit']  = $canEdit;
		$this->data['bCanDelete'] =  $canDelete;
		$this->data['bCanPost'] = $canPost;
		$this->data['bIsWatching'] =  $isWatching;
		$this->data['bIsSticky'] =  $topic->sticky?1:0;
		$this->data['bIsClosed'] = ($topic->closed) ? 1:0;
	}

	public function field_infos(){
		$this->field_listing();
	}
}
