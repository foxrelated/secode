<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_Api_Core extends Core_Api_Abstract {

    public function getParams($params) {

        //$customCheck = Engine_Api::_()->getDbTable('modules', 'nestedcomment')->fetchRow(array('resource_type = ?' => $params['resource_type']));

        $defaultArray = array('resource_type = ?' => $params['resource_type']);
        if (isset($params['moduleName'])) {
            $defaultArray = array_merge($defaultArray, array('module = ?' => $params['moduleName']));
        }

        $customCheck = Engine_Api::_()->getDbTable('modules', 'nestedcomment')->fetchRow($defaultArray);

        if ($customCheck && !$customCheck->params) {
            return '';
        } elseif($customCheck){
            return Zend_Json_Decoder::decode($customCheck->params);
        }
        return '';
    }

    public function getEnabledModule($params) {
        $defaultArray = array('resource_type = ?' => $params['resource_type']);
        if (isset($params['moduleName'])) {
            $defaultArray = array_merge($defaultArray, array('module = ?' => $params['moduleName']));
        }

        $customCheck = Engine_Api::_()->getDbTable('modules', 'nestedcomment')->fetchRow($defaultArray);

        if (isset($params['checkModuleExist'])) {
            return $customCheck;
        }

        if ($customCheck) {
            if (Engine_Api::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                return $customCheck->enabled;
            } else {
                if (Engine_Api::_()->hasModuleBootstrap('sitemobile') && Engine_Api::_()->sitemobile()->isSupportedModule('nestedcomment')) {
                    
                    if(Engine_Api::_()->seaocore()->isSitemobileApp()) {
                        return 0;
                    }
                    return $customCheck->enabled;
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }

    /**
     * Plugin which return the error, if Siteadmin not using correct version for the plugin.
     *
     */
    public function isModulesSupport() {
        $isNestedCommentActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.isActivate', 0);
        if (empty($isNestedCommentActivate))
            return array();

        $modArray = array(
            'sitepage' => '4.8.6p6',
            'sitebusiness' => '4.8.6p4',
            'sitegroup' => '4.8.6p5',
            'sitestore' => '4.8.6p9',
            'siteevent' => '4.8.6p7',
            'siteeventdocument' => '4.8.2p2',
            'sitealbum' => '4.8.6p15',
            'sitemember' => '4.8.6p15',
            'sitereview' => '4.8.6p6',
            'sitevideoview' => '4.8.6',
            'document' => '4.8.6p1',
            'recipe' => '4.8.6p2',
            'list' => '4.8.6p2',
        );
        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
            if (!empty($isModEnabled)) {
                $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
                $product_version = $value;
                $running_version = $getModVersion->version;
                $shouldUpgrade = false;
                if (!empty($running_version) && !empty($product_version)) {
                    $temp_running_verion_2 = $temp_product_verion_2 = 0;
                    if (strstr($product_version, "p")) {
                        $temp_starting_product_version_array = @explode("p", $product_version);
                        $temp_product_verion_1 = $temp_starting_product_version_array[0];
                        $temp_product_verion_2 = $temp_starting_product_version_array[1];
                    } else {
                        $temp_product_verion_1 = $product_version;
                    }
                    $temp_product_verion_1 = @str_replace(".", "", $temp_product_verion_1);


                    if (strstr($running_version, "p")) {
                        $temp_starting_running_version_array = @explode("p", $running_version);
                        $temp_running_verion_1 = $temp_starting_running_version_array[0];
                        $temp_running_verion_2 = $temp_starting_running_version_array[1];
                    } else {
                        $temp_running_verion_1 = $running_version;
                    }
                    $temp_running_verion_1 = @str_replace(".", "", $temp_running_verion_1);


                    if (($temp_running_verion_1 < $temp_product_verion_1) || (($temp_running_verion_1 == $temp_product_verion_1) && ($temp_running_verion_2 < $temp_product_verion_2))) {
                        $shouldUpgrade = true;
                    }
                }
                if (!empty($shouldUpgrade)) {
                    $finalModules[] = $getModVersion->title;
                }
            }
        }
        return $finalModules;
    }

    public function getCommentWidgetParams($params) {
        $mode = Engine_API::_()->sitemobile()->getViewMode();
        if (Engine_API::_()->seaocore()->isSitemobileApp()) {
            if ($mode == 'mobile-mode') {
                $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitemobileapp');
                $contentTableName = Engine_Api::_()->getDbtable('content', 'sitemobileapp')->info('name');
            } else {
                $pagesTable = Engine_Api::_()->getDbtable('tabletpages', 'sitemobileapp');
                $contentTableName = Engine_Api::_()->getDbtable('tabletcontent', 'sitemobileapp')->info('name');
            }
        } else {
            if ($mode == 'mobile-mode') {
                $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitemobile');
                $contentTableName = Engine_Api::_()->getDbtable('content', 'sitemobile')->info('name');
            } else {
                $pagesTable = Engine_Api::_()->getDbtable('tabletpages', 'sitemobile');
                $contentTableName = Engine_Api::_()->getDbtable('tabletcontent', 'sitemobile')->info('name');
            }
        }

        $pagesTableName = $pagesTable->info('name');
        switch ($params['resource_type']):
            case 'blog':
                $name = 'blog_index_view';
                break;
            case 'album_photo':
                $name = 'album_photo_view';
                break;
            case 'album':
                $name = 'album_album_view';
                break;
            case 'event_photo':
                $name = 'event_photo_view';
                break;
            case 'forum':
                $name = 'forum_forum_view';
                break;
            case 'group':
                $name = 'group_index_view';
                break;
            case 'group_photo':
                $name = 'group_photo_view';
                break;
            case 'music_playlist':
                $name = 'music_playlist_view';
                break;
            case 'poll':
                $name = 'poll_poll_view';
                break;
            case 'video':
                $name = 'video_index_view';
                break;
        endswitch;

        if (isset($params['resource_type'])) {
            $params = $pagesTable->select()
                    ->setIntegrityCheck(false)
                    ->from($pagesTableName, null)
                    ->join($contentTableName, "$pagesTableName . page_id = $contentTableName . page_id", 'params')
                    ->where("$pagesTableName.name =?", $name)
                    ->where("$contentTableName.name =?", 'sitemobile.comments')
                    ->query()
                    ->fetchColumn();
            if ($params) {
                return Zend_Json_Decoder::decode($params);
            } else {
                $params = '{"title":"","taggingContent":["friends"],"showComposerOptions":["addSmilies","addPhoto"],"showAsNested":"1","showAsLike":"1","showDislikeUsers":"0","showLikeWithoutIcon":"0","showLikeWithoutIconInReplies":"0","name":"sitemobile.comments"}';
                return Zend_Json_Decoder::decode($params);
            }
        }
    }
    public function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }
}
