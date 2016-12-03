<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ordercheques.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_DbTable_Ordercheques extends Engine_Db_Table {

    protected $_name = 'siteeventticket_ordercheques';

    /**
     * Return cheque detail
     *
     * @param $cheque_id
     * @return object
     */
    public function getChequeDetail($cheque_id) {
        $select = $this->select()
                ->from($this->info('name'))
                ->where("ordercheque_id =?", $cheque_id);

        return $select->query()->fetch();
    }

}
