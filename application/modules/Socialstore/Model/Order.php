<?php

class Socialstore_Model_Order extends Engine_Db_Table_Row implements Socialstore_Payment_Order_Interface {

	protected $_searchTriggers = false;
	protected $_items;

	/**
	 * Identifier getter
	 *
	 * @return string
	 */
	public function getId() {
		return $this -> order_id;
	}
	
	public function getState(){
		return $this->state;
	}

	/**
	 * Total amount getter
	 *
	 * @return decimal (16,2)
	 */
	public function getTotalAmount() {
		return $this -> total_amount;
	}

	public function getSubAmount(){
		return $this->sub_amount;
	}
	/**
	 * Tax amount getter
	 *
	 * @return decimal (16,2)
	 */
	public function getTaxAmount() {
		return $this -> tax_amount;
	}

	/**
	 * Currency getter
	 *
	 * @return string   char(3)
	 */
	public function getCurrency() {
		return $this -> currency;
	}

	/**
	 * Shipping amount getter
	 *
	 * @return decimal (16,2)
	 */
	public function getShippingAmount() {
		return $this->shipping_amount;
	}

	/**
	 * Handling amount getter
	 *
	 * @return decimal (16,2)
	 */
	public function getHandlingAmount() {
		return $this->handling_amount;
	}

	/**
	 * Discount amount getter
	 *
	 * @return decimal (16,2)
	 */
	public function getDiscountAmount() {
		return $this->discount_amount;
	}
	
	public function getCommissionAmount(){
		return $this->commission_amount;
	}

	/**
	 * Items getter
	 *
	 * @return array of Socialstore_Payment_Order_Item
	 */
	public function getItems() {
		// check to get all items;
		if($this -> _items == null) {
			$Items = new Socialstore_Model_DbTable_OrderItems;
			$select = $Items -> select() -> where('order_id=?', $this -> order_id);
			foreach($Items->fetchAll($select) as $item) {
				$this -> _items[] = $item;
			}
		}
		return $this -> _items;
	}

	/**
	 * Get assigned to request item by item identifier
	 *
	 * @param string $index item identifier
	 * @return Socialstore_Payment_Order_Item_Interface
	 */
	public function getItem($index) {
		$items = $this -> getItems();
		return @$items[$index];
	}

	public function getItemByProductId($product_id) {
		$Items = new Socialstore_Model_DbTable_OrderItems;
		$select = $Items -> select() -> where('order_id=?', $this -> order_id)->where('object_id = ?', $product_id);
		return $Items->fetchRow($select);
	}
	public function getItemByProOpt($product_id,$options) {
		$Items = new Socialstore_Model_DbTable_OrderItems;
		$select = $Items -> select() -> where('order_id=?', $this -> order_id)->where('object_id = ?', $product_id);
		if ($options != null && $options != '') {
			$select->where('options = ?', $options);
		}
		return $Items->fetchRow($select);
	}
	
	/**
	 * Shipping address getter
	 *
	 * @return Socialstore_Payment_Order_Interface_Address | null
	 */
	public function getShippingAddress() {
		$Address = new Socialstore_Model_DbTable_ShippingAddresses;
		
		$select = $Address->select()->where('order_id=?', $this->getId());
		$item = $Address->fetchRow($select);
		if(is_object($item)){
			return $item;
		}
		return null;
	}

	/**
	 * Billing address getter
	 *
	 * @return Socialstore_Payment_Order_Interface_Address | null
	 */
	public function getBillingAddress() {
		$Address = new Socialstore_Model_DbTable_BillingAddresses;
		
		$select = $Address->select()->where('order_id=?', $this->getId());
		$item = $Address->fetchRow($select);
		if(is_object($item)){
			return $item;
		}
		return null;
	}

	/**
	 * Order options getter
	 *
	 * @return Socialstore_Payment_Options
	 */
	public function getOptions() {
		return '';
	}
	
	public function getPaytype() {
		return $this -> paytype_id;
	}

	public function getIdentity() {
		return $this -> order_id;
	}

	public function getPlugin() {
		$table = new Socialstore_Model_DbTable_Paytypes;
		$paytype =  $this->paytype_id;
		$item = $table -> find((string)$paytype) -> current();
		if(!is_object($item)) {
			throw new Exception("invalid $paytype or $paytype does not exists!");
		}
		$plugin = $item -> getPlugin();
		$plugin -> setOrder($this);
		return $plugin;
	}
	
	public function updateOrder(){
		$this->save();
	}
	
	public function addItem($item, $qty, $params){
		return $this->getPlugin()->addItem($item, $qty, $params);
	}
	
	public function getQty(){
		return $this->quantity;
	}
	
	public function getItemQty($item){
		
	}
	
	public function setItemQty($item){
		
	}
	
