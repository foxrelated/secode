<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Category.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_Category extends Core_Model_Item_Abstract {

    protected $_searchTriggers = false;

    public function getTitle($inflect = false) {
        if ($inflect) {
            return ucwords($this->category_name);
        } else {
            return $this->category_name;
        }
    }

    public function getHref($params = array()) {

        if ($this->subcat_dependency) {
            $type = 'subsubcategory';
            $params['subsubcategory_id'] = $this->category_id;
            $params['subsubcategoryname'] = $this->getCategorySlug();
            $cat = $this->getTable()->getCategory($this->cat_dependency);
            $params['subcategory_id'] = $cat->category_id;
            $params['subcategoryname'] = $cat->getCategorySlug();
            $cat = $this->getTable()->getCategory( $cat->cat_dependency);
            $params['category_id'] = $cat->category_id;
            $params['categoryname'] = $cat->getCategorySlug();
        } else if ($this->cat_dependency) {
            $type = 'subcategory';
            $params['subcategory_id'] = $this->category_id;
            $params['subcategoryname'] = $this->getCategorySlug();
            $cat = $this->getTable()->getCategory($this->cat_dependency);
            $params['category_id'] = $cat->category_id;
            $params['categoryname'] = $cat->getCategorySlug();
        } else {
            $type = 'category';
            $params['category_id'] = $this->category_id;
            $params['categoryname'] = $this->getCategorySlug();
        }

        $route = "siteevent_general_$type";
        if ($type == 'category' && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $route = Engine_Api::_()->siteevent()->getCategoryHomeRoute();
        }

        $params = array_merge(array(
            'route' => $route,
            'reset' => true,
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    /**
     * Return slug corrosponding to category name
     *
     * @return categoryname
     */
    public function getCategorySlug() {

        if (!empty($this->category_slug)) {
            $slug = $this->category_slug;
        } else {
            $slug = Engine_Api::_()->seaocore()->getSlug($this->category_name, 225);
        }

        return $slug;
    }

    /**
     * Set category icon
     *
     */
    public function setPhoto($photo) {

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            return;
        }

        if (empty($file))
            return;

        //GET PHOTO DETAILS
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $mainName = $path . '/' . $name;

        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $photo_params = array(
            'parent_id' => $this->category_id,
            'parent_type' => "siteevent_category",
        );

        //RESIZE IMAGE WORK
        $image = Engine_Image::factory();
        $image->open($file);
        $image->open($file)
                ->resize(300, 500)
                ->write($mainName)
                ->destroy();

        try {
            $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
        } catch (Exception $e) {
            if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE) {
                echo $e->getMessage();
                exit();
            }
        }

        return $photoFile;
    }

    public function hasChild() {
        $table = $this->getTable();
        //RETURN RESULTS
        return $table->select()
                        ->from($table, new Zend_Db_Expr('COUNT(cat_dependency)'))
                        ->where('cat_dependency = ?', $this->category_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
    }

    protected function _delete() {

        $ratingParamsTable = Engine_Api::_()->getDbtable('ratingparams', 'siteevent');
        $select = $ratingParamsTable->select()
                ->from($ratingParamsTable->info('name'), 'ratingparam_id')
                ->where('category_id = ?', $this->category_id)
                ->where('resource_type = ?', 'siteevent_event');

        $ratingParams = $ratingParamsTable->fetchAll($select);
        foreach ($ratingParams as $ratingParam) {
            Engine_Api::_()->getItem('siteevent_ratingparam', $ratingParam->ratingparam_id)->delete();
        }

        //FIRST SAVE PAGE ID'S CORROSPONDING TO CATEGORY ID FOR UPDATION AFTER DELETE FROM RATING TABLE
        $tableRating = Engine_Api::_()->getDbtable('ratings', 'siteevent');

        $tableRating->delete(array('ratingparam_id != ?' => 0, 'category_id = ?' => $this->category_id, 'resource_type =?' => 'siteevent_event'));

        $tableRating->update(array('category_id' => 0), array('category_id = ?' => $this->category_id, 'resource_type =?' => 'siteevent_event'));

        $this->categoryWidgetizedPagesDelete($this->category_id);
  
        parent::_delete();
    }

  public function categoryWidgetizedPagesDelete($categoryId = 0) {

    if(!$categoryId)
     return false;

    //GET PAGE TABLE
    $pageTable = Engine_Api::_()->getDbTable('pages', 'core');
    $pageTableName = $pageTable->info('name');

    //DELETE CATEGORY PAGE
    $page_id = $pageTable->select()
            ->from($pageTableName, 'page_id')
            ->where('name = ?', "siteevent_index_categories-home_category_" . $categoryId)
            ->query()
            ->fetchColumn();
    if (!empty($page_id)) {
      Engine_Api::_()->getDbTable('content', 'core')->delete(array('page_id = ?' => $page_id));
      $pageTable->delete(array('page_id = ?' => $page_id));
    }
  }  

}