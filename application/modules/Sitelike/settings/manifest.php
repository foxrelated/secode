<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

return array (
  // Package -------------------------------------------------------------------
  'package' => array (
    'type' => 'module' ,
    'name' => 'sitelike' ,
    'version' => '4.8.6p1' ,
    'path' => 'application/modules/Sitelike' ,
    'repository' => 'null' ,
    'title' => 'Likes Plugin and Widgets' ,
    'description' => '' ,
    'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
    'date' => 'Thursday, 4 Nov 2010 18:33:08 +0000' ,
    'copyright' => 'Copyright 2009-2010 BigStep Technologies Pvt. Ltd.' ,
    'actions' => array (
      'install' ,
      'upgrade' ,
      'refresh' ,
      'enable' ,
      'disable' ,
    ) ,
    'callback' => array (
      'path' => 'application/modules/Sitelike/settings/install.php' ,
      'class' => 'Sitelike_Installer' ,
    ) ,
    'directories' => array (
      'application/modules/Sitelike' ,
    ) ,
    'files' => array (
      'application/languages/en/sitelike.csv' ,
    ) ,
  ) ,
  'sitemobile_compatible' =>true,
  // Items ---------------------------------------------------------------------
  'items' => array (
    'sitelike' , 'sitelike_setting' , 'sitelike_mysettings', 'sitelike_mixsettings'
  ) ,
  'hooks' => array (
    array (
      'event' => 'onCoreLikeDeleteBefore' ,
      'resource' => 'Sitelike_Plugin_Core' ,
    ) ,
    array (
      'event' => 'onRenderLayoutMobileSMDefault' ,
      'resource' => 'Sitelike_Plugin_Core' ,
    ) ,
    array (
      'event' => 'onRenderLayoutDefault' ,
      'resource' => 'Sitelike_Plugin_Core' ,
    ) ,
  ) ,
  // Routes --------------------------------------------------------------------
  'routes' => array (
    // Public
    'like_general' => array (
      'route' => 'likes/*' ,
      'defaults' => array (
        'module' => 'sitelike' ,
        'controller' => 'index' ,
        'action' => 'browse' ,
        'like_id' => ''
      ) ,
    ) ,
    // Public
    'like_profileuser' => array (
      'route' => 'likes/profileuserlikes/:profileuser_id/:mutual' ,
      'defaults' => array (
        'module' => 'sitelike' ,
        'controller' => 'index' ,
        'action' => 'profileuserlikes' ,
        'profileuser_id' => '' ,
        'mutual' => ''
      ) ,
    ) ,
    // Public
    'like_likelist' => array (
      'route' => 'likes/likelist/:resource_type/:resource_id/:call_status' ,
      'defaults' => array (
        'module' => 'sitelike' ,
        'controller' => 'index' ,
        'action' => 'likelist' ,
        'resource_type' => '' ,
        'resource_id' => '' ,
        'call_status' => '' ,
      ) ,
    ) ,
    // Public
    'like_myfriend' => array (
      'route' => 'likes/myfriendlikes/:resource_type/:resource_id' ,
      'defaults' => array (
        'module' => 'sitelike' ,
        'controller' => 'index' ,
        'action' => 'myfriendlikes' ,
        'resource_type' => '' ,
        'resource_id' => '' ,
      ) ,
    ) ,
    'like_mylikes' => array (
      'route' => 'likes/mylikes/' ,
      'defaults' => array (
        'module' => 'sitelike' ,
        'controller' => 'index' ,
        'action' => 'mylikes' ,
      ) ,
    ) ,
    'like_myfriendslike' => array (
      'route' => 'likes/myfriendslike/' ,
      'defaults' => array (
        'module' => 'sitelike' ,
        'controller' => 'index' ,
        'action' => 'myfriendslike' ,
      ) ,
    ) ,
    'like_mycontent' => array (
      'route' => 'likes/mycontent/' ,
      'defaults' => array (
        'module' => 'sitelike' ,
        'controller' => 'index' ,
        'action' => 'mycontent' ,
      ) ,
    ) ,
    'like_memberlike' => array (
      'route' => 'likes/memberlike/' ,
      'defaults' => array (
        'module' => 'sitelike' ,
        'controller' => 'index' ,
        'action' => 'memberlike' ,
      ) ,
    ) ,
    'like_settings' => array (
      'route' => 'likes/likesettings/' ,
      'defaults' => array (
        'module' => 'sitelike' ,
        'controller' => 'index' ,
        'action' => 'likesettings' ,
      ) ,
    ) ,
  ) ,
) ;
?>
