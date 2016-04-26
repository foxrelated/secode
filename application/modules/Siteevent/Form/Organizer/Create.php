<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Standard.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Organizer_Create extends Engine_Form {
    /* Custom */

    protected $_item;
    protected $_isEdit;

    /* General */

    public function getItem() {
        return $this->_item;
    }

    //WE ARE JUST CHANGING IN THIS FUNCTION
    public function setItem($item) {
        $this->_item = $item;
        return $this;
    }

    public function getIsEdit() {
        return $this->_isEdit;
    }

    //WE ARE JUST CHANGING IN THIS FUNCTION
    public function setIsEdit($item) {
        $this->_isEdit = $item;
        return $this;
    }

    public function init() {

        $this->addElement('Text', 'host_title', array(
            'label' => 'Host Name',
        ));
        if (!$this->getIsEdit()) {
            $this->host_title->setDecorators(array(array('ViewScript', array(
                        'viewScript' => '_hostDetails.tpl',
                        'host' => $this->getItem(),
                        'label' => 'Host Name',
                        'fieldType' => 'host_title',
                        'class' => 'form element'
            ))));
        }
        $allowedInfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.hostinfo', array('body', 'sociallinks'));
        if (in_array('body', $allowedInfo)) {
            $this->addElement('TinyMce', 'host_description', array(
                'label' => 'Host Description',
                'editorOptions' => array('mode' => "exact",
                    'elements' => $this->getIsEdit() ? 'host_description' : 'host-host_description',
                    'html' => true,
                ),
                'attribs' => array('class' => 'organizer_info'),
                'filters' => array(new Engine_Filter_Censor())
            ));
        }

        $this->addElement('File', 'host_photo', array(
            'label' => 'Host Photo',
            'attribs' => array('class' => 'organizer_info')
        ));
        $this->host_photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');

        if (in_array('sociallinks', $allowedInfo)) {
            $this->addElement('dummy', 'host_links', array(
                'label' => 'Include a link to my social pages e.g. Facebook, Twitter or Website',
                'value' => false,
                'decorators' => array(array('ViewScript', array(
                            'viewScript' => '_hostDetails.tpl',
                            'fieldType' => 'host_links',
                            'label' => "Host's Social Links",
//                      'hasPopulate'=>$this->getIsEdit(),
//                      'host' => $this->getItem(),
                            'class' => 'form element'
                        )))
            ));
        }
        /* $this->addElement('Hidden', 'host_facebook', array());
          $this->addElement('Hidden', 'host_twitter', array());
          $this->addElement('Hidden', 'host_website', array()); */
    }

}