<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'mp3music',
    'version' => '4.06sp2',
    'path' => 'application/modules/Mp3music',
    'title' => 'YN - Mp3 Music Selling',
    'description' => 'This is module Mp3 Music Selling.',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'meta' => array(
        'title' => 'Mp3 Music Selling',
        'description' => 'This is module Mp3 Music Selling.',
        'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
      ),
     'dependencies' => array(
      array(
         'type' => 'module',
         'name' => 'younet-core',
         'minVersion' => '4.02',
      ),
    ),
    'callback' => 
    array (
      'path' => 'application/modules/Mp3music/settings/install.php',
      'class' => 'Mp3music_Installer',
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
      0 => 'application/modules/Mp3music',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/mp3music.csv',
    ),
  ),
    // Compose -------------------------------------------------------------------
  'compose' => array(
    array('_composeMp3Music.tpl', 'mp3music'),
  ),
  'composer' => array(
    'mp3music' => array(
      'script' => array('_composeMp3Music.tpl', 'mp3music'),
      'plugin' => 'Mp3music_Plugin_Composer',
      'auth' => array('mp3music_album', 'create'),
    ),
  ),
  // Content -------------------------------------------------------------------
  'content' => array(
    'music_profile_music' => array(
      'type' => 'action',
      'title' => 'Music Profile Tab',
      'route' => array(
        'module' => 'mp3music',
        'controller' => 'widget',
        'action' => 'profile-music'
      )
    ),
    'music_profile_player' => array(
      'type' => 'action',
      'title' => 'Music Profile Player',
      'route' => array(
        'module' => 'mp3music',
        'controller' => 'widget',
        'action' => 'profile-player'
      )
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Mp3music_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Mp3music_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------           
  'items' => array(
    'mp3music_playlist',
    'mp3music_playlist_song',
    'mp3music_album',
    'mp3music_album_song',
    'mp3music_cat',
    'mp3music_singer',
    'mp3music_singer_type',
    'mp3music_song_rating',
    'mp3music_param',
    'mp3music_payment_account',
    'mp3music_list',
    'mp3music_selling_setting',
    'mp3music_artist',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    // Public
    'mp3music_browse' => array(
      'route' => 'mp3-music/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'index',
        'action' => 'browse',
        'page' => 1,
      ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    'mp3music_browsealbums' => array(
      'route' => 'mp3-music/browse-albums/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'index',
        'action' => 'browsealbums',
        'page' => 1,
      ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),   
    'mp3music_browseplaylists' => array(
      'route' => 'mp3-music/browse-playlists/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'index',
        'action' => 'browseplaylists',
        'page' => 1,
      ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),   
    'mp3music_manage_playlist' => array(
      'route' => 'mp3-music/manage_playlist/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'playlist',
        'action' => 'manage',
        'page' => 1,
      ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    'mp3music_manage_album' => array(
      'route' => 'mp3-music/manage_album/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'album',
        'action' => 'manage',
        'page' => 1,
      ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    'mp3music_edit_playlist' => array(
      'route' => 'mp3-music/edit_playlist/:playlist_id',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'playlist',
        'action' => 'edit',
      ),
      'reqs' => array(
        'playlist_id' => '\d+',
      )
    ),
    'mp3music_edit_album' => array(
      'route' => 'mp3-music/edit_album/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'album',
        'action' => 'edit',
      ),
      'reqs' => array(
        'playlist_id' => '\d+',
      )
    ),
    'mp3music_create_playlist' => array(
      'route' => 'mp3-music/playlist/create',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'playlist',
        'action' => 'create',
      ),
    ),
    'mp3music_create_album' => array(
      'route' => 'mp3-music/album/create/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'album',
        'action' => 'create',
      ),
    ),
    'mp3music_edit_song' => array(
      'route' => 'mp3-music/album/edit_song/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'album',
        'action' => 'edit-song',
      ),
    ),
     'mp3music_edit_singer' => array(
      'route' => 'mp3-music/category/edit_singer/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-category',
        'action' => 'edit-singer',
      ),
    ),
    'mp3music_create_singer' => array(
      'route' => 'mp3-music/category/create_singer/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-category',
        'action' => 'create-singer',
      ),
    ),
    'mp3music_edit_artist' => array(
      'route' => 'mp3-music/category/edit_artist/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-category',
        'action' => 'edit-artist',
      ),
    ),
    'mp3music_create_artist' => array(
      'route' => 'mp3-music/category/create_artist/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-category',
        'action' => 'create-artist',
      ),
    ),
    'mp3music_playlist_append' => array(
      'route' => 'mp3-music/playlist/append/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'playlist',
        'action' => 'playlist-append',
      ),
    ),
    'mp3music_playlist' => array(
      'route' => 'mp3-music/playlist/:playlist_id',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'playlist',
        'action' => 'playlist',
        'playlist_id' => 0,
      ),
      'reqs' => array(
        'playlist_id' => '\d+',
      ),
    ),
    'mp3music_album' => array(
      'route' => 'mp3-music/albums/:album_id',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'album',
        'action' => 'album',
        'album_id' => 0,
      ),
      'reqs' => array(
        'album_id' => '\d+',
      ),
    ),
    'mp3music_album_song' => array(
      'route' => 'mp3-music/albums/:album_id/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'album',
        'action' => 'album',
        'album_id' => 0,
      ),
      'reqs' => array(
        'album_id' => '\d+',
      ),
    ),
    'mp3music_admin_manage_level' => array(
      'route' => 'admin/mp3-music/level/:level_id',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-level',
        'action' => 'index'
      )
    ),
    'mp3music_admin_manage_levelplaylist' => array(
      'route' => 'admin/mp3-music/levelplaylist/:level_id',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-levelplaylist',
        'action' => 'index'
      )
    ),
    'mp3music_admin_manage' => array(
      'route' => 'admin/mp3-music/manage/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-manage',
        'action' => 'index',
         'page' => 1,
      ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    'mp3music_admin_manageplaylist' => array(
      'route' => 'admin/mp3-music/manageplaylist/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-manageplaylist',
        'action' => 'index',
         'page' => 1,
      ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    'mp3music_admin_managesong' => array(
      'route' => 'admin/mp3-music/managesong/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-managesong',
        'action' => 'index',
         'page' => 1,
      ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    'mp3music_admin_managesong_permission' => array(
      'route' => 'admin/mp3-music/managesong/edit-permission/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-managesong',
        'action' => 'edit-permission',
      ),
    ),
    'mp3music_admin_setting' => array(
      'route' => 'admin/mp3-music/settings',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-settings',
        'action' => 'index',
         'page' => 1,
      ),
    ),
    'mp3music_admin_level' => array(
      'route' => 'admin/mp3-music/level',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-level',
        'action' => 'index',
      ),
    ),
    'mp3music_admin_levelplaylist' => array(
      'route' => 'admin/mp3-music/levelplaylist/',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-levelplaylist',
        'action' => 'index',
         'page' => 1,
      ),
    ),
    'mp3music_admin_music_setting' => array(
      'route' => 'admin/mp3-music/category/:page',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-category',
        'action' => 'index',
          'page' => 1,
       ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    'mp3music_browse_playlists' => array(
      'route' => 'mp3-music/browse/:search/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'index',
        'action' => 'browse',
      ),
    ),
    'mp3music_browse_new_albums' => array(
      'route' => 'mp3-music/browse/:search/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'index',
        'action' => 'browse',
      ),
    ),
    'mp3music_browse_topsongs' => array(
      'route' => 'mp3-music/browse/:search/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'index',
        'action' => 'browse',
      ),
    ),
    'mp3music_browse_topdownloads' => array(
      'route' => 'mp3-music/browse/:search/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'index',
        'action' => 'browse',
      ),
    ),
     'mp3music_search' => array(
      'route' => 'mp3-music/search/:search/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'index',
        'action' => 'browse',
      ),
    ),
     'mp3music_migrate' => array(
      'route' => 'mp3-music/migrate',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'index',
        'action' => 'migrate',
      ),
    ),
    'mp3music_iframe' => array(
      'route' => 'mp3-music/iframe-html/:album_id/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'album',
        'action' => 'iframe-html',
        'album_id' => 0,
      ),
      'reqs' => array(
        'album_id' => '\d+',
      ),
    ),
    'mp3music_subscribe' => array(
      'route' => 'mp3-music/subscribe/:user_id/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'album',
        'action' => 'subscribe',
        'user_id' => 0,
      ),
      'reqs' => array(
        'user_id' => '\d+',
      ),
    ),
    'mp3music_unsubscribe' => array(
      'route' => 'mp3-music/unsubscribe/:user_id/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'album',
        'action' => 'unsubscribe',
        'user_id' => 0,
      ),
      'reqs' => array(
        'user_id' => '\d+',
      ),
    ),
    //Selling
    'mp3music_cart' => array(
      'route' => 'mp3-music/cart/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'cart',
        'action' => 'index',
        'page' => 1,
       ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
     'mp3music_account_create' => array(
      'route' => 'mp3-music/account/create',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'account',
        'action' => 'create',
      ),
    ),
    'mp3music_account_myaccount' => array(
      'route' => 'mp3-music/account/myaccount/:page',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'account',
        'action' => 'index',
        'page' => 1,
       ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
     'mp3music_account_edit' => array(
      'route' => 'mp3-music/account/edit',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'account',
        'action' => 'edit',
      ),
    ),
     'mp3music_account_policy' => array(
      'route' => 'mp3-music/account/policy/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'account',
        'action' => 'policy',
      ),
    ),
     'mp3music_payment_threshold' => array(
      'route' => 'mp3-music/account/threshold/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'account',
        'action' => 'threshold',
      ),
    ),
     'mp3music_payment_requestmoney' => array(
      'route' => 'mp3-music/account/requestmoney/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'account',
        'action' => 'requestmoney',
      ),
    ),
    'mp3music_cart_viewtransaction' => array(
      'route' => 'mp3-music/cart/viewtransaction/:id/:username',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'cart',
        'action' => 'view-transaction',
      ),
    ),
    'mp3music_cart_deletedownload' => array(
      'route' => 'mp3-music/cart/downloadlist/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'cart',
        'action' => 'downloads',
      ),
    ),
    'mp3music_cart_checkout'  => array( 
    'route' => 'mp3-music/cart/checkout',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'cart',
        'action' => 'checkout',
      ),
    ),
     
    'mp3music_cart_downloadlist' => array(
      'route' => 'mp3-music/cart/downloadlist/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'cart',
        'action' => 'downloads',
        'page' => 1,
       ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    'mp3music_cart_transaction' => array(
      'route' => 'mp3-music/cart/transaction/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'cart',
        'action' => 'transaction',
        'page' => 1,
       ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    //Selling Setting
    'mp3music_admin_main_ssettings' => array(
      'route' => 'admin/mp3-music/ssettings',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-ssettings',
        'action' => 'index',
      ),
    ),
    'mp3music_admin_main_managegateway' => array(
      'route' => 'admin/mp3-music/managegateway',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-managegateway',
        'action' => 'index',
      ),
    ),
     'mp3music_admin_main_sstatistics' => array(
      'route' => 'admin/mp3-music/sstatistics/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-sstatistics',
        'action' => 'index',
        'page' => 1,
       ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    'mp3music_admin_main_import' => array(
      'route' => 'admin/mp3-music/import/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-import',
        'action' => 'index',
       ),
    ),
    'mp3music_admin_main_managefinance' => array(
      'route' => 'admin/mp3-music/managefinance/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-managefinance',
        'action' => 'index',
     'page' => 1,
       ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    'mp3music_admin_main_request'  => array( 
    'route' => 'admin/mp3-music/managefinance/request/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-managefinance',
        'action' => 'request',
      ),
    ),
    'mp3music_admin_main_manageaccounts' => array(
      'route' => 'admin/mp3-music/manageaccounts/:page/*',
      'defaults' => array(
        'module' => 'mp3music',
        'controller' => 'admin-manageaccounts',
        'action' => 'index',
     'page' => 1,
       ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
  ),
); ?>