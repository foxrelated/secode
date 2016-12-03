<?php
/**
 * SocialEnginePro
 *
 * @category   Application_Extensions
 * @package    E-money
 * @author     Azim
 */

/**
 * @category   Application_Extensions
 * @package    E-money
 */
class Money_Api_Core extends Core_Api_Abstract
{
    public function getItemPaidInfo(Core_Model_Item_Abstract $resource)
    {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if ($resource->getOwner()->getIdentity() == $viewer_id) {
            return true;
        }
        $table = Engine_Api::_()->getItemTable('money_order');
        $row = $table->select()
            ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
            ->where('source_type =?', $resource->getType())
            ->where('user_id =?', $viewer_id)
            ->where('source_id =?', $resource->getIdentity())
            ->where('state =?', 'complete')
            ->limit(1)
            ->query()
            ->fetchColumn();
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserBalance(User_Model_User $user)
    {
        $row = $this->_getMoneyTable()->getBalance($user);

        return $row ? $row->money : 0;
    }

    public function checkUserAccount(User_Model_User $user)
    {
        $row = $this->_getMoneyTable()->getBalance($user);

        if (!$row) {
            $row = $this->_getMoneyTable()->createRow();
            $row->user_id = $user->getIdentity();
            $row->save();
        }
    }

    public function sendBalanceToFriend(User_Model_User $recipients, User_Model_User $sender, $values = array())
    {
        // sender
        $values['type'] = 6;
        $this->updateBalance($recipients, $values['amount']);
        $this->_transferBalanceTransaction($recipients, $sender, $values);

        // recipients
        $values['type'] = 7;
        $values['amount'] = -($values['amount']);
        $this->updateBalance($sender, $values['amount']);
        $this->_transferBalanceTransaction($sender, $recipients, $values);
    }

    public function updateBalance(User_Model_User $user, $amount)
    {
        $row = $this->_getMoneyTable()->getBalance($user);

        $row->money = $row->money + (int)$amount;
        $row->save();
    }

    protected function _transferBalanceTransaction(User_Model_User $recipients, User_Model_User $sender,
                                                   $values = array())
    {

        $transaction = $this->_getTransactionTable()->createRow();
        $transaction->setFromArray(array(
            'user_id' => $recipients->getIdentity(),
            'state' => 'okay',
            'gateway_id' => $values['type'],
            'type' => $values['type'],
            'body' => $values['body'],
            'amount' => $values['amount'],
            'gateway_parent_transaction_id' => $sender->getIdentity(),
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('money.currency', 'USD')
        ));
        $transaction->save();
    }

    protected function _getMoneyTable()
    {
        return Engine_Api::_()->getDbtable('money', 'money');
    }

    protected function _getTransactionTable()
    {
        return Engine_Api::_()->getDbtable('transactions', 'money');
    }
}
