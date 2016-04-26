<?php

interface Socialstore_Payment_Order_Item_Interface{

	/**
	 * @return string
	 */
    public function getId();

    /**
     * Name getter
     *
     * @return string
     */
    public function getName();

    /**
     * get total amount of this item.
     * @return decimal (16,2)
     */
    public function getPrice();

	
	public function getItemTaxAmount();
	/**
     * Tax getter
     *
     * @return decimal (16,2)
     */
    public function getTaxAmount();
	
	/**
	 * get shiping amount
	 * @return decimal (16,2)
	 */
	public function getShippingAmount();
	
	/**
	 * get handing amount
	 * @return decimal (16,2)
	 */
	public function getHandlingAmount();
	
	/**
	 * get discount amount
	 * @return decimal
	 */
	public function getDiscountAmount();

	
	/**
	 * get commission amount
	 * @return decimal (16,2)
	 */
	public function getCommissionAmount();
	
	/**
	 * get total amount
	 */
	public function getTotalAmount();
	
	/**
	 * 
	 */	
	public function getCurrency();
	
	
    /**
     * Qty getter
     *
     * @return numeric
     */
    public function getQty();
	

    /**
     * Description getter
     *
     * @return string
     */
    public function getDescription();
	
    /**
     * Weight getter
     *
     * @return float
     */
    public function getWeight();

    /**
     * Width getter
     *
     * @return float
     */
    public function getWidth();
	

    /**
     * Height getter
     *
     * @return float
     */
    public function getHeight();


    /**
     * Length getter
     *
     * @return float
     */
    public function getLength();
	
	/**
	 * get sub total amount ( not included tax amount)
	 * @return decimal
	 */
	public function getSubAmount();
	
    /**
     * Url getter
     *
     * @return string
     */
    public function getUrl();
	
	public function updateItem();
}