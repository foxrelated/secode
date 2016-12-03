<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Rename.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Topic_Rename extends Engine_Form {

  public function init() {
    
    $this->setTitle('Rename Topic');

    $this->addElement('Text', 'title', array(
        'label' => 'Title',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('StringLength', true, array(1, 64)),
        ),
        'filters' => array(
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Rename Topic',
        'ignore' => true,
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'prependText' => ' or ',
        'type' => 'link',
        'link' => true,
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}