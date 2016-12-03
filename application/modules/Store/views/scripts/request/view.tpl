<h3><?php echo $this->translate('Business Request Details')?></h3>
<?php $product = $this->request->getProduct();?>
<div class="owner-part">
	<?php $owner = $product->getOwner();?>
	<div class="owner-info">
		<div class="photo">
		<?php echo $this->itemPhoto($owner, 'thumb.icon')?>
		</div>
		<div class="title"><?php echo $owner?></div>
		<?php $info = Engine_Api::_()->store()->getUserInfo($owner);?>
		<?php if (!empty($info['job_position'])) :?>
		<div class="info job_position"><?php echo $info['job_position']?></div>
		<?php endif;?>
		<?php if (!empty($info['company'])) :?>
		<div class="info company"><?php echo $info['company']?></div>
		<?php endif;?>
		<?php if (!empty($info['city'])) :?>
		<div class="info city"><?php echo $info['city']?></div>
		<div class="info email"><?php echo $owner->email?></div>
		<?php endif;?>
		<?php if (!empty($info['website'])) :?>
		<div class="info website"><?php echo $info['website']?></div>
		<?php endif;?>
	</div>
	<div class="product-info">
		<?php
    		$photo_url = $product->getPhotoUrl('thumb.normal');
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
				    <li>OGV <span id="product-price-cal" class="price>"><?php echo $product->getPrice()?></span></li>
				</ul>
			   </div>
		 </div>
	</div>
</div>
<div class="serperate">
	<img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Store/externals/images/double-arrow.png';?>"/>
</div>
<div class="requester-part">
	<?php $owner = $this->request->getOwner();?>
	<div class="owner-info">
		<div class="photo">
		<?php echo $this->itemPhoto($owner, 'thumb.icon')?>
		</div>
		<div class="title"><?php echo $owner?></div>
		<?php $info = Engine_Api::_()->store()->getUserInfo($owner);?>
		
		<div class="info job_position">
			<?php if (!empty($info['job_position'])) :?>
			<?php echo $info['job_position']?>
			<?php endif;?>
		</div>
		
		<div class="info company">
			<?php if (!empty($info['company'])) :?>
			<?php echo $info['company']?>
			<?php endif;?>
		</div>
		<div class="info city">
			<?php if (!empty($info['city'])) :?>
			<?php echo $info['city']?>
			<?php endif;?>
		</div>
		<div class="info email"><?php echo $owner->email?></div>
		<div class="info website">
			<?php if (!empty($info['website'])) :?>
			<?php echo $info['website']?>
			<?php endif;?>
		</div>
	</div>
	<ul class="products-list">
		<?php if ($this->request->credit):?>
		<li class="product-item">
			<div class="product-info">
				<div class="listing-item" style="background-image: url('<?php echo $this->layout()->staticBaseUrl . 'application/modules/Credit/externals/images/OGV_hr.png';?>')">
			     </div>
			
				<div class="listing-description">
					<div class="listing-item-name"><?php echo $this->translate('OGV: %s', $this->request->credit_value)?></div>
				</div>
			</div>
		</li>	
		<?php else: ?>
		<?php foreach ($this->request->getProducts() as $product) :?>
		<li class="product-item">
			<div class="product-info">
				<?php
		    		$photo_url = $product->getPhotoUrl('thumb.normal');
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
						    <li>OGV <span id="product-price-cal" class="price"><?php echo $product->getPrice();?></span></li>
						</ul>
					   </div>
				 </div>
			</div>
		</li>
		<?php endforeach;?>
		<?php endif;?>
	</ul>
</div>
<form method="post">
<div class="buttons">
	<button type="submit" class="accept"><?php echo $this->translate('Accept')?></button>

	<button class="dismiss" type="button" onclick="Smoothbox.open('<?php echo $this->url(array('action'=>'dismiss','id'=>$this->request->getIdentity()),'store_request',true)?>');"><?php echo $this->translate('Reject')?></button>

	<button name="cancel" id="cancel" type="button" href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Cancel') ?></button>
</div>
</form>
