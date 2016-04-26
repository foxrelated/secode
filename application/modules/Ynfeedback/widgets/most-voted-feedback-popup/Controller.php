<?php
class Ynfeedback_Widget_MostVotedFeedbackPopupController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $params = array(
            'orderby' => 'vote_count',
            'direction' => 'DESC'
        );
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this -> view -> inputTitle = $request->getParam('inputTitle');
		$this -> view -> isFinal = $request->getParam('isFinal');
        $ideaTbl = Engine_Api::_()->getItemTable('ynfeedback_idea');
        $select = $ideaTbl -> getIdeasSelect($params);
        $this -> view -> ideas = $ideaTbl -> fetchAll($select);
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
		$level_id = ($viewer->getIdentity()) ? $viewer->level_id : 5;
	    $max = $permissionsTable->getAllowed('ynfeedback_idea', $level_id, 'max_feedback');
	    if ($max == null) {
	        $row = $permissionsTable->fetchRow($permissionsTable->select()
	            ->where('level_id = ?', $level_id)
	            ->where('type = ?', 'ynfeedback_idea')
	            ->where('name = ?', 'max_feedback'));
	        if ($row) {
	            $max = $row->value;
	        }
	    }
	    $table = Engine_Api::_()->getItemTable('ynfeedback_idea');
	    $select = $table->select()
	        -> where('user_id = ?', $viewer->getIdentity())
	        -> where('deleted = ?', 0);
	        
	    $raw_data = $table->fetchAll($select);
		$isReachLimit = false;
	    if ($max && ($max != 0) && (sizeof($raw_data) >= $max)) {
	    	$isReachLimit = true;
	    }
		
		$this->view->isReachLimit = $isReachLimit;
		$this->view->isAllowCreate = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynfeedback_idea', null, 'create')->checkRequire();
    }
}
