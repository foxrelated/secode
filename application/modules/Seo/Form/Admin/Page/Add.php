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

class Seo_Form_Admin_Page_Add extends Engine_Form
{
  public function init()
  {

    $this->setTitle('Add SEO Page Wizard')
      ->setDescription('Please provide the page URL which would like to add SEO support for it, and the system will try to parse its module / controller / action name.');

    $this->addElement('text', 'url', array(
      'label' => 'Page URL',
      'filters' => array(
        'StringTrim',
      ),
      'style' => 'width: 98%'
    )); 
      
    // Submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Parse URL',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    //
    
    $createUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'seo', 'controller' => 'pages', 'action' => 'create'), 'admin_default', true);
    
    
    $this->addElement('Cancel', 'skip', array(
      'prependText' => ' - ',
      'label' => 'Skip Wizard',
      'link' => true,
      //'href' => 'javascr',
      'onclick' => 'parent.window.location.href="'.$createUrl.'";',
      'decorators' => array(
        'ViewHelper'
      ),
    ));    
    
    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      //'href' => 'javascr',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array('submit', 'skip', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');    
  }
  
}