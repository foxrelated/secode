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

class Seo_Plugin_Sitemap_Notifier_Google extends Seo_Plugin_Sitemap_Notifier_Abstract
{
  
  protected $SERVICE_URL = 'http://www.google.com/webmasters/sitemaps/ping';
  protected $URL_KEY = 'sitemap';
  
  protected $sucessToken = 'received';
 
}