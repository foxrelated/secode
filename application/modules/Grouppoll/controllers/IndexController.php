<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns 
 * @author     SocialEngineAddOns
 */

class Grouppoll_IndexController extends Core_Controller_Action_Standard
{
	
  //ACTION FOR CREATE THE NEW POLL
  public function createAction()
	{
		//CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid()) return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    
    //NUMBER OF OPTIONS WHICH WILL BE SHOW AT THE TIME OF POLL CREATION
    $this->view->maxOptions = $max_options = Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.maxoptions', 15);

		//GET USER LEVEL
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;

		//SET GROUP SUBJECT
		$subject = null;
    $group_id = $this->_getParam('group_id');
		if ( !Engine_Api::_()->core()->hasSubject() )
		{
			if( null !== $group_id )
			{
				$subject = Engine_Api::_()->getItem('group', $group_id);
				if( $subject && $subject->getIdentity() )
				{
					Engine_Api::_()->core()->setSubject($subject);
				}
			}
		}
		$group_subject = $subject;

		//WHO CAN VIEW THE GROUP-POLL
		if ( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->_forward('requireauth', 'error', 'core');
    }

		//CHECK WHO CAN CREATE POLLS
		$can_create = Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'gpcreate');

    // CHECK FOR THE PERMISION OF POLL CREATION
		if(empty($can_create)) {
			return $this->_forward('requireauth', 'error', 'core');
		}

		//MAKE LINK OF "BACK TO GROUP-POLLS"
		$this->view->TAB_SELECTED_ID = Engine_Api::_()->grouppoll()->getTabId();
		$this->view->group_id = $group_id;


    //SHOW POLL CREATE FORM
    $this->view->form = $form = new Grouppoll_Form_Create();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

			//CHECK OPTIONS
			$options = (array) $this->_getParam('optionsArray');
			$options = array_filter(array_map('trim', $options));
			$options = array_slice($options, 0, $max_options);
			$this->view->options = $options;
			if ( empty($options) || !is_array($options) || count($options) < 2 ) {
				return $form->addError('You must provide at least two possible answers.');
			}
			foreach( $options as $index => $option ) {
				if( strlen($option) > 80 ) {
					$options[$index] = Engine_String::substr($option, 0, 80);
				}
			}

			//CONNET WITH POLL TABLE
      $grouppollTable = Engine_Api::_()->getItemTable('grouppoll_poll');
      $grouppollOptionsTable = Engine_Api::_()->getDbtable('options', 'grouppoll');
      $db = $grouppollTable->getAdapter();
      $db->beginTransaction();

      //GET POSTED VALUES FROM CREATE FORM
      $values = $form->getValues(); 
			if ($values['end_settings'] == 1 && $values['end_time'] == 0) {
				$error = $message . $this->view->translate('Please select end-date and time from calendar !');
        $error = Zend_Registry::get('Zend_Translate')->_($error);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
			}

