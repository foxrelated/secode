<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Review_Create extends Engine_Form {

    public $_error = array();
    protected $_settingsReview;
    protected $_item;
    protected $_profileTypeReview;

    public function getProfileTypeReview() {
        return $this->_profileTypeReview;
    }

    public function setProfileTypeReview($profileTypeReview) {
        $this->_profileTypeReview = $profileTypeReview;
        return $this;
    }

    public function getSettingsReview() {
        return $this->_settingsReview;
    }

    public function setSettingsReview($settingsReview) {
        $this->_settingsReview = $settingsReview;
        return $this;
    }

    public function getItem() {
        return $this->_item;
    }

    public function setItem($item) {
        $this->_item = $item;
        return $this;
    }

    public function init() {

        //GET REVIEW SETTINGS
        $widgetSettingsReviews = $this->getSettingsReview();

        //GET DECORATORS
        $this->loadDefaultDecorators();

        //GET VIEWER INFO
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET EVENT ID
        $getItemEvent = $this->getItem();

        $event_title = "<b>" . $getItemEvent->title . "</b>";

        //IF NOT HAS POSTED THEN THEN SET FORM
        $this->setTitle('Write a Review')
                ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Give your ratings and opinion for %s below:"), $event_title))
                ->setAttrib('name', 'siteevent_create')
                ->setAttrib('id', 'siteevent_create')
                ->getDecorator('Description')->setOption('escape', false);
        $this->setAttrib('class', 'seaocore_form_comment');
        if ($widgetSettingsReviews['siteevent_proscons']) {
            if ($widgetSettingsReviews['siteevent_limit_proscons']) {
                $this->addElement('Textarea', 'pros', array(
                    'label' => 'Pros',
                    'rows' => 2,
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you like about this Event?"),
                    'allowEmpty' => false,
                    'maxlength' => $widgetSettingsReviews['siteevent_limit_proscons'],
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                ));
            } else {
                $this->addElement('Textarea', 'pros', array(
                    'label' => 'Pros',
                    'rows' => 2,
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you like about this Event?"),
                    'allowEmpty' => false,
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                ));
            }
            $this->pros->getDecorator('Description')->setOptions(array('placement' => 'PREPAND', 'escape' => false));

            if ($widgetSettingsReviews['siteevent_limit_proscons']) {
                $this->addElement('Textarea', 'cons', array(
                    'label' => 'Cons',
                    'rows' => 2,
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this Event?"),
                    'allowEmpty' => false,
                    'maxlength' => $widgetSettingsReviews['siteevent_limit_proscons'],
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                ));
            } else {
                $this->addElement('Textarea', 'cons', array(
                    'label' => 'Cons',
                    'rows' => 2,
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this Event?"),
                    'allowEmpty' => false,
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                ));
            }
            $this->cons->getDecorator('Description')->setOptions(array('placement' => 'PREPAND', 'escape' => false));
        }

        $this->addElement('Textarea', 'title', array(
            'label' => 'One-line summary',
            'rows' => 1,
            'allowEmpty' => false,
            'maxlength' => 63,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

        $profileTypeReview = $this->getProfileTypeReview();
        if (!empty($profileTypeReview)) {

            $customFields = new Siteevent_Form_Custom_Standard(array(
                'item' => 'siteevent_review',
                'topLevelId' => 1,
                'topLevelValue' => $profileTypeReview,
                'decorators' => array(
                    'FormElements'
            )));

            $customFields->removeElement('submit');

            $this->addSubForms(array(
                'fields' => $customFields
            ));
        }

        $this->addElement('Textarea', 'body', array(
            'label' => 'Summary',
            'rows' => 3,
            'allowEmpty' => true,
            'required' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

        if ($widgetSettingsReviews['siteevent_recommend']) {
            $this->addElement('Radio', 'recommend', array(
                'label' => 'Recommended',
                'description' => sprintf(Zend_Registry::get('Zend_Translate')->_("Would you recommend %s to a friend?"), $event_title),
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 1
            ));
            $this->recommend->getDecorator('Description')->setOption('escape', false);
        }

        if (empty($viewer_id) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.captcha', 1)) {
            if (Engine_Api::_()->hasModuleBootstrap('siterecaptcha')) {
                Zend_Registry::get('Zend_View')->recaptcha($this);
            } else {
                $this->addElement('captcha', 'captcha', array(
                    'description' => 'Please type the characters you see in the image.',
                    'captcha' => 'image',
                    'required' => true,
                    'captchaOptions' => array(
                        'wordLen' => 6,
                        'fontSize' => '30',
                        'timeout' => '30000',
                        'imgDir' => APPLICATION_PATH . '/public/temporary/',
                        'imgUrl' => $this->getView()->baseUrl() . '/public/temporary',
                        'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
                )));
            }
        }

        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'order' => 10,
            'type' => 'submit',
            'onclick' => "return submitForm('0', $('siteevent_create'), 'create');",
            'ignore' => true
        ));
    }

}
