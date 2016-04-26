<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Level.php
 * @author     Minh Nguyen
 */
class Groupbuy_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("GROUPBUY_FORM_ADMIN_LEVEL_DESCRIPTION");
    $translate = Zend_Registry::get('Zend_Translate');
    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Deals?',
      'description' => 'Do you want to let members view deals? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all deals, even private ones.',
        1 => 'Yes, allow viewing of deals.',
        0 => 'No, do not allow deals to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

    if( !$this->isPublic() ) {

      // Element: create
      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Creation of Deals?',
        'description' => 'GROUPBUY_FORM_ADMIN_LEVEL_CREATE_DESCRIPTION',
        'multiOptions' => array(
          1 => 'Yes, allow creation of deals.',
          0 => 'No, do not allow deals to be created.'
        ),
        'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'edit', array(
        'label' => 'Allow Editing of Deals?',
        'description' => 'Do you want to let members edit deals? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to edit all deals.',
          1 => 'Yes, allow members to edit their own deals.',
          0 => 'No, do not allow members to edit their deals.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
        'label' => 'Allow Deletion of Deals?',
        'description' => 'Do you want to let members delete deals? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to delete all deals.',
          1 => 'Yes, allow members to delete their own deals.',
          0 => 'No, do not allow members to delete their deals.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Deals?',
        'description' => 'Do you want to let members of this level comment on deals?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all deals, including private ones.',
          1 => 'Yes, allow members to comment on deals.',
          0 => 'No, do not allow members to comment on deals.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }
      $this->addElement('Radio', 'can_buy_deal', array(
        'label' => 'Allow users to buy deals?',
        'description' => '',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => 0,
      ));
	  
      $this->addElement('Radio', 'can_sell_deal', array(
        'label' => 'Allow users to publish deals?',
        'description' => '',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => 0,
      ));
	  
       $this->addElement('text', 'commission', array(
        'label' => 'Commission for sold deals (%)?',
        'description' => '',
        'value' => 5,
      ));
	  
       $this->addElement('Radio', 'free_fee', array(
        'label' => 'Fee for Featuring Deals?',
        'description' => '',
        'multiOptions' => array(
          1 => 'No, there is not a fee to feature deals.',
          0 => 'Yes, there is a fee to feature deals.'
        ),
        'value' => 0,
      ));
      $this->addElement('Radio', 'free_display', array(
        'label' => 'Fee for Publishing Deals?',
        'description' => '',
        'multiOptions' => array(
          1 => 'No, there is not a fee to publish deals.',
          0 => 'Yes, there is a fee to publish deals.'
        ),
        'value' => 0,
      ));
      

      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Deals Listing Privacy',
        'description' => 'GROUPBUY_FORM_ADMIN_LEVEL_AUTHVIEW_DESCRIPTION',
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
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Deals Comment Options',
        'description' => 'GROUPBUY _FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
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
 	// HTML Allowed Tags
      $this->addElement('Text', 'auth_html', array(
        'label'       => 'HTML in Deal Description?',
        'description' => 'If you want to allow specific HTML tags, you can enter them below (separated by commas). Example: b, img, a, embed, font',
        'value'       => 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'
      ));
  }
}