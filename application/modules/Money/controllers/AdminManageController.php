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
class Money_AdminManageController extends Core_Controller_Action_Admin
{
    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('money_admin_main', array(), 'money_admin_main_manage');


        $moneyTable = Engine_Api::_()->getDbtable('money', 'money');
        $moneyTableName = $moneyTable->info('name');
        $userTable = Engine_Api::_()->getItemTable('user');
        $userTableName = $userTable->info('name');

        $select = $userTable->select()
                ->from($userTableName)
                ->setIntegrityCheck(false)
                ->joinRight($moneyTableName, "`{$moneyTableName}`.`user_id` = `{$userTableName}`.`user_id`", array('*'))
                ->order($moneyTableName . '.money');
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    }

    public function transactionAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('money_admin_main', array(), 'money_admin_main_transaction');

        $this->view->form = $form = new Money_Form_Admin_Search;


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

        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('money.page', 30);

        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('transactions',
            'money')->getTransactionPaginator($values);
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }

    public function settingsAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('money_admin_main', array(), 'money_admin_main_settings');

        $this->view->form = $form = new Money_Form_Admin_Settings();

        // Populate form
        // Populate currency options
        $supportedCurrencyIndex = array();
        $fullySupportedCurrencies = array();
        $supportedCurrencies = array();
        $gateways = array();
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'money');
        foreach ($gatewaysTable->fetchAll(/* array('enabled = ?' => 1) */) as $gateway) {
            $gateways[$gateway->gateway_id] = $gateway->title;
            $gatewayObject = $gateway->getGateway();
            $currencies = $gatewayObject->getSupportedCurrencies();
            if (empty($currencies)) {
                continue;
            }
            $supportedCurrencyIndex[$gateway->title] = $currencies;
            if (empty($fullySupportedCurrencies)) {
                $fullySupportedCurrencies = $currencies;
            } else {
                $fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
            }
            $supportedCurrencies = array_merge($supportedCurrencies, $currencies);
        }
        $supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);

        $translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
        $fullySupportedCurrencies = array_intersect_key($translationList, array_flip($fullySupportedCurrencies));
        $supportedCurrencies = array_intersect_key($translationList, array_flip($supportedCurrencies));
        $form->getElement('money_currency')->setMultiOptions(array(
            'Fully Supported' => $fullySupportedCurrencies,
            'Partially Supported' => $supportedCurrencies,
        ));

        $form->getElement('money_site_currency')->setMultiOptions(array(
            'Fully Supported' => $fullySupportedCurrencies,
            'Partially Supported' => $supportedCurrencies,
        ));

        $this->view->gateways = $gateways;
        $this->view->supportedCurrencyIndex = $supportedCurrencyIndex;

        $form->populate((array) Engine_Api::_()->getApi('settings', 'core')->money);

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        foreach ($values as $key => $value) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }


        $form->addNotice('Your changes have been saved.');
    }

    function editAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('money_admin_main', array(), 'money_admin_main_manage');

        $this->view->form = $form = new Money_Form_Admin_Edit();

        $table = Engine_Api::_()->getDbtable('money', 'money');
        $user = $table->fetchRow($table->select()->where('user_id =?', $this->_getParam('user_id')));
        $form->populate(array('money' => $user->money));

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();
            $user->setFromArray($values);
            $user->save();

            $db->commit();
        } catch (Exeption $e) {
            $db->rollBack();
            throw $e;
        }

        $form->addNotice('Your changes have been saved.');
    }

}