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

class Seo_Plugin_Channel_User extends Seo_Plugin_Channel_Abstract
{
  /**
	 * @return Zend_Paginator
   */
  public function getPaginator($options = array())
  {
    $params = array(
			'order' => 'user_id',
      'order_direction' => 'DESC',
    );
    $params = array_merge($params, $options);
    
    
    // Make paginator
    $table = Engine_Api::_()->getItemTable('user');
    $select = $table->select()
      ->order($params['order']. ' '.$params['order_direction'])
      ;
          
    return Zend_Paginator::factory($select);
  } 
  
}