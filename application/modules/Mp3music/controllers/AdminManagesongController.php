<?php
class Mp3music_AdminManagesongController extends Core_Controller_Action_Admin
{
  protected $_paginate_params = array();
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_managesong');
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
          $song = Engine_Api::_()->getItem('mp3music_album_song', $value);
          $song->deleteUnused();
        }
      }
    }
    $params = array_merge($this->_paginate_params, array(
        'user' => $this->view->viewer_id, 'admin'=>"admin"
    )); 
     $this->view->form = $form = new Mp3music_Form_Admin_Searchalbum();   
    $values = array();  
    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
    }  
    if(!empty($values['title']))  
    {
         $params['title'] =  trim($values['title']);
    }  
    if(!empty($values['album']))  
    {
         $params['album'] = trim($values['album']);
    }   
    $params['page'] = $this->_getParam('page', 1);   
    $this->view->formValues = $values;                                                                                                                                   
    $obj = new Mp3music_Api_Core();
    $this->view->paginator = $obj->getSongPaginator($params);
    $this->view->params    = $params;
  }

  public function editPermissionAction()
  {
  	if (!$this -> _helper -> requireUser() -> isValid())
		return;
	$this -> view -> form = $form = new Mp3music_Form_Admin_UpdatePermission();
	$song_id = $this -> getRequest() -> getParam('song_id');
	$song = Engine_Api::_() -> getItem('mp3music_album_song', $song_id);
	$auth = Engine_Api::_() -> authorization() -> context;
	$allowed = array();
	// populate permission view in forum
	if ($auth -> isAllowed($song, 'everyone', 'play')) {} 
	else 
	{
		$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
		foreach ($levels as $level) 
		{
			if ($auth -> isAllowed($song, $level, 'play')) 
			{
				$allowed[] = $level -> getIdentity();
			}
		}
		if (count($allowed) == 0 || count($allowed) == count($levels)) 
		{
			$allowed = null;
		}
	}
	if (!empty($allowed)) 
	{
		$form -> populate(array('levels' => $allowed));
	}
	// Check request/method
	if (!$this -> getRequest() -> isPost()) {
		return;
	}
	if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
	{
		$values = $form -> getValues();
		$db = Engine_Api::_() -> getDbTable('albumSongs', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			// Handle permissions
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();

			// Clear permissions view forum
			$auth -> setAllowed($song, 'everyone', 'play', false);
			foreach ($levels as $level) 
			{
				$auth -> setAllowed($song, $level, 'play', false);
			}

			// Add permissions view forum
			if (count($values['levels']) == 0 || count($values['levels']) == count($form -> getElement('levels') -> options)) 
			{
				$auth -> setAllowed($song, 'everyone', 'play', true);
			} 
			else 
			{
				foreach ($values['levels'] as $levelIdentity) 
				{
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($song, $level, 'play', true);
				}
			}
			$db -> commit();
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array($this -> view -> translate('Your changes have been saved.'))
			));
		}
		catch (Exception $e)
		{
			$db -> rollback();
		}
	}
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