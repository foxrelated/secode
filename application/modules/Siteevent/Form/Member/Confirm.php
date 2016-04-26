<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Confirm.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Member_Confirm extends Engine_Form {
    protected $_confirm;
    
    public function setConfirm($flage) {
      $this->_confirm = $flage;
      return $this;
    }

    public function getConfirm() {
      return $this->_confirm;
    }    
    
    public function init() {
        
        $this
                ->setTitle('Confirm Guest');

        $this->addElement('Radio', 'confirm', array(
           // 'label' => 'Confirm Guest',
            'description' => "Are you sure that you want to confirm this guest for participating in this event ? This action can not be undone.",
            'multiOptions' => array(
                1 => 'Yes',
                2 => 'No',
            ),
            'value' => $this->getConfirm(),
        ));
        
        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'label' => 'Confirm Guest',
        ));

        $this->addElement('Cancel', 'cancel', array(
            'prependText' => ' or ',
            'label' => 'cancel',
            'link' => true,
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            ),
        ));

        $this->addDisplayGroup(array(
            'submit',
            'cancel'
                ), 'buttons');
    }

}