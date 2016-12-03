<?php
class Qrcode_Widget_QrcodeImageController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{

		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = Engine_Api::_()->core()->getSubject();
		$log = Zend_Registry::get('Zend_Log');
			
		$subject_id=$subject->getIdentity();
		$user_id = $viewer->getIdentity();
		$log->log('subject'.$subject_id, Zend_Log::DEBUG);
		$log->log('user'.$user_id, Zend_Log::DEBUG);

		if($subject_id != $user_id)
		{
			$userVals =Engine_Api::_()->getDbtable('qrcodes', 'qrcode');
			$select = $userVals->select()->where('user_id = (?)', $subject_id);
			$this->view->fields = $fields = $userVals->fetchRow($select);
		
		}
		else {
			$userVals =Engine_Api::_()->getDbtable('qrcodes', 'qrcode');
			$select = $userVals->select()->where('user_id = (?)', $user_id);
			$this->view->fields = $fields = $userVals->fetchRow($select);
		
		}
		//echo $fields->image_url;

	}


}