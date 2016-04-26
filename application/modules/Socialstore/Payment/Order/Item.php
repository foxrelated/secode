<?php

class Zend_Payment_Data_Order_Item
{
    protected $_itemId;
    protected $_name;
    protected $_price;
    protected $_qty;

    protected $_description;
    protected $_tax;
    protected $_weight;
    protected $_width;
    protected $_height;
    protected $_lenght;
    protected $_url;

    /**
     * Item constructor
     *
     * @param string $id
     * @param string $name
     * @param float  $price
     * @param float  $qty
     */
    public function __construct($itemId, $name, $price, $qty)
    {
        $this->_itemId  = $itemId;
        $this->_name    = $name;
        $this->_price   = $price;
        $this->_qty     = $qty;
    }

    /**
     * Identifier getter
     *
     * @return string
     */
    public function getId()
    {
        return $this->_itemId;
    }

    /**
     * Name getter
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Price getter
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->_price;
    }

    /**
     * Qty getter
     *
     * @return float
     */
    public function getQty()
    {
        return $this->_qty;
    }

    /**
     * Description setter
     *
     * @param string $description
     * @return Zend_Payment_Data_Order_Item
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * Description getter
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Tax setter
     *
     * @param float $tax
     * @return Zend_Payment_Data_Order_Item
     */
    public function setTax($tax)
    {
        $this->_tax = $tax;
        return $this;
    }

    /**
     * Tax getter
     *
     * @return float
     */
    public function getTax()
    {
        return $this->_tax;
    }

    /**
     * Weight setter
     *
     * @param float $weight
     * @return Zend_Payment_Data_Order_Item
     */
    public function setWeight($weight)
    {
        $this->_weight = $weight;
        return $this;
    }

    /**
     * Weight getter
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->_weight;
    }

    /**
     * Width setter
     *
     * @param float $width
     * @return Zend_Payment_Data_Order_Item
     */
    public function setWidth($width)
    {
        $this->_width = $width;
        return $this;
    }

    /**
     * Width getter
     *
     * @return float
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * Height setter
     *
     * @param float $height
     * @return Zend_Payment_Data_Order_Item
     */
    public function setHeight($height)
    {
        $this->_height = $height;
        return $this;
    }

    /**
     * Height getter
     *
     * @return float
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * Length setter
     *
     * @param float $length
     * @return Zend_Payment_Data_Order_Item
     */
    public function setLength($length)
    {
        $this->_lenght = $length;
        return $this;
    }

    /**
     * Length getter
     *
     * @return float
     */
    public function getLength()
    {
        return $this->_lenght;
    }

    /**
     * Url setter
     *
     * @param string $url
     * @return Zend_Payment_Data_Order_Item
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * Url getter
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }
}