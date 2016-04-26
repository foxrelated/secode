<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Group.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Layout_Content_MobileGroup extends Engine_Form {

  public function init() {

    $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'save', 'controller' => 'mobile-layout', 'module' => 'sitegroup'), 'default', true))
            ->setAttrib('class', 'grouplayout_layoutbox_menu_editinfo_form')
            ->setAttrib('id', 'grouplayout_content_groupinfo')
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('HtmlTag', array('tag' => 'ul'))
            ->addDecorator('FormErrors', array('placement' => 'PREPEND', 'escape' => false))
            ->addDecorator('FormMessages', array('placement' => 'PREPEND', 'escape' => false))
            ->addDecorator('Form')
    ;

    $this->addElement('Text', 'description', array(
        'label' => 'Group Description <span>(meta tag)</span>',
        'decorators' => array(
            array('ViewHelper'),
            array('Label', array('tag' => 'span', 'escape' => false)),
            array('HtmlTag', array('tag' => 'li')),
        ),
    ));

    $this->addElement('Text', 'keywords', array(
        'label' => 'Group Keywords <span>(meta tag)</span>',
        'allowEmpty' => false,
        'validators' => array(
        ),
        'decorators' => array(
            array('ViewHelper'),
            array('Label', array('tag' => 'span', 'escape' => false)),
            array('HtmlTag', array('tag' => 'li')),
        ),
    ));

    $this->addElement('Hidden', 'mobilecontentgroup_id', array(
        'validators' => array(
            array('NotEmpty'),
            array('Int'),
        ),
    ));
  }

}

?>