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
class Money_AdminIssueController extends Core_Controller_Action_Admin
{
    public function issueAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('money_admin_main', array(), 'money_admin_main_issue');

        $table = Engine_Api::_()->getDbtable('issues', 'money');
        $select = $table->select()->order('issue_id DESC');

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('money.page', 30);
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }

    public function payAction() {

        $this->view->issue = Engine_Api::_()->getItem('money_issue', $this->_getParam('issue_id'));
        
        
    }

    public function paymoneyAction() {
        $issue = Engine_Api::_()->getItem('money_issue', $this->_getParam('id'));
        $viewer = Engine_Api::_()->user()->getViewer();
        try {

            Engine_Api::_()->getDbtable('money', 'money')->updateMoneyPayPal($issue->getOwner(), $issue->amount);
            
            if($issue->gateway_id == 1 ){
                $type = 4;
            }
            else if($issue->gateway_id == 2){
                $type = 5;
            }
            else if($issue->gateway_id == 3){
                $type = 8;
            }
            
            $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'money');
            $trans = $transactionsTable->createRow();
            $trans->setFromArray(array(
                'user_id' => $issue->getOwner()->getIdentity(),
                'gateway_id' => $issue->gateway_id,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'order_id' => $issue->getIdentity(),
                'type' => $type,
                'state' => 'okay',
                'amount' => $issue->amount, // @todo use this or gross (-fee)?
                'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('money.currency', 'USD'),
            ));
            
            $trans->save();
            
            $issue->enable = 0;
            $issue->save();
            
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notifyApi->addNotification($issue->getOwner(),  $viewer, $issue, 'approve_money');
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        
    }

    public function refuseAction() {
        $issue = Engine_Api::_()->getItem('money_issue', $this->_getParam('id'));
        $viewer = Engine_Api::_()->user()->getViewer();
        try {
            $issue->enable = 3;
            $issue->save();

            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notifyApi->addNotification($issue->getOwner(), $viewer, $issue, 'refuse_money');
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

}