<?php

class Socialstore_Payment_Method_Echeck
{
    const ACCOUNT_TYPE_CHECKING = 'CHECKING';
    const ACCOUNT_TYPE_BUSINESS = 'BUSINESSCHECKING';
    const ACCOUNT_TYPE_SAVINGS  = 'SAVINGS';

    const TRANSACTION_TYPE_CCD  = 'CCD';
    const TRANSACTION_TYPE_PPD  = 'PPD';
    const TRANSACTION_TYPE_TEL  = 'TEL';
    const TRANSACTION_TYPE_WEB  = 'WEB';

    protected $_routingCode;
    protected $_accountNumber;
    protected $_accountType;
    protected $_accountName;
    protected $_bankName;
    protected $_transactioType;
    protected $_checkNumber;

    /**
     * Class constructor
     *
     * @param string $code The valid routing number of the customer�s bank
     * @param string $number The customer�s valid bank account number
     * @param string $type The type of bank account
     * @param string $accountName The name of the bank that holds the customer�s account
     * @param string $bankName The name associated with the bank account
     * @param string $transactionType The type of electronic check transaction
     * @param string $checkNumber The check number on the customer�s paper check
     */
    public function __construct($code, $number, $type, $accountName, $bankName, $transactionType, $checkNumber='')
    {
        $this->_routingCode     = $code;
        $this->_accountNumber   = $number;
        $this->_accountType     = $type;
        $this->_accountName     = $accountName;
        $this->_bankName        = $bankName;
        $this->_transactioType  = $transactionType;
        $this->_checkNumber     = $checkNumber;
    }

    /**
     * Routing code getter
     *
     * @return string
     */
    public function getRoutingCode ()
    {
        return $this->_routingCode;
    }

    /**
     * Account number getter
     *
     * @return string
     */
    public function getAccountNumber ()
    {
        return $this->_accountNumber;
    }

    /**
     * Account type getter
     *
     * @return string
     */
    public function getAccountType ()
    {
        return $this->_accountType;
    }

    /**
     * Account name getter
     *
     * @return string
     */
    public function getAccountName ()
    {
        return $this->_accountName;
    }

    /**
     * Bank name getter
     *
     * @return string
     */
    public function getBankName ()
    {
        return $this->_bankName;
    }

    /**
     * Transaction type getter
     *
     * @return string
     */
    public function getTransactioType ()
    {
        return $this->_transactioType;
    }

    /**
     * Check number getter
     *
     * @return string
     */
    public function getCheckNumber ()
    {
        return $this->_checkNumber;
    }

}