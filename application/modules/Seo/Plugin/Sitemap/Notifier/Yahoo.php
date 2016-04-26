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

class Seo_Plugin_Sitemap_Notifier_Yahoo extends Seo_Plugin_Sitemap_Notifier_Abstract
{
  
  protected $SERVICE_URL = 'http://search.yahooapis.com/SiteExplorerService/V1/updateNotification';
  protected $URL_KEY = 'url';
  
  protected $sucessToken = 'success';
 
}