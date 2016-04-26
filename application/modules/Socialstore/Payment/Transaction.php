<?php

class Socialstore_Payment_Transaction
{
    protected $_transactionId;
    protected $_amount;
    protected $_currency;
    protected $_isFinal = true;

    /**
     * Class constructor
     *
     * @param string $transactionId
     */
    public function __construct($transactionId)
    {
        $this->_transactionId = $transactionId;
    }

    /**
     * Transaction id getter
     *
     * @return string
     */ 	
    public function getId()
    {
        return $this->_transactionId;
    }

    /**
     * isFinal flag setter
     *
     * @param boolean $flag
     * @return Socialstore_Payment_Transaction
     */
    public function setIsFinal($flag)
    {
        $this->_isFinal = $flag;
        return $this;
    }


    /**
     * Check transaction final flag
     *
     * @return boolean
     */
    public function isFinal()
    {
        return $this->_isFinal;
    }

    /**
     * Amount setter
     *
     * @param float $amount
     * @return Socialstore_Payment_Transaction
     */
    public function setAmount($amount)
    {
        $this->_amount = $amount;
        return $this;
    }

    /**
     * Amount getter
     *
     * @return float|null
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * Currency setter
     *
     * @param string $currency
     * @return Socialstore_Payment_Transaction
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
        return $this;
    }

    /**
     * Currency getter
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->_currency;
    }
}