<?php
class Ynresponsivemetro_Widget_MetroVaInfoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-metro' || (!Engine_Api::_() -> hasItemType("video") && !Engine_Api::_() -> hasItemType("album")))
	{
		return $this -> setNoRender(true);
	}
	if(Engine_Api::_() -> hasItemType("video"))
	{
		 $this -> view -> video_icon = $this -> _getParam('video_icon', '');
		 $this -> view -> video_background_color = $this -> _getParam('video_background_color', '');
		 $this -> view -> video_text_color = $this -> _getParam('video_text_color', '');
		 $table = Engine_Api::_()->getItemTable('video');
	     $select = $table->select()
	     	->where('search = ?', 1);
		 $paginator = Zend_Paginator::factory($select);
		 $this -> view -> total_videos = $paginator->getTotalItemCount();
	}
	if(Engine_Api::_() -> hasModuleBootstrap("album") || Engine_Api::_() -> hasModuleBootstrap("advalbum"))
	{
		 $this -> view -> album_icon = $this -> _getParam('album_icon', '');
		 $this -> view -> album_background_color = $this -> _getParam('album_background_color', '');
		 $this -> view -> album_text_color = $this -> _getParam('album_text_color', '');
		 if(Engine_Api::_() -> hasModuleBootstrap("album"))
		 	$table = Engine_Api::_()->getItemTable('album');
		 else {
			 $table = Engine_Api::_() -> getItemTable('advalbum_album');
		 }
	     $select = $table->select()
	     	->where('search = ?', 1);
		 $paginator = Zend_Paginator::factory($select);
		 $this -> view -> total_albums = $paginator->getTotalItemCount();
	}
  }
  
  public function getCacheKey()
  {
    return false;
  }
}
