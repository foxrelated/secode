<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Create.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_Review_Create extends Engine_Form {

  public function init() {
    $this
        ->setTitle('Post your Review')
        ->setAttrib('id', 'list_review_create');

    $this->addElement('Text', 'title', array(
            'label' => 'Review Title',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                    new Engine_Filter_Censor(),
                    new Engine_Filter_HtmlSpecialChars(),
            ),
            'validators' => array(
                    array('StringLength', true, array(1, 64)),
            )
    ));

    $this->addElement('Textarea', 'body', array(
            'label' => 'Review Body',
            'required' => true,
            'allowEmpty' => false,
            'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
            'filters' => array(
                    'StripTags',
                    new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_EnableLinks(),
                    new Engine_Filter_Censor(),
            ),
    ));

    $this->addElement('Button', 'submit', array(
            'label' => 'Post Review',
            'ignore' => true,
            'type' => 'submit',
            'style' => 'margin-left:116px;',
            'decorators' => array(
                    'ViewHelper',
            ),
    ));

    $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
              'prependText' => ' or ',
            'type' => 'submit',
             'link' => true,
            'onclick' => 'javascript:parent.Smoothbox.close()',
            'decorators' => array(
                    'ViewHelper',
            ),
    ));
  }
}