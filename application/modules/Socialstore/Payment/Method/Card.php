<?php

class Socialstore_Payment_Method_Card
{
	
	public static function getTypes(){
		return array(
			'AE'=>'American Express',
			'VI'=>'VISA',
			'MA'=>'Master Card',
			'DI'=>'Discovery'
		);			
	}
	
	
    protected $_type;
    protected $_number;
    protected $_expireMonth;
    protected $_expireYear;

    protected $_cvv;

    /**
     * Maestro and Solo card specific
     */
    protected $_startMonth;
    protected $_startYear;
    protected $_issueNumber;

    /**
     * Card constructor
     *
     * @param string $type
     * @param string $number
     * @param int    $month
     * @param int    $year
     */
    public function __construct($type, $number, $month, $year)
    {
        $this->_type        = $type;
        $this->_number      = $number;
        $this->_expireMonth = $month;
        $this->_expireYear  = $year;
    }

    /**
     * Card type getter
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Credit card number getter
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->_number;
    }

    /**
     * Expiration date getter
     *
     * @param string $format output format, same as for php date function
     * @return string
     */
    public function getExpirationDate($format='my')
    {
        return date($format, mktime(0, 0, 0, $this->_expireMonth, 1, $this->_expireYear));
    }

    /**
     * CVV code setter
     *
     * @param string $cvv cvv code
     * @return Zend_Payment_Data_Card
     */
    public function setCvv($cvv)
    {
        $this->_cvv = $cvv;
        return $this;
    }

    /**
     * CVV code getter
     *
     * @return string
     */
    public function getCvv()
    {
        return $this->_cvv;
    }

    public function setStartMonth($month)
    {
        $this->_startMonth = $month;
        return $this;
    }

    public function setStartYear($year)
    {
        $this->_startYear = $year;
        return $this;
    }

    public function getStartDate($format='%02d%02d')
    {
        return sprintf($format, $this->_startMonth, $this->_startYear);;
    }

    public function setIssueNumber($number)
    {
        $this->_issueNumber = $number;
        return $this;
    }

    public function getIssueNumber()
    {
        return $this->_issueNumber;
    }
}