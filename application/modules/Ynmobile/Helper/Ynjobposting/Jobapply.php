<?php

class Ynmobile_Helper_Ynjobposting_Jobapply extends Ynmobile_Helper_Base{
	
	public function field_id(){
		$this->data['iApplicationId'] =  $this->getIdentity();
	}
	
	public function field_user(){
		$user = $this->entry->getOwner();
        
        $fields =  array('id','type','imgProfile','title');
        
        $helper = Ynmobile_AppMeta::getInstance()
            ->getModelHelper($user);
            
        $user  =  $helper ->toArray($fields);
		
		$this->data['user2'] =  $user;
		$this->data['user'] = array(
			'id'=>$user['iUserId'],
			'title'=>$user['sTitle'],
			'type'=>$user['sModelType'],
			'img'=>$user['sProfilePhotoUrl'],
		);
        
	}
	
	public function field_listing(){
		$this->field_id();
		$this->field_type();		
		$this->field_timestamp();
		$this->field_user();
		
		$this->data['sStatus'] =  $this->entry->status;
		$this->data['sVideoUrl'] =  $this->entry->video_link;
		$this->data['iVideoId'] = $this->entry->video_id;
		
		$this->data['bCanReject'] = ($this->entry->status == 'pending')?1:0;
		$this->data['bCanPass'] = ($this->entry->status == 'pending')?1:0;
		$this->data['bCanDelete'] = 1;
		
		$hasVideo = 0;
		if($this->entry->video_link){
			$hasVideo = 1;
		}else if($this->entry->video_id){
			$video = Engine_Api::_()->getItem('video', $this->entry->video_id);
			if($video){
				$hasVideo =  1;
			}
		}
		
		$this->data['bCanSendMessage'] = 1;
		$this->data['bCanViewVideoResume'] =  $hasVideo;
		
		$job = $this->entry->getJob();
		
		if($job){
			$company =$job->getCompany();
			if($company){
				$this->data['aCompany'] = Ynmobile_AppMeta::_export_one($company, array('listing'));	
			}
			$this->data['aJob'] = Ynmobile_AppMeta::_export_one($job, array('listing'));
		}
	}
	
	public function field_infos(){
		$this->field_listing();
		$textFields = $this -> entry -> getTextFieldValue();
		
		$this->data['aTextFields'] = array();
		
		$photoField = $this -> entry -> getPhotoFieldValue();
		$this->data['sPhotoUrl'] = "";
		
		if(!is_null($photoField)){
			$file = Engine_Api::_()->getItem('storage_file', $photoField->value);
			if($file){
				$this->data['sPhotoUrl'] = $this->finalizeUrl($file->map());
			}
		}
		
		$this->data['aNote'] = array();
		foreach($this->entry -> getNote() as $note){
			$owner = Engine_Api::_()->user()->getUser($note->user_id);
			$this->data['aNote'][]  = array(
				'iNoteId'=>$note->applynote_id,
				'iTimestamp'=>strtotime($note->creation_date),
				'sContent'=>$note->content,
				'user'=> Ynmobile_AppMeta::_export_one($owner, array('simple_array')),
			);
		}
		
		$fields =  $this->entry->getFieldValue();
		$optionTbl =  Engine_Api::_()->fields()->getTable('','options');
		foreach($fields as $field){
			$value =  $field->value;
			
			if($field->type == 'radio'){
				$ids = array(intval($value));
				$value =  $this->getApplyOptionLabels($ids);
			}else if ($field->type == 'checkbox'){
				$ids =  (array)unserialize($value);
				$value =  $this->getApplyOptionLabels($ids);
			}else if ($field->type == 'file'){
				continue;
			}
			
			$this->data['aFields'][] = array(
				'id'=>$field->field_id,
				'type'=>$field->type,
				'label'=>str_replace("Candidate ", "", $field->label),
				'value'=>$value,
				'desc'=>$field->description,
			);
		}
	}
	
	/**
	 * @return string
	 */
	protected function getApplyOptionLabels($ids){
		$db =  Engine_Db_Table::getDefaultAdapter();
		$prefix = Engine_Db_Table::getTablePrefix();

		$table =  'engine4_ynjobposting_submission_fields_options';
		
		$ids[] = -1;
		$ids = implode(',', $ids);

		$labels  =  $db->fetchCol("select `label` from {$table} where option_id IN ({$ids})");
		
		return implode(', ', $labels);
	}
	
	public function field_edit(){
		$this->field_infos();
	}
	
}
