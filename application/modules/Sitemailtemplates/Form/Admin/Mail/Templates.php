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
class Sitemailtemplates_Form_Admin_Mail_Templates extends Core_Form_Admin_Mail_Templates {

  public function init() {

    parent::init();
    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $template = Zend_Controller_Front::getInstance()->getRequest()->getParam('template', '1');
    $textFlag = Zend_Controller_Front::getInstance()->getRequest()->getParam('textFlag', 0);
    $textFlagofSig = Zend_Controller_Front::getInstance()->getRequest()->getParam('signature_editor', 0);

    $languageOfUrl = preg_replace('/[^a-zA-Z_-]/', '', Zend_Controller_Front::getInstance()->getRequest()->getParam('language', 'en'));
    if (empty($textFlag)) {
      $textLinkFlag = $view->url(array('controller' => 'mail', 'action' => 'templates', 'textFlag' => 1, 'signature_editor' => $textFlagofSig, 'template' => $template, 'language' => $languageOfUrl), 'admin_default', true);
      $textDescription = sprintf("Enter the message body below. (Note: If your message is not sent in below format, then <a href='%s'> click here </a> for the compatible Text input box.)", $textLinkFlag);
    } else {
      $textLinkFlag = $view->url(array('controller' => 'mail', 'action' => 'templates', 'textFlag' => 0, 'signature_editor' => $textFlagofSig, 'template' => $template, 'language' => $languageOfUrl), 'admin_default', true);
      $textDescription = sprintf("Enter the message body below. (Note: If you want to switch back to rich TinyMCE editor then <a href='%s'> click here </a>.)", $textLinkFlag);
    }

    if (empty($textFlagofSig)) {
      $textLinkFlag = $view->url(array('controller' => 'mail', 'action' => 'templates', 'textFlag' => $textFlag, 'signature_editor' => 1, 'template' => $template, 'language' => $languageOfUrl), 'admin_default', true);
      $textDescriptionOfSign = sprintf("Enter the email signature below. (Note: If your message is not sent in below format, then <a href='%s'> click here </a> for the compatible Text input box.)", $textLinkFlag);
    } else {
      $textLinkFlag = $view->url(array('controller' => 'mail', 'action' => 'templates', 'textFlag' => $textFlag, 'signature_editor' => 0, 'template' => $template, 'language' => $languageOfUrl), 'admin_default', true);
      $textDescriptionOfSign = sprintf("Enter the email signature below. (Note: If you want to switch back to rich TinyMCE editor then <a href='%s'> click here </a>.)", $textLinkFlag);
    }

    $sitemailtemplateTable = Engine_Api::_()->getDbTable('templates', 'sitemailtemplates');
    $tablesitemailtemplatesName = $sitemailtemplateTable->info('name');
    $select = $sitemailtemplateTable->select()
            ->from($tablesitemailtemplatesName, array('template_id', 'template_title', 'active_template'));
    $resultTemplates = $sitemailtemplateTable->fetchAll($select);

    $templatesArray = array();
    foreach ($resultTemplates as $templates) {
      $templatesArray[$templates->template_id] = $templates->template_title;
    }

    $description = sprintf(Zend_Registry::get('Zend_Translate')->_('Various notification emails are sent to your members as they interact with the community. Use this form to customize the content of these emails. Any changes you make here will only be saved after you click the "Save Changes" button at the bottom of the form.<br />Note: This page is a duplicate of the "Settings" > "Mail Templates" section, with enhancements that enable you to send rich content in email message body, and allow you to choose if template design from "Manage Templates" should apply to a particular message type.'));

    $this->getDecorator('Description')->setOption('escape', false);

    // Languages
    $localeObject = Zend_Registry::get('Locale');
    $translate = Zend_Registry::get('Zend_Translate');
    $languageList = $translate->getList();

    $languages = Zend_Locale::getTranslationList('language', $localeObject);
    $territories = Zend_Locale::getTranslationList('territory', $localeObject);

    $localeMultiOptions = array();
    foreach (/* array_keys(Zend_Locale::getLocaleList()) */ $languageList as $key) {
      $languageName = null;
      if (!empty($languages[$key])) {
        $languageName = $languages[$key];
      } else {
        $tmpLocale = new Zend_Locale($key);
        $region = $tmpLocale->getRegion();
        $language = $tmpLocale->getLanguage();
        if (!empty($languages[$language]) && !empty($territories[$region])) {
          $languageName = $languages[$language] . ' (' . $territories[$region] . ')';
        }
      }
      $url_page = $view->url(array('module' => 'sitemailtemplates', 'controller' => 'mail', 'action' => 'edit-signature', 'language' => $key), 'admin_default', true);
      $final_url = sprintf("If you want to save email signature of all mail templates those are defined in \"Choose Message\" drop-down for $languageName languge, then <a class=\"smoothbox\" href='%s'> click here </a>. (Note: This setting will overwrite all email signatures for all the messages in this language. If you want to change  email signature only for a particular message then you can do that manually choosing that message.)", $url_page);
      $description .= '<div class="tip"><span>' . $final_url . '</span></div>';
    }

    $this
            ->setDescription($description);

    $this->removeElement('template');

    // Element: template_id
    $this->addElement('Select', 'template', array(
        'label' => 'Choose Message',
        'order' => 1,
        'onchange' => 'javascript:fetchEmailTemplate(this.value);',
        'ignore' => true
    ));

    $template_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('template', 1);

    $coreMailtemplatesTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
    $selectCoretemplateTable = $coreMailtemplatesTable->select()->where('mailtemplate_id =?', $template_id);
    $resultCoretemplate = $coreMailtemplatesTable->fetchRow($selectCoretemplateTable);
    $this->template->getDecorator("Description")->setOption("placement", "append");
    $translate = Zend_Registry::get('Zend_Translate');
    foreach ($coreMailtemplatesTable->fetchAll() as $mailTemplate) {
      if ($mailTemplate->type != 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION') {
        $title = $translate->_(strtoupper("_email_" . $mailTemplate->type . "_title"));
        $this->template->addMultiOption($mailTemplate->mailtemplate_id, $title);
      }
    }

    $this->removeElement('body');
    $this->removeElement('bodyhtml');

    $this->addElement('Textarea', 'body_description', array(
        'label' => 'Message Body',
        'description' => $textDescription,
            //'value' => $description
    ));
    $this->body_description->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

    // Element: body
    $this->addElement('Textarea', 'body', array(
        'label' => 'Message Body',
        'description' => $textDescription,
    ));
    $this->body->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $publishUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'home'), 'user_general', true);

    $emailNotificationUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/members/settings/notifications';

    $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));


    //GET THE CURRENT LANGUAGE
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $locale = $view->locale()->getLocale()->__toString();

    $db = Engine_Db_Table::getDefaultAdapter();
    $column = 'email_signature_' . $locale;
    $languageColumn = $db->query("SHOW COLUMNS FROM engine4_core_mailtemplates LIKE '$column'")->fetch();

    if (!empty($languageColumn)) {
      $signature = $column;
    } else {
      $signature = 'email_signature_en';
    }

    $templateObject = Engine_Api::_()->getItem('core_mail_template', $template);

    if (empty($templateObject->show_signature)) {
      $description = Zend_Registry::get('Zend_Translate')->_("<p><span style='color: #92999c; font-size: x-small; font-family: arial,helvetica,sans-serif;'>If you are a member of <a href='%s' target='_blank' style='text-decoration:none;'>$siteTitle</a> and do not want to receive these emails from us in the future, then please <a href='$emailNotificationUrl' style='text-decoration:none;' target='_blank'>click here</a>.</span><br><span style='color: #92999c; font-size: x-small; font-family: arial,helvetica,sans-serif;'> To continue receiving our emails, please add us to your address book or safe list.</span></p>");
    }
    else
      $description = $templateObject->$signature;
    
    $description = sprintf($description, $publishUrl);

    if (count($resultCoretemplate)) {
      if ($resultCoretemplate->type != 'header' && $resultCoretemplate->type != 'footer' && $resultCoretemplate->type != 'header_member' && $resultCoretemplate->type != 'footer_member') {
        // Element: body
        $this->addElement('Textarea', 'sitemailtemplates_footer1_editor', array(
            'label' => 'Email Signature',
            'description' => $textDescriptionOfSign,
            'value' => $description
        ));
        $this->sitemailtemplates_footer1_editor->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Textarea', 'sitemailtemplates_footer1', array(
            'label' => 'Email Signature',
            'description' => $textDescriptionOfSign,
            'value' => $description
        ));
        $this->sitemailtemplates_footer1->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
      }
    }

    if (count($resultCoretemplate)) {
      if ($resultCoretemplate->type != 'header' && $resultCoretemplate->type != 'footer' && $resultCoretemplate->type != 'header_member' && $resultCoretemplate->type != 'footer_member') {
        $this->addElement('Select', 'template_id', array(
            'label' => 'Email Template',
            'description' => 'Select the email template that you want to activate for this message.',
            'multiOptions' => $templatesArray,
        ));
      }
    }

    $this->removeElement('submit');

    if (count($resultCoretemplate)) {
      if ($resultCoretemplate->type != 'header' && $resultCoretemplate->type != 'footer' && $resultCoretemplate->type != 'header_member' && $resultCoretemplate->type != 'footer_member') {
        $this->addElement('Checkbox', 'enable_template', array(
            'label' => 'Activate above email template for this message. (If you un-select this, then the email notifications going out for this message will be simple text emails, and not rich emails.)',
            'description' => 'Activate Above Email Template for this Message',
        ));
      }
    }

    // Element: submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
    ));
  }

}