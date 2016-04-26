<?php
class Ynresponsivemetro_Widget_MetroIntroductionController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-metro')
	{
		return $this -> setNoRender(true);
	}
	$blockTable = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro');
	
	$this->view->title = $this->_getParam('title', '');
	$this->view->content = $this->_getParam('content', '');
	$this->view->background_image = $this->_getParam('background_image', '');
	// get all blocks
	$this -> view -> oneBlock = $blockTable -> getBlocks(array('block' => 9));
	$this -> view -> twoBlock = $blockTable -> getBlocks(array('block' => 10));
	$this -> view -> threeBlock = $blockTable -> getBlocks(array('block' => 11));
	$this -> view -> fourBlock = $blockTable -> getBlocks(array('block' => 12));
  }
  
  public function getCacheKey()
  {
    return false;
  }
}
