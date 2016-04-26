<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: TellFriend.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_TellAFriend extends Engine_Form {

  public $_error = array();

  public function init() {

    $this->setTitle('Tell a friend')
        ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
        ->setAttrib('name', 'lists_create');

    $this->addElement('Text', 'sender_name', array(
            'label' => 'Your Name *',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
                    new Engine_Filter_StringLength(array('max' => '63')),
        )));

    $this->addElement('Text', 'sender_email', array(
            'label' => 'Your Email *',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
                    new Engine_Filter_StringLength(array('max' => '63')),
        )));

    $this->addElement('Text', 'reciver_emails', array(
            'label' => 'To *',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Separate multiple addresses with commas',
            'filters' => array(
                    new Engine_Filter_Censor(),
            ),
    ));
    $this->reciver_emails->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('textarea', 'message', array(
            'label' => 'Message *',
            'required' => true,
            'allowEmpty' => false,
            'attribs' => array('rows' => 24, 'cols' => 150, 'style' => 'width:230px; max-width:400px;height:120px;'),
            'value' => 'Thought you would be interested in this.',
            'description' => 'you can send a personal note in the mail',
            'filters' => array(
                    'StripTags',
                    new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_EnableLinks(),
                    new Engine_Filter_Censor(),
            ),
    ));
    $this->message->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Checkbox', 'send_me', array(
            'label' => "Send a copy to my email address",
    ));

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('list.captcha.post', 1) && empty($viewer_id)) {
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

    $this->addElement('Button', 'send', array(
            'label' => 'Tell a friend',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                    'ViewHelper',
            ),
    ));

    $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'javascript:parent.Smoothbox.close()',
            'decorators' => array(
                    'ViewHelper',
            ),
    ));

    $this->addDisplayGroup(array(
            'send',
            'cancel',
        ), 'buttons', array(
            'decorators' => array(
                    'FormElements',
                    'DivDivDivWrapper'
            ),
    ));
  }
}