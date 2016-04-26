<?php

class Ynaffiliate_Plugin_Core
{

    public function onUserCreateAfter($event)
    {

        $user = $event -> getPayload();
        if (!($user instanceof User_Model_User))
        {
            return;
        }

        $api = Engine_Api::_() -> ynaffiliate();
        $user_id = $_COOKIE['ynafuser'];
        $link_id = $_COOKIE['ynaflink'];
        $time = $_COOKIE['ynafftime'];
        if ($user_id && $link_id)
        {
            $api -> addAssoc($user_id, $user -> getIdentity(), $link_id);
        }
        else
        {
            if (!Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynaffiliate.allowinvite', true))
            {
                return;
            }

            if (!Engine_Api::_() -> hasModuleBootstrap('invite'))
            {
                return;
            }

            $session = new Zend_Session_Namespace('invite');
            $inviteTable = Engine_Api::_() -> getDbtable('invites', 'invite');

            // Get codes
            $codes = array();
            if (!empty($session -> invite_code))
            {
                $codes[] = $session -> invite_code;
            }
            if (!empty($session -> signup_code))
            {
                $codes[] = $session -> signup_code;
            }
            $codes = array_unique($codes);
            // Nothing, exit now
            if (empty($codes))
            {
                return;
            }
            // Get related invites
            $select = $inviteTable -> select() -> where('code IN(?)', $codes) -> order('id');

            $invite = $inviteTable -> fetchRow($select);

            if (is_object($invite))
            {
                $api -> addAssoc($invite -> user_id, $user -> getIdentity(), 0, $invite -> id, $invite -> code, $invite -> timestamp);
            }

        }
    }

    public function onUserDeleteAfter($event)
    {
        $user = $event -> getPayload();

        if (!($user instanceof User_Model_User))
        {
            return;
        }
    }

    public function onUserEnable($event)
    {
        $user = $event -> getPayload();
        if (!($user instanceof User_Model_User))
        {
            return;
        }

    }

    public function onUserDisable($event)
    {
        $user = $event -> getPayload();
        if (!($user instanceof User_Model_User))
        {
            return;
        }

    }

    public function onPaymentSubscriptionUpdateAfter($event)
    {
        try
        {
            $subs = $event -> getPayload();
            if (!($subs instanceof Payment_Model_Subscription))
            {
                return;
            }
            if ($subs -> status == 'active')
            {
                // get order
                $ordersRaw = Engine_Api::_()->getDbtable('orders', 'payment')->fetchAll(array(
                    'source_type = ?' => 'payment_subscription',
                    'source_id = ?' => $subs->subscription_id,
                    'state = ?' => 'complete'
                ));
                if (!count($ordersRaw)) {
                    return;
                }

                $orderIds = array();
                foreach( $ordersRaw as $order ) {
                    $orderIds[] = $order->order_id;
                }

                // fetch transaction
                $transactions = Engine_Api::_()->getDbtable('transactions', 'payment')->fetchAll(array(
                    'order_id IN(?)' => $orderIds,
                    'state =?' => 'okay'
                ));

                // valid transaction found
                if (count($transactions)) {
                    $params = array();
                    $package_id = $subs -> package_id;
                    $Packages = new Payment_Model_DbTable_Packages;
                    $select = $Packages -> select() -> where('package_id = ?', $package_id);
                    $packages_result = $Packages -> fetchRow($select);
                    $amount = $packages_result -> price;
                    $params['total_amount'] = $amount;
                    $params['currency'] = Engine_Api::_() -> getApi('settings', 'core') -> payment['currency'];
                    $params['rule_name'] = 'subscription';
                    $params['module'] = 'payment';

                    $new_user_id = $subs -> user_id;
                    $params['user_id'] = $new_user_id;
                    $affiliate_id = $new_user_id;
                    // get max commission level
                    $MAX_COMMISSION_LEVEL = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.max.commission.level', 5);
                    for ($level = 1; $level <= $MAX_COMMISSION_LEVEL; $level++) {
                        $assoc = Engine_Api::_()->ynaffiliate()->getAssocId($affiliate_id);
                        // check for assoc existent
                        if ($assoc && $assoc->user_id) {
                            $user = Engine_Api::_()->getItem('user', $assoc->user_id);
                        } else {
                            continue;
                        }
                        if ($user->getIdentity()) {
                            $affiliate_id = $user->user_id;
                            $user_id = $user->user_id;
                            $params['affiliate_id'] = $user_id;
                            $params['level'] = $level;
                            $Commissions = new Ynaffiliate_Model_DbTable_Commissions;
                            $Commissions->addCommission($params);
                        } else {
                            continue;
                        }
                    }
                }
                else
                {
                    return;
                }
            }

        }
        catch(Exception $e)
        {
        }
    }

    public function onPaymentAfter($event)
    {
        $params = $event -> getPayload();
        if (!isset($params['user_id'])) {
            return;
        }
        $new_user_id = $params['user_id'];
        // get the first aff id to loop
        $affiliate_id = $new_user_id;
        // get max commission level
        $MAX_COMMISSION_LEVEL = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.max.commission.level', 5);
        for ($level = 1; $level <= $MAX_COMMISSION_LEVEL; $level++) {
            $assoc = Engine_Api::_() -> ynaffiliate() -> getAssocId($affiliate_id);

            // check for assoc existent
            if ($assoc && $assoc->user_id) {
                $user = Engine_Api::_()->getItem('user', $assoc->user_id);
            } else {
                continue;
            }

            // check for user existent
            if ($user->getIdentity()) {
                $affiliate_id = $user->user_id;
                $params['affiliate_id'] = $affiliate_id;
                $params['level'] = $level;
                $Commissions = new Ynaffiliate_Model_DbTable_Commissions;
                $Commissions->addCommission($params);
            } else {
                // user not found
                continue;
            }
        }
    }
}
