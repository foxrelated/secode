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
class Money_IndexController extends Core_Controller_Action_Standard
{
    public function init()
    {
        if (!$this->_helper->requireUser()->isValid())
            return;

        Engine_Api::_()->money()->checkUserAccount(Engine_Api::_()->user()->getViewer());
    }


    public function sendAction()
    {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('money_main', array(), 'money_main_send');

        $this->view->form = $form = new Money_Form_Send();

        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->money = Engine_Api::_()->money()->getUserBalance($viewer);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = Engine_Api::_()->getDbtable('money', 'money');

        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $values = $form->getValues();

            $recipients = preg_split('/[,. ]+/', $values['toValues']);
            $recipients = array_unique($recipients);

            $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);

            foreach ($recipientsUsers as &$recipientUser) {
                if (!$viewer->membership()->isMember($recipientUser)) {
                    return $form->addError('One of the members specified is not in your friends list.');
                }
            }

            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            foreach ($recipientsUsers as &$recipientUser) {
                Engine_Api::_()->money()->sendBalanceToFriend($recipientUser, $viewer, $values);
                $notifyApi->addNotification($recipientUser, $viewer, $viewer, 'send_money', array(
                    'count' => $values['amount']
                ));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your money is sent successfully.')),
            'parentRefresh' => true
        ));
    }

    public function transactionAction()
    {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('money_main', array(), 'money_main_transaction');

        $this->view->form = $form = new Money_Form_Admin_Search();
        $form->removeElement('text');

        $values = array();
        if ($form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            $this->view->formValues = array_filter($values);
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $values['user_id'] = $viewer->getIdentity();

        $items_count = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('money.page', 30);

        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('transactions',
            'money')->getTransactionPaginator($values);
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }

    public function issueAction()
    {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('money_main', array(), 'money_main_issue');

        $this->view->form = $form = new Money_Form_Issue();
        $this->view->commission = $commission = Engine_Api::_()->getApi('settings', 'core')->getSetting('money.commissionissue');

        if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = Engine_Api::_()->getDbtable('issues', 'money');

        $values = $form->getValues();

        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $issue = $table->createRow();
            $values['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();


            $amount = round((($values['amount'] * $commission) / 100) - $values['amount'], 2);
            $values['amount'] = $amount;
            $issue->setfromArray($values);
            $issue->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your application was sent successfully.')),
            'parentRefresh' => true,
        ));
    }
}
