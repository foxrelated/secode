<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLayoutController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_AdminLayoutController extends Core_Controller_Action_Admin {

  public function layoutAction() {
    set_time_limit(0);
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_layoutdefault');

    //GET GROUP PARAM
    $group = $this->_getParam('group', 'core_index_index');
    $groupTable = Engine_Api::_()->getDbtable('pages', 'core');
    $groupTableName = $groupTable->info('name');

    $row = Engine_Api::_()->sitegroup()->getWidgetizedGroup();
    if (!empty($row)) {
      $this->view->group_id = $row->page_id;
    }

    $row = Engine_Api::_()->sitegroup()->getMobileWidgetizedGroup();
    if (!empty($row)) {
      $this->view->mobile_group_id = $row->page_id;
    }

    $contentTable = Engine_Api::_()->getDbtable('admincontent', 'sitegroup');

    //GET CURRENT GROUP
    $this->view->groupObject = $groupObject = $groupTable->fetchRow($groupTable->select()->where('name = ?', $group)->orWhere('page_id = ?', $group));

    if (null === $groupObject) {
      $group = 'core_index_index';
      $groupObject = $groupTable->fetchRow($groupTable->select()->where('name = ?', $group));
      if (null === $groupObject) {
        throw new Engine_Exception('Home page is missing');
      }
    }
    $this->view->group = $group;
    $this->view->groupObject = $groupObject;

    //MAKE GROUP FORM
    $this->view->groupForm = $groupForm = new Sitegroup_Form_AdminLayout_Content_Group();
    if (!$groupObject->custom) {
      $groupForm->removeElement('levels');
    }

    $groupForm->populate($groupObject->toArray());
    $levels = $groupForm->getElement('levels');
    if ($levels && !empty($groupObject->levels)) {
      $levels->setValue(Zend_Json_Decoder::decode($groupObject->levels));
    } else if ($levels) {
      $levels->setValue(array_keys($levels->getMultiOptions()));
    }

    //GET GROUP LIST
    $this->view->groupList = $groupList = $groupTable->fetchAll();
    $userlayoutcontentgroupsTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
    $this->view->totalGroups = $userlayoutcontentgroupsTable->select()->from($userlayoutcontentgroupsTable->info('name'), array('count(*) as count'))->query()->fetchColumn();
    $this->view->limit = 500;
    //GET AVAILABLE CONTENT BLOCKS
    $contentAreas = $this->buildCategorizedContentAreas($this->getContentAreas());
    if (!$this->_getParam('show-all', true)) { //@tprepareContentAreaodo change default to false when ready to roll out
      $contentAreas = $this->filterContentAreasByRequirements($contentAreas, $groupObject->provides);
    }
    $this->view->contentAreas = $contentAreas;

    //RE-INDEX BY NAME
    $contentByName = array();
    foreach ($contentAreas as $category => $categoryAreas) {
      foreach ($categoryAreas as $info) {
        $contentByName[$info['name']] = $info;
      }
    }
    $this->view->contentByName = $contentByName;

    //GET REGISTERED CONTENT AREAS
    $contentRowset = $contentTable->fetchAll($contentTable->select()->where('group_id = ?', $groupObject->page_id)->order('order ASC'));
    $contentStructure = $this->prepareContentArea($contentRowset);

    //VALIDATE STRUCTURE
    //NOTE: DO NOT VALIDATE FOR HEADER OR FOOTER
    $error = false;
    if (substr($groupObject->name, 0, 6) !== 'header' && substr($groupObject->name, 0, 6) !== 'footer') {
      foreach ($contentStructure as &$info1) {
        if (!in_array($info1['name'], array('top', 'bottom', 'main')) || $info1['type'] != 'container') {
          $error = true;
          break;
        }
        foreach ($info1['elements'] as &$info2) {
          if (!in_array($info2['name'], array('left', 'middle', 'right')) || $info1['type'] != 'container') {
            $error = true;
            break;
          }
        }
        //RE ORDER SECOND-LEVEL ELEMENTS
        usort($info1['elements'], array($this, '_reorderContentStructure'));
      }
    }

    if ($error) {
      throw new Exception('group failed validation check');
    }

    //ASSIGN STRUCTURE
    $this->view->contentRowset = $contentRowset;
    $this->view->contentStructure = $contentStructure;

    $rows = Engine_Api::_()->getDbtable('hideprofilewidgets', 'sitegroup')->hideWidgets();
    $hideWidgets = array();
    foreach ($rows as $value)
      $hideWidgets[] = $value->widgetname;
    $this->view->hideWidgets = $hideWidgets;

    $session = new Zend_Session_Namespace('sitegrouplayout');
    if (isset($session->structure))
      unset($session->structure);
    if (isset($session->group_object_id))
      unset($session->group_object_id);

		$isSupport = null;
		$coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
		/*
			return < 0 : when running version is lessthen 4.2.1
			return 0 : If running version is equal to 4.2.1
			return > 0 : when running version is greaterthen 4.2.1
		*/
		if( !empty($coreVersion) ) {
			$coreVersion = $coreVersion->version;
			$isPluginSupport = strcasecmp($coreVersion, '4.2.1');
			if( $isPluginSupport >= 0 ) {
				$isSupport = 1;
			}
		}
    if (!empty($isSupport)) {
      $this->renderScript('admin-layout/layout.tpl');
    } else {
      $this->renderScript('admin-layout/layout_default.tpl');
    }
  }

  public function prepareContentArea($content, $current = null) {

    //GET PARENT CONTENT ID
    $parent_content_id = null;
    if (null !== $current) {
      $parent_content_id = $current->admincontent_id;
    }

    //GET CHILDREN
    $children = $content->getRowsMatching('parent_content_id', $parent_content_id);
    if (empty($children) && null === $parent_content_id) {
      $children = $content->getRowsMatching('parent_content_id', 0);
    }

    //GET STRUCT
    $struct = array();
    foreach ($children as $child) {
      $elStruct = $this->createElementParams($child);
      $elStruct['elements'] = $this->prepareContentArea($content, $child);
      $struct[] = $elStruct;
    }

    return $struct;
  }

  public function createElementParams($row) {

    $data = array(
        'identity' => $row->admincontent_id,
        'type' => $row->type,
        'name' => $row->name,
        'order' => $row->order,
    );
    $params = (array) $row->params;
    if (isset($params['title']))
      $data['title'] = $params['title'];
    $data['params'] = $params;
    return $data;
  }

  public function updateAction() {
    //GET NAVIGATION   
    $groupTable = Engine_Api::_()->getDbtable('pages', 'core');
    $contentTable = Engine_Api::_()->getDbtable('admincontent', 'sitegroup');
    $userlayoutcontentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');
    $userlayoutcontentTablename = $userlayoutcontentTable->info('name');
    set_time_limit(0);
    $userlayoutcontentgroupsTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
    $totalGroups = $userlayoutcontentgroupsTable->select()->from($userlayoutcontentgroupsTable->info('name'), array('count(*) as count'))->query()->fetchColumn();
    $limit = 150;
    $group_reload = $this->_getParam('group_reload', 1);
    $offset = ($group_reload - 1) * $limit;

    $select = $userlayoutcontentTable->select()->from($userlayoutcontentTablename, array('contentgroup_id'))->limit($limit, $offset)->group('contentgroup_id');

    $content_groups_id = $userlayoutcontentTable->fetchAll($select);
    $session = new Zend_Session_Namespace('sitegrouplayout');

    $db = $groupTable->getAdapter();
    $db->beginTransaction();
		//CLOSE THE SMOOTHBOX
		$reload_count = round($totalGroups / $limit);
    try {
      //GET GROUP
      $group = $this->_getParam('group');

      $groupObject = $groupTable->fetchRow($groupTable->select()->where('name = ?', $group)->orWhere('page_id = ?', $group));
      if (null === $groupObject) {
        throw new Engine_Exception('Page is missing');
      }

      //UPDATE LAYOUT
      if (null !== ($newLayout = $this->_getParam('admin_sitegroup_layout'))) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.show.hide.header.footer', $newLayout);
      }

      //GET REGISTERED CONTENT AREAS
      if (isset($session->group_object_id)) {
        $group_object_id = $session->group_object_id;
        $contentRowset = $contentTable->fetchAll($contentTable->select()->where('group_id = ?', $group_object_id)->where('default_admin_layout = ?', 0));
      } else {
        $session->group_object_id = $groupObject->page_id;
        $contentRowset = $contentTable->fetchAll($contentTable->select()->where('group_id = ?', $groupObject->page_id));
      }
      //GET STRUCTURE
      if (isset($session->structure)) {
        $structure = $session->structure;
      } else {
        $structure = $this->_getParam('structure');

        $session->structure = Zend_Json::decode(trim($structure, '()'));

        if (is_string($structure)) {
          $structure = Zend_Json::decode(trim($structure, '()'));
        }

        if (!is_array($structure)) {
          throw new Engine_Exception('Structure is not an array or valid json structure');
        }
      }

      //DIFF
      $orderIndex = 1;
      $newRowsByTmpId = array();
			$oldContentRowParams=null;
      $existingRowsByContentId = array();

      foreach ($structure as $element) {
        //GET INFO
        $content_id = @$element['identity'];
        $tmp_content_id = @$element['tmp_identity'];
        $parent_id = @$element['parent_identity'];
        $tmp_parent_id = @$element['parent_tmp_identity'];

        $newOrder = $orderIndex++;

        //SANITY
        if (empty($content_id) && empty($tmp_content_id)) {
          throw new Exception('content id and tmp content id both empty');
        }

        //GET EXISTING CONTENT ROW (IF ANY)
        $contentRow = null;
        if (!empty($content_id)) {
          $contentRow = $contentRowset->getRowMatching('admincontent_id', $content_id);
          if (null === $contentRow) {
            throw new Exception('content row missing');
          }
        }

        //GET EXISTING PARENT ROW (IF ANY)
        $parentContentRow = null;
        if (!empty($parent_id)) {
          $parentContentRow = $contentRowset->getRowMatching('admincontent_id', $parent_id);
        } else if (!empty($tmp_parent_id)) {
          $parentContentRow = @$newRowsByTmpId[$tmp_parent_id];
        }

        //EXISTING ROW
        if (!empty($contentRow) && is_object($contentRow)) {
          $existingRowsByContentId[$content_id] = $contentRow;

          //UPDATE ROW
          if (!empty($parentContentRow)) {
            $contentRow->parent_content_id = $parentContentRow->admincontent_id;
          }
          if (empty($contentRow->parent_content_id)) {
            $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
          }

          //SET PARAMS
          $oldContentRow = null;
          $oldContentRowParams = json_encode($contentRow->params);
          if (isset($element['params']) && is_array($element['params'])) {
            $contentRow->params = $element['params'];
          }

          if ($contentRow->type == 'container') {
            $newOrder = array_search($contentRow->name, array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow->order = $newOrder;
          $contentRow->save();

          foreach ($content_groups_id as $value) {

            $content_group_id = $value->contentgroup_id;

            if ($contentRow->type == 'container' && $contentRow->name == 'middle') {
              $select_parent = $userlayoutcontentTable->select()->where('type =?', 'container')->where('name =?', $parentContentRow->name)->where('contentgroup_id =?', $content_group_id);
              $content_parent = $userlayoutcontentTable->fetchRow($select_parent);
              $select = $userlayoutcontentTable->select()->where('type =?', $contentRow->type)->where('name =?', $contentRow->name)->where('contentgroup_id =?', $content_group_id)->where('parent_content_id = ?', $content_parent->content_id);
            } else {
              $select = $userlayoutcontentTable->select()->where('type =?', $contentRow->type)->where('name =?', $contentRow->name)->where('contentgroup_id =?', $content_group_id);
            }
            $usercontentRow = $userlayoutcontentTable->fetchRow($select);
            if (empty($usercontentRow))
              continue;

            if ($usercontentRow->type == 'container') {
              $newOrder = array_search($usercontentRow->name, array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
              $userlayoutcontentTable->update(array('order' => $newOrder), array('type = ?' => $contentRow->type, 'contentgroup_id = ?' => $content_group_id, 'name = ?' => $contentRow->name, 'widget_admin = ?' => 1));
            }

            if (isset($session->edittitles) && in_array($element['name'], $session->edittitles)) {
							$userlayoutcontentTable->update(array('params' => $element['params']), array('type = ?' => $contentRow->type, 'contentgroup_id = ?' => $content_group_id, 'name = ?' => $contentRow->name, 'widget_admin = ?' => 1, 'parent_content_id = ?' => $usercontentRow->parent_content_id, 'params =?' => $oldContentRowParams));
            }

            $parent_id = 0;
            if (!empty($parentContentRow)) {
              $select = $userlayoutcontentTable->select()->where('type =?', $parentContentRow->type)->where('name =?', $parentContentRow->name)->where('contentgroup_id =?', $content_group_id);
              $content_groups = $userlayoutcontentTable->fetchRow($select);
              if (!empty($parentContentRow->parent_content_id)) {
                $parentparentContentRow = $contentRowset->getRowMatching('admincontent_id', $parentContentRow->parent_content_id);
                if (!empty($parentparentContentRow)) {
                  $select = $userlayoutcontentTable->select()->where('type =?', $parentparentContentRow->type)->where('name =?', $parentparentContentRow->name)->where('contentgroup_id =?', $content_group_id);
                  $contentParent_groups = $userlayoutcontentTable->fetchRow($select);
                  if ($contentParent_groups->name != $parentparentContentRow->name) {
                    $select = $userlayoutcontentTable->select()->where('type =?', $parentContentRow->type)->where('name =?', $parentContentRow->name)->where('contentgroup_id =?', $content_group_id)->where('parent_content_id <> ?', $content_groups->parent_content_id);
                    $content_groups_2 = $userlayoutcontentTable->fetchRow($select);
                    if (!empty($content_groups_2))
                      $content_groups = $content_groups_2;
                  }
                }
              }
              $parent_id = $content_groups->content_id;
            } else {
              $userlayoutcontentTable->update(array('parent_content_id' => new Zend_Db_Expr('NULL'), 'order' => $newOrder), array('type = ?' => $contentRow->type, 'contentgroup_id = ?' => $content_group_id, 'name = ?' => $contentRow->name, 'widget_admin = ?' => 1));
            }

            if (!empty($parent_id)) {
              //if you place any widget two times please mantion in this condition
              if ($usercontentRow->name != 'core.html-block' && $usercontentRow->name != 'core.ad-campaign' && $usercontentRow->name != 'sitegroupintegration.profile-items' && $usercontentRow->name != 'sitegroupmember.profile-sitegroupmembers') {
                $userlayoutcontentTable->update(array('parent_content_id' => $parent_id, 'order' => $newOrder), array('content_id = ?' => $usercontentRow->content_id, 'widget_admin = ?' => 1));
              }
              if (empty($usercontentRow->parent_content_id)) {
                $userlayoutcontentTable->update(array('parent_content_id' => new Zend_Db_Expr('NULL'), 'order' => $newOrder), array('content_id = ?' => $usercontentRow->content_id, 'widget_admin = ?' => 1));
              }
            }
          }

          //SET PARENT CONTENT
          if (!empty($parentContentRow)) {
            $contentRow->parent_content_id = $parentContentRow->admincontent_id;
          }
          if (empty($contentRow->parent_content_id)) {
            $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
          }
          $contentRow->save();
        }

        //NEW ROW
        else {
          if ($group_reload == 1) {
            if (empty($element['type']) || empty($element['name'])) {
              throw new Exception('missing name and/or type info');
            }

            if ($element['type'] == 'container') {
              $newOrder = array_search($element['name'], array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
            }

            $contentRow = $contentTable->createRow();
            $contentRow->group_id = $groupObject->page_id;
            $contentRow->order = $newOrder;
            $contentRow->type = $element['type'];
            $contentRow->name = $element['name'];
            $contentRow->default_admin_layout = 1;

            //SET PARENT CONTENT
            if (!empty($parentContentRow)) {
              $contentRow->parent_content_id = $parentContentRow->admincontent_id;
            }
            if (empty($contentRow->parent_content_id)) {
              $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
            }
            $contentRow->save();
          }
          foreach ($content_groups_id as $value) {
            $content_group_id = $value->contentgroup_id;
            $userlayoutgroups = null;
            if ($element['type'] == 'container') {
              $newOrder = array_search($element['name'], array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
            } else {
              if ($element['name'] != 'core.html-block' && $element['name'] != 'core.ad-campaign' && $element['name'] != 'sitegroupintegration.profile-items' && $element['name'] != 'sitegroupmember.profile-sitegroupmembers') {
                $select = $userlayoutcontentTable->select()->where('name =?', $element['name'])->where('contentgroup_id =?', $content_group_id);
                $userlayoutgroups = $userlayoutcontentTable->fetchRow($select);
              } else {
                $select = $userlayoutcontentTable->select()->where('name =?', $element['name'])->where('contentgroup_id =?', $content_group_id)->where('params =?', json_encode($element['params']));
                $userlayoutgroups = $userlayoutcontentTable->fetchRow($select);
              }
            }

            if (empty($userlayoutgroups) && isset($element['type']) && isset($element['name'])) {
              $usercontentRow = $userlayoutcontentTable->createRow();
              $usercontentRow->contentgroup_id = $content_group_id;
              $usercontentRow->order = $newOrder;
              $usercontentRow->type = $element['type'];
              $usercontentRow->name = $element['name'];
              if (!empty($parentContentRow)) {
                $select = $userlayoutcontentTable->select()->where('type =?', $parentContentRow->type)->where('name =?', $parentContentRow->name)->where('contentgroup_id =?', $content_group_id)->order('content_id DESC')->order('content_id DESC');
                $content_groups = $userlayoutcontentTable->fetchRow($select);
                if (!empty($content_groups)) {
                  $usercontentRow->parent_content_id = $content_groups->content_id;
                }
                if (empty($usercontentRow->parent_content_id)) {
                  $usercontentRow->parent_content_id = new Zend_Db_Expr('NULL');
                }
              }

              //SET PARAMS
              if (isset($element['params']) && is_array($element['params'])) {
                $usercontentRow->params = $element['params'];
              }
              $usercontentRow->save();
            }
          }

          if ($group_reload == 1) {
            //SET PARAMS
            if (isset($element['params']) && is_array($element['params'])) {
              $contentRow->params = json_encode($element['params']);
            }
            $contentRow->save();
            $newRowsByTmpId[$tmp_content_id] = $contentRow;
          }
        }
      }

      //DELETE ROWS THAT WERE NOT PRESENT IN DATA SENT BACK
      $deletedRowIds = array();
      foreach ($contentRowset as $contentRow) {
        if (empty($existingRowsByContentId[$contentRow->admincontent_id])) {
          $parentContentRow = "";
          if (!empty($contentRow->parent_content_id))
            $parentContentRow = $contentRowset->getRowMatching('admincontent_id', $contentRow->parent_content_id);

          $oldContentRow = json_encode($contentRow->params);
          foreach ($content_groups_id as $value) {
            $parent_id = 0;
            $content_group_id = $value->contentgroup_id;
            if (!empty($parentContentRow)) {
              $select = $userlayoutcontentTable->select()->where('type =?', $parentContentRow->type)->where('name =?', $parentContentRow->name)->where('contentgroup_id =?', $content_group_id);
              $content_groups = $userlayoutcontentTable->fetchRow($select);
              $parentparentContentRow = $contentRowset->getRowMatching('admincontent_id', $parentContentRow->admincontent_id);
              if (!empty($parentparentContentRow)) {
                $select = $userlayoutcontentTable->select()->where('type =?', $parentparentContentRow->type)->where('name =?', $parentparentContentRow->name)->where('contentgroup_id =?', $content_group_id);
                $contentParent_groups = $userlayoutcontentTable->fetchRow($select);
                if ($contentParent_groups && $contentParent_groups->name != $parentparentContentRow->name) {
                  $select = $userlayoutcontentTable->select()->where('type =?', $parentContentRow->type)->where('name =?', $parentContentRow->name)->where('contentgroup_id =?', $content_group_id)->where('parent_content_id <> ?', $contentParent_groups->content_id);
                  $content_groups = $userlayoutcontentTable->fetchRow($select);
                }
              }
              $parent_id = $content_groups->content_id;
            }
            if (!empty($parent_id)) {
              if ( $contentRow->name != 'core.ad-campaign' && $contentRow->name != 'sitegroupintegration.profile-items') {
                $select = $userlayoutcontentTable->select()->where('type =?', $contentRow->type)->where('name =?', $contentRow->name)->where('contentgroup_id =?', $content_group_id)->where('parent_content_id =?', $parent_id);
              } else {
                $select = $userlayoutcontentTable->select()->where('type =?', $contentRow->type)->where('name =?', $contentRow->name)->where('contentgroup_id =?', $content_group_id)->where('parent_content_id =?', $parent_id)->where('params =?', $oldContentRow);
              }
              $content_groups = $userlayoutcontentTable->fetchRow($select);
              if (!empty($content_groups)) {
                $userlayoutcontentTable->delete(array(
                    'name =?' => $contentRow->name, 'contentgroup_id =?' => $content_group_id, 'parent_content_id =?' => $parent_id, 'content_id =?' => $content_groups->content_id, 'widget_admin = ?' => 1
                ));
              }
            } else {
              if (($contentRow->name == 'top' || $contentRow->name == 'bottom') && $contentRow->type == 'container') {
                $select = $userlayoutcontentTable->select()->where('type =?', $contentRow->type)->where('name =?', $contentRow->name)->where('contentgroup_id =?', $content_group_id);
                $content_groups = $userlayoutcontentTable->fetchRow($select);
                $userlayoutcontentTable->delete(array('type =?' => 'container',
                    'name =?' => 'middle', 'contentgroup_id =?' => $content_group_id, 'parent_content_id = ?' => $content_groups->content_id, 'widget_admin = ?' => 1
                ));
                $userlayoutcontentTable->delete(array(
                    'name =?' => $contentRow->name, 'contentgroup_id =?' => $content_group_id, 'content_id =?' => $content_groups->content_id, 'widget_admin = ?' => 1
                ));
              }
            }
          }
          if ($group_reload >= $reload_count) {
            $contentRow->delete();
          }
        }
      }
      $this->view->deleted = $deletedRowIds;
      $select = $contentTable->select()->where('group_id =?', $group)->where('name =?', 'top');
      $top = $contentTable->fetchRow($select);
      if (!empty($top)) {
        $select = $contentTable->select()->where('group_id =?', $group)->where('name =?', 'middle')->where('parent_content_id =?', $top->admincontent_id);
        $middle = $contentTable->fetchRow($select);
				if (!empty($middle)) {
					$select = $contentTable->select()->where('group_id =?', $group)->where('parent_content_id =?', $middle->admincontent_id);
					$middle_block = $contentTable->fetchAll($select);
					$name = array();
					foreach ($middle_block as $block) {
						$name[] = $block->name;
					}
					foreach ($content_groups_id as $value) {
						$content_group_id = $value->contentgroup_id;
						$select = $userlayoutcontentTable->select()->where('name =?', 'top')->where('contentgroup_id =?', $content_group_id);
						$user_top = $userlayoutcontentTable->fetchRow($select);
						if (!empty($user_top)) {
							$select = $userlayoutcontentTable->select()->where('name =?', 'middle')->where('contentgroup_id =?', $content_group_id)->where('parent_content_id =?', $user_top->content_id);
							$user_middile = $userlayoutcontentTable->fetchRow($select);
							if ($user_middile) {
								foreach ($name as $name_value) {
									$userlayoutcontentTable->update(array('parent_content_id' => $user_middile->content_id), array('contentgroup_id = ?' => $content_group_id, 'name = ?' => $name_value, 'widget_admin = ?' => 1));
								}
							}
						}
					}
				}
      } else {
        $select = $contentTable->select()->where('group_id =?', $group)->where('name =?', 'bottom');
        $bottom = $contentTable->fetchRow($select);
        if (!empty($bottom)) {
          $select = $contentTable->select()->where('group_id =?', $group)->where('name =?', 'middle')->where('parent_content_id =?', $bottom->admincontent_id);
          $middle = $contentTable->fetchRow($select);
          $select = $contentTable->select()->where('group_id =?', $group)->where('parent_content_id =?', $middle->admincontent_id);
          $middle_block = $contentTable->fetchAll($select);
          $name = array();
          foreach ($middle_block as $block) {
            $name[] = $block->name;
          }

          foreach ($content_groups_id as $value) {
            $content_group_id = $value->contentgroup_id;
            $select = $userlayoutcontentTable->select()->where('name =?', 'bottom')->where('contentgroup_id =?', $content_group_id);
            $user_bottom = $userlayoutcontentTable->fetchRow($select);
            if (!empty($user_bottom)) {
              $select = $userlayoutcontentTable->select()->where('name =?', 'middle')->where('contentgroup_id =?', $content_group_id)->where('parent_content_id =?', $user_bottom->content_id);
              $user_middile = $userlayoutcontentTable->fetchRow($select);
              if ($user_middile) {
                foreach ($name as $name_value) {
                  $userlayoutcontentTable->update(array('parent_content_id' => $user_middile->content_id), array('contentgroup_id = ?' => $content_group_id, 'name = ?' => $name_value, 'widget_admin = ?' => 1));
                }
              }
            }
          }
        }
      }

      $edit_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layout.setting', 0);
      if (empty($edit_layout_setting)) {
        $select = $contentTable->select()->where('name =?', 'sitegroup.widgetlinks-sitegroup');
        $user_widgetlink = $contentTable->fetchRow($select);
        if (empty($user_widgetlink)) {
          foreach ($content_groups_id as $value) {
            $content_group_id = $value->contentgroup_id;
            $userlayoutcontentTable->delete(array(
                'name =?' => 'sitegroup.widgetlinks-sitegroup', 'contentgroup_id =?' => $content_group_id));
          }
        }
      }

      //SEND BACK NEW CONTENT INFO
      $newData = array();
      foreach ($newRowsByTmpId as $tmp_id => $newRow) {
        $newData[$tmp_id] = $this->createElementParams($newRow);
      }
      $this->view->newIds = $newData;

      $this->view->status = true;
      $this->view->error = false;
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = true;
    }



    if ($group_reload <= $reload_count) {
      $group_reload++;

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => false,
          'redirect' => $this->view->url(array('module' => 'sitegroup', 'controller' => 'layout', 'action' => 'update', 'group' => $group, 'group_reload' => $group_reload, 'admin_sitegroup_layout' => $newLayout), 'admin_default', true),
          // 'parentRedirectTime' => '10',
          //  format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('<div class="tip" style="margin:10px auto;width:750px;"><span>Please do not close this group or navigate to another group till you see a layout changes completion or error message.</span></div><div>
					<center><img src="application/modules/Sitegroup/externals/images/layout/uploading.gif" alt="" /></center>
      	</div>'))
      ));
    } else {

      $contentTable->update(array('default_admin_layout' => 0), array('default_admin_layout = ?' => 1));
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => false,
          'redirect' => $this->view->url(array('module' => 'sitegroup', 'controller' => 'layout', 'action' => 'layout', 'group' => $group), 'admin_default', true),
          'parentRedirectTime' => '10',
          //'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('<ul class="form-notices" style="margin:10px auto;width:500px;float:none;"><li style="float:none;">Your changes have been saved successfully.</li></ul>'))
      ));
    }

    if (isset($session->edittitles))
      unset($session->edittitles);
  }

  public function saveAction() {

    $groupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
    $groupObject = $groupTable->fetchAll($groupTable->select());
    $form = new Sitegroup_Form_AdminLayout_Content_Group();
    $groupcoreTable = Engine_Api::_()->getDbtable('pages', 'core');
    $groupcoreObject = $groupcoreTable->fetchAll($groupcoreTable->select()->where('name =?', 'sitegroup_index_view'));
    $form->populate($groupObject->toArray());
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      $groupTable->update(array('description' => $values['description'], 'keywords' => $values['keywords']), array('name = ?' => 'sitegroup_index_view'));
      $groupcoreTable->update(array('description' => $values['description'], 'keywords' => $values['keywords']), array('name = ?' => 'sitegroup_index_view'));
      $form->addNotice($this->view->translate('Your changes have been saved.'));
    }

    $this->getResponse()->setBody($form->render($this->view));
    $this->_helper->layout->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);
    return;
  }

  public function widgetAction() {

    //RENDER BY WIDGET NAME
    $name = $this->_getParam('name');
    $mod = $this->_getParam('mod');
    if (null === $name) {
      throw new Exception('no widget found with name: ' . $name);
    }
    if (null !== $mod) {
      $name = $mod . '.' . $name;
    }

    $contentInfoRaw = $this->getContentAreas();
    $contentInfo = array();
    foreach ($contentInfoRaw as $info) {
      $contentInfo[$info['name']] = $info;
    }

    //IT HAS A FORM SPECIFIED IN CONTENT MANIFEST
    if (!empty($contentInfo[$name]['adminForm'])) {
      if (is_string($contentInfo[$name]['adminForm'])) {
        $formClass = $contentInfo[$name]['adminForm'];
        Engine_Loader::loadClass($formClass);
        $this->view->form = $form = new $formClass();
      } else if (is_array($contentInfo[$name]['adminForm'])) {
        $this->view->form = $form = new Engine_Form($contentInfo[$name]['adminForm']);
      } else {
        throw new Core_Model_Exception('Unable to load admin form class');
      }

      //TRY TO SET TITLE IF MISSING
      if (!$form->getTitle()) {
        $form->setTitle('Editing: ' . $contentInfo[$name]['title']);
      }

      //TRY TO SET DESCRIPTION IF MISSING
      if (!$form->getDescription()) {
        $form->setDescription('placeholder');
      }

      $form->setAttrib('class', 'global_form_popup ' . $form->getAttrib('class'));

      //ADD TITLE ELEMENT
      if (!$form->getElement('title')) {
        $form->addElement('Text', 'title', array(
            'label' => 'Title',
            'order' => -100,
        ));
      }

      if (!empty($contentInfo[$name]['isPaginated']) && !$form->getElement('itemCountPerGroup')) {
        $form->addElement('Text', 'itemCountPerGroup', array(
            'label' => 'Count',
            'description' => '(number of items to show)',
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
            'order' => 1000000 - 1,
        ));
      }

      //ADD SUBMIT BUTTON
      if (!$form->getElement('submit') && !$form->getElement('execute')) {
        $form->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
            'order' => 1000000,
        ));
      }

      //ADD NAME
      $form->addElement('Hidden', 'name', array(
          'value' => $name,
          'order' => 1000010,
      ));

      if (!$form->getElement('cancel')) {
        $form->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'parent.Smoothbox.close();',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
            'order' => 1000001,
        ));
      }

      if (!$form->getDisplayGroup('buttons')) {
        $submitName = ( $form->getElement('execute') ? 'execute' : 'submit' );
        $form->addDisplayGroup(array(
            $submitName,
            'cancel',
                ), 'buttons', array(
            'order' => 1000002,
        ));
      }

      //FORCE METHOD AND ACTION
      $form->setMethod('post')
              ->setAction($_SERVER['REQUEST_URI']);

      if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

			$session = new Zend_Session_Namespace('sitegrouplayout');
      if (isset($session->edittitles))
        unset($session->edittitles);
      $session->edittitles[] = $name;
        $this->view->values = $form->getValues();
        $this->view->form = null;

      }

      return;
    }

    //TRY TO RENDER ADMIN GROUP
    if (!empty($contentInfo[$name])) {
      try {
        $structure = array(
            'type' => 'widget',
            'name' => $name,
            'request' => $this->getRequest(),
            'action' => 'admin',
            'throwExceptions' => true,
        );

        //CREATE ELEMENT (WITH STRUCTURE)
        $element = new Engine_Content_Element_Container(array(
                    'elements' => array($structure),
                    'decorators' => array(
                        'Children'
                    )
                ));

        $content = $element->render();
        $this->getResponse()->setBody($content);
        $this->_helper->viewRenderer->setNoRender(true);
        return;
      } catch (Exception $e) {
        
      }
    }

    //JUST RENDER DEFAULT EDITING FORM
    $this->view->form = $form = new Engine_Form(array(
                'title' => $contentInfo[$name]['title'],
                'description' => 'placeholder',
                'method' => 'post',
                'action' => $_SERVER['REQUEST_URI'],
                'class' => 'global_form_popup',
                'elements' => array(
                    array(
                        'Text',
                        'title',
                        array(
                            'label' => 'Title',
                        )
                    ),
                    array(
                        'Button',
                        'submit',
                        array(
                            'label' => 'Save',
                            'type' => 'submit',
                            'decorators' => array('ViewHelper'),
                            'ignore' => true,
                            'order' => 1501,
                        )
                    ),
                    array(
                        'Hidden',
                        'name',
                        array(
                            'value' => $name,
                        )
                    ),
                    array(
                        'Cancel',
                        'cancel',
                        array(
                            'label' => 'cancel',
                            'link' => true,
                            'prependText' => ' or ',
                            'onclick' => 'parent.Smoothbox.close();',
                            'ignore' => true,
                            'decorators' => array('ViewHelper'),
                            'order' => 1502,
                        )
                    )
                ),
                'displaygroups' => array(
                    'buttons' => array(
                        'name' => 'buttons',
                        'elements' => array(
                            'submit',
                            'cancel',
                        ),
                        'options' => array(
                            'order' => 1500,
                        )
                    )
                )
            ));

    if (!empty($contentInfo[$name]['isPaginated'])) {
      $form->addElement('Text', 'itemCountPerGroup', array(
          'label' => 'Count',
          'description' => '(number of items to show)',
          'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
          )
      ));
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $session = new Zend_Session_Namespace('sitegrouplayout');
      if (isset($session->edittitles))
        unset($session->edittitles);
      $session->edittitles[] = $name;
      $this->view->values = $form->getValues();
      $this->view->form = null;
    } else {
      $form->populate($this->_getAllParams());
    }
  }

  public function getContentAreas() {

    $contentAreas = array();
    //FROM MODULES
    $modules = Zend_Controller_Front::getInstance()->getControllerDirectory();
    foreach ($modules as $module => $path) {
      $contentManifestFile = dirname($path) . '/settings/content.php';
      if (!file_exists($contentManifestFile))
        continue;
      $ret = include $contentManifestFile;
      $contentAreas = array_merge($contentAreas, (array) $ret);
    }

    //FROM WIDGETS
    $it = new DirectoryIterator(APPLICATION_PATH . '/application/widgets');
    foreach ($it as $dir) {
      if (!$dir->isDir() || $dir->isDot())
        continue;
      $path = $dir->getPathname();
      $contentManifestFile = $path . '/' . 'manifest.php';
      if (!file_exists($contentManifestFile))
        continue;
      $ret = include $contentManifestFile;
      if (!is_array($ret))
        continue;
      array_push($contentAreas, $ret);
    }

    return $contentAreas;
  }

  public function buildCategorizedContentAreas($contentAreas) {

    $categorized = array();
    foreach ($contentAreas as $config) {
      //CHECK SOME STUFF
      if (!empty($config['requireItemType'])) {
        if (is_string($config['requireItemType']) && !Engine_Api::_()->hasItemType($config['requireItemType'])) {
          $config['disabled'] = true;
        } else if (is_array($config['requireItemType'])) {
          $tmp = array_map(array(Engine_Api::_(), 'hasItemType'), $config['requireItemType']);
          $config['disabled'] = !(array_sum($tmp) == count($config['requireItemType']));
        }
      }

      //ADD TO CATEGORY
      $category = ( isset($config['category']) ? $config['category'] : 'Uncategorized' );
      $categorized[$category][] = $config;
    }

    //SORT CATEGORIES
    uksort($categorized, array($this, '_sortCategories'));

    //SORT ITEMS IN CATEGORIES
    foreach ($categorized as $category => &$items) {
      usort($items, array($this, '_sortCategoryItems'));
    }

    return $categorized;
  }

  public function filterContentAreasByRequirements($contentAreas, $provides) {

    //PROCESS PROVIDES
    if (is_string($provides)) {
      $providedFeatures = explode(';', $provides);
      $provides = array();
      foreach ($providedFeatures as $providedFeature) {
        if (false === strpos($providedFeature, '=')) {
          $provides[$providedFeature] = true;
        } else {
          list($feature, $value) = explode('=', $providedFeature);
          if (false === strpos($value, ',')) {
            $provides[$feature] = $value;
          } else {
            $provides[$feature] = explode(',', $value);
          }
        }
      }
    } else if (!is_array($provides)) {
      $provides = array();
    }

    //PROCESS CONTENT AREAS
    $filteredContentAreas = array();
    foreach ($contentAreas as $category => $categoryWidgets) {
      foreach ($categoryWidgets as $widget) {
        $pass = true;
        //REQUIREMENTS
        if (!empty($widget['requirements']) && is_array($widget['requirements'])) {
          foreach ($widget['requirements'] as $k => $v) {
            if (is_numeric($k)) {
              $req = $v;
              $value = null;
            } else {
              $req = $k;
              $value = $v;
            }
            //NOTE: WILL CONTINUE IF MISSING ANY OF THE REQUIREMENTS
            switch ($req) {
              case 'viewer':
                if (isset($provides['no-viewer'])) {
                  $pass = false;
                }
                break;
              case 'no-viewer':
                if (isset($provides['viewer'])) {
                  $pass = false;
                }
                break;
              case 'subject':
                if (!isset($provides['subject']) /* ||
                  isset($provides['no-subject']) */) {
                  $pass = false;
                } else if (is_string($value)) {
                  if (is_string($provides['subject']) &&
                          $provides['subject'] == $value) {
                    
                  } else if (is_array($provides['subject']) &&
                          in_array($value, $provides['subject'])) {
                    
                  } else {
                    $pass = false;
                  }
                } else if (is_array($value)) {
                  if (count(array_intersect($value, (array) $provides['subject'])) <= 0) {
                    $pass = false;
                  }
                }
                break;
              case 'no-subject':
                if (isset($provides['subject']) /* ||
                  !isset($provides['no-subject']) */) {
                  $pass = false;
                }
                //@todo subject blacklist?
                break;
              case 'header-footer';
                if (!isset($provides['header-footer'])) {
                  $pass = false;
                }
                break;
            }
          }
        }
        //ADD TO AREAS
        if ($pass) {
          $filteredContentAreas[$category][] = $widget;
        }
      }
    }

    return $filteredContentAreas;
  }

  protected function _sortCategories($a, $b) {

    if ($a == 'Core')
      return -1;
    if ($b == 'Core')
      return 1;
    return strcmp($a, $b);
  }

  protected function _sortCategoryItems($a, $b) {

    if (!empty($a['special']))
      return -1;
    if (!empty($b['special']))
      return 1;
    return strcmp($a['title'], $b['title']);
  }

  protected function _reorderContentStructure($a, $b) {

    $sample = array('left', 'middle', 'right');
    $av = $a['name'];
    $bv = $b['name'];
    $ai = array_search($av, $sample);
    $bi = array_search($bv, $sample);
    if ($ai === false && $bi === false)
      return 0;
    if ($ai === false)
      return -1;
    if ($bi === false)
      return 1;
    $r = ( $ai == $bi ? 0 : ($ai < $bi ? -1 : 1) );
    return $r;
  }

  public function showHideWidgetAction() {

    //GET WIDGET NAME
    $widgetname = $this->_getParam('widgetname', null);

    //GET OPTION
    $option = $this->_getParam('option', null);
    $hideprofilewidget_table = Engine_Api::_()->getDbTable('hideprofilewidgets', 'sitegroup');
    $row = $hideprofilewidget_table->fetchRow($hideprofilewidget_table->select()->where('widgetname =?', $widgetname));
    if ($option == 1) {
      $contentgroupstable = Engine_Api::_()->getDbTable('contentgroups', 'sitegroup');
      $contentgroupsrow = $contentgroupstable->fetchAll($contentgroupstable->select());
      $admincontenttable = Engine_Api::_()->getDbTable('admincontent', 'sitegroup');
      $admincontentrow = $admincontenttable->fetchRow($admincontenttable->select()->where('name =?', $widgetname));
      if (empty($admincontentrow)) {
        foreach ($contentgroupsrow as $value) {
          Engine_Api::_()->getDbTable('content', 'sitegroup')->delete(array('name =?' => $widgetname));
        }
      }
      if (empty($row)) {
        $hideprofilewidget_table->insert(array('widgetname' => $widgetname));
      }
    } else {
      if (!empty($row)) {
        $hideprofilewidget_table->delete(array('widgetname =?' => $widgetname));
      }
    }
  }
}

?>