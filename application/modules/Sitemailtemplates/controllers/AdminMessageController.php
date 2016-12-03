<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminMessageController.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_AdminMessageController extends Core_Controller_Action_Admin
{
  public function mailAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('Sitemailtemplates_admin_main', array(), 'sitemailtemplates_admin_main_mail');

    $this->view->form = $form = new Sitemailtemplates_Form_Admin_Message_Mail();
    
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $topLevelOptions = Engine_Api::_()->getApi('mail', 'sitemailtemplates')->getProfileTypes(0);
		$topLevelOptionsCount = Count($topLevelOptions);
    $this->view->show_profietype = 0;
    if($topLevelOptionsCount > 2)
    $this->view->show_profietype = 1;
    
    //LET THE LEVEL_IDS BE SPECIFIED IN GET STRING
    $level_ids = $this->_getParam('level_id', false);
    if (is_array($level_ids)) {
      $form->target->setValue($level_ids);
    }

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $this->view->messageSent = 0;
    $values = $_POST;

    if(isset($_FILES['fileupload']['size']) && !empty($_FILES['fileupload']['size'])) {
			//FILE SIZE SHOULD NOT EXCEED FROM ALLOWED LIMIT FOR THIS LEVEL
			$filesize = (int) ini_get('upload_max_filesize') * 1024 * 1024;
			if ($_FILES['fileupload']['size'] > $filesize) {
				$error = $this->view->translate('File size can not be exceed from ') . ($filesize ) . $this->view->translate(' KB');
				$error = Zend_Registry::get('Zend_Translate')->_($error);
				$form->getDecorator('errors')->setOption('escape', false);
				$form->addError($error);
				return;
			}

			//FILE EXTENSION SHOULD NOT DIFFER FROM ALLOWED TYPE
			$ext = str_replace(".", "", strrchr($_FILES['fileupload']['name'], "."));
			if (!in_array($ext, array('pdf', 'txt', 'ps', 'rtf', 'epub', 'odt', 'odp', 'ods', 'odg', 'odf', 'sxw', 'sxc', 'sxi', 'sxd', 'doc', 'ppt', 'pps', 'xls', 'docx', 'pptx', 'ppsx', 'xlsx', 'tif', 'tiff', 'jpeg', 'jpg', 'png', 'bmp', 'zip', 'tar', 'json', 'html', 'xml', 'gif'))) {
				$error = $this->view->translate("Invalid file extension. Allowed extensions are :'pdf', 'txt', 'ps', 'rtf', 'epub', 'odt', 'odp', 'ods', 'odg', 'odf', 'sxw', 'sxc', 'sxi', 'sxd', 'doc', 'ppt', 'pps', 'xls', 'docx', 'pptx', 'ppsx', 'xlsx', 'tif', 'tiff', 'jpeg', 'jpg', 'png', 'bmp', 'zip', 'tar', 'json', 'html', 'xml', 'gif'");
				$error = Zend_Registry::get('Zend_Translate')->_($error);

				$form->getDecorator('errors')->setOption('escape', false);
				$form->addError($error);
				return;
			}
    }

    unset($values['body_text']);
    //OKAY, SAVE
    foreach( $values as $key => $value ) {
      if (!empty($value)) {
				$settings->setSetting($key, $value);
      }
    }

    $emails = array(); 

    if(isset($values['sitemailtemplates_send_mail']) && empty($values['sitemailtemplates_send_mail'])) {
      if(empty($values['toValues'])) {
        $error = Zend_Registry::get('Zend_Translate')->_('Member to Email * Please complete this field - it is required.');
				$form->getDecorator('errors')->setOption('escape', false);
				$form->addError($error);
        return;
      }

      $emails = preg_split('/[,]+/', $values['toValues']);
    }
    else {
			//GET LEVELS
			if(isset($values['member_levels'])) {
				$member_levels = $values['member_levels'];
			}
			else {
				$member_levels = array();
			}

			//GET PROFILE TYPES
			if(isset($values['profile_types'])) {
				$profile_types = $values['profile_types'];
			}
			else {
				$profile_types = array();
			}

			//GET NETWORKS
			if(isset($values['networks'])) {
				$networks = $values['networks'];
			}
			else {
				$networks = array();
			}

			//GET USER TABLE
			$tableUser = Engine_Api::_()->getItemTable('user');
			$tableUserName = $tableUser->info('name');

			$select = $tableUser->select()->setIntegrityCheck(false)->from($tableUserName, array('email'));

			if(!in_array(0, $member_levels) && !in_array(0, $profile_types) && !in_array(0, $networks)) {
				if (!empty($member_levels)) {
					$select->where("$tableUserName.level_id IN (?) ", $member_levels)
								 ->where("$tableUserName.enabled = ?", true);
				}

				if(!empty($networks)) {

					//GET NETWORK TABLE
					$tableNetwork = Engine_Api::_()->getDbTable('membership', 'network');
					$tableNetworkName = $tableNetwork->info('name');
					$select->joinLeft($tableNetworkName, "$tableNetworkName.user_id = $tableUserName.user_id", null)
									->orwhere("$tableNetworkName.resource_id IN (?)", $networks)
									->where("$tableUserName.enabled = ?", true);
				}

				if(!empty($profile_types)) {
					$tableUserFieldValues = Engine_Api::_()->fields()->getTable('user', 'values');
					$tableUserFieldValuesName = $tableUserFieldValues->info('name');
					$select->joinLeft($tableUserFieldValuesName, "$tableUserFieldValuesName.item_id = $tableUserName.user_id", null)
								->orwhere("$tableUserFieldValuesName.field_id IN (?)", $profile_types)
								->where("$tableUserName.enabled = ?", true);
				}
			}
			elseif(!isset($values['member_levels']) && !isset($values['profile_types']) && !isset($values['networks'])) {
				$select->where("$tableUserName.enabled = ?", true);
			}
      else {
				$select->where("$tableUserName.enabled = ?", true);
			}

			$select = $select->group("$tableUserName.user_id");

			foreach( $select->query()->fetchAll(Zend_Db::FETCH_COLUMN, 0) as $email ) {
				$emails[] = $email;
			}
    }

    //TEMPORARILY ENABLE QUEUEING IF REQUESTED
    $temporary_queueing = $settings->core_mail_queueing;
    if (isset($values['queueing']) && $values['queueing']) {
      $settings->core_mail_queueing = 1;
    }

    $mailApi = Engine_Api::_()->getApi('mail', 'core');
    $mail = $mailApi->create();
    $mail
      ->setFrom($values['from_address'], $values['from_name'])
      ->setSubject($values['subject'])
      ->setBodyHtml($values['body']);

    if( !empty($values['body_text']) ) {
      $mail->setBodyText($values['body_text']);
    } else {
      $mail->setBodyText($values['body']);
    }
    
    foreach( $emails as $email ) {
			if( empty($values['temLimit']) ) {
				$mail->addTo($email);
			}
    }

    if(isset($_FILES['fileupload']['size']) && !empty($_FILES['fileupload']['size']) && isset($_FILES['fileupload']['tmp_name'])) {
			$filecontents = file_get_contents($_FILES['fileupload']['tmp_name']);
			$attachment = $mail->createAttachment($filecontents);
			$attachment->filename = $_FILES['fileupload']['name'];
    }

		include APPLICATION_PATH . '/application/modules/Sitemailtemplates/controllers/license/license2.php';

    $mailComplete = $mailApi->create();
    $mailComplete
      ->addTo(Engine_Api::_()->user()->getViewer()->email)
      ->setFrom($values['from_address'], $values['from_name'])
      ->setSubject('Mailing Complete: '.$values['subject'])
      ->setBodyHtml('Your email blast to your members has completed.  Please note that, while the emails have been
        sent to the recipients\' mail server, there may be a delay in them actually receiving the email due to
        spam filtering systems, incoming mail throttling features, and other systems beyond SocialEngine\'s control.');
    $mailApi->send($mailComplete);

    //emails have been queued (or sent); re-set queueing value to original if changed
    if (isset($values['queueing']) && $values['queueing']) {
      $settings->core_mail_queueing = $temporary_queueing;
    }
    $this->view->messageSent = 1;
    $this->view->status = true;
  }

  public function getitemAction() {

    //GET THE SUGGEST USERS
    $users = Engine_Api::_()->getDbtable('users', 'user');
    $select = $users->select()
            ->from($users->info('name'),array('email','displayname','photo_id'))
            ->where('displayname  LIKE ? ', '%' . $this->_getParam('user_ids') . '%')
            ->order('displayname ASC')
            ->limit('40');
    $users = $users->fetchAll($select);
    $data = array();
    
    foreach ($users as $usersitepage) {
      $user_photo = $this->view->itemPhoto($usersitepage, 'thumb.icon');
      $data[] = array(
          'id' => $usersitepage->email,
          'label' => $usersitepage->displayname,
          'photo' => $user_photo
      );
    }
    
    return $this->_helper->json($data);
    $data = Zend_Json::encode($data);
    $this->getResponse()->setBody($data);
  }

  //ACTION FOR UPLOADING THE IMAGES
  public function uploadPhotoAction() {

    $this->_helper->layout->setLayout('default-simple');

    //GET THE DIRECTORY WHERE WE ARE UPLOADING THE PHOTOS
    $adminContactFile = APPLICATION_PATH . '/public/adminmailtemplate';

    if (!is_dir($adminContactFile) && mkdir($adminContactFile, 0777, true)) {
      chmod($adminContactFile, 0777);
    }

    //GET THE PATH
    $path = realpath($adminContactFile);
     $fileName = Engine_Api::_()->seaocore()->tinymceEditorPhotoUploadedFileName();
    //PREPARE
    if (empty($_FILES[$fileName])) {
      $this->view->error = 'File failed to upload. Check your server settings (such as php.ini max_upload_filesize).';
      return;
    }

    //GET PHOTO INFORMATION
    $info = $_FILES[$fileName];

    //SET TARGET FILE
    $targetFile = $path . '/' . $info['name'];

    //TRY TO MOVE UPLOADED FILE
    if (!move_uploaded_file($info['tmp_name'], $targetFile)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Unable to move file to upload directory.");
      return;
    }

    //SEND THE STATUS TO THE TPl
    $this->view->status = true;

    //SEND THE PHOTO URL TO THE TPl
    $this->view->photo_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . '/' . Zend_Controller_Front::getInstance()->getBaseUrl() . '/public/adminmailtemplate/' . $info['name'];
  }

}