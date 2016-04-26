<?php
    class Ynaffiliate_Plugin_Task_ApproveDelayingCommissions extends Core_Plugin_Task_Abstract {
        public function execute() {
            $now = new DateTime();
            $commissionsTable = Engine_Api::_()->getDbTable('commissions', 'ynaffiliate');

            // get delay period
            $delayingPeriod = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.delay', 30);

            // get delaying commission
            $select = $commissionsTable->select()->where('approve_stat = ?', 'delaying');
            $commissions = $commissionsTable->fetchAll($select);

            foreach ($commissions as $commission) {
                if (isset($commission->creation_date)) {
                    $creationDate = new DateTime($commission->creation_date);
                    $diff = $creationDate->diff($now)->format("%a");
                    if ($diff > $delayingPeriod) {
                        $commission -> approve_stat = 'approved';
                        $commission->approved_date = date('Y-m-d H:i:s');
                        $commission -> save();
                    }
                }
            }
        }
    }