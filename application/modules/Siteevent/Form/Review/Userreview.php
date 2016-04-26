<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Userreview.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Review_Userreview extends Engine_Form {

  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem($item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {

    //GET DECORATORS
    $this->loadDefaultDecorators();
    $getUserItem = $this->getItem();
    $user_title = "<b>" . $getUserItem->getTitle() . "</b>";

    //IF NOT HAS POSTED THEN THEN SET FORM
    $this->setTitle('Write a Review')
            ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Give your ratings and opinion for %s below:"), $user_title))
            ->setAttrib('name', 'siteevent_create')
            ->setAttrib('id', 'siteevent_userreview_create')
            ->getDecorator('Description')->setOption('escape', false);


    $this->addElement('Textarea', 'title', array(
        'label' => 'One-line summary',
        'rows' => 1,
        'allowEmpty' => false,
        'maxlength' => 63,
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
        ),
    ));

    $this->addElement('Textarea', 'description', array(
        'label' => 'Summary',
        'rows' => 3,
        'allowEmpty' => false,
        //'maxlength' => 63,
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
        ),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Submit',
        'order' => 10,
        'type' => 'submit',
        'onclick' => "return submitForm('0', $('siteevent_userreview_create'), 'create');",
        'ignore' => true
    ));
  }
}