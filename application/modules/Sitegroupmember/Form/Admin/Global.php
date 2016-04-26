<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegroupmember_Form_Admin_Global extends Engine_Form {

  public function init() {
  
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    
    $this->setTitle('Global Settings')
        ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'language_phrases_group', array(
        'label' => 'Singular Group Title',
        'description' => 'Please enter the Singular Title for group. This text will come in places like feeds generated, widgets etc.',
        'allowEmpty' => FALSE,
        'validators' => array(
            array('NotEmpty', true),
        ),
        'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.group", "group"),

    ));
    
    $this->addElement('Text', 'language_phrases_groups', array(
        'label' => 'Plural Group Title',
        'description' => 'Please enter the Plural Title for groups. This text will come in places like Main Navigation Menu, Group Navigation Menu, widgets etc.',
        'allowEmpty' => FALSE,
        'validators' => array(
            array('NotEmpty', true),
        ),
      'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.groups", "groups"),
    ));

    $this->addElement('Text', 'sitegroupmember_manifestUrl', array(
			'label' => 'Group Members URL alternate text for "group-members"',
			'allowEmpty' => false,
			'required' => true,
			'description' => 'Please enter the text below which you want to display in place of "groupmembers" in the URLs of this plugin.',
			'value' => $coreSettings->getSetting('sitegroupmember.manifestUrl', "group-members"),
    ));
    
		$this->addElement( 'Radio' , 'groupmember_title' , array (
      'label' => 'Member Roles',
      'description' => "Do you want group members to be able to select their member roles in the directory items / groups?",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'groupmember.title' , 1),
    )) ;
    
    $this->addElement( 'Radio' , 'groupmember_member_title' , array (
      'label' => 'Enable Member Title',
      'description' => "Do you want group admins to be able to enter member titles by which members will be called in their directory items / groups?",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'groupmember.member.title' , 1),
    )) ;
    
    $this->addElement('Radio', 'sitegroupmember_member_show_menu', array(
			'label' => 'Members Link',
			'description' => 'Do you want to show the Members link on Groups Navigation Menu? (You might want to show this if Members from Groups are an important component on your website. This link will lead to a widgetized group listing all Group Members, with a search form for Group Members and multiple widgets.',
			'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
			),
			'value' => $coreSettings->getSetting('sitegroupmember.member.show.menu', 1),
    ));

    $this->addElement( 'Radio' , 'groupmember_date' , array (
      'label' => 'Affiliation / Joining Date',
      'description' => "Do you want group members to be able to select their affiliation / joining date in the directory items / groups?",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'groupmember.date' , 1),
    )) ;
    
    $this->addElement( 'Radio' , 'groupmember_announcement' , array (
      'label' => 'Announcements',
      'description' => 'Do you want announcements to be enabled for directory items / groups? (If enabled, then group admins will be able to post announcements for their groups from ‘Manage Announcements’ section of their Group Dashboard.)',
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'groupmember.announcement' , 1),
			'onclick' => 'showAnnouncements(this.value)'
    )) ;
    
    $this->addElement('Radio', 'sitegroupmember_tinymceditor', array(
        'label' => 'Tinymce Editor',
        'description' => 'Do you want to allow tinymce editor for the announcements.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroupmember.tinymceditor', 1),
    ));

    $this->addElement( 'Radio' , 'groupmember_automatically_addmember' , array (
      'label' => 'Automatically Add People',
      'description' => "Do you want people to be automatically added to a group when group admins or other members of that group add them? (Note: This setting will not work for the groups which need admin approval when people try to join that group.)",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'groupmember.automatically.addmember' , 1),
    )) ;

    $this->addElement( 'Radio' , 'groupmember_automatically_like' , array (
      'label' => 'Automatic Like',
      'description' => "Do you want members to automatically Like a group they Join?",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'groupmember.automatically.like' , 0),
    )) ;
    
    $this->addElement( 'Radio' , 'groupmember_automatically_join' , array (
      'label' => 'Automatic Join',
      'description' => "Do you want members to automatically Join a group they Like?",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'groupmember.automatically.join' , 0),
    )) ;

    $this->addElement( 'Radio' , 'groupmember_invite_option' , array (
      'label' => 'Enable “Member Invite Others” Option',
      'description' => "Do you want to enable “Member Invite Others” option in the groups using which group admins will be able to choose who should be able to invite other people to their groups? (If you select ‘No’, then you can choose who would be able to invite other people to the groups on your site.)",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'groupmember.invite.option' , 1),
      'onclick' => 'showInviteOption(this.value)',
    ));

		$this->addElement('Radio', 'groupmember_invite_automatically', array(
			'label' => 'Member Invite Others',
			'description' => 'Do you want group members to invite other people to the groups they join?',
			'multiOptions' => array(
			'0' => 'Yes, members can invite other people.',
			'1' => 'No, only group admins can invite other people',
			),
			'value' => $coreSettings->getSetting( 'groupmember.invite.automatically' , 1),
		));
    
    $this->addElement( 'Radio' , 'groupmember_member_approval_option' , array (
      'label' => 'Enable “Approve Members” Option',
      'description' => "Do you want to enable “Approve Members” option in the groups using which group admins will be able to choose that when people try to join groups, should they be allowed to join immediately, or should they be forced to wait for approval? (If you select ‘No’, then you can choose to allow members to join immediately or wait for approval.)",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'groupmember.member.approval.option' , 1),
      'onclick' => 'showApprovalOption(this.value)'
    ));
    
    $this->addElement('Radio', 'groupmember_member_approval_automatically', array(
			'label' => 'Approve Members',
			'description' => 'When people try to join the groups on your site, should they be allowed to join immediately, or should they be forced to wait for approval?',
			'multiOptions' => array(
				'1' => 'New members can join immediately.',
				'0' => 'New members must be approved.',
			),
			'value' => $coreSettings->getSetting( 'groupmember.member.approval.automatically' , 1),
		));

		$this->addElement( 'Radio' , 'groupmember_groupasgroup' , array (
			'label' => 'Enable "Invite All Group members" Option',
			'description' => "Do you want to let group event owners invite all group members to their group events? (If enabled, then ‘Invite All Group members’ option will be available while creating a group event.)",
			'multiOptions' => array (
				1 => 'Yes' ,
				0 => 'No'
			) ,
			'value' => $coreSettings->getSetting( 'groupmember.groupasgroup' , 0),
		));

    $this->addElement('Button', 'submit', array(
			'label' => 'Save Changes',
			'type' => 'submit',
			'ignore' => true
    ));
  }
}