<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contentgroups.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_MobileContentgroups extends Engine_Db_Table implements Engine_Content_Storage_Interface {

  protected $_rowClass = "Sitegroup_Model_MobileContentgroup";

  public function loadMetaData(Engine_Content $contentAdapter, $name) {
  	
    $select = $this->select()->where('name = ?', $name)->orWhere('mobilecontentgroup_id = ?', $name);
    $group = $this->fetchRow($select);

    if (!is_object($group)) {
      //throw?
      return null;
    }

    return $group->toArray();
  }

  public function loadContent(Engine_Content $contentAdapter, $name) {
  	
    $sitegroup_id = Engine_Api::_()->core()->getSubject()->getIdentity();

    if (is_array($name)) {
      $name = join('_', $name);
    }
    if (!is_string($name) && !is_numeric($name)) {
      throw new Exception('not string');
    }
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->select()->where('group_id = ?', $sitegroup_id)->where('name = ?', $name)->orWhere('mobilecontentgroup_id = ?', $name);
    $group = $this->fetchRow($select);

    if (is_object($group)) {
			$contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitegroup');
			$select = $contentTable->select()
							->where('mobilecontentgroup_id = ?', $group->mobilecontentgroup_id)
							->order('order ASC');
			$content = $contentTable->fetchAll($select);

			$structure = $this->prepareContentArea($content);
			$element = new Engine_Content_Element_Container(array(
									'class' => 'layout_group_' . $group->name,
									'elements' => $structure
							));
    } else {
      $group_id = Engine_Api::_()->sitegroup()->getMobileWidgetizedGroup()->page_id;
			$contentTable = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitegroup');
			$select = $contentTable->select()
							->where('group_id = ?', $group_id)
							->order('order ASC');
			$content = $contentTable->fetchAll($select);
			$structure = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitegroup')->prepareContentArea($content);
			$element = new Engine_Content_Element_Container(array(
									'class' => 'layout_group_sitegroup_index_view',
									'elements' => $structure
							));
    }
    


    return $element;
  }

  public function prepareContentArea($content, $current = null) {

    $parent_content_id = null;
    if (null !== $current) {
      $parent_content_id = $current->mobilecontent_id;
    }

    $children = $content->getRowsMatching('parent_content_id', $parent_content_id);
    if (empty($children) && null === $parent_content_id) {
      $children = $content->getRowsMatching('parent_content_id', 0);
    }

    $struct = array();
    foreach ($children as $child) {
      $elStruct = $this->createElementParams($child);
      $elStruct['elements'] = $this->prepareContentArea($content, $child);
      $struct[] = $elStruct;
    }
    $Modules = array("offer" => "sitegroupoffer", "form" => "sitegroupform", "invite" => "sitegroupinvite", "sdcreate" => "sitegroupdocument", "sncreate" => "sitegroupnote", "splcreate" => "sitegrouppoll", "secreate" => "sitegroupevent", "svcreate" => "sitegroupvideo", "spcreate" => "sitegroupalbum", "sdicreate" => "sitegroupdiscussion", "smcreate" => "sitegroupmusic");
    $subject = Engine_Api::_()->core()->getSubject('sitegroup_group');
    foreach ($struct as $keys => $valuess) {
      $unsetFlage = false;
      $explode_modulename_array = explode('.', $valuess['name']);
      $explode_modulename = $explode_modulename_array[0];
      $search_Key = "";
      $search_Key = array_search($explode_modulename, $Modules);
      if ($explode_modulename == 'sitegroup') {
        if ($valuess['name'] == 'sitegroup.photos-sitegroup' || $valuess['name'] == 'sitegroup.photorecent-sitegroup' || $valuess['name'] == 'sitegroup.albums-sitegroup') {
          $explode_modulename = 'sitegroupalbum';
          $search_Key = 'spcreate';
        }
        if ($valuess['name'] == 'sitegroup.discussion-sitegroup') {
          $explode_modulename = 'sitegroupdiscussion';
          $search_Key = 'sdicreate';
        }
        if ($valuess['name'] == 'sitegroup.overview-sitegroup') {
          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'overview');
          if (empty($isManageAdmin)) {
            $unsetFlage = true;
          }
        }
        if ($valuess['name'] == 'sitegroup.location-sitegroup') {
          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'map');
          if (empty($isManageAdmin)) {
            $unsetFlage = true;
          }
        }
      }
      if (!empty($search_Key)) {
        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", $explode_modulename)) {
            $unsetFlage = true;
          }
        } else {
          $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, $search_Key);
          if (empty($isGroupOwnerAllow)) {
            $unsetFlage = true;
          }
        }
      }
      if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
        if ($valuess['name'] == 'sitegroup.thumbphoto-sitegroup') {
          unset($struct[$keys]);
        }
      }

      if ($unsetFlage)
        unset($struct[$keys]);
    }
    return $struct;
  }

  public function createElementParams($row) {
  	
    $data = array(
        'identity' => $row->mobilecontent_id,
        'type' => $row->type,
        'name' => $row->name,
        'order' => $row->order,
        'widget_admin' => $row->widget_admin,
    );
    $params = (array) $row->params;
    if (isset($params['title']))
      $data['title'] = $params['title'];
    $data['params'] = $params;

    return $data;
  }

  public function deleteGroup(Sitegroup_Model_Group $group) {

    Engine_Api::_()->getDbtable('mobileContent', 'sitegroup')->delete(array('mobilecontentgroup_id = ?' => $group->mobilecontentgroup_id));    
    $group->delete();
    return $this;
  }
  
  /**
   * Gets contentgroup_id,description,keywords
   *
   * @param int $group_id
   * @return contentgroup_id,description,keywords
   */     
  public function getContentGroupId($group_id) {
  	
  	$selectGroupAdmin = $this->select()            
            ->from($this->info('name'), array('mobilecontentgroup_id', 'description', 'keywords'))  
            ->where('name = ?', 'sitegroup_index_view')           
            ->where('group_id =?', $group_id)
            ->limit(1);
    return $this->fetchRow($selectGroupAdmin);
  }

}

?>