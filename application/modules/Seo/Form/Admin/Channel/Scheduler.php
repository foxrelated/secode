<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Seo
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

class Seo_Form_Admin_Channel_Scheduler extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Task Scheduler Settings')
      ->setDescription('These settings affect automatically sitemap building and submitting to search engine.');

    $this->addElement('Text', 'timeout', array(
      'label' => 'Timeout (in seconds)',
      'description' => 'How often (ie, every XYZ seconds) should this task be run? Recommended value is 604800 (seconds), which is once a week.',
    	'filters' => array(
        'Int'
      ),
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        'Digits',
        new Zend_Validate_GreaterThan(0)
      ),      
    ));  
    /*
    $this->addElement('Radio', 'enabled', array(
      'label' => 'Enable Scheduler?',
      'description' => 'You can safely disable this feature by select NO, otherwise YES to activate it.',
    	'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
   
    ));
    */
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');    
    
  }

}