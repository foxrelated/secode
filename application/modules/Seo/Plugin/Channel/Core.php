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

class Seo_Plugin_Channel_Core extends Seo_Plugin_Channel_Abstract
{
  /**
	 * @return Zend_Paginator
   */
  public function getPaginator($options = array())
  {
    $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('core_sitemap');
      
    $ps = array();  
    foreach ($navigation as $p) {
      $ps[] =  $p;
    }  

    return Zend_Paginator::factory($ps);
  } 
  
}