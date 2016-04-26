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

class Seo_Plugin_Channel_Page extends Seo_Plugin_Channel_Abstract
{
  /**
	 * @return Zend_Paginator
   */
  public function getPaginator($options = array())
  {
    $params = array(
			'order' => 'title',
      'order_direction' => 'ASC',
    );
    $params = array_merge($params, $options);
    
    
    // Make paginator
    $table = Engine_Api::_()->getDbtable('pages', 'core');
    $select = $table->select()->where('custom = ?', 1)
      ->order($params['order']. ' '.$params['order_direction'])
      ;
    
    return Zend_Paginator::factory($select);
  } 
  
  public function isSupported()
  {
    return true;
  }
  
}