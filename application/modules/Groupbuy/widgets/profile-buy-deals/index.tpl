<ul class="groupbuy_list">
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class='groupbuy_browse_photo'>
        <a href="<?php echo $item->getHref();?>">
       			<?php   echo $item->getImageHtml('deal_thumb_medium','thumb.normal1',240,195,'') ?>
       		</a>  
      </div>
      <div class='groupbuy_browse_info'>
        <div class='groupbuy_browse_info_title'>
          <h3>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
          </h3>
        </div>
        <div  class="groupbuy_browse_date">
          <?php echo $this->translate('posted '); echo $this->timestamp(strtotime($item->creation_date)) ?>
      
        </div>
        <div class='groupbuy_browse_info_blurb'>
          <div> 
          	<?php echo $this->translate("Value: "); echo $this->currencyadvgroup($item->value_deal,$item->currency);?>
          </div>
         <div> 
          	<?php echo $this->translate("Discount: ");echo $item->discount;?>%
          </div>
          <div> 
          	<?php echo $this->translate("Price: ");echo $this->currencyadvgroup($item->price,$item->currency)?>
          </div>
          <div> 
          	<?php echo $item->current_sold; echo $this->translate(" Bought");?>
          </div>
            <br />
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
