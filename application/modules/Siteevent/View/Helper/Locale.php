<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locale.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_View_Helper_Locale extends Engine_View_Helper_Locale {

    public function toEventDateTime($date, $options = array()) {
        $options = array_merge(array(
            'locale' => $this->getLocale(),
            'size' => 'long',
            'type' => 'datetime',
            'timezone' => Zend_Registry::get('timezone'),
                ), $options);

        $date = $this->_checkDateTime($date, $options);
        if (!$date) {
            return false;
        }

        if (empty($options['format'])) {
            if (substr($options['locale']->__toString(), 0, 2) == 'en' &&
                    $options['size'] == 'long' &&
                    $options['type'] == 'datetime') {
                $options['format'] = 'MMMM d, y h:mm a z';
            } else {
                $options['format'] = Zend_Locale_Data::getContent($options['locale'], $options['type'], $options['size']);
            }
        }
        if( empty($options['format']) ) {
          $options['type'] = 'date';
          $options['format'] = Zend_Locale_Data::getContent($options['locale'], $options['type'], $options['size']);
        }
        
        // Hack for weird usage of L instead of M in Zend_Locale
        $options['format'] = str_replace('L', 'M', $options['format']);
        $options['format'] = str_replace(":ss", "", $options['format']);
        
        $showTimeZone = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.timezone', 1);
        if( empty($showTimeZone) ) {
          $options['format'] = str_replace("zzzz", "", $options['format']);
          $options['format'] = str_replace("z", "", $options['format']);
        }

        $str = $date->toString($options['format'], $options['locale']);
        $str = $this->convertNumerals($str, $options['locale']);
        return $str;
    }

    public function toEventTime($date, $options = array()) {
        $options = array_merge(array(
            'locale' => $this->getLocale(),
            'size' => 'short',
            'type' => 'time',
            'timezone' => Zend_Registry::get('timezone'),
                ), $options);

        $date = $this->_checkDateTime($date, $options);
        if (!$date) {
            return false;
        }

        if (empty($options['format'])) {
            $options['format'] = Zend_Locale_Data::getContent($options['locale'], $options['type'], $options['size']);
        }
        // Hack for weird usage of L instead of M in Zend_Locale
        $options['format'] = str_replace('L', 'M', $options['format']);
        $options['format'] = str_replace(":ss", "", $options['format']);
        
        $showTimeZone = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.timezone', 1);
        if( empty($showTimeZone) ) {
          $options['format'] = str_replace("zzzz", "", $options['format']);
          $options['format'] = str_replace("z", "", $options['format']);
        }

        $str = $date->toString($options['format'], $options['locale']);
        $str = $this->convertNumerals($str, $options['locale']);
        return $str;
    }

    //GET MILITARY TIME.
    public function useMilitaryTime() {

        $localeObject = Zend_Registry::get('Locale');
        $useMilitaryTime = ( stripos($localeObject->getTranslation(array("gregorian", "short"), 'time', $localeObject), 'a') === false );
        return $useMilitaryTime;
    }

    //GET Date TIME Format.
    public function useDateLocaleFormat() {

        $localeObject = Zend_Registry::get('Locale');
        $dateLocaleString = $localeObject->getTranslation('long', 'Date', $localeObject);
        $dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
        $dateLocaleString = strtolower($dateLocaleString);
        $dateLocaleString = preg_replace('/[^ymd]/i', '', $dateLocaleString);
        $dateLocaleString = preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('y', 'm', 'd'), $dateLocaleString);
        return $dateLocaleString;
    }

}
