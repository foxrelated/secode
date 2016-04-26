<?php

class Ynidea_TrophiesController extends Core_Controller_Action_Standard
{

    /**
     * trophy box home page
     */
    public function indexAction()
    {
        // Render
        $this -> _helper -> content -> setNoRender() -> setEnabled();
    }
    

    public function detailAction()
	{	
		$subject = null;
		$viewer = Engine_Api::_()->user()->getViewer();
	    if( !Engine_Api::_()->core()->hasSubject() )
	    {
	      $id = $this->_getParam('id');
	      if( null !== $id )
	      {
	        $subject = Engine_Api::_()->getItem('ynidea_trophy', $id);
			
	        if($subject && $subject->getIdentity())
	        {
	          	Engine_Api::_()->core()->setSubject($subject);
	        }
			else {
				return $this->_helper->requireAuth->forward();
			}
	      }
	    }
		$subject = Engine_Api::_()->core()->getSubject();
		if(!$this->_helper->requireAuth()->setAuthParams($subject, $viewer, 'view')->isValid()) 
			return;
    	$this->_helper->requireSubject('ynidea_trophy');

    	// Increment view count
    	if( !$subject->getOwner()->isSelf($viewer) )
	    {
	      	//$subject->view_count++;
	      	$subject->save();
	    }
		// Render
        $this -> _helper -> content -> setNoRender() -> setEnabled();
	}
    public function createAction(){
        
        // Check auth
        if( !$this->_helper->requireUser()->isValid() ) return;
        
        $viewer = Engine_Api::_()->user()->getViewer();
        
        if( !$this->_helper->requireAuth()->setAuthParams('ynidea_trophy', null, 'create')->isValid()) return;
                   
        $form = $this -> view -> form = new Ynidea_Form_CreateTrophy;
                        
        // If not post or form not valid, return
        if( !$this->getRequest()->isPost() ) {
            return;
        }
        
        $post = $this->getRequest()->getPost();
        if(!$form->isValid($post))
            return;

        // Process
        $table = Engine_Api::_()->getItemTable('ynidea_trophy');       
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            // Create trophy
            $values = array_merge($form->getValues(), array(
                'user_id' => $viewer->getIdentity(),                
            ));
            
            if(Engine_Api::_()->ynidea()->checkTrophyTitle($values['title']))
            {
                $form->getElement('title')->addError('The title have existed!');
                return;
            }
           
            //Insert trophy                                    
            $trophy = $table->createRow();
            $trophy->setFromArray($values);
            $trophy->save();
            $trophy_id = $trophy->getIdentity();
            
            
          // Auth
          $auth = Engine_Api::_()->authorization()->context;
          $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    
          if( empty($values['auth_view']) ) {
            $values['auth_view'] = 'everyone';
          }
    
          if( empty($values['auth_comment']) ) {
            $values['auth_comment'] = 'everyone';
          }
          
          if( empty($values['auth_edit']) ) {
            $values['auth_edit'] = 'owner';
          }
          
          if( empty($values['auth_delete']) ) {
            $values['auth_delete'] = 'owner';
          }
		  
		  if( empty($values['auth_vote']) ) {
            $values['auth_vote'] = 'everyone';
          }
    
          $viewMax = array_search($values['auth_view'], $roles);
          $commentMax = array_search($values['auth_comment'], $roles);
          $editMax = array_search($values['auth_edit'], $roles);
          $deleteMax = array_search($values['auth_delete'], $roles);
		  $voteMax = array_search($values['auth_vote'], $roles);
    
          foreach( $roles as $i => $role ) {
            $auth->setAllowed($trophy, $role, 'view', ($i <= $viewMax));
            $auth->setAllowed($trophy, $role, 'comment', ($i <= $commentMax));
            $auth->setAllowed($trophy, $role, 'edit', ($i <= $editMax));
            $auth->setAllowed($trophy, $role, 'delete', ($i <= $deleteMax));
			$auth->setAllowed($trophy, $role, 'vote', ($i <= $voteMax));
          }
            
          
            $action = @Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $trophy, 'ynidea_trophy_new');
            
