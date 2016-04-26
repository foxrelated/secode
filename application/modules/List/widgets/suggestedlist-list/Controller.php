<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Controller.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Widget_SuggestedlistListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // Get subject and check auth
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return $this->setNoRender();
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $list = Engine_Api::_()->core()->getSubject('list_listing');
        
    //FINDING THE LOCATION OF THIS LIST.
    $location_table = Engine_Api::_()->getDbtable('locations', 'list');
    $location_rName = $location_table->info('name');
    $select_location = $location_table->select();
    $select_location
        ->setIntegrityCheck(false)
        ->from($location_rName, array('city'))
        ->where($location_rName . '.listing_id = ?', $list->listing_id);
    $location_row = $location_table->fetchRow($select_location);
    if (!empty($location_row)) {
			$location_row = $location_row->toarray();
    }
    
    $this->view->view_listing_id = $list->listing_id;
    //get Tag for list
    $this->view->listTags = $listTags = $list->tags()->getTagMaps();
    $tagString = '';
    foreach ($listTags as $value) {
      $tagString .= "'" . $value->tag_id . "',";
    }
    $tagString = trim($tagString, ",");
    //GETTING THE TAG ID OF THIS LIST ID.
    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.suggest.lists', 5);
    $searchTable = Engine_Api::_()->fields()->getTable('list_listing', 'search')->info('name');
    $values['category'] = $list->category_id;
    $table = Engine_Api::_()->getDbtable('listings', 'list');
    $rName = $table->info('name');
    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');
    $select = $table->select();
    $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->order('RAND() DESC ')
        ->where($rName . '.owner_id <> ?', $viewer_id)
        ->where($rName . '.listing_id <> ?', $list->listing_id)
        ->where($rName . '.closed = ?', '0')
        ->where($rName . '.draft = ?', '1')
        ->where($rName . '.approved = ?', '1')
				->where($rName . ".search = ?", 1)
        ->group($rName . '.listing_id')
        ->limit($items_count);

    $sqlStr = '';
    if (!empty($tagString)) {
      $select
          ->setIntegrityCheck(false)
          ->joinLeft($tmName, "$tmName.resource_id = $rName.listing_id")
          ->where($tmName . '.resource_type = ?', 'list_listing');
      $sqlStr = $tmName . '.tag_id IN(' . $tagString . ')';
    }
    if (!empty($list->category_id)) {
      if (empty($sqlStr)) {
        $sqlStr = $rName . '.category_id = ' . "'" . $list->category_id . "'";
      } else {
        $sqlStr.= ' OR ' . $rName . '.category_id = ' . "'" . $list->category_id . "'";
      }
    }

    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($list);
    $customParams = array();

    foreach ($fieldStructure as $map) {
      // Get field meta object
      $field = $map->getChild();
			if( !empty($field) ) {
				$value = $field->getValue($list);
				if (!empty($value)) {
					$customParams[@strtolower(@$field->label)] = @$value->value;
				}
			}
    }

    if (isset($customParams)) {
      $select = $select
              ->setIntegrityCheck(false)
              ->joinLeft($searchTable, "$searchTable.item_id = $rName.listing_id");

      $searchParts = Engine_Api::_()->fields()->getSearchQuery('list_listing', $customParams);

      foreach ($searchParts as $k => $v) {

        $k = str_replace('?', '', $k);
        $ck =  str_replace('= ', '', $k);
        $ck = trim($ck, ' ');
       
          if ($ck=='price' ||$ck=='currency') {
						$price_min = $v - abs(($v*10)/100);
            $price_max = $v + abs(($v*10)/100);
					  if (!empty($sqlStr)) {
							$sqlStr.= ' OR ' . $searchTable . '.' . $ck . " BETWEEN " . "'" . $price_min . "'" . " AND " . "'" . $price_max . "'" . "";
						} else {
							$sqlStr.= $searchTable . '.' . $ck . " BETWEEN " . "'" . $price_min . "'" . " AND " . "'" . $price_max . "'" . "";
						}
          }
        }
    }
    
    if (!empty($location_row['city'])) {

      $select->join($location_rName, "$location_rName.listing_id = $rName.listing_id", null);

			if (!empty($sqlStr)) {
				$sqlStr.= ' OR ' . $location_rName . '.city =' . "'" .  $location_row['city'] . "'";
			} else {
				$sqlStr.= $location_rName . '.city =' . "'" .  $location_row['city'] . "'";
			}
    }
  
    if (!empty($sqlStr)) {
      $select->where($sqlStr);
    }
   
    $results = $table->fetchAll($select);
    $this->view->suggestedlist = $results;

    // NOT RENDER IF LIST COUNT ZERO
    if (!(count($this->view->suggestedlist) > 0)) {
      return $this->setNoRender();
    }

    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($list);
  }
}