<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EmailMe.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_EmailMe extends Engine_Form {

    public $_error = array();

    public function init() {

        $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        $GroupTitle = $sitegroup->title;

        $this->setTitle('Email Me')
                ->setDescription('Please fill the form given below to contact this Group.')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'sitegroups_create');
        $this->setAttrib('class', 'global_form seaocore_form_comment');
        $viewr_name = "";
        $viewr_email = "";
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() > 0) {
            $viewr_name = $viewer->getTitle();
            $viewr_email = $viewer->email;
        }
        // TITLE
        $this->addElement('Text', 'sitegroup_sender_name', array(
            'label' => 'Your Name *',
            'allowEmpty' => false,
            'required' => true,
            'value' => $viewr_name,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        )));

        //SENDER EMAIL
        $this->addElement('Text', 'sitegroup_sender_email', array(
            'label' => 'Your Email *',
            'allowEmpty' => false,
            'required' => true,
            'value' => $viewr_email,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        )));

        // RECIVER EMAILS
//     $this->addElement('Text', 'sitegroup_reciver_emails', array(
//         'label' => 'To *',
//         'allowEmpty' => false,
//         'required' => true,
//         'description' => 'Separate multiple addresses with commas.',
//         'filters' => array(
//             new Engine_Filter_Censor(),
//         ),
//     ));


        $text_value = Zend_Registry::get('Zend_Translate')->_('Thought you would be interested in this.');
//     $this->sitegroup_reciver_emails->getDecorator("Description")->setOption("placement", "append");

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $photo = $view->itemPhoto($sitegroup, 'thumb.icon', "", array("style" => "float: left;height: 35px;margin-right: 5px;width: 35px;"));
        $title = $photo . "<a href='" . $view->url(array('group_url' => $sitegroup->group_url), 'sitegroup_entry_view') . "'  target='_blank'>" . ucfirst($sitegroup->getTitle()) . "</a>";
        $this->addElement('Dummy', 'sitegroup_reciver_emails', array(
            'label' => 'To *',
            'description' => $title,
        ));
        $this->getElement('sitegroup_reciver_emails')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));



        // MESSAGE
        $this->addElement('textarea', 'sitegroup_message', array(
            'label' => 'Message *',
            'required' => true,
            'allowEmpty' => false,
            'attribs' => array('rows' => 24, 'cols' => 150, 'style' => 'width:230px; max-width:400px;height:120px;'),
            'value' => $text_value,
            'description' => 'You can send a personal note in the mail.',
            'filters' => array(
                'StripTags',
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));
        $this->sitegroup_message->getDecorator("Description")->setOption("placement", "append");
        // SEND COPY TO ME
        $this->addElement('Checkbox', 'sitegroup_send_me', array(
            'label' => "Send a copy to my email address.",
        ));
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.captcha.post', 1) && empty($viewer_id)) {
            if (Engine_Api::_()->hasModuleBootstrap('siterecaptcha')) {
                Zend_Registry::get('Zend_View')->recaptcha($this);
            } else {
                $this->addElement('captcha', 'captcha', array(
                    'description' => 'Please type the characters you see in the image.',
                    'captcha' => 'image',
                    'required' => true,
                    'captchaOptions' => array(
                        'wordLen' => 6,
                        'fontSize' => '30',
                        'timeout' => 300,
                        'imgDir' => APPLICATION_PATH . '/public/temporary/',
                        'imgUrl' => $this->getView()->baseUrl() . '/public/temporary',
                        'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
                )));
                $this->captcha->getDecorator("Description")->setOption("placement", "append");
            }
        }

        // Element: SEND
        $this->addElement('Button', 'sitegroup_send', array(
            'label' => 'Send',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        // Element: cancel
        $this->addElement('Cancel', 'sitegroup_cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            // 'href' => 'history(-2)'
            //'onclick' => 'history.go(-1); return false;',
            'onclick' => 'javascript:parent.Smoothbox.close()',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array(
            'sitegroup_send',
            'sitegroup_cancel',
                ), 'sitegroup_buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}

?>