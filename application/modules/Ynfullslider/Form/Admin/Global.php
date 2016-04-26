<?php

class Ynfullslider_Form_Admin_Global extends Engine_Form {

    public function init() {
        $this->setTitle('Global Settings')->setDescription('These settings affect all members in your community.');

        $this->addElement('Text', 'ynfullslider_ffmpeg_path', array(
            'label' => 'Path to FFMPEG',
            'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfullslider.ffmpeg.path', ''),
        ));

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}