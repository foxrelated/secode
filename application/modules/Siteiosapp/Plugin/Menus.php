<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Menus.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteiosapp_Plugin_Menus {

    public function appBuildUrls($row) {
        $params = $row->params;

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        return array(
            'uri' => $view->url(array('module' => 'siteiosapp', 'controller' => 'app-builder', 'action' => 'create', 'tab' => $params['tab']), 'admin_default', false)
        );
    }

    public function mltMapping($row) {
        return false;

//        $params = $row->params;
//        $listingModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview');
//
//        if ($listingModuleEnabled) {
//            $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitereview');
//            $isModSupport = $this->_checkVersion($getModVersion->version, '4.8.10p6');
//            if (isset($getModVersion) && $isModSupport) {
//                return $params;
//            } else {
//                return false;
//            }
//        } else {
//            return false;
//        }
    }

//    private function _checkVersion($databaseVersion, $checkDependancyVersion) {
//        $f = $databaseVersion;
//        $s = $checkDependancyVersion;
//        if (strcasecmp($f, $s) == 0)
//            return -1;
//
//        $fArr = explode(".", $f);
//        $sArr = explode('.', $s);
//        if (count($fArr) <= count($sArr))
//            $count = count($fArr);
//        else
//            $count = count($sArr);
//
//        for ($i = 0; $i < $count; $i++) {
//            $fValue = $fArr[$i];
//            $sValue = $sArr[$i];
//            if (is_numeric($fValue) && is_numeric($sValue)) {
//                if ($fValue > $sValue)
//                    return 1;
//                elseif ($fValue < $sValue)
//                    return 0;
//                else {
//                    if (($i + 1) == $count) {
//                        return -1;
//                    } else
//                        continue;
//                }
//            }
//            elseif (is_string($fValue) && is_numeric($sValue)) {
//                $fsArr = explode("p", $fValue);
//
//                if ($fsArr[0] > $sValue)
//                    return 1;
//                elseif ($fsArr[0] < $sValue)
//                    return 0;
//                else {
//                    return 1;
//                }
//            } elseif (is_numeric($fValue) && is_string($sValue)) {
//                $ssArr = explode("p", $sValue);
//
//                if ($fValue > $ssArr[0])
//                    return 1;
//                elseif ($fValue < $ssArr[0])
//                    return 0;
//                else {
//                    return 0;
//                }
//            } elseif (is_string($fValue) && is_string($sValue)) {
//                $fsArr = explode("p", $fValue);
//                $ssArr = explode("p", $sValue);
//                if ($fsArr[0] > $ssArr[0])
//                    return 1;
//                elseif ($fsArr[0] < $ssArr[0])
//                    return 0;
//                else {
//                    if ($fsArr[1] > $ssArr[1])
//                        return 1;
//                    elseif ($fsArr[1] < $ssArr[1])
//                        return 0;
//                    else {
//                        return -1;
//                    }
//                }
//            }
//        }
//    }
}
