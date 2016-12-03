<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Transactions.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventpaid_Model_DbTable_Transactions extends Engine_Db_Table {

    protected $_rowClass = 'Siteeventpaid_Model_Transaction';

    public function getBenefitStatus(User_Model_User $user = null) {
        // Get benefit setting
        $benefitSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventpaid.payment.benefit');
        if (!in_array($benefitSetting, array('all', 'some', 'none'))) {
            $benefitSetting = 'all';
        }

        switch ($benefitSetting) {
            default:
            case 'all':
                return true;
                break;

            case 'some':
                if (!$user) {
                    return false;
                }
                return (bool) $this->select()
                                ->from($this, new Zend_Db_Expr('TRUE'))
                                ->where('user_id = ?', $user->getIdentity())
                                ->where('type = ?', 'payment')
                                ->where('status = ?', 'okay')
                                ->limit(1);
                break;

            case 'none':
                return false;
                break;
        }

        return false;
    }

}
