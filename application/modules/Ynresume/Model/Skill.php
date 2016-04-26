<?php
class Ynresume_Model_Skill extends Core_Model_Item_Abstract
{
	protected $_searchTriggers = false;

	public function getTitle()
	{
		return $this->text;
	}

	public function getHref($params = array())
	{
		return "";
	}
	
	public function getEndorsedUsers($resume, $onlyEndorsedUser = true)
	{
		$mapTable = Engine_Api::_()->getDbtable('SkillMaps', 'ynresume');
		$select = $mapTable->select()
		->where('resume_id = ?', $resume->getIdentity())
        ->where('user_id <> ?', $resume->user_id)
		->where('skill_id = ?', $this->getIdentity());
        if ($onlyEndorsedUser)
        {
            $select -> where("deleted = 0");
        }
		return $mapTable -> fetchAll($select);
	}


}