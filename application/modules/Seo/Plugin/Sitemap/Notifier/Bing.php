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

class Seo_Plugin_Sitemap_Notifier_Bing extends Seo_Plugin_Sitemap_Notifier_Abstract
{
  
  protected $SERVICE_URL = 'http://www.bing.com/webmaster/ping.aspx';
  protected $URL_KEY = 'siteMap';
  
  protected $sucessToken = 'thanks for submitting';
 
}