<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'poke',
    'version' => '4.8.5',
    'path' => 'application/modules/Poke',
      'title' => 'Poke',
      'description' => 'The Pokes Plugin enables a user to grab the attention of another user. Different users might use and interpret pokes in different ways. While some might use it as just another way to say hello, others might use it to grab atention and initiate conversation with someone that they feel shy to talk to. Yet others might use it to flirt with someone they like!',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
    'callback' => 
    array (
      'path' => 'application/modules/Poke/settings/install.php',
      'class' => 'Poke_Installer',
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
      0 => 'application/modules/Poke',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/poke.csv',
    ),
  ),
  'sitemobile_compatible' =>true,
    // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Poke_Plugin_Core',
    ),
  ),
    // Items ---------------------------------------------------------------------
  'items' => array(
    'pokeusers', 'poke_setting'
  ),
  'routes' => array(
     'poke_extended' => array(
      'route' => 'poke/:controller/:action/*',
      'defaults' => array(
        'module' => 'poke',
        'controller' => 'index',
        'action' => 'index'
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    // User - General
    'poke_general' => array(
      'route' => 'poke/:controller/:action/*',
      'defaults' => array(
        'module' => 'poke',
        'controller' => 'index',
        'action' => 'index'
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      ),

    ),
    
    )  
); ?>
