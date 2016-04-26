<?php
class Yncontest_Widget_NewEntryController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){
		$Model = new Yncontest_Model_DbTable_Entries;
		$limit = 6;
        if($this->_getParam('number') != '' && $this->_getParam('number') >= 0)
        {
            $limit = $this->_getParam('number');
        }
		$select = $Model -> select();
		$select -> where("entry_status = 'published'")
				-> where("approve_status = 'approved'")
				-> order('modified_date desc')
				-> limit($limit);

		$this -> view -> entries = $items = $Model -> fetchAll($select);
		$this -> view -> totalItems = $totalItems = count($items);
		if(!$totalItems) {
			$this -> setNoRender();
		}
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();	
	}
}
