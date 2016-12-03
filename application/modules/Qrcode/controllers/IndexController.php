<?php

class Qrcode_IndexController extends Core_Controller_Action_Standard
{

	public function init()
	{
		$id = $this->_getParam('id', null);
		$subject = null;
		$heading= null;
		$filename = null;
		
		if( null === $id )
		{
			$subject = Engine_Api::_()->user()->getViewer();
			Engine_Api::_()->core()->setSubject($subject);
		}
		else
		{
			$subject = Engine_Api::_()->getItem('user', $id);
			Engine_Api::_()->core()->setSubject($subject);
		}

		$this->view->navigation = $navigation = Engine_Api::_()
		->getApi('menus', 'core')
		->getNavigation('user_settings', ( $id ? array('params' => array('id'=>$id)) : array()));
	}

	public function indexAction ()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->user_status = $user_st = Engine_APi::_()->getItem('user',$viewer->getIdentity()); $user->status;
		
		$viewer = Engine_Api::_()->user()->getViewer();
				
        $user_Values = Engine_Api::_()->fields()->getTable('user', 'values');
        $metaTable = Engine_Api::_()->fields()->getTable('user','meta');
        $user_select = $user_Values->select()
                      ->setIntegrityCheck(false)
                     ->from(array('meta' => $metaTable->info('name')),array('label','field_id'))
                     ->joinLeft(array('val' => $user_Values->info('name')),'val.field_id = meta.field_id',array('value'))
        			->where('item_id = ?',$viewer->getIdentity())
                    ->where('meta.label IN ("Contact","Website","Phone")');
        $this->view->values = $userValues = $metaTable->fetchAll($user_select);
        
