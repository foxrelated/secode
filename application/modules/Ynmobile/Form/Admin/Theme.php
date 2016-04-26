<?php
/**
 * @author    Nam Nguyen <namnv@younetco.com>
 * @since     4.11
 * @copyright YouNet Company
 */


/**
 * Class Ynmobile_Form_Admin_Product
 *
 * Define Form Edit for theme construct
 */
class Ynmobile_Form_Admin_Theme extends Engine_Form
{

    public function init()
    {
        $this->setTitle('Mobile Application Theme Editor');

        $this->addElement('Text', 'name', array(
            'required' => true,
            'label'    => 'Theme Name',
            'value'    => 'Sample theme',
        ));

        $this->addElement('Text', 'positive_color', array(
            'label'       => 'Brand Color',
            'required'    => true,
            'placeholder' => '#01a0db',
            'value'       => '#01a0db',
        ));

//        $this->addElement('Text', 'light_color', array(
//            'label'       => 'Light Color',
//            'required'    => true,
//            'placeholder' => '#ffffff',
//            'value'       => '#ffffff',
//        ));
//
//        $this->addElement('Text', 'stable_color', array(
//            'label'       => 'Stable Color',
//            'required'    => true,
//            'placeholder' => '#f8f8f8',
//            'value'       => '#f8f8f8',
//        ));
//
//
//        $this->addElement('Text', 'calm_color', array(
//            'label'       => 'Calm Color',
//            'required'    => true,
//            'placeholder' => '#43cee6',
//            'value'       => '#43cee6',
//        ));
//
//
//        $this->addElement('Text', 'balanced_color', array(
//            'label'       => 'Balanced Color',
//            'required'    => true,
//            'placeholder' => '#66cc33',
//            'value'       => '#66cc33',
//        ));
//
//
//        $this->addElement('Text', 'energized_color', array(
//            'label'       => 'Energized Color',
//            'required'    => true,
//            'placeholder' => '#f0b840',
//            'value'       => '#f0b840',
//        ));
//
//
//        $this->addElement('Text', 'assertive_color', array(
//            'label'       => 'Assertive Color',
//            'required'    => true,
//            'placeholder' => '#ef4e3a',
//            'value'       => '#ef4e3a',
//        ));
//
//
//        $this->addElement('Text', 'royal_color', array(
//            'label'       => 'Royal Color',
//            'required'    => true,
//            'placeholder' => '#8a6de9',
//            'value'       => '#8a6de9',
//        ));
//
//        $this->addElement('Text', 'dark_color', array(
//            'label'       => 'Dark Color',
//            'required'    => true,
//            'placeholder' => '#444',
//            'value'       => '#444',
//        ));
//
//        $this->addElement('Text', 'base_background_color', array(
//            'label'       => 'Base Background Color',
//            'required'    => true,
//            'placeholder' => '#ffffff',
//            'value'       => '#ffffff',
//        ));
//
//        $this->addElement('Text', 'base_color', array(
//            'label'       => 'Base Color',
//            'required'    => true,
//            'placeholder' => '#000000',
//            'value'       => '#000000',
//        ));

//        $this->addElement('Checkbox', 'is_publish', array(
//            'label'    => 'Publish this theme',
//            'required' => false,
//            'checked'  => true,
//        ));

        // Submit Button
        $this->addElement('Button', '_submit', array(
            'label' => 'Save',
            'type'  => 'submit',
        ));
    }
}