<?php
class Mp3music_Widget_MenuMusicController extends Engine_Content_Widget_Abstract
{
  public function init(){ 
    $this->view->viewer_id  = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->navigation = $this->getNavigation();
  }
  public function indexAction(){
    $this->view->viewer_id  = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->navigation = $this->getNavigation();
  }
  protected $_navigation;
     public function getNavigation(){
    $tabs   = array();
    $tabs[] = array(
          'label'      => 'Browse Music',
          'route'      => 'mp3music_browse',
          'action'     => 'browse',
          'controller' => 'index',
          'module'     => 'mp3music'
        );
   $tabs[] = array(
          'label'      => 'My Music',
          'route'      => 'mp3music_manage_album',
          'action'     => 'manage',
          'controller' => 'album',
          'module'     => 'mp3music'
        );
    $tabs[] = array(
          'label'      => 'My Playlists',
          'route'      => 'mp3music_manage_playlist',
          'action'     => 'manage',
          'controller' => 'playlist',
          'module'     => 'mp3music'
        );
    $tabs[] = array(
          'label'      => 'Upload Music',
          'route'      => 'mp3music_create_album',
          'action'     => 'create',
          'controller' => 'album',
          'module'     => 'mp3music'
        );
     $tabs[] = array(
          'label'      => 'My Account',
          'route'      => 'mp3music_account_myaccount',
          'action'     => 'index',
          'controller' => 'account',
          'module'     => 'mp3music'
        );
     $tabs[] = array(
          'label'      => 'Cart',
          'route'      => 'mp3music_cart',
          'action'     => 'index',
          'controller' => 'cart',
          'module'     => 'mp3music'
        );
     $tabs[] = array(
          'label'      => 'Download List',
          'route'      => 'mp3music_cart_downloadlist',
          'action'     => 'downloads',
          'controller' => 'cart',
          'module'     => 'mp3music'
        );
    if( is_null($this->_navigation) ) {
      $this->_navigation = new Zend_Navigation();
      $this->_navigation->addPages($tabs);
    }
    return $this->_navigation;
  }
}
