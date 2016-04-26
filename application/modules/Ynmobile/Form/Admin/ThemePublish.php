<?php
/**
 * @since  4.11
 * @author Nam Nguyen
 */

/**
 * Class Ynmobile_Form_Admin_ThemePublish
 */
class Ynmobile_Form_Admin_ThemePublish extends Engine_Form
{
    public function init()
    {
        $this
            ->setTitle('Publish This Theme')
            ->setAttrib('class', 'global_form_popup');

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label'      => 'Publish This Theme',
            'type'       => 'submit',
            'ignore'     => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label'       => 'cancel',
            'link'        => true,
            'prependText' => ' or ',
            'href'        => '',
            'onclick'     => 'parent.Smoothbox.close();',
            'decorators'  => array(
                'ViewHelper'
            )
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    }
}