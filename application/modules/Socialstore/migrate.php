<?php

ini_set('display_startup_errors',0);
ini_set('display_errors',0);
$Products = new Socialstore_Model_DbTable_SocialProducts;
$Categories = new Socialstore_Model_DbTable_Categories;
$Customcategories = new Socialstore_Model_DbTable_Customcategories;
$Stores = new Socialstore_Model_DbTable_SocialStores;
$Vats = new Socialstore_Model_DbTable_Vats;
$Taxes = new Socialstore_Model_DbTable_Taxes;
$store_select = $Stores->select()->where('deleted = ?',0);
$stores = $Stores->fetchAll($store_select);
foreach ($stores as $store) {
	if (is_object($store)) {
		$store->category_id = 1;
		$store->save();
	}
}
$product_select = $Products->select()->where('deleted = ?', 0);
$products = $Products->fetchAll($product_select);
foreach ($products as $product) {
	if (is_object($product)) {
		$old_category_id = $product->category_id;
		$old_category_select = $Categories->select()->where('category_id = ?', $old_category_id);
		$old_category = $Categories->fetchRow($old_category_select);
		$select_category = $Customcategories->select();
		$select_category->where('store_id = ?', $product->store_id)->where('name = ?', $old_category->name);
		$temp_category = $Customcategories->fetchRow($select_category);
		if (!is_object($temp_category)) {
			$new_category = $Customcategories->createRow();
			$new_category->store_id = $product->store_id;
			$new_category->name = $old_category->name;
			$new_category->level = 1;
			$new_category->save();
			$customcategory_id = $new_category->customcategory_id;
		}
		else {
			$customcategory_id = $temp_category->customcategory_id;
		}
		$product->category_id = $customcategory_id;
		$vat_id = $product->tax_id;
		$vat_select = $Vats->select()->where('vat_id = ?', $vat_id);
		$vat = $Vats->fetchRow($vat_select);
		if (is_object($vat)) {
			$tax = $Taxes->createRow();
			$tax->name = $vat->name;
			$tax->value = $vat->value;
			$tax->store_id = $product->store_id;
			$tax->creation_date = date('Y-m-d H:i:s');
			$tax->modified_date = date('Y-m-d H:i:s');
			$tax->save();
			$product->tax_id = $tax->tax_id;
		}
		$product->save();		
	}
}
echo "Migration succeeded!";