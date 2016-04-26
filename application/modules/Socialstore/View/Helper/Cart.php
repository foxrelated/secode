<?php

class Socialstore_View_Helper_Cart extends Zend_View_Helper_Abstract{
	
	static private $_viewerId;
	
	public function getViewerId(){
		if(self::$_viewerId === NULL){
			self::$_viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
		}
		return self::$_viewerId;
	}
	
	public function cart($product){
		$xhtml = "";		
		$product_id =  $product->getIdentity();
		$discount_price = $product->getDiscountPrice();
		if ($product->approve_status == 'approved') {
			if ($discount_price == 0) {
				$xhtml = 
				'<div class="pricecart">
					<span class="discount_price">%s</span> 
				</div>';
				if ($product->checkStock()) {
					$xhtml.= '<a class="store_addtocart" href="javascript:en4.store.cart.addProductBox(%s)"><span>%s</span></a>';
					return sprintf($xhtml, $this->view->currency($product->getPretaxPrice()),  $product_id, $this->view->translate('Add to Cart'));
				}
				else {
					$xhtml.= '<div class="store_outofstock"><span class = "store_outofstock_text">%s</span></div>';
					return sprintf($xhtml, $this->view->currency($product->getPretaxPrice()), $this->view->translate('Out of Stock'));
				}
			}
			else {
				$xhtml = 
				'<div class="pricecart">
					<span class="old_price">%s</span>
					<span class="discount_price">%s</span> 
				</div>';
				if ($product->checkStock()) {
					$xhtml.= '<a class="store_addtocart" href="javascript:en4.store.cart.addProductBox(%s)"><span>%s</span></a>';
					return sprintf($xhtml,  $this->view->currency($product->pretax_price), $this->view->currency($discount_price), $product_id, $this->view->translate('Add to Cart'));
				}
				else {
					$xhtml.= '<div class="store_outofstock"><span class = "store_outofstock_text">%s</span></div>';
					return sprintf($xhtml,  $this->view->currency($product->pretax_price),$this->view->currency($discount_price), $this->view->translate('Out of Stock'));
				}
			}
		}
		else {
			if ($discount_price == 0) {
				$xhtml = '<div class="pricecart">
					<span class="discount_price">%s</span> 
				</div>';
				return sprintf($xhtml, $this->view->currency($product->getPretaxPrice()),  $product_id, $this->view->translate('Add to Cart'));
			}
			else {
				$xhtml = '<div class="pricecart">
					<span class="old_price">%s</span>
					<span class="discount_price">%s</span> 
				</div>';
				return sprintf($xhtml, $this->view->currency($product->pretax_price), $this->view->currency($discount_price),   $product_id, $this->view->translate('Add to Cart'));
			} 
		}
		
	}
}
 
							
							
						