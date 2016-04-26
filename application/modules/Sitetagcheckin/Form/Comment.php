<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Comment.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Form_Comment extends Engine_Form {

  public function init() {
    $this->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->setAttrib('class', null)
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                        'module' => 'sitetagcheckin',
                        'controller' => 'activity',
                        'action' => 'comment',
                            ), 'default'));

    //$allowed_html = Engine_Api::_()->getApi('settings', 'core')->core_general_commenthtml;
    $viewer = Engine_Api::_()->user()->getViewer();
    $allowed_html = "";
    if ($viewer->getIdentity()) {
      $allowed_html = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'commentHtml');
    }
    $this->addElement('Textarea', 'body', array(
        'rows' => 1,
        'decorators' => array(
            'ViewHelper'
        ),
        'filters' => array(
            new Engine_Filter_Html(array('AllowedTags' => $allowed_html)),
            new Engine_Filter_Censor(),
        ),
    ));

    if (Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) {
      $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions());
    }

    $this->addElement('Button', 'submit', array(
        'type' => 'submit',
        'ignore' => true,
        'label' => 'Post Comment',
        'decorators' => array(
            'ViewHelper',
        )
    ));

    $this->addElement('Hidden', 'action_id', array(
        'order' => 990,
        'filters' => array(
            'Int'
        ),
    ));

    $this->addElement('Hidden', 'return_url', array(
        'order' => 991,
        'value' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array())
    ));
  }

  public function setActionIdentity($action_id, $checkin_id) {
    $this
            ->setAttrib('id', 'activity-comment-form-' . $checkin_id . '-' . $action_id)
            ->setAttrib('class', 'activity-comment-form')
            ->setAttrib('style', 'display: none;');
    $this->action_id
            ->setValue($action_id)
            ->setAttrib('id', 'activity-comment-id-' . $checkin_id . '-' . $action_id)
            ->setAttrib('style', 'display: none;');
    $this->submit //->getDecorator('HtmlTag')
            ->setAttrib('id', 'activity-comment-submit-' . $checkin_id . '-' . $action_id)
            ->setAttrib('style', 'display: none;');
    ;

    $this->body
            ->setAttrib('id', 'activity-comment-body-' . $checkin_id . '-' . $action_id)
    ;

    return $this;
  }

  public function renderFor($action_id) {
    return $this->setActionIdentity($action_id)->render();
  }

}