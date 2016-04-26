<?php

class Zend_Payment_Data_Order_Address implements Socialstore_Payment_Order_Address_Interface
{
    /* Name */
    protected $_suffix;
    protected $_firstName;
    protected $_middleName;
    protected $_lastName;
    protected $_prefix;

    /* Address */
    protected $_country;
    protected $_region; //?
    protected $_city;
    protected $_street;
    protected $_secondStreet;
    protected $_postCode;

    /* Contacts */
    protected $_phone;
    protected $_fax;
    protected $_email;
    protected $_company;

    /**
     * Address constructor
     *
     * @param string        $fName      first name
     * @param string        $lName      last name
     * @param string        $country    country
     * @param string        $region     country region name
     * @param string        $city       city name
     * @param string|array  $street     street and house
     * @param string        $postcode   post|zip code
     */
    public function __construct($fName, $lName, $country, $region, $city, $street, $postcode = '')
    {
        $this->_firstName   = $fName;
        $this->_lastName    = $lName;
        $this->_country     = $country;
        $this->_region      = $region;
        $this->_city        = $city;
        $this->_postCode    = $postcode;
        $this->setStreet($street);
    }

    /**
     * Set address street
     *
     * @param string|array  $street street and house number
     * @return Zend_Payment_Data_Address
     */
    public function setStreet($street)
    {
        if (is_array($street)) {
            $this->_street  = array_shift($street);
            $this->_secondStreet = empty($street) ? null : array_shift($street);
        } else {
            $this->_street  = $street;
            $this->_secondStreet = null;
        }
        return $this;
    }

    /**
     * Name suffix getter
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->_suffix;
    }

    /**
     * Name suffix setter
     *
     * @param string $suffix
     * @return Zend_Payment_Data_Address
     */
    public function setSuffix($suffix)
    {
        $this->_suffix = $suffix;
    }

    /**
     * Middle name getter
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->_middleName;
    }

    /**
     * Middle name setter
     *
     * @param string $middName
     * @return Zend_Payment_Data_Address
     */
    public function setMiddleName($middName)
    {
        $this->_middleName = $middName;
        return $this;
    }

    /**
     * Name prefix getter
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Name prefix setter
     *
     * @param string $prefix
     * @return Zend_Payment_Data_Address
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
        return $this;
    }

    /**
     * Postal code/zip code getter
     *
     * @return string
     */
    public function getPostCode()
    {
        return $this->_postCode;
    }

    /**
     * Postal code/zip code setter
     *
     * @param string $postCode
     * @return Zend_Payment_Data_Address
     */
    public function setPostCode($postCode)
    {
        $this->_postCode = $postCode;
        return $this;
    }

    /**
     * Phone number getter
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->_phone;
    }

    /**
     * Phone number setter
     *
     * @param string $phone
     * @return Zend_Payment_Data_Address
     */
    public function setPhone($phone)
    {
        $this->_phone = $phone;
        return $this;
    }

    /**
     * Fax getter
     *
     * @return string
     */
    public function getFax()
    {
        return $this->_fax;
    }

    /**
     * Fax setter
     *
     * @param string $fax
     * @return Zend_Payment_Data_Address
     */
    public function setFax($fax)
    {
        $this->_fax = $fax;
        return $this;
    }

    /**
     * Company getter
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->_fax;
    }

    /**
     * Company setter
     *
     * @param string $company
     * @return Zend_Payment_Data_Address
     */
    public function setCompany($company)
    {
        $this->_company = $company;
        return $this;
    }

    /**
     * Email address getter
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Email address setter
     *
     * @param string $email
     * @return Zend_Payment_Data_Address
     */
    public function setEmail($email)
    {
        $this->_email = $email;
        return $this;
    }

    /**
     * First name getter
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->_firstName;
    }

    /**
     * Last name getter
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->_lastName;
    }

    /**
     * Country getter
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->_country;
    }

    /**
     * Region getter
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->_region;
    }

    /**
     * City getter
     *
     * @return string
     */
    public function getCity()
    {
        return $this->_city;
    }

    /**
     * Street getter
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->_street;
    }

    /**
     * Street 2 getter
     *
     * @return string
     */
    public function getStreet2()
    {
        return $this->_secondStreet;
    }

    /**
     * Get full street data concatenated with separator
     *
     * @param string $separator
     * @return string
     */
    public function getFullStreet($separator=' ')
    {
        $street = $this->_street;
        if ($this->_secondStreet) {
            $street .= $separator . $this->_secondStreet;
        }
        return $street;
    }

    /**
     * Get concatenation of all name properties
     *
     * @param string $separator
     * @return string
     */
    public function getFullName($separator=' ')
    {
        $name = $this->_prefix ? $this->_prefix . $separator : '';
        $name.= $this->_firstName ? $this->_firstName . $separator : '';
        $name.= $this->_middleName ? $this->_middleName . $separator : '';
        $name.= $this->_lastName ? $this->_lastName . $separator : '';
        $name.= $this->_suffix ? $this->_suffix . $separator : '';
        return $name;
    }
}