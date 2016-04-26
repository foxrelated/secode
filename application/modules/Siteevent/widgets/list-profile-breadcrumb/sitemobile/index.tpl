<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php 
$subcategory=array();
$subsubcategory=array();
if(!empty($this->siteevent->subcategory_id)) {
	$subcategory = array("href"=>$this->url(array('category_id' => $this->siteevent->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->category_id)->getCategorySlug(), 'subcategory_id' => $this->siteevent->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->subcategory_id)->getCategorySlug()), "siteevent_general_subcategory"),"title"=>$this->translate($this->subcategory_name),"icon"=>"arrow-r");
}
if(!empty($this->siteevent->subsubcategory_id)) {
	$subsubcategory = array("href"=>$this->url(array('category_id' => $this->siteevent->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->category_id)->getCategorySlug(), 'subcategory_id' => $this->siteevent->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->siteevent->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->subsubcategory_id)->getCategorySlug()), "siteevent_general_subsubcategory"),"title"=>$this->translate($this->subsubcategory_name),"icon"=>"arrow-r");
}

$breadcrumb = array(
	array("href"=>$this->url(array('action' => 'home'), 'siteevent_general', true),"title"=>$this->translate("Events Home"),"icon"=>"arrow-r"),
	array("href"=>$this->url(array('category_id' => $this->siteevent->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->category_id)->getCategorySlug()), "siteevent_general_category"),"title"=>$this->translate($this->category_name),"icon"=>"arrow-r"),
	$subcategory,$subsubcategory,
	array("title"=>$this->siteevent->getTitle(),"icon"=>"arrow-d"),
);
echo $this->breadcrumb($breadcrumb);
?>

