<?php
class Ynevent_YnultimatevideoController extends Core_Controller_Action_Standard {

	public function init(){
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			if (0 !== ($event_id = (int)$this -> _getParam('event_id')) && null !== ($event = Engine_Api::_() -> getItem('event', $event_id))) {
				Engine_Api::_() -> core() -> setSubject($event);
			}
		}
		if (!Engine_Api::_() -> core() -> hasSubject()){
			return $this->_helper->requireSubject->forward();
		}
	}

	public function listAction(){
		// return if module is not activated
		$video_enable = Engine_Api::_()->hasItemType('ynultimatevideo_video');
		if(!$video_enable){
			return $this->_helper->requireSubject->forward();
		}

		//Get viewer, event, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Ynevent_Form_Ynultimatevideo_Search;

		if (!$this -> _helper -> requireAuth() -> setAuthParams($event, null, 'view') -> isValid()) {
			return;
		}
		// Check create video authorization
		$canCreate = $event -> authorization() -> isAllowed($viewer, 'video');
		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('event', $viewer, 'video');

		if ($canCreate && $levelCreate) {
			$this -> view -> canCreate = true;
		} else {
			$this -> view -> canCreate = false;
		}

		//Prepare data filer
		$params = $this->_getAllParams();
		$params['parent_type'] = 'event';
		$params['parent_id'] = $event->getIdentity();
		$params['search'] = 1;
		$params['status'] = 1;
		$params['limit'] = 12;
		$form->populate($params);
		$this->view->formValues = $form->getValues();

		//Get data from table video
		$tableVideo = Engine_Api::_()->getItemTable('ynultimatevideo_video');
		$select = $tableVideo -> select()
			-> from($tableVideo -> info('name'), new Zend_Db_Expr("`video_id`"))
			-> where('parent_type = "event"')
			-> where('parent_id = ?', $event -> getIdentity());
		$video_ids = $tableVideo -> fetchAll($select);

		foreach($video_ids as $video_id)
		{
			$params['ids'][] = $video_id -> video_id;
		}

		//Get data
		$this -> view -> paginator = $paginator = $event -> getYnultimatevideoVideosPaginator($params);

		return true;
	}

	public function highlightAction() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$event = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if(!$viewer -> isSelf($event -> getOwner()))
		{
			return;
		}
		$item_id = $this -> _getParam('video_id', null);

		$table = Engine_Api::_() -> getDbTable('highlights', 'ynevent');
		$select = $table->select()
			-> where("event_id = ?", $event -> getIdentity())
			-> where("type = 'video'")
			-> where('item_id = ?', $item_id)
			-> limit(1);
		$items  = $table->fetchAll($select);
		if (!count($items))
		{
			$highlightItem = $table->createRow();
			$highlightItem->setFromArray(array(
				'user_id' => $viewer->getIdentity(),
				'event_id' => $event -> getIdentity(),
				'item_id' => $item_id,
				'type' => 'video',
				'highlight' => 1
			));
		}
		else
		{
			$db = $table -> getAdapter();
			$db -> beginTransaction();
			try {
				$select2 = $table -> select() -> where("event_id = ?", $event -> getIdentity()) -> where("type = 'video'") -> where("highlight = 1") -> limit(1);
				$row2 = $table -> fetchRow($select2);
				if($row2)
				{
					$row2 -> highlight = !$row2 -> highlight;
					$row2->save();
				}

				$select = $table -> select() -> where("event_id = ?", $event -> getIdentity()) -> where('item_id = ?', $item_id) -> where("type = 'video'") -> limit(1);
				$row = $table -> fetchRow($select);
				if ($row->item_id != $row2->item_id) {
					$row -> highlight = !$row -> highlight;
					$row -> save();
				}

				$db -> commit();

			} catch (Exception $e) {
				$db -> rollback();
				$this -> view -> success = false;
			}
		}

		$this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format'=> 'smoothbox',
			'messages' => array($this->view->translate('Success.'))
		));
	}
}

?>
