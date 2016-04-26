<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns 
 * @author     SocialEngineAddOns
 */
class Sitegroup_Api_Core extends Core_Api_Abstract {

    protected $_privacy = array();  // $_privacy['guid']['user_id']['privacy_type']=$privacy;

    /**
     * Privacy settings base on level and package
     *
     * @param object $sitegroup
     * @param string $privacy_type
     * @return int $is_manage_admin
     */

    public function isManageAdmin($sitegroup, $privacy_type) {
        if (empty($sitegroup) || empty($privacy_type))
            return 0;
        $group_id = $sitegroup->group_id;
        //group is declined then not edit group
        if (!empty($sitegroup->declined) && ($privacy_type == "edit")) {
            return 0;
        }

        if ($privacy_type == "view" && !$this->canViewGroup($sitegroup)) {
            return 0;
        }

        if ($this->hasPackageEnable()) {

            $packageInclude = array("tellafriend" => "tfriend", "print" => "print", "overview" => "overview", "map" => "map", "insights" => "insight", /* "layout" => "layout", */ "contact_details" => "contact", "profile" => "profile", "sendupdate" => "sendupdate", "twitter" => "twitter");
            $packageOwnerModules = array("sitegroupoffer" => "offer", "sitegroupform" => "form", "sitegroupinvite" => "invite", "sitegroupbadge" => "badge", "sitegrouplikebox" => "likebox", "sitegroupmember" => "smecreate");
            $subModules = array("sitegroupdocument" => "sdcreate", "sitegroupnote" => "sncreate", "sitegrouppoll" => "splcreate", "sitegroupevent" => "secreate", "sitegroupvideo" => "svcreate", "sitegroupalbum" => "spcreate", "sitegroupmusic" => "smcreate");

            // $packageSubModule = $this->getEnableSubModules();
            //non sub modules
            $search_Key = array_search($privacy_type, $packageInclude);
            if (!empty($search_Key)) {
                return $this->allowPackageContent($sitegroup->package_id, $search_Key);
            }

            //owner base submodules
            $packageOwnerSubModule = @array_search($privacy_type, $packageOwnerModules);
            if (!empty($packageOwnerSubModule)) {
                return $this->allowPackageContent($sitegroup->package_id, "modules", $packageOwnerSubModule);
            }

            //owner base and also depeanded on viewer
            $subModule = @array_search($privacy_type, $subModules);
            if (!empty($subModule)) {
                if (!$this->allowPackageContent($sitegroup->package_id, "modules", $subModule))
                    return 0;
            }
        }else {

            $levelInclude = array("tellafriend" => "tfriend", "print" => "print", "overview" => "overview", "map" => "map", "insights" => "insight", /* "layout" => "layout", */ "contact_details" => "contact", "profile" => "profile", "sendupdate" => "sendupdate", "twitter" => "twitter");
            $levelOwnerModules = array("sitegroupoffer" => "offer", "sitegroupform" => "form", "sitegroupinvite" => "invite", "sitegroupbadge" => "badge", "sitegrouplikebox" => "likebox", "sitegroupmember" => "smecreate");
            $levelGroupBaseSubModule = array("sitegroupdocument" => "sdcreate", "sitegroupnote" => "sncreate", "sitegrouppoll" => "splcreate", "sitegroupevent" => "secreate", "sitegroupvideo" => "svcreate", "sitegroupalbum" => "spcreate", "sitegroupmusic" => "smcreate");
            //non sub modules
            $search_Key = array_search($privacy_type, $levelInclude);
            if (!empty($search_Key)) {
                $group_owner = Engine_Api::_()->getItem('user', $sitegroup->owner_id);
                $can_edit = $this->getManageAdminPrivacyCache('sitegroup_group', "level_" . $group_owner->level_id, $privacy_type);
                if ($can_edit == -1) {
                    $can_edit = Engine_Api::_()->authorization()->getPermission($group_owner->level_id, 'sitegroup_group', $privacy_type); //Engine_Api::_()->authorization()->isAllowed($sitegroup, $group_owner, $privacy_type);
                    $this->setManageAdminPrivacyCache('sitegroup_group', "level_" . $group_owner->level_id, $privacy_type, $can_edit);
                }
                if (empty($can_edit)) {
                    return 0;
                } else {
                    return 1;
                }
            }

            //owner base submodules
            $levelsubModule = @array_search($privacy_type, $levelOwnerModules);
            if (!empty($levelsubModule)) {
                $group_owner = Engine_Api::_()->getItem('user', $sitegroup->owner_id);
                $can_edit = $this->getManageAdminPrivacyCache('sitegroup_group', "level_" . $group_owner->level_id, $privacy_type);
                if ($can_edit == -1) {
                    $can_edit = Engine_Api::_()->authorization()->getPermission($group_owner->level_id, 'sitegroup_group', $privacy_type);
                    $this->setManageAdminPrivacyCache('sitegroup_group', "level_" . $group_owner->level_id, $privacy_type, $can_edit);
                }
                if (empty($can_edit)) {
                    return 0;
                } else {
                    return 1;
                }
            }

            //owner base and also depeanded on viewer
            $levelsubModule = @array_search($privacy_type, $levelGroupBaseSubModule);
            if (!empty($levelsubModule)) {
                $group_owner = Engine_Api::_()->getItem('user', $sitegroup->owner_id);
                $can_edit = $this->getManageAdminPrivacyCache('sitegroup_group', "level_" . $group_owner->level_id, $privacy_type);
                if ($can_edit == -1) {
                    $can_edit = Engine_Api::_()->authorization()->getPermission($group_owner->level_id, 'sitegroup_group', $privacy_type);
                    $this->setManageAdminPrivacyCache('sitegroup_group', "level_" . $group_owner->level_id, $privacy_type, $can_edit);
                }
                if (empty($can_edit)) {
                    return 0;
                }
            }
        }
        $existManageAdmin = 0;
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $manageAdminEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1);
        if (!empty($viewer_id) && !empty($manageAdminEnable)) {
            $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');
            $manageadminTableName = $manageadminTable->info('name');
            $select = $manageadminTable->select()
                    ->from($manageadminTableName, 'manageadmin_id')
                    ->where('user_id = ?', $viewer_id)
                    ->where('group_id = ?', $group_id)
                    ->limit(1);
            $row = $manageadminTable->fetchAll($select)->toArray();
            if (!empty($row[0]['manageadmin_id'])) {
                $existManageAdmin = 1;
            } else {
                $existManageAdmin = 0;
            }
        }

        $is_manage_admin = 1;
        if ($existManageAdmin == 0 || $viewer_id == $sitegroup->owner_id) {
            if (empty($viewer_id)) {
                $viewer = null;
                $viewer_id = 0;
            }
            $viewer_guid = 'user' . "_" . $viewer_id;
            $can_edit = $this->getManageAdminPrivacyCache($sitegroup->getGuid(), $viewer_guid, $privacy_type);
            if ($can_edit == -1) {
                $can_edit = Engine_Api::_()->authorization()->isAllowed($sitegroup, $viewer, $privacy_type);
                $this->setManageAdminPrivacyCache($sitegroup->getGuid(), $viewer_guid, $privacy_type, $can_edit);
            }
            if (empty($can_edit)) {
                $is_manage_admin = 0;
            }
        } elseif ($existManageAdmin == 1 && $viewer_id != $sitegroup->owner_id) {
            $group_owner = Engine_Api::_()->getItem('user', $sitegroup->owner_id);
            $can_edit = $this->getManageAdminPrivacyCache($sitegroup->getGuid(), $group_owner->getGuid(), $privacy_type);
            if ($can_edit == -1) {
                $can_edit = Engine_Api::_()->authorization()->isAllowed($sitegroup, $group_owner, $privacy_type);
                $this->setManageAdminPrivacyCache($sitegroup->getGuid(), $group_owner->getGuid(), $privacy_type, $can_edit);
            }
            if (empty($can_edit)) {
                $is_manage_admin = 0;
            }
        }

        if ($privacy_type == "view" && $is_manage_admin) {
            $is_manage_admin = $sitegroup->isViewableByNetwork();
        }

        return $is_manage_admin;
    }

    public function setManageAdminPrivacyCache($index, $member_index, $privacy_type, $privacy) {
        return $this->_privacy[$index][$member_index][$privacy_type] = $privacy;
    }

    public function getManageAdminPrivacyCache($index, $member_index, $privacy_type) {
        //  print_r($this->_privacy);
        if (isset($this->_privacy[$index][$member_index][$privacy_type])) {
            return $this->_privacy[$index][$member_index][$privacy_type];
        } else {
            return -1;
        }
    }

    /**
     * viewer is group owner or group admin
     *
     * @param object $sitegroup
     * @return bool $isGroupOwnerFlage
     */
    public function isGroupOwner($sitegroup, $user = null) {
        if (empty($user))
            $user = Engine_Api::_()->user()->getViewer();

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $isGroupOwnerFlage = false;
        if (empty($viewer_id))
            return $isGroupOwnerFlage;
        if ($sitegroup->owner_id == $viewer_id)
            return true;

        $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');
        $manageadminTableName = $manageadminTable->info('name');
        $select = $manageadminTable->select()
                ->from($manageadminTableName, 'manageadmin_id')
                ->where('user_id = ?', $viewer_id)
                ->where('group_id = ?', $sitegroup->group_id);
        $row = $manageadminTable->fetchRow($select);
        if (!empty($row))
            $isGroupOwnerFlage = true;

        return $isGroupOwnerFlage;
    }

    /**
     * allow to group owner
     *
     * @param object $sitegroup
     * @param string $privacy_type
     * @return bool $canDo
     */
    public function isGroupOwnerAllow($sitegroup, $privacy_type) {
        if (empty($sitegroup))
            return;
        $group_owner = Engine_Api::_()->getItem('user', $sitegroup->owner_id);
        return (bool) $canDo = Engine_Api::_()->authorization()->getPermission($group_owner->level_id, 'sitegroup_group', $privacy_type);
    }

    public function setDisabledType() {
        $modArray = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.mod.settings', 0));
        $modArrayType = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.mod.types', 0));
        if (!empty($modArray)) {
            foreach ($modArray as $modName) {
                $newModArray[] = strrev($modName);
            }
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.mod.settings', serialize($newModArray));
        }
        if (!empty($modArrayType)) {
            foreach ($modArrayType as $modNameType) {
                $newModArrayType[] = strrev($modNameType);
            }
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.mod.types', serialize($newModArrayType));
        }
    }

    /**
     * Get group id
     *
     * @param string $group_url
     * @return int $groupID
     */
    public function getGroupId($group_url, $groupId = null) {
        $groupID = 0;
        if (!empty($group_url)) {
            $sitegroup_table = Engine_Api::_()->getItemTable('sitegroup_group');
            $select = $sitegroup_table->select()
                    ->from($sitegroup_table->info('name'), 'group_id')
                    ->where('group_url = ?', $group_url);

            if (!empty($groupId)) {
                $select->where('group_id != ?', $groupId);
            }

            $groupID = $select->limit(1)
                    ->query()
                    ->fetchColumn();
        }

        return $groupID;
    }

    /**
     * Get group url
     *
     * @param int $group_id
     * @return string $groupUrl
     */
    public function getGroupUrl($group_id) {

        $groupUrl = 0;
        if (!empty($group_id)) {
            $sitegroup_table = Engine_Api::_()->getItemTable('sitegroup_group');
            $groupUrl = $sitegroup_table->select()
                    ->from($sitegroup_table->info('name'), 'group_url')
                    ->where('group_id = ?', $group_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
        }
        return $groupUrl;
    }

    /**
     * Get group list
     *
     * @param array $params
     * @param array $customParams
     * @return array $paginator;
     */
    public function getSitegroupsPaginator($params = array(), $customParams = null) {

        $paginator = Zend_Paginator::factory($this->getSitegroupsSelect($params, $customParams));
        if (!empty($params['group'])) {
            $paginator->setCurrentPageNumber($params['group']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    /**
     * Get group select query
     *
     * @param array $params
     * @param array $customParams
     * @return string $select;
     */
    public function getSitegroupsSelect($params = array(), $customParams = null) {

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $table = Engine_Api::_()->getDbtable('groups', 'sitegroup');
        $rName = $table->info('name');

        $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
        if (!empty($moduleEnabled)) {
            $membertable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
            $membertableName = $membertable->info('name');
        }

        $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tmName = $tmTable->info('name');

        $searchTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'search')->info('name');

        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitegroup');
        $locationName = $locationTable->info('name');
        $select = $table->select()->setIntegrityCheck(false);

        if (isset($params['browse_group']) && !empty($params['browse_group'])) {
            $columnsArray = array('group_id', 'title', 'group_url', 'body', 'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'modified_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'closed', 'email', 'website', 'phone', 'package_id', 'follow_count');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                $columnsArray = array_merge(array('member_count'), $columnsArray);
                $columnsArray = array_merge(array('member_title'), $columnsArray);
                $columnsArray = array_merge(array('member_approval'), $columnsArray);
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge'))
                $columnsArray[] = 'badge_id';

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer'))
                $columnsArray[] = 'offer';

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
                $columnsArray[] = 'review_count';
                $columnsArray[] = 'rating';
            }

            $select = $select->from($rName, $columnsArray);
        } else {
            $select = $select->from($rName);
        }

        if (isset($params['type']) && !empty($params['type']) && $params['type'] != 'manage') {
            if ($params['type'] == 'browse' || $params['type'] == 'home') {
                $select = $select
                        ->where($rName . '.approved = ?', '1')
                        ->where($rName . '.declined = ?', '0')
                        ->where($rName . '.draft = ?', '1');

                if (!empty($moduleEnabled) && isset($params['type_location']) && $params['type_location'] != 'browseLocation' && $params['type_location'] != 'browseGroup' && $params['type_location'] == 'profilebrowseGroup') {
                    $select = $select->join($membertableName, "$membertableName.group_id = $rName.group_id", array('user_id AS group_owner_id'));
                    if (!empty($values['admingroups'])) {
                        $select = $select->where($membertableName . '.user_id = ?', $params['user_id']);
                    }
                    if (isset($params['onlymember']) && !empty($params['onlymember'])) {
                        $select = $select->where($rName . '.group_id IN (?)', (array) $params['onlymember']);
                    }
                    $select = $select->where($membertableName . '.active = ?', 1);
                }

                $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.status.show', 1);
                if ($stusShow == 0) {
                    $select = $select
                            ->where($rName . '.closed = ?', '0');
                }
                if ($this->hasPackageEnable())
                    $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
            } elseif ($params['type'] == 'browse_home_zero') {
                $select = $select
                        ->where($rName . '.closed = ?', '0')
                        ->where($rName . '.approved = ?', '1')
                        ->where($rName . '.declined = ?', '0')
                        ->where($rName . '.draft = ?', '1');
                if ($this->hasPackageEnable())
                    $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
            }
            $select->where($rName . ".search = ?", 1);
        }
        if (isset($customParams)) {

            $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
            $coreversion = $coremodule->version;
            if ($coreversion > '4.1.7') {

                //PROCESS OPTIONS
                $tmp = array();
                foreach ($customParams as $k => $v) {
                    if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
                        continue;
                    } else if (false !== strpos($k, '_field_')) {
                        list($null, $field) = explode('_field_', $k);
                        $tmp['field_' . $field] = $v;
                    } else if (false !== strpos($k, '_alias_')) {
                        list($null, $alias) = explode('_alias_', $k);
                        $tmp[$alias] = $v;
                    } else {
                        $tmp[$k] = $v;
                    }
                }
                $customParams = $tmp;
            }

            $select = $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($searchTable, "$searchTable.item_id = $rName.group_id", null);

            $searchParts = Engine_Api::_()->fields()->getSearchQuery('sitegroup_group', $customParams);
            foreach ($searchParts as $k => $v) {
                //$v = str_replace("%2C%20",", ",$v);
                $select->where("`{$searchTable}`.{$k}", $v);
            }
        }

        if (isset($params['sitegroup_price']) && !empty($params['sitegroup_price'])) {

            if ((!empty($params['sitegroup_price']['min']) && !empty($params['sitegroup_price']['max']))) {

                if ($params['sitegroup_price']['max'] < $params['sitegroup_price']['min']) {
                    $min = $params['sitegroup_price']['max'];
                    $max = $params['sitegroup_price']['min'];
                } else {
                    $min = $params['sitegroup_price']['min'];
                    $max = $params['sitegroup_price']['max'];
                }

                $select = $select->where($rName . '.price >= ?', $min)->where($rName . '.price <= ?', $max);
            }

            if ((empty($params['sitegroup_price']['min']) && !empty($params['sitegroup_price']['max']))) {
                $select = $select->where($rName . '.price <= ?', $params['sitegroup_price']['max']);
            }

            if ((!empty($params['sitegroup_price']['min']) && empty($param['sitegroup_price']['max']))) {
                $select = $select->where($rName . '.price >= ?', $params['sitegroup_price']['min']);
            }
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer') && isset($params['offer_type']) && !empty($params['offer_type'])) {
            $offerTable = Engine_Api::_()->getDbtable('offers', 'sitegroupoffer');
            $offerTableName = $offerTable->info('name');
            $today = date("Y-m-d H:i:s");
            $select->setIntegrityCheck(false)
                    ->join($offerTableName, "$offerTableName.group_id = $rName.group_id", array(''))
                    ->where("$offerTableName.end_settings = 0 OR ($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$today')");

            if ($params['offer_type'] == 'hot') {
                $select->where("$offerTableName.hotoffer = 1");
            } elseif ($params['offer_type'] == 'featured') {
                $select->where("$offerTableName.sticky = 1");
            }
        }

//     if (isset($params['sitegroup_postalcode']) && empty($params['sitegroup_postalcode']) && isset($params['locationmiles']) && empty($params['locationmiles'])) {
        //check for stret , city etc in location search.
        if (isset($params['sitegroup_street']) && !empty($params['sitegroup_street'])) {
            $select->join($locationName, "$rName.group_id = $locationName.group_id   ", null);
            $select->where($locationName . '.address   LIKE ? ', '%' . $params['sitegroup_street'] . '%');
        } if (isset($params['sitegroup_city']) && !empty($params['sitegroup_city'])) {
            $select->join($locationName, "$rName.group_id = $locationName.group_id   ", null);
            $select->where($locationName . '.city = ?', $params['sitegroup_city']);
        } if (isset($params['sitegroup_state']) && !empty($params['sitegroup_state'])) {
            $select->join($locationName, "$rName.group_id = $locationName.group_id   ", null);
            $select->where($locationName . '.state = ?', $params['sitegroup_state']);
        } if (isset($params['sitegroup_country']) && !empty($params['sitegroup_country'])) {
            $select->join($locationName, "$rName.group_id = $locationName.group_id   ", null);
            $select->where($locationName . '.country = ?', $params['sitegroup_country']);
        }
// 		} else {
// 			$select->join($locationName, "$rName.group_id = $locationName.group_id   ", null);
// 			$select->where($locationName . '.zipcode = ?', $params['sitegroup_postalcode']);
// 		}

        if (!isset($params['sitegroup_location']) && isset($params['locationSearch']) && !empty($params['locationSearch'])) {
            $params['sitegroup_location'] = $params['locationSearch'];

            if (isset($params['locationmilesSearch'])) {
                $params['locationmiles'] = $params['locationmilesSearch'];
            }
        }

        if ((isset($params['sitegroup_location']) && !empty($params['sitegroup_location'])) || (!empty($params['formatted_address']))) {
            $enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.proximitysearch', 1);
            if (isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable))) {
                $longitude = 0;
                $latitude = 0;


                //check for zip code in location search.
                if (empty($params['Latitude']) && empty($params['Longitude'])) {
                    $selectLocQuery = $locationTable->select()->where('location = ?', $params['sitegroup_location']);
                    $locationValue = $locationTable->fetchRow($selectLocQuery);

                    if (empty($locationValue)) {
                        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['sitegroup_location'], 'module' => 'Groups / Communities'));
                        if (!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
                            $latitude = $locationResults['latitude'];
                            $longitude = $locationResults['longitude'];
                        }
                    } else {
                        $latitude = (float) $locationValue->latitude;
                        $longitude = (float) $locationValue->longitude;
                    }
                } else {
                    $latitude = (float) $params['Latitude'];
                    $longitude = (float) $params['Longitude'];
                }

                $radius = $params['locationmiles']; //in miles

                $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.proximity.search.kilometer', 0);
                if (!empty($flage)) {
                    $radius = $radius * (0.621371192);
                }
                //$latitudeRadians = deg2rad($latitude);
                //We have follow this code from event plugin.
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";
                $select->join($locationName, "$rName.group_id = $locationName.group_id   ", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);
                $select->order("distance");
            } else {
// 				if ($params['sitegroup_postalcode'] == 'postalCode') { 
// 					$select->join($locationName, "$rName.group_id = $locationName.group_id", null);
// 					$select->where("`{$locationName}`.formatted_address LIKE ? ", "%" . $params['formatted_address'] . "%");
// 				} 
// 				else {
                $select->join($locationName, "$rName.group_id = $locationName.group_id", null);
                $select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['sitegroup_location']) . "%");
                //}
            }
        } elseif ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupgeolocation') && isset($params['has_currentlocation']) && !empty($params['has_currentlocation']) && !empty($params['latitude']) && !empty($params['longitude'])) {
            $radius = Engine_Api::_()->getApi('settings', 'core')->getSetting('sgl.geolocation.range', 100); // in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            // $latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";
            $select->join($locationName, "$rName.group_id = $locationName.group_id   ", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);
            $select->order("distance");
        }

        //Start Network work
        if (!empty($params['type'])) {
            if ($params['type'] == 'browse' || $params['type'] == 'home' || $params['type'] == 'browse_home_zero') {

                $select = $table->getNetworkBaseSql($select, array('browse_network' => (isset($params['show']) && $params['show'] == "3")));
            }
        }
        //End Network work

        $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
        if (isset($params['type_location']) && $params['type_location'] != 'profilebrowseGroup') {
            if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
                $select->where($rName . '.owner_id = ?', $params['user_id']);
            }
        }

        if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
            $select->where($rName . '.owner_id = ?', $params['user']->getIdentity());
        }

        if ((isset($params['show']) && $params['show'] == "4")) {
            $likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $select->setIntegrityCheck(false)
                    ->join($likeTableName, "$likeTableName.resource_id = $rName.group_id")
                    ->where($likeTableName . '.poster_type = ?', 'user')
                    ->where($likeTableName . '.poster_id = ?', $viewer_id)
                    ->where($likeTableName . '.resource_type = ?', 'sitegroup_group');
        }

        if ((isset($params['show']) && $params['show'] == "5")) {
            $select->where($rName . '.featured = ?', 1);
        }

        if (!empty($params['users'])) {
            $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
            $select->where($rName . '.owner_id in (?)', new Zend_Db_Expr($str));
        }

        if (empty($params['users']) && isset($params['show']) && $params['show'] == '2') {
            $select->where($rName . '.owner_id = ?', '0');
        }
        if (!empty($params['tag'])) {
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tmName, "$tmName.resource_id = $rName.group_id")
                    ->where($tmName . '.resource_type = ?', 'sitegroup_group')
                    ->where($tmName . '.tag_id = ?', $params['tag']);
        }

