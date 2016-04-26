<?php
class Mp3music_AdminManageplaylistController extends Core_Controller_Action_Admin
{
  protected $_paginate_params = array();
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_manageplaylist');
    $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.songsPerPage', 10);
    $this->_paginate_params['sort']   = $this->getRequest()->getParam('sort', 'recent');
    $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
  }
  public function indexAction()
  {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $playlist = Engine_Api::_()->getItem('mp3music_playlist', $value);
          $songs = $playlist->getPSongs();
          foreach($songs as $song)
          {
             $song->delete();
          }
          $playlist->delete();
        }
      }
    }
    $params = array_merge($this->_paginate_params, array(
        'user' => $this->view->viewer_id,'admin'=>"admin"
    )); 
     $this->view->form = $form = new Mp3music_Form_Admin_Search();   
    $values = array();  
    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
    }  
    if(!empty($values['title']))  
    {
         $params['title'] =   trim($values['title']);
    }  
    if(!empty($values['owner']))  
    {
         $params['owner'] =  trim($values['owner']);
    }   
    $params['page'] = $this->_getParam('page', 1);   
    $this->view->formValues = $values;                                                                                            
    $obj = new Mp3music_Api_Core();
    $this->view->paginator = $obj->getPaginator($params);
    $this->view->params    = $params;
  }
}
 function locdau($str)
 {
      $str= strtolower($str); 
      $str= preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/","a",$str);  
      $str= preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/","e",$str);  
      $str= preg_replace("/(ì|í|ị|ỉ|ĩ)/","i",$str);  
      $str= preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/","o",$str);  
      $str= preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/","u",$str);  
      $str= preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/","y",$str);  
      $str= preg_replace("/(đ)/","d",$str);  
      $str= preg_replace("/(!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_)/","-",$str); 
      $str= preg_replace("/(-+-)/","-",$str); //thay thế 2- thành 1- 
      $str= preg_replace("/(^\-+|\-+$)/","",$str); 
      $str= preg_replace("/(-)/"," ",$str); 
      return $str;
 }