<?php
class Ynresponsivemetro_Widget_MetroBlocksController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-metro')
	{
		return $this -> setNoRender(true);
	}
	$blockTable = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro');
	// get all blocks
	$this -> view -> videoBlock = $blockTable -> getBlocks(array('block' => 1));
	$this -> view -> eventBlock = $blockTable -> getBlocks(array('block' => 2));
	$this -> view -> photosBlock = $blockTable -> getBlocks(array('block' => 3, 'limit' => 5));
	$this -> view -> groupBlock = $blockTable -> getBlocks(array('block' => 4));
	$this -> view -> oneBlock = $blockTable -> getBlocks(array('block' => 5));
	$this -> view -> twoBlock = $blockTable -> getBlocks(array('block' => 6));
	$this -> view -> threeBlock = $blockTable -> getBlocks(array('block' => 7));
	
	$this->view->background_image = $this->_getParam('background_image', '');
	
	if(Engine_Api::_() -> hasItemType("video"))
	{
		$table = Engine_Api::_()->getItemTable('video');
	    $select = $table->select()
	      -> where('search = ?', 1)
	      -> order('rand()')
		  -> limit(1);
		 $this -> view -> videoBlock = $table -> fetchRow($select);
		 $this -> view -> hasVideo = true;
	}
	
	if(Engine_Api::_() -> hasItemType("event"))
	{
		$table = Engine_Api::_()->getItemTable('event');
	    $select = $table->select()
	      -> where('search = ?', 1)
		  -> where("endtime > FROM_UNIXTIME(?)", time())
	      -> order('rand()')
		  -> limit(1);
		 $this -> view -> eventBlock = $table -> fetchRow($select);
		 $this -> view -> hasEvent = true;
	}
	
	if(Engine_Api::_() -> hasItemType("group"))
	{
		$table = Engine_Api::_()->getItemTable('group');
	    $select = $table->select()
	      -> where('search = ?', 1)
	      -> order('rand()')
		  -> limit(1);
		 $this -> view -> groupBlock = $table -> fetchRow($select);
		 $this -> view -> hasGroup = true;
	}
  }
  
  public function getCacheKey()
  {
    return false;
  }
}
