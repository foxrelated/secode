<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Wheretobuies.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Wheretobuies extends Engine_Db_Table {

    protected $_name = 'siteevent_wheretobuy';
    protected $_rowClass = "Siteevent_Model_WhereToBuy";

    public function getList($params = array()) {

        $select = $this->select();
        if (isset($params['enabled'])) {
            $select->where('enabled = ?', $params['enabled']);
        }

        return $this->fetchAll($select);
    }

}
