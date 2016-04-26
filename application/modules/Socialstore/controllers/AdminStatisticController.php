<?php

class Socialstore_AdminStatisticController extends Core_Controller_Action_Admin{
	
	public function init() {
		Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_statistics');
	}
	public function indexAction(){
		$statistic = Socialstore_Api_Statistic::getInstance();
		$this->view->totalStores = $statistic->getTotalStores();
		$this->view->featuredStores = $statistic->getFeaturedStores();
		$this->view->approvedStores = $statistic->getApprovedStores();
		$this->view->showStores = $statistic->getShowStores();
		$this->view->usersFollow = $statistic->getUsersFollow();
		$this->view->storesFollowed = $statistic->getStoresFollowed();
		$this->view->totalProducts = $statistic->getTotalProducts();
		$this->view->featuredProducts = $statistic->getFeaturedProducts();
		$this->view->approvedProducts = $statistic->getApprovedProducts();
		$this->view->showProducts = $statistic->getShowProducts();
		$this->view->usersFavourite = $statistic->getUsersFavourite();
		$this->view->productsFavourited = $statistic->getProductsFavourited();
		$this->view->soldProducts = $statistic->getTotalSoldProducts();
		$this->view->storesPublishFee = $storesPubFee = $statistic->getStoresPublishFee();
		$this->view->storesFeaturedFee = $storesFeaFee = $statistic->getStoresFeaturedFee();
		$this->view->storesFee = $storesFee = $storesPubFee + $storesFeaFee; 
		$this->view->productsPublishFee = $productsPubFee = $statistic->getProductsPublishFee();
		$this->view->productsFeaturedFee = $productFeaFee = $statistic->getProductsFeaturedFee();
		$this->view->productsFee = $productsFee = $productsPubFee + $productFeaFee;
		$this->view->commission = $commission = $statistic->getCommission();
		$this->view->totalIncome = $storesFee + $productsFee + $commission;
	}
}
