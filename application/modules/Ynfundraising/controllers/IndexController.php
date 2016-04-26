<?php
class Ynfundraising_IndexController extends Core_Controller_Action_Standard
{
	/**
	 * init check exist Ynidea plugin enable
	 *
	 */
	public function init()
  	{
	}
	/**
	 * Browser Campaign (Home page)
	 */
	public function browseAction() {
		// Render
		$this->_helper->content->setNoRender ()->setEnabled ();
	}

	/**
	 * Show all Ideas/Trophies to create campaign
	 */
	public function createAction() {
		// Check auth
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		if (! $this->_helper->requireAuth ()->setAuthParams ( 'ynfundraising_campaign', null, 'create' )->isValid ())
		{
			return;
		}

		if(!Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ())
		{
			return $this->_helper->redirector->gotoRoute ( array (
				"action" => "create-step-one"
				), 'ynfundraising_general', true );
		}

		$this->view->form = $form = new Ynfundraising_Form_SearchParent ();
		// Process form
		$form->isValid ( $this->_getAllParams () );
		$values = $form->getValues ();
		$this->view->formValues = array_filter ( $values );
		$this->view->paginator = $paginator = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getParentPaginator ( $values );

		$items_count = ( int ) Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.page', 10 );
		$this->view->paginator->setItemCountPerPage ( $items_count );
		$this->view->paginator->setCurrentPageNumber ( $this->_getParam ( 'page', 1 ) );
		$this->view->current_count = $paginator->getTotalItemCount ();
	}
	/**
	 * Confirm create campaign for owner
	 */
	public function confirmCreateAction() {
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		//check auth
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $this->_helper->requireAuth ()->setAuthParams ( 'ynfundraising_campaign', null, 'create' )->isValid ())
		{
			return;
		}
		if(!Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ()) {
			return $this->_helper->requireAuth->forward ();
		}

