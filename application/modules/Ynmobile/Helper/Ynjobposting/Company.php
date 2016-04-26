<?php

/**
 * @since 4.10
 * @author Nam Nguyen <namnv@younetco.com>
 */
class Ynmobile_Helper_Ynjobposting_Company extends Ynmobile_Helper_Base{
	function field_id(){
		$this->data['iCompanyId'] = $this->entry->getIdentity();
	}
	
	function field_linkedObject(){
		$this->field_id();
		$this->field_type();
		$this->field_title();
		$this->field_imgIcon();
		$this->data['sLat'] =  $this->entry->latitude;
		$this->data['sLong'] =  $this->entry->longitude;
	}
	
	function field_stats(){
		parent::field_stats();
	}
	
	function field_as_attachment(){
		$this->data['iId'] =  $this->entry->getIdentity();
 		$this->field_infos();
	}
	
	public function field_cover(){
		$coverPhotoUrl = "";
		if ($this->entry->cover_photo)
		{
			$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->entry->cover_photo)->current();
			$coverPhotoUrl = $this->finalizeUrl($coverFile->map());
		}else{
			$coverPhotoUrl = $this->getNoImg('cover');
		}
        $this->data['coverImg'] = $coverPhotoUrl;
	}
	
	function field_imgIcon(){
        $this->_field_img('thumb.profile','imgIcon');
    }
	
	function field_imgNormal(){
        $this->_field_img('thumb.profile','sPhotoUrl'); 
    }
	
	/**
	 * iCompanyId,
		sModelType,
		sTitle,
		sImage,
		aIndustry,
		sLocation,
		iTotalJobs
	 */
	function field_listing(){
		$this->field_id();
		$this->field_type();
		$this->field_title();
		$this->field_stats();
		$this->field_imgFull();
		// $this->field_imgIcon();
		$this->field_imgNormal();
		
		
		$company = $this->entry;
		
		$this->data['sLocation'] = $this->entry->location;
		$this->data['fLatitude'] = $this->entry->latitude;
		$this->data['fLongitude'] = $this->entry->longitude;
		
		$this->data['aIndustry'] = array();
		
		$industries  = $company->getIndustries();
		foreach($industries as $item){
			$this->data['aIndustry'][] =  array(
				'id'=>$item->getIdentity(),
				'type'=>$item->getType(),
				'title'=>$item->getTitle(),
			);
		}
		
		$this->data['sStatus'] = $company->status;
		
		$this->data['iTotalDraftJobs'] = $this->countJobsWithStatus('draft');
		$this->data['iTotalPendingJobs'] = $this->countJobsWithStatus('pending');
		$this->data['iTotalPublishedJobs'] = $this->countJobsWithStatus('published');
		$this->data['iTotalEndedJobs'] = $this->countJobsWithStatus('ended');
		$this->data['iTotalExpiredJobs'] = $this->countJobsWithStatus('expired');
		$this->data['iTotalDeniedJobs'] = $this->countJobsWithStatus('denied');
		$this->data['bCanClose'] =  ($company->isClosable() && $company->status == 'published')?1:0;
		$this->data['bCanPublish'] =  ($company->isClosable() && $company->status == 'closed')?1:0;
		$this->data['bCanEdit'] =  $company->isEditable();
		$this->data['bCanDelete'] =  $company->isDeletable();
		$this->data['bCanView'] =  $company->isViewable();
		$this->data['bCanComment'] =  $company->isCommentable();
		$this->data['sDescription'] =  $company->description;
		$this->data['sWebsite'] =  $company->website;
		$this->data['sSize'] =  $company->getSize();
		$this->data['sSizeFrom'] =  $company->from_employee;
		$this->data['sSizeTo'] =  $company->to_employee;
		
		$this->field_user();
	}

	public function field_infos(){
		$this->field_listing();
		$this->field_cover();
		
		$company = $this->entry;
		
		$this->data['aContact']= array(
			'name'=>$company->contact_name,
			'email'=>$company->contact_email,
			'phone'=>$company->contact_phone,
			'fax'=>$company->contact_fax,
		);
		
		
		
		$tableCompanyInfo = Engine_Api::_() -> getDbTable('companyinfos', 'ynjobposting');
		$infos  = $tableCompanyInfo->getRowInfoByCompanyId($company->getIdentity());
		$this->data['aInfo'] =  array();
		
		foreach($infos as $info){
			$this->data['aInfo'][] =  array(
				'id'=>$info->getIdentity(),
				'header'=>$info->header,
				'content'=>$info->content,
				'iCompanyId'=>$info->company_id,
			);
		}
		
		$view = Zend_Registry::get('Zend_View');
		$viewer =  $this->getViewer();
		
		
		$view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		$fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($company);	
		if($fieldStructure){
			$structure =  $view -> fieldValueLoop($company, $fieldStructure);
			$this->data['fieldStructure'] =  $structure;
		}


		if($viewer){
			
			$isSelf = $viewer -> isSelf($company -> getOwner());
			$follow =  Engine_Api::_()->getDbtable('follows','ynjobposting')->getFollowBy($company->getIdentity(), $viewer->getIdentity());
			$this->data['bIsFollowing'] =( $follow && $follow->active)?1:0;
			$this->data['bCanEditSubmissionForm'] = $isSelf?1:0;
			$this->data['bCanViewApplications'] = $isSelf?1:0;
			$this->data['bCanManagePostedJobs'] = $isSelf?1:0;
		}		
	}

	function field_edit(){
		$this->field_infos();
		$this->field_auth();
		$this->data['sAuthView'] =  $this->data['auth']['view'];
		
		$tableIndustry = Engine_Api::_()->getItemTable('ynjobposting_industry');
	    $tableIndustryMap = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
		$main_industry = $tableIndustryMap -> getMainIndustryByCompanyId($this->entry -> getIdentity());
		$this->data['iIndustryId'] = $main_industry->industry_id;
	}
		
	function countJobsWithStatus($status){
    	$jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
    	$select = $jobTbl -> select() -> where("company_id = ?", $this->entry->getIdentity());
    	if (isset($status) && 
    	in_array($status, array('draft', 'pending', 'denied', 'published', 'ended', 'expired', 'deleted')))
    	{
    		$select->where("status = ?", $status);
    	}
    	
		return Zend_Paginator::factory($select)->getTotalItemCount();
	}
}