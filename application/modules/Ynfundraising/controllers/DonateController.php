<?php
class Ynfundraising_DonateController extends Core_Controller_Action_Standard {
	/**
	 * init check exist Ynidea plugin enable
	 */
	public function init() {
		/*
		 * Campaign is not created from idea/trophy also allows to donate
		if (! Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ()) {
			return $this->_helper->requireAuth->forward ();
		}
		*/
	}
	public function indexAction() {
		$values = $this->_getAllParams ();
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $values ['campaign_id'] );
		// check if campaign is not exist or status <> ongoing
		if (! $campaign || $campaign->status != Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS) {
			return $this->_helper->requireAuth->forward ();
		}

		$viewer = Engine_Api::_ ()->user ()->getViewer ();

		if (! $this->_helper->requireAuth ()->setAuthParams ( $campaign, $viewer, 'donate' )->isValid ()) {
			return;
		}

		/*
		 * Check allow_anonymous on campaign to show is_anonymous field
		 */
		$allow_anonymous = $campaign->allow_anonymous;

		// Get navigation
		$this->view->form = $form = new Ynfundraising_Form_DonateCampaign ();

		// need to check if number is decimal
		if (! empty ( $values ['sponsor'] ) && is_numeric ( $values ['sponsor'] )) {
			$populate_values = array_merge ( $campaign->toArray (), array (
					'amount' => $values ['sponsor']
			) );
		} else {
			$populate_values = $campaign->toArray ();
		}
		$form->populate ( $populate_values );
		$this->view->campaign = $campaign;

		$baseUrl = Ynfundraising_Plugin_Utilities::getBaseUrl ();
		$router = $this->getFrontController ()->getRouter ();

		if (! $this->getRequest ()->isPost ()) {
			return;
		}

		if (! $form->isValid ( $this->getRequest ()->getPost () )) {
			return;
		}

		$view = Zend_Registry::get ( 'Zend_View' );
		$values = $form->getValues ();

		if ($values ['amount'] < $campaign->minimum_donate) {
			$form->addError ( Zend_Registry::get ( 'Zend_Translate' )->_ ( "Your donation must be a number and at least " ) . $view->currencyfund( $campaign->minimum_donate, $campaign->currency ) );
			return;
		}
		if (isset ( $values ['is_agreed'] ) && $values ['is_agreed'] != 1) {
			$form->addError ( Zend_Registry::get ( 'Zend_Translate' )->_ ( "You must agree to the terms and conditions to proceed." ) );
			return;
		}

		// create donation
		$donationTbl = new Ynfundraising_Model_DbTable_Donations ();
		$db = $donationTbl->getAdapter ();
		$db->beginTransaction ();

		try {
			$donation = $donationTbl->createRow ();
			$donation->campaign_id = $campaign->getIdentity ();
			$donation->user_id = $viewer->getIdentity ();
			$donation->message = $values ['message'];
			$donation->currency = $campaign->currency;
			$donation->amount = $values ['amount'];

			$donation->donation_date = date ( 'Y-m-d H:i:s' );
			if (isset ( $values ['is_anonymous'] )) {
				$donation->is_anonymous = $values ['is_anonymous'];
			}
			if (isset ( $values ['guest_email'] )) {
				$donation->guest_email = $values ['guest_email'];
			}
			if (isset ( $values ['guest_name'] )) {
				$donation->guest_name = $values ['guest_name'];
			}
			$donation->save ();
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}

