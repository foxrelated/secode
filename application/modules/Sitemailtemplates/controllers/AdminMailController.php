<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminMailController.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_AdminMailController extends Core_Controller_Action_Admin
{

  public function templatesAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('Sitemailtemplates_admin_main', array(), 'sitemailtemplates_admin_main_templates');

    $this->view->form = $form = new Sitemailtemplates_Form_Admin_Mail_Templates();
    
    $this->view->textFlag = $textFlag = $this->_getParam('textFlag', 0);
		if( empty( $textFlag ) ) {
			$form->removeElement('body_description');
		}
    else {
			$form->removeElement('body');
		}
		
		$this->view->textFlagofSig = $textFlagofSig = $this->_getParam('signature_editor', 0);
		if( empty( $textFlagofSig ) ) {
			$form->removeElement('sitemailtemplates_footer1_editor');
		}
    else {
			$form->removeElement('sitemailtemplates_footer1');
		}
    
    //GET LANGUAGE
    $local_language = $this->view->locale()->getLocale()->__toString();
    $local_language = explode('_', $local_language);
    $this->view->language = $local_language[0];
    
    $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
   
    $this->view->language_url = $language = preg_replace('/[^a-zA-Z_-]/', '', $this->_getParam('language', $defaultLanguage));
    if( !Zend_Locale::isLocale($language) ) {
      $form->removeElement('submit');
      return $form->addError('Please select a valid language.');
    }

    if($language == 'auto'){
      $language = $translate->getLocale();
    }

    //CHECK DIR FOR EXIST/WRITE
    $languageDir = APPLICATION_PATH . '/application/languages/' . $language;
    $languageFile = $languageDir . '/custom.csv';

    if( !is_dir($languageDir) ) {
      $form->removeElement('submit');
      return $form->addError('The language does not exist, please create it first');
    }

    if( !is_writable($languageDir) ) {
      $form->removeElement('submit');
      return $form->addError('The language directory is not writable. Please set CHMOD -R 0777 on the application/languages folder.');
    }

    if( is_file($languageFile) && !is_writable($languageFile) ) {
      $form->removeElement('submit');
      return $form->addError('The custom language file exists, but is not writable. Please set CHMOD -R 0777 on the application/languages folder.');
    }

    //GET TEMPLATE
    $template = $this->_getParam('template', '1');
    $templateObject = Engine_Api::_()->getItem('core_mail_template', $template);
    if( !$templateObject ) {
      $templateObject = Engine_Api::_()->getDbtable('MailTemplates', 'core')->fetchRow();
      $template = $templateObject->mailtemplate_id;
    }
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $column = 'email_signature_'.$language;
		$languageColumn = $db->query("SHOW COLUMNS FROM engine4_core_mailtemplates LIKE '$column'")->fetch();
    
    if(!empty($languageColumn)) {
      $signatureColumn = $languageColumn;
    }
    else {
      $signatureColumn = 'email_signature_en';
    }
    
    if(is_array($languageColumn) && isset($languageColumn['Field'])) {
        $signatureColumn = $languageColumn['Field'];
    }    
    
    if(empty($templateObject->show_signature) && empty($templateObject->$signatureColumn)) {
			$publishUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'home'), 'user_general', true);

			$emailNotificationUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->baseUrl().'/members/settings/notifications';

      $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));

			$description = $this->view->translate("<p><span style='color: #92999c; font-size: x-small; font-family: arial,helvetica,sans-serif;'>If you are a member of <a href='%s' target='_blank' style='text-decoration:none;'>$siteTitle</a>, and would like to choose which emails you receive from us, then please <a href='$emailNotificationUrl' style='text-decoration:none;' target='_blank'>click here</a>.</span><br><span style='color: #92999c; font-size: x-small; font-family: arial,helvetica,sans-serif;'> To continue receiving our emails, please add us to your address book or safe list.</span></p>");
			$email_signature = sprintf($description, $publishUrl);
    }
    else {
      $email_signature = $templateObject->$signatureColumn;
    }

    //POPULATE FORM
    $description = $this->view->translate(strtoupper("_email_".$templateObject->type."_description"));
    $description .= '<br /><br />';
    $description .= $this->view->translate('Available Placeholders:');
    $description .= '<br />';
    $description .= join(', ', explode(',', $templateObject->vars));

    $form->getElement('template')
					->setDescription($description)
					->getDecorator('Description')
					->setOption('escape', false);

    //GET TRANSLATECORE_CONTROLLER_ACTION_ADMIN
    $translate = Zend_Registry::get('Zend_Translate');

    //GET STUFF
    $subjectKey = strtoupper("_email_".$templateObject->type."_subject");
    $subject = $translate->_($subjectKey, $language);
    if( $subject == $subjectKey ) {
      $subject = $translate->_($subjectKey, 'en');
    }

    $bodyHTMLKey = strtoupper("_email_".$templateObject->type."_bodyhtml");
    $body = $translate->_($bodyHTMLKey, $language);
    if( $body == $bodyHTMLKey ) {
      $body = $translate->_($bodyHTMLKey, 'en');
    }

    // get body from email body key if not found by bodyhtml key
    if( $body == $bodyHTMLKey ) {

        $bodyKey = strtoupper("_email_".$templateObject->type."_body");
        $body = $translate->_($bodyKey, $language);
        if( $body == $bodyKey ) {
          $body = $translate->_($bodyKey, 'en');
        }
        $body = nl2br($body);
    }

    $signatureyKey = strtoupper("_email_".$templateObject->type."_signature");
    $signature = $translate->_($signatureyKey, $language);
    if( $signature == $signatureyKey ) {
      $signature = $translate->_($signatureyKey, 'en');
    }
   
    if($signature == strtoupper("_email_".$templateObject->type."_signature")) {
      $signature = $email_signature;
    }

    $sitemailtemplateTable = Engine_Api::_()->getDbTable('templates','sitemailtemplates');
    $activate_template_id = $sitemailtemplateTable->select()
															->from($sitemailtemplateTable->info('name'), 'template_id')
														  ->where('active_template =?',1)
														  ->limit(1)
														  ->query()->fetchColumn();
    
    if(empty($textFlag)) {
			$form->populate(array(
				'language' => $language,
				'template' => $template,
				'subject' => $subject,
				'sitemailtemplates_footer1' => $signature,
				'body' => $body,
				'enable_template' => $templateObject->enable_template,
				'template_id' => $templateObject->template_id,
			));
    }
    else {
      $form->populate(array(
      'language' => $language,
      'template' => $template,
      'subject' => $subject,
      'sitemailtemplates_footer1' => $signature,
      'body_description' => $body,
      'enable_template' => $templateObject->enable_template,
      'template_id' => $templateObject->template_id,
    ));
    }
    
    if($templateObject->template_id == 0) {
      $form->populate(array(
				'template_id' => $activate_template_id,
			));
    }
    
    //CHECK METHOD/VALID
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    //PROCESS
    $values = $form->getValues();

    if( !empty($values['body_description']) ){ 
			$values['body'] = $values['body_description'];
			unset($values['body_description']);
		}
		
    //$values['sitemailtemplates_footer1'] = '';
		if( !empty($values['sitemailtemplates_footer1_editor']) ){ 
			$values['sitemailtemplates_footer1'] = $values['sitemailtemplates_footer1_editor'];
			unset($values['sitemailtemplates_footer1_editor']);
		}

    $values['template_id'] = !empty($values['template_id']) ? $values['template_id'] : 0;
    $tableMailtemplate = Engine_Api::_()->getDbtable('MailTemplates', 'core');
    $tableMailtemplate->update(array('enable_template' => $values['enable_template'],'template_id' => $values['template_id']), array('type = ?' => $templateObject->type));

    if( empty($languageColumn) ) {
		  $db->query("ALTER TABLE `engine4_core_mailtemplates` ADD `$column` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `email_signature_en`");
		}
    
    $tableMailtemplate->update(array($column => $values['sitemailtemplates_footer1'],'template_id' => $values['template_id']), array('type = ?' => $templateObject->type));
    
    $writer = new Engine_Translate_Writer_Csv();

    //TRY TO WRITE TO A FILE
    $targetFile = APPLICATION_PATH . '/application/languages/' . $language . '/custom.csv';
    if( !file_exists($targetFile) ) {
      touch($targetFile);
      chmod($targetFile, 0777);
    }

    //SET THE LOCAL FOLDER DEPENDING ON THE LANGUAGE_ID
    $writer->read(APPLICATION_PATH . '/application/languages/' . $language . '/custom.csv');

    //WRITE NEW SUBJECT
    $writer->removeTranslation(strtoupper("_email_" . $templateObject->type . "_subject"));
    $writer->setTranslation(strtoupper("_email_" . $templateObject->type . "_subject"), $values['subject']);

    //WRITE NEW BODY
    $writer->removeTranslation(strtoupper("_email_" . $templateObject->type . "_bodyhtml"));
    $writer->setTranslation(strtoupper("_email_" . $templateObject->type . "_bodyhtml"), $values['body']);
    $writer->write();

    //WRITE NEW Signature
    $writer->removeTranslation(strtoupper("_email_" . $templateObject->type . "_signature"));
    $writer->setTranslation(strtoupper("_email_" . $templateObject->type . "_signature"), $values['sitemailtemplates_footer1']);
    $writer->setTranslation($values['sitemailtemplates_footer1'], $values['sitemailtemplates_footer1']);
    $writer->write();

    //CLEAR CACHE?
    $translate->clearCache();

    $form->addNotice('Your changes have been saved.');
  }
  
  public function editSignatureAction()
  {
    $this->view->form = $form = new Sitemailtemplates_Form_Admin_Mail_Signature();
  
    $tableMailtemplate = Engine_Api::_()->getDbtable('MailTemplates', 'core');
  
    $language = Zend_Controller_Front::getInstance()->getRequest()->getParam('language', null);
    $column = 'email_signature_'.$language;
    
		$db = Engine_Db_Table::getDefaultAdapter();
		$languageColumn = $db->query("SHOW COLUMNS FROM engine4_core_mailtemplates LIKE '$column'")->fetch();
		
		//CHECK METHOD/VALID
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
		
		if( empty($languageColumn) ) {
		  $db->query("ALTER TABLE `engine4_core_mailtemplates` ADD `$column` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `email_signature_en`");
		  $tableMailtemplate->update(array($column => $_POST['sitemailtemplates_footer1']));
		}
		elseif(!empty($languageColumn)) {
			$tableMailtemplate->update(array($column => $_POST['sitemailtemplates_footer1']));
		}
	
		Engine_Api::_()->getApi('settings', 'core')->setSetting('signature.' . $language, $_POST['sitemailtemplates_footer1']);
		
		$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
		));
  }

}