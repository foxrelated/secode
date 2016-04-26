<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Level.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

  public function init() {
    parent::init();

    $this
        ->setTitle('Member Level Settings')
        ->setDescription("LISTING_FORM_ADMIN_LEVEL_DESCRIPTION");

    $this->addElement('Radio', 'view', array(
            'label' => 'Allow Viewing of Listings?',
            'description' => 'Do you want to let members view listings? If set to no, some other settings on this page may not apply.',
            'multiOptions' => array(
                    2 => 'Yes, allow viewing of all listings, even private ones.',
                    1 => 'Yes, allow viewing of listings.',
                    0 => 'No, do not allow listings to be viewed.',
            ),
            'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if (!$this->isModerator()) {
      unset($this->view->options[2]);
    }

    if (!$this->isPublic()) {

      $this->addElement('Radio', 'create', array(
              'label' => 'Allow Creation of Listings?',
              'description' => 'LISTING_FORM_ADMIN_LEVEL_CREATE_DESCRIPTION',
              'multiOptions' => array(
                      1 => 'Yes, allow creation of listings.',
                      0 => 'No, do not allow listings to be created.'
              ),
              'value' => 1,
      ));

      $this->addElement('Radio', 'edit', array(
              'label' => 'Allow Editing of Listings?',
              'description' => 'Do you want to let members edit listings? If set to no, some other settings on this page may not apply.',
              'multiOptions' => array(
                      2 => 'Yes, allow members to edit all listings.',
                      1 => 'Yes, allow members to edit their own listings.',
                      0 => 'No, do not allow members to edit their listings.',
              ),
              'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->edit->options[2]);
      }

      $this->addElement('Radio', 'delete', array(
              'label' => 'Allow Deletion of Listings?',
              'description' => 'Do you want to let members delete listings? If set to no, some other settings on this page may not apply.',
              'multiOptions' => array(
                      2 => 'Yes, allow members to delete all listings.',
                      1 => 'Yes, allow members to delete their own listings.',
                      0 => 'No, do not allow members to delete their listings.',
              ),
              'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->delete->options[2]);
      }

      $this->addElement('Radio', 'comment', array(
              'label' => 'Allow Commenting on Listings?',
              'description' => 'Do you want to let members of this level comment on listings?',
              'multiOptions' => array(
                      2 => 'Yes, allow members to comment on all listings, including private ones.',
                      1 => 'Yes, allow members to comment on listings.',
                      0 => 'No, do not allow members to comment on listings.',
              ),
              'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->comment->options[2]);
      }

      $this->addElement('Radio', 'style', array(
              'label' => 'Allow Custom CSS Styles?',
              'description' => 'If you enable this feature, your members will be able to customize the colors and fonts of their listings by altering their CSS styles.',
              'multiOptions' => array(
                      1 => 'Yes, enable custom CSS styles.',
                      0 => 'No, disable custom CSS styles.',
              ),
              'value' => 1,
      ));

      $this->addElement('Radio', 'overview', array(
				'label' => 'Allow Overview?',
				'description' => 'Do you want to let members enter rich Overview for their listings?',
				'multiOptions' => array(
								1 => 'Yes',
								0 => 'No'
				),
				'value' => 1,
      ));

      $this->addElement('Radio', 'photo', array(
              'label' => 'Allow Uploading of Photos?',
              'description' => 'Do you want to let members upload photos to listings? If set to no, the option to upload photos will not appear.',
              'multiOptions' => array(
                      2 => 'Yes, allow photo uploading to listings, including private ones.',
                      1 => 'Yes, allow photo uploading to listings.',
                      0 => 'No, do not allow photo uploading.'
              ),
              'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->photo->options[2]);
      }

      $this->addElement('MultiCheckbox', 'auth_view', array(
              'label' => 'Listing Privacy',
              'description' => 'LISTING_FORM_ADMIN_LEVEL_AUTHVIEW_DESCRIPTION',
              'multiOptions' => array(
                      'everyone' => 'Everyone',
                      'registered' => 'All Registered Members',
                      'owner_network' => 'Friends and Networks',
                      'owner_member_member' => 'Friends of Friends',
                      'owner_member' => 'Friends Only',
                      'owner' => 'Just Me'
              ),
              'value' => array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner')
      ));

      $this->addElement('MultiCheckbox', 'auth_comment', array(
              'label' => 'Listing Comment Options',
              'description' => 'LISTING_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
              'multiOptions' => array(
                      'everyone' => 'Everyone',
                      'registered' => 'All Registered Members',
                      'owner_network' => 'Friends and Networks',
                      'owner_member_member' => 'Friends of Friends',
                      'owner_member' => 'Friends Only',
                      'owner' => 'Just Me'
              ),
              'value' => array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner')
      ));

      $this->addElement('MultiCheckbox', 'auth_photo', array(
              'label' => 'Photo Upload Options',
              'description' => 'Your users can choose from any of the options checked below when they decide who can upload the photos in their list. If you do not check any options, everyone will be allowed to create.',
              'multiOptions' => array(
                      'registered' => 'All Registered Members',
                      'owner_network' => 'Friends and Networks',
                      'owner_member_member' => 'Friends of Friends',
                      'owner_member' => 'Friends Only',
                      'owner' => 'Just Me'
              ),
              'value' => array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner')
      ));

      $this->addElement('Radio', 'video', array(
              'label' => 'Allow Uploading of Videos?',
              'description' => 'Do you want to let members upload Videos to listings?',
              'multiOptions' => array(
                      2 => 'Yes, allow video uploading to listings, including private ones.',
                      1 => 'Yes, allow video uploading to listings.',
                      0 => 'No, do not allow video uploading.'
              ),
              'value' => ( $this->isModerator() ? 2 : 1 ),
        ));
        if (!$this->isModerator()) {
          unset($this->video->options[2]);
        }

      $this->addElement('MultiCheckbox', 'auth_video', array(
              'label' => 'Video Upload Options',
              'description' => 'Your users can choose from any of the options checked below when they decide who can upload the videos in their list. If you do not check any options, everyone will be allowed to create.',
              'multiOptions' => array(
                      'registered' => 'All Registered Members',
                      'owner_network' => 'Friends and Networks',
                      'owner_member_member' => 'Friends of Friends',
                      'owner_member' => 'Friends Only',
                      'owner' => 'Just Me'
              ),
              'value' => array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner')
      ));

      $this->addElement('Radio', 'approved', array(
              'label' => 'Listing Approval Moderation',
              'description' => 'Do you want new Listing to be automatically approved?',
              'multiOptions' => array(
                      1 => 'Yes, automatically approve Listing.',
                      0 => 'No, site admin approval will be required for all Listing.'
              ),
              'value' => 1,
      ));

      $this->addElement('Radio', 'featured', array(
              'label' => 'Listing Featured Moderation',
              'description' => 'Do you want new Listing to be automatically made featured?',
              'multiOptions' => array(
                      1 => 'Yes, automatically make Listing featured.',
                      0 => 'No, site admin will be making Listing featured.'
              ),
              'value' => 1,
      ));

      $this->addElement('Radio', 'sponsored', array(
              'label' => 'Listing Sponsored Moderation',
              'description' => 'Do you want new Listing to be automatically made Sponsored?',
              'multiOptions' => array(
                      1 => 'Yes, automatically make Listing Sponsored.',
                      0 => 'No, site admin will be making Listing Sponsored.'
              ),
              'value' => 1,
      ));

      $this->addElement('Text', 'max', array(
              'label' => 'Maximum Allowed Listings',
              'description' => 'Enter the maximum number of allowed listings. The field must contain an integer, use zero for unlimited.',
              'validators' => array(
                      array('Int', true),
                      new Engine_Validate_AtLeast(0),
              ),
      ));
    }
  }

}