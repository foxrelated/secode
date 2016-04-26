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

class Seo_Form_Admin_Page_Edit extends Seo_Form_Admin_Page_Create
{
  public function init()
  {

    parent::init();
    
    $this->setTitle('Edit SEO Page')
      ->setDescription('Please fill out form below to update SEO page.');


    $this->submit->setLabel('Save Changes');

  }
  
}