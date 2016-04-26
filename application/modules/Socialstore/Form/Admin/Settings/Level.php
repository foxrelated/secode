<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Level.php
 * @author     Long Le
 */
class Socialstore_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("STORE_FORM_ADMIN_LEVEL_DESCRIPTION");
	$currency = Socialstore_Api_Core::getDefaultCurrency();
     //Element: view
    $this->addElement('Radio', 'store_view', array(
      'label' => 'Allow Viewing of Stores?',
      'description' => 'Do you want to let members view stores? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all stores, even private ones.',
        1 => 'Yes, allow viewing of stores.',
        0 => 'No, do not allow stores to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->store_view->options[2]);
    }

    $this->addElement('Radio', 'product_view', array(
      'label' => 'Allow Viewing of Products?',
      'description' => 'Do you want to let members view products? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all products, even private ones.',
        1 => 'Yes, allow viewing of products.',
        0 => 'No, do not allow products to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->product_view->options[2]);
    }
    
    if( !$this->isPublic() ) {

      // Element: create
      $this->addElement('Radio', 'store_create', array(
        'label' => 'Allow Creation of Stores?',
        'description' => 'STORE_FORM_ADMIN_LEVEL_CREATE_DESCRIPTION',
        'multiOptions' => array(
          1 => 'Yes, allow creation of stores.',
          0 => 'No, do not allow stores to be created.'
        ),
        'value' => 1,
      ));

      $this->addElement('Radio', 'product_create', array(
        'label' => 'Allow Creation of Products?',
        'description' => 'PRODUCT_FORM_ADMIN_LEVEL_CREATE_DESCRIPTION',
        'multiOptions' => array(
          1 => 'Yes, allow creation of products.',
          0 => 'No, do not allow products to be created.'
        ),
        'value' => 1,
      ));
      // Element: edit
      $this->addElement('Radio', 'store_edit', array(
        'label' => 'Allow Editing of Stores?',
        'description' => 'Do you want to let members edit stores? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          1 => 'Yes, allow members to edit their own stores.',
          0 => 'No, do not allow members to edit their stores.',
        ),
      ));
      if( !$this->isModerator() ) {
        unset($this->store_edit->options[2]);
      }
       $this->addElement('Radio', 'product_edit', array(
        'label' => 'Allow Editing of Products?',
        'description' => 'Do you want to let members edit products? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          1 => 'Yes, allow members to edit their own products.',
          0 => 'No, do not allow members to edit their products.',
        ),
      ));
      if( !$this->isModerator() ) {
        unset($this->product_edit->options[2]);
      }
      
/*
      // Element: delete
      $this->addElement('Radio', 'store_delete', array(
        'label' => 'Allow Deletion of Stores?',
        'description' => 'Do you want to let members delete stores? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          1 => 'Yes, allow members to delete their own stores.',
          0 => 'No, do not allow members to delete their stores.',
        ),
      )); */
          $this->addElement('Radio', 'product_delete', array(
        'label' => 'Allow Deletion of Products?',
        'description' => 'Do you want to let members delete products? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          1 => 'Yes, allow members to delete their own products.',
          0 => 'No, do not allow members to delete their products.',
        ),
      ));
      // Element: comment
      $this->addElement('Radio', 'store_comment', array(
        'label' => 'Allow Commenting on Stores?',
        'description' => 'Do you want to let members of this level comment on stores?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all stores, including private ones.',
          1 => 'Yes, allow members to comment on stores.',
          0 => 'No, do not allow members to comment on stores.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->store_comment->options[2]);
      }
      $this->addElement('Radio', 'product_comment', array(
        'label' => 'Allow Commenting on Products?',
        'description' => 'Do you want to let members of this level comment on products?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all products, including private ones.',
          1 => 'Yes, allow members to comment on products.',
          0 => 'No, do not allow members to comment on products.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->product_comment->options[2]);
      }
      $this->addElement('Radio', 'product_buy', array(
        'label' => 'Allow users to buy products?',
        'description' => '',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => 1,
      ));
	  $this->addElement('Radio', 'store_approve', array(
        'label' => 'Approve Store?',
        'description' => 'Automatically approve store?',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => 1,
      ));
      $this->addElement('Radio', 'product_approve', array(
        'label' => 'Approve Product?',
        'description' => 'Automatically approve product?',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => 1,
      ));

       $this->addElement('Text', 'store_pubfee', array(
      'label' => 'Fee For Publishing Store',
      'description' => 'Set 0 to free.',
       'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
        ),
      'value' => 0,
       ));
              $this->addElement('Text', 'product_pubfee', array(
      'label' => 'Fee For Publishing Product',
      'description' => 'Set 0 to free.',
      'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
        ),
      'value' => 0,
       ));
    $this->addElement('Text', 'store_ftedfee', array(
      'label' => 'Fee To Feature Store',
      'description' => 'Set 0 to free.',
      'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
        ),
      'value' => 0,
    ));
   	  $this->addElement('Text', 'product_ftedfee', array(
      'label' => 'Fee To Feature Product',
      'description' => 'Set 0 to free.',
   	  'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
      ),
      'value' => 0,
    ));   
	if(Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection())
	{
		 //Minh Add - Start
	    $this->addElement('Text', 'product_gdafee', array(
	      'label' => 'Fee To Use Deal Request',
	      'description' => 'Set 0 to free.',
		    'validators' => array(
	          array('Int', true),
	          new Engine_Validate_AtLeast(0),
	        ),
	      'value' => 0,
	    ));   
	    //--End 
	}
       $this->addElement('text', 'product_com', array(
        'label' => 'Product commission (%)?',
        'description' => '',
       'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
        ),
        'value' => 0,
      ));


      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'store_authcom', array(
        'label' => 'Stores Comment Options',
        'description' => 'STORE_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
        'description' => '',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network','owner_member_member', 'owner_member', 'owner')
      ));
       // Element: auth_comment
      $this->addElement('MultiCheckbox', 'product_authcom', array(
        'label' => 'Products Comment Options',
        'description' => 'PRODUCT_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
        'description' => '',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network','owner_member_member', 'owner_member', 'owner')
      ));
    }
  }
}