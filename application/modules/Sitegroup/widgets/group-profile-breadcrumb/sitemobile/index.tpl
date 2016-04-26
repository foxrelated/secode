<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
$subcategory=array();
$subsubcategory=array();
if(!empty($this->sitegroup->subcategory_id) && $this->subcategory_name) {
$subcategory = array("href"=>$this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getItem('sitegroup_category', $this->sitegroup->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitegroup->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitegroup_category', $this->sitegroup->subcategory_id)->getCategorySlug()), "sitegroup_general_subcategory") ,"title"=>$this->translate($this->subcategory_name),"icon"=>"arrow-r");
}
if(!empty($this->sitegroup->subsubcategory_id) && $this->subsubcategory_name) {
$subsubcategory = array("href"=>$this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getItem('sitegroup_category', $this->sitegroup->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitegroup->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitegroup_category', $this->sitegroup->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->sitegroup->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('sitegroup_category', $this->sitegroup->subsubcategory_id)->getCategorySlug()), "sitegroup_general_subsubcategory"),"title"=>$this->translate($this->subsubcategory_name),"icon"=>"arrow-r");
}

$breadcrumb = array(
    array("href"=>$this->url(array(),'sitegroup_general', false),"title"=>"Groups Home","icon"=>"arrow-r"),
    array("href"=>$this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getItem('sitegroup_category', $this->sitegroup->category_id)->getCategorySlug()), "sitegroup_general_category"),"title"=>$this->translate($this->category_name),"icon"=>"arrow-r"),
    $subcategory,$subsubcategory,
		array("title"=>$this->sitegroup->getTitle(),"icon"=>"arrow-d"),
     );

echo $this->breadcrumb($breadcrumb);
?>