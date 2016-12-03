<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Settings
 *
 * @author isabek
 */

/**
 * Global Settings Form
 * 
 *   Form Elements
 * 
 * Disable menu? (CheckBox, default:cheked)
 * Show (Select Box, default:all [registered members, unregistered members])
 * Location(RadioButtons[2], default: right[left])
 */
class Socialslider_Form_Admin_Settings extends Engine_Form {

    public function init() {
        
        
        $this->addElement('Checkbox', 'enable', array(
            'label' => 'Ok',
            'description' => 'Visible the plugin',
            'value' => Engine_Api::_()->getApi('settings','core')->getSetting('socialslider.enable',1)
        ));

        $this->addElement('Select', 'show', array(
            'label' => 'Allowed Users',
            'multiOptions' => array(
                0 => 'All',
                1 => 'Registered members',
                2 => 'Unregistered members'
            ),
            'value'=>  Engine_Api::_()->getApi('settings','core')->getSetting('socialslider.show',0)
            
        ));

        $this->addElement('Radio', 'location', array(
            'label' => 'Location',
            'multiOptions' => array(
                'right' => 'Right',
                'left' => 'Left'
            ),
            'value' => Engine_Api::_()->getApi('settings','core')->getSetting('socialslider.location','right')
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}

?>
