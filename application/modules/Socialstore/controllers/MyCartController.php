<?php

class Socialstore_MyCartController extends Core_Controller_Action_Standard {
	public function init() {
		Zend_Registry::set('active_menu', 'socialstore_main_mycart');

	}

	public function indexAction() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$cart = Socialstore_Api_Cart::getInstance();
		$count = $cart -> countAllQty();
		if ($count < 1) {
			return $this -> _forward('empty-cart');
		}

		try {
			$params = $this -> _getAllParams();
			// View Submit to Check Out

			if (isset($params['checkout_submit'])) {
				$allowGuest = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.guestpurchase', 0);
				if (!$this -> _helper -> requireUser() -> isValid()) {
					if ($allowGuest == 0) {
						return;
					}

				}
				if ($params['total'] == 0) {
					return;
				}
				$viewer = Engine_Api::_() -> user() -> getViewer();
				$allowPurchase = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('social_product', $viewer, 'product_buy');
				if ($allowPurchase == 0 && $allowGuest == 0) {
					return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array($this -> view -> translate('You are not allowed to purchase products!'))));
				}
				$order = $this -> _checkout();
				if (!is_object($order) && $order == 'invalid') {
					return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array($this -> view -> translate('Please remove invalid products before continue!'))));

				}
				$this -> _helper -> redirector -> gotoRoute(array('controller' => 'payment', 'action' => 'process', 'id' => $order -> getId()), 'socialstore_extended');
			}

			// View Submit to Update Cart (change quantity)
			if (isset($params['updatecart_submit'])) {
				$this -> _updateCart();
			}

		} catch(Exception $e) {
			throw $e;
		}
		// view cart and some thing else.
	}

	public function emptyCartAction() {

	}

	public function ajaxAddProductAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$allowPurchase = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('social_product', $viewer, 'product_buy');
		if ($allowPurchase == 0) {
			$this -> view -> error = '1';
			$this -> view -> text = Zend_Registry::get('Zend_Translate') -> _('You are not allowed to purchase products!');
		} else {
			$cart = Socialstore_Api_Cart::getInstance();
			$product_id = $this -> _getParam('product_id');
			$product = Engine_Api::_() -> getItem('social_product', $product_id);
			if ($product -> min_qty_purchase != 0) {
				$product_quantity = $product -> min_qty_purchase;
			} else {
				$product_quantity = '1';
			}
			$cart -> addItem($product_id, $product_quantity);
			$count = $cart -> countAllQty();
			$this -> view -> count = $count;
			$this -> view -> error = 0;
			if ($count == 1) {
				$this -> view -> text = Zend_Registry::get('Zend_Translate') -> _($count . ' Item in Cart');
			} elseif ($count > 1) {
				$this -> view -> text = Zend_Registry::get('Zend_Translate') -> _($count . ' Items in Cart');
			}
		}
	}

	public function addProductAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$id = $this -> _getParam('id');
		$options = $this -> _getParam('option');
		$product = Engine_Api::_() -> getItem('social_product', $id);
		$this -> _helper -> layout -> setLayout('admin-simple');
		if ($product -> product_type == 'default') {
			$type = 0;
			$this -> view -> form = $form = new Socialstore_Form_Quantity( array('product' => $product));
		} elseif ($product -> product_type == 'downloadable') {
			$type = 1;
			$this -> view -> form = $form = new Socialstore_Form_Downloadable();
		}
		$form -> populate(array('option' => $options));
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$values = $form -> getValues();
			if ($type == 0) {
				$current_max = $product -> getCurrentAvailable();
				$min = $product -> min_qty_purchase;
				if ($current_max == 'unlimited') {
					if ($values['quantity'] < $min) {
						return $form -> addError('Invalid Quantity Number. Minimum quantity allowed: ' . $min);
					}
				} elseif ($min <= $current_max && $current_max != 0) {
					if ($values['quantity'] < $min || $values['quantity'] > $current_max) {
						$error = $this -> view -> translate('Invalid Quantity Number. Minimum and maximum quantity allowed for purchase are %1$s and %2$s', $product -> min_qty_purchase, $current_max);
						return $form -> addError($error);
					}
				} else {
					return $form -> addError('Maximum available unit reached. You cannot purchase this product anymore!');
				}
			} else {
				$values['quantity'] = 1;
			}
			if (@$values['option'] && $values['option'] != '') {
				$options = $values['option'];
			} else {
				$options = '';
				$defaultType = $product -> getOptions();
				$i = 0;
				if (count($defaultType) > 0) {
					foreach ($defaultType as $key => $type) {
						$default_option = Engine_Api::_() -> getApi('attribute', 'socialstore') -> getDefaultOption($product -> product_id, $key);
						if ($default_option != null) {
							if ($i == 0)
								$options .= $default_option -> option_id;
						} else {
							$options .= '-' . $default_option;
						}
					}
				}
			}
			$cart = Socialstore_Api_Cart::getInstance();
			if ($options != '') {
				$ProductOptions = new Socialstore_Model_DbTable_Productoptions;
				$productoption = $ProductOptions -> createRow();
				$productoption -> product_id = $product -> product_id;
				$productoption -> options = $options;
				$productoption -> save();
				$productoption_id = $productoption -> productoption_id;
			} else {
				$productoption_id = '';
			}
			$cart -> addItem($id, $values['quantity'], false, $productoption_id);
			$array = $this -> _getAllParams();
			if (isset($array['submit'])) {
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
			} elseif (isset($array['checkout'])) {
				$router = $this -> getFrontController() -> getRouter();
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRedirect' => $router -> assemble(array('module' => 'socialstore', 'controller' => 'my-cart', 'action' => 'index'), 'default', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Add to cart successfully!')), ));
			}
		}
	}

	public function checkQuantityAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$data = array();
		$product_id = $this -> _getParam('product_id', 0);
		$product = Engine_Api::_() -> getItem('social_product', $product_id);
		$quantity = $this -> _getParam('quantity');

		if ($product && $product -> product_type == 'default') {
			$cart = Socialstore_Api_Cart::getInstance();
			$cart_items = $cart -> getCartItems();
			foreach ($cart_items as $item) {
				$product_cur = $item -> getObject();
				if (!is_object($product_cur)) {
					continue;
				}
				if ($product_cur -> getIdentity() == $product_id) {
					$product -> setQuantity($item -> getItemQuantity());
					$product -> setOptions($item -> options);
					break;
				}
			}

			$current_max = $product -> getCurrentAvailable();
			$min = $product -> min_qty_purchase;
			if ($current_max == 'unlimited') {
				if ($quantity < $min) {
					$data['status'] = false;
					$data['message'] = $this -> view -> translate('The quantity must be greater than %s', $min);
					$data['quantity'] = $product -> getQuantity();
					$data['total'] = $this -> view -> currency($product -> getTotalAmount());
					echo json_encode($data);
					return;
				}
			} elseif ($min <= $current_max && $current_max != 0) {
				if ($quantity < $min) {
					$data['status'] = false;
					$data['message'] = $this -> view -> translate('The quantity must be equal or greater than %s', $min);
					$data['quantity'] = $product -> getQuantity();
					$data['total'] = $this -> view -> currency($product -> getTotalAmount());
					echo json_encode($data);
					return;
				} elseif ($quantity > $current_max) {
					$data['status'] = false;
					$data['message'] = $this -> view -> translate('The quantity must be equal or less than %s', $current_max);
					$data['quantity'] = $product -> getQuantity();
					$data['total'] = $this -> view -> currency($product -> getTotalAmount());
					echo json_encode($data);
					return;
				}
			}

			$cart -> addItem($product_id, $quantity, true, $this -> _getParam('option'));
			$cart -> refresh();
			$data['status'] = true;
			$data['quantity'] = $quantity;
			$data['total'] = $this -> view -> currency($product -> getPrice() * $quantity);
		} else {
			$data['status'] = false;
			$data['message'] = $this -> view -> translate('Product can not be found!');
		}

		echo json_encode($data);
		return;
	}

	protected function _updateCart() {
		$params = $this -> _getParam('cartitem_qty');
		$cart = Socialstore_Api_Cart::getInstance();

		foreach ($params as $key => $product_quantity) {
			$key = explode('_', $key);
			$product_id = $key[0];
			$product = Engine_Api::_() -> getItem('social_product', $product_id);
			if ($product -> product_type == 'default') {
				$current_max = $product -> getCurrentAvailable();
				$min = $product -> min_qty_purchase;
				if ($current_max == 'unlimited') {
					if ($product_quantity['qty'] < $min) {
						$product_quantity['qty'] = $min;
					}
				} elseif ($min <= $current_max && $current_max != 0) {
					if ($product_quantity['qty'] < $min) {
						$product_quantity['qty'] = $min;
					} elseif ($product_quantity['qty'] > $current_max) {
						$product_quantity['qty'] = $current_max;
					}
				}
			}
			$cart -> addItem($product_id, $product_quantity['qty'], true, $product_quantity['options']);

		}
		$cart -> refresh();
		return;
	}

	protected function _checkout() {
		$cartitem_check = $this -> _getParam('cartitem_check');
		$cartitem_qty = $this -> _getParam('cartitem_qty');
		$cartitems = array();

		foreach ($cartitem_check as $item_id => $item_amount) {
			$value = (int)$cartitem_qty[$item_id]['qty'];
			if ($value < 1) {
				throw new Exception("Invalid Quantity");
			}
			$cartitems[$item_id]['qty'] = $value;
			$cartitems[$item_id]['options'] = $cartitem_qty[$item_id]['options'];
		}
		return Socialstore_Api_Cart::getInstance() -> makeOrder($cartitems);
	}

	public function removeItemAction() {
		$id = $this -> _getParam('cartitem-id');
		$cart = Socialstore_Api_Cart::getInstance() -> removeCartItem($id);
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRedirect' => $this -> getFrontController() -> getRouter() -> assemble(array('module' => 'socialstore', 'controller' => 'my-cart', 'action' => 'index'), 'default', true), 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Remove item successfully.'))));
	}
}
