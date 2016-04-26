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

class Seo_Plugin_Channel_Blog extends Seo_Plugin_Channel_Custom
{

  /**
	 * @return Zend_Paginator
   */
  public function getPaginator($options = array())
  {
    $this->_channel->item_type = 'blog';
    $this->_channel->item_order = 'creation_date DESC';
    
    return parent::getPaginator($options);    
  }   
  
}