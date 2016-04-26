<?php
class Ynfundraising_Api_ConvertMailVars extends Core_Api_Abstract
{

	protected static $_baseUrl;

	public static function getBaseUrl()
	{
		$request =  Zend_Controller_Front::getInstance()->getRequest();
		if(self::$_baseUrl == NULL && $request)
		{
			self::$_baseUrl = sprintf('%s://%s', $request->getScheme(), $request->getHttpHost());

		}
		return self::$_baseUrl;
	}
	/**
	 * @param   string $type
	 * @return  string
	 */
	public function selfURL()
    {
      return self::getBaseUrl();
    }

	public function inflect($type) {
		return sprintf('vars_%s', $type);
	}

	public function vars_default($params, $vars) {
		return $params;
	}

	/**
	 * call from api
	 */
	public function process($params, $vars, $type) {
		$method_name = $this->inflect($type);
		if(method_exists($this, $method_name)) {
			return $this -> {$method_name}($params, $vars);
		}
		return $this -> vars_default($params, $vars);
	}

	/**
	 *
	 */
	public function vars_fundraising_createCampaignToRequester($params, $vars) {
		$rparams[] = array();
		$rparams['campaign_name'] = $params['title'];
		$rparams['social_site'] = $params['social_site'];
		return $rparams;
	}
	public function vars_fundraising_createCampaignToOtherRequester($params, $vars) {
		$rparams[] = array();
		$campaign = Engine_Api::_()->getItem('ynfundraising_campaign', $params['campaign_id']);
		$rparams['campaign_link'] = $this->getBaseUrl().$campaign->getHref();
		$rparams['parent_name'] = $params['parent_name'];
		$rparams['campaign_owner'] = $params['campaign_owner'];
		$rparams['parent_owner'] = $params['parent_owner'];
		return $rparams;
	}

	public function vars_fundraising_inviteFriends($params, $vars) {
		$rparams[] = array();
		$rparams['inviter_name'] = $params['inviter_name'];
		$rparams['campaign_name'] = $params['title'];
		$rparams['message'] = $params['message'];
		$campaign = Engine_Api::_()->getItem('ynfundraising_campaign', $params['campaign_id']);
		$rparams['campaign_link'] = $this->getBaseUrl().$campaign->getHref();
		return $rparams;
	}

	public function vars_fundraising_campaignClosedToOwner($params, $vars) {
		$rparams[] = array();
		$rparams['reason'] = $params['reason'];
		$campaign = Engine_Api::_()->getItem('ynfundraising_campaign', $params['campaign_id']);
		$rparams['campaign_link'] = $this->getBaseUrl().$campaign->getHref();
		return $rparams;
	}

	public function vars_fundraising_campaignClosedToParent($params, $vars) {
		$rparams[] = array();
		$rparams['reason'] = $params['reason'];
		$campaign = Engine_Api::_()->getItem('ynfundraising_campaign', $params['campaign_id']);
		$rparams['campaign_link'] = $this->getBaseUrl().$campaign->getHref();
		return $rparams;
	}

	public function vars_fundraising_campaignClosedToDonor($params, $vars) {
		$rparams[] = array();
		$rparams['campaign_owner'] = $params['campaign_owner'];
		$rparams['reason'] = $params['reason'];
		$campaign = Engine_Api::_()->getItem('ynfundraising_campaign', $params['campaign_id']);
		$rparams['campaign_link'] = $this->getBaseUrl().$campaign->getHref();
		return $rparams;
	}
	public function vars_fundraising_campaignExpiredToOwner($params, $vars) {
		$rparams[] = array();
		$campaign = Engine_Api::_()->getItem('ynfundraising_campaign', $params['campaign_id']);
		$rparams['campaign_link'] = $this->getBaseUrl().$campaign->getHref();
		return $rparams;
	}

	public function vars_fundraising_campaignExpiredToParent($params, $vars) {
		$rparams[] = array();
		$campaign = Engine_Api::_()->getItem('ynfundraising_campaign', $params['campaign_id']);
		$rparams['campaign_link'] = $this->getBaseUrl().$campaign->getHref();
		return $rparams;
	}

	public function vars_fundraising_campaignExpiredToDonor($params, $vars) {
		$rparams[] = array();
		$rparams['campaign_name'] = $params['title'];
		$campaign = Engine_Api::_()->getItem('ynfundraising_campaign', $params['campaign_id']);
		$rparams['campaign_link'] = $this->getBaseUrl().$campaign->getHref();
		return $rparams;
	}

	public function vars_fundraising_thanksDonor($params, $vars) {
		$rparams[] = array();
		$campaign = Engine_Api::_()->getItem('ynfundraising_campaign', $params['campaign_id']);
		$rparams['campaign_name'] = $params['campaign_name'];
		$rparams['campaign_owner'] = $params['campaign_owner'];
		$rparams['campaign_link'] = $this->getBaseUrl().$params['campaign_link'];
		$rparams['personal_message'] = $params['personal_message'];

		return $rparams;
	}

	public function vars_fundraising_requestTimeoutRequester($params, $vars) {
		$rparams[] = array();
		$rparams['time_out'] = $params['time_out']."h";
		return $rparams;
	}

	public function vars_fundraising_requestTimeoutOwner($params, $vars) {
		$rparams[] = array();
		$rparams['time_out'] = $params['time_out']."h";
		$rparams['requester_name'] = $params['requester_name'];
		$rparams['campaign_type'] = $params['campaign_type'];
		$rparams['manage_request_link'] = $this->getBaseUrl().$params['manage_request_link'];
		return $rparams;
	}

	public function vars_fundraising_requestApproved($params, $vars) {
		$rparams[] = array();
		$rparams['parent_owner'] = $params['parent_owner'];
		$rparams['campaign_type'] = $params['campaign_type'];
		$rparams['time_out'] = $params['time_out'];
		$rparams['create_campaign_link'] = $this->getBaseUrl().$params['create_campaign_link'];

		return $rparams;
	}

	public function vars_fundraising_campaignGoalToOwner($params, $vars) {
		$rparams[] = array();
		$rparams['campaign_link'] = $this->getBaseUrl().$params['campaign_link'];

		return $rparams;
	}

	public function vars_fundraising_campaignGoalToParent($params, $vars) {
		$rparams[] = array();
		$rparams['campaign_link'] = $this->getBaseUrl().$params['campaign_link'];

		return $rparams;
	}

	public function vars_fundraising_campaignGoalToDonor($params, $vars) {
		$rparams[] = array();
		$rparams['campaign_link'] = $this->getBaseUrl().$params['campaign_link'];

		return $rparams;
	}

	public function vars_fundraising_updatingDonor($params, $vars) {
		$rparams[] = array();
		$rparams['donor_name'] = $params['donor_name'];
		$rparams['campaign_link'] = $this->getBaseUrl().$params['campaign_link'];

		return $rparams;
	}

}


