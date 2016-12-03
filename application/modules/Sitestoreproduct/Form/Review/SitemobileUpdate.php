<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Update.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
class Sitestoreproduct_Form_Review_SitemobileUpdate extends Engine_Form {

  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem($item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {

    //GET VIEWER INFO
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET ZEND REQUEST
    //GET DECORATORS
    $this->loadDefaultDecorators();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $getItemProduct = $this->getItem();
    $sitestoreproduct_title = "<b>" . $getItemProduct->title . "</b>";
    //GET REVIEW TABLE
    $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
    $params = array();
    $params['resource_id'] = $getItemProduct->product_id;
    $params['resource_type'] = $getItemProduct->getType();
    $params['viewer_id'] = $viewer_id;
    $params['type'] = 'user';
    $hasPosted = $reviewTable->canPostReview($params);

    //IF NOT HAS POSTED THEN THEN SET FORM
    $this->setTitle('Update your Review')
            ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("You can update your review for %s below:"), $sitestoreproduct_title))
            ->setAttrib('name', 'sitestoreproduct_update')
            ->setAttrib('id', 'sitestoreproduct_update')
            ->setAttrib('style', 'display:block')->getDecorator('Description')->setOption('escape', false);

    $this->addElement('Textarea', 'body', array(
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
        'ignore' => true
    ));
  }

}