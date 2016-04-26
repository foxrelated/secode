<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Update.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Review_Update extends Engine_Form {

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
        $getItemEvent = $this->getItem();
        $siteevent_title = "<b>" . $getItemEvent->title . "</b>";
        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
        $params = array();
        $params['resource_id'] = $getItemEvent->event_id;
        $params['resource_type'] = $getItemEvent->getType();
        $params['viewer_id'] = $viewer_id;
        $params['type'] = 'user';
        $hasPosted = $reviewTable->canPostReview($params);

        //IF NOT HAS POSTED THEN THEN SET FORM
        $this->setTitle('Update your Review')
                ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("You can update your review for %s below:"), $siteevent_title))
                ->setAttrib('name', 'siteevent_update')
                ->setAttrib('id', 'siteevent_update')
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
            'onclick' => "return submitForm('$hasPosted', $('siteevent_update'), 'update');",
            'ignore' => true
        ));
    }

}