<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfundraising
 * @author     YouNet Company
 */

class Ynfundraising_Plugin_Utilities {
	public static function getBaseUrl()
	{
		return rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']);
	}

	public static function isSandboxMode()
	{
		return Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfundraising.mode', 1);
	}

	public static function getPaymentUrl($method = 'paypal')
	{
		$isSandBox = Ynfundraising_Plugin_Utilities::isSandboxMode();
		if ($method == 'paypal') {
			if ($isSandBox) {
				return Ynfundraising_Plugin_Constants::PAYPAL_SANDBOX_URL;
			}
			return Ynfundraising_Plugin_Constants::PAYPAL_URL;
		}
	}
}