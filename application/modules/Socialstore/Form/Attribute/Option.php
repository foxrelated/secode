<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Option.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Socialstore_Form_Attribute_Option extends Engine_Form
{
  public function init()
  {
    $this->setMethod('POST')
      ->setAttrib('class', 'global_form_smoothbox');
	$this->setTitle('Attribute Option');
    // Add label
    $this->addElement('Text', 'label', array(
      'label' => 'Choice Label',
      'required' => true,
      'allowEmpty' => false,
    ));

   $this->addElement('Text', 'adjust_price',array(
      'label'=>'Adjust Price',
      'allowEmpty' => false,
      'required'=>true,
      'description' => '',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>    '0.00',
      'validators' => array(
        array('NotEmpty', true),
       	array('Float', true),
    )));
    
    // Add submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Add cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'onclick' => 'parent.Smoothbox.close();',
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}