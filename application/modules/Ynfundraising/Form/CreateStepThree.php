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
class Ynfundraising_Form_CreateStepOne extends Engine_Form {
	public $_error = array ();
	public function init() {
		$this->setTitle ( 'Create New Campaign' )
		->setAttrib ( 'name', 'ynfundraising_create_step1');
		$user = Engine_Api::_ ()->user ()->getViewer ();
		$user_level = Engine_Api::_ ()->user ()->getViewer ()->level_id;
		$translate = Zend_Registry::get ( 'Zend_Translate' );
		$view = Zend_Registry::get ( 'Zend_View' );
		
		// Campaign name
		$this->addElement ( 'Text', 'title', array (
				'label' => '*Campaign Name',
				'required' => true,
				'filters' => array (
						new Engine_Filter_Censor (),
						'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256' 
						) ) 
				) 
		) );
		
		// Tags
		$this->addElement ( 'Text', 'tags', array (
				'label' => 'Tags (Keywords)',
				'autocomplete' => 'off',
				'description' => 'Separate tags with commas.',
				'filters' => array (
						new Engine_Filter_Censor () 
				) 
		) );
		$this->tags->getDecorator ( "Description" )->setOption ( "placement", "append" );
		
		// Short Description
		$this->addElement ( 'textarea', 'short_description', array (
				'label' => '*Short Description',
				'value' => '' 
		) );
		
		// Main Description
		$allowed_html = Engine_Api::_ ()->authorization ()->getPermission ( $user_level, 'ynfundraising_campaign', 'auth_html' );
		$upload_url = "";
		if (Engine_Api::_ ()->authorization ()->isAllowed ( 'album', $user, 'create' )) {
			$upload_url = Zend_Controller_Front::getInstance ()->getRouter ()->assemble ( array (
					'action' => 'upload-photo' 
			), 'ynfundraising_general', true );
		}
		$theme_advanced_buttons1 = "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,|,charmap,emotions,iespell,media";
		$theme_advanced_buttons2 = "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,fullscreen";
		$theme_advanced_buttons3 = "formatselect,fontselect,fontsizeselect,|,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo";
		$allowed_html = "strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, table, tbody, tr, td, caption";
		$this->addElement ('TinyMce', 'main_description', array (
				'label' => '*Main Description',
				'required' => true,
				'allowEmpty' => false,
				'editorOptions' => array (
						'mode' =>'exact',
			            'elements'=>'main_description',
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
	  //Thumdnail
	  $this->addElement('File', 'thumbnail', array(
        'label' => 'Photo*',
        'title' => $translate->translate('Main image of campaign'),
        'required'=>true,    
          'description' => 'Main image of campaign (jpg, png, gif, jpeg)',
      ));
      $this->thumbnail->getDecorator("Description")->setOption("placement", "append");
      $this->thumbnail->addValidator('Extension', false, 'jpg,png,gif,jpeg');
		
		// Paypal account
		$this->addElement ( 'Text', 'paypal_account', array (
				'label' => '*Your PayPal Account',
				'required' => true,
				'filters' => array (
						new Engine_Filter_Censor () 
				),
				'value' => '0.00',
				'validators' => array (
						array (
								'NotEmpty',
								true 
						),
						array (
								'Float',
								true 
						),
						array (
								'GreaterThan',
								true,
								array (
										0 
								) 
						) 
				) 
				
		) );
		
		// Fundraising goal
		$this->addElement ( 'Text', 'goal', array (
				'label' => 'Fundraising Goal',
				'title' => '',
				'allowEmpty' => false,
				'required' => true,
				'filters' => array (
						new Engine_Filter_Censor () 
				),
				'value' => '0.00',
				'validators' => array (
						array (
								'NotEmpty',
								true 
						),
						array (
								'Float',
								true 
						),
						array (
								'GreaterThan',
								true,
								array (
										0 
								) 
						) 
				)
				 
		) );
		
		// Element: currency
		
		$this->addElement ( 'Select', 'currency', array (
				'label' => 'Currency',
				'multiOptions' => Ynfundraising_Model_DbTable_Currencies::getMultiOptions(),
				'value' => Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.currency', 'USD' ) 
		) );
		
		// Expiry date
		$expiry_date = new Engine_Form_Element_CalendarDateTime ( 'expiry_date' );
		$expiry_date->setLabel ( "Expiry Date" );
		$expiry_date->setAllowEmpty ( false );
		$this->addElement ( $expiry_date );
		
		// List predefined
		$this->addElement('Dummy', 'predefined', array(	
		      'decorators' => array(
			          array('ViewScript', array(
			                'viewScript' => '_addPredefined.tpl',
			                'class'      => 'form element'
			          ))
			      ),
		));
		// Minimum donation amount
		$this->addElement ( 'Text', 'minimum_donate', array (
				'label' => 'Minimum Donation Amount',
				'title' => '',
				'allowEmpty' => false,
				'required' => true,
				'filters' => array (
						new Engine_Filter_Censor () 
				),
				'value' => '0.00',
				'validators' => array (
						array (
								'NotEmpty',
								true 
						),
						array (
								'Float',
								true 
						),
						array (
								'GreaterThan',
								true,
								array (
										0 
								) 
						) 
				)
				 
		) );
		
		// Allow anonymous
		$this->addElement ( 'Checkbox', 'allow_anonymous', array (
				'label' => 'Allow anonymous donation? If donor select anonymous donation then his name and photo are hidden from public',
				'value' => 0,
				'checked' => false 
		) );
		
		// Address
		$this->addElement ( 'Text', 'address', array (
				'label' => 'Location',
				'required' => false,
				'readonly' => true,
				'style'    => 'width: 400px;',
				'description' => $view->htmlLink(array('action' => 'add-location',
				                  'route' => 'ynfundraising_general',
				                  'reset' => true,
								 ), 
								$view->translate('Add address/city/zip/country'), 
								array('class'=>'smoothbox'
                				)),
				'filters' => array (
						new Engine_Filter_Censor () 
				) 
		) );
		$this->address->getDecorator ( "Description" )->setOption ( "placement", "append" )->setEscape(FALSE);
		
		// location
		$this->addElement ( 'Hidden', 'location', array(
			'order' => '1'
		));
		
		// View privacy
		$availableLabels = array (
				'everyone' => 'Everyone',
				'registered' => 'All Registered Members',
				'owner_network' => 'Friends and Networks',
				'owner_member_member' => 'Friends of Friends',
				'owner_member' => 'Friends Only',
				'owner' => 'Just Me' 
		);
		
		$options = ( array ) Engine_Api::_ ()->authorization ()->getAdapter ( 'levels' )->getAllowed ( 'ynfundraising', $user, 'auth_view' );
		$options = array_intersect_key ( $availableLabels, array_flip ( $options ) );
		
		$this->addElement ( 'Select', 'auth_view', array (
				'label' => 'Privacy',
				'description' => 'Who may see this campaign?',
				'multiOptions' => $options,
				'value' => 'everyone' 
		) );
		$this->auth_view->getDecorator ( 'Description' )->setOption ( 'placement', 'append' );
		
		$options = ( array ) Engine_Api::_ ()->authorization ()->getAdapter ( 'levels' )->getAllowed ( 'ynfundraising', $user, 'auth_comment' );
		$options = array_intersect_key ( $availableLabels, array_flip ( $options ) );
		
		// Comment privacy
		$this->addElement ( 'Select', 'auth_comment', array (
				'label' => 'Comment Privacy',
				'description' => 'Who may post comments on this campaign?',
				'multiOptions' => $options,
				'value' => 'everyone' 
		) );
		$this->auth_comment->getDecorator ( 'Description' )->setOption ( 'placement', 'append' );
		
		// Donate privacy
		$options = ( array ) Engine_Api::_ ()->authorization ()->getAdapter ( 'levels' )->getAllowed ( 'ynfundraising', $user, 'auth_donate' );
		$options = array_intersect_key ( $availableLabels, array_flip ( $options ) );
		
		$this->addElement ( 'Select', 'auth_donate', array (
				'label' => 'Donate Privacy',
				'description' => 'Who may donate this campaign?',
				'multiOptions' => $options,
				'value' => 'everyone' 
		) );
		$this->auth_donate->getDecorator ( 'Description' )->setOption ( 'placement', 'append' );
		
		$this->addElement ( 'Button', 'submit', array (
				'label' => 'Create Campaign',
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
	      'prependText' => ' or ',
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
