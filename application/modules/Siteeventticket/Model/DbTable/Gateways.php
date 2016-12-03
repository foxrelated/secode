<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Gateways.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_DbTable_Gateways extends Engine_Db_Table {

    protected $_name = 'siteeventticket_gateways';
    protected $_rowClass = 'Siteeventticket_Model_Gateway';
    protected $_serializedColumns = array('config');
    protected $_cryptedColumns = array('config');
    static private $_cryptKey;

    public function getEnabledGatewayCount() {
        return $this->select()
                        ->from($this, new Zend_Db_Expr('COUNT(*)'))
                        ->where('enabled = ?', 1)
                        ->query()
                        ->fetchColumn()
        ;
    }

    public function getEnabledGateways() {
        return $this->fetchAll($this->select()->where('enabled = ?', true));
    }

    // Inline encryption/decryption
    public function insert(array $data) {
        // Serialize
        $data = $this->_serializeColumns($data);

        // Encrypt each column
        foreach ($this->_cryptedColumns as $col) {
            if (!empty($data[$col])) {
                $data[$col] = self::_encrypt($data[$col]);
            }
        }

        return parent::insert($data);
    }

    public function update(array $data, $where) {
        // Serialize
        $data = $this->_serializeColumns($data);

        // Encrypt each column
        foreach ($this->_cryptedColumns as $col) {
            if (!empty($data[$col])) {
                $data[$col] = self::_encrypt($data[$col]);
            }
        }

        return parent::update($data, $where);
    }

    protected function _fetch(Zend_Db_Table_Select $select) {
        $rows = parent::_fetch($select);

        foreach ($rows as $index => $data) {
            // Decrypt each column
            foreach ($this->_cryptedColumns as $col) {
                if (!empty($rows[$index][$col])) {
                    $rows[$index][$col] = self::_decrypt($rows[$index][$col]);
                }
            }
            // Unserialize
            $rows[$index] = $this->_unserializeColumns($rows[$index]);
        }

        return $rows;
    }

    // Crypt Utility

    static private function _encrypt($data) {
        if (!extension_loaded('mcrypt')) {
            return $data;
        }

        $key = self::_getCryptKey();
        $cryptData = mcrypt_encrypt(MCRYPT_DES, $key, $data, MCRYPT_MODE_ECB);

        return $cryptData;
    }

    static private function _decrypt($data) {
        if (!extension_loaded('mcrypt')) {
            return $data;
        }

        $key = self::_getCryptKey();
        $cryptData = mcrypt_decrypt(MCRYPT_DES, $key, $data, MCRYPT_MODE_ECB);
        $cryptData = rtrim($cryptData, "\0");

        return $cryptData;
    }

    static private function _getCryptKey() {
        if (null === self::$_cryptKey) {
            $key = Engine_Api::_()->getApi('settings', 'core')->core_secret
                    . '^'
                    . Engine_Api::_()->getApi('settings', 'core')->payment_secret;
            self::$_cryptKey = substr(md5($key, true), 0, 8);
        }

        return self::$_cryptKey;
    }

    public function getEventGateway($event_id) {
        return $this->select()->from($this->info('name'), 'gateway_id')->where('event_id =? AND enabled = 1', $event_id)->query()->fetchColumn();
    }

    /**
     * Return PayPal gateway id, if exist
     *
     * @param int $event_id
     * @return int
     */
    public function isPayPalGatewayEnable($event_id) {
        $select = $this->select()
                ->from($this->info('name'), 'gateway_id')
                ->where("plugin = 'Payment_Plugin_Gateway_PayPal'")
                ->where("enabled = 1")
                ->where("event_id = ?", $event_id);

        return $select->query()->fetchColumn();
    }

}
