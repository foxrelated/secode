<?php

class Ynfullslider_Form_Admin_Slider_Finish extends Engine_Form
{
    public function init() {

        $this
            ->setAttrib('class', 'global_form')
        ;

        $this -> addElement('Dummy', 'finish', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_slider_edit_finish.tpl',
                )
            )),
        ));
    }
}
?>