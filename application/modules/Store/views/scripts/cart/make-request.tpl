
<?php $product = $this->product;?>
<div id="store-cart-make-request">
	<h3><?php echo $this->translate('Business Request')?></h3>
	<div class="product-info">
		<?php
    		$photo_url = $product->getPhotoUrl();
			if (empty($photo_url)) $photo_url = $this->layout()->staticBaseUrl . 'application/modules/Store/externals/images/nophoto_product_thumb_normal.png';
		?>
	     <div class="listing-item" style="background-image: url('<?php echo $photo_url;?>')">
	     </div>
		 <div class="listing-description">
		       <div class="listing-item-name">
			     <?php echo $this->htmlLink($product->getHref(),$this->string()->truncate($product->getTitle(), 20))?>
			   </div>
			   
			   <div class="listing-item-condition">
			   	<ul>
			   		<?php if (!empty($product->item_condition)):?>
				    <li>
			    	<?php echo $this->translate('Condition: '); ?><?php echo $this->translate($product->getCondition()); ?></li> 
				    <?php endif;?>
				    <li><?php echo $this->translate('OGV'); ?> <span id="product-price-cal" class="price <?php echo ($this->productPrice >= $this->totalPrice) ? 'green' : 'red';?>"><?php echo $this->productPrice?></span></li>
				</ul>
			   </div>
		 </div>
	</div>
	<div class="request-info">
		<form id="cart-request-form" method="post">
			<input type="hidden" name="credit" value="<?php echo $this->credit;?>" />
			<input id="cart-request-form-url" type="hidden" name="url_submit" />
			<?php if ($this->credit) :?>
				<div id="cart-request-via-credit">
					<div class="label"><?php echo $this->translate('Give OGV')?></div>
					<img id="ogv_modal" src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Store/externals/images/OGV_modal.png';?>"/>
					<span class="input-label"><?php echo $this->translate('OGV')?></span>
					<input class="green" name="credit_value" id="request-via-credit-input" type="number" value="<?php echo $this->productPrice?>"/>
				</div>
			<?php else:?>
				<div id="cart-request-via-exchange">
					<div class="label"><?php echo $this->translate('Select Items')?></div>
					<button type="button" onclick="goToSelectItem()"><?php echo $this->translate('Select Item')?></button>
					<ul id="request-product-items">
					<?php $i = 1;?>
					<?php foreach ($this->products as $item) : $i++;?>
						<li class="product-item">
							<span class="title" data-label="<?php echo $this->translate('Item %s', $i-1); ?>"><?php echo $this->string()->truncate($item->getTitle(), 20); ?></span>
							<span class="select-item-remove store-cart-item-remove" title="<?php echo $this->translate('Remove');?>"><i class="hei hei-trash-o"></i></span>
							<span class="price">
								<?php $price = $item->getPrice(); 
								$price = Engine_Api::_()->store()->getCredits((double)$price);
								echo $price;
								?>
							</span>
							<input data-price="<?php echo $price;?>" class="select-item-id" type="hidden" name="select_item_id[]" value="<?php echo $item->getIdentity()?>" />
						</li>
					<?php endforeach;?>
					<?php for ($i; $i <= 5; $i++) :?>
						<li class="product-item no-product">
							<span class="title"><?php echo $this->translate('Item %s', $i); ?></span>
							<span class="price">0</span>
						</li>
					<?php endfor;?>		
					</ul>
					<div id="request-price-total">
						<span class="label"><?php echo $this->translate('Total')?></span>
						<span id="request-price-total-cal" class="total-price price <?php echo ($this->totalPrice >= $this->productPrice) ? 'green' : 'red';?>"><?php echo $this->totalPrice;?></span>
					</div>
				</div>
				
			<?php endif;?>
			<div class="form-button">
				<button type="submit" value="Submit"><?php echo $this->translate('Confirm');?></button>
				<button name="cancel" id="cancel" type="button" href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Cancel') ?></button>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	function goToSelectItem() {
		var url = '<?php echo $this->url(array('action'=>'select-items','product_id'=>$product->getIdentity()),'store_cart',true)?>';
		var ids = [];
		$$('input.select-item-id').each(function(el) {
			ids.push(el.get('value'));
		});
		var ids = ids.join();
		window.location = url+'/select_id/'+ids;
	}
	function reCalPrice() {
		var productPrice = <?php echo $this->productPrice;?>;
		var totalPrice = 0;
		$$('input.select-item-id').each(function(el) {
			var price = parseFloat(el.get('data-price'));
			totalPrice += price;
		});
		if ($('request-price-total-cal')) {
			$('request-price-total-cal').set('text', totalPrice);
			if (totalPrice >= productPrice) {
				$('request-price-total-cal').addClass('green');
				$('request-price-total-cal').removeClass('red');
			}
			else {
				$('request-price-total-cal').addClass('red');
				$('request-price-total-cal').removeClass('green');
			}
		}
		if (productPrice >= totalPrice) {
			$('product-price-cal').addClass('green');
			$('product-price-cal').removeClass('red');
		}
		else {
			$('product-price-cal').addClass('red');
			$('product-price-cal').removeClass('green');
		}
	}
	window.addEvent('domready', function() {
		$$('.select-item-remove').addEvent('click', function() {
			var li = this.getParent('.product-item');
			var label = li.getElement('.title');
			label.set('text', label.get('data-label'));
			li.getElement('.select-item-remove').destroy();
			li.getElement('.price').set('text', '0');
			li.getElement('.select-item-id').destroy();
			reCalPrice();
		})
		
		$$('#request-via-credit-input').addEvent('change', function() {
			var productPrice = <?php echo $this->productPrice;?>;
			var credit = parseFloat(this.get('value'));
			if (productPrice >= credit) {
				$('product-price-cal').addClass('green');
				$('product-price-cal').removeClass('red');
			}
			else {
				$('product-price-cal').addClass('red');
				$('product-price-cal').removeClass('green');
			}
			if (credit >= productPrice) {
				this.addClass('green');
				this.removeClass('red');
			}
			else {
				this.addClass('red');
				this.removeClass('green');
			}
		});
		
		$('cart-request-form').addEvent('submit', function(event) {


			var values = this.toQueryString();
			var url = '<?php echo $this->url(array('action'=>'confirm-request','product_id'=>$product->getIdentity()),'store_cart',true)?>?'+values;
			$('cart-request-form-url').set('value', url);
			return true;
		})
	});
</script>