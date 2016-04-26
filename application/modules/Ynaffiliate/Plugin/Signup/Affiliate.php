<?php

class Ynaffiliate_Plugin_Signup_Affiliate extends Core_Plugin_FormSequence_Abstract
{
  protected $_name = 'ynaffiliate';
  protected $_formClass = 'Ynaffiliate_Form_Signup_Affiliate';
  protected $_script = array('signup/form/affiliate.tpl', 'ynaffiliate');
  protected $_adminFormClass = 'Ynaffiliate_Form_Admin_Signup_Affiliate';
  protected $_adminScript = array('admin-signup/affiliate.tpl', 'ynaffiliate');
  protected $_skip;
  protected $_invisible = false;
  
  public function __construct(){
  	// check and make package.
  	// disable display active form.
  	
	$referer = null;
	
	if(isset($_REQUEST['refid']) && $_REQUEST['refid']){
		$referer =  $this->getUserByRefererId($_REQUEST['refid']);
	}
	
	if(!is_object($referer)){
		// skip this step and continue.
		$this->_invisible = true;
	}
	
	 $settings = Engine_Api::_()->getApi('settings', 'core');
	 
	 $visible =  $settings->getSetting('ynaffiliate.visible',false);
	
	 if(!$visible){
		$this->_invisible = true;
	 }
	 
	 if(is_object($referer)){
	 	$referer_id = $referer->getIdentity();
	 	$this->getSession()->referer_id =  $referer_id;
		$day =  (int)$settings->getSetting('ynaffiliate.expireddays',30);
		$expired = $day* 86400 + time();
		setcookie('ynaffiliate',$referer_id,$expired,'/');
	 }else{
	 	$this->getSession()->referer_id = 0;
	 }
  }
  
  public function isActive(){
  	if($this->_invisible){
  		return false;	
  	}
  	return parent::isActive();
  }
  
  public function getUserByRefererId($id){
  	$model = Engine_Api::_()->getDbTable('Users','User');
	$select  = $model->select()->where('user_id=?',$id);
	return $model->fetchRow($select);	
  }

  public function onSubmit(Zend_Controller_Request_Abstract $request)
  {
    // Form was valid
    $skip = $request->getParam("skip");
    // do this if the form value for "skip" was not set
    // if it is set, $this->setActive(false); $this->onsubmisvalue and return true.
    if( $skip == "skipForm" ) {
      $this->setActive(false);
      $this->onSubmitIsValid();
      $this->getSession()->skip = true;
      $this->_skip = true;
      return true;
    } else {
      parent::onSubmit($request);
    }
  }

  public function onProcess()
  {
    // In this case, the step was placed before the account step.
    // Register a hook to this method for onUserCreateAfter
    if( !$this->_registry->user ) {
      // Register temporary hook
      Engine_Hooks_Dispatcher::getInstance()->addEvent('onUserCreateAfter', array(
        'callback' => array($this, 'onProcess'),
      ));
      return;
    }
    $user = $this->_registry->user;
    
    $data = $this->getSession()->data;
    $form = $this->getForm();
    if( !$this->_skip && !$this->getSession()->skip ) {
      if( $form->isValid($data) ) {
        $values = $form->getValues();
        Engine_Api::_()->getDbtable('invites', 'invite')->sendInvites($user, @$values['recipients'], @$values['message']);
      }
    }
  }

  public function onAdminProcess($form)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $step_table = Engine_Api::_()->getDbtable('signup', 'user');
    $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'Ynaffiliate_Plugin_Signup_Affiliate'));
    $step_row->enable = $form->getValue('enable');
    $step_row->save();
	
	$settings->setSetting('ynaffiliate.visible',$form->getValue('visible'));

    $form->addNotice('Your changes have been saved.');
  }

}