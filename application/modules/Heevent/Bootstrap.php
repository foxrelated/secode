<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Heevent_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  protected function _bootstrap($resource = null)
  {
     // ini_set('display_errors',1);
    parent::_bootstrap($resource);
    $front = Zend_Controller_Front::getInstance();

    $front->registerPlugin(new Heevent_Plugin_Core());
    $view = Zend_Registry::get('Zend_View');
    $content = <<<CONTENT
(function(index){
  var timezones = {
    '-12':'Etc/GMT-12',
    '-11':'Pacific/Samoa',
    '-10':'Pacific/Honolulu',
    '-9':'America/Anchorage',
    '-8':'US/Pacific',
    '-7':'US/Mountain',
    '-6':'US/Central',
    '-5':'US/Eastern',
    '-4':'America/Halifax',
    '-3.3':'Canada/Newfoundland',
    '-3':'America/Buenos_Aires',
    '-2':'Atlantic/South_Georgia',
    '-1':'Atlantic/Azores',
    '0':'Europe/London',
    '1':'Europe/Berlin',
    '2':'Europe/Athens',
    '3':'Europe/Moscow',
    '3.3':'Iran',
    '4':'Asia/Dubai',
    '4.3':'Asia/Kabul',
    '5':'Asia/Yekaterinburg',
    '5.3':'Asia/Calcutta',
    '5.45':'Asia/Katmandu',
    '6':'Asia/Omsk',
    '6.3':'Indian/Cocos',
    '7':'Asia/Krasnoyarsk',
    '8':'Asia/Hong_Kong',
    '9':'Asia/Tokyo',
    '9.3':'Australia/Adelaide',
    '10':'Australia/Sydney',
    '11':'Asia/Magadan',
    '12':'Pacific/Auckland'
  };
var exdate=new Date();
exdate.setDate(exdate.getDate() + 30);
var c_value = escape(timezones[index]) + "; expires="+exdate.toUTCString();
document.cookie="timezone=" + c_value;
})('' + new Date().toTimeString().split('GMT')[1]/100);
CONTENT;

    $view->headScript()->appendScript($content);

//    $content = <<<CONTENT
//<!--[if gte IE 9]>
//  <style type="text/css">
//    body * {
//       filter: none !important;
//    }
//  </style>
//<![endif]-->
//CONTENT;
//    $view->headStyle()->appendStyle($content);

  }
}