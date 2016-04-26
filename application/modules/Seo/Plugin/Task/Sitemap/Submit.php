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

class Seo_Plugin_Task_Sitemap_Submit extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
    
    Engine_Api::_()->seo()->buildSitemap();

    $notifyservices = explode(',',Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.notifyservices', 'google,bing,ask'));
    
    $services = array();
    foreach ($notifyservices as $service)
    {
      $services[$service] = array();
      if ($service == 'yahoo')
      {
        $services[$service] = array('appid' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.notifyyahooappid'));
      } 
    }
    
    $sitemap = Engine_Api::_()->seo()->getSitemapInfo();  
    $url = $sitemap['gzip'] ? $sitemap['gzipfile']['url'] : $sitemap['file']['url'];  
    
    Engine_Api::_()->seo()->submitSitemap($url, $services);
    
    
    return;
  }

}