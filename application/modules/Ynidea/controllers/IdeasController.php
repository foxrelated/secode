<?php
class Ynidea_IdeasController extends Core_Controller_Action_Standard {
    
    public function editAction(){
         //Require User
        if(!$this->_helper->requireUser->isValid()) return;
        
        // Render
        $this -> _helper -> content -> setNoRender() -> setEnabled();
    }
	public function viewAllAction()
	{
		// Render
        $this -> _helper -> content -> setNoRender() -> setEnabled();
	}
	public function favouriteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $idea_id = (int) $this->_getParam('id');
        $idea = Engine_Api::_()->getItem('ynidea_idea', $idea_id);
        if(!$idea)
             return $this->_helper->requireAuth->forward();
        if( !$this->_helper->requireAuth()->setAuthParams($idea, $viewer, 'view')->isValid()) {
          return;
        }
        $db = Engine_Api::_()->getDbtable('ideas', 'ynidea')->getAdapter();
        $db->beginTransaction();
        try {
           if($idea)
            {
              if($idea->checkFavourite())
              {
                  $favourite_table = Engine_Api::_()->getDbTable('favourites','ynidea');
                  $favourite = $favourite_table->createRow();
                  $favourite->idea_id = $idea->idea_id;
                  $favourite->user_id  = $viewer->getIdentity(); 
                  $favourite->save();
                  $db->commit();
              }
              echo Zend_Json::encode(array('success'=>1)); 
            }
        } catch (Exception $e) {
          $db->rollback();
          $this->view->success = false;
          throw $e;
        }
    }
    public function unFavouriteAction()
    {  
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $idea_id = (int) $this->_getParam('id');
        $idea = Engine_Api::_()->getItem('ynidea_idea', $idea_id);
        if(!$idea)
             return $this->_helper->requireAuth->forward();
        if( !$this->_helper->requireAuth()->setAuthParams($idea, $viewer, 'view')->isValid() ) {
          return;
        }
        $db = Engine_Api::_()->getDbtable('ideas', 'ynidea')->getAdapter();
        $db->beginTransaction();
        try {
            if($idea)
            {
                if(!$idea->checkFavourite())
                {
                      $favourite =  $idea->getFavourite();
                      $favourite->delete();
                      $db->commit();
                }
                echo Zend_Json::encode(array('success'=>1)); 
            }
        } catch (Exception $e) {
          $db->rollback();
          $this->view->success = false;
          throw $e;
        }
    }
	public function followAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $idea_id = (int) $this->_getParam('id');
        $idea = Engine_Api::_()->getItem('ynidea_idea', $idea_id);
        if(!$idea)
             return $this->_helper->requireAuth->forward();
        if( !$this->_helper->requireAuth()->setAuthParams($idea, $viewer, 'view')->isValid()) {
          return;
        }
        $db = Engine_Api::_()->getDbtable('ideas', 'ynidea')->getAdapter();
        $db->beginTransaction();
        try {
           if($idea)
            {
              if($idea->checkFollow())
              {
                  $follow_table = Engine_Api::_()->getDbTable('follows','ynidea');
                  $follow = $follow_table->createRow();
                  $follow->idea_id = $idea->idea_id;
                  $follow->user_id  = $viewer->getIdentity(); 
                  $follow->save();
				  
				  $idea->follow_count = $idea->follow_count + 1;
				  $idea->save();
                  $db->commit();
              }
              echo Zend_Json::encode(array('success'=>1)); 
            }
        } catch (Exception $e) {
          $db->rollback();
          $this->view->success = false;
          throw $e;
        }
    }
    public function unFollowAction()
    {  
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $idea_id = (int) $this->_getParam('id');
        $idea = Engine_Api::_()->getItem('ynidea_idea', $idea_id);
        if(!$idea)
             return $this->_helper->requireAuth->forward();
        if( !$this->_helper->requireAuth()->setAuthParams($idea, $viewer, 'view')->isValid() ) {
          return;
        }
        $db = Engine_Api::_()->getDbtable('ideas', 'ynidea')->getAdapter();
        $db->beginTransaction();
        try {
            if($idea)
            {
                if(!$idea->checkFollow())
                {
                      $follow =  $idea->getFollow();
                      $follow->delete();
                      $db->commit();
                }
                echo Zend_Json::encode(array('success'=>1)); 
            }
        } catch (Exception $e) {
          $db->rollback();
          $this->view->success = false;
          throw $e;
        }
    }
	public function giveAwardAjaxAction()
	{
		$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		$viewer = Engine_Api::_()->user()->getViewer();
		$idea = Engine_Api::_()->getItem('ynidea_idea', $this->_getParam('idea_id'));
		if(!$idea)
             return $this->_helper->requireAuth->forward(); 
                
		// Process
        $table = Engine_Api::_()->getDbtable('awards','ynidea');
        $db = $table->getAdapter();
        $db->beginTransaction();
        $values = array();
        try
        {            
            $values['user_id'] = $viewer->getIdentity();
            $values['trophy_id'] = $this->_getParam('trophy_id');
            $values['idea_id']= $this->_getParam('idea_id'); 
			$values['award']= $this->_getParam('award');
			$values['comment']= $this->_getParam('comment');
            
            $award = $table->createRow();
            $award->setFromArray($values);

            $award->save();
			
			$idea->award = 1;
			$idea->save();
			
			$action = @Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $idea, 'ynidea_idea_award');
            
            if( $action != null )
            {
                  Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $idea);
            }        
			// send notification to all follow users
			$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $userFollows = $idea->getFollows();
             foreach($userFollows as $follow)
             {   if($follow->user_id != $viewer->getIdentity())
                 {
                     $userFollow = Engine_Api::_()->getItem('user', $follow->user_id);            
                     $notifyApi->addNotification($userFollow, $viewer, $idea, 'ynidea_idea_award', array(
                      'label' => $idea->title
                    ));
                 }
             }   
                                            
            // Commit
            $db->commit();
			echo Zend_Json::encode(array('success'=>1));
        }
        catch( Exception $e )
        {
          $db->rollBack();
          throw $e;
        }
	}
}