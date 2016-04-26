<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Fundraising
 * @copyright  Copyright 2012 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: CreateStep1.php
 * @author     Minh Nguyen
 */
class Ynfundraising_Form_CreateStepFour extends Engine_Form {
	public function init() {
		$this->setAttrib('class','global_form')->setAttrib ( 'name', 'ynfundraising_create_step_four');
		$this->setTitle('Contact Information');
		$user = Engine_Api::_ ()->user ()->getViewer ();
		$user_level = Engine_Api::_ ()->user ()->getViewer ()->level_id;
		// Element: full name
		$this->addElement ( 'Text', 'name', array (
					'label' => 'Full Name',
					'required' => false,
					'style'	=> 'width: 250px',
					'filters' => array (
							new Engine_Filter_Censor (),
							'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256'
						) )
				)
		) );

		// Element: phone
		$this->addElement ( 'Text', 'phone', array (
				'label' => 'Phone',
				'required' => false,
				'style'	=> 'width: 250px',
				'filters' => array (
						new Engine_Filter_Censor (),
						'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256'
						) )
				)
		) );

		// Element: email address
		$this->addElement ( 'Text', 'email', array (
				'label' => 'Email Address',
				'required' => false,
				'style'	=> 'width: 250px',
				'filters' => array (
						new Engine_Filter_Censor (),
						'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256'
						) )
				),
				'validators' => array(
			        array('NotEmpty', true),
			        array('EmailAddress', true),
			    )
		) );

		// Element: country
		$this->addElement ( 'Select', 'country', array (
				'label' => 'Country',
				'multiOptions' => Ynfundraising_Model_DbTable_Countries::getMultiOptions(),
				'value' => Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.country', 'VNM' )
		) );

		// Element: state
		$this->addElement ( 'Text', 'state', array (
				'label' => 'State',
				'required' => false,
				'style'	=> 'width: 250px',
				'filters' => array (
						new Engine_Filter_Censor (),
						'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256'
						) )
				)
		) );

		// Element: city
		$this->addElement ( 'Text', 'city', array (
				'label' => 'City',
				'required' => false,
				'style'	=> 'width: 250px',
				'filters' => array (
						new Engine_Filter_Censor (),
						'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256'
						) )
				)
		) );

		// Element: city
		$this->addElement ( 'Text', 'street', array (
				'label' => 'Street',
				'required' => false,
				'style'	=> 'width: 250px',
				'filters' => array (
						new Engine_Filter_Censor (),
						'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256'
						) )
				)
		) );

		// About Me
		$allowed_html = Engine_Api::_ ()->authorization ()->getPermission ( $user_level, 'ynfundraising_campaign', 'auth_html' );
		$upload_url = "";
		if (Engine_Api::_ ()->authorization ()->isAllowed ( 'album', $user, 'create' )) {
			$upload_url = Zend_Controller_Front::getInstance ()->getRouter ()->assemble ( array (
					'action' => 'upload-photo'
			), 'ynfundraising_general', true );
		}
		$theme_advanced_buttons1 = "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,|,charmap,emotions,iespell,media";
		$theme_advanced_buttons2 = "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,fullscreen,";
		$theme_advanced_buttons3 = "formatselect,fontselect,fontsizeselect,|,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo";
		$this->addElement ('TinyMce', 'about_me', array (
				'label' => 'About Me',
				'required' => false,
				'allowEmpty' => false,
				'editorOptions' => array (
						'mode' =>'exact',
			            'elements'=>'about_me',
			            'upload_url' => $upload_url,
						'theme_advanced_buttons1' => $theme_advanced_buttons1,
						'theme_advanced_buttons2' => $theme_advanced_buttons2,
						'theme_advanced_buttons3' => $theme_advanced_buttons3,
						'toolbar1' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
						'toolbar2' => "print preview media | forecolor backcolor emoticons",
				),

				'filters' => array (
						new Engine_Filter_Censor (),
						new Engine_Filter_Html ( array (
								'AllowedTags' => $allowed_html
						) )
				)
		) );

		$this->addElement ( 'Button', 'submit', array (
				'label' => 'Save Changes',
				'type' => 'submit',
				'ignore' => true,
				'onclick' => 'removeSubmit()',
				'decorators' => array(
		        'ViewHelper',
		        ),
		));
		$this->addElement('Cancel', 'cancel', array(
	      'label' => 'cancel',
	      'link' => true,
	      'prependText' => Zend_Registry::get('Zend_Translate')->_('or '),
	      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'create'), 'ynfundraising_general', true),
	      'onclick' => '',
	      'decorators' => array(
	        'ViewHelper'
	      )
	    ));
	     // DisplayGroup: buttons
        $this->addDisplayGroup(array(
          'submit',
          'cancel',
        ), 'buttons', array(
          'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
          ),
        ));
	}
}
