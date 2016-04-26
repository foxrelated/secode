<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndonation
 * @author     YouNet Company
 */

class Ynfundraising_Plugin_Constants {
	const CURRENCY_CODE = 'USD';

	const PAYPAL_SANDBOX_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	const PAYPAL_URL = 'https://www.paypal.com/cgi-bin/webscr';
	const PAYPAL_PAYMENT_SUCCESS = 'Completed';
	const DONATION_SUCCESS = 1;

	// campaign status
	const CAMPAIGN_DRAFT_STATUS = 'draft';
	const CAMPAIGN_ONGOING_STATUS = 'ongoing';
	const CAMPAIGN_CLOSED_STATUS = 'closed';
	const CAMPAIGN_REACHED_STATUS = 'reached';
	const CAMPAIGN_EXPIRED_STATUS = 'expired';

	// request status
	const REQUEST_WAITING_STATUS = 'waiting';
	const REQUEST_APPROVED_STATUS = 'approved';
	const REQUEST_DENIED_STATUS = 'denied';

}