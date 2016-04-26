<?php

class Groupbuy_SubscriptionController extends Core_Controller_Action_Standard{
	public function subscribeWidgetAction(){
		
		$message =  NULL;
		$data = array();
		$error =  false;
		
		try{
			$data =  $this->_request->getPost();
			$table = Engine_Api::_()->getDbTable('SubscriptionContacts','Groupbuy');
			//$viewer =  Engine_Api::_()->user()->getViewer();
			$contact  = $table->addContact($data);
			if($contact->verified){
				$message = $this->view->translate("Your email address was verified!");
			}else{
				$condition = $table->addCondition($data);
				$verify_action =  'subscribe_verify';
				$verify_code=  Engine_Api::_()->getDbTable('Verifications','Groupbuy')->addVerify($contact->getIdentity(), $verify_action,1);
				
				$params = array();
				
				// check if website link is validated. we need something else from thats.
				
				$params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
				$params['website_link'] = $href= 'http://'.@$_SERVER['HTTP_HOST']; 
				
				$params['verify_code'] = $verify_code;
				$href=  				 
				'http://'. @$_SERVER['HTTP_HOST'].
				Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'groupbuy','controller'=>'subscription','action' => 'subscribe-verify','code'=>$verify_code),'default',true);
				$params['verify_link'] = $href;				
				Engine_Api::_()->getApi('Mail','Groupbuy')->send($data['email'],'groupbuy_dealsubscribed',$params );
				$message =  $this->view->translate("An verification email has been sent.");	
			}			
		}catch(Exception $e){
			$message =  $e->getMessage();
			$error = true;
		}

		$this->view->error =  $error;
		$this->view->message  =  $message;
	}
	
	/**
	 * we do not check expired date at this time.
	 */
	public function subscribeVerifyAction(){
		$verify_code = $this->_getParam('code','###');
		$verify_action =  'subscribe_verify';
		$verify=  Engine_Api::_()->getDbTable('Verifications','Groupbuy')->getVerify($verify_code, $verify_action);
				
		if(!is_object($verify)){
			return $this->_forward('subscribe-failure');
		}
		
		$table = Engine_Api::_()->getDbTable('SubscriptionContacts','Groupbuy');
		$contact = $table->find($verify->item_id)->current();
		
		if(!is_object($contact)){
			return $this->_forward('subscribe-failure');
		}
		
		$contact->verified = 1;
		$contact->verified_date = date('Y-m-d H:i:s');
		$contact->save();
	}

	public function unsubscribeAction(){
		
	}
	/**
	 * we do not check expired date at this time.
	 */
	public function unsubscribeVerifyAction(){
		$code = $this->_getParam('code','###');
		$table = Engine_Api::_()->getDbTable('SubscriptionContacts','Groupbuy');
		$contact = $table->fetchRow($table->select()->where('verify_code=?',$code));

		if(!is_object($contact)){
			return $this->_forward('unsubscribe-failure');
		}		
		$contact->delete();
	}
	public function subscribeFailureAction(){
		
	}
}
