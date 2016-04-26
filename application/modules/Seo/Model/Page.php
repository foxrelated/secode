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

class Seo_Model_Page extends Core_Model_Item_Abstract
{
  const MODE_PREPEND = 'prepend';
  const MODE_APPEND  = 'append';
  const MODE_OVERRIDE = 'override';
  const MODE_DEFAULT = 'default'; 
  
  protected $_searchTriggers = false;
  
}