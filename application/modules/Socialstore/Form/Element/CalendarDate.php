<?php

class Socialstore_Form_Element_CalendarDate extends Zend_Form_Element_Xhtml
{
  public $helper = 'formCalendarDate';
  public $ignoreValid;
  protected $_yearMin;
  protected $_yearMax;
  protected $_dayOptions;
  protected $_monthOptions;
  protected $_yearOptions;
  protected $_minuteOptions;
  protected $_hourOptions;
  protected $_useMilitaryTime;


  public function init()
  {
    $localeObject = Zend_Registry::get('Locale');
    $dateLocaleString = $localeObject->getTranslation('long', 'Date', $localeObject);
    $dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
    $dateLocaleString = strtolower($dateLocaleString);
    $dateLocaleString = preg_replace('/[^ymd]/i', '', $dateLocaleString);
    $dateLocaleString = preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('y', 'm', 'd'), $dateLocaleString);
    $this->dateFormat = $dateLocaleString;
    $this->useMilitaryTime = $this->_useMilitaryTime();
  }

  /**
   * Load default decorators
   *
   * @return void
   */
  public function loadDefaultDecorators()
  {
    if( $this->loadDefaultDecoratorsIsDisabled() ) {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) ) {
      $this->addDecorator('ViewHelper');
      Engine_Form::addDefaultDecorators($this);
    }
  }


  
  // Options
  
  public function setMultiOptions($options)
  {
    return $this;
  }

  public function getMultiOptions()
  {
    if( empty($this->options) ) {
      $this->options = array(
        'year' => $this->getYearOptions(),
        'month' => $this->getMonthOptions(),
        'day' => $this->getDayOptions(),
      );
    }    
    return $this->options;
  }
  // Year

  public function setYearMin($min)
  {
    $this->_yearMin = (int) $min;
    return $this;
  }
  
  public function getYearMin()
  {
    // Default is 100 years ago
    if( null === $this->_yearMin ) {
      $date = new Zend_Date();
      $this->_yearMin = (int) $date->get(Zend_Date::YEAR) - 100;
    }
    return $this->_yearMin;
  }

  public function setYearMax($max)
  {
    $this->_yearMax = $max;
    return $this;
  }

  public function getYearMax()
  {
    // Default is this year
    if( null === $this->_yearMax ) {
      $date = new Zend_Date();
      $this->_yearMax = (int) $date->get(Zend_Date::YEAR);
    }
    return $this->_yearMax;
  }

  public function getYearOptions()
  {
    if( null === $this->_yearOptions ) {
      $this->_yearOptions = array();
      if( $this->getAllowEmpty() ) {
        $this->_yearOptions[''] = '';
      }
      for( $i = $this->getYearMax(), $m = $this->getYearMin(); $i > $m; $i-- ) {
        $this->_yearOptions[$i] = (string) $i;
      }
    }
    return $this->_yearOptions;
  }



  // Month
  
  public function getMonthOptions()
  {
    if( null === $this->_monthOptions ) {
      $this->_monthOptions = array();
      if( $this->getAllowEmpty() ) {
        $this->_monthOptions[''] = '';
      }

      // Prepare month names
      $localeObject = Zend_Registry::get('Locale');
      $months = Zend_Locale::getTranslationList('months', $localeObject);
      $months = $months['format'][$months['default']];
      
      for( $i = 1; $i <= 12; $i++ ) {
        $this->_monthOptions[$i] = $months[$i];
      }
    }
    return $this->_monthOptions;
  }



  // Day

  public function getDayOptions()
  {
    if( null === $this->_dayOptions ) {
      $this->_dayOptions = array();
      if( $this->getAllowEmpty() ) {
        $this->_dayOptions[''] = '';
      }
      
      for( $i = 1; $i <= 31; $i++ ) {
        $this->_dayOptions[$i] = $i;
      }
    }
    return $this->_dayOptions;
  }

  // Value/valid
  
  public function setValue($value)
  {
    if( is_array($value) ) {
      // Process date
      $year = null;
      $month = null;
      $day = null;
      if( isset($value['date']) && preg_match('/^(\d+)\/(\d+)\/(\d+)$/', $value['date'], $m) ) {
        array_shift($m);
        $year = $m[stripos($this->dateFormat, 'y')];
        $month = $m[stripos($this->dateFormat, 'm')];
        $day = $m[stripos($this->dateFormat, 'd')];
      } else {
        if( isset($value['year']) && is_numeric($value['year']) ) {
          $year = $value['year'];
        }
        if( isset($value['month']) && is_numeric($value['month']) ) {
          $month = $value['month'];
        }
        if( isset($value['day']) && is_numeric($value['day']) ) {
          $day = $value['day'];
        }
      }

      $valueString = sprintf($formatString, $year, $month, $day);

      $value = $valueString;
    }
    
    return parent::setValue($value);
  }

  public function getValue()
  {
    return parent::getValue();
  }

  public function isValid($value, $context = null)
  {
    // Empty
    if( $this->getAllowEmpty() && (empty($value) || (is_array($value) && 0 == count(array_filter($value)))) ) {
      return parent::isValid($value, $context);
    }

    $this->setValue($value);
    $value = $this->getValue();

    // Normal processing
    if( is_string($value) ) {
      if( preg_match('/^(\d{4})-(\d{2})-(\d{2})( (\d{2}):(\d{2})(:(\d{2}))?)?$/', $value, $m) ) {
        $year = $m[1];
        $month = $m[2];
        $day = $m[3];
      } else {
        $this->addError('Please select a date from the calendar.');
        return false;
      }
    } else if( is_array($value) ) {
      $m = explode('/', $value['date']);
      if( count($m) === 3 ) {
        $year = $m[stripos($this->dateFormat, 'y')];
        $month = $m[stripos($this->dateFormat, 'm')];
        $day = $m[stripos($this->dateFormat, 'd')];
      } else {
        $year = null;
        $month = null;
        $day = null;
      }

    }

    // Check validity
    if( !$year || !$month || !$day ) {
      $this->addError('Please select a date from the calendar.');
      return false;
    }

    return parent::isValid($value, $context);
  }

  protected function _useMilitaryTime()
  {
    if( null === $this->_useMilitaryTime ) {
      $localeObject = Zend_Registry::get('Locale');
      $this->_useMilitaryTime = ( stripos($localeObject->getTranslation(array("gregorian", "short"), 'time', $localeObject), 'a') === false );
    }

    return $this->_useMilitaryTime;
  }
}