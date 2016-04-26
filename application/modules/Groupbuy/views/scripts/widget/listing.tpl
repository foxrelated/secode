<?php if(Count($this->paginator) <= 0): ?>              
       <div class="tip" style="clear: inherit;">
      <span>
           <?php echo $this->translate('There is no deal found.');?>
      </span>
      <div style="clear: both;"></div>
    </div>
<?php else:?>

<ul class="generic_list_widget"> 
  <?php foreach( $this->paginator as $item ): ?>
    <li style="padding-top:5px">
       <div class='groupbuy_widget_info'>
        <div class='groupbuy_widget_photo'>
           <div class='groupbuy_widget_value'>
              <div class= 'groupbuy_widget_value_value'> 
              	<?php echo $this->price($item->value_deal); echo $this->translate(" Value");?>
              </div>
              <div class = 'groupbuy_widget_value_price'> 
              	<span class = 'groupbuy_widget_value_price_price'>
              	<?php $this->price($item->price) ?>
              	</span> 
              	<span class = "groupbuy_widget_value_price_value">
              	<?php echo $this->translate(" Value");?>
              </span>
              </div>
           </div>
           <div>
          <a href="<?php echo $item->getHref();?>">
           	<?php   echo $item->getImageHtml('deal_thumb_medium','thumb.normal',170,140) ?>
           </a>
    	</div>	
    	</div>
  		
      </div>
      <div class="groupbuy_browse_info">
      	<p class="groupbuy_browse_info_title">
      		<?php echo $this->htmlLink($item->getHref(),$item->getTitle())?>
      	</p>
      	<p class='groupbuy_browse_info_date'>
           <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
           <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
        </p>
      </div>      
      <div class="groupbuy_browse_info_blurb">
                <?php if ($item->getLocation() != '') {
                 	  echo $item->getLocation(); echo ', ';}?>  
              	<?php echo $this->number($item->current_sold); echo $this->translate(" Bought");?>
      
        </div>
              
    </li>
  <?php endforeach; ?>
      <div style="clear: both;">
    </div>

    <div class="link_view" style="display: block; text-align: right;">
        <a href="<?php echo $this->url(array('action'=>'listing' , 'orderby' => 'creation_date'), 'groupbuy_general', true) ;?>">
            <?php echo $this->translate('View all');?>
        </a>
    </div>
</ul>
<?php endif; ?>