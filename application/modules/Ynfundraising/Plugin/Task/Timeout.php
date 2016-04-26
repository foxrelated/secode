<?php
class Ynfundraising_Plugin_Task_Timeout extends Core_Plugin_Task_Abstract {
	public function execute() {
		/**
		 * Check requests time out
		 */
		try {
			if (Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ()) {
				// get all request with status approved and (not exists campaign
				// or campaign not published)
				$requests = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getAllRequestsTimeOut ();
				$params = array ();
				$params ['time_out'] = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.timeout', '' );
				if (count ( $requests ) > 0) {
					foreach ( $requests as $request ) {
						if ($request->checkCampaignDraft ()) {
							// send mail to requester
							$user = Engine_Api::_ ()->getItem ( 'user', $request->requester_id );
							if ($user) {
								$sendTo = $user->email;
								Engine_Api::_ ()->getApi ( 'mail', 'ynfundraising' )->send ( $sendTo, 'fundraising_requestTimeoutRequester', $params );
							}

							// send mail to requester
							$params ['requester_name'] = $user->getTitle ();
							$params ['campaign_type'] = $request->parent_type;
							$params ['manage_request_link'] = Zend_Controller_Front::getInstance ()->getRouter ()->assemble ( array (
									'action' => 'manage-requests'
							), "ynfundraising_general" );
							$parent_obj = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getItemFromType ( $request->toArray () );
							if ($parent_obj && $parent_obj->getOwner ()->getIdentity () > 0) {
								$sendTo = $parent_obj->getOwner ()->email;
								Engine_Api::_ ()->getApi ( 'mail', 'ynfundraising' )->send ( $sendTo, 'fundraising_requestTimeoutOwner', $params );
							}

							// Show all other requets on this idea/trophy
							$requestTbl = Engine_Api::_ ()->getItemTable ( 'ynfundraising_request' );
							$other_request_select = $requestTbl->select ()->where ( 'request_id <> ?', $request->getIdentity () )->where ( 'parent_type = ?', $request->parent_type )->where ( 'parent_id = ?', $request->parent_id )->where ( 'is_completed = ?', 0 );
							$other_request_result = $requestTbl->fetchAll ( $other_request_select );
							if (count ( $other_request_result ) > 0) {
								foreach ( $other_request_result as $other_request ) {
									$other_request->visible = 1;
									$other_request->save ();
								}
							}

							$campaign = $request->checkCampaignDraft ();
							$campaign->delete ();
							$request->delete ();
						} elseif (! $request->checkCampaignOtherDraft ()) {
							// send mail to requester
							$user = Engine_Api::_ ()->getItem ( 'user', $request->requester_id );
							if ($user) {
								$sendTo = $user->email;
								Engine_Api::_ ()->getApi ( 'mail', 'ynfundraising' )->send ( $sendTo, 'fundraising_requestTimeoutRequester', $params );
							}

							// send mail to owner idea/trophy
							$params ['requester_name'] = $user->getTitle ();
							$params ['campaign_type'] = $request->parent_type;
							$params ['manage_request_link'] = Zend_Controller_Front::getInstance ()->getRouter ()->assemble ( array (
									'action' => 'manage-requests'
							), "ynfundraising_general" );
							$parent_obj = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getItemFromType ( $request->toArray () );
							if ($parent_obj && $parent_obj->getOwner ()->getIdentity () > 0) {
								$sendTo = $parent_obj->getOwner ()->email;
								Engine_Api::_ ()->getApi ( 'mail', 'ynfundraising' )->send ( $sendTo, 'fundraising_requestTimeoutOwner', $params );
							}

							// Show all other requets on this idea/trophy
							$requestTbl = Engine_Api::_ ()->getItemTable ( 'ynfundraising_request' );
							$other_request_select = $requestTbl->select ()->where ( 'request_id <> ?', $request->getIdentity () )->where ( 'parent_type = ?', $request->parent_type )->where ( 'parent_id = ?', $request->parent_id )->where ( 'is_completed = ?', 0 );
							$other_request_result = $requestTbl->fetchAll ( $other_request_select );
							if (count ( $other_request_result ) > 0) {
								foreach ( $other_request_result as $other_request ) {
									$other_request->visible = 1;
									$other_request->save ();
								}
							}

							$request->delete ();
						}
					}
				}
			}
			/**
			 * check campaign time out
			 */
			$params = array (
					'expiry_date' => true
			);

			$campaigns = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getCampaignPaginator ( $params );

			if (count ( $campaigns ) > 0) {
				foreach ( $campaigns as $campaign )
				{
					$campaignItem = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign->getIdentity() );
					$campaignItem->status = 'expired';
					$campaignItem->save ();
					// send notification to follower
					$notificationTbl = Engine_Api::_ ()->getDbtable ( 'notifications', 'activity' );

					$follows = Engine_Api::_ ()->ynfundraising ()->getFollowers ( $campaign->getIdentity () );
					if (count($follows) > 0) {
						foreach ( $follows as $follow ) {
							$notificationTbl->addNotification ( $follow->getOwner (), $campaign->getOwner (), $campaign, 'ynfundraising_notify_expired', array () );
						}
					}
					// Start send mail
					// minhnc - Send mail to owner
					$params = $campaign->toArray ();
					if ($campaign->getOwner ()->getIdentity () > 0) {
						$sendTo = $campaign->getOwner ()->email;

						Engine_Api::_ ()->getApi ( 'mail', 'ynfundraising' )->send ( $sendTo, 'fundraising_campaignExpiredToOwner', $params );
					}

					if (Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ()) {
						// minhnc - send mail to trophy/idea owner
						$parent_obj = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getItemFromType ( $campaign->toArray () );
						if ($parent_obj && $parent_obj->getOwner ()->getIdentity () > 0) {
							$sendTo = $parent_obj->getOwner ()->email;
							Engine_Api::_ ()->getApi ( 'mail', 'ynfundraising' )->send ( $sendTo, 'fundraising_campaignExpiredToParent', $params );
						}
					}
					// minhnc - Send mail to donors
					$values = array (
							"campaign" => $campaign->getIdentity ()
					);

					$donors = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getDonorPaginator ( $values );
					if (count($donors) > 0) {
						foreach ( $donors as $donor ) {
							$user = Engine_Api::_ ()->getItem ( 'user', $donor->user_id );
							if ($user) {
								$sendTo = $user->email;
								Engine_Api::_ ()->getApi ( 'mail', 'ynfundraising' )->send ( $sendTo, 'fundraising_campaignExpiredToDonor', $params );
							} elseif ($donor->guest_email) {
								$sendTo = $donor->guest_email;
								Engine_Api::_ ()->getApi ( 'mail', 'ynfundraising' )->send ( $sendTo, 'fundraising_campaignExpiredToDonor', $params );
							}
						}
					}
					// end send mail
				}
			}
		} catch ( Exception $e ) {
			if (APPLICATION_ENV == 'development') {
				throw $e;
			}
		}
	}
}
