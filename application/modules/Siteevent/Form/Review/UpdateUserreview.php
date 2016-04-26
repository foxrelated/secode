<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UpdateUserreview.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Review_UpdateUserreview extends Engine_Form {

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
    $getItemUser = $this->getItem();
    $user_title = "<b>" . $getItemUser->getTitle() . "</b>";

    //IF NOT HAS POSTED THEN THEN SET FORM
    $this->setTitle('Update your Review')
            ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("You can update your review for %s below:"), $user_title))
            ->setAttrib('name', 'siteevent_update')
            ->setAttrib('id', 'siteevent_update')
            ->setAttrib('class', 'siteevent_review_form')
            ->setAttrib('style', 'display:block')->getDecorator('Description')->setOption('escape', false);

    $this->addElement('Textarea', 'description', array(
        'label' => 'Summary',
        'rows' => 3,
        'allowEmpty' => true,
        'required' => false,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
        ),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Add your Opinion',
        'order' => 10,
        'type' => 'submit',
        'onclick' => "return submitForm('0', $('siteevent_update'), 'update');",
        //'onclick' => "return submitForm('$hasPosted', $('siteevent_update'), 'update');",
        'ignore' => true
    ));
  }

}