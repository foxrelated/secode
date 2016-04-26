<?php

class Ynaffiliate_Model_DbTable_Assoc extends Engine_Db_Table
{
    protected $_rowClass = "Ynaffiliate_Model_Assoc";

    public function countClient($user_id = null)
    {
        $select = $this -> select() -> from($this -> info('name'), 'COUNT(*) AS count') -> where('approved = 1');
        if ($user_id) {
            $select -> where('user_id = ?', $user_id);
        }
        return $select->query()->fetchColumn(0);
    }

    public function countAllClient($userId)
    {
        $aResult = array();
        $aResult[0][] = $userId;
        $clientCount = 0;
        $maxLevel = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.max.commission.level', 5);
        for ($level = 0; $level < $maxLevel; $level++)
        {
            // set next level to empty if previous is empty
            if (empty($aResult[$level])) {
                $aResult[$level + 1] = array();
            } else {
                $select = $this->select() -> from($this -> info('name'), 'new_user_id')
                    ->where('user_id IN (?)', $aResult[$level])->where('approved = 1');
                $clients = $this -> fetchAll($select);

                foreach ($clients as $client)
                {
                    $aResult[$level + 1][] = $client -> new_user_id;
                }
                $clientCount += count($aResult[$level + 1]);
            }
        }
        return $clientCount;
    }

    /**
     * @param null $user_id
     * @param null $level
     * @return array
     */
    public function getClient($user_id = null, $fromLevel = 0, $last_assoc_id = 0, $search_user_id = 0, $noLimit = 0) {
        if (!$user_id) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $user_id = $viewer->getIdentity();
        }

        $clientLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.client.limit', 3);
        $maxLevel = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.max.commission.level', 5);

        // level offset to use when load more for middle clients
        if ($fromLevel >= $maxLevel){
            return;
        }
        // pass 1 to start recursive
        $result = $this->_getClient($user_id, $clientLimit, $fromLevel + 1, $maxLevel, $last_assoc_id, $search_user_id, $noLimit);

        return $result;
    }

    /**
     * @param $user_id
     * @param $clientLimit
     * @param $level
     * @param $maxLevel
     * @return array
     * get client with preset value for recursive purpose
     */
    protected function _getClient($user_id, $clientLimit, $level, $maxLevel, $last_assoc_id = 0, $search_user_id = 0, $noLimit = 0) {

        // max level reached, return
        if ($level > $maxLevel) {
            return array();
        }
        $result = array();

        // get next level client
        $select = $this->select()->where('user_id = ?', $user_id)
                ->where('approved = 1')
                ->where('assoc_id > ?', $last_assoc_id);

        // don't limit if searching
        if (!$search_user_id && !$noLimit) {
            $select->limit($clientLimit);
        }
        $clients = $this->fetchAll($select);
        foreach ($clients as $client) {

            $child_clients = array();
            $found = 0;
            // user not found at this level, continue search or back
            if (!$search_user_id || ($client->new_user_id != $search_user_id)) {
                $child_clients = $this->_getClient($client->new_user_id, $clientLimit, $level + 1, $maxLevel, 0, $search_user_id, $noLimit );
            } else {
                $found = 1;
            }
            // count all client, start by get direct clients
            $select = $this -> select()
                ->from($this->info('name'), 'COUNT(*) AS count')
                ->where('approved = 1') -> where('user_id = ?', $client -> new_user_id);
            $direct_client = $select->query()->fetchColumn(0);
            // count all client, including clients of children
            $total_client = $this -> countAllClient($client -> new_user_id);
            $one_client = array(
                'creation_date' => $client -> creation_date,
                'user_id' => $client -> new_user_id,
                'assoc_id' => $client -> assoc_id,
                'clients' => $child_clients,
                'total_client' => $total_client,
                'direct_client' => $direct_client,
                'level' => $level,
                'is_last' => ($level == $maxLevel - 1) ? 1:0
            );

            // if this is the found client or it's parent, add it to the tree
            if (!$search_user_id || ($search_user_id && count($child_clients)) || $found) {
                $result[] = $one_client;
            }
        }

        return $result;
    }

	public function getAllClients($userId)
	{
		$aResult[0][] = $userId;
		$maxLevel = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.max.commission.level', 5);
		for ($level = 0; $level < $maxLevel; $level++) 
		{
			$clients = null;
			if($aResult[$level])
			{
			 	$select = $this->select() -> from($this -> info('name'), 'new_user_id')
        				->where('user_id IN (?)', $aResult[$level])->where('approved = 1');
			 	$clients = $this -> fetchAll($select);
			}
			foreach ($clients as $client) 
			{
				 $aResult[$level + 1][] = $client -> new_user_id;
			}
		}
        return $aResult;
	}
}