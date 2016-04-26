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
class Siteevent_Form_Editor_Create extends Engine_Form {

    protected $_profileTypeReview;

    public function getProfileTypeReview() {
        return $this->_profileTypeReview;
    }

    public function setProfileTypeReview($profileTypeReview) {
        $this->_profileTypeReview = $profileTypeReview;
        return $this;
    }

    public function init() {

        //GET VIEWER INFO
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $siteevent_title = "<b>" . $siteevent->title . "</b>";
        $this->loadDefaultDecorators();

        $this->setTitle('Write an Editor Review')
                ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Give your ratings and opinion for %s below:"), $siteevent_title))
                ->setAttrib('name', 'siteevent_create')
                ->setAttrib('id', 'siteevent_create')
                ->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Textarea', 'pros', array(
            'label' => 'The Good',
            'allowEmpty' => false,
            'attribs' => array('rows' => 3),
            'maxlength' => 500,
            'required' => true,
            'filters' => array(
            ),
        ));

        $this->addElement('Textarea', 'cons', array(
            'label' => 'The Bad',
            'allowEmpty' => false,
            'attribs' => array('rows' => 3),
            'maxlength' => 500,
            'required' => true,
            'filters' => array(
            ),
        ));

        $this->addElement('Textarea', 'title', array(
            'label' => 'The Bottom Line',
            'allowEmpty' => false,
            'attribs' => array('rows' => 3),
            'maxlength' => 500,
            'required' => true,
            'filters' => array(
            ),
        ));

        $profileTypeReview = $this->getProfileTypeReview();
        if (!empty($profileTypeReview)) {

            if (!$this->_item) {
                $customFields = new Siteevent_Form_Custom_Standard(array(
                    'item' => 'siteevent_review',
                    'topLevelId' => 1,
                    'topLevelValue' => $profileTypeReview,
                    'decorators' => array(
                        'FormElements'
                )));
            } else {
                $customFields = new Siteevent_Form_Custom_Standard(array(
                    'item' => $this->getItem(),
                    'topLevelId' => 1,
                    'topLevelValue' => $profileTypeReview,
                    'decorators' => array(
                        'FormElements'
                )));
            }

            $customFields->removeElement('submit');
            if ($customFields->getElement($defaultProfileId)) {
                $customFields->getElement($defaultProfileId)
                        ->clearValidators()
                        ->setRequired(false)
                        ->setAllowEmpty(true);
            }

            $this->addSubForms(array(
                'fields' => $customFields
            ));
        }

        $this->addElement('Textarea', 'body', array(
            'label' => 'Conclusion',
            'allowEmpty' => true,
            'required' => false,
            'filters' => array(
            ),
        ));

        if ($this->_item && $this->_item->status == 1) {
            $this->addElement('Textarea', 'update_reason', array(
                'label' => 'Reason Of Updation',
                'allowEmpty' => false,
                'attribs' => array('rows' => 3),
                'required' => true,
                'filters' => array(
                ),
            ));
        }

        $this->addElement('Select', 'status', array(
            'label' => 'Status',
            'multiOptions' => array("1" => "Published", "0" => "Saved As Draft"),
            'description' => 'If this entry is published, it cannot be switched back to draft mode.'
        ));
        $this->status->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => Engine_Api::_()->getItem('siteevent_event', $event_id)->getHref(),
            'decorators' => array(
                'ViewHelper',
            ),
        ));

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