// 		if ($params['widget'] == 'locationsearch') {
// 			$select->where($rName . ".location != ?", '');
// 		}

        if (isset($params['admingroups'])) {
            $str = (string) ( is_array($params['admingroups']) ? "'" . join("', '", $params['admingroups']) . "'" : $params['admingroups'] );
            $select->where($rName . '.group_id in (?)', new Zend_Db_Expr($str));
        }

        if (isset($params['adminjoinedgroups'])) {
            $str = (string) ( is_array($params['adminjoinedgroups']) ? "'" . join("', '", $params['adminjoinedgroups']) . "'" : $params['adminjoinedgroups'] );
            $select->where($rName . '.group_id in (?)', new Zend_Db_Expr($str));
        }

        if (isset($params['notIncludeSelfGroups']) && !empty($params['notIncludeSelfGroups'])) {
            $select->where($rName . '.owner_id != ?', $params['notIncludeSelfGroups']);
        }

        if (!empty($params['category'])) {
            $select->where($rName . '.category_id = ?', $params['category']);
        }

        if (!empty($params['category_id'])) {
            $select->where($rName . '.category_id = ?', $params['category_id']);
        }

        if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
            if (!empty($params['badge_id'])) {
                $select->where($rName . '.badge_id = ?', $params['badge_id']);
            }
        }

        if (!empty($params['profile_type'])) {
            $select->where($rName . '.profile_type = ?', $params['profile_type']);
        }

        if (!empty($params['subcategory'])) {
            $select->where($rName . '.subcategory_id = ?', $params['subcategory']);
        }

        if (!empty($params['subcategory_id'])) {
            $select->where($rName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (!empty($params['subsubcategory'])) {
            $select->where($rName . '.subsubcategory_id = ?', $params['subsubcategory']);
        }

        if (!empty($params['subsubcategory_id'])) {
            $select->where($rName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (isset($params['closed']) && $params['closed'] != "") {
            $select->where($rName . '.closed = ?', $params['closed']);
        }

        //Could we use the search indexer for this?
        if (!empty($params['search'])) {

            $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tmName, "$tmName.resource_id = $rName.group_id and " . $tmName . ".resource_type = 'sitegroup_group'", null)
                    ->joinLeft($tagName, "$tagName.tag_id = $tmName.tag_id", array($tagName . ".text"));
            //$params['search'] = str_replace("%20"," ",$params['search']);
            $select->where($rName . ".title LIKE ? OR " . $rName . ".body LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['search'] . '%');
        }

        if (!empty($params['start_date'])) {
            $select->where($rName . ".creation_date > ?", date('Y-m-d', $params['start_date']));
        }

        if (!empty($params['end_date'])) {
            $select->where($rName . ".creation_date < ?", date('Y-m-d', $params['end_date']));
        }

        if (!empty($params['has_photo'])) {
            $select->where($rName . ".photo_id > ?", 0);
        }

        if (!empty($params['has_review'])) {
            $select->where($rName . ".review_count > ?", 0);
        }

        if ((isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] != 'all' && $_GET['alphabeticsearch'] != '@')) {
            $select->where($rName . ".title LIKE ?", $_GET['alphabeticsearch'] . '%');
        } elseif (isset($_GET['alphabeticsearch']) && ($_GET['alphabeticsearch'] == 'all' && $_GET['alphabeticsearch'] != '@')) {
//      $select->group($rName . '.group_id');
//      return $select;
        } elseif (isset($_GET['alphabeticsearch']) && ($_GET['alphabeticsearch'] == '@' && $_GET['alphabeticsearch'] != 'all')) {
            $select->where($rName . ".title REGEXP '^[0-9]'");
        }
        $select->group($rName . '.group_id');

        if (!empty($params['type']) && empty($params['orderby'])) {
            if ($params['type'] == 'browse' || $params['type'] == 'manage') {
                $order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.browseorder', 1);
                switch ($order) {
                    case "1":
                        $select->order($rName . '.creation_date DESC');
                        break;
                    case "2":
                        $select->order($rName . '.view_count DESC');
                        break;
                    case "3":
                        $select->order($rName . '.title');
                        break;
                    case "4":
                        $select->order($rName . '.sponsored' . ' DESC');
                        break;
                    case "5":
                        $select->order($rName . '.featured' . ' DESC');
                        break;
                    case "6":
                        $select->order($rName . '.sponsored' . ' DESC');
                        $select->order($rName . '.featured' . ' DESC');
                        break;
                    case "7":
                        $select->order($rName . '.featured' . ' DESC');
                        $select->order($rName . '.sponsored' . ' DESC');
                        break;
                }
            }
        } else {
            if (!empty($params['orderby']) && $params['orderby'] == "title") {
                $select->order($rName . '.' . $params['orderby']);
            } elseif (isset($params['orderby']) && !empty($params['orderby']))
                $select->order($rName . '.' . $params['orderby'] . ' DESC');
        }
        $select->order($rName . '.creation_date DESC'); //echo $select;die;
        return $select;
    }

    public function getPackageAuthInfo($modulename) {
        $sitegroupModSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.mod.settings', 0);
        $sitegroupModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.mod.types', 0);
        $modSecondArray = $modFirstArray = array();
        if (!empty($sitegroupModSetting)) {
            $modFirstArray = unserialize($sitegroupModSetting);
        }
        if (!empty($sitegroupModType)) {
            $modSecondArray = unserialize($sitegroupModType);
        }
        $modArray = array_merge($modFirstArray, $modSecondArray);
        return in_array(strrev($modulename), $modArray);
    }

    /**
     * Get Group View Link
     *
     * @param int $group_id
     * @param int $owner_id
     * @param string $slug
     * @return link
     */
    public function getHref($group_id, $owner_id, $slug = null) {

        $group_url = Engine_Api::_()->sitegroup()->getGroupUrl($group_id);
        $params = array_merge(array('group_url' => $group_url));
        $urlO = Zend_Controller_Front::getInstance()->getRouter()
                ->assemble($params, 'sitegroup_entry_view', true);

        //SITEGROUPURL WORK START
        $sitegroupUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupurl');
        if (!empty($sitegroupUrlEnabled)) {
            $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manifestUrlS', "groupitem");
            $banneUrlArray = Engine_Api::_()->sitegroup()->getBannedGroupUrls();
            $group_likes = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.likelimit.forurlblock', "5");
            $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.change.url', 1);

            $sitegroupObject = Engine_Api::_()->getItem('sitegroup_group', Engine_Api::_()->sitegroup()->getGroupId($group_url));
            $replaceStr = str_replace("/" . $routeStartS . "/", "/", $urlO);
            if ((!empty($change_url)) && ($sitegroupObject->like_count >= $group_likes) && !in_array($group_url, $banneUrlArray) && !empty($sitegroupObject)) {
                $urlO = $replaceStr;
            }
        }
        return $urlO;
    }

    public function sitegroup_auth($admin_tab) {
        include_once APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license2.php';
        return $checkAuth;
    }

//

    /**
     * GET LINK OF PHOTO VIEW GROUP
     *
     * @param object $image
     * @param array $params
     * @return link
     */
    public function getHreflink($image, $params = array()) {
        $params = array_merge(array(
            'route' => 'sitegroup_imagephoto_specific',
            'reset' => true,
            'controller' => 'photo',
            'action' => 'view',
            'photo_id' => $image->getIdentity(),
            'album_id' => $image->collection_id,
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    /**
     * Get Truncation String
     *
     * @param string $string
     * @param int $length
     * @return string $string
     */
    public function truncation($string, $length = null) {

        if (empty($length)) {
            $length = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.title.truncation', 16);
        }

        $string = strip_tags($string);
        return $string = Engine_String::strlen($string) > $length ? Engine_String::substr($string, 0, ($length - 3)) . '...' : $string;
    }

    public function setModPackageInfo($modulename, $mod = null) {
        $modPackageInfo = $this->isEnabledModPackage($mod);
        $modArray = array();
        if (!empty($modPackageInfo)) {
            $modArray = unserialize($modPackageInfo);
            if (!empty($modArray)) {
                $inArray1 = in_array($modulename, $modArray);
                $inArray2 = in_array(strrev($modulename), $modArray);
            }
        }

        if (!empty($inArray1) && empty($inArray2)) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.mod.settings', 'a:0:{}');
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.mod.types', 'a:0:{}');
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitegroup.edit.package');
            $modArray = array();
        }
        if (!empty($inArray2) && empty($inArray1)) {
            return;
        }

        $modArray[] = strrev($modulename);
        $arrayName = 0;
        $mod ? $arrayName = 'sitegroup.mod.types' : $arrayName = 'sitegroup.mod.settings';
        Engine_Api::_()->getApi('settings', 'core')->setSetting($arrayName, serialize($modArray));
        return;
    }

    /**
     * Check location is enable
     *
     * @param array $params
     * @return int $check
     */
    public function enableLocation($params = array()) {
        $sitegroup_recent_info = Zend_Registry::isRegistered('sitegroup_recent_info') ? Zend_Registry::get('sitegroup_recent_info') : null;
        if (!empty($sitegroup_recent_info)) {
            $check = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.location', 1);

            if (!empty($check)) {
                $check = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1);
            }
        } else {
            return false;
        }

        return $check;
    }

    /**
     * THIS FUNCTION SHOW PEOPLE LIKES OR FRIEND LIKES.
     *
     * @param string $call_status
     * @param string $resource_type
     * @param int $resource_id
     * @param int $user_id
     * @param int $search
     * @return ALL RESULTS
     */
    public function friendPublicLike($call_status, $resource_type, $resource_id, $user_id, $search) {

        $likeTable = Engine_Api::_()->getItemTable('core_like');
        $likeTableName = $likeTable->info('name');
        $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
        $membershipTableName = $membershipTable->info('name');
        $userTable = Engine_Api::_()->getItemTable('user');
        $userTableName = $userTable->info('name');

        $sub_status_select = $userTable->select()
                ->setIntegrityCheck(false)
                ->from($likeTableName, array('poster_id'))
                ->where($likeTableName . '.resource_type = ?', $resource_type)
                ->where($likeTableName . '.resource_id = ?', $resource_id)
                ->where($likeTableName . '.poster_id != ?', 0)
                ->where($userTableName . '.displayname LIKE ?', '%' . $search . '%')
                ->order('	like_id DESC');
        if ($call_status == 'friend') {

            $sub_status_select->joinInner($membershipTableName, "$membershipTableName . user_id = $likeTableName . poster_id", NULL)
                    ->joinInner($userTableName, "$userTableName . user_id = $membershipTableName . user_id")
                    ->where($membershipTableName . '.resource_id = ?', $user_id)
                    ->where($membershipTableName . '.active = ?', 1)
                    ->where($likeTableName . '.poster_id != ?', $user_id);
        } else if ($call_status == 'public') {

            $sub_status_select->joinInner($userTableName, "$userTableName . user_id = $likeTableName . poster_id");
        }
        return Zend_Paginator::factory($sub_status_select);
    }

    /**
     * number of group like
     *
     * @param string $RESOURCE_TYPE
     * @param int $RESOURCE_ID
     * @param int $LIMIT
     */
    public function groupLike($RESOURCE_TYPE, $RESOURCE_ID, $LIMIT) {

        $likeTable = Engine_Api::_()->getItemTable('core_like');
        $likeTableName = $likeTable->info('name');
        $select = $likeTable->select()
                ->from($likeTableName, array('poster_id'))
                ->where('resource_type = ?', $RESOURCE_TYPE)
                ->where('resource_id = ?', $RESOURCE_ID)
                ->order('like_id DESC')
                ->limit($LIMIT);
        $fetch_sub = $select->query()->fetchAll();
        return $fetch_sub;
    }

    /**
     * Function for showing 'Liked Link'.This function use in the like button.
     *
     * @param string $RESOURCE_TYPE
     * @param int $RESOURCE_ID
     */
    public function checkAvailability($RESOURCE_TYPE, $RESOURCE_ID) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $sub_status_table = Engine_Api::_()->getItemTable('core_like');
        $sub_status_name = $sub_status_table->info('name');
        $sub_status_select = $sub_status_table->select()
                ->from($sub_status_name, array('like_id'))
                ->where('resource_type = ?', $RESOURCE_TYPE)
                ->where('resource_id = ?', $RESOURCE_ID)
                ->where('poster_type =?', $viewer->getType())
                ->where('poster_id =?', $viewer->getIdentity())
                ->limit(1);
        return $sub_status_select->query()->fetchAll();
    }

    /**
     * Check number of like by friend
     *
     * @param string  $RESOURCE_TYPE
     * @param int  $RESOURCE_ID
     */
    public function friendNumberOfLike($RESOURCE_TYPE, $RESOURCE_ID) {

        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $likeTable = Engine_Api::_()->getItemTable('core_like');
        $likeTableName = $likeTable->info('name');
        $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
        $membershipTableName = $membershipTable->info('name');
        $select = $likeTable->select()
                ->from($likeTableName, array('COUNT(' . $likeTableName . '.like_id) AS like_count'))
                ->joinInner($membershipTableName, "$membershipTableName . user_id = $likeTableName . poster_id", NULL)
                //->joinInner($userName, "$userName.user_id = $likeTableName.poster_id", NULL)
                ->where($membershipTableName . '.resource_id = ?', $user_id)
                ->where($membershipTableName . '.active = ?', 1)
                ->where($likeTableName . '.resource_type = ?', $RESOURCE_TYPE)
                ->where($likeTableName . '.resource_id = ?', $RESOURCE_ID)
                ->where($likeTableName . '.poster_id != ?', $user_id)
                ->where($likeTableName . '.poster_id != ?', 0)
                ->group($likeTableName . '.resource_id');
        $fetch_count = $select->query()->fetchAll();
        if (!empty($fetch_count)) {
            return $fetch_count[0]['like_count'];
        } else {
            return 0;
        }
    }

    /**
     * Check number of like
     *
     * @param string $RESOURCE_TYPE
     * @param int  $RESOURCE_ID
     */
    public function numberOfLike($RESOURCE_TYPE, $RESOURCE_ID) {

        $likeTable = Engine_Api::_()->getItemTable('core_like');
        $likeTableName = $likeTable->info('name');
        $select = $likeTable->select()
                ->from($likeTableName, array('COUNT(' . $likeTableName . '.like_id) AS like_count'))
                ->where('resource_type = ?', $RESOURCE_TYPE)
                ->where('resource_id = ?', $RESOURCE_ID)
                ->where('poster_id != ?', 0)
                ->group('resource_id');
        $fetch_count = $select->query()->fetchAll();
        if (!empty($fetch_count)) {
            return $fetch_count[0]['like_count'];
        } else {
            return 0;
        }
    }

    public function getEnabledSubModules() {

        $coreTable = Engine_Api::_()->getDbtable('modules', 'core');
        ///////////////////START FOR INRAGRATION WORK WITH OTHER PLUGIN./////////
        $sitegroupintegrationEnabled = $coreTable->isModuleEnabled('sitegroupintegration');
        if (!empty($sitegroupintegrationEnabled)) {
            $mixSettingsResults = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getIntegrationItems();
        }
        ///////////////////END FOR INRAGRATION WORK WITH OTHER PLUGIN./////////


        if ($coreTable->isModuleEnabled('sitegroupalbum') ||
                $coreTable->isModuleEnabled('sitegroupvideo') ||
                $coreTable->isModuleEnabled('sitegrouppoll') ||
                $coreTable->isModuleEnabled('sitegroupnote') ||
                $coreTable->isModuleEnabled('sitegroupevent') ||
                $coreTable->isModuleEnabled('sitegroupdocument') ||
                $coreTable->isModuleEnabled('sitegroupreviews') ||
                $coreTable->isModuleEnabled('sitegroupdiscussion') ||
                $coreTable->isModuleEnabled('sitegroupoffer') ||
                $coreTable->isModuleEnabled('sitegroupform') ||
                $coreTable->isModuleEnabled('sitegroupinvite') ||
                $coreTable->isModuleEnabled('sitegroupmember') ||
                $coreTable->isModuleEnabled('sitegroupmusic') ||
                (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup'))) ||
                (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup'))) ||
                !empty($mixSettingsResults)) {
            return $group_redirect = 1;
        } else {
            return $group_redirect = 0;
        }
    }

    //Package Related Functions

    /**
     * Get List of enabled submodule for package
     *
     */
    public function getEnableSubModules($tempPackages = null) {

        $enableSubModules = array();

        $includeModules = array("sitegroupdocument" => 'Documents', "sitegroupoffer" => 'Offers', "sitegroupform" => "Form", "sitegroupdiscussion" => "Discussions", "sitegroupnote" => "Notes", "sitegroupalbum" => "Photos", "sitegroupvideo" => "Videos", "sitegroupevent" => "Events", "sitegrouppoll" => "Polls", "sitegroupinvite" => "Invite & Promote", "sitegroupbadge" => "Badges", "sitegrouplikebox" => "External Badge", "sitegroupmusic" => "Music", "sitegroupmember" => "Member", "siteevent" => "Events", "sitevideo" => "Videos");

        $enableAllModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        $enableModules = array_intersect(array_keys($includeModules), $enableAllModules);


        foreach ($enableModules as $module) {
            if ($this->isPluginActivate($module)) {
                if ($module == 'siteevent') {
                    if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
                        $enableSubModules['sitegroupevent'] = $includeModules['sitegroupevent'];
                    }
                } elseif ($module == 'sitevideo') {
                    if ((Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
                        $enableSubModules['sitegroupvideo'] = $includeModules['sitegroupvideo'];
                    }
                } else {
                    $enableSubModules[$module] = $includeModules[$module];
                }
            }
        }

        //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
        $sitegroupintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration');
        if (!empty($sitegroupintegrationEnabled)) {
            $mixResults = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getIntegrationItems();

            $mixSettings = array();
            $title = '';
            foreach ($mixResults as $modName) {
                if ($modName['resource_type'] == 'list_listing') {
                    $title = "Listings";
                } elseif ($modName['resource_type'] == 'sitereview_listing') {
                    if ($tempPackages == 'adminPackages') {
                        $title = "Reviews" . ' - ' . $modName['item_title'];
                    } else {
                        $title = $modName['item_title'];
                    }
                } elseif ($modName['resource_type'] == 'sitepage_page') {
                    $title = "Pages";
                } elseif ($modName['resource_type'] == 'sitebusiness_business') {
                    $title = "Businesses";
                } elseif ($modName['resource_type'] == 'document') {
                    $title = "Documents";
                } elseif ($modName['resource_type'] == 'folder') {
                    $title = "Folder";
                } elseif ($modName['resource_type'] == 'quiz') {
                    $title = "Quiz";
                } elseif ($modName['resource_type'] == 'sitefaq_faq') {
                    $title = "Faqs";
                } elseif ($modName['resource_type'] == 'sitetutorial_tutorial') {
                    $title = "Tutorials";
                } elseif ($modName['resource_type'] == 'sitestoreproduct_product') {
                    $title = "Store Products";
                }
                $mixSettings[$modName['resource_type'] . '_' . $modName['listingtype_id']] = $title;
            }

            $enableSubModules = array_merge($enableSubModules, $mixSettings);
        }
        //END FOR INRAGRATION WORK WITH OTHER PLUGIN.

        asort($enableSubModules);
        return $enableSubModules;
    }

    /**
     * Check package is enable or not for site
     * @return bool
     */
    public function hasPackageEnable() {
        return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.package.enable', 1);
    }

    /**
     * Allow contect for perticuler package
     * @params $type : which check
     * $params $package_id : Id of group
     * $params $params : array some extra
     * */
    public function allowPackageContent($package_id, $type = null, $subModuleName = null) {

        if (!$this->hasPackageEnable())
            return;
        $flage = false;
        if (Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
            $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

            if (!empty($sitegroup->pending) && !$this->isGroupOwner($sitegroup)) {
                return $flage;
            }
        }
        $package = Engine_Api::_()->getItem('sitegroup_package', $package_id);

        if (empty($package))
            return $flage;

        switch ($type) {
            case "modules":

                $includeArray = $this->getEnableSubModules();

                $modulesArray = unserialize($package->modules);
                if (empty($modulesArray))
                    $modulesArray = array();

                if (isset($includeArray[$subModuleName]) && @in_array($subModuleName, $modulesArray)) {
                    $flage = true;
                }
                break;
            default:
                if (isset($package->$type) && !empty($package->$type))
                    $flage = true;
                break;
        }

        return $flage;
    }

    /**
     * Get Group Profile Fileds level base on package
     * @params int $group_id : Id of group
     * @return bool
     * */
    public function getPackageProfileLevel($group_id = null) {
        if (!$this->hasPackageEnable())
            return;
        $package = null;
        $group = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        if (!empty($group)) {
            $package = $group->getPackage();
        }
        if (!empty($package))
            return $package->profile;
        else
            return 0;
    }

    /**
     * Get Group Profile Fileds If package set some fields
     * @params int $group_id : Id of group
     * @return array : profile fields
     * */
    public function getProfileFields($group_id = null) {
        $group = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        if (!empty($group)) {
            $package = $group->getPackage();
            return unserialize($package->profilefields);
        }
    }

    /**
     * Get Group Profile Fileds If package selected fields Id
     * @params int $group_id : Id of group
     * @return array : profile fields
     * */
    public function getSelectedProfilePackage($group_id = null) {
        $profileType = array();
        $profileType[""] = "";
        $group = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        if (!empty($group)) {
            $package = $group->getPackage();
            $profile = unserialize($package->profilefields);

            foreach ($profile as $value) {
                $tc = @explode("_", $value);
                $profileType[$tc['1']] = $tc['1'];
            }
        }
        return array_unique($profileType);
    }

    /**
     * Get Group Profile Fileds  level base on level
     * @params int $level_id : level id of group owner
     * @return array : profile fields
     * */
    public function getLevelProfileFields($level_id) {
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $profileFields = $permissionsTable->getAllowed('sitegroup_group', $level_id, array("profilefields"));
        return unserialize($profileFields['profilefields']);
    }

    /**
     * Get Group Profile Fileds If owner level selected fields Id
     * @params int $level_id : Level Id of group owner
     * @return array : profile fields
     * */
    public function getSelectedProfileLevel($level_id) {

        $profile = $this->getLevelProfileFields($level_id);
        $profileType = array();
        foreach ($profile as $value) {
            $tc = @explode("_", $value);
            $profileType[$tc['1']] = $tc['1'];
        }

        return array_unique($profileType);
    }

    /**
     * Send emails for perticuler group
     * @params $type : which mail send
     * $params $groupId : Id of group
     * */
    public function sendMail($type, $groupId) {

        if (empty($type) || empty($groupId)) {
            return;
        }
        $group = Engine_Api::_()->getItem('sitegroup_group', $groupId);
        $mail_template = null;
        if (!empty($group)) {

            $owner = Engine_Api::_()->user()->getUser($group->owner_id);
            switch ($type) {
                case "APPROVAL_PENDING":
                    $mail_template = 'sitegroup_group_approval_pending';
                    break;
                case "EXPIRED":
                    if (!$this->hasPackageEnable())
                        return;
                    if ($group->getPackage()->isFree())
                        $mail_template = 'sitegroup_group_expired';
                    else
                        $mail_template = 'sitegroup_group_renew';
                    break;
                case "OVERDUE":
                    $mail_template = 'sitegroup_group_overdue';
                    break;
                case "CANCELLED":
                    $mail_template = 'sitegroup_group_cancelled';
                    break;
                case "ACTIVE":
                    $mail_template = 'sitegroup_group_active';
                    break;
                case "PENDING":
                    $mail_template = 'sitegroup_group_pending';
                    break;
                case "REFUNDED":
                    $mail_template = 'sitegroup_group_refunded';
                    break;
                case "APPROVED":
                    $mail_template = 'sitegroup_group_approved';
                    break;
                case "DISAPPROVED":
                    $mail_template = 'sitegroup_group_disapproved';
                    break;
                case "DECLINED":
                    $mail_template = 'sitegroup_group_declined';
                    break;
                case "RECURRENCE":
                    $mail_template = 'sitegroup_group_recurrence';
                    break;
            }
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, $mail_template, array(
                'site_title' => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1),
                'group_title' => ucfirst($group->getTitle()),
                'group_description' => ucfirst($group->body),
                'group_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group->group_url), 'sitegroup_entry_view', true) . '"  >' . ucfirst($group->getTitle()) . ' </a>',
                'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group->group_url), 'sitegroup_entry_view', true),
            ));
        }
    }

    /**
     * Check here that show payment link or not
     * $params $groupId : Id of group
     * @return bool $showLink
     * */
    public function canShowPaymentLink($group_id) {
        if (!$this->hasPackageEnable())
            return;

        $showLink = true;
        $group = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        if (!empty($group->declined)) {
            return (bool) false;
        }
        if (!empty($group)) {
            if (!$this->isGroupOwner($group)) {
                return (bool) false;
            }
            $package = $group->getPackage();
            if ($package->isFree()) {
                return (bool) false;
            }

            if (empty($group->expiration_date) || $group->expiration_date === "0000-00-00 00:00:00") {
                return (bool) true;
            }

            if ($group->status != "initial" && $group->status != "overdue") {
                return (bool) false;
            }

            if (($package->isOneTime()) && !$package->hasDuration() && !empty($group->approved)) {
                return false;
            }
        } else {
            $showLink = false;
        }
        return (bool) $showLink;
    }

    /**
     * Check here that show renew link or not
     * $params $groupId : Id of group
     * @return bool $showLink
     * */
    public function canShowRenewLink($group_id) {
        if (!$this->hasPackageEnable())
            return;
        $showLink = false;
        $group = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        if (!empty($group->declined)) {
            return (bool) false;
        }
        if (!empty($group)) {
            if (!$this->isGroupOwner($group)) {
                return (bool) false;
            }
            $package = $group->getPackage();

            if (!$package->isOneTime() || $package->isFree() || (!empty($package->level_id) && !in_array($group->getOwner()->level_id, explode(",", $package->level_id)))) {
                return (bool) false;
            }
            if ($package->renew) {
                if (!empty($group->expiration_date) && $group->status != "initial" && $group->status != "overdue") {
                    $diff_days = round((strtotime($group->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                    if ($diff_days <= $package->renew_before || $group->expiration_date <= date('Y-m-d H:i:s')) {
                        return (bool) true;
                    }
                }
            }
        }
        return (bool) $showLink;
    }

    /**
     * Check here that show renew link  or not for admin
     * $params $groupId : Id of group
     * @return bool $showLink
     * */
    public function canAdminShowRenewLink($group_id) {
        if (!$this->hasPackageEnable())
            return false;

        $showLink = false;
        $group = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        if (!empty($group)) {
            if (!empty($group->approved) && $group->expiration_date !== "2250-01-01 00:00:00")
                $showLink = true;
        }
        return (bool) $showLink;
    }

    /**
     * DISAPROVED AFTER EXPIRY GROUP THIS IS USE ONLY FOR ENABLE PACKAGE MENAGEMENT
     * @params array $params
     * */
    public function updateExpiredGroups($params = array()) {

//PACKAGE MANAGMENT NOT ENABLE
        if (!$this->hasPackageEnable())
            return;

        $table = Engine_Api::_()->getDbtable('groups', 'sitegroup');

        $rName = $table->info('name');
//LIST FOR GROUPS WHICH ARE EXPIRIED NOW AND SEND MAIL
        $select = $table->select()
                ->from($rName, array('group_id'))
                ->where('status <>  ?', 'expired')
                ->where('approved = ?', '1')
                ->where('expiration_date <= ?', date('Y-m-d H:i:s'));
        //START GROUP-EVENT CODE

        $sitegroupeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
        $siteeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
        $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        foreach ($table->fetchAll($select) as $group) {
            $this->sendMail("EXPIRED", $group->group_id);
        }

//UPDATE THE STATUS
        $table->update(array(
            'approved' => 0,
            'status' => 'expired'
                ), array(
            'status <>?' => 'expired',
            'expiration_date <=?' => date('Y-m-d H:i:s'),
        ));
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.task.updateexpiredgroups', time());

        $select = $table->select()
                ->from($rName, array('group_id'))
                ->where('status =  ?', 'expired');
        foreach ($table->fetchAll($select) as $group) {
            if ($sitegroupeventEnabled) {
                //FETCH Notes CORROSPONDING TO THAT Group ID
                $sitegroupeventtable = Engine_Api::_()->getItemTable('sitegroupevent_event');
                $select = $sitegroupeventtable->select()
                        ->from($sitegroupeventtable->info('name'), 'event_id')
                        ->where('group_id = ?', $group->group_id);
                $rows = $sitegroupeventtable->fetchAll($select)->toArray();
                if (!empty($rows)) {
                    foreach ($rows as $key => $event_ids) {
                        $event_id = $event_ids['event_id'];
                        if (!empty($event_id)) {
                            $sitegroupeventtable->update(array(
                                'search' => '0'
                                    ), array(
                                'event_id =?' => $event_id
                            ));
                        }
                    }
                }
            }

            if ($siteeventEnabled) {
                //FETCH Notes CORROSPONDING TO THAT Group ID
                $siteeventtable = Engine_Api::_()->getItemTable('siteevent_event');
                $select = $siteeventtable->select()
                        ->from($siteeventtable->info('name'), 'event_id')
                        ->where('parent_type = ?', 'sitegroup_group')
                        ->where('parent_id = ?', $group->group_id);
                $rows = $siteeventtable->fetchAll($select)->toArray();
                if (!empty($rows)) {
                    foreach ($rows as $key => $event_ids) {
                        $event_id = $event_ids['event_id'];
                        if (!empty($event_id)) {
                            $siteeventtable->update(array(
                                'search' => '0'
                                    ), array(
                                'event_id =?' => $event_id
                            ));
                        }
                    }
                }
            }

            if ($sitevideoEnabled) {
                //FETCH Notes CORROSPONDING TO THAT Group ID
                $sitevideotable = Engine_Api::_()->getItemTable('sitevideo_video');
                $select = $sitevideotable->select()
                        ->from($sitevideotable->info('name'), 'video_id')
                        ->where('parent_type = ?', 'sitegroup_group')
                        ->where('parent_id = ?', $group->group_id);
                $rows = $sitevideotable->fetchAll($select)->toArray();
                if (!empty($rows)) {
                    foreach ($rows as $key => $video_ids) {
                        $video_id = $video_ids['video_id'];
                        if (!empty($video_id)) {
                            $sitevideotable->update(array(
                                'search' => '0'
                                    ), array(
                                'video_id =?' => $video_id
                            ));
                        }
                    }
                }
            }

            $table->update(array(
                'search' => 0
                    ), array(
                'status =?' => 'expired',
            ));
        }
    }

    /**
     * Get expiry date for group
     * $params object $group
     * @return date
     * */
    public function getExpiryDate($group) {
        if (empty($group->expiration_date) || $group->expiration_date === "0000-00-00 00:00:00")
            return "-";
        $translate = Zend_Registry::get('Zend_Translate');
        if ($group->expiration_date === "2250-01-01 00:00:00")
            return $translate->translate('Never Expires');
        else {
            if (strtotime($group->expiration_date) < time())
                return "Expired";

            return date("M d,Y g:i A", strtotime($group->expiration_date));
        }
    }

    /**
     * Get status of group
     * $params object $group
     * @return string
     * */
    public function getGroupStatus($group) {
        $translate = Zend_Registry::get('Zend_Translate');
        if (!empty($group->declined)) {
            return "<span style='color: red;'>" . $translate->translate("Declined") . "</span>";
        }

        if (!empty($group->pending)) {
            return $translate->translate("Approval Pending");
        }
        if (!empty($group->approved)) {
            return $translate->translate("Approved");
        }


        if (empty($group->approved)) {
            return $translate->translate("Dis-Approved");
        }

        return "Approved";
    }

    /**
     * On installation time enable submodule for default package
     * $params string $modulename
     * */
    public function oninstallPackageEnableSubMOdules($modulename) {
        if (!Engine_Api::_()->sitegroup()->hasPackageEnable())
            return;
        $package = Engine_Api::_()->getItemtable('sitegroup_package')->fetchRow(array('defaultpackage = ?' => 1));
        if (!empty($package)) {
            $values = array();
            $values = unserialize($package->modules);
            $values[] = $modulename;
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $package->modules = serialize($values);
                $package->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

//Count the no of likes on a group
    public function getGroupLikes($values = array()) {
        if (empty($values['group_id']))
            return;

        $group_id = $values['group_id'];
        $likeTable = Engine_Api::_()->getItemTable('core_like');
        $like_name = $likeTable->info('name');

        $like_select = $likeTable->select()
                ->from($like_name)
                ->where('resource_type = ?', 'sitegroup_group')
                ->where('resource_id = ?', $group_id);

        if (!empty($values['startTime']) && !empty($values['endTime'])) {
            $like_select->where($like_name . '.creation_date >= ?', gmdate('Y-m-d H:i:s', $values['startTime']))
                    ->where($like_name . '.creation_date < ?', gmdate('Y-m-d H:i:s', $values['endTime']));
        }
        return count($like_select->query()->fetchAll());
    }

//Calculate the no of likes on a group date or month wise
    public function getReportLikes($values = array()) {
        if (empty($values['group_id']))
            return;

        $group_id = $values['group_id'];
        $likeTable = Engine_Api::_()->getItemTable('core_like');
        $like_name = $likeTable->info('name');

        $like_select = $likeTable->select()
                ->from($like_name, array('COUNT(like_id) as group_likes', 'creation_date'))
                ->where('resource_type = ?', 'sitegroup_group')
                ->where('resource_id = ?', $group_id)
                ->group('resource_id');

        if (!empty($values['startTime']) && !empty($values['endTime'])) {
            $like_select->where($like_name . '.creation_date >= ?', gmdate('Y-m-d', $values['startTime']))
                    ->where($like_name . '.creation_date < ?', gmdate('Y-m-d', $values['endTime']));
        }

        if (!empty($values['user_report'])) {
            if (!empty($values['time_summary'])) {
                if ($values['time_summary'] == 'Monthly') {
                    $startTime = date('Y-m', mktime(0, 0, 0, $values['month_start'], date('d'), $values['year_start']));
                    $endTime = date('Y-m', mktime(0, 0, 0, $values['month_end'], date('d'), $values['year_end']));
                } else {
                    if (!empty($values['start_daily_time'])) {
                        $start = $values['start_daily_time'];
                    }
                    if (!empty($values['start_daily_time'])) {
                        $end = $values['end_daily_time'];
                    }
                    $startTime = date('Y-m-d', $start);
                    $endTime = date('Y-m-d', $end);
                }
            }
            if (!empty($values['time_summary'])) {

                switch ($values['time_summary']) {

                    case 'Monthly':
                        $like_select
                                ->where("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m') >= ?", $startTime)
                                ->where("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m') <= ?", $endTime);
                        if (!isset($values['total_stats']) && empty($values['total_stats'])) {
                            $like_select->group("DATE_FORMAT(" . $like_name . " .creation_date, '%m')");
                        }
                        break;

                    case 'Daily':
                        $like_select
                                ->where("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m-%d') >= ?", $startTime)
                                ->where("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m-%d') <= ?", $endTime);
                        if (!isset($values['total_stats']) && empty($values['total_stats'])) {
                            $like_select->group("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m-%d')");
                        }
                        break;
                }
            }
        }
        $like_array = $likeTable->fetchAll($like_select)->toarray();
        return $like_array;
    }

//Calculate the no of comments on a group date or month wise
    public function getReportComments($values = array()) {
        if (empty($values['group_id']))
            return;

        $group_id = $values['group_id'];
        $commentTable = Engine_Api::_()->getItemTable('core_comment');
        $comment_name = $commentTable->info('name');

        $comment_select = $commentTable->select()
                ->from($comment_name, array('COUNT(comment_id) as group_comments', 'creation_date'))
                ->where('resource_type = ?', 'sitegroup_group')
                ->where('resource_id = ?', $group_id)
                ->group('resource_id');

        if (!empty($values['startTime']) && !empty($values['endTime'])) {
            $comment_select->where($comment_name . '.creation_date >= ?', gmdate('Y-m-d', $values['startTime']))
                    ->where($comment_name . '.creation_date < ?', gmdate('Y-m-d', $values['endTime']));
        }

        if (!empty($values['user_report'])) {
            if (!empty($values['time_summary'])) {
                if ($values['time_summary'] == 'Monthly') {
                    $startTime = date('Y-m', mktime(0, 0, 0, $values['month_start'], date('d'), $values['year_start']));
                    $endTime = date('Y-m', mktime(0, 0, 0, $values['month_end'], date('d'), $values['year_end']));
                } else {
                    if (!empty($values['start_daily_time'])) {
                        $start = $values['start_daily_time'];
                    }
                    if (!empty($values['start_daily_time'])) {
                        $end = $values['end_daily_time'];
                    }
                    $startTime = date('Y-m-d', $start);
                    $endTime = date('Y-m-d', $end);
                }
            }
            if (!empty($values['time_summary'])) {

                switch ($values['time_summary']) {

                    case 'Monthly':
                        $comment_select
                                ->where("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m') >= ?", $startTime)
                                ->where("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m') <= ?", $endTime);
                        if (!isset($values['total_stats']) && empty($values['total_stats'])) {
                            $comment_select->group("DATE_FORMAT(" . $comment_name . " .creation_date, '%m')");
                        }
                        break;

                    case 'Daily':
                        $comment_select
                                ->where("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m-%d') >= ?", $startTime)
                                ->where("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m-%d') <= ?", $endTime);
                        if (!isset($values['total_stats']) && empty($values['total_stats'])) {
                            $comment_select->group("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m-%d')");
                        }
                        break;
                }
            }
        }
        $comment_array = $commentTable->fetchAll($comment_select)->toarray();
        return $comment_array;
    }

//Count the no of comments on a group
    public function getGroupComments($values = array()) {
        if (empty($values['group_id']))
            return;

        $group_id = $values['group_id'];
        $commentTable = Engine_Api::_()->getItemTable('core_comment');
        $comment_name = $commentTable->info('name');

        $comment_select = $commentTable->select()
                ->from($comment_name)
                ->where('resource_type = ?', 'sitegroup_group')
                ->where('resource_id = ?', $group_id);

        if (!empty($values['startTime']) && !empty($values['endTime'])) {
            $comment_select->where($comment_name . '.creation_date >= ?', gmdate('Y-m-d H:i:s', $values['startTime']))
                    ->where($comment_name . '.creation_date < ?', gmdate('Y-m-d H:i:s', $values['endTime']));
        }
        return count($comment_select->query()->fetchAll());
    }

    // This function checks that whether comments have to be displayed in insights or not
    public function displayCommentInsights() {
        $userlayout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
        if (!empty($userlayout)) {
            $ContentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');
        } else {
            $ContentTable = Engine_Api::_()->getDbtable('content', 'core');
        }
        $select = $ContentTable->select()->where('name= ?', 'sitegroup.info-sitegroup')->limit(1);
        $infoWidget = $ContentTable->fetchRow($select);
        if (!empty($infoWidget)) {
            return true;
        } else {
            return false;
        }
    }

    public function hasGroupLike($RESOURCE_ID, $viewer_id) {
        if (empty($RESOURCE_ID) || empty($viewer_id))
            return false;

        $sub_status_table = Engine_Api::_()->getItemTable('core_like');

        $sub_status_select = $sub_status_table->select()
                ->where('resource_type = ?', 'sitegroup_group')
                ->where('resource_id = ?', $RESOURCE_ID)
                ->where('poster_id = ?', $viewer_id);
        $fetch_sub = $sub_status_table->fetchRow($sub_status_select);
        if (!empty($fetch_sub))
            return true;
        else
            return false;
    }

    /**
     * check photo show in light box or not
     * */
    public function canShowPhotoLightBox() {
        global $sitegroupalbum_isLightboxActive;
        if (empty($sitegroupalbum_isLightboxActive)) {
            return;
        } else {
            return SEA_SITEGROUPALBUM_LIGHTBOX;
        }
    }

    /**
     * check in case draft, not approved viewer can view group
     * */
    public function canViewGroup($sitegroup) {
        $can_view = true;
        if (empty($sitegroup->draft) || (empty($sitegroup->aprrove_date)) || (empty($sitegroup->approved) && empty($sitegroup->pending) ) || !empty($sitegroup->declined) || ($this->hasPackageEnable() && $sitegroup->expiration_date !== "2250-01-01 00:00:00" && strtotime($sitegroup->expiration_date) < time())) {
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
            if (empty($isManageAdmin)) {
                $can_view = false;
            }
        }
        return $can_view;
    }

    public function attachGroupActivity($sitegroup) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($sitegroup->getOwner(), $sitegroup, 'sitegroup_new');

            if ($action != null) {
                Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action, true);
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitegroup);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function onGroupDelete($group_id) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($group_id) || empty($viewer_id)) {
            return;
        }

        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        Engine_Api::_()->getDbtable('locations', 'sitegroup')->delete(array('group_id =?' => $group_id));

        //FETCH PHOTO AND OTHER BELONGINGS
        $table = Engine_Api::_()->getItemTable('sitegroup_photo');
        $select = $table->select()->where('group_id = ?', $group_id);
        $rows = $table->fetchAll($select);
        if (!empty($rows)) {
            foreach ($rows as $sitegroupphoto) {
                //DELETE PHOTO AND OTHER BELONGINGS
                $sitegroupphoto->delete();
            }
        }

        $table = Engine_Api::_()->getItemTable('sitegroup_album');
        $select = $table->select()->where('group_id = ?', $group_id);
        $rows = $table->fetchAll($select);
        if (!empty($rows)) {
            foreach ($rows as $sitegroupalbum) {
                //DELETE ALBUM AND OTHER BELONGINGS
                $sitegroupalbum->delete();
            }
        }

        //END GROUP-ALBUM CODE
        //START GROUP-BADGE CODE
        $sitegroupbadgeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge');
        if ($sitegroupbadgeEnabled) {
            //DELETE BADGE REQUESTS CORROSPONDING TO THAT GROUP ID
            Engine_Api::_()->getItemTable('sitegroupbadge_badgerequest')->delete(array('group_id = ?' => $group_id));
        }
        //END GROUP-BADGE CODE
        //START GROUP-DISCUSSION CODE
        $sitegroupDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion');
        if ($sitegroupDiscussionEnabled) {

            $table = Engine_Api::_()->getItemTable('sitegroup_topic');
            $select = $table->select()->where('group_id = ?', $group_id);
            $rows = $table->fetchAll($select);
            if (!empty($rows)) {
                foreach ($rows as $topic) {
                    $topic->delete();
                }
            }

            $table = Engine_Api::_()->getItemTable('sitegroup_post');
            $select = $table->select()->where('group_id = ?', $group_id);
            $rows = $table->fetchAll($select);
            if (!empty($rows)) {
                foreach ($rows as $post) {
                    $post->delete();
                }
            }

            Engine_Api::_()->getDbtable('topicwatches', 'sitegroup')->delete(array('group_id =?' => $group_id));
        }
        //END GROUP-DISCUSSION CODE
        //START GROUP-DOCUMENT CODE
        $sitegroupdocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
        if ($sitegroupdocumentEnabled) {

            //FETCH DOCUMENTS CORROSPONDING TO THAT SITEGROUP ID
            $table = Engine_Api::_()->getItemTable('sitegroupdocument_document');
            $select = $table->select()
                    ->from($table->info('name'), 'document_id')
                    ->where('group_id = ?', $group_id);
            $rows = $table->fetchAll($select)->toArray();
            if (!empty($rows)) {
                foreach ($rows as $key => $document_ids) {
                    $document_id = $document_ids['document_id'];
                    if (!empty($document_id)) {
                        Engine_Api::_()->sitegroupdocument()->deleteContent($document_id);
                    }
                }
            }
        }
        //END GROUP-DOCUMENT CODE
        //START GROUP-EVENT CODE
        $sitegroupeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
        if ($sitegroupeventEnabled) {
            //FETCH Notes CORROSPONDING TO THAT Group ID
            $table = Engine_Api::_()->getItemTable('sitegroupevent_event');
            $select = $table->select()
                    ->from($table->info('name'), 'event_id')
                    ->where('group_id = ?', $group_id);
            $rows = $table->fetchAll($select)->toArray();
            if (!empty($rows)) {
                foreach ($rows as $key => $event_ids) {
                    $event_id = $event_ids['event_id'];
                    if (!empty($event_id)) {
                        //DELETE EVENT, ALBUM AND EVENT IMAGES
                        Engine_Api::_()->sitegroupevent()->deleteContent($event_id);
                    }
                }
            }
        }
        //END GROUP-EVENT CODE
        //START ADVANCED-EVENT CODE
        $siteeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
        $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        if ($siteeventEnabled) {
            //FETCH Notes CORROSPONDING TO THAT Group ID
            $table = Engine_Api::_()->getItemTable('siteevent_event');
            $select = $table->select()
                    ->from($table->info('name'), 'event_id')
                    ->where('parent_type = ?', 'sitegroup_group')
                    ->where('parent_id = ?', $group_id);
            $rows = $table->fetchAll($select)->toArray();
            if (!empty($rows)) {
                foreach ($rows as $key => $event_ids) {
                    $resource = Engine_Api::_()->getItem('siteevent_event', $event_ids['event_id']);
                    if ($resource)
                        $resource->delete();
                }
            }
        }
        if ($sitevideoEnabled) {
            //FETCH Notes CORROSPONDING TO THAT Group ID
            $table = Engine_Api::_()->getItemTable('sitevideo_video');
            $select = $table->select()
                    ->from($table->info('name'), 'video_id')
                    ->where('parent_type = ?', 'sitegroup_group')
                    ->where('parent_id = ?', $group_id);
            $rows = $table->fetchAll($select)->toArray();
            if (!empty($rows)) {
                foreach ($rows as $key => $video_ids) {
                    $resource = Engine_Api::_()->getItem('sitevideo_video', $video_ids['video_id']);
                    if ($resource)
                        $resource->delete();
                }
            }
        }
        //END ADVANCED-EVENT CODE
        //START GROUP-FORM CODE
        $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
        if ($sitegroupFormEnabled) {
            $mapstable = Engine_Api::_()->fields()->getTable('sitegroupform', 'maps');
            $optiontable = Engine_Api::_()->fields()->getTable('sitegroupform', 'options');

            $groupquetion_table = Engine_Api::_()->getDbtable('groupquetions', 'sitegroupform');
            $sitegroupform_table = Engine_Api::_()->getDbtable('sitegroupforms', 'sitegroupform');
            $select = $groupquetion_table->select()->where('group_id =?', $group_id);
            $optionid = $groupquetion_table->fetchRow($select);
            $option_id = $optionid->option_id;
            if (!empty($option_id)) {
                $matatable = Engine_Api::_()->fields()->getTable('sitegroupform', 'meta');
                $select_options = $matatable->select()->where('option_id =?', $option_id);
                $select_options_result = $select_options->from($matatable->info('name'), array('field_id'));
                $result = $matatable->fetchAll($select_options_result)->toArray();
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    foreach ($result as $key => $id) {
                        $field_id = $id['field_id'];
                        $matatable->delete(array('field_id =?' => $field_id));
                        $optiontable->delete(array('field_id =?' => $field_id));
                    }
                    $optiontable->delete(array('option_id =?' => $option_id));
                    $groupquetion_table->delete(array('option_id =?' => $option_id));
                    $mapstable->delete(array('option_id =?' => $option_id));
                    $sitegroupform_table->delete(array('group_id =?' => $group_id));
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }
        }
        //END GROUP-FORM CODE
        //START GROUP-NOTE CODE
        $sitegroupnoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote');
        if ($sitegroupnoteEnabled) {
            //FETCH Notes CORROSPONDING TO THAT Group ID
            $table = Engine_Api::_()->getItemTable('sitegroupnote_note');
            $select = $table->select()
                    ->from($table->info('name'), 'note_id')
                    ->where('group_id = ?', $group_id);
            $rows = $table->fetchAll($select)->toArray();
            if (!empty($rows)) {
                foreach ($rows as $key => $note_ids) {
                    $note_id = $note_ids['note_id'];
                    if (!empty($note_id)) {

                        //DELETE NOTE, ALBUM AND NOTE IMAGES
                        Engine_Api::_()->sitegroupnote()->deleteContent($note_id);
                    }
                }
            }
        }
        //END GROUP-NOTE CODE
        //START GROUP-OFFER CODE
        $sitegroupofferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer');
        if ($sitegroupofferEnabled) {
            //FETCH Offers CORROSPONDING TO THAT Group ID
            $table = Engine_Api::_()->getItemTable('sitegroupoffer_offer');
            $select = $table->select()->where('group_id = ?', $group_id);
            $rows = $table->fetchAll($select);
            if (!empty($rows)) {
                foreach ($rows as $sitegroupoffer) {
                    Engine_Api::_()->sitegroupoffer()->deleteContent($sitegroupoffer->offer_id);
                }
            }
        }
        //END GROUP-OFFER CODE
        //START GROUP-POLL CODE
        $sitegrouppollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
        if ($sitegrouppollEnabled) {
            //FETCH POLLS CORROSPONDING TO THAT GROUP ID
            $table = Engine_Api::_()->getItemTable('sitegrouppoll_poll');
            $select = $table->select()->where('group_id = ?', $group_id);
            $rows = $table->fetchAll($select);
            if (!empty($rows)) {
                foreach ($rows as $sitegrouppoll) {
                    //DELETE POLL AND OTHER BELONGINGS
                    $sitegrouppoll->delete();
                }
            }
        }
        //END GROUP-POLL CODE
        //START GROUP-REVIEW CODE
        $sitegroupreviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');
        if ($sitegroupreviewEnabled) {

            //FETCH REVIEWS
            $table = Engine_Api::_()->getItemTable('sitegroupreview_review');
            $select = $table->select()->where('group_id = ?', $group_id);
            $rows = $table->fetchAll($select);

            if (!empty($rows)) {
                foreach ($rows as $review) {
                    Engine_Api::_()->sitegroupreview()->deleteContent($review->review_id);
                }
            }
        }
        //END GROUP-REVIEW CODE
        //START GROUP-WISHLIST CODE
        $sitegroupwishlistEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupwishlist');
        if ($sitegroupwishlistEnabled) {
            Engine_Api::_()->getDbtable('groups', 'sitegroupwishlist')->delete(array('group_id =?' => $group_id));
        }
        //END GROUP-WISHLIST CODE
        //START GROUP-VIDEO CODE
        $sitegroupvideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo');
        if ($sitegroupvideoEnabled) {
            //FETCH VIDEOS CORROSPONDING TO THAT SITEGROUP ID


            $table = Engine_Api::_()->getItemTable('sitegroupvideo_video');
            $select = $table->select()->where('group_id = ?', $group_id);
            $rows = $table->fetchAll($select);
            if (!empty($rows)) {
                foreach ($rows as $video) {
                    //DELETE VIDEO AND OTHER BELONGINGS
                    Engine_Api::_()->getDbtable('ratings', 'sitegroupvideo')->delete(array('video_id = ?' => $video->video_id));
                    $video->delete();
                }
            }
        }
        //END GROUP-VIDEO CODE
        //START GROUP-MUSIC CODE
        $sitegroupmusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic');
        if ($sitegroupmusicEnabled) {
            //FETCH PLAYLIST CORROSPONDING TO THAT GROUP ID
            $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sitegroupmusic');
            $playlistSelect = $playlistTable->select()->where('group_id = ?', $group_id);
            foreach ($playlistTable->fetchAll($playlistSelect) as $playlist) {
                foreach ($playlist->getSongs() as $song) {
                    $song->deleteUnused();
                }
                $playlist->delete();
            }
        }
        //END GROUP-MUSIC CODE
        //START GROUP-MEMBER CODE
        $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
        if ($sitegroupmemberEnabled) {
            $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
            $membershipTable->delete(array('resource_id =?' => $group_id, 'group_id =?' => $group_id));
        }
        //END GROUP-MUSIC CODE
        //FINALLY START GROUP CODE

        $searchTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'search');
        $valuesTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'values');

        $groupstatisticsTable = Engine_Api::_()->getDbtable('groupstatistics', 'sitegroup');

        $writesTable = Engine_Api::_()->getDbtable('writes', 'sitegroup');
        $listsTable = Engine_Api::_()->getDbtable('lists', 'sitegroup');

        //$viewedsTable = Engine_Api::_()->getDbtable('vieweds', 'sitegroup');
        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');

        $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitegroup');

        $authAllowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
        $claimTable = Engine_Api::_()->getDbtable('claims', 'sitegroup');
        $sitegroupitemofthedaysTable = Engine_Api::_()->getDbtable('itemofthedays', 'sitegroup');

        $layoutcontentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');
        $layoutcontentgroupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
        //GETTING THE CONTENTGROUP ID FROM CONTENTGROUPS TABLE SO THAT WE CAN REMOVE THE CONTENT FROM CONTENT TABLE ALSO.
        $LayoutContentGroupName = $layoutcontentgroupTable->info('name');

        $select = $layoutcontentgroupTable->select()->from($LayoutContentGroupName, 'contentgroup_id')->where('group_id = ?', $group_id);
        $LayoutContentGroupid = $layoutcontentgroupTable->fetchRow($select);
        if (!empty($LayoutContentGroupid)) {
            $LayoutContentGroupid = $LayoutContentGroupid->toarray();
        }

        $searchTable->delete(array('item_id =?' => $group_id));
        $valuesTable->delete(array('item_id =?' => $group_id));
        $writesTable->delete(array('group_id =?' => $group_id));
        $listsTable->delete(array('group_id =?' => $group_id));
        $groupstatisticsTable->delete(array('group_id =?' => $group_id));

        // $viewedsTable->delete(array('group_id =?' => $group_id));
        $manageadminsTable->delete(array('group_id =?' => $group_id));
        $locationsTable->delete(array('group_id =?' => $group_id));

        $sitegroupitemofthedaysTable->delete(array('resource_id =?' => $group_id, 'resource_type' => 'sitegroup_group'));

        $authAllowTable->delete(array('resource_id =?' => $group_id, 'resource_type =?' => 'sitegroup_group'));
        $claimTable->delete(array('group_id =?' => $group_id));

        //DELETE FIELD ENTRIES IF EXISTS
        $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'values');
        $fieldvalueTable->delete(array(
            'item_id = ?' => $group_id,
        ));

        $fieldsearchTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'search');
        $fieldsearchTable->delete(array(
            'item_id = ?' => $group_id,
        ));

        if (!empty($LayoutContentGroupid)) {
            $layoutcontentTable->delete(array('contentgroup_id =?' => $LayoutContentGroupid['contentgroup_id']));
        }

        $layoutcontentgroupTable->delete(array('group_id =?' => $group_id));
        $sitegroup->cancel();
        $sitegroup->delete();

        //END GROUP CODE
    }

    public function isEnabledModPackage($mod) {
        if (!empty($mod)) {
            $arrayName = 'sitegroup.mod.types';
        } else {
            $arrayName = 'sitegroup.mod.settings';
        }
        return Engine_Api::_()->getApi('settings', 'core')->getSetting($arrayName, null);
    }

    public function isModulesActivated() {
        $groupModArray = array(
            // 'sitegroupalbum' => 'Groups / Communities - Albums Extension',
            'sitegroupbadge' => 'Groups / Communities - Badges Extension',
            'sitegroupdocument' => 'Groups / Communities - Documents Extension',
            'sitegroupevent' => 'Groups / Communities - Events Extension',
            'sitegroupform' => 'Groups / Communities - Form Extension',
            'sitegroupinvite' => 'Groups / Communities - Inviter Extension',
            'sitegroupnote' => 'Groups / Communities - Notes Extension',
            'sitegroupoffer' => 'Groups / Communities - Offers Extension',
            'sitegrouppoll' => 'Groups / Communities - Polls Extension',
            'sitegroupreview' => 'Groups / Communities - Reviews and Ratings Extension',
            'sitegroupvideo' => 'Groups / Communities - Videos Extension',
            'sitegroupmusic' => 'Groups / Communities - Music Extension',
            //  'sitegroupmember' => 'Groups / Communities - Group Members Extension',
            'sitegrouplikebox' => 'Groups / Communities - Like Box',
            'communityad' => 'Advertisements / Community Ads Plugin',
            'sitegroupintegration' => 'Groups / Communities - Multiple Listings and Products Showcase Extension',
            'siteevent' => 'Advanced Events',
            'sitevideo' => 'Advanced Videos',
        );
        $notActivatedModArray = array();
        foreach ($groupModArray as $modNameKey => $modNameValue) {
            $isModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($modNameKey);
            if ($modNameKey == 'communityad') {
                $isModuleActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.navi.auth', null);
            } else {
                $isModuleActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting($modNameKey . '.isActivate', null);
            }
            //Condition: If Plugin enabled but not activated.
            if (!empty($isModuleEnabled) && empty($isModuleActivate)) {
                $notActivatedModArray[$modNameKey] = $modNameValue;
            }
        }
        return $notActivatedModArray;
    }

    public function isEnabled() {
        $hostType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.viewgroup.sett', 0);
        $hostName = convert_uudecode($hostType);
        if ($hostName == 'localhost' || strpos($hostName, '192.168.') != false || strpos($hostName, '127.0.') != false) {
            return;
        }

        return 1;
    }

    public function isPluginActivate($modName) {
        return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting($modName . '.isActivate', 0);
    }

    /**
     *  CHECK Payment PLUGIN ENABLE / DISABLE
     * */
    public function enablePaymentPlugin() {
        return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('payment');
    }

    /**
     *  Check Viewer able to edit style
     * */
    public function allowStyle() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            return (bool) Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('sitegroup_group', $viewer->level_id, 'style');
        } else {
            return (bool) 0;
        }
    }

    /**
     * get viewer like groups
     */
    public function getMyLikeGroups($params = array()) {
//    $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
//    $likeName = $likeTable->info('name');
//    $select = $likeTable->select()
//            ->where($likeName . '.poster_id = ?', $params['poster_id'])
//            ->where($likeName . '.poster_type = ?', $params['poster_type'])
//            ->where($likeName . '.resource_type = ?', $params['resource_type'])
//            ->order($likeName . '.creation_date DESC');
//    return $likeTable->fetchAll($select);
    }

    /**
     * Gets member like groups
     *
     * $member User_Model_User 
     */
    public function getMemberLikeGroupsOfIds($member) {
        $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
        $likeName = $likeTable->info('name');
        return $select = $likeTable->select()
                ->from($likeName, "resource_id")
                ->where($likeName . '.poster_id = ?', $member->getIdentity())
                ->where($likeName . '.poster_type = ?', $member->getType())
                ->where($likeName . '.resource_type = ?', 'sitegroup_group')
                ->order($likeName . '.creation_date DESC')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
        ;
    }

    /**
     * Gets feed for which viewer like
     *
     * @$user User_Model_User
     * @param array $params
     */
    public function getFeedActionLikedGroups(User_Model_User $user, array $params = array()) {
//    $ids = array();
//    if (!(bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.feed.likegroup', 0))
//      return $ids;
//    $getMyLikes = $this->getMyLikeGroups(array("poster_type" => $user->getType(), "poster_id" => $user->getIdentity(), "resource_type" => "sitegroup_group"));
//    $count = count($getMyLikes);
//
//    if (!empty($count)) {
//      $resource_ids = array();
//      foreach ($getMyLikes as $likeItems) {
//        $resource_ids[] = $likeItems->resource_id;
//      }
//
//      if (!empty($resource_ids)) {
//        //Proc args
//        extract($params); //action_id, limit, min_id, max_id
//        $actionDbTable = Engine_Api::_()->getDbtable('actions', 'activity');
//        $typesAdmin = array("sitegroup_post_self", "sitegroupalbum_admin_photo_new", "sitegroupvideo_admin_new", "sitegroupevent_admin_new", "sitegroupnote_admin_new", "sitegrouppoll_admin_new", "sitegroupdocument_admin_new", "sitegroupoffer_admin_new", "sitegroup_admin_topic_create", "sitegroupmusic_admin_new", "sitegroup_profile_photo_update");
//        $select = $actionDbTable->select()
//                ->where("type in (?)", new Zend_Db_Expr("'" . join("', '", $typesAdmin) . "'"))
//                ->where("subject_type = ? ", "sitegroup_group")
//                ->where("subject_id IN(?)", new Zend_Db_Expr(join(',', $resource_ids)))
//                ->order('action_id DESC')
//                ->limit($limit);
//
//        if (null !== $action_id) {
//          $select->where('action_id = ?', $action_id);
//        } else {
//          if (null !== $min_id) {
//            $select->where('action_id >= ?', $min_id);
//          } else if (null !== $max_id) {
//            $select->where('action_id <= ?', $max_id);
//          }
//        }
//        $results = $actionDbTable->fetchAll($select);
//        foreach ($results as $actionData)
//          $ids[] = $actionData->action_id;
//      }
//    }
//    return $ids;
    }

    /**
     * Gets feed type group title and photo is enable
     *
     * @return bool
     */
    public function isFeedTypeGroupEnable() {
        return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.feed.type', 0);
    }

    /**
     * Set default widget in core groups table
     *
     * @param object $table
     * @param string $tablename
     * @param int $group_id
     * @param string $type
     * @param string $widgetname
     * @param int $middle_id 
     * @param int $order 
     * @param string $title 
     * @param int $titlecount    
     */
    function setDefaultDataWidget($table, $tablename, $group_id, $type, $widgetname, $middle_id, $order, $title = null, $titlecount = null, $advanced_activity_params = null) {

        $selectWidgetId = $table->select()
                ->where('group_id =?', $group_id)
                ->where('type = ?', $type)
                ->where('name = ?', $widgetname)
                ->where('parent_content_id = ?', $middle_id)
                ->limit(1);
        $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
        if (empty($fetchWidgetContentId)) {
            $contentWidget = $table->createRow();
            $contentWidget->group_id = $group_id;
            $contentWidget->type = $type;
            $contentWidget->name = $widgetname;
            $contentWidget->parent_content_id = $middle_id;
            $contentWidget->order = $order;
            if (empty($advanced_activity_params) && $title && $titlecount) {
                $contentWidget->params = "{\"title\":\"$title\",\"titleCount\":$titlecount}";
            } else {
                $contentWidget->params = "$advanced_activity_params";
            }
            $contentWidget->save();
        }
    }

    /**
     * Return group paginator
     *
     * @param int $total_items
     * @param int $items_per_group
     * @param int $p
     * @return paginator
     */
    public function makeGroup($total_items, $items_per_group, $p) {
        if (!$items_per_group)
            $items_per_group = 1;
        $maxgroup = ceil($total_items / $items_per_group);
        if ($maxgroup <= 0)
            $maxgroup = 1;
        $p = ( ($p > $maxgroup) ? $maxgroup : ( ($p < 1) ? 1 : $p ) );
        $start = ($p - 1) * $items_per_group;
        return array($start, $p, $maxgroup);
    }

    /**
     * Return count
     *
     * @param string $tablename
     * @param string $modulename
     * @param int $group_id
     * @param int $title_count
     * @return paginator
     */
    public function getTotalCount($group_id, $modulename, $tablename) {

        if ($modulename == 'siteevent' || $modulename == 'sitevideo') {
            $table = Engine_Api::_()->getDbtable($tablename, $modulename);
            $count = 0;
            $count = $table
                    ->select()
                    ->from($table->info('name'), array('count(*) as count'))
                    ->where("parent_type = ?", 'sitegroup_group')
                    ->where("parent_id =?", $group_id)
                    ->query()
                    ->fetchColumn();
        } else {
            $table = Engine_Api::_()->getDbtable($tablename, $modulename);
            $count = 0;
            $select = $table
                    ->select()
                    ->from($table->info('name'), array('count(*) as count'))
                    ->where("group_id = ?", $group_id);

            if ($tablename == 'albums' && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.hide.autogenerated', 1)) {
                $select->where($table->info('name') . '.default_value' . '= ?', 0);
                $select->where($table->info('name') . ".type is Null");
            }

            $count = $select->query()->fetchColumn();
        }
        return $count;
    }

    /**
     * Return tabid
     *
     * @param string $widgetname
     * @param int $group_id
     * @param int $layout
     * @return tabid
     */
    public function GetTabIdinfo($widgetname, $groupid, $layout) {

        global $sitegroup_GetTabIdType;
        $tab_id = '';
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            if (!$layout) {
                if (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
                    $tablecontent = Engine_Api::_()->getDbtable('content', 'sitemobile');
                    $select = $tablecontent->select()
                            ->from($tablecontent->info('name'), 'content_id')
                            ->where('name = ?', $widgetname)
                            ->limit(1);
                    $tab_id = $select->query()->fetchColumn();
                } elseif (Engine_Api::_()->sitemobile()->checkMode('tablet-mode')) {
                    $tablecontent = Engine_Api::_()->getDbtable('tabletcontent', 'sitemobile');
                    $select = $tablecontent->select()
                            ->from($tablecontent->info('name'), 'content_id')
                            ->where('name = ?', $widgetname)
                            ->limit(1);
                    $tab_id = $select->query()->fetchColumn();
                }
            } else {
                $table = Engine_Api::_()->getDbtable('mobileContentgroups', 'sitegroup');
                $select = $table->select()
                        ->from($table->info('name'), 'mobilecontentgroup_id')
                        ->where('name = ?', 'sitegroup_index_view')
                        ->where('group_id = ?', $groupid)
                        ->limit(1);
                $mobilecontentgroup_id = $select->query()->fetchColumn();
                if ($mobilecontentgroup_id) {

                    $tablecontent = Engine_Api::_()->getDbtable('mobileContent', 'sitegroup');
                    $select = $tablecontent->select()
                            ->from($tablecontent->info('name'), 'mobilecontent_id')
                            ->where('name = ?', $widgetname)
                            ->where('mobilecontentgroup_id = ?', $mobilecontentgroup_id)
                            ->limit(1);
                    $tab_id = $select->query()->fetchColumn();
                } else {
                    $group_id = $this->getMobileWidgetizedGroup()->page_id;
                    if (!empty($group_id)) {
                        $tablecontent = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitegroup');
                        $select = $tablecontent->select()
                                ->from($tablecontent->info('name'), 'mobileadmincontent_id')
                                ->where('name = ?', $widgetname)
                                ->where('group_id = ?', $group_id)
                                ->limit(1);
                        $tab_id = $select->query()->fetchColumn();
                    }
                }
            }
            return $tab_id;
        }

        if (!$layout) {
            $tablecontent = Engine_Api::_()->getDbtable('content', 'core');
            $select = $tablecontent->select()
                    ->from($tablecontent->info('name'), 'content_id')
                    ->where('name = ?', $widgetname)
                    ->where('page_id = ?', $this->getWidgetizedGroup()->page_id)
                    ->limit(1);
            $tab_id = $select->query()->fetchColumn();
        } else {

            $table = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
            $select = $table->select()
                    ->from($table->info('name'), 'contentgroup_id')
                    ->where('name = ?', 'sitegroup_index_view')
                    ->where('group_id = ?', $groupid)
                    ->limit(1);
            $contentgroup_id = $select->query()->fetchColumn();
            if ($contentgroup_id) {

                $tablecontent = Engine_Api::_()->getDbtable('content', 'sitegroup');
                $select = $tablecontent->select()
                        ->from($tablecontent->info('name'), 'content_id')
                        ->where('name = ?', $widgetname)
                        ->where('contentgroup_id = ?', $contentgroup_id)
                        ->limit(1);
                $tab_id = $select->query()->fetchColumn();
            } else {
                $group_id = $this->getWidgetizedGroup()->page_id;
                if (!empty($group_id)) {
                    $tablecontent = Engine_Api::_()->getDbtable('admincontent', 'sitegroup');
                    $select = $tablecontent->select()
                            ->from($tablecontent->info('name'), 'admincontent_id')
                            ->where('name = ?', $widgetname)
                            ->where('group_id = ?', $group_id)
                            ->limit(1);
                    $tab_id = $select->query()->fetchColumn();
                }
            }
        }

        return $sitegroup_GetTabIdType ? $tab_id : $sitegroup_GetTabIdType;
    }

    /**
     * Gets widgetized group
     *
     * @return Zend_Db_Table_Select
     */
    public function getWidgetizedGroup() {

        //GET CORE GROUP TABLE
        $tableNameGroup = Engine_Api::_()->getDbtable('pages', 'core');
        $select = $tableNameGroup->select()
                ->from($tableNameGroup->info('name'), array('page_id', 'description', 'keywords'))
                ->where('name =?', 'sitegroup_index_view')
                ->limit(1);

        return $tableNameGroup->fetchRow($select);
    }

    /**
     * Return parse string
     *
     * @param string $content
     * @return parse string
     */
    public function parseString($content) {
        return str_replace("'", "\'", trim($content));
    }

    /**
     * Return option of showing the widget of third type layout
     *
     * @param int $group_id
     * @param int $layout
     * @return third type layout show or not
     */
    public function getwidget($layout, $group_id) {
        if (!$layout) {
            $group_id = $this->getWidgetizedGroup()->page_id;
            if (!empty($group_id)) {
                $table = Engine_Api::_()->getDbtable('content', 'core');
                $selectContent = $table->select()
                        ->from($table->info('name'), 'page_id')
                        ->where("name IN ('core.container-tabs', 'sitegroup.widgetlinks-sitegroup')")
                        ->where('page_id =?', $group_id)
                        ->limit(1);
                $contentinfo = $selectContent->query()->fetchAll();
                if (empty($contentinfo)) {
                    $contentinformation = 0;
                } else {
                    $contentinformation = 1;
                }
            }
        } else {
            $table = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
            $select = $table->select()
                    ->from($table->info('name'), 'contentgroup_id')
                    ->where('name = ?', 'sitegroup_index_view')
                    ->where('group_id = ?', $group_id)
                    ->limit(1);
            $row = $table->fetchRow($select);
            if ($row !== null) {
                $group_id = $row->contentgroup_id;
                $table = Engine_Api::_()->getDbtable('content', 'sitegroup');
                $selectContent = $table->select()
                        ->from($table->info('name'), 'contentgroup_id')
                        ->where("name IN ('core.container-tabs', 'sitegroup.widgetlinks-sitegroup')")
                        ->where('contentgroup_id =?', $group_id);
                $contentinfo = $selectContent->query()->fetchAll();
                if (!empty($contentinfo)) {
                    $contentinformation = 1;
                } else {
                    $contentinformation = 0;
                }
            } else {
                $group_id = $this->getWidgetizedGroup()->page_id;
                $table = Engine_Api::_()->getDbtable('admincontent', 'sitegroup');
                $selectContent = $table->select()
                        ->from($table->info('name'), 'group_id')
                        ->where("name IN ('core.container-tabs', 'sitegroup.widgetlinks-sitegroup')")
                        ->where('group_id =?', $group_id);
                $contentinfo = $selectContent->query()->fetchAll();
                if (!empty($contentinfo)) {
                    $contentinformation = 1;
                } else {
                    $contentinformation = 0;
                }
            }
        }
        return $contentinformation;
    }

    /**
     * Return option of showing the top title for widgets
     *
     * @param int $group_id
     * @param int $layout
     * @return top title show or not
     */
    public function showtoptitle($layout, $group_id) {
        if (!$layout) {
            $group_id = $this->getWidgetizedGroup()->page_id;
            if (!empty($group_id)) {
                $table = Engine_Api::_()->getDbtable('content', 'core');
                $tablename = $table->info('name');
                $selectContent = $table->select()
                        ->from($table->info('name'), 'page_id')
                        ->where('name =?', 'core.container-tabs')
                        ->where('page_id =?', $group_id)
                        ->limit(1);
                $contentinfo = $selectContent->query()->fetchAll();
                if (empty($contentinfo)) {
                    $contentinformation = 1;
                } else {
                    $contentinformation = 0;
                }
            }
        } else {
            $table = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
            $select = $table->select()
                    ->from($table->info('name'), 'contentgroup_id')
                    ->where('name = ?', 'sitegroup_index_view')
                    ->where('group_id =?', $group_id)
                    ->limit(1);
            $row = $table->fetchRow($select);
            if ($row !== null) {
                $group_id = $row->contentgroup_id;
                $table = Engine_Api::_()->getDbtable('content', 'sitegroup');
                $selectContent = $table->select()
                        ->from($table->info('name'), 'contentgroup_id')
                        ->where('name =?', 'core.container-tabs')
                        ->where('contentgroup_id =?', $group_id)
                        ->limit(1);
                $contentinfo = $selectContent->query()->fetchAll();
                if (empty($contentinfo)) {
                    $contentinformation = 1;
                } else {
                    $contentinformation = 0;
                }
            } else {
                $group_id = $this->getWidgetizedGroup()->page_id;
                $table = Engine_Api::_()->getDbtable('admincontent', 'sitegroup');
                $selectContent = $table->select()
                                ->from($table->info('name'), 'group_id')
                                ->where('name =?', 'core.container-tabs')
                                ->where('group_id =?', $group_id)->limit(1);
                ;
                $contentinfo = $selectContent->query()->fetchAll();
                if (empty($contentinfo)) {
                    $contentinformation = 1;
                } else {
                    $contentinformation = 0;
                }
            }

            return $contentinformation;
        }
    }

    /**
     * Return true or false for ad show on paid groups
     *
     * @param object $sitegroup
     * @return true or false
     */
    public function showAdWithPackage($sitegroup) {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.isActivate', 1)) {
            return 0;
        }
        $params = array();
        $params['lim'] = 1;
        $fetch_community_ads = Engine_Api::_()->communityad()->getAdvertisement($params);
        if (empty($fetch_community_ads))
            return 0;

        $package = $sitegroup->getPackage();

        if (isset($package->ads)) {
            return (bool) $package->ads;
        } else {
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1)) {
                return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adwithpackage', 1);
            } else {
                return 0;
            }
        }
    }

    public function getModulelabel($title) {

        $menuitemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
        $selectMenuitemsTable = $menuitemsTable->select()->where('name =?', "core_admin_main_plugins_$title");
        $resultMenuitems = $menuitemsTable->fetchRow($selectMenuitemsTable);
        return $resultMenuitems;
    }

    public function getBannedUrls() {
        $merge_array = array();
        $pageUrlFinalArray = array();
        $businessUrlFinalArray = array();
        $storeUrlFinalArray = array();
        $staticpageUrlFinalArray = array();
        $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
        $urlArray = $bannedPageurlsTable->select()->from($bannedPageurlsTable, 'word')
                        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        $enableSitepage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');

        if ($enableSitepage) {
            $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
            $pageUrlArray = $pageTable->select()->from($pageTable, 'page_url')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            foreach ($pageUrlArray as $url) {
                $pageUrlFinalArray[] = strtolower($url);
            }
            $merge_array = array_merge($urlArray, $pageUrlFinalArray);
        } else {
            $merge_array = $urlArray;
        }

        $enableSitebusiness = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');
        if ($enableSitebusiness) {
            $businessTable = Engine_Api::_()->getDbtable('business', 'sitebusiness');
            $businessUrlArray = $businessTable->select()->from($businessTable, 'business_url')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            foreach ($businessUrlArray as $url) {
                $businessUrlFinalArray[] = strtolower($url);
            }
            $merge_array = array_merge($merge_array, $businessUrlFinalArray);
        }

        $enableSitestore = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
        if ($enableSitestore) {
            $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
            $storeUrlArray = $storeTable->select()->from($storeTable, 'store_url')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            foreach ($storeUrlArray as $url) {
                $storeUrlFinalArray[] = strtolower($url);
            }
            $merge_array = array_merge($merge_array, $storeUrlFinalArray);
        }

        $enableSitestaticpage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestaticpage');
        if ($enableSitestaticpage) {
            $staticpageTable = Engine_Api::_()->getDbtable('pages', 'sitestaticpage');
            $staticpageUrlArray = $staticpageTable->select()->from($staticpageTable, 'page_url')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            foreach ($staticpageUrlArray as $url) {
                $staticpageUrlFinalArray[] = strtolower($url);
            }
            $merge_array = array_merge($merge_array, $staticpageUrlFinalArray);
        }

        return $merge_array;
    }

    /**
     * Return tabid
     *
     * @param string $widgetname
     * @param int $group_id
     * @param int $layout
     * @return tabid
     */
    public function getTabIdInfoIntegration($widgetname, $groupid, $layout, $resource_type = null) {

        global $sitegroup_GetTabIdType;
        $tab_id = '';
        if (!$layout) {
            $tablecontent = Engine_Api::_()->getDbtable('content', 'core');
            $select = $tablecontent->select()
                    ->from($tablecontent->info('name'), 'content_id')
                    ->where('name = ?', $widgetname);

            if (!empty($resource_type)) {
                $select->where('params LIKE ?', '%' . $resource_type . '%');
            }

            $select->order('order ASC')->limit(1);
            $tab_id = $select->query()->fetchColumn();
        } else {
            $table = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
            $select = $table->select()
                    ->from($table->info('name'), 'contentgroup_id')
                    ->where('name = ?', 'sitegroup_index_view')
                    ->where('group_id = ?', $groupid)
                    ->limit(1);
            $contentgroup_id = $select->query()->fetchColumn();
            if ($contentgroup_id) {
                $tablecontent = Engine_Api::_()->getDbtable('content', 'sitegroup');
                $select = $tablecontent->select()
                        ->from($tablecontent->info('name'), 'content_id')
                        ->where('name = ?', $widgetname)
                        ->where('contentgroup_id = ?', $contentgroup_id);

                if (!empty($resource_type)) {
                    $select->where('params LIKE ?', '%' . $resource_type . '%');
                }

                $select->order('order ASC')->limit(1);
                $tab_id = $select->query()->fetchColumn();
            } else {
                $group_id = $this->getWidgetizedGroup()->page_id;
                $tablecontent = Engine_Api::_()->getDbtable('admincontent', 'sitegroup');
                $select = $tablecontent->select()
                        ->from($tablecontent->info('name'), 'admincontent_id')
                        ->where('name = ?', $widgetname)
                        ->where('group_id = ?', $group_id);

                if (!empty($resource_type)) {
                    $select->where('params LIKE ?', '%' . $resource_type . '%');
                }

                $select->order('order ASC')->limit(1);
                $tab_id = $select->query()->fetchColumn();
            }
        }

        return $sitegroup_GetTabIdType ? $tab_id : $sitegroup_GetTabIdType;
    }

    public function getBannedGroupUrls() {

        $merge_array = array();
        // GET THE ARRAY OF BANNED GROUPURLS
        if (!defined('SITEGROUP_BANNED_URLS')) {
            $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
            $urlArray = $bannedPageurlsTable->select()->from($bannedPageurlsTable, 'word')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

            $enableSitepage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');

            if ($enableSitepage) {
                $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
                $pageUrlArray = $pageTable->select()->from($pageTable, 'page_url')
                                ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
                $merge_array = array_merge($urlArray, $pageUrlArray);
            } else {
                $merge_array = $urlArray;
            }

            $enableSitebusiness = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');
            if ($enableSitebusiness) {
                $businessTable = Engine_Api::_()->getDbtable('business', 'sitebusiness');
                $businessUrlArray = $businessTable->select()->from($businessTable, 'business_url')
                                ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
                $merge_array = array_merge($merge_array, $businessUrlArray);
            }

            $enableSitestore = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
            if ($enableSitestore) {
                $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
                $storeUrlArray = $storeTable->select()->from($storeTable, 'store_url')
                                ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
                $merge_array = array_merge($merge_array, $storeUrlArray);
            }

            $enableSitestaticpage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestaticpage');
            if ($enableSitestaticpage) {
                $staticpageTable = Engine_Api::_()->getDbtable('pages', 'sitestaticpage');
                $staticpageUrlArray = $staticpageTable->select()->from($staticpageTable, 'page_url')
                                ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
                $merge_array = array_merge($merge_array, $staticpageUrlArray);
            }

            define('SITEGROUP_BANNED_URLS', serialize($merge_array));
        }
        return $banneUrlArray = unserialize(SITEGROUP_BANNED_URLS);
    }

    public function isLessThan417AlbumModule() {
        $groupalbumModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitegroupalbum');
        $groupalbumModuleVersion = $groupalbumModule->version;
        if ($groupalbumModuleVersion < '4.1.7') {
            return true;
        } else {
            return false;
        }
    }

    //ACTION FOR LIKES
    public function autoLike($resource_id, $resource_type) {

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GETTING THE VALUE OF RESOURCE ID AND RESOURCE TYPE
// 		$resource_id = $this->_getParam('resource_id');
// 		$resource_type = $this->_getParam('resource_type');

        if (empty($viewer_id)) {
            return;
        }

        $likeTable = Engine_Api::_()->getItemTable('core_like');
        $likeTableName = $likeTable->info('name');
        $sub_status_select = $likeTable->select()
                ->from($likeTableName, new Zend_Db_Expr('COUNT(*)'))
                ->where('resource_type = ?', $resource_type)
                ->where('resource_id = ?', $resource_id)
                ->where('poster_type =?', $viewer->getType())
                ->where('poster_id =?', $viewer_id)
                ->limit(1);
        $like_id = (integer) $sub_status_select->query()->fetchColumn();

        //GET THE VALUE OF LIKE ID
        //$like_id = $this->_getParam('like_id');
        //$status = $this->_getParam('smoothbox', 1);
        //$this->view->status = true;
        //GET LIKES.
        $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
        $resource = Engine_Api::_()->getItem($resource_type, $resource_id);

        //CHECK FOR LIKE ID
        if (empty($like_id)) {

            //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
            $like_id_temp = Engine_Api::_()->sitegroup()->checkAvailability($resource_type, $resource_id);
            if (empty($like_id_temp[0]['like_id'])) {

                if (!empty($resource)) {
                    $like_id = $likeTable->addLike($resource, $viewer);
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
                        Engine_Api::_()->sitelike()->setLikeFeed($viewer, $resource);
                }

                $notify_table = Engine_Api::_()->getDbtable('notifications', 'activity');
                $db = $likeTable->getAdapter();
                $db->beginTransaction();
                try {

                    //CREATE THE NEW ROW IN TABLE
                    if (!empty($getOwnerId) && $getOwnerId != $viewer_id) {

                        $notifyData = $notify_table->createRow();
                        $notifyData->user_id = $getOwnerId;
                        $notifyData->subject_type = $viewer->getType();
                        $notifyData->subject_id = $viewer_id;
                        $notifyData->object_type = $object_type;
                        $notifyData->object_id = $resource_id;
                        $notifyData->type = 'liked';
                        $notifyData->params = $resource->getShortType();
                        $notifyData->date = date('Y-m-d h:i:s', time());
                        $notifyData->save();
                    }

                    //PASS THE LIKE ID.
                    $this->view->like_id = $like_id;
                    $this->view->error_mess = 0;
                    $db->commit();
                } catch (Exception $e) {

                    $db->rollBack();
                    throw $e;
                }
                $like_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Liked.');
            } else {
                $this->view->like_id = $like_id_temp[0]['like_id'];
                $this->view->error_mess = 1;
            }
        }
// 		else {
// 			if (!empty($resource)) {
// 				$likeTable->removeLike($resource, $viewer);
// 				  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
//           Engine_Api::_()->sitelike()->removeLikeFeed($viewer, $resource);
// 			}
// 			$this->view->error_mess = 0;
// 
// 			$like_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Unliked.');
//     }
//     if (empty($status)) {
//       $this->_forward('success', 'utility', 'core', array(
//               'smoothboxClose' => true,
//               'parentRefresh' => true,
//               'messages' => array($like_msg)
//           )
//       );
//     }
    }

    /**
     * Return categoryid
     *
     * @param string $content_id
     * @param string $widgetname
     * @return categoryid
     */
    public function getSitegroupCategoryid($content_id = null, $widgetname) {

        $contentTable = Engine_Api::_()->getDbtable('content', 'core');
        $group_id = $contentTable
                ->select()
                ->from($contentTable->info('name'), array('page_id'))
                ->where('content_id =?', $content_id)
                ->query()
                ->fetchColumn();
        //GET CONTENT TABLE
        $contentTable = Engine_Api::_()->getDbtable('content', 'core');
        $params = $contentTable
                ->select()
                ->from($contentTable->info('name'), array('params'))
                ->where('page_id =?', $group_id)
                ->where('name =?', $widgetname)
                ->query()
                ->fetchColumn();
        if ($params)
            $params = Zend_Json::decode($params);
        if ($params && isset($params['category_id']) && !empty($params['category_id'])) {
            return $params['category_id'];
        } else {
            return 0;
        }
    }

    //SEND NOTIFICATION TO GROUP ADMIN WHEN OWN GROUP LIKE AND COMMENT.
    public function itemCommentLike($subject, $notificationType, $baseOnContentOwner = null) {

//         $item_title = $subject->getShortType();
//         $item_title_url = $subject->getHref();
//         $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
//         $item_title_link = "<a href='$item_title_baseurl'>" . $item_title . " </a>";
        //FETCH DATA
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {

            $object_type = $subject->getType();
            $object_id = $subject->getIdentity();
// 					if ($notificationType == 'sitegroup_contentcomment') {
// 						if ($baseOnContentOwner) {
// 								$subject_type = $viewer->getType();
// 								$subject_id = $viewer->getIdentity();
// 						} else {
// 								$subject_type = $viewer->getType();
// 								$subject_id = $viewer->getIdentity();
// 						}
// 					} else {
            $subject_type = $viewer->getType();
            $subject_id = $viewer->getIdentity();
            //}

            if ($notificationType == 'sitegroup_contentlike') {
                $notification = '%"notificationlike":"1"%';
                $notificationFriend = '%"notificationlike":"2"%';
            } else {
                $notification = '%"notificationcomment":"1"%';
                $notificationFriend = '%"notificationcomment":"2"%';
            }

            $db = Zend_Db_Table_Abstract::getDefaultAdapter();

            $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();

            if ($friendId) {
                $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitegroup_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitegroup_membership` WHERE (engine4_sitegroup_membership.group_id = " . $subject->group_id . ") AND (engine4_sitegroup_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitegroup_membership.notification = 1) AND (engine4_sitegroup_membership.action_notification LIKE '" . $notification . "' or (engine4_sitegroup_membership.action_notification LIKE '" . $notificationFriend . "' and (engine4_sitegroup_membership .user_id IN (" . join(",", $friendId) . "))))");
            } else {
                $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitegroup_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitegroup_membership` WHERE (engine4_sitegroup_membership.group_id = " . $subject->group_id . ") AND (engine4_sitegroup_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitegroup_membership.notification = 1) AND (engine4_sitegroup_membership.action_notification LIKE '" . $notification . "')");
            }
        } else {
            if (isset($subject->group_id)) {
                $manageAdminsIds = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($subject->group_id, $viewer_id);

                foreach ($manageAdminsIds as $value) {
                    $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
                    $action_notification = unserialize($value['action_notification']);
                    if (!empty($value['notification']) && (in_array('like', $action_notification) || in_array('comment', $action_notification))) {

                        $row = $notifyApi->createRow();
                        $row->user_id = $user_subject->getIdentity();

// 						if ($notificationType == 'sitegroup_contentcomment') {
// 								if ($baseOnContentOwner) {
// 										$row->subject_type = $subject->parent_type;
// 										$row->subject_id = $subjectParent->getIdentity();
// 								} else {
// 										$row->subject_type = $viewer->getType();
// 										$row->subject_id = $viewer->getIdentity();
// 								}
// 						} else {
                        $row->subject_type = $viewer->getType();
                        $row->subject_id = $viewer->getIdentity();
                        //}
                        $row->type = "$notificationType";
                        $row->object_type = $subject->getType();
                        $row->object_id = $subject->getIdentity();
                        //$row->params = '{"eventname":"' . $item_title_link . '"}';
                        $row->date = date('Y-m-d H:i:s');
                        $row->save();
                    }
                }
            }
        }
    }

    public function sendNotificationToFollowers($object, $actionObject, $notificationType, $count = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $group_id = $object->group_id;
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        //ITEM TITLE AND TILTE WITH LINK.
        $item_title = isset($object->title) ? $object->title : $object->getTitle();
        $item_title_url = $object->getHref();
        $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
        $item_title_link = "<a href='$item_title_baseurl'>" . $item_title . "</a>";
        $followersIds = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollowers('sitegroup_group', $group_id, $viewer->getIdentity());
        $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notidicationSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.feed.type', 0);
        foreach ($followersIds as $value) {

            if (!empty($notidicationSettings)) {
                $notificationsTable->delete(array('type =?' => "$notificationType", 'object_type = ?' => $object->getType(), 'object_id = ?' => $object->getIdentity(), 'subject_id = ?' => $sitegroup->getIdentity(), 'subject_type = ?' => $sitegroup->getType(), 'user_id =?' => $value['poster_id']));
            } else {
                $notificationsTable->delete(array('type =?' => "$notificationType", 'object_type = ?' => $object->getType(), 'object_id = ?' => $object->getIdentity(), 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'user_id =?' => $value['poster_id']));
            }

            $user_subject = Engine_Api::_()->user()->getUser($value['poster_id']);
            $row = $notificationsTable->createRow();
            $row->user_id = $user_subject->getIdentity();
            if (!empty($notidicationSettings)) {
                $row->subject_type = $sitegroup->getType();
                $row->subject_id = $sitegroup->getIdentity();
            } else {
                $row->subject_type = $viewer->getType();
                $row->subject_id = $viewer->getIdentity();
            }

            $row->type = "$notificationType";
            $row->object_type = $object->getType();
            $row->object_id = $object->getIdentity();

            if ($notificationType == 'sitegroupalbum_create') {
                $row->params = '{"count":"' . $count . '"}';
            } else {
                $row->params = '{"eventname":"' . $item_title_link . '"}';
            }
            $row->date = date('Y-m-d H:i:s');
            $row->save();
        }
    }

    public function sendNotificationEmail($object, $actionObject, $notificationType = null, $emailType = null, $params = null, $count = null) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');

        $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notificationSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.feed.type', 0);

        $manageAdminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $group_id = $object->group_id;

        $subject = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        $owner = $subject->getOwner();

        //previous notification is delete.
        $notificationsTable->delete(array('type =?' => "$notificationType", 'object_type = ?' => "sitegroup_group", 'object_id = ?' => $group_id, 'subject_id = ?' => $viewer_id));

        //GET GROUP TITLE AND GROUP TITLE WITH LINK.
        $grouptitle = $subject->title;
        //$group_url = Engine_Api::_()->sitegroup()->getGroupUrl($subject->group_id);
        //$group_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true);
        //$group_title_link = '<a href="' . $group_baseurl . '"  >' . $grouptitle . ' </a>';
        //ITEM TITLE AND TILTE WITH LINK.
        if ($notificationType == 'sitegroupdocument_create') {
            $item_title = $object->sitegroupdocument_title;
        } else {
            $item_title = $object->title;
        }
        $item_title_url = $object->getHref();
        $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
        $item_title_link = "<a href='$item_title_baseurl' style='text-decoration:none;' >" . $item_title . " </a>";

        //POSTER TITLE AND PHOTO WITH LINK
        $poster_title = $viewer->getTitle();
        $poster_url = $viewer->getHref();
        $poster_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $poster_url;
        $poster_title_link = "<a href='$poster_baseurl' style='font-weight:bold;text-decoration:none;' >" . $poster_title . " </a>";
        if ($viewer->photo_id) {
            $photo = 'http://' . $_SERVER['HTTP_HOST'] . $viewer->getPhotoUrl('thumb.icon');
        } else {
            $photo = 'http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/application/modules/Sitegroup/externals/images/nophoto_user_thumb_icon.png';
        }
        $image = "<img src='$photo' />";
        $posterphoto_link = "<tr><td colspan='2' style='height:20px;'></td></tr><tr></tr><tr><td valign='top' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding-right:15px;text-align:left'><a href='$poster_baseurl'  >" . $image . " </a></td><td valign='top' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;width:100%;text-align:left'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;width:100%'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif'><span style='color:#333333;'>";

        //MEASSGE WITH LINK.
        if (isset($actionObject)) {
            $post_baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $actionObject->getHref();
        }
        $created = $post = ' ';
        if ($notificationType == 'sitegroupalbum_create') {
            $post = $poster_title . ' created a new album in group: ' . $grouptitle;
            $created = ' created the album ';
        } elseif ($notificationType == 'sitegroupdocument_create') {
            $post = $poster_title . ' created a new document in group: ' . $grouptitle;
            $created = ' created the document ';
        } elseif ($notificationType == 'sitegroupevent_create') {
            $post = $poster_title . ' created a new event in group: ' . $grouptitle;
            $created = ' created the event ';
        } elseif ($notificationType == 'sitegroupmusic_create') {
            $post = $poster_title . ' created a new playlist in group: ' . $grouptitle;
            $created = ' created the music ';
        } elseif ($notificationType == 'sitegroupnote_create') {
            $post = $poster_title . ' created a new note in group: ' . $grouptitle;
            $created = ' created the note ';
        } elseif ($notificationType == 'sitegroupoffer_create') {
            $post = $poster_title . ' created a new offer in group: ' . $grouptitle;
            $created = ' created the offer ';
        } elseif ($notificationType == 'sitegrouppoll_create') {
            $post = $poster_title . ' created a new poll in group: ' . $grouptitle;
            $created = ' created the poll ';
        } elseif ($notificationType == 'sitegroupvideo_create') {
            $post = $poster_title . ' posted a new video in group: ' . $grouptitle;
            $created = ' created the video ';
        } elseif ($notificationType == 'sitegroupdiscussion_create') {
            $post = $poster_title . ' created a new discussion in group: ' . $grouptitle;
            $created = ' created the discussion ';
        }
        if (!empty($post_baseUrl)) {
            if ($params == 'Activity Comment' || $params == 'Activity Reply') {
                $post_link = "<a href='$post_baseUrl'  >" . 'post' . "</a>";
                $post_linkformail = "<table cellspacing='0' cellpadding='0' border='0' style='border-collapse:collapse;' width='90%'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:20px;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;' width='100%'><tr><td colspan='2' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;border-bottom:1px solid #dddddd;padding-bottom:5px;'><a style='font-weight:bold;margin-bottom:10px;text-decoration:none;' href='$post_baseUrl'>" . 'post' . "</a></td></tr><tr><td valign='top' style='padding:10px 15px 10px 10px;'><a href='$poster_baseurl'  >" . $image . " </a></td><td valign='top' style='padding-top:10px;font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;width:100%;text-align:left;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;width:100%;'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;text-decoration:none;text-decoration:none;'>" . $poster_title_link . 'post' . $item_title_link . '.' . "</td></tr></table></td></tr></table></td></tr></table>";
            } else {
                $post_link = "<a href='$post_baseUrl'  >" . $post . "</a>";
                $post_linkformail = "<table cellspacing='0' cellpadding='0' border='0' style='border-collapse:collapse;' width='90%'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:20px;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;' width='100%'><tr><td colspan='2' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;border-bottom:1px solid #dddddd;padding-bottom:5px;'><a style='font-weight:bold;margin-bottom:10px;text-decoration:none;' href='$post_baseUrl'>" . $post . "</a></td></tr><tr><td valign='top' style='padding:10px 15px 10px 10px;'><a href='$poster_baseurl'  >" . $image . " </a></td><td valign='top' style='padding-top:10px;font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;width:100%;text-align:left;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;width:100%;'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;text-decoration:none;'>" . $poster_title_link . $created . $item_title_link . '.' . "</td></tr></table></td></tr></table></td></tr></table>";
            }
        }

        //FETCH DATA
        if (empty($sitegroupmemberEnabled)) {
            $manageAdminsIds = $manageAdminTable->getManageAdmin($group_id, $viewer_id);
            foreach ($manageAdminsIds as $value) {
                $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
                $action_notification = unserialize($value['action_notification']);
                if (!empty($value['notification']) && in_array('created', $action_notification)) {
                    $row = $notificationsTable->createRow();
                    $row->user_id = $user_subject->getIdentity();
                    if ($notificationSettings == 1) {
                        $row->subject_type = $subject->getType();
                        $row->subject_id = $subject->getIdentity();
                    } else {
                        $row->subject_type = $viewer->getType();
                        $row->subject_id = $viewer->getIdentity();
                    }
                    $row->type = "$notificationType";
                    $row->object_type = $object->getType();
                    $row->object_id = $object->getIdentity();
                    $row->date = date('Y-m-d H:i:s');

                    if ($notificationType == 'sitegroupalbum_create') {
                        $row->params = '{"count":"' . $count . '"}';
                    } else {
                        $row->params = '{"eventname":"' . $item_title_link . '"}';
                    }
                    $row->save();
                }

                //EMAIL SEND TO ALL MANAGEADMINS.
                $action_email = json_decode($value['action_email']);
                if (!empty($value['email']) && in_array('created', $action_email)) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "$emailType", array(
                        'group_title' => $grouptitle,
                        'item_title' => $item_title,
                        'body_content' => $post_linkformail,
                    ));
                }
            }
        }

        //START SEND EMAIL TO ALL MEMBER WHO HAVE JOINED THE GROUP INCLUDE MANAGE ADMINS.
        if (!empty($sitegroupmemberEnabled)) {
            $membersIds = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinMembers($group_id, $viewer_id, $viewer_id, 0, 1);
            foreach ($membersIds as $value) {
                $action_email = json_decode($value['action_email']);
                $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
                if ($params != 'Activity Comment' && $params != 'Activity Reply') {
                    if (!empty($value['email_notification']) && $action_email->emailcreated == 1) {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "$emailType", array(
                            'group_title' => $grouptitle,
                            'item_title' => $item_title,
                            'body_content' => $post_linkformail,
                        ));
                    } elseif (!empty($value['email_notification']) && $action_email->emailcreated == 2) {
                        $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
                        if (in_array($value['user_id'], $friendId)) {
                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "$emailType", array(
                                'group_title' => $grouptitle,
                                'item_title' => $item_title,
                                'body_content' => $post_linkformail,
                            ));
                        }
                    }
                }
            }
        }
        //END SEND EMAIL TO ALL MEMBER WHO HAVE JOINED THE GROUP INCLUDE MANAGE ADMINS.

        if ($params != 'Activity Comment' && $params != 'Groupevent Invite' && $params != 'Activity Reply') {
            $object_type = $subject->getType();
            $object_id = $subject->getIdentity();
            $subject_type = $viewer->getType();
            $subject_id = $viewer->getIdentity();
        } elseif ($params == 'Groupevent Invite') {
            $object_type = $object->getType();
            $object_id = $object->getIdentity();
            //      $subject_type = $viewer->getType();
            //      $subject_id = $viewer->getIdentity();
            if ($notificationSettings == 1) {
                $subject_type = $subject->getType();
                $subject_id = $subject->getIdentity();
            } else {
                $subject_type = $viewer->getType();
                $subject_id = $viewer->getIdentity();
            }
        }

        if ($params != 'Activity Comment' && $params != 'Activity Reply') {
            $notificationcreated = '%"notificationcreated":"1"%';
            $notificationsinglecodecreated = '%"notificationcreated":1%';
            $notificationfriendcreated = '%"notificationcreated":"2"%';
            $notificationfriendsinglecodecreated = '%"notificationcreated":2%';
            if ($count) {
                $countparams = '{"count":"' . $count . '"}';
            } else {
                $countparams = null;
            }
            if (!empty($sitegroupmemberEnabled)) {
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
                if (!empty($friendId)) {
                    $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitegroup_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, '" . $countparams . "' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitegroup_membership` WHERE (engine4_sitegroup_membership.group_id = " . $subject->group_id . ") AND (engine4_sitegroup_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitegroup_membership.notification = 1) AND (engine4_sitegroup_membership.action_notification LIKE '" . $notificationcreated . "' or engine4_sitegroup_membership.action_notification LIKE '" . $notificationsinglecodecreated . "' or (engine4_sitegroup_membership.action_notification LIKE '" . $notificationfriendcreated . "' or engine4_sitegroup_membership.action_notification LIKE '" . $notificationfriendsinglecodecreated . "' and (engine4_sitegroup_membership .user_id IN (" . join(",", $friendId) . "))))");
                } else {
                    $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitegroup_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, '" . $countparams . "' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitegroup_membership` WHERE (engine4_sitegroup_membership.group_id = " . $subject->group_id . ") AND (engine4_sitegroup_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitegroup_membership.notification = 1) AND (engine4_sitegroup_membership.action_notification LIKE '" . $notificationcreated . "' OR engine4_sitegroup_membership.action_notification LIKE '" . $notificationsinglecodecreated . "')");
                }
            }
        }
    }

    /**
     * Check If The Attachment Types in Activity Feed Should be Enabled or Not
     */
    public function enableComposer($composerType = null) {
        return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($composerType) && method_exists(Engine_Api::_()->getApi('core', $composerType), 'enableComposer') ? Engine_Api::_()->getApi('core', $composerType)->enableComposer() : false;
    }

    /**
     * Gets widgetized group
     *
     * @return Zend_Db_Table_Select
     */
    public function getMobileWidgetizedGroup() {

        if (!Engine_Api::_()->hasModuleBootstrap('sitemobile'))
            return false;

        //GET CORE GROUP TABLE
        $tableNameGroup = Engine_Api::_()->getDbtable('pages', 'sitemobile');
        $select = $tableNameGroup->select()
                ->from($tableNameGroup->info('name'), array('page_id', 'description', 'keywords'))
                ->where('name =?', 'sitegroup_index_view')
                ->limit(1);

        return $tableNameGroup->fetchRow($select);
    }

    public function showTabsWithoutContent() {
        return Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.show.tabs.without.content', 0);
    }

    public function checkEnableForMobile($moduleName) {
        if (!Engine_Api::_()->hasModuleBootstrap('sitemobile'))
            return false;

        //GET CORE GROUP TABLE
        $modulesSitemobile = Engine_Api::_()->getDbtable('modules', 'sitemobile');
        $enable_mobile = $modulesSitemobile->select()
                ->from($modulesSitemobile->info('name'), array('enable_mobile'))
                ->where('name =?', $moduleName)
                ->where('enable_mobile= ?', 1)
                ->query()
                ->fetchColumn();

        return $enable_mobile;
    }

    /**
     * Set default widget in core groups table
     *
     * @param object $table
     * @param string $tablename
     * @param int $group_id
     * @param string $type
     * @param string $widgetname
     * @param int $middle_id
     * @param int $order
     * @param string $title
     * @param int $titlecount
     */
    function setDefaultDataContentWidget($table, $tablename, $group_id, $type, $widgetname, $middle_id, $order, $title = null, $titlecount = null, $advanced_activity_params = null) {

        $selectWidgetId = $table->select()
                ->where('page_id =?', $group_id)
                ->where('type = ?', $type)
                ->where('name = ?', $widgetname)
                ->where('parent_content_id = ?', $middle_id)
                ->limit(1);
        $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
        if (empty($fetchWidgetContentId)) {
            $contentWidget = $table->createRow();
            $contentWidget->page_id = $group_id;
            $contentWidget->type = $type;
            $contentWidget->name = $widgetname;
            $contentWidget->parent_content_id = $middle_id;
            $contentWidget->order = $order;
            if (empty($advanced_activity_params) && $title && $titlecount) {
                $contentWidget->params = "{\"title\":\"$title\",\"titleCount\":$titlecount}";
            } else {
                $contentWidget->params = "$advanced_activity_params";
            }
            $contentWidget->save();
        }
    }

    public function sendInviteEmail($object, $actionObject = null, $params = array(), $memberIdsArray = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
        $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');

        $manageAdminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $group_id = $params['parent_id'];

        $subject = Engine_Api::_()->getItem($params['parent_type'], $group_id);
        $owner = $subject->getOwner();

        //GET GROUP TITLE AND GROUP TITLE WITH LINK.
        $grouptitle = $subject->title;
        $group_url = Engine_Api::_()->sitegroup()->getGroupUrl($subject->group_id);
        $group_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true);
        $group_title_link = '<a href="' . $group_baseurl . '"  >' . $grouptitle . ' </a>';

        //ITEM TITLE AND TILTE WITH LINK.
        $item_title = $object->title;
        $item_title_url = $object->getHref();
        $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
        $item_title_link = "<a href='$item_title_baseurl'  >" . $item_title . " </a>";


        //POSTER TITLE AND PHOTO WITH LINK
        $poster_title = $viewer->getTitle();
        $poster_url = $viewer->getHref();
        $poster_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $poster_url;
        $poster_title_link = "<a href='$poster_baseurl'  >" . $poster_title . " </a>";
        if ($viewer->photo_id) {
            $photo = 'http://' . $_SERVER['HTTP_HOST'] . $viewer->getPhotoUrl('thumb.icon');
        } else {
            $photo = 'http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/application/modules/Sitegroup/externals/images/nophoto_sitegroup_thumb_icon.png';
        }
        $image = "<img src='$photo' />";
        $posterphoto_link = "<a href='$poster_baseurl'  >" . $image . " </a>";

        //MEASSGE WITH LINK.
        if (!empty($actionObject) && isset($actionObject)) {
            $post_baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $actionObject->getHref();
            $post = $poster_title . ' Posted in ' . $grouptitle;
            $post_link = "<a href='$post_baseUrl'  >" . $post . " </a>";
        }

        //FETCH DATA
        if ($params['tempValue'] == 'InviteMembers') {
            foreach ($memberIdsArray as $value) {
                $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
                if (!empty($value['email'])) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, $params['emailType'], array(
                        'group_title' => $grouptitle,
                        'item_title_with_link' => $item_title_link,
                        'item_title' => $item_title,
                        'viewertitle_link' => $poster_title_link,
                        'viewerphoto_link' => $posterphoto_link,
                    ));
                }
            }
        } elseif ($params['tempValue'] == 'Groupevent Invite') {
            $groupMembers = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinMembers($group_id);
            foreach ($groupMembers as $value) {

                if ($value['user_id'] == $object->owner_id)
                    continue;
                $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
                if (!empty($value['email'])) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, $params['emailType'], array(
                        'group_title' => $grouptitle,
                        'item_title_with_link' => $item_title_link,
                        'item_title' => $item_title,
                        'viewertitle_link' => $poster_title_link,
                        'viewerphoto_link' => $posterphoto_link,
                    ));
                }
            }
        } else {
            $manageAdminsIds = $manageAdminTable->getManageAdmin($group_id, $viewer_id);
            foreach ($manageAdminsIds as $value) {
                $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
                if (!empty($value['email'])) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, $params['emailType'], array(
                        'group_title' => $grouptitle,
                        'item_title_with_link' => $item_title_link,
                        'item_title' => $item_title,
                        'viewertitle_link' => $poster_title_link,
                        'viewerphoto_link' => $posterphoto_link,
                    ));
                }
            }
        }

        $object_type = $object->getType();
        $object_id = $object->getIdentity();
        $subject_type = $viewer->getType();
        $subject_id = $viewer->getIdentity();

        if (!empty($sitegroupmemberEnabled)) {
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            if ($params['tempValue'] == 'InviteMembers') {
                $friendId = $memberIdsArray;
            } else {
                $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
            }
            if (!empty($friendId)) {
                $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitegroup_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $params['notificationType'] . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitegroup_membership` WHERE (engine4_sitegroup_membership.group_id = " . $subject->group_id . ") AND (engine4_sitegroup_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitegroup_membership.notification = 1 or (engine4_sitegroup_membership.notification = 2 and (engine4_sitegroup_membership .user_id IN (" . join(",", $friendId) . "))))");
            } else {
                $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitegroup_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $params['notificationType'] . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitegroup_membership` WHERE (engine4_sitegroup_membership.group_id = " . $subject->group_id . ") AND (engine4_sitegroup_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitegroup_membership.notification = 1)");
            }
        }
    }

    public function getsecondLevelMaps($option_id) {
        // Get second level fields
        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitegroup_group');
        $secondLevelMaps = array();
        $secondLevelFields = array();

        $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
        if (!empty($secondLevelMaps)) {
            foreach ($secondLevelMaps as $map) {
                $secondLevelFields[$map->child_id] = $map->getChild();
            }
        }
        return $secondLevelMaps;
    }

    /**
     * Gte Profiletype Label
     *
     * @param int $option_id
     * @return string $profie
     */
    public function getProfileTypeName($option_id) {

        $table_options = Engine_Api::_()->fields()->getTable('sitegroup_group', 'options');
        $profie = $table_options->select()
                ->from($table_options->info('name'), 'label')
                ->where('option_id = ?', $option_id)
                ->query()
                ->fetchColumn();
        return $profie;
    }

    public function updateMemberCount($sitegroup) {

        if (!$sitegroup)
            return;

        $tableSitegroupMembership = Engine_Api::_()->getDbtable('membership', 'sitegroup');
        $tableSitegroupMembershipName = $tableSitegroupMembership->info('name');
        $count = $tableSitegroupMembership->select()
                ->from($tableSitegroupMembershipName, array('count(*) as count'))
                ->where('resource_id =?', $sitegroup->getIdentity())
                ->where('active =?', 1)
                ->where('resource_approved =?', 1)
                ->where('user_approved =?', 1)
                ->query()
                ->fetchColumn();

        $sitegroup->member_count = $count;
        $sitegroup->save();
    }

    /**
     * Check here that show payment link or not
     * $params $group_id : Id of group
     * @return bool $showLink
     * */
    public function canShowCancelLink($group_id) {


        if (!Engine_Api::_()->sitegroup()->hasPackageEnable())
            return;

        $showLink = false;
        $group = Engine_Api::_()->getItem('sitegroup_group', $group_id);


        if (!empty($group)) {
            $package = $group->getPackage();

            if (!$package->isFree() && $group->status == "active" && !$package->isOneTime() && !empty($group->approved)) {
                return (bool) true;
            }
        }

        return (bool) $showLink;
    }

    public function allowInThisGroup($sitegroup, $packagePrivacyName, $levelPrivacyName) {
        if ($this->hasPackageEnable()) {
            if (!$this->allowPackageContent($sitegroup->package_id, "modules", $packagePrivacyName)) {
                return false;
            }
        } else {
            $isGroupOwnerAllow = $this->isGroupOwnerAllow($sitegroup, $levelPrivacyName);
            if (empty($isGroupOwnerAllow)) {
                return false;
            }
        }
        return true;
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
