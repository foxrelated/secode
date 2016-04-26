<?php

/**
 * @author Nam Nguyen <namnv@younetco.com>
 * @since 4.10 
 */
class Ynmobile_Helper_Ynlisting_Listing extends Ynmobile_Helper_Base{
	
	public function getYnmobileApi(){
		return Engine_Api::_()->getApi('ynlisting', 'ynmobile');
	}
	public function field_id(){
		$this->data['iListingId']  = $this->entry->getIdentity();
		
 	}
	
	public function field_listing(){
		$this->field_id();
		$this->field_type();
		$this->field_imgIcon();
		$this->field_imgFull();
		$this->field_imgNormal();
		$this->field_href();
		$this->field_title();
		$this->field_timestamp();
		
		$this->field_stats();
		$this->field_user();
		
		$listing =  $this->entry;
		
		$this->data['iCategoryId'] = 0;
		$this->data['sCategoryTitle'] =  "";
		
		if($category = $listing->getCategory()){
			$this->data['iCategoryId'] = $category->getIdentity();
			$this->data['sCategoryTitle'] =  $category->getTitle();
		}
		
		$view  = Zend_Registry::get('Zend_View');
		
		$this->data['bIsNew'] = $listing->isNew()?1:0;
		$this->data['bCanView'] = $listing->isViewable()?1:0;
		$this->data['bCanEdit'] = $listing->isEditable()?1:0;
		$this->data['bCanDelete'] = $listing->isDeletable()?1:0;
		$this->data['bCanUploadPhotos'] =  $listing->canUploadPhotos()?1:0;
		$this->data['bCanUploadVideos']  = $listing->canUploadVideos()?1:0;
		$this->data['bCanCreateTopic'] =  $listing->canDiscuss()?1:0;
		$this->data['bCanShare'] =  $listing->canShare()?1:0;
		$this->data['bCanPrint'] =  $listing->canPrint()?1:0;
		$this->data['bCanLike'] =  $listing->canLike()?1:0;
		$this->data['bCanComment'] =  $listing->canLike()?1:0;
		
		$this->data['bIsExpired'] =  $listing->expired()?1:0;
		$this->data['iTotalRate'] = $listing->ratingCount();
		$this->data['fRateValue'] =  $listing->getRating();
		$this->data['bIsRated'] = $bIsRated =   $listing->checkRated()?1:0;
		$this->data['sMediaType'] =  $listing->getMediaType();
		$this->data['sApprovedStatus'] = $listing->approved_status;
		$this->data['sStatus'] =  $listing->status;
		$this->data['bIsFeatured'] =  $listing->featured?1:0;
		$this->data['sCurrencySymbol']=  $listing->currency;
		$this->data['bIsHighlight'] =  $listing->highlight?1:0;
		$this->data['sLong'] =  $listing->longitude;
		$this->data['sLat'] =  $listing->latitude;
		$this->data['sShortDesc'] =  $listing->short_description;
		$this->data['sDesc'] =  $listing->description;
		$this->data['sAboutUs'] = $listing->about_us;
		$this->data['sTheme'] =  $listing->theme;
		$this->data['sCurrency'] =  $listing->currency;
		$this->data['fPrice'] =  $listing->price;
		$this->data['iTotalView'] = $listing->view_count;
		$this->data['sLocation'] = $listing->location;
		$this->data['sPrice'] = "";
		$this->data['iEndDate'] = $listing->end_date?strtotime($listing->end_date):0;
		$this->data['sEndDate'] = $listing->end_date?date("d M Y", strtotime($listing->end_date)):0;
		$this->data['iApprovedDate'] = $listing->approved_date?strtotime($listing->approved_date):0;
		$this->data['sApprovedDate'] = $listing->approved_date?date("d M Y", strtotime($listing->approved_date)):0;
		$this->data['bIsFollowing'] = 0;
		$this->data['bCanFollow'] = 0;
		$this->data['bCanReview'] =  0;
		
		$owner = $this->entry->getOwner();

		$this->data['sSellerTitle'] =  $owner->getTitle();
		$this->data['sSellerEmail'] = ($owner->getIdentity())? $owner->email : '';

		if($listing->price){
			$this->data['sPrice'] =  $view -> locale() -> toCurrency($listing->price, $listing->currency);;	
		}
		
		$viewer  = $this->getViewer();
		
		if($viewer)
		{	
			$auth =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
			
			if(!$viewer->isSelf($owner)){
				
				$canReview = $auth ->setAuthParams('ynlistings_listing', null, 'rate')->checkRequire();
				$canReport = $auth ->setAuthParams('ynlistings_listing', null, 'report')->checkRequire();
				
				$this->data['bCanReview'] = ($canReview && !$bIsRated)?1:0;	
				$this->data['bCanReport'] = $canReport?1:0;	
			}
			
			
			
			$tableFollow = Engine_Api::_()->getItemTable('ynlistings_follow');
			$row = $tableFollow -> getRow($viewer -> getIdentity(), $listing->user_id);
			if($row && $row->status == 1){
				$this->data['bIsFollowing'] = 1;
			}else{
				$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
				$this->data['bCanFollow']  = $permissionsTable->getAllowed('ynlistings_listing', $viewer->level_id, 'follow')?1:0;	
			}
		}
 	}


