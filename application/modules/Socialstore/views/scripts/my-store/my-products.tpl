<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
	<br />
	<?php echo $this->content()->renderWidget('socialstore.search-my-products') ?>
</div>
<div class='layout_middle'>
<h3><?php echo $this->translate('List Products of Store')?></h3>
<br />
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="main_product_list">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class="recent_product_img">
			<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
		</div>		
		<div class='product_browse_options'>   
          <?php echo $this->htmlLink(array(
              'action' => 'edit-product',
              'product' => $item->getIdentity(),
              'route' => 'socialstore_mystore_general',
              'reset' => true,
            ), $this->translate('Edit Product'), array(
              'class' => 'buttonlink icon_store_edit',
            )) ?>
          <?php echo $this->htmlLink(array(
              'route' => 'socialstore_extended',
              'controller' => 'product-photo',
              'action' => 'list-photo',
              'product_id' => $item->getIdentity(),
            ), $this->translate('Edit Photos'), array(
              'class' => 'buttonlink icon_store_photo_new'
          )) ?> 
          <?php echo $this->htmlLink(array(
              'route' => 'socialstore_mystore_general',
              'action' => 'product-attribute',
              'product_id' => $item->getIdentity(),
            ), $this->translate('Attributes'), array(
              'class' => 'buttonlink icon_store_edit'
          )) ?> 
          <?php if ($item->product_type != 'downloadable') {
          	echo $this->htmlLink(array(
              'route' => 'socialstore_discount',
          	  'action' => 'discount',
              'product_id' => $item->getIdentity(),
            ), $this->translate('Discount'), array(
              'class' => 'buttonlink icon_product_discount'
          	));
          } ?> 
          <?php echo $this->htmlLink(array(
		              'action' => 'delete-product',
		              'product_id' => $item->getIdentity(),
		              'route' => 'socialstore_product_general',
		              'reset' => true,
		            ), $this->translate('Delete'), array(
		              'class' => 'buttonlink smoothbox icon_product_delete',
		            )) ?> 
            </div>
		<div class="product_info">
			<div class="product_title"> <?php echo $item ?></div>
			<div class="quickstats">
				<div>
					<span><?php echo $this->translate("Price")?>:</span>
					<span><b class="price"><?php echo $this->currency($item->getPretaxPrice()) ?></b></span>
				</div>
				<div>
					<span><?php echo $this->translate("Statistics")?>:</span>
					<span>
						<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count)) ?>
	            		-
	            		<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
	            		-
	            		<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>
					</span>
				</div>
				<div>
					<span><?php echo $this->translate("Creation Date")?>:</span>
					<span><?php echo $item->creation_date ?></span>
				</div>
				<div>
					<span><?php echo $this->translate("Approve Status")?>:</span>
					<span><?php echo $this->translate(ucfirst($item->approve_status)) ?> 
						<?php if($item->isApproved()){ echo ' - ', $item->approved_date; }?>
		            <?php if ($item->approve_status == 'new') :
		            	 echo ' | ',$this->htmlLink(array(
		              'action' => 'publish-product',
		              'product_id' => $item->getIdentity(),
		              'route' => 'socialstore_mystore_general',
		              'reset' => true,
		            ), $this->translate('Publish'), array(
		            ));
		            endif;?>
					</span>
				</div>
				<div>
					<span><?php echo $this->translate("View Status")?>:</span>
					<span><?php echo $this->translate(ucfirst($item->view_status)) ?> 
				| <?php if ($item->view_status == 'show') :
            	 echo $this->htmlLink(array(
		              'action' => 'show-product',
		              'product_id' => $item->getIdentity(),
		              'route' => 'socialstore_mystore_general',
		              'reset' => true,
		            ), $this->translate('Hide'), array(
		            'class'=>'smoothbox',
		              'title'=> $this->translate("click here to hide this product")
		            ));
		            elseif ($item->view_status == 'hide') :
		            	echo $this->htmlLink(array(
		              'action' => 'show-product',
		              'product_id' => $item->getIdentity(),
		              'route' => 'socialstore_mystore_general',
		              'reset' => true,
		            ), $this->translate('Show'), array(
		            	'class'=>'smoothbox',
		              'title'=> $this->translate("click here to show this product")
		            ));
		            endif;?></span>
				</div>
			</div>
		</div>
		<div style="clear: both"></div>
		</li>
      <?php endforeach; ?>
    </ul>


  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have no product yet.');?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => false,
    //'query' => '',
    'params' => $this->formValues,
  )); ?>
</div>
