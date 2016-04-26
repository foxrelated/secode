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
 
 
 
class Seo_Form_Admin_Page_Filter extends Engine_Form
{
  
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this
      ->setAttribs(array(
        'id' => 'seo_admin_filter_form',
        'class' => 'global_form_box',
      ));

    $this->addElement('Text', 'page_module', array(
      'label' => 'Module'
    ));
      
    $this->addElement('Text', 'page_controller', array(
      'label' => 'Controller'
    ));
    
    $this->addElement('Text', 'page_action', array(
      'label' => 'Action'
    ));
 

    foreach( $this->getElements() as $fel ) {
      if( $fel instanceof Zend_Form_Element ) {
        
        $fel->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
          ->addDecorator('HtmlTag', array('tag' => 'div', 'id'  => $fel->getName() . '-search-wrapper', 'class' => 'form-search-wrapper'));
        
      }
    }  
    
   // $submit = new Engine_Form_Element_Submit('filtersubmit', array('type' => 'submit'));
    $submit = new Engine_Form_Element_Button('filtersubmit', array('type' => 'submit'));
    $submit
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElement($submit);
      
    $this->addElement('Hidden', 'order', array(
      'order' => 1001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
      'order' => 1002,
    ));


    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'seo', 'controller'=>'pages'), 'admin_default', true));
  }  
  

}