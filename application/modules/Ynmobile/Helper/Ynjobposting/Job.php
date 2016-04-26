<?php

/**
 * @author Nam Nguyen <namnv@younetco.com>
 * @since 4.10 
 */
class Ynmobile_Helper_Ynjobposting_Job extends Ynmobile_Helper_Base{
	
	/**
	 "job_id": 1,
      "company_id": 1,
      "industry_id": 2,
      "user_id": 3,
      "title": "[TOÀN QUỐC] Tuyển dụng Giao Nhận Lắp Đặt điện máy",
      "description": "<ul>\r\n<li>Mang sản phẩm đến tận nh&agrave; cho kh&aacute;ch h&agrave;ng, hỗ trợ th&ocirc;ng tin, hướng dẫn c&aacute;ch sử dụng sản phẩm cho kh&aacute;ch h&agrave;ng.</li>\r\n<li>Hỗ trợ kh&aacute;ch h&agrave;ng thủ tục thanh to&aacute;n v&agrave; thu tiền h&agrave;ng ho&aacute;.</li>\r\n<li>Tư vấn linh kiện phục vụ việc lắp đặt c&aacute;c thiết bị Điện Tử, Điện Lạnh cho kh&aacute;ch h&agrave;ng.</li>\r\n<li>Lắp đặt, bảo tr&igrave; c&aacute;c thiết bị Điện Tử, Điện Lạnh theo y&ecirc;u cầu kh&aacute;ch h&agrave;ng.</li>\r\n<li>Được Đ&Agrave;O TẠO KIẾN THỨC ĐIỆN TỬ v&agrave; KỸ NĂNG LẮP ĐẶT CHUY&Ecirc;N NGHIỆP trước khi l&agrave;m việc.</li>\r\n</ul>",
      "skill_experience": "<ul>\r\n<li>NAM, tuổi từ 20 đến 40, sức khỏe tốt.</li>\r\n<li>Tốt nghiệp trung cấp nghề Điện, Điện tử, Điện lạnh.</li>\r\n<li>C&oacute; khả năng xử l&yacute; t&igrave;nh huống nhanh.</li>\r\n<li>C&oacute; xe m&aacute;y v&agrave; hiểu r&otilde; về giao th&ocirc;ng khu vực m&igrave;nh ứng tuyển</li>\r\n<li>Trung thực, tận t&acirc;m cẩn thận, y&ecirc;u th&iacute;ch c&ocirc;ng việc phục vụ kh&aacute;ch h&agrave;ng.</li>\r\n<li>C&oacute; t&iacute;nh kỷ luật, nghi&ecirc;m t&uacute;c, tận tụy trong c&ocirc;ng việc, tinh thần tr&aacute;ch nhiệm cao.</li>\r\n</ul>",
      "level": "entry_level",
      "type": "full_time",
      "language_prefer": "",
      "education_prefer": "highschool",
      "salary_from": "200.00",
      "salary_to": "300.00",
      "salary_currency": "USD",
      "working_place": "Ho Chi Minh, Vietnam",
      "longitude": "106.67629199999999",
      "latitude": "10.746903",
      "working_time": "",
      "creation_date": "2015-02-25 10:37:09",
      "view_count": 0,
      "candidate_count": 0,
      "status": "published",
      "expiration_date": "2015-03-27 10:37:12",
      "approved_date": "2015-02-25 10:37:12",
      "number_day": 30,
      "featured": 0,
      "share_count": 0,
      "click_count": 0,
      "company_name": "Thế Giới Di Động"
	 */
	 
	 
	public function field_stats(){
		parent::field_stats();
		
		$job =  $this->entry;
		
		$this->data['iTotalCandidate'] =  $job->candidate_count;
		
		$this->data['iTotalShare'] =  intval($job->share_count);
		$this->data['iTotalClick']  = intval($job->click_count);
		$this->data['iTotalView'] =  intval($job->view_count);
		$this->data['sDescription'] =  $job->description;
		$this->data['sSkillExperience'] =  $job->skill_experience;
		$this->data['iLevel'] =  $job->level;
		$this->data['sLevel'] = $job->getLevel();
		$this->data['iJobType'] =  $job->type;
 		$this->data['sJobType'] =  $job->getJobType();
		$this->data['sLanguagePreferer'] = $job->language_prefer;
		$this->data['sEducationPrefer'] = $job->education_prefer;
		
		$this->data['sSalaryCurrency'] = $job->salary_currency;
		$this->data['sLocation'] = $job->working_place;
		$this->data['sLong'] = $job->longitude;
		$this->data['sLat'] = $job->latitude;
		$this->data['sWorkingTime']  = $job-> working_time;
		$this->data['iIndustryId'] = $job->industry_id;
		$this->data['sStatus'] =  $job->status;
 		$this->data['sExpirationDate'] =  $job->expiration_date;
		$this->data['sApprovedDate'] = $job->approved_date;
		$this->data['iNumberDay'] = intval($job->number_day);
		$this->data['bIsNegotiable']  = (is_null($job->salary_from) && is_null($job->salary_to ))?1:0;
		
		$this->data['fSalaryFrom'] = $job->salary_from?$job->salary_from:0.0;
		$this->data['fSalaryTo'] = $job->salary_to?$job->salary_to:0.0;
		
		$this->data['sSalaryTo'] = "";
		$this->data['sSalaryFrom'] = "";
		$this->data['bIsExpired']='';
		$this->data['sExpiredTimeStamp']= strtotime($job->expiration_date);;;
		$this->data['sExpiredTimeFormatted'] =  date("d M", strtotime($job->expiration_date));
		
		
		$view = Zend_Registry::get('Zend_View');
		$this->data['sSalary'] = $job->getSalary();
		
		
		if (!is_null($job->salary_from))
		{
			$this->data['sSalaryFrom'] = $view -> locale() -> toCurrency($job->salary_from, $job->salary_currency);
		}
    	if (!is_null($job->salary_to))
		{
			$this->data['sSalaryTo'] = $view -> locale() -> toCurrency($job->salary_to, $job->salary_currency);
		}
		
		$company  = $job->getCompany();
		$this->data['oCompany'] =  Ynmobile_AppMeta::_export_one($company, array('linkedObject'));
		
		$this->data['bIsPublished'] =$bIsPublished =   $job->isPublished()?1:0;
		$this->data['bHasApplied'] = $bHasApplied = $job->hasApplied();
		$this->data['bHasSaved'] =  $bIsSaved =  $job->hasSaved()?1:0;
		$this->data['bCanEdit'] =  $job->isEditable()?1:0;
		$this->data['bCanDelete'] =  $job->isDeletable()?1:0;
		$this->data['bCanView'] =  $job->isViewable()?1:0;
		$this->data['bCanFeature'] = $job->isFeaturable()?1:0;
		$this->data['bIsFeatured'] =  $job->isFeatured()?1:0;
		$this->data['bCanEnd']  = 0;
		
		$bIsOwner =  $job->isOwner();
		$viewer  = Engine_Api::_()->user()->getViewer();
		
		$submissionForm = $job->getSubmissionForm();
		
		
		if($viewer){
			$this->data['bCanExpire'] =  0;
			$this->data['bCanViewApplication']  = $viewer->isSelf($company->getOwner())?1:0;
			
			$industries  = $company->getIndustries();
			foreach($industries as $item){
				$this->data['aIndustry'][] =  array(
					'id'=>$item->getIdentity(),
					'type'=>$item->getType(),
					'title'=>$item->getTitle(),
				);
			}

			if( $bIsPublished){
				
				$auth =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
				
				$canApply = $auth ->setAuthParams('ynjobposting_job', null, 'apply')->checkRequire();
				$canShare = $auth->setAuthParams('ynjobposting_job', null, 'share')->checkRequire();
				$canReport = $auth->setAuthParams('ynjobposting_job', null, 'report')->checkRequire();
				$canPrint = $auth->setAuthParams('ynjobposting_job', null, 'print')->checkRequire();
				
				$this->data['bCanApply'] = (!$job->isOwner() && !$bHasApplied && $canApply && $submissionForm)?1:0;
				$this->data['bCanShare'] =  $canShare;
				$this->data['bCanReport'] =  $canReport;
				$this->data['bCanPrint'] = $canPrint;
				$this->data['bCanSave'] = !$job->isOwner() && $canApply && !$job->hasApplied() && !$job->hasSaved();
				$this->data['bCanPromote'] = 1;
				$this->data['bCanEnd'] =  $job->isEndable();	
				$this->data['bSubmissionForm'] =  $submissionForm?1:0;
				
			}

			 
		}
	}

