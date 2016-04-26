<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manifest.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
return array(
    'package' => array(
        'type' => 'module',
        'name' => 'sesmusic',
        'version' => '4.8.9p2',
        'path' => 'application/modules/Sesmusic',
        'title' => 'Advanced Music Albums, Songs & Playlists Plugin',
        'description' => 'Advanced Music Albums, Songs & Playlists Plugin',
        'author' => '<a href="http://www.socialenginesolutions.com" style="text-decoration:underline;" target="_blank">SocialEngineSolutions</a>',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sesmusic/settings/install.php',
            'class' => 'Sesmusic_Installer',
        ),
        'directories' => array(
            'application/modules/Sesmusic',
        ),
        'files' => array(
            'application/languages/en/sesmusic.csv',
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sesmusic_Plugin_Core',
        ),
        array(
            'event' => 'onRenderLayoutMobileDefault',
            'resource' => 'Sesmusic_Plugin_Core',
        ),
    ),
    // Compose -------------------------------------------------------------------
    'compose' => array(
        array('_composeMusic.tpl', 'sesmusic'),
    ),
    'composer' => array(
        'sesmusic' => array(
            'script' => array('_composeMusic.tpl', 'sesmusic'),
            'plugin' => 'Sesmusic_Plugin_Composer',
            'auth' => array('sesmusic_album', 'create'),
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sesmusic_artist', 'sesmusic_artists', 'sesmusic_categories', 'sesmusic_albums', 'sesmusic_albumsongs', 'sesmusic_album', 'sesmusic_albumsong', 'sesmusic_playlistsongs', 'sesmusic_playlist', 'sesmusic_favourites'
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sesmusic_extended' => array(
            'route' => 'musics/:controller/:action/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            ),
        ),
        'sesmusic_songs' => array(
            'route' => 'music/songs/:action/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'song',
                'action' => 'lyrics',
            ),
            'reqs' => array(
                'action' => '(lyrics|browse)',
            ),
        ),
        'sesmusic_artists' => array(
            'route' => 'music/artists/:action/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'artist',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(browse|favourite-artists)',
            ),
        ),
        'sesmusic_album_default' => array(
            'route' => 'music/album/:action/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'album',
            ),
            'reqs' => array(
                'action' => '(favourite-albums|like-albums|rated-albums)',
            ),
        ),
        'sesmusic_albumsong_default' => array(
            'route' => 'music/song/:action/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'song',
            ),
            'reqs' => array(
                'action' => '(favourite-songs|like-songs|rated-songs)',
            ),
        ),
        'sesmusic_general' => array(
            'route' => 'music/album/:action/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'index',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(index|browse|manage|create|home|delete)',
            ),
        ),
        'sesmusic_playlists' => array(
            'route' => 'music/playlist/:action/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'playlist',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(index|browse|manage|create|delete-cookies)',
            ),
        ),
        'sesmusic_artist' => array(
            'route' => 'music/artist/:artist_id/:slug/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'artist',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'artist_id' => '\d+'
            )
        ),
        'sesmusic_album_view' => array(
            'route' => 'music/album/:album_id/:slug/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'album',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'album_id' => '\d+'
            )
        ),
        'sesmusic_albumsong_view' => array(
            'route' => 'music/song/:albumsong_id/:slug/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'song',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'albumsong_id' => '\d+'
            )
        ),
        'sesmusic_album_specific' => array(
            'route' => 'music/album/:album_id/:slug/:action/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'album',
                'action' => 'view',
            ),
            'reqs' => array(
                'album_id' => '\d+',
                'action' => '(view|edit|delete|sort|set-profile|add-song|append-song|download-zip|append-songs)',
            ),
        ),
        'sesmusic_albumsong_specific' => array(
            'route' => 'music/song/:albumsong_id/:action/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'song',
                'action' => 'view',
            ),
            'reqs' => array(
                'albumsong_id' => '\d+',
                'action' => '(view|delete|rename|tally|upload|append|download-song|edit|print)',
            ),
        ),
        'sesmusic_playlist_view' => array(
            'route' => 'music/playlist/:playlist_id/:slug/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'playlist',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'playlist_id' => '\d+'
            )
        ),
        'sesmusic_playlist_specific' => array(
            'route' => 'music/playlists/:playlist_id/:slug/:action/*',
            'defaults' => array(
                'module' => 'sesmusic',
                'controller' => 'playlist',
                'action' => 'view',
            ),
            'reqs' => array(
                'playlist_id' => '\d+',
                'action' => '(view|edit|delete|sort|set-profile|add-song|append-song)',
            ),
        ),
    ),
);
?>