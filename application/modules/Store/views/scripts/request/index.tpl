<div class="layout_middle">
	<h3><?php echo $this->translate("Business Request <img src='/application/modules/Core/externals/images/next.png'>  %s", $this->product)?></h3>
	<div id="show-today"><?php echo $this->locale()->toDate(Engine_Api::_()->store()->getToday());?></div>

	<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
	<ul id="request-list">
	<?php foreach ($this->paginator as $request) :?>
		<li class="request-item">
			<div class="request-left">
				<?php $order = $request->getOrder();?>
				<?php if ($request->credit) :?>
				<div class="item-photo" style="background-image: url('<?php echo $this->layout()->staticBaseUrl . 'application/modules/Store/externals/images/OGV_modal.png';?>')"></div>
				<div class="request-info">
					<div class="credit-value"><?php echo $this->translate('OGV: %s', $request->credit_value)?></div>
					<?php if ($order) :?>
					<div class="order-key"><?php echo $this->translate('Request Key:<span class="request-key"> %s</span>', $order->ukey)?></div>
					<?php endif;?>
				</div>
				<?php else :?>
				<?php $products = $request->getProducts();?>
				<?php if (count($products)) :?>
				<ul class="exchange-product-list">
					<?php foreach ($products as $item) :?>
					<li class="exchange-product-item">
						<?php
				    		$photo_url = $item->getPhotoUrl('thumb.normal');
							if (empty($photo_url)) $photo_url = $this->layout()->staticBaseUrl . 'application/modules/Store/externals/images/nophoto_product_thumb_normal.png';
			    		?>
						<div class="item-photo" style="background-image: url(' <?php echo $photo_url;?>')"></div>
						<div class="request-info">
							<div class="title"><?php echo $item;?></div>

							<div class="condition"><?php echo $this->translate($item->getCondition());?></div>
							<div class="credit"><?php echo $this->translate('OGV %s', Engine_Api::_()->store()->getCredits((double) $item->getPrice()));?></div>
							<?php if ($item->type == 'simple') :?>
							<div class="quantity"><?php echo $this->translate('Quantity %s', $item->quantity);?></div>
							<?php endif;?>
							<div class="time"><?php echo $this->timestamp($item->creation_date);?></div>
							<?php if ($order) :?>
							<div class="order-key"><?php echo $this->translate('Request Key:<span class="request-key"> %s</span>', $order->ukey)?></div>
							<?php endif;?>
						</div>
					</li>
					<?php endforeach;?>
				</ul>
				<?php else :?>
				<div class="tip">
					<span>
						<?php echo $this->translate('There are no available Items to exchange.') ?>
					</span>
			    </div>
				<?php endif;?>
				<?php endif;?>
			</div>
			<div class="request-right">
				<button class="view-request-button" onclick="Smoothbox.open('<?php echo $this->url(array('action'=>'view','id'=>$request->getIdentity()),'store_request',true)?>');"><?php echo $this->translate('View Details')?></button>
				<div class="action-buttons">
					<span><a href="<?php echo $this->url(array('action'=>'dismiss','id'=>$request->getIdentity()),'store_request',true)?>" class="request-link dismiss smoothbox" title="<?php echo $this->translate('Reject')?>"><img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Store/externals/images/dismiss.png';?>"/></a></span>
					<span><a href="<?php echo $this->url(array('action'=>'summary','id'=>$request->getIdentity()),'store_request',true)?>" class="request-link approve" title="<?php echo $this->translate('Accept')?>"><img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Store/externals/images/approve.png';?>"/></a></span>
				</div>
				<?php $owner = $request->getOwner();if ($owner) :?>
				<div class="owner-info">
					<?php echo $owner;?>
				</div>
				<?php endif;?>
				<?php if ($order && $order->getCountry()) :?>
				<div class="country">
					<?php echo $order->getCountry();?>
				</div>
				<?php endif;?>
			</div>
		</li>
	<?php endforeach;?>
	</ul>	
	<div id='paginator'>
		<?php if( $this->paginator->count() > 1 ): ?>
		     <?php echo $this->paginationControl($this->paginator, null, null, array(
		            'pageAsQuery' => true,
		            'query' => array(),
		          )); ?>
		<?php endif; ?>
	</div>
	<?php else: ?>
    <div class="tip">
		<span>
			<?php echo $this->translate('There are no Business Requests for this Item.') ?>
		</span>
    </div>
	<?php endif; ?>
</div>