	public function field_edit(){
		//populate tags
        $tagStr = '';
        foreach ($this->entry->tags()->getTagMaps() as $tagMap) {
            $tag = $tagMap -> getTag();
            if (!isset($tag -> text))
                continue;
            if ('' !== $tagStr)
                $tagStr .= ', ';
            $tagStr .= $tag -> text;
        }
        $this->data['sTags'] =  $tagStr;
		$this->field_auth();
	}	
	
	public function field_auth(){
        $entry =  $this->entry;
        $auth = Engine_Api::_() -> authorization() -> context;
		$sViewPrivacy =  'everyone';
		$sCommentPrivacy =  'everyone';
		
        $roles = array(
            'owner', 'owner_member', 'network', 'registered', 'everyone'
        );
		
        foreach ($roles as $role)
        {
            if (1 == $auth -> isAllowed($entry, $role, 'view'))
            {
                $sViewPrivacy = $role;
            }
            if (1 == $auth -> isAllowed($entry, $role, 'comment'))
            {
                $sCommentPrivacy = $role;
            }
        }
        $this->data['sAuthView'] = $sViewPrivacy;
        $this->data['sAuthComment'] = $sCommentPrivacy;
    }
	public function field_id(){
		$this->data['iJobId'] = $this->entry->getIdentity();
	}
	
	public function field_apply(){
		if(!isset($this->entry->applied_date)){
			return ;
		} 
		
		$this->data['sAppliedTimeStamp']  =  strtotime($this->entry->applied_date);;
		$this->data['sAppliedTimeFormatted'] =  date("d M Y", strtotime($this->entry->applied_date));
	}
	
	
	
	public function field_listing(){
		$this->field_id();
		$this->field_type();
		$this->field_title();
		$this->field_href();
		$this->field_stats();
		
		$this->field_canShare();
		$this->field_canView();
		$this->field_user();
	}
	
	
	public function field_infos(){
		$this->field_listing();
		
		$job  = $this->entry;
		
		$this->data['aInfo'] = array();
		
		foreach($job->getInfo() as $info){
			$this->data['aInfo'][]=  array(
				'iInfoId'=>$info->jobinfo_id,
				'header'=>$info->header,
				'content'=>$info->content,
				'iJobId' =>$info->job_id,
			);
		}


		


		$this->data['sSkillExperience'] = $job->skill_experience;

	}
	
	public function field_options(){
		$this->data['id'] = $this->entry->getIdentity();
		$this->data['title'] =  $this->entry->getTitle();		
	}
}