		// In smoothbox
		$this->_helper->layout->setLayout ( 'default-simple' );
		$this->view->form = new Ynfundraising_Form_ConfirmCreate ();
	}
	/**
	 * Send request to Idea/Trophy Owner
	 */
	public function requestCreateAction()
	{
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		//check auth
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $this->_helper->requireAuth ()->setAuthParams ( 'ynfundraising_campaign', null, 'create' )->isValid ())
		{
			return;
		}
		if(!Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ()) {
			return $this->_helper->requireAuth->forward ();
		}

		$parent_id = $this->_getParam('parent_id');
		$parent_type = $this->_getParam('parent_type');
		$parent = Engine_Api::_()->getApi('core', 'ynfundraising')->getItemFromType(array('parent_id'=>$parent_id, 'parent_type'=>$parent_type));
		if(!$parent || $parent->checkExistCampaign() || $parent->checkExistRequestApproved() || $parent->checkExistRequest())
		{
			return $this->_helper->requireAuth->forward ();
		}
		// In smoothbox
		$this->_helper->layout->setLayout ( 'default-simple' );
		$this->view->form = $form = new Ynfundraising_Form_RequestCreate ();

		if (! $this->getRequest ()->isPost ()) {
			return;
		}
		$values = $form->getValues ();
		$table = Engine_Api::_ ()->getDbTable ( 'requests', 'ynfundraising' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();

		try {
			$values = array_merge ( $values, array (
					'requester_id' => $viewer->getIdentity (),
					'request_date' => date ( 'Y-m-d H:i:s' ),
					'status' => 'waiting'
			) );
			$request = $table->createRow ( $values );
			$request->setFromArray ( $values );
			$request->save ();

			// send notification to idea/trophy owner
			$item = Engine_Api::_()->ynfundraising()->getItemFromType($values); // idea or trophy
			$owner = $item->getOwner();

			$notificationTbl = Engine_Api::_ ()->getDbtable ( 'notifications', 'activity' );
			$notificationTbl->addNotification ( $owner, $viewer, $item, 'ynfundraising_notify_request');

			$db->commit();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}

		$router = $this->getFrontController ()->getRouter ();
		$this->_forward ( 'success', 'utility', 'core', array (
				'smoothboxClose' => true,
				'parentRedirect' => $router->assemble ( array (
						'controller' => 'request'
				), 'ynfundraising_extended', true ),
				'format' => 'smoothbox',
				'messages' => array (
						$this->view->translate ( 'Request campaign successfully.' )
				)
		) );
	}
	/*
	 * Create campaign step 01
	 */
	public function createStepOneAction()
	{
		// Check auth
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $this->_helper->requireAuth ()->setAuthParams ( 'ynfundraising_campaign', null, 'create' )->isValid ())
		{
			return;
		}

		$this->view->form = $form = new Ynfundraising_Form_CreateStepOne ();
		$parent_type = $this->_getParam ( 'parent_type' );
		$parent_id = $this->_getParam ( 'parent_id' );
		$request_id = $this->_getParam ( 'request_id' );
		$request = Engine_Api::_ ()->getItem ( 'ynfundraising_request', $request_id );

		$campaing_outsideIdea = false;

		if ($request)
		{
			//Create campaign from request approved
			if($request->requester_id != $viewer->getIdentity() || $request->status != 'approved' || $request->is_completed == 1)
			{
				return $this->_helper->requireAuth->forward ();
			}
			$parent_type = $request->parent_type;
			$parent_id = $request->parent_id;
			$item = array (
					'parent_type' => $parent_type,
					'parent_id' => $parent_id
			);
			$parent_obj = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getItemFromType ( $item );
		}
		elseif($parent_type && $parent_id)
		{
			//Idea/Trophy owner create campaign
			$item = array (
					'parent_type' => $parent_type,
					'parent_id' => $parent_id
			);
			$parent_obj = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getItemFromType ( $item );
			if (! $parent_obj || ! $parent_obj->isOwner ( $viewer ) || $parent_obj->checkExistRequestApproved()) {
				return $this->_helper->requireAuth->forward ();
			}
		}
		else
		{
			//create campaing with out idea box (with parent_type = 'user'  + parent_id = user_id)
			$campaing_outsideIdea = true;
			$parent_type = 'user';
			$parent_id = $viewer->getIdentity();


		}
		//Check Idea/Trophy exist and campaign exist
		if (!$campaing_outsideIdea && (! $parent_obj || $parent_obj->checkExistCampaign()))
		{
			return $this->_helper->requireAuth->forward ();
		}

		// If not post or form not valid, return
		if (! $this->getRequest ()->isPost ()) {
			return;
		}

		$post = $this->getRequest ()->getPost ();


		if (! $form->isValid ( $post ))
			return;

			// Process
		$table = Engine_Api::_ ()->getDbTable ( 'campaigns', 'ynfundraising' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();

		try {
			// Create campaign
			$values = array_merge ( $form->getValues (), array (
					'user_id' => $viewer->getIdentity (),
					'owner_type' => $viewer->getType ()
			) );

			if ($parent_type) {
				$values ['parent_type'] = $parent_type;
			}

			if ($parent_id) {
				$values ['parent_id'] = $parent_id;
			}

			$arr_predefined = array();
			foreach ($values ['predefined'] as $predefined) {
				if ($predefined != '') {
					$arr_predefined[] = (float)$predefined;
				}
			}

			$values ['predefined'] = implode ( ',', $arr_predefined );
			if(strtotime($values['expiry_date']) > 0)
			{
				 // Convert times
			    $oldTz = date_default_timezone_get();
			    date_default_timezone_set($viewer->timezone);
			    $expiry_date = strtotime($values['expiry_date']);
				$now = strtotime(date('Y-m-d H:i:s'));
				date_default_timezone_set($oldTz);
			    $values['expiry_date'] = date('Y-m-d H:i:s', $expiry_date);

				if($expiry_date <= $now)
			      {
			          $form->getElement('expiry_date')->addError('Expiry Date should be greater than Current Time!');
			          return;
			      }
			}
			else {
				$values['expiry_date'] = "0000-00-00 00:00:00";
			}
			if($values['minimum_donate'] > $values['goal'])
		      {
		          $form->getElement('goal')->addError('Fundraising Goal should be greater than Minimum Donation Amount!');
		          return;
		      }
		    date_default_timezone_set($oldTz);

			$campaign = $table->createRow ();
			$campaign->setFromArray ( $values );
			$campaign->save ();


			// if ideabox is available -> send email to other requesters
			if(Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ())
			{
				if ($request) {
					// update request_id when create campaign from request approved
					$campaign->request_id = $request_id;
					$request->is_completed = 1;
					$request->save ();
					$campaign->save ();
				}
				$requestTbl = Engine_Api::_ ()->getItemTable ( 'ynfundraising_request' );
				$request_select = $requestTbl->select ()->where ( 'status = ?', Ynfundraising_Plugin_Constants::REQUEST_WAITING_STATUS )->where ( 'parent_id = ?', $parent_id )->where ( 'parent_type = ?', $parent_type )->where ( 'is_completed = ?', 0 );
				$request_result = $requestTbl->fetchAll ( $request_select );
				foreach ( $request_result as $other_request ) {
					$other_request->visible = 0;
					$other_request->save ();
				}
			}

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

			  if( empty($values['auth_donate']) ) {
                $values['auth_vote'] = 'everyone';
              }

              $viewMax = array_search($values['auth_view'], $roles);
              $commentMax = array_search($values['auth_comment'], $roles);
              $editMax = array_search($values['auth_edit'], $roles);
              $deleteMax = array_search($values['auth_delete'], $roles);
			  $voteMax = array_search($values['auth_donate'], $roles);

              foreach( $roles as $i => $role ) {
	          	$auth->setAllowed($campaign, $role, 'view', ($i <= $viewMax));
	          	$auth->setAllowed($campaign, $role, 'comment', ($i <= $commentMax));
	          	$auth->setAllowed($campaign, $role, 'edit', ($i <= $editMax));
	            $auth->setAllowed($campaign, $role, 'delete', ($i <= $deleteMax));
				$auth->setAllowed($campaign, $role, 'donate', ($i <= $voteMax));
              }


			// Set photo
			if (! empty ( $values ['thumbnail'] )) {
				$campaign->setPhoto ( $form->thumbnail );
			}

			// Add tags
			$tags = preg_split ( '/[,]+/', $values ['tags'] );
			$campaign->tags ()->addTagMaps ( $viewer, $tags );

			// Commit
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		return $this->_redirect ( "ynfundraising/photo/upload/subject/ynfundraising_campaign_" . $campaign->campaign_id );
	}
	/*
	 * Create campaign step 02 (Upload and manage photos)
	 */
	public function createStepTwoAction() {
		// Check auth
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();

		$campaign_id = $this->_getParam ( 'campaignId' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );

		if (! $campaign)
		{
			return $this->_helper->requireAuth->forward ();
		}
		if(!$this->_helper->requireAuth()->setAuthParams($campaign, $viewer, 'edit')->isValid()) return;

		$this->view->campaign_id = $campaign->campaign_id;
		$this->view->photo_id = $campaign->photo_id;
		$this->view->form = $form = new Ynfundraising_Form_Photo_Manage ();
		$form->video_url->setValue ( $campaign->video_url );
		if ($campaign->photo_id > 0)
			if (! $campaign->getPhoto ( $campaign->photo_id )) {
				$campaign->addPhoto ( $campaign->photo_id );
			}
		$this->view->album = $album = $campaign->getSingletonAlbum ();
		$this->view->paginator = $paginator = $album->getCollectiblesPaginator ();

		$paginator->setCurrentPageNumber ( $this->_getParam ( 'page' ) );
		$paginator->setItemCountPerPage ( 100 );

		foreach ( $paginator as $photo ) {
			$subform = new Ynfundraising_Form_Photo_Edit ( array (
					'elementsBelongTo' => $photo->getGuid ()
			) );
			$subform->removeElement ( 'label' );
			if ($photo->file_id == $campaign->photo_id) {
				$subform->removeElement ( 'delete' );
			}
			$subform->populate ( $photo->toArray () );
			$form->addSubForm ( $subform, $photo->getGuid () );
			$form->cover->addMultiOption ( $photo->getIdentity (), $photo->getIdentity () );
		}
		if (! $this->getRequest ()->isPost ()) {
			return;
		}

		if (! $form->isValid ( $this->getRequest ()->getPost () )) {
			return;
		}

		// Process
		$table = Engine_Api::_ ()->getItemTable ( 'ynfundraising_photo' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();

		try {
			$values = $form->getValues ();
			$params = array (
					'campaign_id' => $campaign->getIdentity (),
					'user_id' => $viewer->getIdentity ()
			);
			$cover = $values ['cover'];
			// Process
			$count = 0;

			foreach ( $paginator as $photo ) {
				$subform = $form->getSubForm ( $photo->getGuid () );
				$subValues = $subform->getValues ();
				$subValues = $subValues [$photo->getGuid ()];

				if (isset ( $cover ) && $cover == $photo->photo_id) {
					$campaign->photo_id = $photo->file_id;
					$photo->save ();
					$campaign->save ();
				}
				if (isset ( $values ['video_url'] ) && $values ['video_url'] != '') {
					$campaign->video_url = $values ['video_url'];
					$campaign->save ();
				}
				if (isset ( $subValues ['delete'] ) && $subValues ['delete'] == '1') {
					if ($campaign->photo_id == $photo->file_id) {
						$campaign->photo_id = 0;
						$campaign->save ();
					}
					$photo->delete ();
				} else {
					$photo->setFromArray ( $subValues );
					$photo->save ();
				}
			}

			$db->commit ();
		}

		catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		return $this->_helper->redirector->gotoRoute ( array (
				"action" => "create-step-four",
				'campaignId' => $campaign->campaign_id
		), 'ynfundraising_general', true );
	}
	/**
	 * Add location + check google map
	 */
	public function addLocationAction() {
		$this->view->form = $form = new Ynfundraising_Form_addLocation ();
		// If not post or form not valid, return
		if (! $this->getRequest ()->isPost ()) {
			return;
		}
	}
	/**
	 * Create step four add contact information
	 */
	public function createStepFourAction()
	{
		// Check auth
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$campaign_id = $this->_getParam ( 'campaignId' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		if (! $campaign)
		{
			return $this->_helper->requireAuth->forward ();
		}
		if(!$this->_helper->requireAuth()->setAuthParams($campaign, $viewer, 'edit')->isValid()) return;

		$this->view->campaign_id = $campaign_id;
		$this->view->form = $form = new Ynfundraising_Form_CreateStepFour ();
		$form->populate ( $campaign->toArray () );
		// If not post or form not valid, return
		if (! $this->getRequest ()->isPost ()) {
			return;
		}

		$post = $this->getRequest ()->getPost ();
		if (! $form->isValid ( $post ))
			return;

			// Process
		$table = Engine_Api::_ ()->getDbTable ( 'campaigns', 'ynfundraising' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();

		try {
			// Create campaign
			$values = $form->getValues ();
			$campaign->setFromArray ( $values );
			$campaign->save ();
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		return $this->_helper->redirector->gotoRoute ( array (
				"action" => "create-step-five",
				'campaignId' => $campaign->campaign_id
		), 'ynfundraising_general', true );
	}
	/**
	 * Create step five
	 *
	 */
	public function createStepFiveAction()
	{
		// Check auth
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$campaign_id = $this->_getParam ( 'campaignId' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		if (! $campaign)
		{
			return $this->_helper->requireAuth->forward ();
		}
		if(!$this->_helper->requireAuth()->setAuthParams($campaign, $viewer, 'edit')->isValid()) return;

		$this->view->campaign_id = $campaign_id;
		$this->view->form = $form = new Ynfundraising_Form_CreateStepFive ();
		$form->populate ( $campaign->toArray () );
		// If not post or form not valid, return
		if (! $this->getRequest ()->isPost ()) {
			return;
		}

		$post = $this->getRequest ()->getPost ();
		if (! $form->isValid ( $post ))
			return;

			// Process
		$table = Engine_Api::_ ()->getDbTable ( 'campaigns', 'ynfundraising' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();

		try {
			// Create campaign
			$values = $form->getValues ();
			$campaign->setFromArray ( $values );
			$campaign->save ();
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		return $this->_helper->redirector->gotoRoute ( array (
				"action" => "create-step-six",
				'campaignId' => $campaign->campaign_id
		), 'ynfundraising_general', true );
	}
	/**
	 *
	 * Create step six
	 */
	public function createStepSixAction()
	{
		// Check auth
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$campaign_id = $this->_getParam ( 'campaignId' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		if (! $campaign)
		{
			return $this->_helper->requireAuth->forward ();
		}
		if(!$this->_helper->requireAuth()->setAuthParams($campaign, $viewer, 'edit')->isValid()) return;

		$this->view->form = $form = new Ynfundraising_Form_CreateStepSix ();
		$this->view->campaign_id = $campaign_id;
		// If not post or form not valid, return
		if (! $this->getRequest ()->isPost ()) {
			return;
		}

		$post = $this->getRequest ()->getPost ();
		if (! $form->isValid ( $post ))
			return;

		// Process send invte
		$values = $form->getValues();
		$string_recipients = $values['recipients'];
		$arr_recipients = explode(',', $string_recipients);
		$toUsers = explode(',',$values['toValues']);
		$message = $values['custom_message'];

		$params = $campaign->toArray();
		$params['inviter_name'] = $viewer->getTitle();
		$params['message'] = $message;
		//send invite to friend on se site
		foreach($toUsers as $user_id)
		{
			$user = Engine_Api::_ ()->getItem ( 'user', $user_id );
			if($user->getIdentity())
			{
				$sendTo = $user->email;
			    Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_inviteFriends',$params);
			}
		}
		$pattern = '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
		//send invite with email
		foreach($arr_recipients as $email)
		{
			if(preg_match($pattern, $email))
			{
				$sendTo = $email;
			    Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_inviteFriends',$params);
			}
		}

		$form->toValues->setValue("");

		if($campaign->published == 0)
		{
			return $this->_helper->redirector->gotoRoute ( array (
					"action" => "create-step-seven",
					'campaignId' => $campaign->campaign_id
			), 'ynfundraising_general', true );
		}
		else {
			$form->addNotice ($this->view->translate('Send invite successfully.'));
		}
	}

	/**
	 * get all friends
	 */
	public function suggestAction()
	{
		$campaign_id = ( int ) $this->_getParam ( 'campaign_id' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer->getIdentity () || ! $campaign) {
			$data = null;
		} else {
			$data = array ();
			$table = Engine_Api::_ ()->getItemTable ( 'user' );
			$select = $table->select ();

			if (0 < ($limit = ( int ) $this->_getParam ( 'limit', 10 ))) {
				$select->limit ( $limit );
			}

			if (null !== ($text = $this->_getParam ( 'search', $this->_getParam ( 'value' ) ))) {
				$select->where ( '`' . $table->info ( 'name' ) . '`.`displayname` LIKE ?', '%' . $text . '%' );
			}

			$ids = array ();
			foreach ( $table->fetchAll ( $select ) as $user ) {
				$data [] = array (
						'type' => 'user',
						'id' => $user->getIdentity (),
						'guid' => $user->getGuid (),
						'label' => $user->getTitle (),
						'photo' => $this->view->itemPhoto ( $user, 'thumb.icon' ),
						'url' => $user->getHref ()
				);
				$ids [] = $user->getIdentity ();
			}
		}
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$data = Zend_Json::encode ( $data );
		$this->getResponse ()->setBody ( $data );
	}
	/**
	 * Create step seven (publish campaign)
	 */
	public function createStepSevenAction()
	{
		// Check auth
		if (! $this->_helper->requireUser ()->isValid ()) {
			return;
		}
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$campaign_id = $this->_getParam ( 'campaignId' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		if (! $campaign || $campaign->published == 1) {
			return $this->_helper->requireAuth->forward ();
		}
		if (! $this->_helper->requireAuth ()->setAuthParams ( $campaign, $viewer, 'edit' )->isValid ()) {
			return;
		}

		$this->view->campaign_id = $campaign_id;
		// If not post or form not valid, return
		if (! $this->getRequest ()->isPost ()) {
			return;
		}
		// Process
		$table = Engine_Api::_ ()->getDbTable ( 'campaigns', 'ynfundraising' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();

		try {
			// publish campaign
			$campaign->status = Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS;
			$campaign->published = 1;
			$campaign->save ();

			$params = $campaign->toArray ();

			// if ideabox is available -> send email to other requesters
			if(Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin () && $campaign->parent_type != 'user')
			{
				$objParent = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getItemFromType ( $params );
				$params ['parent_name'] = $objParent->title;
				$params ['campaign_owner'] = $viewer->getTitle ();
				$params ['parent_owner'] = $objParent->getOwner ()->getTitle ();
				$requests = Engine_Api::_ ()->ynfundraising ()->getRequestPaginator ( array (
						'parent_id' => $campaign->parent_id,
						'parent_type' => $campaign->parent_type
				) );
				foreach ( $requests as $request ) {
					if ($request->is_completed == 0) {
						$user = Engine_Api::_ ()->getItem ( 'user', $request->requester_id );
						if ($user->getIdentity () && $user->getIdentity () != $viewer->getIdentity ()) {
							$sendTo = $user->email;
							Engine_Api::_ ()->getApi ( 'mail', 'ynfundraising' )->send ( $sendTo, 'fundraising_createCampaignToOtherRequester', $params );
						}
						if ($request->status == Ynfundraising_Plugin_Constants::REQUEST_WAITING_STATUS) {
							$request->delete ();
						} else {
							$request->is_completed = 1;
							$request->save ();
						}
					}
				}
			}

			$db->commit ();

			$action = @Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $viewer, $campaign, 'ynfundraising_new' );
			if ($action != null) {
				Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->attachActivity ( $action, $campaign );
			}

			// send mail to owner campaign
			$sendTo = $viewer->email;
			$params ['social_site'] = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'core.general.site.title', 'My Community' );
			Engine_Api::_ ()->getApi ( 'mail', 'ynfundraising' )->send ( $sendTo, 'fundraising_createCampaignToRequester', $params );
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		return $this->_helper->redirector->gotoRoute ( array (
				"controller" => "campaign",
				"action" => "index"
		), 'ynfundraising_extended', true );
	}
	/**
	 * Edit campaign information
	 *
	 */
	public function editStepOneAction() {
		// Check auth
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$campaign_id = $this->_getParam ( 'campaignId' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		if (! $campaign)
		{
			return $this->_helper->requireAuth->forward ();
		}
		if(!$this->_helper->requireAuth()->setAuthParams($campaign, $viewer, 'edit')->isValid()) return;

		$predefinedLists = explode(',', $campaign->predefined);
		$this->view->campaign_id = $campaign_id;
		$this->view->form = $form = new Ynfundraising_Form_CreateStepOne(array('predefinedLists'=>$predefinedLists));
		$form->removeElement ( "thumbnail" );
		//$form->removeElement ( 'currency' );
		if($campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS) {
			$form->currency->setAttrib('disabled','disabled');
		}

		$form->setTitle ( $this->view->translate ( "Edit Campaign" ) );
		$form->submit->setLabel ( $this->view->translate ( "Save Changes" ) );
		$form->populate ( $campaign->toArray () );

		//show tags
		$tagStr = '';
	    foreach( $campaign->tags()->getTagMaps() as $tagMap ) {
	      $tag = $tagMap->getTag();
	      if( !isset($tag->text) ) continue;
	      if( '' !== $tagStr ) $tagStr .= ', ';
	      $tagStr .= $tag->text;
	    }
	    $form->populate(array(
	      'tags' => $tagStr,
	    ));
		// Convert times
		$expiry_date = strtotime($campaign->expiry_date);
	    $oldTz = date_default_timezone_get();
	    date_default_timezone_set($viewer->timezone);
		$expiry_date = date('Y-m-d H:i:s', $expiry_date);
	    date_default_timezone_set($oldTz);

		$form->populate(array(
	        'expiry_date' => $expiry_date,
	      ));

		$auth = Engine_Api::_()->authorization()->context;
    	$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

		foreach( $roles as $role ) {
	      if ($form->auth_view){
	        if( $auth->isAllowed($campaign, $role, 'view') ) {
	         $form->auth_view->setValue($role);
	        }
	      }

	      if ($form->auth_comment){
	        if( $auth->isAllowed($campaign, $role, 'comment') ) {
	          $form->auth_comment->setValue($role);
	        }
	      }
	      if ($form->auth_donate){
	        if( $auth->isAllowed($campaign, $role, 'donate') ) {
	          $form->auth_donate->setValue($role);
	        }
	      }
	    }

		// If not post or form not valid, return
		if (! $this->getRequest ()->isPost ()) {
			return;
		}

		$post = $this->getRequest ()->getPost ();
		if (! $form->isValid ( $post ))
			return;

			// Process
		$table = Engine_Api::_ ()->getDbTable ( 'campaigns', 'ynfundraising' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();

		try {
			// Create campaign
			$values = $form->getValues ();
			if($campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS) {
				$values['currency'] = $campaign->currency;
			}
			$values ['modified_date'] = date ( 'Y-m-d H:i:s' );
			// $values['predefined'] = implode(',', $post['predefined']);
			if(strtotime($values['expiry_date']) > 0)
			{
				// Convert times
			    $oldTz = date_default_timezone_get();
			    date_default_timezone_set($viewer->timezone);
				$now = strtotime(date('Y-m-d H:i:s'));
				$expiry_date = strtotime($values['expiry_date']);
			    date_default_timezone_set($oldTz);
			    $values['expiry_date'] = date('Y-m-d H:i:s', $expiry_date);

				if($expiry_date <= $now)
			      {
			          $form->getElement('expiry_date')->addError('Expiry Date should be greater than Current Time!');
			          return;
			      }
			}
			else
				{
					$values['expiry_date'] = "0000-00-00 00:00:00";
				}
			$arr_predefined = array();
			foreach ($values ['predefined'] as $predefined) {
				if ($predefined) {
					$arr_predefined[] = (float)$predefined;
				}
			}

			if($values['minimum_donate'] > $values['goal'])
		      {
		          $form->getElement('goal')->addError('Fundraising Goal should be greater than Minimum Donation Amount!');
		          return;
		      }

			$values ['predefined'] = implode ( ',', $arr_predefined );
			$campaign->setFromArray ( $values );
			$campaign->save ();

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

			  if( empty($values['auth_donate']) ) {
                $values['auth_vote'] = 'everyone';
              }

              $viewMax = array_search($values['auth_view'], $roles);
              $commentMax = array_search($values['auth_comment'], $roles);
              $editMax = array_search($values['auth_edit'], $roles);
              $deleteMax = array_search($values['auth_delete'], $roles);
			  $voteMax = array_search($values['auth_donate'], $roles);

              foreach( $roles as $i => $role ) {
                $auth->setAllowed($campaign, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($campaign, $role, 'comment', ($i <= $commentMax));
                $auth->setAllowed($campaign, $role, 'edit', ($i <= $editMax));
                $auth->setAllowed($campaign, $role, 'delete', ($i <= $deleteMax));
				$auth->setAllowed($campaign, $role, 'donate', ($i <= $voteMax));
              }
			  // handle tags
		      $tags = preg_split('/[,]+/', $values['tags']);
		      $campaign->tags()->setTagMaps($viewer, $tags);

			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		return $this->_helper->redirector->gotoRoute ( array (
				"action" => "create-step-two",
				'campaignId' => $campaign->campaign_id
		), 'ynfundraising_general', true );
	}
	/**
	 *  send mail when campaign owner close campaign
	 *
	 */
	public function sendAction()
	{
		// In smoothbox
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$this->_helper->layout->setLayout ( 'default-simple' );
		$campaign_id = $this->_getParam ( 'campaignId' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		if(!$campaign)
		{
			return $this->_helper->requireAuth->forward ();
		}
		if(!$campaign->isOwner($viewer) || $campaign->status != Ynfundraising_Plugin_Constants::CAMPAIGN_CLOSED_STATUS) {
			return $this->_helper->requireAuth->forward ();
		}
		$this->view->form = $form = new Ynfundraising_Form_SendCampaignDonor ();

		if (!$this->getRequest ()->isPost ()) {
			return;
		}

		$post = $this->getRequest ()->getPost ();

		if(isset($post['submit']))
			return;
		if(isset($post['submit_']))
		{
			if (! $form->isValid ( $post )) {
				return;
			}

			// minhnc - Send mail to donors
			$values = array("campaign" => $campaign_id);
			$donors = Engine_Api::_()->getApi('core', 'ynfundraising')->getDonorPaginator($values);

			$params = $campaign->toArray();
			$params['campaign_owner'] = $campaign->getOwner()->getTitle();
			if (count($donors) > 0) {
				foreach ($donors as $donor)
				{
					$user = Engine_Api::_ ()->getItem ( 'user', $donor->user_id );
					if($user->getIdentity() > 0)
					{
						$sendTo = $user->email;
						Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_campaignClosedToDonor',$params);
					}
					elseif($donor->guest_email) {
						$sendTo = $donor->guest_email;
						Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_campaignClosedToDonor',$params);

					}
				}
			}

		}
		if(isset($post['submit_']))
		{
			return $this->_forward ( 'success', 'utility', 'core', array (
					'messages' => array (
							Zend_Registry::get ( 'Zend_Translate' )->_ ( 'Your message will be sent to all donors. The campaign has been closed successfully.' )
					),
					'layout' => 'default-simple',
					'smoothboxClose' => true,
					'parentRefresh' => true
			) );
		}
		else
		{
			return $this->_forward ( 'success', 'utility', 'core', array (
					'messages' => array (
							Zend_Registry::get ( 'Zend_Translate' )->_ ( 'Your campaign has been closed successfully.' )
					),
					'layout' => 'default-simple',
					'smoothboxClose' => true,
					'parentRefresh' => true
			) );
		}
	}
	/**
	 * Campaign owner close campaign
	 *
	 */
	public function closeAction()
	{
		// check auth
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $this->_helper->requireAuth ()->setAuthParams ( 'ynfundraising_campaign', null, 'close' )->isValid ()) {
			return;
		}
		$campaign_id = $this->_getParam ( 'campaignId' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		if (! $campaign || ! $campaign->isOwner ( $viewer )) {
			return $this->_helper->requireAuth->forward ();
		}
		if ($campaign->status != Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS && $campaign->status != Ynfundraising_Plugin_Constants::CAMPAIGN_DRAFT_STATUS) {
			return $this->_helper->requireAuth->forward ();
		}
		// In smoothbox
		$this->_helper->layout->setLayout ( 'default-simple' );
		$form = new Ynfundraising_Form_CloseCampaign ();
		// check if campaign status is ongoing
		$is_ongoing = true;
		if ($campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_DRAFT_STATUS) {
			$is_ongoing = false;
		}
		if (!$is_ongoing) {
			$form->setTitle ( 'Delete Campaign' )
			->setDescription ( 'Are you sure you want to delete this campaign?' );
			$form->submit->setLabel('Delete Campaign');
		}
		$this->view->form = $form;

		if (! $this->getRequest ()->isPost ()) {
			return;
		}

		$db = Engine_Db_Table::getDefaultAdapter ();
		$db->beginTransaction ();
		try {
			if (isset ( $campaign->request_id ) && $campaign->request_id != 0) {
				$request = Engine_Api::_ ()->getItem ( 'ynfundraising_request', $campaign->request_id );
				if ($request) {
					$request->delete ();
				}
			}
			if (!$is_ongoing) {
				$campaign->delete ();
			} else {
				$campaign->status = Ynfundraising_Plugin_Constants::CAMPAIGN_CLOSED_STATUS;
				$campaign->save ();
			}

			$values = array (
					'parent_id' => $campaign->parent_id,
					'parent_type' => $campaign->parent_type,
					'is_completed' => 0
			);

			if (Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ()) {
				$other_request_result = Engine_Api::_ ()->ynfundraising ()->getRequestPaginator ( $values );
				foreach ( $other_request_result as $other_request ) {
					$other_request->visible = 1;
					$other_request->save ();
				}
			}

			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}

		// send notification to follower if campaign status is ongoing and return to Send form
		if ($is_ongoing) {
			$follows = Engine_Api::_ ()->ynfundraising ()->getFollowers ( $campaign->getIdentity () );
			$notificationTbl = Engine_Api::_ ()->getDbtable ( 'notifications', 'activity' );
			if (count($follows) > 0) {
				foreach ( $follows as $follow ) {
					$notificationTbl->addNotification ( $follow->getOwner (), $campaign->getOwner (), $campaign, 'ynfundraising_notify_close' );
				}
			}
			return $this->_forward ( 'send', null, null, array (
					'campaignId' => $campaign_id
			) );
		}
		return $this->_forward ( 'success', 'utility', 'core', array (
				'messages' => array (
						Zend_Registry::get ( 'Zend_Translate' )->_ ( 'Your campaign has been deleted successfully.' )
				),
				'layout' => 'default-simple',
				'smoothboxClose' => true,
				'parentRefresh' => true
		) );
	}
	/**
	 * Cancel request create campaign
	 */
	public function cancelRequestAction() {
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		if(!Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ()) {
			return $this->_helper->requireAuth->forward ();
		}
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$this->_helper->layout->setLayout ( 'default-simple' );
		$this->view->form = $form = new Ynfundraising_Form_CancelRequest ();
		$request_id = $this->_getParam ( 'request_id' );
		$request = Engine_Api::_ ()->getItem ( 'ynfundraising_request', $request_id );
		if (! $request || $request->requester_id != $viewer->getIdentity ()) {
			return $this->_helper->requireAuth->forward ();
		}

		if (! $this->getRequest ()->isPost ()) {
			return;
		}
		$table = Engine_Api::_ ()->getDbTable ( 'requests', 'ynfundraising' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();

		try {
			$request->delete ();
			$db->commit();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		$router = $this->getFrontController ()->getRouter ();
		$this->_forward ( 'success', 'utility', 'core', array (
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array (
						$this->view->translate ( 'Your request is cancelled successfully.' )
				)
		) );
	}
	/**
	 * View detail campaign
	 */
	public function viewAction() {
		$date = date ( 'Y-m-d H:i:s' );
		$subject = null;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();

		if (! Engine_Api::_ ()->core ()->hasSubject ())
		{
			$id = $this->_getParam ( 'campaignId' );
			if (null !== $id)
			{
				$subject = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $id );

				if ($subject && $subject->getIdentity () && $subject->published == '1') {
					Engine_Api::_ ()->core ()->setSubject ( $subject );
				} elseif ($subject && $subject->isOwner ( $viewer )) {
					Engine_Api::_ ()->core ()->setSubject ( $subject );
				} else {
					return $this->_helper->requireAuth->forward ();
				}
			}
		}
		$subject = Engine_Api::_ ()->core ()->getSubject ();
		
		//hide campaign with inactivated without owner
		if(!$subject->isOwner ( $viewer ) && empty($subject->activated))
			 return $this->_helper->requireAuth->forward ();
		//check auth view
		if(!$this->_helper->requireAuth()->setAuthParams($subject, $viewer, 'view')->isValid()) return;

		$this->_helper->requireSubject ( 'ynfundraising_campaign' );

		//check expired and send mail
		// Convert times
		$expiry_date = $subject->expiry_date;
		$now = date('Y-m-d H:i:s');
		$campaign = $subject;
		if($expiry_date <= $now && $expiry_date != '0000-00-00 00:00:00' && $expiry_date != '1970-01-01 00:00:00' && $campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS)
	    {

			$campaign->status = Ynfundraising_Plugin_Constants::CAMPAIGN_EXPIRED_STATUS;
		 	$campaign->save();

			$notificationTbl = Engine_Api::_ ()->getDbtable ( 'notifications', 'activity' );
			$follows = Engine_Api::_ ()->ynfundraising ()->getFollowers ( $campaign->getIdentity () );
			if (count($follows) > 0) {
				foreach ( $follows as $follow )
					{
						$notificationTbl->addNotification ( $follow->getOwner (), $campaign->getOwner (), $campaign, 'ynfundraising_notify_expired', array () );
					}
			}

	        // Start send mail
			// minhnc - Send mail to owner
			$params = $campaign->toArray();
			$sendTo = $campaign->getOwner()->email;
			Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_campaignExpiredToOwner',$params);

			//minhnc - send mail to trophy/idea owner
			if ($campaign->parent_type != 'user') {
				$parent_obj = Engine_Api::_()->getApi('core', 'ynfundraising')->getItemFromType($subject->toArray());
				if ($parent_obj && $parent_obj->getOwner()->getIdentity() > 0) {
					$sendTo = $parent_obj->getOwner()->email;
					Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_campaignExpiredToParent',$params);
				}
			}
			// minhnc - Send mail to donors
			$values = array("campaign" => $campaign->getIdentity());
			$donors = Engine_Api::_()->getApi('core', 'ynfundraising')->getDonorPaginator($values);
			if (count($donors) > 0) {
				foreach ($donors as $donor)
				{
					$user = Engine_Api::_ ()->getItem ( 'user', $donor->user_id );
					if($user->getIdentity() > 0)
					{
						$sendTo = $user->email;
						Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_campaignExpiredToDonor',$params);
					}
					elseif($donor->guest_email) {
						$sendTo = $donor->guest_email;
						Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_campaignExpiredToDonor',$params);

					}
				}
			}
			//end send mail
	    }

		if($subject->status == Ynfundraising_Plugin_Constants::CAMPAIGN_DRAFT_STATUS && !$subject->isOwner( $viewer ) && !$parent_obj->isOwner($viewer))
		{
			return $this->_helper->requireAuth->forward ();
		}

		if (! $subject->getOwner ()->isSelf ( $viewer )) {
			$subject->view_count ++;
			$subject->save ();
		}
		// Render
		$this->_helper->content->setNoRender ()->setEnabled ();
	}

	public function listAction() {
		// Prepare data
		$form = new Ynfundraising_Form_CampaignSearch ();

		// Process form
		$form->isValid ( $this->_getAllParams () );

		$values = $form->getValues ();
		$this->view->formValues = array_filter ( $values );

		$values ['published'] = 1;
		// Get campaign paginator
		$items_count = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.page', 10 );
		$paginator = Engine_Api::_ ()->ynfundraising ()->getCampaignPaginator ( $values );
		$paginator->setItemCountPerPage ( $items_count );
		$this->view->paginator = $paginator;

		// render
		$this->_helper->content->setEnabled ();
	}
	/**
	 *  past campaign page
	 *
	 */
	public function pastCampaignsAction() {
		// Prepare data
		$form = new Ynfundraising_Form_CampaignSearch ();
		// $form->setAction($this->view->url(array(),'default')."fundraising/past-campaigns");

		// Process form
		$form->isValid ( $this->_getAllParams () );
		$values = $form->getValues ();
		$this->view->formValues = array_filter ( $values );
		$values ['published'] = 1;
		$values ['past'] = 1;
		// Get campaign paginator
		$items_count = ( int ) Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.page', 10 );
		$paginator = Engine_Api::_ ()->ynfundraising ()->getCampaignPaginator ( $values );
		$paginator->setItemCountPerPage ( $items_count );
		$this->view->paginator = $paginator;
		// render
		$this->_helper->content->setEnabled ();
	}
	/**
	 * Rating campaign
	 */
	public function campaignRateAction()
	{
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( TRUE );

		if (! $this->_helper->requireUser ()->isValid ())
			return;

		$campaign_id = ( int ) $this->_getParam ( 'campaignId' );
		$rates = ( int ) $this->_getParam ( 'rates' );
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );

		$can_rate = false;
		if(($viewer->getIdentity() != $campaign->getOwner()->getIdentity()) || ($viewer->getIdentity() == $campaign->getOwner()->getIdentity() && Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfundraising.rate', 0)))
			$can_rate = Engine_Api::_()->getApi('core', 'ynfundraising')->checkCampaignRating($campaign->getIdentity(),$viewer->getIdentity());

		if ($rates == 0 || $campaign_id == 0 || !$can_rate) {
			return;
		}

		$rateTable = Engine_Api::_ ()->getDbtable ( 'campaignRatings', 'ynfundraising' );
		$db = $rateTable->getAdapter ();
		$db->beginTransaction ();
		try {
			$rate = $rateTable->createRow ();
			$rate->poster_id = $viewer->getIdentity ();
			$rate->campaign_id = $campaign_id;
			$rate->rate_number = $rates;
			$rate->save ();
			// Commit
			$db->commit ();
		}

		catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		return $this->_redirect ( "fundraising/view/campaignId/" . $campaign_id . '#ratecampaign' );
	}
	/**
	 * Manage requests
	 *
	 */
	public function manageRequestsAction()
	{
		// Check authoraiztion permisstion
		if (! $this->_helper->requireUser ()->isValid ()) {
			return;
		}
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if( !Engine_Api::_()->authorization()->isAllowed('ynidea_idea', $viewer, 'create') && !Engine_Api::_()->authorization()->isAllowed('ynidea_trophy', $viewer, 'create'))
		{
	      return;
	    }

		// Prepare data
		$form = new Ynfundraising_Form_RequestSearch ();

		// Process form
		$form->isValid ( $this->_getAllParams () );
		$values = $form->getValues ();
		$this->view->formValues = array_filter ( $values );
		// to check ideas, trophies from viewer
		$values ['user_id'] = $viewer->getIdentity ();
		$values['is_manage'] = true;
		// Get campaign paginator
		$items_count = ( int ) Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.page', 10 );
		$paginator = Engine_Api::_ ()->ynfundraising ()->getRequestPaginator ( $values );
		$paginator->setItemCountPerPage ( $items_count );
		$this->view->paginator = $paginator;
		// render
		$this->_helper->content->setEnabled ();
	}
	/**
	 * Invite friends action
	 *
	 */
	public function inviteFriendsAction()
	{
		// Check auth
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$campaign_id = $this->_getParam ( 'campaignId' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );

		if (! $campaign) {
			return $this->_helper->requireAuth->forward ();
		}

		$this->view->form = $form = new Ynfundraising_Form_CreateStepSix ();
		$form->setAttrib('class','global_form_popup');
		$form->cancel->setAttrib ('onclick','javascript:parent.Smoothbox.close();');
		$form->cancel->setAttrib ('href','');
		$this->view->campaign_id = $campaign_id;
		// If not post or form not valid, return
		if (! $this->getRequest ()->isPost ()) {
			return;
		}

		$post = $this->getRequest ()->getPost ();
		if (! $form->isValid ( $post ))
			return;

		// Process send invte
		$values = $form->getValues();
		$string_recipients = $values['recipients'];
		$arr_recipients = explode(',', $string_recipients);
		$toUsers = explode(',',$values['toValues']);
		$message = $values['custom_message'];

		$params = $campaign->toArray();
		$params['inviter_name'] = $viewer->getTitle();
		$params['message'] = $message;
		//send invite to friend on se site
		foreach($toUsers as $user_id)
		{
			$user = Engine_Api::_ ()->getItem ( 'user', $user_id );
			if($user->getIdentity())
			{
				$sendTo = $user->email;
			    Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_inviteFriends',$params);
			}
		}
		$pattern = '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
		//send invite with email
		foreach($arr_recipients as $email)
		{
			if(preg_match($pattern, $email))
			{
				$sendTo = $email;
			    Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_inviteFriends',$params);
			}
		}
		$this->_forward ( 'success', 'utility', 'core', array (
				'smoothboxClose' => true,
				'parentRefresh' => false,
				'format' => 'smoothbox',
				'messages' => array (
						$this->view->translate ( 'Invite friends successfully.' )
				)
		) );
	}
    /**
	 * Idea/trophy close campaign
	 *
	 */
	public function ownerCloseAction()
	{
		// In smoothbox
		$this->_helper->layout->setLayout ( 'default-simple' );
		$campaign_id = $this->_getParam ( 'campaignId' );
		$this->view->form = $form = new Ynfundraising_Form_OwnerCloseCampaign ();
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );

		if(!$campaign)
		{
			return $this->_helper->requireAuth->forward ();
		}
		$flag = false;
		if($campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS)
		{
				if(($viewer->isAdmin() && Engine_Api::_()->authorization()->isAllowed('ynfundraising_campaign', $viewer, 'close')))
				{
					$flag = true;
				}
				else {
					if ($campaign->parent_type != 'user') {
						$parent = Engine_Api::_()->getApi('core', 'ynfundraising')->getItemFromType($campaign->toArray());
						if ($parent->isOwner($viewer)) {
							$flag = true;
					}
				}
			}
		}

		if(!$flag)
			return $this->_helper->requireAuth->forward ();

		if (!$this->getRequest ()->isPost ()) {
			return;
		}

		$post = $this->getRequest ()->getPost ();

		if (! $form->isValid ( $post )) {
			return;
		}

		$db = Engine_Db_Table::getDefaultAdapter ();
		$db->beginTransaction ();

		try {
			$campaign->status = Ynfundraising_Plugin_Constants::CAMPAIGN_CLOSED_STATUS;
			$campaign->save ();

			// send notification to follower
			$follows = Engine_Api::_()->ynfundraising()->getFollowers($campaign->getIdentity());
			$notificationTbl = Engine_Api::_ ()->getDbtable ( 'notifications', 'activity' );

			if (count($follows) > 0) {
				foreach ($follows as $follow) {
					$notificationTbl->addNotification ( $follow->getOwner(), $campaign->getOwner(), $campaign, 'ynfundraising_notify_close');
				}
			}

			$db->commit ();

			// Start send mail
			// minhnc - Send mail to owner
			$params = $campaign->toArray();
			$params['reason'] = $post['reason'];
			if($campaign->getOwner()->getIdentity() > 0)
			{
				$sendTo = $campaign->getOwner()->email;
				Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_campaignClosedToOwner',$params);
			}

			if ($campaign->parent_type != 'user') {
				//minhnc - send mail to trophy/idea owner
				$parent_obj = Engine_Api::_()->getApi('core', 'ynfundraising')->getItemFromType($campaign->toArray());
				if($parent_obj && $parent_obj->getOwner()->getIdentity() > 0)
				{
					$sendTo = $parent_obj->getOwner()->email;
					Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_campaignClosedToParent',$params);
				}
			}

			// minhnc - Send mail to donors
			$values = array("campaign" => $campaign->getIdentity());
			$donors = Engine_Api::_()->getApi('core', 'ynfundraising')->getDonorPaginator($values);

			$params['campaign_owner'] = $campaign->getOwner()->getTitle();
			if (count($donors) > 0) {
				foreach ($donors as $donor)
				{
					$user = Engine_Api::_ ()->getItem ( 'user', $donor->user_id );
					if($user->getIdentity() > 0)
					{
						$sendTo = $user->email;
						Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_campaignClosedToDonor',$params);
					}
					elseif($donor->guest_email)
					{
						$sendTo = $donor->guest_email;
						Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_campaignClosedToDonor',$params);
					}
				}
			}
			//end send mail

		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}


		return $this->_forward ( 'success', 'utility', 'core', array (
				'messages' => array (
						Zend_Registry::get ( 'Zend_Translate' )->_ ( 'The campaign has been closed successfully.' )
				),
				'layout' => 'default-simple',
				'smoothboxClose' => true,
				'parentRefresh' => true
		) );

	}
	/**
	 * email to donors
	 *
	 */
	 public function emailDonorsAction()
	 {
	 	// In smoothbox
		$this->_helper->layout->setLayout ( 'default-simple' );
		$campaign_id = $this->_getParam ( 'campaignId' );
		$this->view->form = $form = new Ynfundraising_Form_EmailToDonors();
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		if(!$campaign || !$campaign->isOwner($viewer))
		{
			return $this->_helper->requireAuth->forward ();
		}
		if (!$this->getRequest ()->isPost ()) {
			return;
		}

		$post = $this->getRequest ()->getPost ();

		if (! $form->isValid ( $post )) {
			return;
		}
		$params = $form->getValues();
		// minhnc - Send mail to donors
		$values = array("campaign" => $campaign->getIdentity());
		$donors = Engine_Api::_()->getApi('core', 'ynfundraising')->getDonorPaginator($values);

		if (count($donors) > 0) {
			foreach ($donors as $donor)
			{
				$user = Engine_Api::_ ()->getItem ( 'user', $donor->user_id );
				if($user)
				{
					$sendTo = $user->email;
					Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_emailToDonors',$params);
				}
				elseif($donor->guest_email) {
					$sendTo = $donor->guest_email;
					Engine_Api::_()->getApi('mail','ynfundraising')->send($sendTo, 'fundraising_emailToDonors',$params);

				}
			}
		}
		return $this->_forward ( 'success', 'utility', 'core', array (
				'messages' => array (
						Zend_Registry::get ( 'Zend_Translate' )->_ ( 'Send mail successfully.' )
				),
				'layout' => 'default-simple',
				'smoothboxClose' => true,
				'parentRefresh' => false
		) );

	 }
	/**
	 *
	 * Promote campaign
	 *
	 */
	 public function promoteAction()
	 {
	 	// In smoothbox
		$this->_helper->layout->setLayout ( 'default-simple' );
		$campaign_id = $this->_getParam ( 'campaignId' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );

		if(!$campaign)
		{
			return $this->_helper->requireAuth->forward ();
		}
		$this->view->campaign = $campaign;
		$values = array("campaign" => $campaign->getIdentity(), 'limit' => 5);
		$this->view->donors = $donors = Engine_Api::_()->getApi('core', 'ynfundraising')->getDonorPaginator($values);
		$this->view->goal = $goal =  $campaign->goal;
		$this->view->total_amount = $amount =  $campaign->total_amount?$campaign->total_amount:'0';
		$percent  = ($goal!=0)?round(($amount*100)/$goal,2):'0';
		$this->view->percent = ($percent>100)?100:$percent;
	 }

	 /**
	 *
	 * campaign badge
	 *
	 */
	 public function campaignBadgeAction()
	 {
	 	// In smoothbox
		$this->_helper->layout->disableLayout();
		$campaign_id = $this->_getParam ( 'campaignId' );
		$this->view->status = $status = $this->_getParam ( 'status' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );

		if(!$campaign)
		{
			return $this->_helper->requireAuth->forward ();
		}
		$this->view->campaign = $campaign;
		$values = array("campaign" => $campaign->getIdentity(), 'limit' => 5);
		$this->view->donors = $donors = Engine_Api::_()->getApi('core', 'ynfundraising')->getDonorPaginator($values);
		$this->view->goal = $goal =  $campaign->goal;
		$this->view->total_amount = $amount =  $campaign->total_amount?$campaign->total_amount:'0';
		$percent  = ($goal!=0)?round(($amount*100)/$goal,2):'0';
		$this->view->percent = ($percent>100)?100:$percent;
	 }
}
