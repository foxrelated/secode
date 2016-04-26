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

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'seo',
    'version' => '4.0.6',
    'revision' => '$Revision: $Id$ $',
    'path' => 'application/modules/Seo',
    'repository' => 'radcodes.com',
    'title' => 'SEO Sitemap',
    'description' => 'This plugin provides Search Engine Optimization (SEO) for Title, Description, Keywords as well as customized additional headers. Including Sitemap file building and update notification to Google, Bing, Ask, Yahoo etc..',
    'author' => 'Radcodes Developments',
    'changeLog' => 'settings/changelog.php',
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Seo/settings/install.php',
      'class' => 'Seo_Installer',
    ),
    'dependencies' => array(
      'radcodes' => array(
        'type' => 'module',
        'name' => 'radcodes',
        'minVersion' => '4.0.3'
      )
    ),    
    'directories' => array(
      'application/modules/Seo',
    ),
    'files' => array(
      'application/languages/en/seo.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(

    array(
      'event' => 'onRenderLayoutDefaultSeo',
      'resource' => 'Seo_Plugin_Core',
    ),
    
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'seo_page',
    'seo_channel',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'seo_extended' => array(
      'route' => 'seos/:controller/:action/*',
      'defaults' => array(
        'module' => 'seo',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      ),
    ),   
    'seo_general' => array(
      'route' => 'seos/:action/*',
      'defaults' => array(
        'module' => 'seo',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|browse|manage|create|tags)',
      ),
    ),
    'seo_specific' => array(
      'route' => 'seos/:action/:seo_id/*',
      'defaults' => array(
        'module' => 'seo',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'seo_id' => '\d+',
        'action' => '(delete|edit)',
      ),
    ),
  ),
);
