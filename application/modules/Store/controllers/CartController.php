<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: CartController.php 4/25/12 6:19 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
//class Store_CartController extends Store_Controller_Action_User
class Store_CartController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->_helper->requireAuth()->setAuthParams('store_product', $viewer, 'order')->isValid();
  }

  public function indexAction()
  {
    /**
     * @var $table  Store_Model_DbTable_Carts
     * @var $viewer User_Model_User
     * @var $cart   Store_Model_Cart
     */
    $this->view->page = $page = $this->_getParam('page', 1);
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getItemTable('store_cart');

    $this->view->via_credits = $via_credits = $this->_getParam('via_credits', 0);

    if (null == ($cart = $table->getCart($viewer->getIdentity())) || !$cart->hasItem() || (!$cart->user_id && !$cart->token)) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'products'), 'store_general', true);
    }

    if ($cart->isPublic() && !$cart->hasPublicDetails($viewer)) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'store', 'controller' => 'cart', 'action' => 'details'), 'default', true);
    }

    // Clear transaction session
    $session = new Zend_Session_Namespace('Store_Transaction');
    $session->unsetAll();

    /**
     * @var $paginator    Zend_Paginator
     * @var $detailsTbl   Store_Model_DbTable_Details
     * @var $locationsTbl Store_Model_DbTable_Locations
     */
    $paginator = Zend_Paginator::factory($cart->getItems());
    $paginator->setItemCountPerPage(6);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->bIds = $cart->getCartProductsInBundlesIds();
    $this->view->paginator = $paginator;

    // Shipping Details
    $detailsTbl = Engine_Api::_()->getDbTable('details', 'store');
    $locationsTbl = Engine_Api::_()->getDbTable('locations', 'store');
    $this->view->details = $details = $detailsTbl->getDetails($viewer);
    if ($details['c_location']) {
      $this->view->country = $details['c_country'];
      $this->view->region = $details['c_state'];
    } else {
      $this->view->country = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_1']));
      $this->view->region = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_2']));
    }

    if ($this->_helper->contextSwitch->getCurrentContext() == 'html') {
      $this->view->justHtml = true;
      return;
    }

    /**
     * Get totals
     * @var $item Store_Model_Cartitem
     * @var $api Store_Api_Core
     */
    $this->view->offers = $this->getOffers($cart, $via_credits);

    $cartParams = $cart->getCartParams($via_credits);
    $totalTaxAmt = $cartParams['totalTaxAmt'];
    $totalItemAmt = $cartParams['totalItemAmt'];
    $totalShippingAmt = $cartParams['totalShippingAmt'];

    //$totalShippingAmt = $cart->getTotalShippingAmount($via_credits);
    $this->view->cart = $cart;
    $this->view->totalPrice = $totalItemAmt;
    $this->view->shippingPrice = $totalShippingAmt;
    $this->view->taxesPrice = $totalTaxAmt;

    // Enabled Gateways
    $this->view->api = $api = Engine_Api::_()->store();
    $mode = $api->getPaymentMode();
    if ($mode == 'client_store') {
      $gateways = Engine_Api::_()->getDbTable('gateways', 'store')->fetchAll(array('title = ?' => 'PayPal'));
    } else {
      $gateways = Engine_Api::_()->getDbTable('gateways', 'store')->getEnabledGateways();
    }
    $this->view->gateways = $gateways;
  }

  public function priceAction()
  {
    /**
     * @var $table  Store_Model_DbTable_Carts
     * @var $viewer User_Model_User
     * @var $cart   Store_Model_Cart
     */

    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getItemTable('store_cart');

    $this->view->via_credits = $via_credits = $this->_getParam('via_credits', 0);
    $this->view->offer_id = $offer_id = (int)$this->_getParam('offer_id', 0);

    $cart = $table->getCart($viewer->getIdentity());

    /**
     * Get totals
     * @var $item Store_Model_Cartitem
     */
    $totalItemAmt = 0;
    $totalShippingAmt = 0;
    $totalTaxAmt = 0;
    $bIds = $cart->getCartProductsInBundlesIds();
    if ($cart) {
      /**
       * @var $offer Offers_Model_Offer
       * @var $products Offers_Model_DbTable_Products
       */
      $isOffersEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('offers');
      if ($isOffersEnabled && $offer_id) {
        $productsIds = array();
        $ids = array();
        $offer = Engine_Api::_()->getItem('offer', $offer_id);
        $products = $offer->getProductsToArray();

        foreach ($cart->getPurchasableItems() as $item) {
          if ($via_credits && !$item->isStoreCredit()) {
            continue;
          }
          foreach ($products as $index => $product) {
            if ($product->product_id == $item->product_id) {
              $ids[] = $product->product_id;
              unset($products[$index]);
              unset($product);
            }
          }
          if (!count($products)) {
            $productsIds[$offer_id] = $ids;
          }
        }
      }

      $location_id = (int)$cart->getShippingLocationId();

      foreach ($cart->getPurchasableItems() as $item) {
        if ($via_credits && !$item->isStoreCredit()) {
          continue;
        }
        if ($isOffersEnabled && $offer_id && isset($productsIds[$offer_id]) && in_array($item->product_id, $productsIds[$offer_id])) {
          $totalItemAmt += $offer->getDiscountPrice($item->getPrice(in_array($item->product_id, $bIds))) + $item->getPrice(in_array($item->product_id, $bIds)) * ($item->qty - 1);
        } else {
          $totalItemAmt += $item->getPrice(in_array($item->product_id, $bIds)) * $item->qty;
        }
        //$totalShippingAmt += $item->getShippingPrice($location_id) * $item->qty;
        //$totalTaxAmt += $item->getTax() * $item->qty;
      }
    }
    //$totalShippingAmt = $cart->getTotalShippingAmount($via_credits);
    $cartParams = $cart->getCartParams($via_credits);
    $totalTaxAmt = $cartParams['totalTaxAmt'];
    //$totalItemAmt = $cartParams['totalItemAmt'];
    $totalShippingAmt = $cartParams['totalShippingAmt'];

    // Enabled Gateways
    $this->view->api = $api = Engine_Api::_()->store();
    $mode = $api->getPaymentMode();
    if ($mode == 'client_store') {
      $gateways = Engine_Api::_()->getDbTable('gateways', 'store')->fetchAll(array('title = ?' => 'PayPal'));
    } else {
      $gateways = Engine_Api::_()->getDbTable('gateways', 'store')->getEnabledGateways();
    }
    $this->view->gateways = $gateways;

    $this->view->html = $this->view->partial('cart/_checkout.tpl', array(
      'totalPrice' => $totalItemAmt,
      'shippingPrice' => $totalShippingAmt,
      'taxesPrice' => $totalTaxAmt,
      'gateways' => $gateways,
      'api' => $api,
      'via_credits' => $via_credits,
      'offers' => $this->getOffers($cart, $via_credits),
      'offer_id' => $offer_id
    ));
  }

  public function orderAction()
  {
    /**
     * @var $viewer User_Model_User
     * @var $settings   Core_Model_DbTable_Settings
     */
    $cart_id = (int)$this->_getParam('cart');
    $gateway_id = (int)$this->_getParam('gateway_id');
    $offer_id = (int)$this->_getParam('offer_id', 0);
    $viewer = Engine_Api::_()->user()->getViewer();

    if (
      !$cart_id ||
      !Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('store_product', $viewer, 'order') ||
      !(Engine_Api::_()->getDbTable('gateways', 'store')->isGatewayEnabled($gateway_id))
    ) {
      return;
    }
    $gateway = Engine_Api::_()->getDbTable('gateways', 'store')->getGateway($gateway_id);
    $via_credits = ($gateway->title == 'Credit') ? true : false;

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $currency = $settings->getSetting('payment.currency', 'USD');

    /**
     * @var $cartTb Store_Model_DbTable_Carts
     */
    $cartTb = Engine_Api::_()->getItemTable('store_cart');
    $select = $cartTb->select()
      ->where('cart_id = ?', $cart_id)
      ->where('user_id = ?', $viewer->getIdentity())
      ->where('active = ?', 1)
      ->limit(1);

    /**
     * Get cart
     *
     * @var $cart Store_Model_Cart
     */
    if (null == ($cart = $cartTb->fetchRow($select))) {
      return;
    }

    //Get all purchasable items
    $cartItems = $cart->getPurchasableItems();
    if ($cartItems->count() <= 0) {
      return;
    }

    $duplicates = array();

    foreach ($cartItems as $cI) {
      if (!isset($duplicates[$cI->product_id])) {
        $duplicates[$cI->product_id] = array('count' => 0, 'items' => array());
      }
      $prod = $cI->getProduct();
      $duplicates[$cI->product_id]['count'] += 1;
      $duplicates[$cI->product_id]['product'] = $prod;//->toArray();
      $duplicates[$cI->product_id]['items'][] = $cI;//->toArray();
    }

    $overflowProducts = array();
    foreach ($duplicates as $k => $v) {
      if ($v['count'] <= 1) {
        unset($duplicates[$k]);
        continue;
      }

      $qty = 0;
      $tmpItems = array();
      foreach ($v['items'] as $cItem) {
        $qty += $cItem->qty;
        $tmpItems[] = $cItem->getIdentity();
      }
      if ($v['product']->quantity < $qty) {
        $overflowProducts = array_merge($overflowProducts, $tmpItems);
      }
    }
    if(count($overflowProducts)) {
      $this->view->errorItems = ($overflowProducts);
      $this->view->errorMessage = $this->view->translate('STORE_Quantity overflow for marked items');
      $this->view->status = 0;
      $this->view->code = 1;
      return;
    }
    /**
     * @var $offer Offers_Model_Offer
     * @var $products Offers_Model_DbTable_Products
     */
    $isOffersEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('offers');
    if ($isOffersEnabled && $offer_id) {
      $productsIds = array();
      $ids = array();
      $offer = Engine_Api::_()->getItem('offer', $offer_id);
      $products = $offer->getProductsToArray();

      foreach ($cart->getPurchasableItems() as $item) {
        if ($via_credits && !$item->isStoreCredit()) {
          continue;
        }
        foreach ($products as $index => $product) {
          if ($product->product_id == $item->product_id) {
            $ids[] = $product->product_id;
            unset($products[$index]);
            unset($product);
          }
        }
        if (!count($products)) {
          $productsIds[$offer_id] = $ids;
        }
      }
    }

    /**
     * Get all totals in a single loop
     *
     * @var $api  Store_Api_Core
     * @var $item Store_Model_Cartitem
     */
    $api = Engine_Api::_()->store();
    $shippingLocationId = $cart->getShippingLocationId();

    $totalItemAmt = 0;
    $totalTaxAmt = 0;
    $totalShippingAmt = 0;
    $totalCommissionAmt = 0;
    $bIds = $cart->getCartProductsInBundlesIds();
    foreach ($cartItems as $item) {
      if ($via_credits && !$item->isStoreCredit()) {
        continue;
      }



      if ($isOffersEnabled && $offer_id && isset($productsIds[$offer_id]) && in_array($item->product_id, $productsIds[$offer_id])) {
        $totalItemAmt += (double)($offer->getDiscountPrice($item->getPrice($item->product_id, $bIds)) +
          $item->getPrice($item->product_id, $bIds) * ($item->qty - 1));
      } else {
        $totalItemAmt += (double)($item->getPrice($item->product_id, $bIds) * $item->qty);
      }
      $totalCommissionAmt += (double)($api->getCommission($item->getPrice($item->product_id, $bIds)) * $item->qty);
      //$totalShippingAmt += (double)($item->getShippingPrice($shippingLocationId) * $item->qty);
      //$totalTaxAmt += (double)($item->getTax() * $item->qty);
    }
    //$totalShippingAmt = $cart->getTotalShippingAmount($via_credits);
    $cartParams = $cart->getCartParams($via_credits);
    $totalTaxAmt = $cartParams['totalTaxAmt'];
    //$totalItemAmt = $cartParams['totalItemAmt'];
    $totalShippingAmt = $cartParams['totalShippingAmt'];

    $totalAmt = $totalItemAmt + $totalTaxAmt + $totalShippingAmt;

    if ($totalAmt <= 0) {
      return;
    }

    /**
     * @var $table      Store_Model_DbTable_Orders
     * @var $itemsTable Store_Model_DbTable_Orderitems
     */
    $table = Engine_Api::_()->getDbTable('orders', 'store');
    $itemsTable = Engine_Api::_()->getDbTable('orderitems', 'store');

    $shippingDetails = $cart->getShippingDetails();

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      if (
        $cart->order_id == null ||
        null == ($order = Engine_Api::_()->getItem('store_order', $cart->order_id))
      ) {
        $data = array(
          'user_id' => $viewer->getIdentity(),
          'gateway_id' => $gateway_id,
          'item_type' => $cart->getType(),
          'item_id' => $cart->getIdentity(),
          'item_amt' => $totalItemAmt,
          'tax_amt' => $totalTaxAmt,
          'shipping_amt' => $totalShippingAmt,
          'total_amt' => $totalAmt,
          'commission_amt' => $totalCommissionAmt,
          'currency' => $currency,
          'shipping_details' => $shippingDetails,
          'via_credits' => $via_credits,
          'offer_id' => $offer_id,
          'token' => $cart->token
        );
        /**
         * @var $order Store_Model_Order
         */
        $order = $table->createRow();
        $order->setFromArray($data);
        $order->save();

        $cart->order_id = $order->getIdentity();
        $cart->save();
      } else {
        $order->gateway_id = $gateway_id;
        $order->status = 'initial';
        $order->item_amt = $totalItemAmt;
        $order->tax_amt = $totalTaxAmt;
        $order->shipping_amt = $totalShippingAmt;
        $order->total_amt = $totalAmt;
        $order->commission_amt = $totalCommissionAmt;
        $order->currency = $currency;
        $order->shipping_details = $shippingDetails;
        $order->via_credits = $via_credits;
        $order->offer_id = $offer_id;
        $order->updateUkey();
      }

      $cartItems = $cart->getPurchasableItems();

      /**
       * he@todo Should we clean all the old items?
       *
       * @var $cartItem Store_Model_Cartitem
       * @var $product  Store_Model_Product
       */
      $itemsTable->delete(array('order_id = ?' => $order->getIdentity()));
      $createdItems = array();
      $errorItems = array();
      foreach ($cartItems as $cartItem) {
        if ($via_credits && !$cartItem->isStoreCredit()) {
          continue;
        }
        try {
          $itemAmt = 0;
          $offerQuantity = 0;
          $product = $cartItem->getProduct();
          $commissionAmt = (double)$api->getCommission($cartItem->getPrice($cartItem->product_id, $bIds));
          $shippingAmt = (double)$product->getShippingPrice($shippingLocationId);
          $taxAmt = (double)$product->getTax();

          if ($isOffersEnabled && $offer_id && isset($productsIds[$offer_id]) && in_array($cartItem->product_id, $productsIds[$offer_id])) {
            $itemAmt += (double)($offer->getDiscountPrice($cartItem->getPrice($cartItem->product_id, $bIds)));
            $totalAmt = (double)($itemAmt + $shippingAmt + $taxAmt);
            $data = array(
              'page_id' => $product->page_id,
              'order_id' => $order->getIdentity(),
              'item_id' => $product->getIdentity(),
              'item_type' => $product->getType(),
              'name' => $product->getTitle(),
              'params' => $cartItem->params,
              'qty' => 1,
              'item_amt' => $itemAmt,
              'tax_amt' => $taxAmt,
              'shipping_amt' => $shippingAmt,
              'commission_amt' => $commissionAmt,
              'total_amt' => $totalAmt,
              'currency' => $currency,
              'via_credits' => $via_credits
            );

            $createdItems[] = $itemsTable->insert($data);
            $offerQuantity = 1;
          }

          if ($cartItem->qty - $offerQuantity) {
            $itemAmt = (double)$cartItem->getPrice($cartItem->product_id, $bIds);
            $totalAmt = (double)($itemAmt + $shippingAmt + $taxAmt);

            $data = array(
              'page_id' => $product->page_id,
              'order_id' => $order->getIdentity(),
              'item_id' => $product->getIdentity(),
              'item_type' => $product->getType(),
              'name' => $product->getTitle(),
              'params' => $cartItem->params,
              'qty' => $cartItem->qty - $offerQuantity,
              'item_amt' => $itemAmt,
              'tax_amt' => $taxAmt,
              'shipping_amt' => $shippingAmt,
              'commission_amt' => $commissionAmt,
              'total_amt' => $totalAmt,
              'currency' => $currency,
              'via_credits' => $via_credits
            );

            $createdItems[] = $itemsTable->insert($data);
          }

          //Count totals
        } catch (Exception $e) {
          print_firebug($e->__toString());
          $errorItems[] = $cartItem->getIdentity();
          continue;
        }
      }

      // Commit
      $db->commit();

    } catch (Exception $e) {
      $db->rollBack();
      print_firebug($e);

      $this->view->status = 0;
      $this->view->errorMessage = Zend_Registry::get('Zend_Translate')
        ->translate('STORE_An error has occurred while creating your order.'
          . ' Please, try again with another gateway.');
      return;
    }

    //he@todo Should I inform a purchaser about these items?
    $this->view->createdItems = $createdItems;
    $this->view->errorItems = $errorItems;

    $this->view->status = 1;
    $this->view->link = $this->view->url(array('order_id' => $order->ukey), 'store_transaction_profile', true);
  }

  public function addAction()
  {
    $subject = null;

    if (!Engine_Api::_()->core()->hasSubject()) {
      $id = $this->_getParam('product_id');
      if (null !== $id) {
        $subject = Engine_Api::_()->getItem('store_product', $id);
        if ($subject->getStore()) {
          $approved = $subject->getStore()->approved;
        } else {
          $approved = 1;
        }

        if ($subject && $subject->getIdentity() && $approved) {
          Engine_Api::_()->core()->setSubject($subject);
        } else {
          if ($this->_getParam('format') == 'json') {
            $this->view->status = 0;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product doesn\'t exist');
            return;
          }
          $this->_redirectCustom(
            $this->view->url(
              array(
                'action' => 'index'
              ), 'store_general', true
            )
          );
        }
      }
    }

    $this->_helper->requireSubject('store_product');
    /**
     * @var $product    Store_Model_Product
     * @var $viewer     User_Model_User
     * @var $cartTb     Store_Model_DbTable_Carts
     * @var $table Store_Model_DbTable_Cartitems
     * @var $cart       Store_Model_Cart
     */
    $product = Engine_Api::_()->core()->getSubject('store_product');
    $viewer = Engine_Api::_()->user()->getViewer();

    $params = array();
    $cartTb = Engine_Api::_()->getItemTable('store_cart');
    $table = Engine_Api::_()->getDbTable('cartitems', 'store');

    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $cart = $cartTb->getCart($viewer->getIdentity());

    $productParams = $this->_getParam('params', array());

    if ($cart && $cart->getRowByProduct($product->getIdentity(), $productParams)) {
      $this->view->status = false;
      $this->view->message = $this->view->translate('STORE_This product already added to your cart.');
      return;
    }

    /**
     * @var $settings Core_Model_DbTable_Settings
     */
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    if ($product->getPrice() < $settings->getSetting('store.minimum.price', 0.15)) {
      if ($product->type == 'simple') {
        $this->view->status = false;
        $this->view->message = $this->view->translate('STORE_This product cannot be added to your cart. '
          . 'The price of the product lower than allowed Minimum Price.');
        return;
      }
    }

    if ($product->type == 'simple' && is_array($product->params) && count($product->params) > 0) {
      $params = $this->_getParam('params', array());

      if (count($params) <= 0) {
        $this->view->message = $this->view->translate('STORE_Additional parameters are required!');
        return;
      }

      $flag = true;
      foreach ($product->params as $key => $value) {
        if ($value['label'] != $params[$key]['label'] || !in_array($params[$key]['value'], explode(',', $value['options']))) {
          $flag = false;
        }
      }

      if (!$flag) {
        $this->view->message = $this->view->translate('STORE_Please, check the additional parameters carefully! Wrong parameters have been assigned.');
        return;
      }
    }

    $quantity = $this->_getParam('quantity', 1);
    if ($quantity > $product->quantity) {
      $quantity = $product->quantity;
    }

    // Process
    $db = Engine_Api::_()->getItemTable('store_product')->getAdapter();
    $db->beginTransaction();

    try {
      if (null == $cart) {
        $data = array(
          'user_id' => $viewer->getIdentity()
        );

        $cart = $cartTb->createRow($data);
        $cart->save();
      }

      $data = array(
        'cart_id' => $cart->getIdentity(),
        'product_id' => $product->getIdentity(),
        'price' => $product->price,
        'title' => $product->getTitle(),
        'qty' => $quantity,
        'params' => $params,
        'bundle_id' => $product->getBundleId()
      );

      $item = $table->createRow($data);
      $this->view->item_id = $item->save();

      // Commit
      $db->commit();
    } catch (Engine_Image_Exception $e) {
      $db->rollBack();
    }

    if (!($item instanceof Store_Model_Cartitem)) {
      $this->view->status = false;
      return;
    }

    $items = $table->fetchAll($table
        ->select()
        ->where('cart_id = ?', $cart->getIdentity())
        ->order('cartitem_id DESC')
    );

    $this->view->status = true;
    $this->view->totalCount = $cart->getItemCount();
    $this->view->totalPrice = @$this->view->locale()->toCurrency($cart->getPrice(), $currency);
    $this->view->html = $this->view->partial(
      'cart/_product.tpl',
      'store',
      array(
        'viewer' => $viewer,
        'item' => $item,
        'items' => $items,
      )
    );
  }

  public function addBundleAction()
  {
    $ids = $this->_getParam('ids');
    if(!$this->getRequest()->isPost() || !$ids || strlen(trim($ids)) <= 0 ) {
      if ($this->_getParam('format') == 'json') {
        $this->view->status = 0;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product doesn\'t exist');
        return;
      }
      $this->_redirectCustom(
        $this->view->url(
          array(
            'action' => 'index'
          ), 'store_general', true
        )
      );
    }

    /**
     * @var $viewer     User_Model_User
     * @var $prodsTbl     Store_Model_DbTable_Products
     * @var $cartTb     Store_Model_DbTable_Carts
     * @var $table Store_Model_DbTable_Cartitems
     * @var $cart       Store_Model_Cart
     * @var $settings Core_Model_DbTable_Settings
     */

    $ids = explode(',', $ids);
    $prodsTbl = Engine_Api::_()->getItemTable('store_product');
    $select = $prodsTbl->getSelect(array('ids' => $ids));
    $products = $prodsTbl->fetchAll($select);

    $viewer = Engine_Api::_()->user()->getViewer();

    $params = array();
    $cartTb = Engine_Api::_()->getItemTable('store_cart');
    $table = Engine_Api::_()->getDbTable('cartitems', 'store');

    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $cart = $cartTb->getCart($viewer->getIdentity());

    $messages = array();

    foreach($products as $product) {
      if ($cart && $cart->getRowByProduct($product->getIdentity(), array())) {
        $messages[] = array(
          'title' => $product->getTitle(),
          'message' => $this->view->translate('STORE_This product already added to your cart.')
        );
        continue;
      }

      $settings = Engine_Api::_()->getDbTable('settings', 'core');
      if ($product->getPrice() < $settings->getSetting('store.minimum.price', 0.15)) {
        if ($product->type == 'simple') {
          $messages[] = array(
            'title' => $product->getTitle(),
            'message' => $this->view->translate('STORE_This product cannot be added to your cart. '
                . 'The price of the product lower than allowed Minimum Price.')
          );
          continue;
        }
      }

      //@TODO options for products with options
      /*if ($product->type == 'simple' && is_array($product->params) && count($product->params) > 0) {
        $params = $this->_getParam('params', array());

        if (count($params) <= 0) {
          $this->view->message = $this->view->translate('STORE_Additional parameters are required!');
          return;
        }

        $flag = true;
        foreach ($product->params as $key => $value) {
          if ($value['label'] != $params[$key]['label'] || !in_array($params[$key]['value'], explode(',', $value['options']))) {
            $flag = false;
          }
        }

        if (!$flag) {
          $this->view->message = $this->view->translate('STORE_Please, check the additional parameters carefully! Wrong parameters have been assigned.');
          return;
        }
      }*/
      //@TODO options for products with options

      $quantity = 1;
      if ($quantity > $product->quantity) {
        $quantity = $product->quantity;
      }
      if($quantity <= 0) {
        $this->view->status = 0;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid quantity');
        return;
      }


      $db = Engine_Api::_()->getItemTable('store_product')->getAdapter();
      $db->beginTransaction();

      try {
        if (null == $cart) {
          $data = array(
            'user_id' => $viewer->getIdentity()
          );

          $cart = $cartTb->createRow($data);
          $cart->save();
        }

        $data = array(
          'cart_id' => $cart->getIdentity(),
          'product_id' => $product->getIdentity(),
          'price' => $product->price,
          'title' => $product->getTitle(),
          'qty' => $quantity,
          'params' => $params,
          'bundle_id' => $product->getBundleId()
        );

        $item = $table->createRow($data);
        $this->view->item_id = $item->save();

        // Commit
        $db->commit();
      } catch (Engine_Image_Exception $e) {
        $db->rollBack();
      }

      /*if (!($item instanceof Store_Model_Cartitem)) {
        $this->view->status = false;
        return;
      }*/

    }


    $items = $table->fetchAll($table
        ->select()
        ->where('cart_id = ?', $cart->getIdentity())
        ->order('cartitem_id DESC')
    );

    $this->view->status = true;
    $this->view->messages = $messages;
    $this->view->totalCount = $cart->getItemCount();
    $this->view->totalPrice = @$this->view->locale()->toCurrency($cart->getPrice(), $currency);
    $this->view->html = $this->view->partial(
      'cart/_product.tpl',
      'store',
      array(
        'viewer' => $viewer,
        'item' => $item,
        'items' => $items,
      )
    );
  }

  public function removeAction()
  {
    $subject = null;
    if (!Engine_Api::_()->core()->hasSubject()) {
      $id = $this->_getParam('product_id');
      if (null !== $id) {
        $subject = Engine_Api::_()->getItem('store_product', $id);
        if ($subject->getStore()) {
          $approved = $subject->getStore()->approved;
        } else {
          $approved = 1;
        }

        if ($subject && $subject->getIdentity() && $approved) {
          Engine_Api::_()->core()->setSubject($subject);
        } else {
          if ($this->_getParam('format') == 'json') {
            $this->view->status = 0;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product doesn\'t exist');
            return;
          }
          $this->_redirectCustom(
            $this->view->url(
              array(
                'action' => 'index'
              ), 'store_general', true
            )
          );
        }
      }
    }

    $this->_helper->requireSubject('store_product');
    /**
     * @var $viewer     User_Model_User
     * @var $cartTb     Store_Model_DbTable_Carts
     * @var $cartitemTb Store_Model_DbTable_Cartitems
     * @var $cart       Store_Model_Cart
     */

    $viewer = Engine_Api::_()->user()->getViewer();

    $cartTb = Engine_Api::_()->getItemTable('store_cart');
    $cartitemTb = Engine_Api::_()->getDbTable('cartitems', 'store');

    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $cart = $cartTb->getCart($viewer->getIdentity());

    $cartitem_id = $this->_getParam('item_id', 0);
	if ($this->_getParam('remove_one', false)) {
		$item = Engine_Api::_()->getItem('store_cartitem', $cartitem_id);
		if ($item->qty > 1) {
			$item->qty = $item->qty - 1;
			$item->save();
		}
		else {
			$cartitemTb->delete(array('cartitem_id =' . $cartitem_id));
		}
	}
	else {
		$cartitemTb->delete(array('cartitem_id =' . $cartitem_id));
	}
	
    $this->view->status = 1;
    $this->view->totalPrice = @$this->view->locale()->toCurrency($cart->getPrice(), $currency);
    $this->view->totalCount = @$this->view->locale()->toNumber($cart->getItemCount());
    $this->view->supportedTotalPrice = $cart->getPurchasableItemsPrice();
	$this->view->backUrl = $this->view->url(array('controller'=>'cart'),'store_extended',true);
    return;
  }

  public function detailsAction()
  {
    $this->view->form = $form = new Store_Form_Cart_Details();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $format = $this->getParam('format', null);
    $details = (array)$this->getRequest()->getPost();
    /**
     * @var $viewer       User_Model_User
     * @var $detailsTable Store_Model_DbTable_Details
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $detailsTable = Engine_Api::_()->getDbTable('details', 'store');

    try {
      $detailsTable->setDetails($viewer, $details);

      if($format == 'smoothbox') {
        $this->_forward('success', 'utility', 'core', array(
          'parentRefresh' => 10,
          'messages' => Zend_Registry::get('Zend_Translate')->_('The details you have entered have been successfully saved.'),
        ));
      } else {
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $this->_redirect(
          $host_url . $this->view->url(
            array(
              'module' => 'store',
              'controller' => 'cart',
            ), 'default', true
          )
        );
      }
    } catch (Exception $e) {
      $form->addErrorMessage($this->view->translate('An unexpected error has occurred! Please, make sure you have filled all the required fields correctly.'));
    }
  }

  public function seeDetailsAction()
  {
    $item_id = $this->_getParam('item_id', 0);

    /**
     * Get models
     *
     * @var $item    Store_Model_Cartitem
     * @var $product Store_Model_Product
     */
    if (null == ($item = Engine_Api::_()->getItem('store_cartitem', $item_id)) ||
      null == ($product = $item->getProduct())
    ) {
      $this->_forward('success', 'utility', 'core', array(
        'parentClose' => 10,
        'messages' => Zend_Registry::get('Zend_Translate')->_('STORE_No product found'),
      ));
    }
    $this->view->product = $product;
    $this->view->item = $item;
    $this->view->isProductQuantityEnough = ($product->quantity < $item->qty) ? 0 : 1;

    $viewer = Engine_Api::_()->user()->getViewer();
    $detailsTbl = Engine_Api::_()->getDbTable('details', 'store');

    if ($detailsTbl->getDetail($viewer, 'c_location')) {
      $isLocationSupported = true;
    } else {
      $isLocationSupported = $item->isUserLocationSupported();
    }

    $this->view->isUserLocationSupported = $isLocationSupported;

    if (!$isLocationSupported) {
      $this->view->parent_id = $parent_id = $this->_getParam('parent_id', 0);
      /**
       * @var $locationApi Store_Api_Location
       * @var $product     Store_Model_Product
       * @var $table       Store_Model_DbTable_Locations
       * @var $parent      Store_Model_Location
       * @var $locationApi Store_Api_Location
       */
      $locationApi = Engine_Api::_()->getApi('location', 'store');
      $table = Engine_Api::_()->getDbTable('locations', 'store');

      $select = $table->select()->where('location_id = ?', $parent_id);
      $parent = $table->fetchRow($select);

      $paginator = $locationApi->getPaginator($product->page_id, $this->_getParam('page', 1));
      $paginator->setItemCountPerPage(20);
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));

      $this->view->paginator = $paginator;
      $this->view->parent = $parent;
    }
  }

  public function pulldownAction()
  {
    /**
     * @var $viewer User_Model_User
     * @var $table Store_Model_DbTable_Carts
     * @var $cart Store_Model_Cart
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('carts', 'store');
    $cart = $table->getCart($viewer->getIdentity());
    $this->view->items = $items = $cart->getItems();

    // Force rendering now
    $this->_helper->viewRenderer->postDispatch();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  public function updateMiniAction()
  {
    /**
     * @var $viewer User_Model_User
     * @var $table Store_Model_DbTable_Carts
     * @var $cart Store_Model_Cart
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('carts', 'store');
    $cart = $table->getCart($viewer->getIdentity());

    $this->view->status = 1;
    $this->view->itemCount = $itemCount = (int)$cart->getItemCount();
    $this->view->text = $this->view->translate('%s cart', @$this->view->locale()->toNumber($itemCount));
  }
	
  public function checkoutAction()
  {
	    $viewer = Engine_Api::_()->user()->getViewer();
	    $table = Engine_Api::_()->getItemTable('store_cart');
		
		$this->view->via_credits = $via_credits = $this->_getParam('via_credits', 0);
		
	    if (null == ($cart = $table->getCart($viewer->getIdentity())) || !$cart->hasItem() || (!$cart->user_id && !$cart->token)) {
	      return $this->_helper->redirector->gotoRoute(array('action' => 'products'), 'store_general', true);
	    }
	
	    if ($cart->isPublic() && !$cart->hasPublicDetails($viewer)) {
	      return $this->_helper->redirector->gotoRoute(array('module' => 'store', 'controller' => 'cart', 'action' => 'details'), 'default', true);
	    }
	
	    // Clear transaction session
	    $session = new Zend_Session_Namespace('Store_Transaction');
	    $session->unsetAll();
		
		$product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0));
		if (!$product) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'products'), 'store_general', true);
		}
	    /**
	     * @var $paginator    Zend_Paginator
	     * @var $detailsTbl   Store_Model_DbTable_Details
	     * @var $locationsTbl Store_Model_DbTable_Locations
	     */
	     $item = $cart->getRowByProduct($product->getIdentity());
		 if (!$item) {
		 	return $this->_helper->redirector->gotoRoute(array('action' => 'products'), 'store_general', true);
		 }
	    $this->view->product = $product;
		$this->view->item = $item;
		$this->view->bIds = $cart->getCartProductsInBundlesIds();
			
	    // Shipping Details
	    $detailsTbl = Engine_Api::_()->getDbTable('details', 'store');
	    $locationsTbl = Engine_Api::_()->getDbTable('locations', 'store');
	    $this->view->details = $details = $detailsTbl->getDetails($viewer);
	    if ($details['c_location']) {
	      $this->view->country = $details['c_country'];
	      $this->view->region = $details['c_state'];
	    } else {
	      $this->view->country = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_1']));
	      $this->view->region = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_2']));
	    }
	
	    if ($this->_helper->contextSwitch->getCurrentContext() == 'html') {
	      $this->view->justHtml = true;
	      return;
	    }
	
	    /**
	     * Get totals
	     * @var $item Store_Model_Cartitem
	     * @var $api Store_Api_Core
	     */
	    $this->view->offers = $this->getOffers($cart, $via_credits);
		$shippingLocationId = $cart->getShippingLocationId();
	    $cartParams = $cart->getCartParams($via_credits);
	    $totalTaxAmt = (double)$product->getTax();
	    $totalItemAmt = $cartParams['totalItemAmt'];
	    $totalShippingAmt = (double)$product->getShippingPrice($shippingLocationId);
		
	   
	
	    //$totalShippingAmt = $cart->getTotalShippingAmount($via_credits);
	    $this->view->cart = $cart;
	    $this->view->totalPrice = $product->price;
	    $this->view->shippingPrice = $totalShippingAmt;
	    $this->view->taxesPrice = $totalTaxAmt;
	
	    // Enabled Gateways
	    $this->view->api = $api = Engine_Api::_()->store();
	    $mode = $api->getPaymentMode();
	    if ($mode == 'client_store') {
	      $gateways = Engine_Api::_()->getDbTable('gateways', 'store')->fetchAll(array('title = ?' => 'PayPal'));
	    } else {
	      $gateways = Engine_Api::_()->getDbTable('gateways', 'store')->getEnabledGateways();
	    }
	    $this->view->gateways = $gateways;
	}

	public function selectItemsAction() {
		$this->_helper->layout->setLayout('default-simple');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$product = $this->view->product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id'), 0);
		if (!$product) {
			$this->_forward('success', 'utility', 'core', array(
	            'parentRefresh' => false,
	            'smoothboxClose' => true,
	            'messages' => Array($this->view->translate('We are sorry, the product was not found.'))
	        ));
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$params = array();
		$params['owner'] = true;
		$params['user_id'] = $viewer->getIdentity();
	    $params['order'] = 'DESC';
		$params['quantity'] = true;
	    $table = Engine_Api::_()->getDbTable('products', 'store');
		$select = $table->getSelect($params);
		$products = $table->fetchAll($select);
		$this->view->products = $products;
		$selectId = $this->_getParam('select_id', ''); 
		$selectId = explode(',',$selectId); 
		$this->view->selectId = $selectId;
	}





	public function makeRequestAction()
	{
		$this->_helper->layout->setLayout('default-simple');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$credit = $this->view->credit = $this->_getParam('credit', false);
		$product = $this->view->product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id'), 0);
		if (!$product) {
			$this->_forward('success', 'utility', 'core', array(
	            'parentRefresh' => false,
	            'smoothboxClose' => true,
	            'messages' => Array($this->view->translate('Can not found the product'))
	        ));
		}
		$productPrice = $product->getPrice(); 
		$productPrice = Engine_Api::_()->store()->getCredits((double)$productPrice);
		$this->view->productPrice = $productPrice;
		$viewer = Engine_Api::_()->user()->getViewer();


      $balans = Engine_Api::_()->getItem('credit_balance', $viewer->getIdentity())->current_credit;

   // print_die($balans);

    if($balans<$productPrice){
      $this->view->credit_is_null = true;
    }else{
      $this->view->credit_is_null = false;
    }

		if ($credit) {
			$this->view->totalPrice = $productPrice;
		}
		else {
			$params = array();
			$params['owner'] = true;
			$params['user_id'] = $viewer->getIdentity();
		    $params['order'] = 'DESC';
			$params['quantity'] = true;
		    $params['ids'] = $this->_getParam('select_item_id', array(0));
			if (empty($params['ids'])) $params['ids'] = array(0);
		    $table = Engine_Api::_()->getDbTable('products', 'store');
			$select = $table->getSelect($params);
			$products = $table->fetchAll($select);
			$totalPrice = 0;
			foreach ($products as $item) {
				$price = $item->getPrice(); 
				$price = Engine_Api::_()->store()->getCredits((double)$price);
				$totalPrice += (double)$price;
			}
			$this->view->totalPrice = $totalPrice;
			$this->view->products = $products;
		}
		if (!$this->getRequest()->isPost()) {
            return;
        }
		$params = $this->getRequest()->getPost();
		$url = $params['url_submit'];
		$this->_forward('success', 'utility', 'core', array(
          'parentRedirect' => $url,
          'messages' => Zend_Registry::get('Zend_Translate')->_('Please wait...'),
        ));
	}




	public function confirmRequestAction() {



		$viewer = Engine_Api::_()->user()->getViewer();
		$credit = $this->view->credit = $this->_getParam('credit', false);
		$product = $this->view->product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id'), 0);
		$productPrice = $product->getPrice(); 
		$productPrice = Engine_Api::_()->store()->getCredits((double)$productPrice);
		$this->view->productPrice = $productPrice;
		$this->view->api     = Engine_Api::_()->store();
	    $detailsTbl          = Engine_Api::_()->getDbTable('details', 'store');
	    $locationsTbl        = Engine_Api::_()->getDbTable('locations', 'store');
	    $this->view->details = $details = $detailsTbl->getDetails($viewer);
	    if ($details['c_location']) {
	      $this->view->country = $details['c_country'];
	      $this->view->region = $details['c_state'];
	    } else {
	      $this->view->country = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_1']));
	      $this->view->region = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_2']));
	    }
	
	    $this->view->balance = Engine_Api::_()->getItem('credit_balance', $viewer->getIdentity())->current_credit;
		
		$table = Engine_Api::_()->getItemTable('store_cart');
		$cart = $table->getCart($viewer->getIdentity());
		$cartParams = $cart->getCartParams(0);
		$shippingLocationId = $cart->getShippingLocationId();
	    $totalTaxAmt = (double)$product->getTax();
	    $totalItemAmt = $cartParams['totalItemAmt'];
	    $totalShippingAmt = (double)$product->getShippingPrice($shippingLocationId);
		$commissionAmt = 0;
	    $this->view->cart = $cart;
	    $this->view->totalPrice = $product->price;
	    $this->view->shippingPrice = $totalShippingAmt;
	    $this->view->taxesPrice = $totalTaxAmt;
		$this->view->price = $price = ($credit) ? $this->_getParam('credit_value',$product->getPrice()) : 0;
		$settings = Engine_Api::_()->getDbTable('settings', 'core');
    	$this->view->currency = $currency = $settings->getSetting('payment.currency', 'USD');



		if (!$this->getRequest()->isPost()) {
            return;
        }
		
		$table = Engine_Api::_()->getDbTable('orders', 'store');
	    $itemsTable = Engine_Api::_()->getDbTable('orderitems', 'store');
	
	    $shippingDetails = $cart->getShippingDetails();
		$gateway_id = ($credit) ? 3 : 99;
        $data = array(
          'user_id' => $viewer->getIdentity(),
          'gateway_id' => $gateway_id,
          'item_type' => $product->getType(),
          'item_id' => $product->getIdentity(),
          'item_amt' => $price,
          'tax_amt' => $totalTaxAmt,
          'shipping_amt' => $totalShippingAmt,
          'total_amt' => $price+$totalTaxAmt+$totalShippingAmt,
          'commission_amt' => $commissionAmt,
          'currency' => $currency,
          'shipping_details' => $shippingDetails,
          'via_credits' => $credit,
          'offer_id' => 0,
          'token' => $cart->token,
          'status' => 'pending',
          'payment_date' => date('Y-m-d H:i:s')
        );
        /**
         * @var $order Store_Model_Order
         */
        $order = $table->createRow();
        $order->setFromArray($data);
        $order->save();
		
		$itemsTable->delete(array('order_id = ?' => $order->getIdentity()));
		$data = array(
          'page_id' => $product->page_id,
          'order_id' => $order->getIdentity(),
          'item_id' => $product->getIdentity(),
          'item_type' => $product->getType(),
          'name' => $product->getTitle(),
          'params' => '[]',
          'qty' => 1,
          'item_amt' => $price,
          'tax_amt' => $totalTaxAmt,
          'shipping_amt' => $totalShippingAmt,
          'commission_amt' => $commissionAmt,
          'total_amt' => $price+$totalTaxAmt+$totalShippingAmt,
          'currency' => $currency,
          'via_credits' => $credit,
          'status' => 'pending',
        );
		$orderItem = $itemsTable->createRow();
        $orderItem->setFromArray($data);
        $orderItem->save();
		
		$requestTable = Engine_Api::_()->getDbTable('brequests', 'store');
		$data = array(
          'product_id' => $product->getIdentity(),
          'order_id' => $order->getIdentity(),
          'order_item_id' => $orderItem->getIdentity(),
          'user_id' => $viewer->getIdentity(),
          'credit' => $credit,
          'credit_value' => $this->_getParam('credit_value', 0),
          'product_ids' => $this->_getParam('select_item_id', array()),
          'creation_date' => date('Y-m-d H:i:s')
        );
		$request = $requestTable->createRow();
        $request->setFromArray($data);
        $request->save();
		$cartitemTb = Engine_Api::_()->getDbTable('cartitems', 'store');
		$item = $cart->getRowByProduct($product->getIdentity());
		if ($item) {
			if ($item->qty > 1) {
				$item->qty = $item->qty - 1;
				$item->save();
			}
			else {
				$cartitemTb->delete(array('cartitem_id =' . $item->cartitem_id));
			}
		}
		
		$notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
		$notificationTable->addNotification($product->getOwner(), $viewer, $request, 'store_request_brequest');
		
		return $this->_helper->redirector->gotoRoute(array('action' => 'payment-complete', 'product_id' => $order->getIdentity()), 'store_cart', true);
	}
	
	public function paymentCompleteAction() {
		$this->view->order = $order = Engine_Api::_()->getItem('store_order', $this->_getParam('product_id'));	
	}
	
  protected function getOffers($cart, $via_credits)
  {
    $isOffersEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('offers');
    $offers = array();
    if ($isOffersEnabled) {
      /**
       * @var $offersTable Offers_Model_DbTable_Offers
       * @var $products Offers_Model_DbTable_Products
       */
      $offersTable = Engine_Api::_()->getDbTable('offers', 'offers');
      $offerProducts = $offersTable->getMyUpcomingStoreOffersProductsToArray();

      if (!count($offerProducts)) {
        return $offers;
      }

      foreach ($cart->getPurchasableItems() as $item) {
        if ($via_credits && !$item->isStoreCredit()) {
          continue;
        }
        foreach ($offerProducts as $key => $products) {
          foreach ($products as $index => $product) {
            if ($product->product_id == $item->product_id) {
              unset($offerProducts[$key][$index]);
              unset($products[$index]);
              unset($product);
            }
          }
          if (!count($products)) {
            $offers[$key] = Engine_Api::_()->getItem('offer', $key);
          }
        }
      }
    }
    return $offers;
  }
}

