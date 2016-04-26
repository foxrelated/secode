<?php

class Ynidea_Form_CreateTrophy extends Engine_Form {
    public $_error = array();

    public function init() {
        $this -> setDescription("Compose your new page below, then click 'Save' to publish trophy.") -> setAttrib('name', 'ynidea_create_trophy');
        $user = Engine_Api::_() -> user() -> getViewer();
        $user_level = Engine_Api::_() -> user() -> getViewer() -> level_id;

        $translate = Zend_Registry::get('Zend_Translate');

        $this -> addElement('Text', 'title', array('label' => 'Title', 'required' => true, 'title' => $translate -> translate('Title of trophy'), 'description' => 'Please give a unique name to this trophy', 'autofocus' => 'autofocus', 'filters' => array(new Engine_Filter_Censor(), 'StripTags', new Engine_Filter_StringLength( array('max' => '255')))));
        $this -> title -> getDecorator("Description") -> setOption("placement", "append");

        //thumbnail
        $this -> addElement('File', 'thumbnail', array('label' => 'Thumbnail', 'title' => $translate -> translate('Main image of page'), 'description' => 'You may upload an image (jpg, png, gif, jpeg) to illustrate this idea. It will help people remember it. ', ));
        $this -> thumbnail -> getDecorator("Description") -> setOption("placement", "append");
        $this -> thumbnail -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
        //Judges
        $this -> addElement('Text', 'to', array('label' => 'Judges', 'autocomplete' => 'off', 'description' => 'Judges.', 'filters' => array(new Engine_Filter_Censor(), ), ));
        $this -> to -> getDecorator("Description") -> setOption("placement", "append");

        // Init to Values
        $this -> addElement('Hidden', 'toValues', array('label' => '', 'order' => 3, 'validators' => array('NotEmpty'), 'filters' => array('HtmlEntities'), ));
        Engine_Form::addDefaultDecorators($this -> toValues);

        // Full Description
        $allowed_html = Engine_Api::_() -> authorization() -> getPermission($user_level, 'ynidea_idea', 'auth_html');
        $upload_url = "";
        if (Engine_Api::_() -> authorization() -> isAllowed('album', $user, 'create')) {
            $upload_url = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'upload-photo'), 'ynidea_general', true);
        }
        $theme_advanced_buttons1 = "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,|,charmap,emotions,iespell,media";
        $theme_advanced_buttons2 = "pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,fullscreen";
        $theme_advanced_buttons3 = "";
        $this -> addElement('TinyMce', 'description', array('label' => 'Full Description', 'required' => true, 'allowEmpty' => false, 'decorators' => array('ViewHelper'), 'editorOptions' => array('bbcode' => 1, 'html' => 1, 'mode' => 'exact', 'elements' => 'description', 'upload_url' => $upload_url, 'theme_advanced_buttons1' => $theme_advanced_buttons1, 'theme_advanced_buttons2' => $theme_advanced_buttons2, 'theme_advanced_buttons3' => $theme_advanced_buttons3, 'theme_advanced_resizing' => true, 'height' => '500px', 'width' => '900px'), 'filters' => array(new Engine_Filter_Censor(), new Engine_Filter_Html( array('AllowedTags' => $allowed_html))), ));

        if (Ynidea_Api_Core::checkFundraisingPlugin()) {
            $this -> addElement('Checkbox', 'allow_campaign', array('label' => 'Allow other members to create fundraising campaign on my trophy', 'value' => 0, ));
        }
        $this -> addElement('Button', 'submit', array('label' => 'Save', 'type' => 'submit', 'ignore' => true, 'style' => 'margin-top:10px;', 'decorators' => array('ViewHelper', ), ));
        // Element: cancel
        $this -> addElement('Cancel', 'cancel', array('label' => 'cancel', 'link' => true, 'prependText' => ' or ', 'onclick' => '', 'style' => 'margin-top:20px;', 'decorators' => array('ViewHelper', ), ));

        // DisplayGroup: buttons
        $this -> addDisplayGroup(array('submit', 'cancel', ), 'buttons', array('decorators' => array('FormElements', 'DivDivDivWrapper'), ));
    }

}
