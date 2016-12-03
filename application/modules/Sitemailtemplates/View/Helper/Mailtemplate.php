<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Mailtemplate.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_View_Helper_Mailtemplate extends Zend_View_Helper_Abstract {

  public function mailtemplate($data = array()) {

    $template_id = 0;
    if(isset($data['template_id'])) {
			$template_id = $data['template_id'];
    }  
   
    $mailTemplate_id = 0;
    if(isset($data['mailtemplate_id'])) {
			$mailTemplate_id = $data['mailtemplate_id'];
    } 

		$publishUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'home'), 'user_general', true);

		$emailNotificationUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->baseUrl().'/members/settings/notifications';

		$siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1)); 

		$description = $this->view->translate("<p><span style='color: #92999c; font-size: x-small; font-family: arial,helvetica,sans-serif;'>If you are a member of <a href='%s' target='_blank' style='text-decoration:none;'>$siteTitle</a>, and would like to choose which emails you receive from us, then please <a href='$emailNotificationUrl' style='text-decoration:none;' target='_blank'>click here</a>.</span><br><span style='color: #92999c; font-size: x-small; font-family: arial,helvetica,sans-serif;'> To continue receiving our emails, please add us to your address book or safe list.</span></p>");

    if(empty($template_id)) {
      //MAKE QUERY
			$sitemailtemplateTable = Engine_Api::_()->getDbTable('templates','sitemailtemplates');
			$tablesitemailtemplatesName = $sitemailtemplateTable->info('name');
			$select = $sitemailtemplateTable->select()->where('active_template = ?',1);
      $resultTemplate = $sitemailtemplateTable->fetchRow($select)->toArray();
    }
    else {
			$resultTemplate = Engine_Api::_()->getItem('sitemailtemplates_templates', $template_id)->toArray();
    }

    //GET THE CURRENT LANGUAGE
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $locale = $view->locale()->getLocale()->__toString();
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $column = 'email_signature_'.$locale;
		$languageColumn = $db->query("SHOW COLUMNS FROM engine4_core_mailtemplates LIKE '$column'")->fetch();
    
    if(!empty($languageColumn)) {
      $signature = $column;
    }
    else {
      $signature = 'email_signature_en';
    }
    
    if(!empty($mailTemplate_id)) {
			$templateObject = Engine_Api::_()->getItem('core_mail_template', $mailTemplate_id);
			if(empty($templateObject->show_signature) && empty($templateObject->$signature) && ($templateObject->type != 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION')) {
				$textFooter = sprintf($description, $publishUrl);
			}
			elseif($templateObject->type != 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION') {
				$textFooter = $templateObject->$signature;
			}
      else {
        $textFooter = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.footer1', $description);
      }
    }
    else {
      $textFooter = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.footer1', $description);
    }
   
    $siteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'home'), 'user_general', true);

    $data_html = array('bodyHtmlTemplate' => $data['bodyHtmlTemplate'],'siteUrl' => $siteUrl,'textofFooter' => $textFooter);
    $final_data = array_merge($resultTemplate,$data_html);

    return $this->view->partial(
                    '_set-templates.tpl', 'sitemailtemplates',$final_data);
  }
}