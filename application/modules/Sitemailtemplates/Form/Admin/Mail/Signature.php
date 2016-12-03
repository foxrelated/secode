<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Templates.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_Form_Admin_Mail_Signature extends Engine_Form {

  public function init()
  {
  
		$this
		->setTitle('Email Signature');
		
		$this
		->setAttribs(array(
						'id' => 'signature_form',
		));
  
    $language = Zend_Controller_Front::getInstance()->getRequest()->getParam('language');
  
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $publishUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'home'), 'user_general', true);

    $emailNotificationUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $view->baseUrl().'/members/settings/notifications';

    $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));

		$editorOptions = Engine_Api::_()->seaocore()->tinymceEditorOptions();
    
		$description = Zend_Registry::get('Zend_Translate')->_("<p><span style='color: #92999c; font-size: x-small; font-family: arial,helvetica,sans-serif;'>If you are a member of <a href='%s' target='_blank' style='text-decoration:none;'>$siteTitle</a> and do not want to receive these emails from us in the future, then please <a href='$emailNotificationUrl' style='text-decoration:none;' target='_blank'>click here</a>.</span><br><span style='color: #92999c; font-size: x-small; font-family: arial,helvetica,sans-serif;'> To continue receiving our emails, please add us to your address book or safe list.</span></p>");
		$description= sprintf($description, $publishUrl);

		$column = 'email_signature_'.$language;
		$db = Engine_Db_Table::getDefaultAdapter();
		$languageColumn = $db->query("SHOW COLUMNS FROM engine4_core_mailtemplates LIKE '$column'")->fetch();
		
		if(!empty($languageColumn)) {
			if(Engine_Api::_()->getApi('settings', 'core')->getSetting('signature.' . $language) == 1) {
				$coretemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
				$signature = $coretemplateTable->select()
												->from($coretemplateTable->info('name'), $column)
												->query()->fetchColumn();
				$signature = $signature;
			}
			else {
			  $signature = Engine_Api::_()->getApi('settings', 'core')->getSetting('signature.' . $language, $description);
			}
		}
		else {
		  $signature = Engine_Api::_()->getApi('settings', 'core')->getSetting('signature.' . $language, $description);
		}
		
    // Element: body
		$this->addElement('TinyMce', 'sitemailtemplates_footer1', array(
			//'label' => $title,
			'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions(),
			'value' => $signature,
		));
		$this->getElement('sitemailtemplates_footer1')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

    // Buttons
    $this->addElement('Button', 'save_signature', array(
      'label' => 'Save Changes',
      'onclick' => 'saveSignature()',
      //'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
    
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('save_signature', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');
    
  }
}