<?php

class Ynfullslider_Form_Admin_Search extends Engine_Form
{

    public function init() {
        $this->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            // ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => ''))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

        $this->setAttribs(
            array(
                'id' => 'filter_form',
                'class' => 'global_form_box'
            )
        );
        $this->setMethod('get');

        $this->addElement('Text', 'title', array(
            // 'label' => 'Search slider'
            'placeholder' => Zend_Registry::get('Zend_Translate')->_('Search slider')
        ));

        $this->addElement('hidden', 'status', array(
            'order' => 101
        ));
        // Buttons
        $this->addElement('Button', 'button', array(
            'label' => 'Search',
            'type' => 'submit',
        ));

        $this->button->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'ynfullslider_btn_search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }
}