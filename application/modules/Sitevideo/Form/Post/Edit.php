<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Post_Edit extends Engine_Form {

    public function init() {

        $this->setTitle('Edit Post');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.tinymceditor', 1)) {
            $this->addElement('Textarea', 'body', array(
                //'label' => 'Body',
                'allowEmpty' => false,
                'required' => true,
                'filters' => array(
                    new Engine_Filter_Censor(),
                    new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_EnableLinks(),
                ),
            ));
        } else {
            $this->addElement('TinyMce', 'body', array(
                //'label' => 'Body',
                'allowEmpty' => false,
                'required' => true,
                'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),
                'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions(),
                'filters' => array(new Engine_Filter_Censor()),
            ));
        }

        $this->addElement('Button', 'submit', array(
            'label' => 'Edit Post',
            'ignore' => true,
            'type' => 'submit',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'prependText' => ' or ',
            'type' => 'link',
            'link' => true,
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    }

}
