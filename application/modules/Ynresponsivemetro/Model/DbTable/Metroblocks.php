<?php
class Ynresponsivemetro_Model_DbTable_Metroblocks extends Engine_Db_Table
{
	protected $_rowClass = "Ynresponsivemetro_Model_Metroblock";
	protected $_name = 'ynresponsive1_metroblocks';

	public function getBlocks($params = array())
	{
		$select = $this -> select();
		if(isset($params['block']))
		{
			$select -> where('block = ?', $params['block']);
		}
		$limit = 1;
		if(isset($params['limit']))
		{
			$limit = $params['limit'];
		}
		$select -> limit($limit);
		if($limit == 1)
		{
			return $this -> fetchRow($select);
		}
		else
		{
			$select -> order("rand()");
			return $this -> fetchAll($select);
		}
	}
	
	public function getFeaturedPhotosPaginator()
	{
		$select = $this -> select();
		$select -> where('block = ?', 8) -> order('title');
		return Zend_Paginator::factory($select);
	}
}
