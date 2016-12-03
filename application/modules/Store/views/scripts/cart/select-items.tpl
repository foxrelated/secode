<?php $product_price = $this->product->getPrice(); 
$product_price = Engine_Api::_()->store()->getCredits((double)$product_price);
?>
<div id="store-cart-select-items">
	<h3><?php echo $this->translate('Select Item')?></h3>
	<div id="items-value">
		<span class="label"><?php echo $this->translate('Item(s)')?></span>
		<span id="items-value-cal" class="value red">0</span>
	</div>
	<div id="items-select">
	<?php if (count($this->products)):?>
		<form id="items-select-form" method="get" action="<?php echo $this->url(array('action'=>'make-request','product_id'=>$this->product->getIdentity()),'store_cart',true);?>">
			<ul id="items-list">
			<?php foreach ($this->products as $item):?>
				<li class="select-item handle-listing">
					<div class="listing-border">
				    	<?php
				    		$photo_url = $item->getPhotoUrl();
							if (empty($photo_url)) $photo_url = $this->layout()->staticBaseUrl . 'application/modules/Store/externals/images/nophoto_product_thumb_normal.png';
			    		?>
					     <div class="listing-item" style="background-image: url('<?php echo $photo_url;?>')">
					     </div>
						 <div class="listing-description">
						       <div class="listing-item-name">
							     <?php echo $this->htmlLink($item->getHref(),$this->string()->truncate($item->getTitle(), 20))?>
							   </div>
							   
							   <div class="listing-item-condition">
							   	<ul>
							   		<?php if (!empty($item->item_condition)):?>
	 							    <li>
 							    	<?php echo $this->translate('Condition: '); ?><?php echo $this->translate($item->getCondition()); ?></li> 
								    <?php endif;?>
								    <?php $priceStr = $item->getPrice(); 
								    if ($priceStr && $item->isStoreCredit()) :
									$priceStr = Engine_Api::_()->store()->getCredits((double)$priceStr);
								    ?>
									<?php endif;?>
								    <li>OGV: <?php echo $priceStr;?></li>
								    <li>
								    	<input type="checkbox" name="select_item_id[]" class="select-item-checkbox" value="<?php echo $item->getIdentity();?>" data-price="<?php echo $priceStr;?>" <?php if (in_array($item->getIdentity(), $this->selectId)) echo 'checked';?>/>
								    </li>
								</ul>
							   </div>
						 </div>
						 
					</div>
				</li>
			<?php endforeach;?>
			</ul>
			<div class="form-button">
				<button type="submit" value="Submit"><?php echo $this->translate('Select Item');?></button>
				<button name="cancel" id="cancel" type="button" href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Cancel') ?></button>
			</div>
		</form>
	<?php else: ?>
		<div>
			<p><?php echo $this->translate('We are sorry but you don\'t have any available Item to exchange.');?></p>
			<br>
		</div>
		<button class="close" onclick="parent.Smoothbox.close()"><?php echo $this->translate('Close')?></button>
	<?php endif;?>
	</div>
</div>

<script type="text/javascript">
	function updateSelectPrice() {
		var product_price = parseFloat(<?php echo $product_price?>);
		var total = 0;
		$$('.select-item-checkbox:checked').each(function(el) {
			var price = parseFloat(el.get('data-price'));
			total += price;
		});
		var update = $('items-value-cal');
		if (update) {
			update.set('text', total);
			if (total >= product_price) {
				update.removeClass('red');
				update.addClass('green');
			}
			else {
				update.removeClass('green');
				update.addClass('red');
			}
		}
	}
	window.addEvent('domready', function() {
		$$('.select-item-checkbox').addEvent('change', function() {
			updateSelectPrice();
		});
		$('items-select-form').addEvent('submit', function(event) {
			if ($$('.select-item-checkbox:checked').length > 5) {
				return false;
				alert('<?php echo $this->translate('You can select up to 5 Items only.')?>');
			}
		});
		updateSelectPrice();
	});
</script>
