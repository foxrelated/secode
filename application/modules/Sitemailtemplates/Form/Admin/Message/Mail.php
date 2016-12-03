<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Mail.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_Form_Admin_Message_Mail extends Engine_Form
{

  public function init()
  {

		$this
				->setAttribs(array(
								'id' => 'mail_form',
				));

    //UPLOAD PHOTO URL
    $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitemailtemplates', 'controller' => 'message', 'action' => "upload-photo"), 'admin_default', true);

		$description = $this->getTranslator()->translate(
						'Using this form, you will be able to send an email out to your members by choosing robust targeting options for who should receive your email based on “Member Levels”, “Profile Types” and “Networks”, depending on which of these are being used on your site. You can also choose to send email to particular members by choosing their names from the auto-suggest box. Emails are sent out using a queue system, so they will be sent out over time. An email will be sent to you when all emails have been sent.');
			
		$editorOptions =Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url); 

		$getLimitFlag = false;
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$mailtemSiteLim = $settings->getSetting('mailtem.site.lim');
		$mailQueueLimit = $settings->getSetting('mailtem.queue.limit');
		$mailSubjectLimit = $settings->getSetting('mailtem.subject.limit');
		$getTotleLimit = $mailQueueLimit + $mailSubjectLimit;

		$description = vsprintf($description, array(
			'http://www.socialengine.net/support/documentation/article?q=178&question=Admin-Panel---Manage--Email-All-Members',
		));
		$description .= '<div class="tip"><span>NOTE: If you select "Everyone" in any of the 3 fields below: \'Member Levels\', \'Profile Types\' or \'Networks Selection\', then clicking on "Send Emails" button will send that email to "Everyone" or all the members of your site.<br />
Now, if you want to send email only to the members of a specific member level or profile type or network, then you will have to leave the other 2 fields blank..</span></div>';
		// Decorators
		$this->loadDefaultDecorators();
		$this->getDecorator('Description')->setOption('escape', false);

		$this
			->setTitle('Email Members')
			->setDescription($description);


		$settings_mail = Engine_Api::_()->getApi('settings', 'core')->core_mail;

		if( !@$settings_mail['queueing'] ) {
			$this->addElement('Radio', 'queueing', array(
				'label' => 'Utilize Mail Queue', 
        'order' => 1,
				'description' => 'Mail queueing permits the emails to be sent out over time, preventing your mail server
						from being overloaded by outgoing emails.  It is recommended you utilize mail queueing for large email
						blasts to help prevent negative performance impacts on your site.',
				'multiOptions' => array(
					1 => 'Utilize Mail Queue (recommended)',
					0 => 'Send all emails immediately (only recommended for less than 100 recipients).',
				),
				'value' => 1,
			));
		}

    $this->addElement('Text', 'from_address', array(
      'label' => 'From',
      'order' => 2,
      'value' => (!empty($settings_mail['from']) ? $settings_mail['from'] : 'noreply@' . $_SERVER['HTTP_HOST']),
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        'EmailAddress',
      )
    ));
    $this->from_address->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);

    $this->addElement('Text', 'from_name', array(
      'label' => 'From (name)',
      'order' => 3,
      'required' => true,
      'allowEmpty' => false,
      'value' => (!empty($settings_mail['name']) ? $settings_mail['name'] : 'Site Administrator'),
    ));

    $this->addElement('Radio', 'sitemailtemplates_send_mail', array(
        'label' => 'Email Who?',
        'order' => 4,
        'multiOptions' => array(
          1 => 'Group of Members',
          0 => 'Particular Members',
        ),
        'value' => 1,
        'onclick' => 'showMemeberlevel(this.value)'
      ));

    $member_levels = array();
    $public_level = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel();
    foreach( Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $row ) {
      if( $public_level->level_id != $row->level_id ) {
        $member_count = $row->getMembershipCount();

        if( null !== ($translate = $this->getTranslator()) ) {
          $title = $translate->translate($row->title);
        } else {
          $title = $row->title;
        }


        $member_levels[$row->level_id] = $title . ' (' . $member_count . ')';
      }
    }

		$sitemailtemplates_api = Engine_Api::_()->getApi('mail', 'sitemailtemplates');
		$levels_prepared = $sitemailtemplates_api->getMemberLevels();
		$this->addElement('Multiselect', 'member_levels', array(
				'label' => 'Member Levels',
        'order' => 5,
				'description' => 'Select the member levels to which your email should be sent. Use CTRL-click to select or deselect multiple Member Levels.',
				'multiOptions' => $levels_prepared,
				'value' => '',
		));
		
		$topLevelOptions = $sitemailtemplates_api->getProfileTypes(0);
		$topLevelOptionsCount = Count($topLevelOptions);
		if($topLevelOptionsCount > 2){
			$this->addElement('Multiselect', 'profile_types', array(
					'label' => 'Profile Types',
          'order' => 6,
					'description' => 'Select the profile types to which your email should be sent. Use CTRL-click to select or deselect multiple Profile Types.',
					'multiOptions' => $topLevelOptions,
					'value' => '',
			));
		}

		$networksOptions = $sitemailtemplates_api->getNetworks();

		$this->addElement('Multiselect', 'networks', array(
				'label' => 'Networks Selection',
        'order' => 7,
				'description' => 'Select the networks, members of which should receive your email. Use CTRL-click to select or deselect multiple Networks.',
				'multiOptions' => $networksOptions,
				'value' => '',
		));

   // init to
    $this->addElement('Text', 'user_ids',array(
        'label'=>'Member to Email',
         'description' => 'Start typing the name of the member in the auto-suggest below.',
         'order' => 8,
        'autocomplete'=>'off'));
    Engine_Form::addDefaultDecorators($this->user_ids);

    // Init to Values
    $this->addElement('Hidden', 'toValues', array(
            'order' => 9,
    ));
    Engine_Form::addDefaultDecorators($this->toValues);

    $this->addElement('Text', 'subject', array(
      'label' => 'Subject',
      'order' => 10,
      'required' => true,
      'allowEmpty' => false,
    ));

    $this->addElement('TinyMce', 'body', array(
      'label' => 'Message Body',
      'order' => 11,
      'editorOptions' => $editorOptions,
      'required' => true,
      'allowEmpty' => false,
    ));
    $this->body->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));


    $this->addElement('Textarea', 'body_text', array(
      'label' => 'Body (text)',
    ));

    $this->addDisplayGroup(array('body_text'), 'advanced', array(
      'decorators' => array(
        'FormElements',
        array('Fieldset', array('style' => 'display:none;')),
      ),
    ));

		if( !empty($mailtemSiteLim) && ( empty($mailQueueLimit) || empty($mailSubjectLimit) || ((int)$mailtemSiteLim !== (int)$getTotleLimit) ) ) {
			$getLimitFlag = true;
		}
		$this->addElement('Hidden', 'temLimit', array('order' => '50', 'value' => $getLimitFlag));

		$filesize = (int) ini_get('upload_max_filesize') * 1024 * 1024;
		$description = Zend_Registry::get('Zend_Translate')->_('Browse and choose a file. Maximum permissible size: %s KB and allowed file types: pdf, txt, ps, rtf, epub, odt, odp, ods, odg, odf, sxw, sxc, sxi, sxd, doc, ppt, pps, xls, docx, pptx, ppsx, xlsx, tif, tiff, jpeg, jpg, png, bmp, zip, tar, json, html, xml');
		$description = sprintf($description, $filesize);

		$this->addElement('File', 'fileupload', array(
				'label' => 'Attach File',
         'order' => 12,
				'required' => false,	
        'empty' => true,
				'description' =>  $description
		));
		$this->fileupload->getDecorator('Description')->setOption('placement', 'append');

    // init submit
    $this->addElement('Button', 'send_mail', array(
      'label' => 'Send Emails',
      'onclick' => 'sendEmail()',
      'order' => 13,
      //'type' => 'submit',
      'ignore' => true,
    ));
  }

}