            //Insert judges
            $judges = array_unique(explode(',',preg_replace('/\s+/u','',$values['toValues'])));            
            $judge_table = Engine_Api::_()->getItemTable('ynidea_judge'); 
            $jude_item['trophy_id'] = $trophy_id;           
            foreach($judges as $ju){               
                if($ju!='' && $ju != 0){                     
                    $jude_item['user_id'] = $ju; 
                    $judge = $judge_table->createRow();                                         
                    $judge->setFromArray($jude_item);
                    $judge->save();                   
                   
                }
            }
            
            // Set photo
            if( !empty($values['thumbnail']) ) {
                $trophy->setPhoto($form->thumbnail);
            }
            
            // Commit
            $db->commit();
        }
        catch( Exception $e )
        {
          $db->rollBack();
          throw $e;
        }   
        
         // Redirect
        return $this->_helper->redirector->gotoRoute(array('action' => 'edit','id'=>$trophy->trophy_id,'slug'=>$trophy->getSlug()), 'ynidea_trophies', true);     
    }
    
    public function editAction()
    {
        // Check auth
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();  
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $this->_getParam('id'));
        $this->view->trophy_id = $trophy->trophy_id;
        if( !$this->_helper->requireAuth()->setAuthParams($trophy, $viewer, 'edit')->isValid() ) return;
        if(!$trophy)
             return $this->_helper->requireAuth->forward(); 
                
        $this->view->form = $form = new Ynidea_Form_EditTrophy();
        
        // Populate form    
        $form->populate($trophy->toArray());
                
        // If not post or form not valid, return
        if( !$this->getRequest()->isPost() ) {
            return;
        }
        $post = $this->getRequest()->getPost();
        if(!$form->isValid($post))
            return;

        // Process
        $table = Engine_Api::_()->getItemTable('ynidea_trophy');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            $values = $form->getValues();
            if($values['title'] != $trophy->title)
            {
                if(Engine_Api::_()->ynidea()->checkTitle($values['title']))
                {
                    $form->getElement('title')->addError('The title have existed!');
                    return;
                }
            }
            
            $trophy->setFromArray($values);
            $trophy->user_id = $viewer->user_id;
            $trophy->modified_date = date('Y-m-d H:i:s');
            $trophy->save();
                 
            $action = @Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $trophy, 'ynidea_trophy_edit');
			
			//Insert judges
            $judges = array_unique(explode(',',preg_replace('/\s+/u','',$values['toValues'])));            
            $judge_table = Engine_Api::_()->getItemTable('ynidea_judge'); 
            $jude_item['trophy_id'] = $this->_getParam('id');           
            foreach($judges as $ju){               
                if($ju!='' && $ju != 0){                     
                    $jude_item['user_id'] = $ju; 
                    $judge = $judge_table->createRow();                                         
                    $judge->setFromArray($jude_item);
                    $judge->save();                   
                   
                }
            }
            
			// Set photo
            if( !empty($values['thumbnail']) ) {
                $trophy->setPhoto($form->thumbnail);
            }
			
            // Commit
            $db->commit();
        }
        catch( Exception $e )
        {
          $db->rollBack();
          throw $e;
        }
        
        // Redirect
        return $this->_helper->redirector->gotoRoute(array('action' => 'detail','id'=>$trophy->trophy_id,'slug'=>$trophy->getSlug()), 'ynidea_trophies', true);
    }
    
    
	public function favouriteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $trophy_id = (int) $this->_getParam('id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
        if(!$trophy)
             return $this->_helper->requireAuth->forward();
        //if( !$this->_helper->requireAuth()->setAuthParams($idea, $viewer, 'view')->isValid()) {
        //  return;
        //}
        $db = Engine_Api::_()->getDbtable('trophies', 'ynidea')->getAdapter();
        $db->beginTransaction();
        try {
           if($trophy)
            {
              if($trophy->checkFavourite())
              {
                  $favourite_table = Engine_Api::_()->getDbTable('trophyfavourites','ynidea');
                  $favourite = $favourite_table->createRow();
                  $favourite->trophy_id = $trophy->trophy_id;
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
        $trophy_id = (int) $this->_getParam('id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
        if(!$trophy)
             return $this->_helper->requireAuth->forward();
        //if( !$this->_helper->requireAuth()->setAuthParams($idea, $viewer, 'view')->isValid() ) {
        //  return;
        //}
        $db = Engine_Api::_()->getDbtable('trophies', 'ynidea')->getAdapter();
        $db->beginTransaction();
        try {
            if($trophy)
            {
                if(!$trophy->checkFavourite())
                {
                      $favourite =  $trophy->getFavourite();
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
	public function deleteAction()
    {        
        //$this->_helper->layout->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(TRUE);
        // In smoothbox
         $this->_helper->layout->setLayout('default-simple');
         $this->view->form = $form = new Ynidea_Form_DeleteTrophy;
        
        if( !$this->getRequest()->isPost() ) {
          $this->view->status = false;
          $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
          return;
        }
        
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $trophy_id = (int) $this->_getParam('id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
        if(!$trophy)
             return $this->_helper->requireAuth->forward();
        //if( !$this->_helper->requireAuth()->setAuthParams($idea, $viewer, 'view')->isValid() ) {
        //  return;
        //}
        $db = Engine_Api::_()->getDbtable('trophies', 'ynidea')->getAdapter();
        $db->beginTransaction();
        try {
              $trophy->delete();
              $db->commit();
              
        } catch (Exception $e) {
          $db->rollback();
          $this->view->success = false;
          throw $e;
        }
		$this->view->status = true;
    	$this->view->message = Zend_Registry::get('Zend_Translate')->_('Your trophy has been deleted.');
    	return $this->_forward('success' ,'utility', 'core', array(
      	'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'my-trophies', 'action' => 'index'), 'ynidea_extended', true),
      	'messages' => Array($this->view->message)
    ));
    }
    public function enableVotingAction()
    {  
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $trophy_id = (int) $this->_getParam('id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
        if(!$trophy)
             return $this->_helper->requireAuth->forward();
        //if( !$this->_helper->requireAuth()->setAuthParams($idea, $viewer, 'view')->isValid() ) {
        //  return;
        //}
        $db = Engine_Api::_()->getDbtable('trophies', 'ynidea')->getAdapter();
        $db->beginTransaction();
        try {
              $trophy->status = 'voting';
              $trophy->save();
			  
			  // send notification to all judges
			  $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
			  $judges = $trophy->getJudgers();
		      foreach($judges as $judge)
		      {
		      	if($judge->user_id != $viewer->getIdentity())
             	{
		            $ujudge = Engine_Api::_()->getItem('user', $judge->user_id);            
		            $notifyApi->addNotification($ujudge, $viewer, $trophy, 'ynidea_judges_enablevoting', array(
		                  'label' => $trophy->title
		                ));
		        }
			  }
			  
              $db->commit();
              echo Zend_Json::encode(array('success'=>1)); 
        } catch (Exception $e) {
          $db->rollback();
          $this->view->success = false;
          throw $e;
        }
    }
	public function disableVotingAction()
    {  
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $trophy_id = (int) $this->_getParam('id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
        if(!$trophy)
             return $this->_helper->requireAuth->forward();
        //if( !$this->_helper->requireAuth()->setAuthParams($idea, $viewer, 'view')->isValid() ) {
        //  return;
        //}
        $db = Engine_Api::_()->getDbtable('trophies', 'ynidea')->getAdapter();
        $db->beginTransaction();
        try {
           
              $trophy->status = 'finished';
              $trophy->save();
			  // send notification to all judges
			  $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
			  $judges = $trophy->getJudgers();
		      foreach($judges as $judge)
		      {
		      	if($judge->user_id != $viewer->getIdentity())
             	{
		            $ujudge = Engine_Api::_()->getItem('user', $judge->user_id);            
		            $notifyApi->addNotification($ujudge, $viewer, $trophy, 'ynidea_judges_disablevoting', array(
		                  'label' => $trophy->title
		                ));
				}
		       }
              $db->commit();
              echo Zend_Json::encode(array('success'=>1)); 
        } catch (Exception $e) {
          $db->rollback();
          $this->view->success = false;
          throw $e;
        }
    }
	public function suggestAction()
	{
		$trophy_id = (int) $this->_getParam('trophy_id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
		$viewer = Engine_Api::_()->user()->getViewer();
    	if( !$viewer->getIdentity()) 
    	{
	      $data = null;
	    } 
	    else 
	    {
	      $data = array();
	      $table = Engine_Api::_()->getItemTable('user');
	      $select = $table->select();
		  
	      if( 0 < ($limit = (int) $this->_getParam('limit', 10)) ) {
	        $select->limit($limit);
	      }
	
	      if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))) ) {
	        $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
	      }
	      
	      $ids = array();
	      foreach($table->fetchAll($select) as $user ) 
	      {
	      	if(!$trophy)
			{
				$data[] = array(
		          'type'  => 'user',
		          'id'    => $user->getIdentity(),
		          'guid'  => $user->getGuid(),
		          'label' => $user->getTitle(),
		          'photo' => $this->view->itemPhoto($user, 'thumb.icon'),
		          'url'   => $user->getHref(),
		        );
		        $ids[] = $user->getIdentity();
			}
			else {
		      	if(!$trophy->checkJudges($user))
				{
			        $data[] = array(
			          'type'  => 'user',
			          'id'    => $user->getIdentity(),
			          'guid'  => $user->getGuid(),
			          'label' => $user->getTitle(),
			          'photo' => $this->view->itemPhoto($user, 'thumb.icon'),
			          'url'   => $user->getHref(),
			        );
			        $ids[] = $user->getIdentity();
				}
			}
	      }
	    }
	   $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(true);
       $data = Zend_Json::encode($data);
       $this->getResponse()->setBody($data);
	}
	public function assignAction()
    {  
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $trophy_id = (int) $this->_getParam('id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
		$this->view->trophy_id = $trophy_id;
        if(!$trophy)
             return $this->_helper->requireAuth->forward();
		$this->view->form = $form = new Ynidea_Form_AssignJudges();
		if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) 
		{
            $db = Engine_Api::_()->getDbTable('judges', 'ynidea')->getAdapter();
            $db->beginTransaction();
            try {
                $values = $this->getRequest()->getPost();
				//Insert judges
	            $judges = array_unique(explode(',',preg_replace('/\s+/u','',$values['toValues'])));            
	            $judge_table = Engine_Api::_()->getItemTable('ynidea_judge'); 
	            $jude_item['trophy_id'] = $values['trophy_id'];           
	            foreach($judges as $ju)
	            {               
	                if($ju!='' && $ju != 0)
	                {                     
	                    $jude_item['user_id'] = $ju; 
	                    $judge = $judge_table->createRow();                                         
	                    $judge->setFromArray($jude_item);
	                    $judge->save();                   
	                   
	                }
            	}
                $db->commit();
                $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format'=> 'smoothbox',
                  'messages' => array($this->view->translate('Assign successfully.'))
                  ));
            } catch (Exception $e) {
                $db->rollback();
                $this->view->success = false;
            }
        }
	}
    
    public function judgeVoteAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $trophy_id = (int)$this->_getParam('trophy_id');
		$trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
		$viewer = Engine_Api::_()->user()->getViewer(); 
        $idea_id = (int) $this->_getParam('idea_id');
        $user_id = (int) $this->_getParam('user_id');
        $point = (float)$this->_getParam('point');
        
        
        $flag = true;     
        // check enable checkTrophyVote($trophy_id) 
        if(!Engine_Api::_()->ynidea()->checkTrophyVote($trophy_id))
            $flag = false;
            
        // check voted checkTrophyExistedVote($trophy_id,$idea_id,$user_id)    
        //if(Engine_Api::_()->ynidea()->checkTrophyExistedVote($trophy_id,$idea_id,$user_id))
        //   $flag = false;
           
         //check is judge    checkIsJudge($trophy_id,$user_id)    
        if(!Engine_Api::_()->ynidea()->checkIsJudge($trophy_id,$user_id))
            $flag = false;
        if(!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams($trophy, $viewer, 'vote')->checkRequire())
			$flag = false;
        if(!$flag)  
            return;
        // Process
        $table = Engine_Api::_()->getItemTable('ynidea_trophyvote');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            $values['trophy_id'] = $trophy_id;
            $values['idea_id'] = $idea_id;
            $values['user_id'] = $user_id;
            $values['value'] = $point;                                            
            if(!Engine_Api::_()->ynidea()->checkTrophyExistedVote($trophy_id,$idea_id,$user_id))              
            	$vote = $table->createRow();
			else {
				$vote = Engine_Api::_()->ynidea()->checkTrophyExistedVote($trophy_id,$idea_id,$user_id);
			}
            $vote->setFromArray($values);                 
            $vote->save();            
            
            // Commit
            $db->commit();
        }
        catch( Exception $e )
        {
            $flag = 0;
            $db->rollBack();
            throw $e;
        }        
        $myvote = Engine_Api::_()->ynidea()->getTrophyVote($trophy_id,$idea_id,$user_id);
        $judges = Engine_Api::_()->ynidea()->getCountJudge($trophy_id);
        $voters = Engine_Api::_()->ynidea()->getCountJudgeVote($trophy_id,$idea_id);
        $score = Engine_Api::_()->ynidea()->getScoreJudge($trophy_id,$idea_id); 
        $score = number_format($score/$voters,2);
        
        $this->view->myvote = $myvote; 
        $this->view->voters = $voters."/".$judges;
        
        $this->view->score = $score."/10";
        $this->view->success = $flag;
        //echo Zend_Json::encode(array('success'=>$flag));           
    }
	public function downloadPdfAction()
 	{
        $viewer = Engine_Api::_()->user()->getViewer();
        $trophy_id = (int) $this->_getParam('id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
        if(!$trophy)
             return $this->_helper->requireAuth->forward();
        else
        {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
        }
        $pdf = new HTML2PDF('P', 'A4', 'fr');
        $pdf->pdf->SetDisplayMode('real');

        //Add rule for html2pdf
        $content = preg_replace("/\<colgroup.*?\<\/colgroup\>/",'',$trophy->description);
		$info = $this->view->translate("Nominees").": ".$trophy->getNominees()." | ".$this->view->translate("Judges").": ".$trophy->getJudges();
        $pdf->WriteHTML('<page style="font-family: freeserif"><br />'.'<h4>'.nl2br($trophy->title).'</h4>'.nl2br($info).nl2br($content).'</page>');
             
        $name = "trophy_".$trophy->trophy_id.".pdf";
        $pdf->Output($name);
    }
	public function judgeAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
        $trophy_id = (int) $this->_getParam('id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
		if(!$trophy)
             return $this->_helper->requireAuth->forward();
        else
        {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
        }
		$db = Engine_Api::_()->getDbTable('judges', 'ynidea')->getAdapter();
        $db->beginTransaction();
        try {            
            $judge_table = Engine_Api::_()->getItemTable('ynidea_judge'); 
            $jude_item['trophy_id'] = $trophy_id;           
                         
            $jude_item['user_id'] = $viewer->user_id; 
            $judge = $judge_table->createRow();                                         
            $judge->setFromArray($jude_item);
            $judge->save();                   
        	$db->commit();
            $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
              'parentRefresh' => true,
              'format'=> 'smoothbox',
              'messages' => array($this->view->translate('Judge successfully.'))
              ));
        } catch (Exception $e) {
            $db->rollback();
            $this->view->success = false;
        }
	}
	public function deleteJudgeAction()
    {        
         $this->_helper->layout->setLayout('default-simple');
         $this->view->form = $form = new Ynidea_Form_DeleteJudge;
        
        if( !$this->getRequest()->isPost() ) {
          $this->view->status = false;
          $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
          return;
        }
        
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $trophy_id = (int) $this->_getParam('id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
		
		$judge_id = (int) $this->_getParam('judge_id');
		$judge = Engine_Api::_()->getItem('ynidea_judge', $judge_id);
        if(!$trophy || !$judge)
             return $this->_helper->requireAuth->forward();
        $db = Engine_Api::_()->getDbtable('judges', 'ynidea')->getAdapter();
        $db->beginTransaction();
        try {
              $judge->delete();
              $db->commit();
			  $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
              'parentRefresh' => true,
              'format'=> 'smoothbox',
              'messages' => array($this->view->translate('Delete Judge successfully.'))
              ));
              
        } catch (Exception $e) {
          $db->rollback();
          $this->view->success = false;
          throw $e;
        }
		
    }
        
    /**
     * 
     */ 
    public function manageNomineesAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
		if( !$this->_helper->requireUser()->isValid() ) return;
		$trophy_id = (int) $this->_getParam('id');
		$this->view->trophy_id = $trophy_id;
		$trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
		
		//get all ideas
		$values = array('limit'=>1000);
        $ideas = Engine_Api::_()->getApi('core', 'ynidea')->getIdeaPaginator($values);
        $this->view->form = $form = new Ynidea_Form_ManageNominees;  
		
		//get all nominees of this trophy
		$params = array();
		$params['limit'] = 1000;
		$params['trophy_id'] = $trophy_id;
        $params['orderby'] = 'title';
		$params['direction'] = 'ASC';
        $nominees = Engine_Api::_()->getApi('core', 'ynidea')->getIdeaPaginator($params);
		
		$idea_count = 0;
		foreach ($nominees as $idea) 
		{
			$form -> ideas -> addMultiOption($idea -> getIdentity(), $idea -> getTitle());
			$idea_count ++;
		}
		$nominees_arr = array();
		$nominees_count = 0;
		foreach($nominees as $no)
		{
			$nominees_arr[] = $no->getIdentity();
			$nominees_count ++;
		}
		
		$form -> ideas->setValue($nominees_arr);	
		if($idea_count == $nominees_count)
			$form->all->setValue(true);
		$this->view->current_count = $idea_count; 
		
		// Not posting
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$list_nominees = $this -> getRequest() -> getPost('ideas');
        $table_nominee = Engine_Api::_()->getItemTable('ynidea_nominee');  
		$trophy->resetNominees();     
		$nomineeitem = array();    
        $nomineeitem['trophy_id'] =$trophy->trophy_id;
		
        foreach($list_nominees as $no){    
            //Check valid idea
            if(Engine_Api::_()->ynidea()->checkExistedIdea($no) && Engine_Api::_()->ynidea()->checkExistedNominee($no,$trophy->trophy_id) == false){
                $nomineeitem['idea_id'] = $no;
                $nominee = $table_nominee->createRow();
                $nominee->setFromArray($nomineeitem);
                $nominee->save();
            }
        }         
		return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Edit nominees successfully')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
				));
    }
	public function ajaxAction() {
		// Disable layout
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$this->_helper->requireUser()->isValid() ) return;
		$trophy_id = (int) $this->_getParam('id');
		$text = $this -> _getParam('text');
		
        // Process form
        $values = array('limit'=>1000);
        $items = Engine_Api::_()->getApi('core', 'ynidea')->getIdeaPaginator($values);
                        
		$params = array();
		$params['limit'] = 1000;
		$params['trophy_id'] = $trophy_id;
        $params['orderby'] = 'title';
		$params['direction'] = 'ASC';
        $nominees = Engine_Api::_()->getApi('core', 'ynidea')->getIdeaPaginator($params);
		
		$item_arr = array();
		foreach ($items as $item) {
			$item_arr[] = array(
				'id' => $item -> getIdentity(),
				'title' => $item -> getTitle()
			);
		}
		$this -> view -> rows = $item_arr;
		$this -> view -> total = count($item_arr);
	}
	public function resetVotesAction()
	{
		// In smoothbox
         $this->_helper->layout->setLayout('default-simple');
         $this->view->form = $form = new Ynidea_Form_ResetVotes;
        
        if( !$this->getRequest()->isPost() ) {
          $this->view->status = false;
          $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
          return;
        }
		if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $trophy_id = (int) $this->_getParam('id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
        if(!$trophy && !$trophy->isOwner($viewer))
             return $this->_helper->requireAuth->forward();
		$trophy->resetVotes();
		$this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
              'parentRefresh' => true,
              'format'=> 'smoothbox',
              'messages' => array($this->view->translate('Reset all votes successfully.'))
              ));
	}
	public function uploadPhotoAction()
      {    
        // Disable layout
        $this->_helper->layout->disableLayout();

        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $destination = "public/ynidea/";
            if (!is_dir($destination)) {
                mkdir($destination);
            }
        $destination =     "public/ynidea/".$user_id."/";
            if (!is_dir($destination)) {
                    mkdir($destination);
            }
            $upload = new Zend_File_Transfer_Adapter_Http();
            $upload->setDestination($destination);
			$file_info = pathinfo($upload->getFileName('Filedata', false));
			
            $fullFilePath = $destination . time() . '.' . $file_info['extension'];
            $image = Engine_Image::factory();
            $image->open($_FILES['Filedata']['tmp_name'])
                    ->resize(720, 720)
                    ->write($fullFilePath);

            $this->view->status = true;
            $this->view->name = $_FILES['Filedata']['name'];
            $this->view->photo_url = Zend_Registry::get('StaticBaseUrl') . $fullFilePath;
            $this->view->photo_width = $image->getWidth();
            $this->view->photo_height = $image->getHeight();
      }
	public function addNomineesAction()
    {  
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $trophy_id = (int) $this->_getParam('id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
		$this->view->trophy_id = $trophy_id;
        if(!$trophy || !$trophy->isOwner($viewer))
             return $this->_helper->requireAuth->forward();
		$this->view->form = $form = new Ynidea_Form_AddNominees();
		if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) 
		{
            $db = Engine_Api::_()->getDbTable('nominees', 'ynidea')->getAdapter();
            $db->beginTransaction();
            try {
                $values = $this->getRequest()->getPost();
				//Insert nominees
	            $nominees = array_unique(explode(',',preg_replace('/\s+/u','',$values['toValues'])));            
	            $nominee_table = Engine_Api::_()->getItemTable('ynidea_nominee'); 
	            $nominee_item['trophy_id'] = $values['trophy_id'];           
	            foreach($nominees as $no)
	            {               
	                if($no!='' && $no != 0)
	                {                     
	                    $nominee_item['idea_id'] = $no; 
	                    $nominee = $nominee_table->createRow();                                         
	                    $nominee->setFromArray($nominee_item);
	                    $nominee->save();                   
	                   
	                }
            	}
                $db->commit();
                $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format'=> 'smoothbox',
                  'messages' => array($this->view->translate('Add nominees successfully.'))
                  ));
            } catch (Exception $e) {
                $db->rollback();
                $this->view->success = false;
            }
        }
	}
	public function suggestIdeasAction()
	{
		$trophy_id = (int) $this->_getParam('trophy_id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
		$viewer = Engine_Api::_()->user()->getViewer();
    	if( !$viewer->getIdentity()) 
    	{
	      $data = null;
	    } 
	    else 
	    {
	      $data = array();
		  
	      $table = Engine_Api::_()->getItemTable('ynidea_idea');
	      $select = $table->select()->where("`publish_status` = 'publish'");
		  
	      if( 0 < ($limit = (int) $this->_getParam('limit', 10)) ) {
	        $select->limit($limit);
	      }
	
	      if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))) ) {
	        $select->where('`'.$table->info('name').'`.`title` LIKE ?', '%'. $text .'%');
	      }
	      
	      $ids = array();
	      foreach($table->fetchAll($select) as $idea ) 
	      {
	      	if(!$trophy->checkNominee($idea))
			{
		        $data[] = array(
		          'type'  => 'user',
		          'id'    => $idea->getIdentity(),
		          'label' => $idea->title,
		          'photo' => $this->view->itemPhoto($idea, 'thumb.icon'),
		          'url'   => $idea->getHref(),
		        );
		        $ids[] = $idea->getIdentity();
			}
	      }
	    }
	   $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(true);
       $data = Zend_Json::encode($data);
       $this->getResponse()->setBody($data);
	}
	public function removeNomineeAction()
    {        
         $this->_helper->layout->setLayout('default-simple');
         $this->view->form = $form = new Ynidea_Form_RemoveNominee;
        
        if( !$this->getRequest()->isPost() ) {
          $this->view->status = false;
          $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
          return;
        }
        
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $trophy_id = (int) $this->_getParam('trophy_id');
        $trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);
		
		$idea_id = (int) $this->_getParam('id');
		$idea = Engine_Api::_()->getItem('ynidea_idea', $idea_id);
		$nominee = $trophy->checkNominee($idea);
        if(!$trophy || !$nominee)
             return $this->_helper->requireAuth->forward();
        $db = Engine_Api::_()->getDbtable('nominees', 'ynidea')->getAdapter();
        $db->beginTransaction();
        try {
              $nominee->delete();
              $db->commit();
			  $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
              'parentRefresh' => true,
              'format'=> 'smoothbox',
              'messages' => array($this->view->translate('Delete nominee successfully.'))
              ));
              
        } catch (Exception $e) {
          $db->rollback();
          $this->view->success = false;
          throw $e;
        }
		
    }
}
