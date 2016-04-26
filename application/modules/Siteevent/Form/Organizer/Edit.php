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
class Siteevent_Form_Organizer_Edit extends Siteevent_Form_Organizer_Create {

    public function init() {

        parent::init();
        $this->setTitle("Edit Host Information")
                ->setDescription('Edit the information of host using the form below and then click on "Save Changes" to save it.<br/>Note: Modifying this host’s information will apply reflect on all the events hosted by this host.');
        if ($this->host_links) {
            $viewScriptOptions = $this->host_links->getDecorator('ViewScript')->getOptions();
            $viewScriptOptions['host'] = $this->getItem();
            $this->host_links->setDecorators(array(array('ViewScript', $viewScriptOptions)));
        }
        $this->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'prependText' => ' or ',
            'label' => 'cancel',
            'link' => true,
            'onclick' => "javascript:parent.Smoothbox.close();",
            'decorators' => array(
                'ViewHelper'
            ),
        ));


        $this->addDisplayGroup(array(
            'execute',
            'cancel',
                ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}