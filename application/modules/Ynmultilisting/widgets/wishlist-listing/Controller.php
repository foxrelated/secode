<?php
class Ynmultilisting_Widget_WishlistListingController extends Engine_Content_Widget_Abstract {
 	public function indexAction() {
       	$form = new Ynmultilisting_Form_Wishlist_Search();
        //Setup params
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		$originalOptions = $params;
        if ($form->isValid($params)) {
            $values = $form->getValues();
            $params = array_merge($params, $values);
		}
		        
        if (!isset($params['page']) || $params['page'] == '0') {
            $page = 1;
        }
        else {
            $page = (int)$params['page'];
        }
 		
		$params['listingtype_id'] = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
		$table = Engine_Api::_()->getItemTable('ynmultilisting_wishlist');
       	$select = $table->getWishlistSelect($params);
		$wishlists = $table->fetchAll($select);
		$availableWishlists = array();
		foreach ($wishlists as $wishlist) {
			if ($wishlist->isViewable() && $wishlist->hasListings()) {
				$availableWishlists[] = $wishlist;
			}
		}
        
        $limit = $this->_getParam('itemCountPerPage', 10);
		if (!$limit) $limit = 10;
		
		$this->view->paginator = $paginator = Zend_Paginator::factory($availableWishlists);
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page );
        
        $this->view->formValues = array_filter($originalOptions);
	}
}