        if (!$this->_helper->requireUser()->isValid()){
			return true;
		}
		$this->view->form = $form = new Qrcode_Form_Qrcode();
		if( !$this->getRequest()->isPost() ) {
			return;
		}

		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}
		

	}

	public function userinfoAction ()
	{
		$field_id=array();
		$userData = array();
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$subject = Engine_Api::_()->core()->getSubject();

		$fieldOptions = 	Engine_Api::_()->fields()->getFieldsOptions($subject);
		$fieldMetas = Engine_Api::_()->fields()->getFieldsMeta($subject);
		$userMap = array();
		$userOption = array();
		foreach ($fieldMetas as $fieldMeta){
			$userMap[$fieldMeta->field_id] = $fieldMeta->label;
		}
		foreach ($fieldOptions as $fieldOption){
			$userOptions[$fieldOption->field_id.'-'.$fieldOption->option_id] = $fieldOption->label;

		}

		$valuesStructure = array();
		$valueCount = 0;


		$qrtype = $this->_getParam('qrtype');
		$settings = Engine_Api::_()->getDbtable('settings', 'core');

		$infoHeading=array();
		if($qrtype == 0){//website
			$field_id = $settings->__get("qrcode.website");
			$infoHeading="Website";
		}
	   else if($qrtype == 1){//phone
			$field_id = $settings->__get("qrcode.phone");
			$infoHeading="Phone";
			
		}
		else {// Default Contact details
			$field_id = $settings->__get("qrcode.contact");
			$infoHeading="Contacts";
		}

		
		if($qrtype == 2 ){
			if(!empty($viewer->status)){
				$userData['Status'] = $viewer->status;	
			}
			else {
					$userData[] ='';
			}
		}
		else if($qrtype == 4 ){
			$userData['Profile'] = $_SERVER['SERVER_NAME'].$viewer->getHref();
			$infoHeading = "Profile";
		}
		else if($qrtype == 5){//custom url
			$infoHeading="Custom Url";
		}
		else{
			
			$userVals = Engine_Api::_()->fields()->getTable('user', 'values');
			$select = $userVals->select()->where('item_id = (?)', $viewer->getIdentity())
			->where('field_id in (?)',explode(',',$field_id));
			$fields = $userVals->fetchAll($select);
			
			

			foreach ($fields as $field){
				if(array_key_exists($field->field_id.'-'.$field->value,$userOptions)){
					$userData[$userMap[$field->field_id]] = $userOptions[$field->field_id.'-'.$field->value];
					$infoHeading = $userMap[$field->field_id];
				}
				else
				{
					$userData[$userMap[$field->field_id]] = $field->value;
					$infoHeading = $userMap[$field->field_id];
				}
				$this->view->rest = $userdata = $userData[$userMap[$field->field_id]];
			}
		}

		//$userData['display']=true;
		if($qrtype==null)
		{
			// this field blank becuase, null value can't response to ajax call.
		}
		else 
		{
		$data = array('heading'=>$infoHeading,'qrtype'=>$qrtype,'userdata'=>$userData,'user_id'=> $viewer->getIdentity());
		if( $this->_getParam('sendNow', true) ) {
			return $this->_helper->json($data);
		} else {
			$this->_helper->viewRenderer->setNoRender(true);
			$data = Zend_Json::encode($data);
			$this->getResponse()->setBody($data);
		}
		}


	}

	public function embeddedimageAction ()
	{
		$userdata=$this->_getParam('userdata');
		$heading=$this->_getParam('heading');
		$qrCode = $this->_getParam('qrtype');
	
		$customValue = $this->_getParam('customVal');
		if(!empty($customValue ))
		{
			$userdata =$customValue;
			$scale = 9;
			$modulesize = 6;
			$padding = 4;
			$keys   =   array_keys($userdata);
			$heading =$keys[0];
		}
		else
		{
			$scale = 9;
			$modulesize = 6;
			$padding = 4;
			$keys   =   array_keys($userdata);
			$heading =$keys[0];
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$display=$this->_getParam('display');
		
	
		if($qrCode == 1 )
		{
			$userdata[$heading]="tel:".$userdata[$heading];
			$modulesize = 6;
		}
		else if($qrCode == 0){
			$userdata[$heading]="http://".$userdata[$heading];
			$modulesize = 6;
		}
		
		else if($qrCode == 2){
			$userdata[$heading]= $userdata[$heading];
			$modulesize = 6;
		}
		else if($qrCode == 4){
			$userdata[$heading]= "http://".$userdata[$heading];
			$modulesize = 6;
		}
		else if($qrCode == 5){
			$customValue = $customValue;
			$modulesize = 6;
		}
		else
		{
				
			$contacts = "BEGIN:VCARD
VERSION:3.0
N:".$userdata['Last Name'].";".$userdata['First Name']."
FN:".$userdata['First Name']."
ORG:
TITLE:
TEL;TYPE=WORK,VOICE:".(array_key_exists('Phone',$userdata)? $userdata['Phone']:"")."
EMAIL;TYPE=PREF,INTERNET:".$viewer->email."
URL:http://".$userdata['Website']."
REV:20100426T103000Z
END:VCARD";

			$userdata[$heading] = $contacts;
			$padding =5;
			$modulesize = 3;
		}


		if(!empty($customValue))
		{
			$code_params=array('text' => $customValue,
					'backgroundColor' => '#FFFFFF',
					'foreColor' => '#000000',
					'padding' => $padding,//array(5,3,3,5),
					'scale'=>$scale,
					'moduleSize' => $modulesize);
		}
		else {
			$code_params=array('text' => $userdata[$heading],
	                                'backgroundColor' => '#FFFFFF', 
	                                'foreColor' => '#000000', 
	                                 'padding' => $padding,//array(5,3,3,5),
			                        'scale'=>$scale,
									'moduleSize' => $modulesize);
		}
		$time=(time()*1000);
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$fileName = $time.'-'.Engine_Api::_()->user()->getViewer()->getIdentity().'.png';
		$imagepath = APPLICATION_PATH.'/public/user' . DIRECTORY_SEPARATOR  . $fileName;
		$renderer_params = array('imageType' => 'png','sendResult' => false);
		$res = Zend_Matrixcode::render('qrcode', $code_params, 'image', $renderer_params);
		imagepng($res,$imagepath);
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        
	
		
		$qrcodeTable =  Engine_Api::_()->getDbTable('qrcodes','qrcode');
				
		
		try {
			$qrcodeTable->insert(array(
					  	'field_type' => $qrCode,
				 		'image_url' => $fileName,
					    'custom_url' => $customValue,
						'user_id'=>  $user_id));	
		}
		catch(Exception $e)
		{

			try {
				$qrcodeTable->update(array(
					  	'field_type' => $qrCode,
				 		'image_url' => $fileName,
						'custom_url' => $customValue,
						'display'	=> 0,
				),
				array('user_id = ? '=>  $user_id));
			}
			catch(Exception $e){
					
			}
		}
		echo "public/user/".$fileName;
	}
	public function displaycheckAction ()
	{

		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$display=$this->_getParam('display');
		if($display == 'false')
		$display = 0;
		else
		$display = 1;
		$qrcodeTable =  Engine_Api::_()->getDbTable('qrcodes','qrcode');
		$qrcodeTable->update(array(
				  	'display' => $display,
			
		),
		array('user_id = ? '=>  $user_id));
		$viewer = Engine_Api::_()->user()->getViewer();
		$user_id=$viewer->getIdentity();
		$userVals =Engine_Api::_()->getDbtable('qrcodes', 'qrcode');
		$select = $userVals->select()->where('user_id = ?', $user_id);
		$fields = $userVals->fetchRow($select);
		$title='';
		if($fields['field_type'] == 0)
		{
			$title='Website';
		}
		else if($fields['field_type'] == 1)
		{
			$title='Phone';
		}
		else if($fields['field_type'] == 2)
		{
			$title='Status';
		}
		else if($fields['field_type'] == 4)
		{
			$title='Profile link';
		}
		else if($fields['field_type'] == 5)
		{
			$title='custom_url';
		}
		else
		{
			$title='Contact';
		}
		$subject = $viewer;
		$viewer = Engine_Api::_()->user()->getViewer();
		if($fields['display'] == 0)
		{

		}
		else{


			$url = "public/user/".$fields['image_url'];
					
			$zoom = $this->_getParam("zoom","");
			$size = $this->_getParam("size","");
			$sensor = $this->_getParam("sensor","");
			$query = $this->_getParam("q","");
			if(!empty($zoom) && !empty($size) && !empty($sensor)){
				$url = $url.'&zoom='.$zoom.'&size='.$size.'&sensor='.$sensor;
			}
			if(!empty($query)){
				$url = $url.'&q='.$query;
			}
			$log->log($url, Zend_Log::DEBUG);
			$body = $this->view->translate('Created QR Code for ').$title;
			$return_url = '';
			$attachmentParam['uri'] ="/qrcode";
			$attachmentParam['title'] = $this->view->translate('Generate your QR Code');
			$attachmentParam['description'] = $this->view->translate("Generate QR Code for your profile");
			$attachmentParam['thumb'] = $url;
			$attachmentParam['type'] = 'link';

			$status = "";
			////
			try {
				$attachment = null;
				$attachmentData = $attachmentParam;
				if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
					$type = $attachmentData['type'];
					$config = null;
					foreach( Zend_Registry::get('Engine_Manifest') as $data ) {
						if( !empty($data['composer'][$type]) ) {
							$config = $data['composer'][$type];
						}
					}
					if( !empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1]) ) {
						$config = null;
					}
					if( $config ) {
						$plugin = Engine_Api::_()->loadClass($config['plugin']);
						$method = 'onAttach'.ucfirst($type);
						$attachment = $plugin->$method($attachmentData);
					}
				}
				////
				$type = 'post';
				if( $viewer->isSelf($subject) ) {
					$type = 'post_self';
				}

				// Add notification for <del>owner</del> user
				$subjectOwner = $subject->getOwner();

				if( !$viewer->isSelf($subject) &&
				$subject instanceof User_Model_User ) {
					$notificationType = 'post_'.$subject->getType();
					Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
           			 'url1' => $subject->getHref(),
					));
				}

				// Add activity
				$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, $type, $body);

				// Try to attach if necessary
				if( $action && $attachment ) {
					Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
				}

				$status = true;
			} catch( Exception $e ) {
				$log->log($e, Zend_Log::DEBUG);
				$status = false;

			}
		}
	}

	public function previousimageAction ()
	{

		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$viewer = Engine_Api::_()->user()->getViewer();
		$user_id=$viewer->getIdentity();
		$userVals =Engine_Api::_()->getDbtable('qrcodes', 'qrcode');
		$select = $userVals->select()->where('user_id = ?', $user_id);
		$fields = $userVals->fetchRow($select);
		$data= array('image_url' => $fields->image_url,'custom_url' => $fields->custom_url,'field'=>$fields->field_type,'display' =>$fields->display);
		return $this->_helper->json($data);
	}

   
}