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
 
 
 
class Seo_Form_Admin_Channel_Create extends Engine_Form
{
  protected $_item;
  
  public function init()
  {
    $this->setTitle('Add SEO Sitemap Channel')
      ->setDescription('Please fill out the form below to add new supported channel.')
     // ->setAttrib('class', 'global_form_popup')
      ;

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'description' => 'Enter name of this channel',
      'allowEmpty' => false,
      'required' => true,
    ));
    
    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'attribs' => array(
        'rows' => '2',
        'cols' => '80',
      ),
    ));

    if (!$this->_item || $this->_item->custom)
    {
      $this->addElement('Text', 'plugin', array(
        'label' => 'Plugin Class Name',
        'description' => 'Enter channel\'s plugin classname where items would be pulled, it must extends Seo_Plugin_Channel_Abstract. Please leave this field blank if you do not know or really understand what this is for.',
        'attribs' => array(
          'class' => 'text',
          'rows' => '2',
        ),
        'validators' => array(
          new Engine_Validate_Callback(array($this, 'validatePlugin')),
        ),
      ));
      
      $types = Engine_Api::_()->getItemTypes();
      $item_types = array();
      foreach ($types as $type) {
        if (strpos($type, '_') === false) {
          $item_types[$type] = $type;
        }
      }
      
      $this->addElement('Text', 'item_type', array(
        'label' => 'Item Type',
        'description' => 'Example: ' . join(", ", $item_types) . ' etc..',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
          new Engine_Validate_Callback(array($this, 'validateItemType')),
        ),    
      ));
      
      $this->addElement('Text', 'item_order', array(
        'label' => 'Item Ordering',
        'description' => 'Example: "creation_date desc", "article_id desc"',   
      ));    
    }
    $this->addElement('Select', 'changefreq', array(
      'label' => 'Change Frequency',
      'multiOptions' => array(
      	'always' => 'always',
      	'hourly' => 'hourly',
      	'daily' => 'daily',
      	'weekly' => 'weekly',
      	'monthly' => 'monthly',
      	'yearly' => 'yearly',
        'never' => 'never',
      ),
      'description' => 'How frequently the page is likely to change. This value provides general information to search engines and may not correlate exactly to how often they crawl the page. The value "always" should be used to describe documents that change each time they are accessed. The value "never" should be used to describe archived URLs.'
    ));
    
    $this->addElement('Select', 'priority', array(
      'label' => 'Priority',
      'multiOptions' => array(
      	'1.0' => '1.0',
      	'0.9' => '0.9',
      	'0.8' => '0.8',
      	'0.7' => '0.7',
      	'0.6' => '0.6',
      	'0.5' => '0.5',
      	'0.4' => '0.4',
      	'0.3' => '0.3',
      	'0.2' => '0.2',
      	'0.1' => '0.1',
      	'0.0' => '0.0',
      ),
      'description' => 'The priority of this URL relative to other URLs on your site. This value does not affect how your pages are compared to pages on other sites - it only lets the search engines know which pages you deem most important for the crawlers.'
    ));
    
    
    $this->addElement('Text', 'maxitems', array(
      'label' => 'Max Items',
      'description' => 'Enter 0 if you wish to include all urls belong to this channel.',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        'Digits',
        new Zend_Validate_Between(0,1000),
      ),
      'attribs' => array(
        'class' => 'short',
      ),
      'value' => 0,      
    ));    
    
    $this->addElement('Checkbox', 'enabled', array(
      'label' => 'Enabled?',
      'value' => 1
    ));
    
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Channel',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => ' or ',
      'ignore' => true,
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'seo', 'controller' => 'channels', 'action' => 'index'), 'admin_default', true),
      //'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      )
    ));
  }

  public function validatePlugin($value)
  {

    if (!class_exists($value, true))
    {
      $this->plugin->getValidator('Engine_Validate_Callback')->setMessage('Could not find or load plugin class.');
      return false;
    }

    return true;
  }  
  
  
  public function validateItemType($value)
  {
    if (!Engine_Api::_()->hasItemType($value)) {
      $this->item_type->getValidator('Engine_Validate_Callback')->setMessage('Item Type does not exist.');
      return false;
    }
    
    return true;
  }
  
  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }    
  
}