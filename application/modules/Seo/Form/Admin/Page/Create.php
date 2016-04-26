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

class Seo_Form_Admin_Page_Create extends Engine_Form
{
  public function init()
  {

    $this->setTitle('Add New SEO Page')
      ->setDescription('Please fill out form below to create a new SEO page. A standard SocialEngine page is made up of 3 components: controller, module, and action. If you do not know these components setting of a page, you can use the Add Page Wizard tool, or simply view the HTML source of that page, the body\'s tag id attribute will has "global_page_MODULE-CONTROLLER-ACTION" pattern.');

    $this->addElement('text', 'page_module', array(
      'label' => 'Module Name',
      'description' => 'Example: article, listing, event, album etc..',
      'required' => true,
      'maxlength' => 63,
      'filters' => array(
        'StringTrim',
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
    )); 
      
    $this->addElement('text', 'page_controller', array(
      'label' => 'Controller Name',
      'description' => 'Example: index, topic, photo etc..',
      'required' => true,
      'maxlength' => 63,
      'filters' => array(
        'StringTrim',
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
    )); 
    
    $this->addElement('text', 'page_action', array(
      'label' => 'Action Name',
      'description' => 'Example: browse, create, edit, upload etc..',
      'required' => true,
      'maxlength' => 63,
      'filters' => array(
        'StringTrim',
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
    ));    

    $this->addElement('text', 'title', array(
      'label' => 'Page Title',
      'description' => 'Enter title that you would like to show on the browser title bar. If a page has "subject" associated with it (ex: album/article/listing view pages etc..), you can use "%subject_title%", "%subject_owner%" tag as a placeholder for the subject\'s title/name. For example: Album %subject_title% by %subject_owner%',
      'maxlength' => 63,
      'filters' => array(
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
    ));

    $this->addElement('Radio', 'title_mode', array(
      'label' => 'Global Title Display',
      'description' => 'By default, the global Site Name is prepend to the page title. You can disable this default feature by choosing one of the output display options below.',
      'allowEmpty' => false,
      'required' => true,
    	'value' => Seo_Model_Page::MODE_DEFAULT,
      'multiOptions' => array(
        Seo_Model_Page::MODE_DEFAULT => 'DEFAULT - Use Global Setting',
        Seo_Model_Page::MODE_OVERRIDE => 'OVERRIDE Mode - [Page Title]',
        Seo_Model_Page::MODE_PREPEND => 'PREPEND Mode - [Site Name - Page Title]',
        Seo_Model_Page::MODE_APPEND => 'APPEND Mode - [Page Title - Site Name]',
      ),
    ));
    
    
    $this->addElement('textarea', 'description', array(
      'label' => 'Meta Description',
      'description' => 'Enter description for page meta description tag. If a page has "subject" associated with it, you can use "%subject_description%" tag as a placeholder for the subject\'s description. For example: This is description for %subject_title%: %subject_description%',
      'maxlength' => 400,
      'filters' => array(
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '400'))
      ),
    ));

    $this->addElement('Radio', 'description_mode', array(
      'label' => 'Global Description Display',
      'description' => 'By default, the global Site Description is prepend to the page description. You can disable this default feature by choosing one of the output display options below.',
      'allowEmpty' => false,
      'required' => true,
      'value' => Seo_Model_Page::MODE_DEFAULT,
      'multiOptions' => array(
        Seo_Model_Page::MODE_DEFAULT => 'DEFAULT - Use Global Setting',
        Seo_Model_Page::MODE_OVERRIDE => 'OVERRIDE Mode - [Page Description]',
        Seo_Model_Page::MODE_PREPEND => 'PREPEND Mode - [Site Description - Page Description]',
        Seo_Model_Page::MODE_APPEND => 'APPEND Mode - [Page Description - Site Description]',
      )
    ));
    
    
    $keywords_required = false;
    $this->addElement('Text', 'keywords',array(
      'label'=>'Meta Keywords',
      'description' => 'Separate each keyword with commas. If a page has "subject" associated with it, you can use "%subject_keywords%" tag as a placeholder for the subject\'s keywords. For example: test, sample, more, %subject_keywords%, %subject_owner%',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));

    $this->addElement('Radio', 'keywords_mode', array(
      'label' => 'Global Keywords Display',
      'description' => 'By default, the global Site Keywords is prepend to the page keywords. You can disable this default feature by choosing one of the output display options below.',
      'allowEmpty' => false,
      'required' => true,
      'value' => Seo_Model_Page::MODE_DEFAULT,
      'multiOptions' => array(
        Seo_Model_Page::MODE_DEFAULT => 'DEFAULT - Use Global Setting',
        Seo_Model_Page::MODE_OVERRIDE => 'OVERRIDE Mode - [Page Keywords]',
        Seo_Model_Page::MODE_PREPEND => 'PREPEND Mode - [Site Keywords - Page Keywords]',
        Seo_Model_Page::MODE_APPEND => 'APPEND Mode - [Page Keywords - Site Keywords]',
      )
    ));    
    
    $this->addElement('textarea', 'extra_headers', array(
      'label' => 'Additional Headers',
      'description' => 'If you have additional header meta tag, script, style etc.. for the page, please enter them in the box below.',
    ));
    
    // Search
    $this->addElement('Checkbox', 'enabled', array(
      'label' => "Enabled?",
      'value' => 1,
    ));

    // Submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Page',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
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

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');    
  }
  
}