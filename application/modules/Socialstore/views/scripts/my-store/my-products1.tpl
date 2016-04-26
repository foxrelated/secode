<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>
<div class='layout_middle'>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <table>
    	<thead>
    		<tr>
    			<th></th>
    		</tr>
    	</thead>
    	<tbody>
    		<?php foreach( $this->paginator as $item ): ?>
        <td>
			<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
		</td>		
		<td class="product_info">
			<div class="product_title"> <?php echo $item ?></div>
			
		</td>
		<td>   
          <?php echo $this->htmlLink(array(
              'action' => 'edit-product',
              'product' => $item->getIdentity(),
              'route' => 'store_mystore_general',
              'reset' => true,
            ), $this->translate('Edit Product'), array(
              
            )) ?>
          <?php echo $this->htmlLink(array(
              'route' => 'socialstore_extended',
              'controller' => 'product-photo',
              'action' => 'list-photo',
              'product_id' => $item->getIdentity(),
            ), $this->translate('Edit Photos'), array(
              
          )) ?> 
          <?php echo $this->htmlLink(array(
              'action' => 'delete-product',
              'product_id' => $item->getIdentity(),
              'route' => 'store_product_general',
              'reset' => true,
            ), $this->translate('Delete'), array(
              
            )) ?> 
            <?php if ($item->approve_status == 'new') :
            	 echo $this->htmlLink(array(
              'action' => 'publish-product',
              'product_id' => $item->getIdentity(),
              'route' => 'store_mystore_general',
              'reset' => true,
            ), $this->translate('Publish'), array(
              
            ));
            endif; ?>
            <?php if ($item->view_status == 'show') :
            	 echo $this->htmlLink(array(
              'action' => 'show-product',
              'product_id' => $item->getIdentity(),
              'route' => 'store_mystore_general',
              'reset' => true,
            ), $this->translate('Hide'), array(
              
            ));
            elseif ($item->view_status == 'hide') :
            	 echo $this->htmlLink(array(
              'action' => 'show-product',
              'product_id' => $item->getIdentity(),
              'route' => 'store_mystore_general',
              'reset' => true,
            ), $this->translate('Show'), array(
              
            ));
            endif; ?>
        </td>
	    </tr>
      <?php endforeach; ?>
	</tbody>
</table>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have no product yet.');?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    //'query' => '',
    //'params' => $this->formValues,
  )); ?>
</div>
