<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Admin_Review_Global extends Engine_Form {

    public function init() {

        $this->setTitle('Review Settings')
                ->setDescription('Reviews & ratings are an extremely useful feature that enables you to gather refined ratings, reviews and feedback for the Events in your community. Below, you can highly configure the settings for reviews & ratings on your site.');

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $this->addElement('Radio', 'siteevent_reviews', array(
            'label' => 'Allow Reviews',
            'description' => 'Do you want to allow editors and users to write review on events? (Note: If you select ‘No’, then some other settings on this page may not apply. From Member Level Settings, you can choose if visitors should be able to review events.)',
            'multiOptions' => array(
                3 => 'Yes, allow Editors and Users',
                2 => 'Yes, allow Users only',
                1 => 'Yes, allow Editors only',
                0 => 'No',
            ),
            'value' => $settings->getSetting('siteevent.reviews', 2),
            'onclick' => 'hideOwnerReviews(this.value);'
        ));

        $this->addElement('Radio', 'siteevent_allowreview', array(
            'label' => 'Allow Only User Ratings',
            'description' => "Do you want to allow users to only rate events?",
            'multiOptions' => array(
                1 => 'No, allow both User Reviews and Ratings',
                0 => 'Yes, allow Ratings only',
            ),
            'value' => $settings->getSetting('siteevent.allowreview', 1),
        ));


        $this->addElement('Radio', 'siteevent_allowownerreview', array(
            'label' => 'Allow Event Owners to Review',
            'description' => 'Do you want to allow event owners to review and rate events posted by them?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('siteevent.allowownerreview', 1),
        ));

        $this->addElement('Radio', 'siteevent_allowguestreview', array(
            'label' => 'Allow Only Event Guests to Review Events',
            'description' => 'Do you want to allow only event guests to review and rate Events?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('siteevent.allowguestreview', 1),
        ));
        
        $this->addElement('Radio', 'siteevent_reviewbeforeeventend', array(
            'label' => 'Allow Review After Event Ends',
            'description' => "Do you want to allow users to review, only after the event ends?
",
            'multiOptions' => array(
                1 => 'Yes, allow users to review only after the event ends.',
                0 => 'No, users can also review before the event ends.',
            ),
            'value' => $settings->getSetting('siteevent.reviewbeforeeventend', 1),
        ));        

        $this->addElement('Radio', 'siteevent_editorreview', array(
            'label' => 'Editing Editor Reviews',
            'description' => 'Do you want to let editors edit all "Editor Reviews"?',
            'multiOptions' => array(
                1 => 'Yes, allow editors to edit all "Editor Reviews".',
                0 => 'No, editors can only edit their own "Editor Reviews".',
            ),
            'value' => $settings->getSetting('siteevent.editorreview', 0),
        ));

        $this->addElement('Radio', 'siteevent_proscons', array(
            'label' => 'Pros and Cons in User Reviews',
            'description' => 'Do you want Pros and Cons fields in Reviews? (If enabled, reviewers will be able to enter Pros and Cons for the Events that they review, and the same will be shown in their reviews.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('siteevent.proscons', 1),
            'onclick' => 'prosconsInReviews(this.value)',
            'allowEmpty' => true,
            'required' => false,
        ));

        $this->addElement('Radio', 'siteevent_proncons', array(
            'label' => "Required Pros and Cons",
            'description' => 'Do you want to make Pros and Cons fields to be required when reviewers review events on your site?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('siteevent.proncons', 1),
        ));

        $this->addElement('Text', 'siteevent_limit_proscons', array(
            'label' => 'Pros and Cons Character Limit',
            'description' => 'What character limit should be applied to the Pros and Cons fields? (Enter 0 for no character limitation.)',
            'value' => $settings->getSetting('siteevent.limit.proscons', 500),
            'allowEmpty' => false,
            'required' => true,
        ));

        $this->addElement('Radio', 'siteevent_recommend', array(
            'label' => 'Recommended in Reviews',
            'description' => 'Do you want Recommended field in Reviews? (If enabled, reviewers will be able to select if they would recommend that Event to a friend, and the same will be shown in their review.)',
            'value' => $settings->getSetting('siteevent.recommend', 1),
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'allowEmpty' => true,
            'required' => false,
        ));

        $this->addElement('Radio', 'siteevent_summary', array(
            'label' => 'Required Summary',
            'description' => 'Do you want to make Summary field to be required when reviewers review events on your site?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('siteevent.summary', 1),
        ));


        $this->addElement('Radio', 'siteevent_report', array(
            'label' => 'Report',
            'description' => 'Allow logged-in users to report reviews as inappropriate.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('siteevent.report', 1),
            'allowEmpty' => true,
            'required' => false,
        ));

        $this->addElement('Radio', 'siteevent_share', array(
            'label' => 'Share',
            'description' => 'Allow logged-in users to share reviews in their activity feeds.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('siteevent.share', 1),
            'allowEmpty' => true,
            'required' => false,
        ));

        $this->addElement('Radio', 'siteevent_email', array(
            'label' => 'Email',
            'description' => 'Allow logged-in users to email the review content.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('siteevent.email', 1),
            'allowEmpty' => true,
            'required' => false,
        ));

//        $this->addElement('Radio', 'siteevent_captcha', array(
//            'label' => 'Enable Captcha',
//            'description' => 'Do you want to enable captcha when visitors review events on your site?',
//            'multiOptions' => array(
//                1 => 'Yes',
//                0 => 'No'
//            ),
//            'value' => $settings->getSetting('siteevent.captcha', 1),
//        ));

        $this->addElement('Textarea', 'siteevent_contact', array(
            'label' => 'Email Addresses',
            'description' => 'Enter the email addresses on which notification emails will be sent when visitors of your site review events on your site. From Member Level Settings, you can choose if visitors should be able to review events. (Note: You can add multiple addresses with commas.)',
            'value' => $settings->getSetting('siteevent.contact', $settings->getSetting('core.mail.from', 'email@domain.com')),
            'allowEmpty' => false,
            'required' => true,
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}