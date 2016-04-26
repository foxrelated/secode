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

class Seo_Plugin_Channel_Employment extends Seo_Plugin_Channel_Abstract
{
  /**
	 * @return Zend_Paginator
   */
  public function getPaginator($options = array())
  {
    $params = array(
      'order' => 'recent',
    );
    $params = array_merge($params, $options);
    
    return Engine_Api::_()->employment()->getEmploymentsPaginator($params);
  } 
  

}