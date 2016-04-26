<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfundraising
 * @author     YouNet Company
 */

class Ynfundraising_Model_Request extends Core_Model_Item_Abstract
{
	protected $_searchTriggers = false;
	public function checkCampaignDraft()
	{
		$campaignTbl = Engine_Api::_ ()->getItemTable ( 'ynfundraising_campaign' );
		$campaignTblName = $campaignTbl->info ( 'name' );

		$select = $campaignTbl->select()->where("request_id = ?", $this->getIdentity())->where('status = ?', Ynfundraising_Plugin_Constants::CAMPAIGN_DRAFT_STATUS);
		return $campaignTbl->fetchRow($select);
	}
	public function checkCampaignOtherDraft()
	{
		$campaignTbl = Engine_Api::_ ()->getItemTable ( 'ynfundraising_campaign' );
		$campaignTblName = $campaignTbl->info ( 'name' );

		$select = $campaignTbl->select()->where("request_id = ?", $this->getIdentity())->where('status <> ?', Ynfundraising_Plugin_Constants::CAMPAIGN_DRAFT_STATUS);
		return $campaignTbl->fetchRow($select);
	}

	public function getCampaignFromRequest($request_id)
	{
		$campaignTable = Engine_Api::_()->getDbtable('campaigns', 'ynfundraising');
		$select = $campaignTable->select()
		->where('request_id = ?', $request_id)
		->limit(1);
		$row = $campaignTable->fetchRow($select);
		return $row;
	}
}