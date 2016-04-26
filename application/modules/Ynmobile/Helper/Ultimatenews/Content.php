<?php

/**
 * @author Nam Nguyen <namnv@younetco.com>
 * @since 4.10 
 */
class Ynmobile_Helper_Ultimatenews_Content extends Ynmobile_Helper_Base{
	
	
	function getYnmobileApi(){
        return Engine_Api::_()->getApi('ultimatenews','ynmobile');
    }
	
	public function field_id(){
		$this->data['iArticleId'] =  $this->entry->getIdentity();
	}
	
	function field_canComment(){
		
		$bCanComment  =  Engine_Api::_()->authorization()->isAllowed('ultimatenews',null, 'comment')?1:0;
		// $bCanComment  = (Engine_Api::_() -> authorization() -> isAllowed($this->entry, null, 'comment')) ? 1 : 0;
        $this->data['bCanComment'] =$bCanComment;
        $this->data['bCanLike'] =$bCanComment;
    }
	
	public function field_listing(){
		$this->field_id();
		$this->field_title();
		$this->field_desc();
		$this->field_type();
		$this->field_href();
// 		
		$content =  $this->entry;
		$this->field_canComment();
		// $this->field_canEdit();
		// $this->field_canDelete();
		// $this->field_canView();
		$this->field_imgIcon();
		$this->field_imgNormal();
		$this->field_imgFull();
		
		$this->data['iTimeStamp'] = strtotime($this->entry->posted_date);  

		// $this->field_user();
		// $this->field_timestamp();
		// $this->field_stats();
	}
	
	
	public function field_infos(){
		$this->field_listing();		
		$entry  = $this->entry;
		$this->data['sContent'] = $entry->content;
		$this->data['sImage'] =  $entry->image;
		$this->data['sLinkDetail'] =  $entry->link_detail;
		$this->data['sAuthor'] =  $entry->author;
		$this->data['pubDate'] =  $entry->pubDate;
		$this->data['bIsActive'] =  $entry->is_active;
		
		$this->data['bIsFeatured'] =  $entry->is_featured;
		$this->data['iTotalView']  = $entry->count_view;
		$this->field_liked();
		$this->field_likes();
		$this->field_totalComment();
		$this->field_totalLike();
		
		
		
		if($this->entry->category_id){
			if($feed =  $this->getYnmobileApi()->getFeedById($this->entry->category_id)){
				// $this->data['iFeedId'] =  $feed->getIdentity();
				// $this->data['sFeedTitle'] = $feed->getTitle();
				$this->data['aFeed'] =  Ynmobile_AppMeta::_export_one($feed, $fields =  array('infos'));
			}
		}
	}
}
