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

class Seo_Plugin_Sitemap_Notifier
{
  static function factory($service, $params = array())
  {
    $class = 'Seo_Plugin_Sitemap_Notifier_'.ucfirst(strtolower($service));
    return new $class($params);
  }
}