<div class='layout_middle'>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="groupbuy_list">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class="groupbuy_browse_photo">
            	<a href="<?php echo $item->getHref();?>">
           			<?php   echo $item->getImageHtml('deal_thumb_medium','thumb.normal1',266,195) ?>
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
          <?php if($item->isSoldOut()):?><div class="groupbuy_deal_sold_out"></div><?php endif; ?>
          <div class='groupbuy_browse_info'>
            <p class='groupbuy_browse_info_title'>
              <?php echo $this->htmlLink($item->getHref(), substr($item->getTitle(), 0, 58)) ?>
              <?php if(strlen($item->getTitle()) > 55) echo $this->translate('...');?>
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

  <?php elseif( $this->category || $this->show == 2 || $this->search ):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has created a deal with that criteria.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'groupbuy_general', true).'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has created a deal yet.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'groupbuy_general', true).'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
</div>