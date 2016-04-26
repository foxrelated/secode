<?php

/**
 * Unified payment response
 */
class Socialstore_Payment_Response implements Socialstore_Payment_Response_Interface
{
    const STATUS_APPROVED   = 'approved';
    const STATUS_DECLINED   = 'declined';
    const STATUS_PENDING    = 'pending';
    const STATUS_ERROR      = 'error';

    /**
     * Response status
     * @var string
     */
    protected $_status;

    /**
     * Response messages
     * @var array
     */
    protected $_messages = array();

    /**
     * Data options
     *
     * @var Socialstore_Payment_Options
     */
    protected $_options;

    /**
     * Response constructor
     *
     * @param string $status
     */
    public function __construct($status)
    {
        $this->_status = $status;
    }

    /**
     * Set response messages
     *
     * @param string|array $messages
     */
    public function setMessages($messages)
    {
        if (is_array($messages)) {
            $this->_messages = $messages;
        } elseif (!empty($messages)) {
            $this->_messages = array($messages);
        }
        return $this;
    }

    /**
     * Get response messages
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Get all messages as one string
     *
     * @param string $delimiter messages delimiter
     * @return string
     */
    public function getMessage($delimiter = ',')
    {
        return implode($delimiter, $this->_messages);
    }

    /**
     * Check if response is successful
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->_status === self::STATUS_APPROVED;
    }

    /**
     * Response status getter
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Options setter
     *
     * @param Socialstore_Payment_Options $info
     * @return Socialstore_Payment_Response
     */
    public function setOptions(Socialstore_Payment_Options $options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Options getter
     *
     * @return null|Socialstore_Payment_Options
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Option value getter
     *
     * @param string $code
     * @return mixed
     */
    public function getOption($code)
    {
        if ($this->_options && $this->_options->has($code)) {
            return $this->_options->get($code);
        }
        return null;
    }

    /**
     * Transaction getter
     *
     * Instantiate transaction object based on response transaction_id,
     * amount and currency options.
     *
     * @return Socialstore_Payment_Transaction | null
     */
    public function getTransaction()
    {
        $transaction = $this->getOption('transaction_id');
        if ($transaction) {
            $transaction = new Socialstore_Payment_Transaction($transaction);
            $transaction->setAmount($this->getOption('amount'))
                ->setCurrency($this->getOption('currency'));
        }
        return $transaction;
    }
}
