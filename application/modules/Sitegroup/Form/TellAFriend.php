<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Tellfriend.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_TellAFriend extends Engine_Form {

  public $_error = array();

  public function init() {
    $this->setTitle('Tell a friend')
            ->setDescription('Please fill the form given below and send it to your friends. Through this you can let them know about this Group.')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitegroups_create');
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
    $this->addElement('Text', 'sitegroup_reciver_emails', array(
        'label' => 'To *',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Separate multiple addresses with commas.',
        'filters' => array(
            new Engine_Filter_Censor(),
        ),
    ));
		$text_value  = Zend_Registry::get('Zend_Translate')->_('Thought you would be interested in this.');
    $this->sitegroup_reciver_emails->getDecorator("Description")->setOption("placement", "append");
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

    // Element: SEND
    $this->addElement('Button', 'sitegroup_send', array(
        'label' => 'Tell a friend',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));
       // Element: cancel
    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
    $this->addElement('Cancel', 'sitegroup_cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
       // 'href' => 'history(-2)'
       'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    } else {
        $this->addElement('Cancel', 'sitegroup_cancel', array(
           'label' => 'cancel',
           'link' => true,
           'prependText' => ' or ',
          // 'href' => 'history(-2)'
          'onclick' => 'history.go(-1); return false;',
           'decorators' => array(
               'ViewHelper',
           ),
       ));
    }

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