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
 
 
 
class Seo_Form_Admin_Channel_Edit extends Seo_Form_Admin_Channel_Create
{

  protected $_item;
  
  public function init()
  {
    parent::init();
    
    $this->setTitle('Edit SEO Sitemap Channel')
      ->setDescription('Please fill out the form below to update channel.')
     // ->setAttrib('class', 'global_form_popup')
      ;

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