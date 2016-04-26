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

class Seo_Plugin_Channel_Custom extends Seo_Plugin_Channel_Abstract
{
  
  /**
	 * @return Zend_Paginator
   */
  public function getPaginator($options = array())
  {
    
    $table = Engine_Api::_()->getItemTable($this->_channel->item_type);
    $select = $table->select();
    
    if ($this->_channel->item_order)
    {
      $select->order($this->_channel->item_order);
    }
          
    return Zend_Paginator::factory($select);
  }  
  
  public function isSupported()
  {
    return Engine_Api::_()->hasItemType($this->_channel->item_type);
  }
}