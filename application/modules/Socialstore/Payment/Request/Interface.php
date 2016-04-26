<?php


interface Socialstore_Payment_Request_Interface
{
    /**
     * Request action getter
     *
     * @return string
     */
    public function getAction();

    /**
     * Method getter
     *
     * @return Socialstore_Payment_Method_Interface
     */
    public function getMethod();

    /**
     * Request order getter
     *
     * @return Socialstore_Payment_Order_Interface
     */
    public function getOrder();

    /**
     * Order setter
     *
     * @param Socialstore_Payment_Order_Interface $order
     */
    public function setOrder(Socialstore_Payment_Order_Interface $order);

    /**
     * Request transaction getter
     *
     * @return Socialstore_Payment_Transaction
     */
    public function getTransaction();

    /**
     * Transaction setter
     *
     * @param Socialstore_Payment_Transaction $transaction
     */
    public function setTransaction(Socialstore_Payment_Transaction $transaction);

    /**
     * Request options getter
     *
     * @return Socialstore_Payment_Options
     */
    public function getOptions();

    /**
     * Options setter
     *
     * @param Socialstore_Payment_Options|array $options
     */
    public function setOptions($options);

    /**
     * Request advanced options getter
     *
     * @return array
     */
    public function getAdvancedOptions();
}