<?php

interface Socialstore_Payment_Order_Interface{
	
	public function getPlugin();
	/**
	 * pay type follow
	 * @return string
	 */
	public function getPaytype();
	/**
     * Identifier getter
     *
     * @return string
     */
    public function getId();


	/**
     * Tax amount getter
     *
     * @return decimal (16,2)
     */
    public function getTaxAmount();


    /**
     * Shipping amount getter
     *
     * @return decimal (16,2)
     */
    public function getShippingAmount();

    /**
     * Handling amount getter
     *
     * @return decimal (16,2)
     */
    public function getHandlingAmount();

    /**
     * Discount amount getter
     *
     * @return decimal (16,2)
     */
    public function getDiscountAmount();
	
	/**
	 * get commission amount
	 * @return decimal (16,2)
	 */
	public function getCommissionAmount();
	
    /**
     * Total amount getter
     *
     * @return decimal (16,2)
     */
    public function getTotalAmount();
	
	/**
	 * get sub total amount ( the amount that not included tax amount)
	 * @return decimal
	 */
	public function getSubAmount();

    /**
     * Currency getter
     *
     * @return string   char(3)
     */
    public function getCurrency();


	public function getState();
    /**
     * Items getter
     *
     * @return array of Socialstore_Payment_Order_Item
     */
    public function getItems();

    /**
     * Get assigned to request item by item identifier
     *
     * @param string $index item identifier
     * @return Socialstore_Payment_Order_Item_Interface
     */
    public function getItem($index);

    /**
     * Shipping address getter
     *
     * @return Socialstore_Payment_Order_Interface_Address | null
     */
    public function getShippingAddress();

    

    /**
     * Billing address getter
     *
     * @return Socialstore_Payment_Order_Interface_Address | null
     */
    public function getBillingAddress();

    
    /**
     * Order options getter
     *
     * @return Socialstore_Payment_Options
     */
    public function getOptions();
	
	
	/**
	 * add item to order
	 */	
	public function addItem($item, $qty, $params);
	
	/**
	 * update order
	 */
	public function updateOrder();
}
