<?php
  echo $this->render('_attributeField.tpl')
?>
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
	<br />
</div>
<div class='layout_middle'>
<h3><?php echo $this->translate('Product Attributes')?></h3>
<br />

<?php if ($this->product->attributeset_id == 0) : ?>
  <ul>
  	<li class = "ynstore_review_li_class">
          	<?php 
          	echo $this->htmlLink(
          			array(
			            'product_id' => $this->product->product_id,  
						'action' => 'add-product-set',
				        'route' => 'socialstore_mystore_general',
			        ), 
			        $this->translate('Select Attribute Set'), 
			        array(
			        	'class' => 'smoothbox',
	        		)); 
	        		
	        if ($this->presets && count($this->presets) > 0) {
	        	echo ' '.$this->translate('or').' '.$this->htmlLink(
									          			array(
												            'product_id' => $this->product->product_id,  
															'action' => 'load-attribute-preset',
													        'route' => 'socialstore_mystore_general',
												        ), 
												        $this->translate('Load Attribute Presets'), 
												        array(
												        	'class' => 'smoothbox',
										        	));
	        }		
	        ?>
	</li> 
  </ul>
<?php else: ?>
  <ul>	
	<li class = "ynstore_review_li_class">
				<span><?php echo $this->translate('Attribute Set').": "?></span>
				<span><?php echo $this->attrSetName; ?></span>
				<span>
					<?php echo $this->htmlLink(
          			array(
			            'product_id' => $this->product->product_id,  
						'action' => 'edit-product-set',
				        'route' => 'socialstore_mystore_general',
			        ), 
			        $this->translate('Edit'), 
			        array(
			        	'class' => 'smoothbox',
	        		)) ?>
				</span>
				<span>
				<?php echo ' - '.$this->htmlLink(
									array(
						            	'product_id' => $this->product->product_id,  
										'action' => 'save-attribute-preset',
								        'route' => 'socialstore_mystore_general',
							        ), 
							        $this->translate('Save as Attribute Preset'), 
							        array(
							        	'class' => 'smoothbox',
					        		))?>
				</span>
	</li>
  </ul>
<?php endif;?>
<br />

<ul class="ynstore_attributes">
  <?php if (count($this->types) > 0) :?>
  <?php foreach( $this->types as $field ): ?>
    <?php echo $this->ynStoreAttribute($field) ?>
  <?php endforeach; ?>
  <?php endif;?>
</ul>

</div>