	public function field_infos(){
		$this->field_listing();
		
		$view = Zend_Registry::get('Zend_View');
		
		$listing =  $this->entry;
		$this->data['sSpecification'] ="";
		$this->data['iTotalListing'] =  Engine_Api::_()->getItemTable('ynlistings_listing')->getTotalListingsByUser($this->entry->user_id);
		
		
		$view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		$fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($listing);	
		if($fieldStructure){
			$structure =  $view -> fieldValueLoop($listing, $fieldStructure);
			$this->data['sSpecification'] =  $structure;
		}
		
		$this->data['aPhoto'] = array();
		$this->data['aVideo'] =  array();
		
		if(1){
			$album = $listing -> getSingletonAlbum();
			$photos = $album -> getCollectiblesPaginator();
			
			$this->data['aPhoto'] =  Ynmobile_AppMeta::_exports_by_page($photos, 0, 1000, array('listing'));
		}
		
		if(Engine_Api::_()->ynlistings()->checkYouNetPlugin('video') || Engine_Api::_()->ynlistings()->checkYouNetPlugin('ynvideo')){
			$tableMappings = Engine_Api::_()->getItemTable('ynlistings_mapping');
			$videos = $tableMappings -> getVideosPaginator(array(
				'listing_id'=>$listing->getIdentity(),
			));
			$this->data['aVideo'] =  Ynmobile_AppMeta::_exports_by_page($videos, 0, 1000, array('detail'));
		}
		
		$this->field_totalAlbums();
		$this->field_totalVideos();
		$this->field_totalTopics();
		

	}

	public function field_totalTopics(){
		$this->data['iTotalTopics'] = Engine_Api::_() -> getItemTable('ynlistings_topic') -> getTopicsPaginator(array('listing_id'=>$this->entry->getIdentity()))->getTotalItemCount();	
	}

	public function field_totalVideos(){
		$this->data['iTotalVideos'] = Engine_Api::_()->getItemTable('ynlistings_mapping') -> getWidgetVideosPaginator(array('listing_id'=>$this->entry->getIdentity()))->getTotalItemCount();
	}

	public function field_totalAlbums(){
		$this->data['iTotalAlbums'] = Engine_Api::_() -> getItemTable('ynlistings_album') -> getAlbumsPaginator(array(
			'close'=>0,
			
			'listing_id'=>$this->entry->getIdentity()
		))->getTotalItemCount();
	}

	public function field_edit(){
		$this->field_infos();
		$this->field_auth();
	}
}
