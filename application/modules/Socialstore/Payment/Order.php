<?php


class Socialstore_Payment_Order implements Socialstore_Payment_Order_Interface
{
    const ADDRESS_SHIPPING  = 'shipping';
    const ADDRESS_BILLING   = 'billing';

    protected $_identifier;
    protected $_currency;
    protected $_total;

    protected $_tax;
    protected $_shipping;
    protected $_handling;
    protected $_insurance;
    protected $_discount;

    protected $_items        = array();
    protected $_addresses    = array();

    protected $_options;

    public function __construct($identifier, $total, $currency)
    {
        $this->_identifier  = $identifier;
        $this->_total       = $total;
        $this->_currency    = $currency;
    }

    /**
     * Identifier getter
     *
     * @return string
     */
    public function getId()
    {
        return $this->_identifier;
    }

    /**
     * Total amount getter
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->_total;
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

    /**
     * Tax amount getter
     *
     * @return float
     */
    public function getTax()
    {
        return $this->_tax;
    }

    /**
     * Tax amount setter
     *
     * @param float $tax
     * @return Socialstore_Payment_Order_Interface
     */
    public function setTax($tax)
    {
        $this->_tax = $tax;
        return $this;
    }

    /**
     * Shipping amount getter
     *
     * @return float
     */
    public function getShipping()
    {
        return $this->_shipping;
    }

    /**
     * Shipping amount setter
     *
     * @param float $shipping
     * @return Socialstore_Payment_Order_Interface
     */
    public function setShipping($shipping)
    {
        $this->_shipping = $shipping;
        return $this;
    }

    /**
     * Handling amount getter
     *
     * @return float
     */
    public function getHandling()
    {
        return $this->_handling;
    }

    /**
     * Handling amount setter
     *
     * @param float $handling
     * @return Socialstore_Payment_Order_Interface
     */
    public function setHandling($handling)
    {
        $this->_handling = $handling;
        return $this;
    }

    /**
     * Discount amount getter
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->_discount;
    }

    /**
     * Discount amount setter
     *
     * @param float $discount
     * @return Socialstore_Payment_Order_Interface
     */
    public function setDiscount($discount)
    {
        $this->_discount = $discount;
        return $this;
    }

    /**
     * Items getter
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Items setter
     *
     * @param array $items
     * @return Socialstore_Payment_Order_Interface
     */
    public function setItems(array $items)
    {
        $this->_items = $items;
        return $this;
    }

    /**
     * Add new item to request
     *
     * @param Socialstore_Payment_Order_Interface_Item $item  Item object
     * @param null|string                     $index Item unique index that can be used for item retrieval
     * @return Socialstore_Payment_Order_Interface
     */
    public function addItem(Socialstore_Payment_Order_Interface_Item $item, $index=null)
    {
        if (null === $index) {
            $index = $item->getId();
        }
        if (isset($this->_items[$index])) {
            throw new Socialstore_Payment_Exception('Item with index "'.$index.'" already exist');
        }
        $this->_items[$index] = $item;
        return $this;
    }

    /**
     * Get assigned to request item by item identifier
     *
     * @param string $index item identifier
     * @return Socialstore_Payment_Order_Interface_Item
     */
    public function getItem($index)
    {
        return $this->_items[$index];
    }

    /**
     * Addresses getter
     *
     * @return array
     */
    public function getAddresses()
    {
        return $this->_addresses;
    }

    /**
     * Addresses setter
     *
     * @param array $addresses
     * @return Socialstore_Payment_Order_Interface
     */
    public function setAddresses(array $addresses)
    {
        $this->_addresses = $addresses;
        return $this;
    }

    /**
     * Set address with type
     *
     * @param Socialstore_Payment_Order_Interface_Address $address
     * @param string                             $type
     * @return Socialstore_Payment_Order_Interface
     */
    public function setAddress(Socialstore_Payment_Order_Interface_Address $address, $type)
    {
        $this->_addresses[$type] = $address;
        return $this;
    }

    /**
     * Get address by type
     *
     * @param string $type
     * @return Socialstore_Payment_Order_Interface_Address | null
     */
    public function getAddress($type)
    {
        return isset($this->_addresses[$type]) ? $this->_addresses[$type] : null;
    }

    /**
     * Shipping address setter
     *
     * @param Socialstore_Payment_Order_Interface_Address $address
     * @return Socialstore_Payment_Order_Interface
     */
    public function setShippingAddress(Socialstore_Payment_Order_Interface_Address $address)
    {
        return $this->setAddress($address, self::ADDRESS_SHIPPING);
    }

    /**
     * Shipping address getter
     *
     * @return Socialstore_Payment_Order_Interface_Address | null
     */
    public function getShippingAddress()
    {
        return $this->getAddress(self::ADDRESS_SHIPPING);
    }

    /**
     * Billing address setter
     *
     * @param Socialstore_Payment_Order_Interface_Address $address
     * @return Socialstore_Payment_Order_Interface
     */
    public function setBillingAddress(Socialstore_Payment_Order_Interface_Address $address)
    {
        return $this->setAddress($address, self::ADDRESS_BILLING);
    }

    /**
     * Billing address getter
     *
     * @return Socialstore_Payment_Order_Interface_Address | null
     */
    public function getBillingAddress()
    {
        return $this->getAddress(self::ADDRESS_BILLING);
    }

    /**
     * Order options setter
     *
     * @param Socialstore_Payment_Options $options
     * @return Socialstore_Payment_Order_Interface
     */
    public function setOptions($options)
    {
    	if(is_array($options)){
    		$options =  new Socialstore_Payment_Options($options);
    	}
        $this->_options = $options;
        return $this;
    }

    /**
     * Order options getter
     *
     * @return Socialstore_Payment_Options
     */
    public function getOptions()
    {
        return $this->_options;
    }
}