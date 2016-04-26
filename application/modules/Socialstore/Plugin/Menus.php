<?php

class Socialstore_Plugin_Menus {

	public function canMyStore() {

		$viewer = Engine_Api::_() -> user() -> getViewer();

		if(!is_object($viewer)) {
			return false;
		}
		// not login user, checked here.
		if(!$viewer -> getIdentity()) {
			return false;
		}

		if(Engine_Api::_() -> authorization() -> isAllowed('social_store', $viewer, 'store_create')) {
			return true;
		}

		// Must availabe if the viewer has already a store.

		
		// check if current use has store.

		return false;
	}
	
	/**
	 * display "My Favourite"
	 */
	public function canMyFavourite() {
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$viewer || !$viewer->getIdentity() ) {
	      return false;
	    }
		return true;
	}

	/**
	 * display "My Following"
	 */
	public function canMyFollowing() {
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$viewer || !$viewer->getIdentity() ) {
	      return false;
	    }
		return true;
	}

	/**
	 * display "Shopping Cart"
	 */
	public function canMyCart() {
		return true;
	}

	/**
	 * display "My Orders"
	 */
	public function canMyOrders() {
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$viewer || !$viewer->getIdentity() ) {
	      return false;
	    }		
		return true;
	}

	/**
	 * display "My Address Book"
	 */
	public function canMyAddressBook() {
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$viewer || !$viewer->getIdentity() ) {
	      return false;
	    }		
		return true;
	}

	/**
	 * display "Faqs"
	 */
	public function canFaqs() {
		return true;
	}


	/**
	 * display "Faqs"
	 */
	public function canHelp() {
		return true;
	}
	
	/**
	 * Display "Store"
	 */
	public function canStore() {
		$viewer = Engine_Api::_()->user()->getViewer();
	if( !Engine_Api::_()->authorization()->isAllowed('social_store', $viewer, 'store_view') ) {
      return false;
    }
		return true;
	}

	/**
	 * Display "Products"
	 */
	public function canProduct() {
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !Engine_Api::_()->authorization()->isAllowed('social_product', $viewer, 'product_view') ) {
     		return false;
    	}
		return true;
	}
	public function canViewGDAs()
    {
        return Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection();
    }
	public function onMenuInitialize_UserProfileSocialstore($row)
	  {
	  	if(!Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection())
			return false;
	    $viewer = Engine_Api::_()->user()->getViewer();
	    $subject = Engine_Api::_()->core()->getSubject();
	    $label = "Deal Requests";
	    if($viewer->isSelf($subject))
	    {  
	         $label = "My Deal Requests";
	    }          
	    return array(
	        'label' => $label,
	        'icon' => 'application/modules/Socialstore/externals/images/gda_icon.png',
	        'route' => 'socialstore_extended',
	        'params' => array(
	          'controller' => 'gda',
	          'action' => 'requests',
	          'userId' => $subject->user_id,
	        )
	      );
	  }
}