	public function removeItem($item){
		$this->tax_amount -= $item->getTaxAmount() * $item->getQty();
		$this->handling_amount -=  $item->getHandlingAmount();
		$this->discount_amount -=  $item->getDiscountAmount();
		$this->shipping_amount -=  $item->getShippingAmount();
		$this->sub_amount -=  $item->getSubAmount();
		$this->quantity -= $item->getQty();
		$this->total_amount -=  $item->getTotalAmount();
		parent::save();	
		$item->delete();
	}
	
	public function removeAll() {
		$items = $this->getItems();
		foreach ($items as $item) {
			$item->delete();
		}
		$this->tax_amount = 0;
		$this->handling_amount =  0;
		$this->discount_amount =  0;
		$this->shipping_amount =  0;
		$this->sub_amount =  0;
		$this->quantity = 0;
		$this->total_amount =  0;
		parent::save();	
	}
	
	public function saveInsecurity(){
		$items =  $this->getItems();
		$total_amount = 0;
		$discount_amount   = 0;
		$handling_amount = 0;
		$tax_amount = 0;
		$shipping_amount = 0;
		$sub_amount = 0;
		$commission_amount = 0;
		$quantity = 0;
		foreach($items as $item){
			$total_amount  += $item->getTotalAmount();
			$handling_amount  += $item->getHandlingAmount();
			$tax_amount  += $item->getTaxAmount() * $item->getQty();
			$shipping_amount  += $item->getShippingAmount();
			$discount_amount  += $item->getDiscountAmount();
			$commission_amount += $item->getCommissionAmount();
			$sub_amount += $item->getSubAmount();
			$quantity += $item->getQty();
		}
		$this->tax_amount = $tax_amount;
		$this->handling_amount =  $handling_amount;
		$this->discount_amount =  $discount_amount;
		$this->shipping_amount =  $shipping_amount;
		$this->sub_amount =  $sub_amount;
		$this->commission_amount = $commission_amount;
		$this->quantity = $quantity;
		$this->total_amount =  $total_amount;
		
		parent::save();	
	}
	
	
	/**
	 * @return true|false
	 */
	public function noBilling(){
		return $this->getPlugin()->noBilling();
	}
	
	/**
	 * @return true|false
	 */
	public function noShipping(){
		return $this->getPlugin()->noShipping();
	}
	
	public function noPackage() {
		$Packages = new Socialstore_Model_DbTable_ShippingPackages;
		$select = $Packages->select()->where('order_id = ?', $this->order_id);
		$result = $Packages->fetchRow($select);
		if (count($result) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function reset(){
		$db =  Engine_Db_Table::getDefaultAdapter();
		$db->delete('engine4_socialstore_orderitems',array('order_id=?'=>$this->getId()));
		$db->delete('engine4_socialstore_shippingpackages', array('order_id = ?' => $this->getId()));
	}
	
	public function getProducts() {
		$products = array();
		$Product = new Socialstore_Model_DbTable_SocialProducts;
		$Items = new Socialstore_Model_DbTable_OrderItems;
		$select = $Items -> select() -> where('order_id=?', $this -> order_id);
		$results = $Items->fetchAll($select);
		foreach($results as $item) {
			$products[] = $Product->getProduct($item->object_id);
		}
		return $products;		
	}

	public function getTotalAmountByStore($store_id) {
		$items = $this->getItems();
		$total = 0;
		foreach ($items as $item) {
			if ($item->store_id == $store_id) {
				$total += $item->total_amount;
			}
		}
		return $total;
	}
	function _getPdfFile()
	{
		$file = Engine_Api::_()->getApi('storage', 'storage')->get($this->pdf_id);
		return $file;
	}
	function getPdfPath()
	{
		$file = $this->_getPdfFile();
		if($file === null)
		{
			return null;
		}
		return APPLICATION_PATH . DS .$file->storage_path;
	}	
	function pdfrender($html){
		//$pdf = new Zend_Pdf();
		$html2pdf = new Html2Pdf_Converter('P', 'A4', 'en',true,'UTF-8',3);
		$html2pdf->addFont('tahoma');
		//$html2pdf->_html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->writeHTML($html);
		$pdf_file = $this->_getPdfFile();
		if($pdf_file === null ){
		
			// store file
			$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';			
			$html2pdf->Output($path.DS.'order_'.$this->getIdentity().'.pdf', 'F');						
			// storage
			
			$storage = Engine_Api::_() -> storage();
			if ($this->owner_id == 0) {
				$id = $this->guest_id;
			}
			else {
				$id = $this->owner_id;
			}
			$params = array(
		      'parent_type' => 'order_item',
		      'parent_id' => $id
		    );
			$pdf_file = $storage->create($path.DS.'order_'.$this->getIdentity().'.pdf', $params);
			$this->pdf_id = $pdf_file->file_id;
			$this->save(); 
			
			// Remove temp files
    		@unlink($path.DS.'order_'.$this->getIdentity().'.pdf');
    		
		}
		else 
		{			
			$path = APPLICATION_PATH . DS .$pdf_file->storage_path;
			if(file_exists($path)){
				@unlink($path);
			}
			$html2pdf->Output($path,'F');
		}								
	}
	
}
