<?php
class Ynresponsivemetro_Widget_MetroFeaturedPhotosController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (YNRESPONSIVE_ACTIVE != 'ynresponsive-metro')
		{
			return $this -> setNoRender(true);
		}
		$blockTable = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro');
		if ((Engine_Api::_() -> hasModuleBootstrap("advalbum") || Engine_Api::_() -> hasModuleBootstrap("album") ) && !$blockTable -> getBlocks(array('block' => 8)))
		{
			// Get paginator
			$table = NULL;
			if(Engine_Api::_() -> hasModuleBootstrap("advalbum"))
			{
				$table = Engine_Api::_() -> getItemTable('advalbum_album');
			}
			else 
			{
				$table = Engine_Api::_() -> getItemTable('album');
			}
			
			$select = $table -> select() -> where('search = ?', true) -> order('rand()') -> limit($this -> _getParam('itemCountPerPage', 10) * 1.5);
			
			// Create new array filtering out private albums
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$album_select = $select;
			$new_select = array();
			$i = 0;
			foreach ($album_select->getTable()->fetchAll($album_select) as $album)
			{
				if (Engine_Api::_() -> authorization() -> isAllowed($album, $viewer, 'view'))
				{
					$new_select[$i++] = $album;
				}
			}
			$this -> view -> paginator = $paginator = Zend_Paginator::factory($new_select);
			$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 10));
			$this -> view -> type = 'album';
		}
		else if($blockTable -> getBlocks(array('block' => 8)))
		{
			$this -> view -> paginator = $blockTable -> getBlocks(array('block' => 8, 'limit' => $this -> _getParam('itemCountPerPage', 10)));
			$this -> view -> type = 'photo';
		}
		if(!count($this -> view -> paginator))
		{
			return $this -> setNoRender(true);
		}
	}

	public function getCacheKey()
	{
		return false;
	}

}
