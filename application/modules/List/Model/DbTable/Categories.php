<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Categories.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_DbTable_Categories extends Engine_Db_Table {

  protected $_rowClass = 'List_Model_Category';

  /**
   * Return subcaregories
   *
   * @param int category_id
   * @return all sub categories
   */
  public function getSubCategories($category_id) {

		//RETURN IF CATEGORY ID IS EMPTY
    if (empty($category_id)) {
      return;
    }

		//MAKE QUERY
    $select = $this->select()
									->from($this->info('name'), array('category_name', 'category_id', 'cat_order', 'cat_dependency'))
									->where('cat_dependency = ?', $category_id)
									->order('cat_order');

		//RETURN RESULTS
    return $this->fetchAll($select);
  }

  /**
   * Get category object
   * @param int $category_id : category id
   * @return category object
   */
  public function getCategory($category_id) {

		//RETURN CATEGORY OBJECT
    return $this->find($category_id)->current();
  }

  /**
   * Return categories
   *
   * @param array $category_ids
   * @return all categories
   */
  public function getCategories($category_ids = null) {

		//MAKE QUERY
    $select = $this->select()
										->where('cat_dependency = ?', 0)
										->order('cat_order');

		if(!empty($category_ids)) {
			foreach($category_ids as $ids) {
				$categoryIdsArray[] = "category_id = $ids";
			}
			$select->where("(".join(") or (", $categoryIdsArray).")");
		}

		//RETURN DATA
    return $this->fetchAll($select);
  }
  
  /**
   * Return categories count
   *
   * @param array $category_ids
   * @return all categories
   */
  public function getCategoriesCount() {

		//MAKE QUERY
    return $this->select()
                    ->from($this->info('name'), array('COUNT(category_id) as total_count'))
										->where('cat_dependency = ?', 0)
                    ->where('subcat_dependency = ?', 0)
                    ->query()
                    ->fetchColumn();
  }  


 /**
   * Gets all categories and subcategories
   *
   * @param string $category_id
   * @param string $fieldname
   * @param int $listCondition
   * @param string $list
   * @param  all categories and subcategories
   */

  public function getAllCategories($category_id, $fieldname, $listCondition, $page, $subcat = null, $limit = 0) {
    $tableCategoriesName = $this->info('name');
    $tableListing = Engine_Api::_()->getDbtable('listings', 'list');
    $tableListingName = $tableListing->info('name');
    $select = $this->select()->setIntegrityCheck(false)
            ->from($tableCategoriesName);
    if ($subcat == 1) {
      $select = $select->joinLeft($tableListingName, $tableListingName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', array('count(DISTINCT ' . $tableListingName . '.' . $page . ' ) as count'));
    } else {
      $select = $select->joinLeft($tableListingName, $tableListingName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', array('count(DISTINCT ' . $tableListingName . '.listing_id ) as count'));
      // $select = $select->joinLeft($tablePageName, $tablePageName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', array('count( ' . $tablePageName . '.' . $fieldname . ' ) as count'));
    }

    $select = $select->where($tableCategoriesName . '.cat_dependency = ' . $category_id)
            ->group($tableCategoriesName . '.category_id')
            ->order('cat_order');

    if (!empty($limit)) {
      $select = $select->limit($limit);
    }
    
    if ($listCondition == 1) {
      
    $listApi = Engine_Api::_()->list();
    $expirySettings = $listApi->expirySettings();
     
    if ($expirySettings == 2) {
      $approveDate = $listApi->adminExpiryDuration();
      $select->where($tableListing->info('name') . ".`approved_date` >= ?", $approveDate);
    } elseif ($expirySettings == 1) {
      $current_date = date("Y-m-d i:s:m", time());
      $select->where("(" . $tableListing->info('name') . ".`end_date` IS NULL or " . $tableListing->info('name') . ".`end_date` >= ?)", $current_date);
    }
      $select->where($tableListingName . '.approved = ?', 1)->where($tableListingName . '.draft = ?', 1)->where($tableListingName . '.search = ?', 1);
			$select = $tableListing->getNetworkBaseSql($select, array('not_groupBy' => 1));
    }

    return $this->fetchAll($select);
  }

  /**
   * Return slug corrosponding to category name
   *
   * @param string $categoryname
   * @return categoryname
   */
  public function getCategorySlug($categoryname) {

		if(Engine_Api::_()->getApi('settings', 'core')->getSetting('list.categorywithslug', 1)) {
			return trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($categoryname))), '-');
		}
		else {
			return $categoryname;
		}		    
  }

  /**
   * Return category mapping data
   *
   */
	public function categoryMappingData() {

		//GET PROFILEMAPS TABLE NAME
    $tableProfilemapsName = Engine_Api::_()->getDbtable('profilemaps', 'list')->info('name');

		//GET CATEGORY TABLE NAME
    $tableCategoryName = $this->info('name');

		//GET FIELD OPTION TABLE NAME
		$tableFieldOptionsName = Engine_Api::_()->getDbtable('options', 'list')->info('name');

		//MAKE QUERY
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($tableCategoryName, array('category_id', 'category_name'))
            ->joinLeft($tableProfilemapsName, "$tableCategoryName.category_id = $tableProfilemapsName.category_id", array('profile_type', 'profilemap_id'))
            ->joinLeft($tableFieldOptionsName, "$tableFieldOptionsName.option_id = $tableProfilemapsName.profile_type", array('label'))
            ->where($tableCategoryName . ".cat_dependency = ?", 0);

		//RETURN DATA
    return Zend_Paginator::factory($select);
	}
}
