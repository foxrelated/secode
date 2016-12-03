<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 22.03.12 11:50 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Qrcode_Installer extends Engine_Package_Installer_Module
{
	public function onPreInstall()
	{
		parent::onPreInstall();
		$result = $this->checkLicense();
		$error_message = "You don't have valid license. Please contact at support@ipragmatech.com or open a ticket";
		if (!$result) {
			return $this->_error($error_message);
		}
		
	}

	public function checkLicense()
	{
		$curl = curl_init();
		$params_str = '?product_id=1936&url='. $_SERVER['SERVER_NAME'];

		$url = "http://www.ipragmatech.com/api/get_product_validation/".$params_str;

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);
		curl_close($curl);

		return $result;
	}

}