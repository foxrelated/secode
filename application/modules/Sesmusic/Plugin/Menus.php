<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Menus.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Plugin_Menus {

  public function onMenuInitialize_SesmusicMainManage() {

    //Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity())
      return false;

    //Must be able to create playlists
    if (!Engine_Api::_()->authorization()->isAllowed('sesmusic_album', $viewer, 'create'))
      return false;

    return true;
  }
  
    public function onMenuInitialize_SesmusicQuickCreate() {

    //Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity())
      return false;

    //Must be able to create playlists
    if (!Engine_Api::_()->authorization()->isAllowed('sesmusic_album', $viewer, 'create'))
      return false;

    return true;
  }

  public function onMenuInitialize_SesmusicMainCreate() {

    //Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity())
      return false;

    //Must be able to create playlists
    if (!Engine_Api::_()->authorization()->isAllowed('sesmusic_album', $viewer, 'create'))
      return false;

    return true;
  }

  public function onMenuInitialize_SesmusicMainBrowse() {

    $viewer = Engine_Api::_()->user()->getViewer();

    //Must be able to view playlists
    if (!Engine_Api::_()->authorization()->isAllowed('sesmusic_album', $viewer, 'view'))
      return false;

    return true;
  }

  public function onMenuInitialize_SesmusicProfileEdit() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $album = Engine_Api::_()->core()->getSubject();

    if ($album->getType() !== 'sesmusic_album')
      throw new Sesmusic_Model_Exception('Whoops, not a music album!');

    if (!$viewer->getIdentity() || !$album->authorization()->isAllowed($viewer, 'edit'))
      return false;

    return array(
        'label' => 'Edit Album',
        'icon' => 'application/modules/Sesmusic/externals/images/edit.png',
        'route' => 'sesmusic_album_specific',
        'params' => array(
            'action' => 'edit',
            'album_id' => $album->getIdentity(),
            'slug' => $album->getSlug(),
        )
    );
  }

  public function onMenuInitialize_SesmusicProfileReport() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $album = Engine_Api::_()->core()->getSubject();

    if ($album->getType() !== 'sesmusic_album')
      throw new Sesmusic_Model_Exception('This music album does not exist.');

    if (!$viewer->getIdentity())
      return false;

    return array(
        'label' => 'Report',
        'icon' => 'application/modules/Sesmusic/externals/images/report.png',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $album->getGuid(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_SesmusicProfileShare() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $album = Engine_Api::_()->core()->getSubject();

    if ($album->getType() !== 'sesmusic_album')
      throw new Sesmusic_Model_Exception('This music album does not exist.');

    if (!$viewer->getIdentity())
      return false;

    return array(
        'label' => 'Share',
        'icon' => 'application/modules/Sesmusic/externals/images/share.png',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $album->getType(),
            'id' => $album->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_SesmusicProfileDelete() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $album = Engine_Api::_()->core()->getSubject();

    if ($album->getType() !== 'sesmusic_album')
      throw new Sesmusic_Model_Exception('This music album does not exist.');

    if (!$album->authorization()->isAllowed($viewer, 'delete'))
      return false;

    return array(
        'label' => 'Delete Album',
        'icon' => 'application/modules/Sesmusic/externals/images/delete.png',
        'class' => 'smoothbox',
        'route' => 'sesmusic_general',
        'params' => array(
            'action' => 'delete',
            'album_id' => $album->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_SesmusicProfileCreate() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $album = Engine_Api::_()->core()->getSubject();

    if ($album->getType() !== 'sesmusic_album')
      throw new Sesmusic_Model_Exception('This music album does not exist.');

    if (!$album->authorization()->isAllowed($viewer, 'create'))
      return false;

    return array(
        'label' => 'Upload Songs',
        'icon' => 'application/modules/Sesmusic/externals/images/new.png',
        'route' => 'sesmusic_general',
        'params' => array(
            'action' => 'create',
        ),
    );
  }

  public function onMenuInitialize_SesmusicProfileAddplaylist() {

    $album = Engine_Api::_()->core()->getSubject();
    if ($album->getType() !== 'sesmusic_album')
      throw new Sesmusic_Model_Exception('This music album does not exist.');

    return array(
        'label' => 'Add to Playlist',
        'icon' => 'application/modules/Sesmusic/externals/images/playlist.png',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'sesmusic',
            'controller' => 'song',
            'action' => 'append-songs',
            'album_id' => $album->album_id
        ),
    );
  }

  //Song View Page Options
  public function onMenuInitialize_SesmusicSongProfileEdit() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $song = Engine_Api::_()->core()->getSubject();
    $album = Engine_Api::_()->getItem('sesmusic_album', $song->album_id);

    if ($song->getType() !== 'sesmusic_albumsong')
      throw new Sesmusic_Model_Exception('Whoops, not a music album!');

    if (!$viewer->getIdentity() || !$album->authorization()->isAllowed($viewer, 'edit'))
      return false;

    return array(
        'label' => 'Edit Song',
        'icon' => 'application/modules/Sesmusic/externals/images/edit.png',
        'route' => 'sesmusic_albumsong_specific',
        'params' => array(
            'action' => 'edit',
            'albumsong_id' => $song->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_SesmusicSongProfileReport() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $song = Engine_Api::_()->core()->getSubject();

    if ($song->getType() !== 'sesmusic_albumsong')
      throw new Sesmusic_Model_Exception('This music album does not exist.');

    if (!$viewer->getIdentity())
      return false;

    return array(
        'label' => 'Report',
        'icon' => 'application/modules/Sesmusic/externals/images/report.png',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $song->getGuid(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_SesmusicSongProfilePrint() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $song = Engine_Api::_()->core()->getSubject();

    if ($song->getType() !== 'sesmusic_albumsong')
      throw new Sesmusic_Model_Exception('This music album does not exist.');

    if (!$viewer->getIdentity())
      return false;

    return array(
        'label' => 'Print',
        'icon' => 'application/modules/Sesmusic/externals/images/print.png',
        'route' => 'sesmusic_albumsong_specific',
        'params' => array(
            'action' => 'print',
            'albumsong_id' => $song->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SesmusicSongProfileShare() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $song = Engine_Api::_()->core()->getSubject();

    if ($song->getType() !== 'sesmusic_albumsong')
      throw new Sesmusic_Model_Exception('This music album does not exist.');

    if (!$viewer->getIdentity())
      return false;

    return array(
        'label' => 'Share',
        'icon' => 'application/modules/Sesmusic/externals/images/share.png',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $song->getType(),
            'id' => $song->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_SesmusicSongProfileDelete() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $song = Engine_Api::_()->core()->getSubject();
    $album = Engine_Api::_()->getItem('sesmusic_album', $song->album_id);

    if ($song->getType() !== 'sesmusic_albumsong')
      throw new Sesmusic_Model_Exception('This music album does not exist.');

    if (!$album->authorization()->isAllowed($viewer, 'delete'))
      return false;

    return array(
        'label' => 'Delete Song',
        'icon' => 'application/modules/Sesmusic/externals/images/delete.png',
        'class' => 'smoothbox',
        'route' => 'sesmusic_albumsong_specific',
        'params' => array(
            'action' => 'delete',
            'albumsong_id' => $song->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_SesmusicSongProfileAddplaylist() {

    $song = Engine_Api::_()->core()->getSubject();
    if ($song->getType() !== 'sesmusic_albumsong')
      throw new Sesmusic_Model_Exception('This album song does not exist.');

    return array(
        'label' => 'Add to Playlist',
        'icon' => '',
        'class' => 'smoothbox',
        'route' => 'sesmusic_albumsong_specific',
        'params' => array(
            'action' => 'append',
            'albumsong_id' => $song->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SesmusicSongProfileDownload() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $song = Engine_Api::_()->core()->getSubject();

    if ($song->getType() !== 'sesmusic_albumsong')
      throw new Sesmusic_Model_Exception('This album song does not exist.');

    if (!$viewer->getIdentity())
      return false;

    return array(
        'label' => 'Download',
        'icon' => 'application/modules/Sesmusic/externals/images/doenload.png',
        'route' => 'sesmusic_albumsong_specific',
        'params' => array(
            'action' => 'download-song',
            'albumsong_id' => $song->getIdentity()
        ),
    );
  }
}