<?php 

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideoview
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2012-06-028 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'sitevideoview',
    'version' => '4.7.1',
    'path' => 'application/modules/Sitevideoview',
    'title' => 'Video Lightbox Viewer Plugin',
    'description' => 'Video Lightbox Viewer Plugin',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
    'callback' =>
        array(
            'path' => 'application/modules/Sitevideoview/settings/install.php',
            'class' => 'Sitevideoview_Installer',
        ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Sitevideoview',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/sitevideoview.csv',
    ),
  ),
); ?>