      //POLL CREATION CODE
      try {
        $values = array_merge($form->getValues(), array(
                    'owner_type' => $viewer->getType(),
                    'owner_id' => $viewer->getIdentity(),
                ));
        $this->view->is_error = 0;
        $this->view->excep_error = 0;

        //CREATE THE MAIN POLL
        $grouppollRow = $grouppollTable->createRow(); 
        $grouppollRow->setFromArray($values);
				$grouppollRow->parent_id = $grouppollRow->group_id = $group_id;
				$grouppollRow->approved = 1;
				$grouppollRow->parent_type = 'group';
				$group = Engine_Api::_()->getItem('group', $group_id);
				$grouppollRow->group_owner_id = $group->user_id;
				$grouppollRow->save();

        //CREATE OPTIONS
        $censor = new Engine_Filter_Censor();
        foreach( $options as $option ) {
        $grouppollOptionsTable->insert(array(
          'poll_id' => $grouppollRow->poll_id,
          'grouppoll_option' => $censor->filter($option),
          ));
        }
        
				//COMMENT PRIVACY
				$auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
        $auth_comment = "everyone";
        $commentMax = array_search($auth_comment, $roles);
        foreach ($roles as $i => $role) {
          $auth->setAllowed($grouppollRow, $role, 'comment', ($i <= $commentMax));
        }

				//ACTIVITY FEED
				$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $group_subject, 'grouppoll_new');
        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $grouppollRow);
        }

        $db->commit();
      }
			catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

			//GO TO GROUP PROFILE PAGE WITH GROUP POLL SELECTED TAB
			return $this->_helper->redirector->gotoRoute(array('id' => $group_id, 'tab' => Engine_Api::_()->grouppoll()->getTabId()), 'group_profile', true);
    }
  }

  //ACTION FOR VIEW THE POLL
  public function viewAction()
	{
		
    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET POLL MODEL
    $this->view->grouppoll = $grouppoll = Engine_Api::_()->getItem('grouppoll_poll', $this->_getParam('poll_id'));
		if (empty($grouppoll)) {
			return $this->_forward('notfound', 'error', 'core');
		}

		if(!empty($viewer_id)) {
			$level_id = Engine_Api::_()->user()->getViewer()->level_id;
		}
		else {
			$level_id = 0;
		}
  
    //DIS-APPROVED POLL WILL BE VISIBLE ONLY TO POLL-OWNER, GROUP-OWNER AND SUEPRADMIN
		if ($grouppoll->owner_id != $viewer_id && $grouppoll->group_owner_id != $viewer_id && $level_id != 1 && $grouppoll->approved != 1 ) {
			return $this->_forward('requireauth', 'error', 'core');
		}

		$group_id = $grouppoll->group_id;

		//SET GROUP SUBJECT
		$subject = null;
		if ( !Engine_Api::_()->core()->hasSubject() )
		{
			if ( null !== $group_id )
			{
				$subject = Engine_Api::_()->getItem('group', $group_id);
				if ( $subject && $subject->getIdentity() )
				{
					Engine_Api::_()->core()->setSubject($subject);
				}
			}
		}
		$group_subject = $subject;
		$gp_auth_vote = $grouppoll->gp_auth_vote;
		if ( !$group_subject->membership()->isMember($viewer) ) {
      $group_member = 0; 
    }
    else {
      $group_member = 1;
    }

		//CHECK THAT VIEWER IS OFFICER OR NOT
		$list = $group_subject->getOfficerList();
		$listItem = $list->get($viewer);
    $isOfficer = ( null !== $listItem );

    if ((($gp_auth_vote == 1 && $viewer_id != 0) || ($gp_auth_vote == 2 && $group_member == 1) || ($gp_auth_vote == 3 && $grouppoll->group_owner_id == $viewer_id) || ($gp_auth_vote == 3 && $isOfficer == 1)) && $grouppoll->approved == 1) {
      $this->view->can_vote = 1;
		}
    else {
      $this->view->can_vote = 0;
    }

		//WHO CAN VIEW THE GROUP-POLL
		if ( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->_forward('requireauth', 'error', 'core');
    }
 
		//WHO CAN COMMENT ON THIS THIS POLL
		if ( !$subject->authorization()->isAllowed($viewer, 'comment') ) {
			$this->view->can_comment = 0;
    }
		else {
			$this->view->can_comment = 1;
		}

		//SEND GROUP TITLE TO TPL
		$this->view->group_title = $subject->title;

		//DESTROY THE PREVIOUS SUBJECT
		Engine_Api::_()->core()->clearSubject();
 
    //REPORT CODE
    if (!empty($viewer_id)) {
      $report = $grouppoll;
      if (!empty($report)) {
        Engine_Api::_()->core()->setSubject($report);
      }
      if (!$this->_helper->requireSubject()->isValid())
        return;
    }

    $this->view->grouppollOptions = $grouppoll->getOptions();
    $this->view->hasVoted = $grouppoll->viewerVoted();
    $this->view->showPieChart = Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.showPieChart', false);
    $this->view->canVote = $grouppoll->authorization()->isAllowed(null, 'vote');
    $this->view->canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.canchangevote', false);

		$grouppoll->views++;
		$grouppoll->save();
  }

  //ACTION FOR DELETE POLL
  public function deleteAction()
	{
		//CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid()) return;

    //GET LOGGED IN INFORMATION
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		$this->view->group_id = $group_id = $this->_getParam('group_id');
		$this->view->TAB_SELECTED_ID = Engine_Api::_()->grouppoll()->getTabId();

    //GET POLL MODEL
    $this->view->grouppoll = $grouppoll = Engine_Api::_()->getItem('grouppoll_poll', $this->_getParam('poll_id'));
   
		//POLL OWNER AND GROUP OWNER CAN DELETE POLL
		if($viewer_id != $grouppoll->owner_id && $viewer_id != $grouppoll->group_owner_id) {
			return $this->_forward('requireauth', 'error', 'core');
		}

    //DELETE POLL FROM DATATBASE AND SCRIBD AFTER CONFIRMATION
    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
      $grouppoll->delete();
			return $this->_helper->redirector->gotoRoute(array('id' => $group_id, 'tab' => Engine_Api::_()->grouppoll()->getTabId()), 'group_profile', true);
    }
  }

  public function voteAction()
  { 

    //CHECK USER VALIDATION
    if( !$this->_helper->requireUser()->isValid() ) return;
  
    //GET SUBJECT
    $grouppoll = null;
    if( null !== ($grouppollIdentity = $this->_getParam('poll_id')) ) {
      $grouppoll = Engine_Api::_()->getItem('grouppoll_poll', $grouppollIdentity);
      if ( null !== $grouppoll ) {
        Engine_Api::_()->core()->setSubject($grouppoll);
      }
    }

    //CHECK METHOD
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    $this->view->option_id = $option_id = $this->_getParam('option_id');
    $canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.canchangevote', false);

    //GET LOGGED IN INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if ( !$grouppoll ) {
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('This poll does not seem to exist anymore.');
      return;
    }

    if ( $grouppoll->hasVoted($viewer) && !$canChangeVote ) {
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('You have already voted on this poll, and are not permitted to change your vote.');
      return;
    }

    $db = Engine_Api::_()->getDbtable('polls', 'grouppoll')->getAdapter();
    $db->beginTransaction();
    try {
      $grouppoll->vote($viewer, $option_id);
      $db->commit();
    } 
    catch( Exception $e ) {
      $db->rollback();
      $this->view->success = false;
      throw $e;
    }
    
    $this->view->success = true;
    $grouppollOptions = array();
    foreach( $grouppoll->getOptions()->toArray() as $option ) {
      $option['votesTranslated'] = $this->view->translate(array('%s vote', '%s votes', $option['votes']), $this->view->locale()->toNumber($option['votes']));
      $grouppollOptions[] = $option;
    }
    $this->view->grouppollOptions = $grouppollOptions;
    $this->view->votes_total = $grouppoll->vote_count;
  }
  
  public function closeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $grouppoll = Engine_Api::_()->getItem('grouppoll_poll', $this->_getParam('poll_id'));
    $group_id = $this->_getParam('group_id');
    $TAB_SELECTED = Engine_Api::_()->grouppoll()->getTabId();
    
    //POLL OWNER AND GROUP OWNER CAN CLOSE POLL
		if($viewer_id != $grouppoll->owner_id && $viewer_id != $grouppoll->group_owner_id) {
			return $this->_forward('requireauth', 'error', 'core');
		}
     
    $grouppollTable = $grouppoll->getTable();
    $db = $grouppollTable->getAdapter();
    $db->beginTransaction();

    try {
      $grouppoll->closed = (bool) $this->_getParam('closed');
      $grouppoll->save();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
		return $this->_helper->redirector->gotoRoute(array('id' => $group_id, 'tab' => $TAB_SELECTED), 'group_profile', true);
 
  }

}
?>