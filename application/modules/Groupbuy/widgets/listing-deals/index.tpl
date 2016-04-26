<div class='layout_middle'>
  <?php if( count($this->paginator)): ?>
    <ul class="groupbuy_list">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class="groupbuy_browse_photo">
            	<a href="<?php echo $item->getHref();?>">
           			<?php   echo $item->getImageHtml('deal_thumb_medium','thumb.normal1',339,195,'') ?>
           		</a>            
          </div>
          <div class="groupbuy_widget_value">
              <div class="groupbuy_widget_value_value"> 
              	<?php echo $this->currencyadvgroup($item->value_deal, $item->currency)," ",$this->translate("Value") ?>  
              </div>
              <div class="groupbuy_widget_value_price"> 
                  <span class="groupbuy_widget_value_price_price"><?php echo $this->currencyadvgroup($item->price, $item->currency) ?></span> 
              </div>
           </div>
          <div class='groupbuy_browse_info'>
            <p class='groupbuy_browse_info_title'>
              <?php echo $this->htmlLink($item->getHref(), $item->title) ?>
            </p>
            <p class='groupbuy_browse_info_date'>
               <?php echo $this->timestamp($item->creation_date) ?>
               <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
            </p>
            <div class='groupbuy_browse_info_blurb'>

              <?php if ($item->location_title) { ?>
              <span>
              	<?php echo $item->location_title?>
              </span>
              <span>
                  <?php echo $this->translate("-"); ?>
              </span>
              <span>
                  <?php echo $this->number($item->current_sold) ?>    
              </span>
              <span> 
                  <?php echo $this->translate("Bought");?>
              </span>
              <?php } else { ?>
                  <span>
                      <?php echo $this->number($item->current_sold) ?>    
                  </span>
                  <span> 
                      <?php echo $this->translate("Bought");?>
                  </span>
              <?php } ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
  )); ?>
</div>
<script type="text/javascript">
window.addEvent('domready',function(){
    $$('ul.groupbuy_list li:nth-child(2n)').setStyle('margin','0px 0px 11px 11px');
});
</script>