<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Leveltype.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {

      $isEnabledPackage = Engine_Api::_()->siteevent()->hasPackageEnable();
        parent::init();

        $this->setTitle('Member Level Settings')
                ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

        $eventGuestOnlyText = Engine_Api::_()->siteevent()->isTicketBasedEvent() ? 'Event Guests Only [Note: This option will not available to users if "Tickets" setting is enabled in "Ticket Settings" section.]' : 'Event Guests Only';
        
        $view_element = "view";
        $this->addElement('Radio', "$view_element", array(
            'label' => 'Allow Viewing of Events?',
            'description' => 'Do you want to let members view events? If set to no, some other settings on this page may not apply.',
            'multiOptions' => array(
                2 => 'Yes, allow viewing of all events, even private ones.',
                1 => 'Yes, allow viewing of events.',
                0 => 'No, do not allow events to be viewed.',
            ),
            'value' => ( $this->isModerator() ? 2 : 1 ),
        ));
        if (!$this->isModerator()) {
            unset($this->$view_element->options[2]);
        }

        if (!$this->isPublic()) {

            $create_element = "create";
            $this->addElement('Radio', "$create_element", array(
                'label' => 'Allow Creation of Events?',
                'description' => 'Do you want to let members create events? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view events, but only want certain levels to be able to create events.',
                'multiOptions' => array(
                    1 => 'Yes, allow creation of events.',
                    0 => 'No, do not allow events to be created.'
                ),
                'value' => 1,
            ));

            $edit_element = "edit";
            $this->addElement('Radio', "$edit_element", array(
                'label' => 'Allow Editing of Events?',
                'description' => 'Do you want to let members edit events? If set to no, some other settings on this page may not apply.',
                'multiOptions' => array(
                    2 => 'Yes, allow members to edit all events.',
                    1 => 'Yes, allow members to edit their own events.',
                    0 => 'No, do not allow members to edit their events.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$edit_element->options[2]);
            }

            $delete_element = "delete";
            $this->addElement('Radio', "$delete_element", array(
                'label' => 'Allow Deletion of Events?',
                'description' => 'Do you want to let members delete events? If set to no, some other settings on this page may not apply.',
                'multiOptions' => array(
                    2 => 'Yes, allow members to delete all events.',
                    1 => 'Yes, allow members to delete their own events.',
                    0 => 'No, do not allow members to delete their events.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$delete_element->options[2]);
            }

            $comment_element = "comment";
            $this->addElement('Radio', "$comment_element", array(
                'label' => 'Allow Commenting on Events?',
                'description' => 'Do you want to let members of this level comment on events?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to comment on all events, including private ones.',
                    1 => 'Yes, allow members to comment on events.',
                    0 => 'No, do not allow members to comment on events.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$comment_element->options[2]);
            }

            //ADDING SETTINGS IF SITEEVENTTICKET & TICKET SETTING IS ENABLED.
            if (Engine_Api::_()->siteevent()->hasTicketEnable() && !$this->isPublic()) {
            // Element : Ticket

                $this->addElement('Radio', "ticket_create", array(
                    'label' => 'Allow Creation of Tickets?',
                    'description' => 'Do you want to let members create tickets? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view tickets, but only want certain levels to be able to create tickets.',
                    'multiOptions' => array(
                        1 => 'Yes, allow creation of tickets.',
                        0 => 'No, do not allow tickets to be created.'
                    ),
                    'value' => 1,
                ));

                $this->addElement('Radio', "coupon_creation", array(
                  'label' => 'Allow Coupon Creation?',
                  'description' => 'Do you want to let members create coupons for their event tickets? If set to no, users of this level will not be allowed to create coupons.',
                  'multiOptions' => array(
                      1 => 'Yes, allow members to create coupons.',
                      0 => 'No, do not allow members to create coupons.',
                  ),
                  'value' => 1,
                )); 
            }
            
            $style_element = "style";
            $this->addElement('Radio', "$style_element", array(
                'label' => 'Allow Custom CSS Styles?',
                'description' => 'If you enable this feature, your members will be able to customize the colors and fonts of their events by altering their CSS styles.',
                'multiOptions' => array(
                    1 => 'Yes, enable custom CSS styles.',
                    0 => 'No, disable custom CSS styles.',
                ),
                'value' => 1,
            ));

            //PACKAGE BASED CHECKS - DISPLAY THIS SETTING WHEN PACKAGE IS DISABLED
            if (empty($isEnabledPackage)) {
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1)) {
                $overview_element = "overview";
                $this->addElement('Radio', "$overview_element", array(
                    'label' => 'Allow Overview?',
                    'description' => 'Do you want to let members enter rich Overview for their events?',
                    'multiOptions' => array(
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    'value' => 1,
                ));
            }
            }

            $this->addElement('Radio', "contact", array(
                'label' => 'Allow Contact Details',
                'description' => 'Do you want to let members enter contact details for their events?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 1,
            ));

            $this->addElement('Radio', "metakeyword", array(
                'label' => 'Meta Tags / Keywords',
                'description' => 'Do you want to let members enter meta tags / keywords for their events?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 1,
            ));

            $availableLabels = array('everyone' => 'Everyone', 'registered' => 'All Registered Members', 'owner_network' => 'Friends and Networks (user events only)', 'owner_member_member' => 'Friends of Friends (user events only)', 'owner_member' => 'Friends Only (user events only)', 'parent_member' => "Parent Member (Members of 'Directory / Pages' or  'Directory / Businesses' or 'Groups / Communities' only)", 'member' => $eventGuestOnlyText, 'leader' => 'Owner and Leaders Only / Just Me');
            $roles = array('everyone', 'registered', 'owner_network', 'owner_member_member', 'parent_member', 'owner_member', 'member', 'leader');

            if ((Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitepage_page", 'item_module' => 'sitepage')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) || (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitebusiness_business", 'item_module' => 'sitebusiness')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmember')) || (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitegroup_group", 'item_module' => 'sitegroup')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember'))) {
                
            } else {
                unset($availableLabels['parent_member']);
            }

            $auth_view_element = "auth_view";
            $this->addElement('MultiCheckbox', "$auth_view_element", array(
                'label' => 'Event View Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can see their events. If you do not check any options, everyone will be allowed to view events.',
                'multiOptions' => $availableLabels,
                'value' => $roles
            ));

            $availableLabels = array('registered' => 'All Registered Members', 'owner_network' => 'Friends and Networks (user events only)', 'owner_member_member' => 'Friends of Friends (user events only)', 'owner_member' => 'Friends Only (user events only)', 'parent_member' => "Parent Member (Members of 'Directory / Pages' or  'Directory / Businesses' or 'Groups / Communities'       only)", 'like_member' => "Who Liked This Page / Business / Group / Store", 'member' => $eventGuestOnlyText, 'leader' => 'Owner and Leaders Only / Just Me');
            $roles = array('registered', 'owner_network', 'owner_member_member', 'parent_member', 'owner_member', 'member', 'leader');

            if ((Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitepage_page", 'item_module' => 'sitepage')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) || (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitebusiness_business", 'item_module' => 'sitebusiness')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmember')) || (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitegroup_group", 'item_module' => 'sitegroup')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember'))) {
                
            } else {
                unset($availableLabels['parent_member']);
            }

            if ((Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitepage_page", 'item_module' => 'sitepage')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) || (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitebusiness_business", 'item_module' => 'sitebusiness')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness')) || (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitegroup_group", 'item_module' => 'sitegroup')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup')) && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitestore_store", 'item_module' => 'sitestore')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore'))) {
                
            } else {
                unset($availableLabels['like_member']);
            }

            $auth_comment_element = "auth_comment";
            $this->addElement('MultiCheckbox', "$auth_comment_element", array(
                'label' => 'Event Comment Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their events. If you do not check any options, everyone will be allowed to post comments on events.',
                'multiOptions' => $availableLabels,
                'value' => $roles
            ));

            if (Engine_Api::_()->hasModuleBootstrap('advancedactivity')) {
                $post_element = "post";
                $this->addElement('Radio', "$post_element", array(
                    'label' => 'Allow Posting of Updates?',
                    'description' => 'Do you want to let members to post updates on events?',
                    'multiOptions' => array(
                        2 => 'Yes, allow posting updates on events, including private ones.',
                        1 => 'Yes, allow posting updates on events.',
                        0 => 'No, do not allow posting updates.'
                    ),
                    'value' => ( $this->isModerator() ? 2 : 1 ),
                ));
                if (!$this->isModerator()) {
                    unset($this->$post_element->options[2]);
                }
                $auth_post_element = "auth_post";
                $this->addElement('MultiCheckbox', "$auth_post_element", array(
                    'label' => 'Posting Updates Options',
                    'description' => 'Your members can choose from any of the options checked below when they decide who can post updates in their events. If you do not check any options, everyone will be allowed to post updates to the events of this member level.',
                    'multiOptions' => $availableLabels,
                    'value' => $roles
                ));
            }

            $topic_element = "topic";
            $this->addElement('Radio', "$topic_element", array(
                'label' => 'Allow Posting of Discusstion Topics?',
                'description' => 'Do you want to let members post discussion topics to events?',
                'multiOptions' => array(
                    2 => 'Yes, allow discussion topic posting to events, including private ones.',
                    1 => 'Yes, allow discussion topic posting to events.',
                    0 => 'No, do not allow discussion topic posting.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$topic_element->options[2]);
            }
            $auth_topic_element = "auth_topic";
            $this->addElement('MultiCheckbox', "$auth_topic_element", array(
                'label' => 'Discussion Topic Posting Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can post the discussion topics in their events. If you do not check any options, everyone will be allowed to post discussion topics to the events of this member level.',
                'multiOptions' => $availableLabels,
                'value' => $roles
            ));

            //PACKAGE BASED CHECKS - DISPLAY THIS SETTING WHEN PACKAGE IS DISABLED
            if (empty($isEnabledPackage)) {
            $photo_element = "photo";
            $this->addElement('Radio', "$photo_element", array(
                'label' => 'Allow Uploading of Photos?',
                'description' => 'Do you want to let members upload Photos to events?',
                'multiOptions' => array(
                    2 => 'Yes, allow photo uploading to events, including private ones.',
                    1 => 'Yes, allow photo uploading to events.',
                    0 => 'No, do not allow photo uploading.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$photo_element->options[2]);
            }
            }
            $auth_photo_element = "auth_photo";
            $this->addElement('MultiCheckbox', "$auth_photo_element", array(
                'label' => 'Photo Upload Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can upload the photos in their events. If you do not check any options, everyone will be allowed to upload photos to the events of this member level.',
                'multiOptions' => $availableLabels,
                'value' => $roles
            ));

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                $document_element = "document";
                $this->addElement('Radio', "$document_element", array(
                    'label' => 'Allow Creation of Documents in Events?',
                    'description' => 'Do you want to let members of this level to create documents in event?',
                    'multiOptions' => array(
                        2 => 'Yes, allow members to create documents in all events, including private ones.',
                        1 => 'Yes, allow members to create documents in events.',
                        0 => 'No, do not allow members to create documents in events.',
                    ),
                    'value' => ( $this->isModerator() ? 2 : 1 ),
                ));
                if (!$this->isModerator()) {
                    unset($this->$document_element->options[2]);
                }
                $auth_document_element = "auth_document";
                $this->addElement('MultiCheckbox', "$auth_document_element", array(
                    'label' => 'Document Creation Options',
                    'description' => 'Your users can choose from any of the options checked below when they decide who can create the documents in their event. If you do not check any options, everyone will be allowed to create.',
                    'multiOptions' => $availableLabels,
                    'value' => $roles
                ));
            }

            if (Engine_Api::_()->siteevent()->enableVideoPlugin()) {
                //PACKAGE BASED CHECKS - DISPLAY THIS SETTING WHEN PACKAGE IS DISABLED
                if (empty($isEnabledPackage)) {
                  $video_element = "video";
                  $this->addElement('Radio', "$video_element", array(
                      'label' => 'Allow Uploading of Videos?',
                      'description' => 'Do you want to let members upload Videos to events?',
                      'multiOptions' => array(
                          2 => 'Yes, allow video uploading to events, including private ones.',
                          1 => 'Yes, allow video uploading to events.',
                          0 => 'No, do not allow video uploading.'
                      ),
                      'value' => ( $this->isModerator() ? 2 : 1 ),
                  ));
                  if (!$this->isModerator()) {
                      unset($this->$video_element->options[2]);
                  }
                }
                $auth_video_element = "auth_video";
                $this->addElement('MultiCheckbox', "$auth_video_element", array(
                    'label' => 'Video Upload Options',
                    'description' => 'Your members can choose from any of the options checked below when they decide who can upload the videos in their events. If you do not check any options, everyone will be allowed to upload video.',
                    'multiOptions' => $availableLabels,
                    'value' => $roles
                ));
            }

            if (empty($isEnabledPackage)) {
            $approved_element = "approved";
            $this->addElement('Radio', "$approved_element", array(
                'label' => 'Event Approval Moderation',
                'description' => 'Do you want new Event to be automatically approved?',
                'multiOptions' => array(
                    1 => 'Yes, automatically approve Event.',
                    0 => 'No, site admin approval will be required for all Event.'
                ),
                'value' => 1,
            ));

            $featured_element = "featured";
            $this->addElement('Radio', "$featured_element", array(
                'label' => 'Event Featured Moderation',
                'description' => 'Do you want new Event to be automatically made featured?',
                'multiOptions' => array(
                    1 => 'Yes, automatically make Event featured.',
                    0 => 'No, site admin will be making Event featured.'
                ),
                'value' => 1,
            ));

            $sponsored_element = "sponsored";
            $this->addElement('Radio', "$sponsored_element", array(
                'label' => 'Event Sponsored Moderation',
                'description' => 'Do you want new Event to be automatically made Sponsored?',
                'multiOptions' => array(
                    1 => 'Yes, automatically make Event Sponsored.',
                    0 => 'No, site admin will be making Event Sponsored.'
                ),
                'value' => 1,
            ));
            }
            $diary_create_element = "create_diary";
            $this->addElement('Radio', "$diary_create_element", array(
                'label' => 'Allow Creation of Diaries?',
                'description' => 'Do you want to let members create diaries and add events to their diaries? If set to no, members of this level will neither be able to create diaries nor they will be able to add events to their diaries',
                'multiOptions' => array(
                    1 => 'Yes, allow creation of diaries.',
                    0 => 'No, do not allow diaries to be created.'
                ),
                'value' => 1,
            ));
        }

        $this->addElement('Radio', "diary", array(
            'label' => 'Allow Viewing of Diaries?',
            'description' => 'Do you want to let members view Diaries? If set to no, some other settings on this page may not apply.',
            'multiOptions' => array(
                2 => 'Yes, allow members to view all diaries, even private ones.',
                1 => 'Yes, allow viewing of diaries.',
                0 => 'No, do not allow diaries to be viewed.',
            ),
            'value' => ( $this->isModerator() ? 2 : 1 ),
        ));

        if (!$this->isModerator()) {
            unset($this->diary->options[2]);
        }

        if (!$this->isPublic()) {

            $this->addElement('MultiCheckbox', "auth_diary", array(
                'label' => 'Diaries View Privacy',
                'description' => 'Your members can choose from any of the options checked below when they decide who can see their diaries. These options appear on your members\' "Create New Diaries" and "Edit Diaries" pages. If you do not check any options, everyone will be allowed to view diaries.',
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

            $review_create_element = "review_create";
            $this->addElement('Radio', "$review_create_element", array(
                'label' => 'Allow Writing of Reviews',
                'description' => 'Do you want to let members write reviews for events?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to write reviews.',
                    0 => 'No, do not allow members to write reviews.',
                ),
                'value' => 1,
            ));

            $review_reply_element = "review_reply";
            $this->addElement('Radio', "$review_reply_element", array(
                'label' => 'Allow Commenting on Reviews?',
                'description' => 'Do you want to let members to comment on Reviews?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to comment on reviews.',
                    0 => 'No, do not allow members to comment on reviews.',
                ),
                'value' => 1,
            ));
            if (!$this->isModerator()) {
                unset($this->$review_reply_element->options[2]);
            }

            $review_update_element = "review_update";
            $this->addElement('Radio', "$review_update_element", array(
                'label' => 'Allow Updating of Reviews?',
                'description' => 'Do you want to let members to update their reviews?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to update their own reviews.',
                    0 => 'No, do not allow members to update their reviews.',
                ),
                'value' => 1,
            ));

            $review_delete_element = "review_delete";
            $this->addElement('Radio', "$review_delete_element", array(
                'label' => 'Allow Deletion of Reviews?',
                'description' => 'Do you want to let members delete reviews?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to delete all reviews.',
                    1 => 'Yes, allow members to delete their own reviews.',
                    0 => 'No, do not allow members to delete their reviews.',
                ),
                'value' => ( $this->isModerator() ? 2 : 0 ),
            ));
            if (!$this->isModerator()) {
                unset($this->$review_delete_element->options[2]);
            }

            $max_element = "max";
            $this->addElement('Text', "$max_element", array(
                'label' => 'Maximum Allowed Events',
                'description' => 'Enter the maximum number of allowed events. This field must contain an integer, use zero for unlimited.',
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(0),
                ),
            ));
        } else {
            $this->addElement('Radio', "creation_link", array(
                'label' => 'Show "Create New Event" link',
                'description' => 'Do you want to let members view "Create New Event" link on various places like Navigation Bar, Events Home or as configured by you? If set to yes, then on clicking "Create New Event" link members will be redirected to the sign in page.',
                'multiOptions' => array(
                    1 => 'Yes, show "Create New Event" link.',
                    0 => 'No, do not show "Create New Event" link.',
                ),
                'value' => 0,
            ));
        }
        
        //ADDING SETTINGS IF SITEEVENTTICKET & TICKET SETTING IS ENABLED.
        if (Engine_Api::_()->siteevent()->hasTicketEnable() && !$this->isPublic() && !Engine_Api::_()->siteevent()->hasPackageEnable()) {
            
            $this->addElement('Select', 'commission_handling', array(
            'label' => 'Commission Type',
            'description' => 'Select the type of commission. This commission will be applied on all the orders placed for event tickets from the events of this level.',
             'multiOptions' => array(
                1 => 'Percent',
                0 => 'Fixed'
              ),
            'value' => 1,
            'onchange' => 'showcommissionType();'
            ));

            $localeObject = Zend_Registry::get('Locale');
            $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
            $this->addElement('Text', 'commission_fee', array(
                'label' => 'Commission Value (' . $currencyName . ')',
                'description' => 'Enter the value of the commission. (If you do not want to apply any commission, then simply enter 0.)',
                'allowEmpty' => false,
                'value' => 1,
            ));

            $this->addElement('Text', 'commission_rate', array(
                'label' => 'Commission Value (%)',
                'description' => 'Enter the value of the commission. (Do not add any symbol. For 10% commission, enter commission value as 10. You can only enter commission percentage between 0 and 100.)',
                 'validators' => array(
                        array('Between', true, array('min' => 0, 'max' => 100, 'inclusive' => true)),
                    ),
                'value' => 1,
            ));

            // Element: transfer_threshold
            if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', '0')) {
                $this->addElement('Text', 'transfer_threshold', array(
                    'label' => "Payment Threshold Amount ($currencyName)",
                    'description' => 'Enter the payment threshold amount. Event owners of events of this level will be able to request you for their payments when the total amount of their Event Ticket sale becomes more than this threshold amount.',
                    'allowEmpty' => false,
                    'required' => true,
                    'value' => 100,
                ));               
            }
      }
    }

}
