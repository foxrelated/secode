<?php

class Socialstore_View_Helper_Favourite extends Zend_View_Helper_Abstract{
	
	static private $_viewerId;
	
	static private $_textUrl;
	
	public function getViewerId(){
		if(self::$_viewerId === NULL){
			self::$_viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
		}
		return self::$_viewerId;
	}
	
	public function getUrlReturn() {
		if (self::$_textUrl === NULL) {
			$textUrl = base64_encode($_SERVER['REQUEST_URI']);
			self::$_textUrl = $textUrl;
		}
		return self::$_textUrl;
	}
	
	public function favourite($product){
		$xhtml = "";		
		$text = $this->getUrlReturn();
		$product_id =  $product->getIdentity();
		if($product->isFavourited($this->getViewerId())){
			$xhtml =  sprintf('<a  href="javascript:void(0);" onclick="en4.store.fav(%s,%s,\'%s\')" class="store_fav_unfavourite store_fav_%s">%s</a>',$product_id, $this->getViewerId(), $text, $product_id , $this->view->translate('Unfavourite'));
		}else{
			$xhtml =  sprintf('<a href="javascript:void(0);" onclick="en4.store.fav(%s,%s,\'%s\')" class="store_fav_favourite store_fav_%s">%s</a>',$product_id, $this->getViewerId(), $text, $product_id , $this->view->translate('Favourite'));
		}
		return $xhtml;
	}
}
