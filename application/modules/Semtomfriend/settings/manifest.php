<?php

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'semtomfriend',
    'version' => '4.0.3',
    'path' => 'application/modules/Semtomfriend',
    'repository' => 'socialenginemods.net',
    'title' => 'Tom Friend',
    'description' => 'Tom Friend',
    'author' => 'SocialEngineMods',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion'  => '4.1.6',
        'required' => true
      ),
      array(
        'type' => 'module',
        'name' => 'semods',
        'minVersion'  => '4.0.4',
        'required' => true
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Semtomfriend/settings/install.php',
      'class' => 'Semtomfriend_Installer',
    ),
    'directories' => array(
      'application/modules/Semtomfriend',
    ),
    'files' => array(
      'application/languages/en/semtomfriend.csv',
    ),
  ),

  // Items ---------------------------------------------------------------------
  'items' => array(
    
  ),

  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    
    array(
      'event' => 'onUserSignupAfter',
      'resource' => 'Semtomfriend_Plugin_Core',
    ),
    
  ),
  
  // Routes --------------------------------------------------------------------
  'routes' => array(

  // end routes
  ),
);