		return $this->_forward ( 'summary', null, null, array (
				'donation_id' => $donation->getIdentity (),
				'campaign_id' => $campaign->getIdentity (),
				'is_agreed' => 1
		) );
	}
	public function summaryAction() {
		$this->view->params = $this->getRequest ()->getPost ();

		$campaignId = $this->_getParam ( 'campaign_id', 0 );
		$this->view->is_agreed = $is_agreed = $this->_getParam ( 'is_agreed', 0 );

		if ($campaignId) {
			$this->view->campaign = $campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaignId );
		}

		$donationId = $this->_getParam ( 'donation_id', 0 );
		if ($donationId) {
			$this->view->donation = $donation = Engine_Api::_ ()->getItem ( 'ynfundraising_donation', $donationId );
		}

		if (isset ( $campaign ) && is_object ( $campaign ) && isset ( $donation ) && is_object ( $donation )) {
			// $gateway = Engine_Api::_ ()->getDbtable ( 'gateways',
			// 'yndonation' )->find ( 'paypal' )->current ();
			$this->view->formUrl = $formUrl = Ynfundraising_Plugin_Utilities::getPaymentUrl ();
			$baseUrl = Ynfundraising_Plugin_Utilities::getBaseUrl ();
			$router = $this->getFrontController ()->getRouter ();

			$returnUrl = $baseUrl . $router->assemble ( array (
					'action' => 'view',
					'campaignId' => $campaign->getIdentity ()
			), 'ynfundraising_general', true );

			$notifyUrl = $baseUrl . $router->assemble ( array (
					'controller' => 'donate',
					'action' => 'notify',
					'donation_id' => $donation->getIdentity ()
			), 'ynfundraising_extended', true );

			// print_r ( $notifyUrl );
			/*
			 * note: cancel_url is used in Summary page to return to Campaign
			 * detail page return_url is sent to Paypal as a return URL after
			 * successful donation
			 */

			$this->view->configRequest = array (
					'item_name' => 'Donation for campaign ' . $campaign->title,
					'account_username' => $campaign->paypal_account,
					'amount' => $donation->amount,
					'currency' => $campaign->currency,
					'notify_url' => $notifyUrl,
					'return_url' => $returnUrl,
					'cancel_url' => $returnUrl
			);
		}
	}
	public function notifyAction() {
		$params = $this->_getAllParams ();
		// Zend_Registry::get ( 'Zend_Log' )->log ( "sfsfsfsfsdf",
		// Zend_Log::DEBUG );

		// fake data
		/*
		 * $params ['payment_gross'] = 1; $params ['mc_currency'] = 'USD';
		 * $params ['payment_status'] =
		 * Ynfundraising_Plugin_Constants::PAYPAL_PAYMENT_SUCCESS; $params
		 * ['payer_email'] = 'test@gmail.com'; $params ['payer_status'] =
		 * 'Completed'; $params ['txn_id'] = 'AGEDFGHRT';
		 */
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$donationId = $params ['donation_id'];
		$donation = Engine_Api::_ ()->getItem ( 'ynfundraising_donation', $donationId );

		if (! $donation || ($donation->status == 1)) {
			return;
		}

		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $donation->campaign_id );

		// check if campaign is not exist or status <> ongoing
		if (! $campaign || $campaign->status != Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS) {
			return;
		}
		$is_reached = false;

		if ($params ['payment_status'] == Ynfundraising_Plugin_Constants::PAYPAL_PAYMENT_SUCCESS
			&& ($donation->amount == $params['payment_gross'] || $donation->amount == $params['mc_gross'])
			&& ($donation->currency == $params['mc_currency'])) {

			if (isset ( $campaign ) && is_object ( $campaign )
				&& $params ['receiver_email'] == $campaign->paypal_account
				) {

				$db = Engine_Db_Table::getDefaultAdapter ();
				$db->beginTransaction ();
				try {
					// update donation
					if (isset($params['mc_fee'])) {
						$donation->commission_fee = $params['mc_fee'];
					}
					else {
						$donation->commission_fee = 0;
					}
					$donation->donation_date = date ( 'Y-m-d H:i:s' );
					$donation->payer_email = $params ['payer_email'];
					$donation->payer_status = $params ['payer_status'];
					$donation->transaction_id = $params ['txn_id'];
					$donation->status = 1;
					$donation->save ();

					// update amount of campaign
					$campaign->total_amount += $donation->amount;

					// check if campaign goal is reached
					if ($campaign->total_amount >= $campaign->goal) {
						$is_reached = true;
						$campaign->status = Ynfundraising_Plugin_Constants::CAMPAIGN_REACHED_STATUS;

						/*
						$request_values = array('parent_id' => $campaign->parent_id, 'parent_type' => $campaign->parent_type);
						$requests = Engine_Api::_ ()->ynfundraising ()->getRequestPaginator ( $request_values );
						foreach($requests as $request)
						{
							$request->is_completed = 1;
							$request->save();
						}
						*/
					}

					$campaign->save ();

					$db->commit ();
				} catch ( Exception $e ) {
					$db->rollBack ();
					throw $e;
				}

				/*
				 * --- SEND EMAIL ---
				 */
				// for user
				$donor = Engine_Api::_ ()->user ()->getUser ( $donation->user_id );
				$mailApi = Engine_Api::_ ()->getApi ( 'mail', 'ynfundraising' );
				$view = Zend_Registry::get ( 'Zend_View' );
				// amount with currency to add to activity feed
				$amount = $view->locale ()->toCurrency ( $donation->amount, $campaign->currency );

				// send email to donor
				$params_thanksDonor = array (
						'campaign_name' => $campaign->getTitle (),
						'campaign_owner' => $campaign->getOwner ()->getTitle (),
						'campaign_link' => $campaign->getHref (),
						'campaign_id' => $campaign->getIdentity (),
						'personal_message' => empty($campaign->email_message)?'':$campaign->email_message
				);

				// send email to campaign owner
				$params_updatingDonor = array (
						'donor_name' => $donor->getTitle (),
						'campaign_link' => $campaign->getHref ()
				);

				// fundraising_campaignGoalToOwner
				$params_campaignGoalToOwner = array (
						'campaign_link' => $campaign->getHref ()
				);

				// fundraising_campaignGoalToParent
				$params_campaignGoalToParent = array (
						'campaign_link' => $campaign->getHref ()
				);

				// fundraising_campaignGoalToDonor
				$params_campaignGoalToDonor = array (
						'campaign_link' => $campaign->getHref ()
				);

				/*
				 * If campaign goal is reached -- BEGIN --
				 */
				if ($is_reached) {
					// fundraising_campaignGoalToOwner
					$mailApi->send ( $campaign->getOwner ()->email, 'fundraising_campaignGoalToOwner', $params_campaignGoalToOwner );

					// send mail to parent of campaing if it is idea/trophy
					if ($campaign->parent_type != 'user') {
						// fundraising_campaignGoalToParent
						$parent = Engine_Api::_ ()->ynfundraising ()->getItemFromType ( $campaign->toArray () );
						if ($parent && $parent->getOwner()->getIdentity() > 0) {
							$mailApi->send ( $parent->getOwner ()->email, 'fundraising_campaignGoalToParent', $params_campaignGoalToParent );
						}
					}
					// fundraising_campaignGoalToDonor
					$values = array (
							"campaign" => $campaign->getIdentity ()
					);
					$donors = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getDonorPaginator ( $values );
					if (count($donors) > 0) {
						foreach ( $donors as $donor_goal ) {
							$user = Engine_Api::_ ()->getItem ( 'user', $donor_goal->user_id );
							if ($user->getIdentity () > 0) {
								$sendTo = $user->email;
								$mailApi->send ( $sendTo, 'fundraising_campaignGoalToDonor', $params_campaignGoalToDonor );
							} elseif ($donor_goal->guest_email) {
								$sendTo = $donor_goal->guest_email;
								$mailApi->send ( $sendTo, 'fundraising_campaignGoalToDonor', $params_campaignGoalToDonor );
							}
						}
					}
				}
				// -- END --
				$notificationTbl = Engine_Api::_ ()->getDbtable ( 'notifications', 'activity' );
				$guest_name = '';
				if ($donor->getIdentity () > 0) {
					// send email to donor
					$mailApi->send ( $donor->email, 'fundraising_thanksDonor', $params_thanksDonor );
					// send email to campaign owner
					if ($campaign->getOwner()->getIdentity() > 0) {
						$mailApi->send ( $campaign->getOwner ()->email, 'fundraising_updatingDonor', $params_updatingDonor );
					}

					// add to activity feed if not anonymous
					if (! $donation->is_anonymous) {
						// if do not leave a message
						if ($donation->message) {
							$action = @Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $donor, $campaign, 'ynfundraising_donate_msg', null, array (
									'amount' => $amount,
									'message' => $donation->message
							) );
						} else {
							// if has message
							$action = @Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $donor, $campaign, 'ynfundraising_donate', null, array (
									'amount' => $amount
							) );
						}
						// send notification to campaign owner
						$notificationTbl->addNotification ( $campaign->getOwner (), $donor, $campaign, 'ynfundraising_notify_donate', array (
								'amount' => $amount
						) );
					} else {
						if ($donation->message) {
							$action = @Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $campaign->getOwner (), $campaign, 'ynfundraising_anonymous_msg', null, array (
									'donor' => 'An anonymous donor',
									'amount' => $amount,
									'message' => $donation->message
							) );
						} else {
							$action = @Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $campaign->getOwner (), $campaign, 'ynfundraising_anonymous', null, array (
									'donor' => 'An anonymous donor',
									'amount' => $amount
							) );
						}
						// send notification to campaign owner
						$notificationTbl->addNotification ( $campaign->getOwner (), $donor, $campaign, 'ynfundraising_notify_invi', array (
								'donor' => 'An anonymous donor',
								'amount' => $amount
						) );
					}

					if ($action != null) {
						Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->attachActivity ( $action, $campaign );
					}
				} else {
					// for guest : send to guest email
					if ($donation->guest_email != '') {
						$mailApi->send ( $donation->guest_email, 'fundraising_thanksDonor', $params_thanksDonor );
					}
					// if not anonymous
					$guest_name = 'An anonymous donor';
					if (! $donation->is_anonymous) {
						$guest_name = 'A guest';
						if ($donation->guest_name != '') {
							$guest_name = $donation->guest_name;
						}
					}
					if ($donation->message) {
						$action = @Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $campaign->getOwner (), $campaign, 'ynfundraising_anonymous_msg', null, array (
								'donor' => $guest_name,
								'amount' => $amount,
								'message' => $donation->message
						) );
					} else {
						$action = @Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $campaign->getOwner (), $campaign, 'ynfundraising_anonymous', null, array (
								'donor' => $guest_name,
								'amount' => $amount
						) );
					}
					if ($action != null) {
						Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->attachActivity ( $action, $campaign );
					}

					// send notification to campaign owner
					$notificationTbl->addNotification ( $campaign->getOwner (), $campaign->getOwner (), $campaign, 'ynfundraising_notify_invi', array (
							'donor' => $guest_name,
							'amount' => $amount
					) );
				}

				// send notification to follower
				$follows = Engine_Api::_ ()->ynfundraising ()->getFollowers ( $campaign->getIdentity () );
				if (count($follows) > 0) {
					foreach ( $follows as $follow ) {
						if ($follow->user_id != $viewer->getIdentity ()) {
							// for donor
							if ($donor->getIdentity () > 0) {
								if (! $donation->is_anonymous) {
									// send notification to campaign owner
									$notificationTbl->addNotification ( $follow->getOwner (), $donor, $campaign, 'ynfundraising_notify_donate', array (
											'amount' => $amount
									) );
								} else {
									// send notification to campaign owner
									$notificationTbl->addNotification ( $follow->getOwner (), $donor, $campaign, 'ynfundraising_notify_invi', array (
											'donor' => 'An anonymous donor',
											'amount' => $amount
									) );
								}
							} else {
								$notificationTbl->addNotification ( $follow->getOwner (), $follow->getOwner (), $campaign, 'ynfundraising_notify_invi', array (
										'donor' => $guest_name,
										'amount' => $amount
								) );
							}
						}
						if ($is_reached) {
							// for campaign reached
							$notificationTbl->addNotification ( $follow->getOwner (), $campaign->getOwner (), $campaign, 'ynfundraising_notify_reached', array () );
						}
					}
				}
			}
		}

		$this->_helper->content->setNoRender ();
	}
	public function editAction() {
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$view = Zend_Registry::get ( 'Zend_View' );

		$values = $this->_getAllParams ();
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $values ['campaign_id'] );
		$donation = Engine_Api::_ ()->getItem ( 'ynfundraising_donation', $values ['donation_id'] );

		// check if campaign is not exist or status <> ongoing
		if (! $campaign || $campaign->status != Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS) {
			return $this->_helper->requireAuth->forward ();
		}
		if (! $donation || $donation->status == 1) {
			return $this->_helper->requireAuth->forward ();
		}

		$viewer = Engine_Api::_ ()->user ()->getViewer ();

		// check if guest is allow to donate to this campaign
		if (! $this->_helper->requireAuth ()->setAuthParams ( $campaign, $viewer, 'donate' )->isValid ())
			return;

		$this->view->form = $form = new Ynfundraising_Form_DonateCampaign ();
		$form->populate ( $values );
		$this->view->campaign = $campaign;
		$this->view->donation = $donation;

		if (isset ( $campaign ) && is_object ( $campaign ) && isset ( $donation ) && is_object ( $donation )) {
			/*
			 * check edit permission on donation
			 */
			// if donation was successful
			if ($donation->status == Ynfundraising_Plugin_Constants::DONATION_SUCCESS) {
				// return to donation page
				return $this->_helper->redirector->gotoRoute ( array (
						'action' => 'index'
				), 'ynfundraising_extended' );
			} else {
				// if donation by an user was NOT successful
				if ($viewer->getIdentity () > 0 && $donation->user_id != $viewer->getIdentity ()) {
					return $this->_helper->requireAuth->forward ();
				}
				// if this page is accessed by a guess to a donation by an user
				if ($viewer->getIdentity () <= 0 && $donation->user_id > 0) {
					return $this->_helper->requireAuth->forward ();
				}
			}

			if (! $this->getRequest ()->isPost ()) {
				$values = array_merge ( $donation->toArray (), $campaign->toArray () );
				$values ['amount'] = str_replace ( $view->currencyfund( '', $campaign->currency ), '', $view->currencyfund( $donation->amount, $campaign->currency ) );

				$form->populate ( $values );
				return;
			}

			if ($form->isValid ( $this->getRequest ()->getPost () )) {

				$values = $form->getValues ();

				if ($values ['amount'] < $campaign->minimum_donate) {
					$form->addError ( "Your donation must be a number and at least " . $view->currencyfund( $campaign->minimum_donate, $campaign->currency ) );
					return;
				}
				if (isset ( $values ['is_agreed'] ) && $values ['is_agreed'] != 1) {
					$form->addError ( Zend_Registry::get ( 'Zend_Translate' )->_ ( "You must agree to the terms and conditions to proceed." ) );
					return;
				}

				$donation->message = $values ['message'];
				$donation->donation_date = date ( 'Y-m-d H:i:s' );
				$donation->is_anonymous = $values ['is_anonymous'];
				$donation->amount = $values ['amount'];
				if (isset ( $values ['guest_email'] )) {
					$donation->guest_email = $values ['guest_email'];
				}
				if (isset ( $values ['guest_name'] )) {
					$donation->guest_name = $values ['guest_name'];
				}
				$donation->save ();

				return $this->_forward ( 'summary', null, null, array (
						'donation_id' => $donation->getIdentity (),
						'campaign_id' => $campaign->getIdentity ()
				) );
			}
		} else {
			return $this->_helper->redirector->gotoRoute ( array (
					'action' => 'index'
			), 'ynfundraising_extended' );
		}
	}
}