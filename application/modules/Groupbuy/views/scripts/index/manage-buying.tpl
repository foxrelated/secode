
<div class="headline">
  <h2>
    <?php echo $this->translate('GroupBuy');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<div class='layout_right'>
  <?php echo $this->form->render($this) ?>

  <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="quicklinks">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->quickNavigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>
<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
</script>
<div class='layout_middle'>
    <?php
if($this->message): ?>
<div class="message">
<?php echo $this->translate("You have just bought a deal!"); ?>
</div>
<?php
endif;
?>  
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="groupbuy_browse">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class="groupbuy_browse_photo">
            <a href="<?php echo $item->getHref();?>">
           			<?php echo $item->getImageHtml('deal_thumb_medium','thumb.normal',170,140,'') ?>
           		</a>            
          </div>
          <div class='groupbuy_browse_options'>   
            <?php /*echo $this->htmlLink(array(
              'action' => 'contract',
              'item' => $item->item,
              'route' => 'groupbuy_general',
              'reset' => true,
            ), $this->translate('Deal Contract'), array(
              'class' => 'buttonlink smoothbox',
            ))*/ ?>
          <?php /* if($item->status_buy == 0):  ?>
              <?php echo $this->htmlLink(array(
              'action' => 'request-refund',
              'item' => $item->item,
              'route' => 'groupbuy_general',
              'reset' => true,
            ), $this->translate('Request Refund'), array(
              'class' => 'buttonlink smoothbox icon_groupbuy_refund',
            )) ?>
          <?php  endif; */?> 
          <!--  <?php echo $this->htmlLink(array(
              'action' => 'delete-buy',
              'item' => $item->item,
              'route' => 'groupbuy_general',
              'reset' => true,
            ), $this->translate('Delete'), array(
              'class' => 'buttonlink smoothbox icon_groupbuy_delete',
            )) ?> -->
          </div>
          <div class='groupbuy_browse_info'>
            <p class='groupbuy_browse_info_title'>
              <?php echo $this->htmlLink($item->getHref(), $item->title) ?>
            </p>
            <p class='groupbuy_browse_info_date'>
               <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              - <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
            </p>
            <div class='groupbuy_browse_info_blurb'>
              <span class="deal_price_label"> 
                  <?php echo $this->translate("Value:");?>
              </span>
              <span class="groupbuy_deal_price_value"><?php echo trim($this->currencyadvgroup($item->value_deal,$item->currency)); ?></span>
              -
              <span> 
                  <?php echo $this->translate("Price:")?>
              </span>
              <span>
              	<?php echo $this->currencyadvgroup($item->price,$item->currency) ?>
              </span>
              -
               <span> 
                  <?php echo $this->translate("Discount:")?>
              </span>
              <span>
              	<?php echo $this->discount($item->discount) ?>
              </span>
              <br />
              <span>
                  <?php echo $this->translate("Quantity: ")?>
              </span>
              <span class="deal_number_value">
              	<?php echo $this->number($item->number) ?>
              </span>
              -
              <span> 
                  <?php echo $this->translate("Total:");?>
              </span>
			  <span class="deal_total_value">
              	<?php echo $this->currencyadvgroup($item->total,$item->currency) ?>
              </span>
              <!--<br />
              <span> 
                  <?php echo $this->translate("Coupon Code:");?>
              </span>
              <span>
                   <?php echo $item->getCoupon() ?>
              </span> -->
              <!--<br />
              <span> 
                  <?php echo $this->translate("Status:");?>
              </span>
              <span>
                   <?php echo $item->getStatusString() ?>
              </span> 
              -->
              <br />
              <?php if ($item->location_title): ?>
              <span> 
              <?php echo $this->translate("Location:");?>
              </span>
              <span>
              	<?php echo $item->getLocation()?>
              </span>
              <br />
              <?php endif; ?>
              <?php if ($item->cat_title): ?>              
              <span> 
                  <?php echo $this->translate("Category:");?>
              </span>
              <span>
              	<?php echo $item->cat_title?>
              </span>
              <!--<br /> 
              <?php endif; ?>
              <span> 
                  <?php echo $this->translate("Start Time:");?>
              </span>
              <span>
              	<?php 
                  	echo $this->locale()->toDateTime($item->start_time)?>
              </span>
              <br />
              <span> 
                  <?php echo $this->translate("End Time:");?>
              </span>
              <span>
              	<?php echo $this->locale()->toDateTime($item->end_time)?>
              </span>
              -->
                <?php // $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
              <?php // echo $this->fieldValueLoop($item, $fieldStructure) ?>
            </div>
          </div>
           <div class='groupbuy_list_description'>
                  <?php //echo $this->string()->truncate($this->string()->stripTags($item->description), 300)?>
              </div>
        </li>
      <?php endforeach; ?>
    </ul>
   <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any deals that match your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any deals.');?>
          <?php echo $this->translate('Get started by %1$sbuy%2$s a deal.', '<a href="'.$this->url(array('action' => ''), 'groupbuy_general').'">', '</a>'); ?>
      </span>
    </div>
  <?php endif; ?>
   <?php echo $this->paginationControl($this->paginator, null, array("pagination/dealpagination.tpl","groupbuy"), array("orderby"=>$this->orderby)); ?>

</div>

