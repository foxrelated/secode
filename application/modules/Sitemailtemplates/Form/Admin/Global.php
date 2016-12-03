<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_Form_Admin_Global extends Engine_Form {

  public function init() {
    
    $this->loadDefaultDecorators();

    $textFlag = Zend_Controller_Front::getInstance()->getRequest()->getParam('textFlag', 0);   

    $editorOptions = array(
        'plugins' => 'emotions,preview,table,layer,style,xhtmlxtras,paste,spellchecker,iespell,fullscreen',
        'theme_advanced_buttons1' => "fullscreen,preview,code,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|, link,unlink,anchor,charmap,image,|,hr,removeformat,cleanup",
        'theme_advanced_buttons2' => "bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,|,table",
        'theme_advanced_buttons3' => "formatselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,spellchecker,iespell,emotions");

    // create an object for view
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));

    $description = sprintf(Zend_Registry::get('Zend_Translate')->_('These settings affect all members in your community.'));
    
    $this->getDecorator('Description')->setOption('escape', false);
    $this
				->setTitle('Global Settings')
				->setDescription($description);
    $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
    $settings = $coreSettingsApi;
    
    $this->addElement('Text', 'sitemailtemplates_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'required' => true,
        'value' => $coreSettingsApi->getSetting('sitemailtemplates.lsettings'),
    ));

    if (APPLICATION_ENV == 'production') {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
          'description' => 'System Mode',
          'value' => 1,
      ));
    } else {
      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $publishUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'home'), 'user_general', true);

    $emailNotificationUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $view->baseUrl().'/members/settings/notifications';

		$description = Zend_Registry::get('Zend_Translate')->_("<p><span style='color: #92999c; font-size: x-small; font-family: arial,helvetica,sans-serif;'>If you are a member of <a href='%s' target='_blank' style='text-decoration:none;'>$siteTitle</a> and do not want to receive these emails from us in the future, then please <a href='$emailNotificationUrl' style='text-decoration:none;' target='_blank'>click here</a>.</span><br><span style='color: #92999c; font-size: x-small; font-family: arial,helvetica,sans-serif;'> To continue receiving our emails, please add us to your address book or safe list.</span></p>");
		$description= sprintf($description, $publishUrl);

    $mailTemplateLink = $view->url(array('module' => 'sitemailtemplates','controller' => 'mail', 'action' => 'templates'), 'admin_default', true);
    
    if( empty( $textFlag ) ) {
			$textLinkFlag = $view->url(array('module' => 'sitemailtemplates','controller' => 'settings', 'textFlag' => 1), 'admin_default', true);
			$textDescription = sprintf("Enter the Email Signature for the emails that are sent to your members. (Note: This Signature <b>will only</b> be sent for emails other than the ones listed for the “<b>Choose Message</b>” field of “<a href='%1s'>Mail Templates</a>” section. If your email signature is not sent in below format, then <a href='%2s'> click here </a> for the compatible Text input box.)", $mailTemplateLink, $textLinkFlag);
    }
    else {
			$textLinkFlag = $view->url(array('module' => 'sitemailtemplates','controller' => 'settings','textFlag' => 0), 'admin_default', true);
			$textDescription = sprintf("Enter the Email Signature for the emails that are sent to your members. (Note: This Signature <b>will only</b> be sent for emails other than the ones listed for the “<b>Choose Message</b>” field of “<a href='%1s'>Mail Templates</a>” section. If you want to switch back to rich TinyMCE editor then <a href='%2s'> click here </a>.)", $mailTemplateLink, $textLinkFlag);
    }

		$this->addElement('Textarea', 'sitemailtemplates_footer1', array(
				'label' => 'Email Signature',
        'description' => $textDescription,
			  'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.footer1', $description),
			));
    $this->sitemailtemplates_footer1->addDecorator( 'Description' , array ( 'placement' => Zend_Form_Decorator_Abstract::PREPEND , 'escape' => false ) ) ;


//     // Element: body
// 		$this->addElement('TinyMce', 'sitemailtemplates_footer1', array(
// 			'label' => 'Email Signature',
//       'description' => $textDescription,
// 			'editorOptions' => $editorOptions,
// 			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.footer1', $description),
//      
// 		));

    // Add submit button
    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true
    ));

    $this->addElement('Radio', 'sitemailtemplates_check_setting', array(
        'label' => 'Activate Rich Emails',
        'description' => '',
        'multiOptions' => array(
            1 => 'Yes, I have tested the settings of this plugin. Make all outgoing emails rich.',
            0 => 'No, I am still testing and configuring the settings of this plugin. Send all outgoing emails as plain text, i.e., like what would happen without this plugin.'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemailtemplates.check.setting', 0),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}