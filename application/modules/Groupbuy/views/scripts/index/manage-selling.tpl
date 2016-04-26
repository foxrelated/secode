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
          <?php if($item->published <= 10 && $item->status < 30): ?>
          <?php if ($item->isEditable()): ?> 
            <?php echo $this->htmlLink(array(
              'action' => 'edit',
              'deal' => $item->getIdentity(),
              'route' => 'groupbuy_general',
              'reset' => true,
            ), $this->translate('Edit Deal'), array(
              'class' => 'buttonlink icon_groupbuy_edit',
            )) ?>
            <?php endif; ?>
             <?php echo $this->htmlLink(array(
              'route' => 'groupbuy_extended',
              'controller' => 'photo',
              'action' => 'upload',
              'deal_id' => $item->getIdentity(),
            ), $this->translate('Add Photos'), array(
              'class' => 'buttonlink icon_groupbuy_photo_new'
          )) ?>
            <?php
            endif;
             if($item->published == 0): ?>
            <?php echo $this->htmlLink(array(
              'action' => 'publish',
              'deal' => $item->getIdentity(),
              'route' => 'groupbuy_general',
              'reset' => true,
              'session_id' => session_id(),     
            ), $this->translate('Publish Deal'), array(
              'class' => 'buttonlink icon_groupbuy_online',
            )) ?>
          <?php endif; ?>
          <?php if($item->published > 10): ?>
           <?php echo $this->htmlLink(array(
              'action' => 'statistic',
              'deal' => $item->getIdentity(),
              'route' => 'groupbuy_general',
              'reset' => true,    
            ), $this->translate('Statistics'), array(
              'class' => 'buttonlink icon_groupbuy_statistic',
            )) ?>
            <?php  endif;?> 
          <?php if ($item->isDeleteable() && $item->status != 30): ?> 
            <?php echo $this->htmlLink(array(
              'action' => 'delete',
              'deal' => $item->getIdentity(),
              'route' => 'groupbuy_general',
              'reset' => true,
            ), $this->translate('Delete Deal'), array(
              'class' => 'buttonlink smoothbox icon_groupbuy_delete',
            )) ?>
          <?php  endif;?> 
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
              	<?php echo $this->discount( $item->discount) ?>
              </span>
              <br />
              <span> 
                  <?php echo $this->translate("Min Sold:");?>
              </span>
              <span>
              	<?php echo $this->number($item->min_sold); ?>	
              </span>
              -
              <span> 
                  <?php echo $this->translate("Max Sold:");?>
              </span>
              <span>
              	<?php echo $this->number($item->max_sold); ?>	
              </span>
              -
              <span> 
                  <?php echo $this->translate("Sold:");?>
              </span>
              <span>
              	<?php echo $this->number($item->current_sold); ?>	
              </span>
              <br />
              <span> 
                  <?php echo $this->translate("Status:");?>
              </span>
              <span>
                   <?php echo $this->translate($item->getStatusString()) ?>
              </span> 
              <br />
              <span> 
                  <?php echo $this->translate("Published:");?>
              </span>
              <span>
                  <?php echo $this->translate($item->getPublishedString()) ?>
              </span> 
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
              <br /> 
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
              
                <?php // $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
              <?php //echo $this->fieldValueLoop($item, $fieldStructure) ?>
            </div>
          </div>
           <div class='groupbuy_list_description'>
                  <?php // echo $this->string()->truncate($this->string()->stripTags($item->description), 300)
                  ?>
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
        <?php if( $this->canCreate ): ?>
          <?php echo $this->translate('Get started by %1$sposting%2$s a new deal.', '<a href="'.$this->url(array('action' => 'create'), 'groupbuy_general').'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
   <?php echo $this->paginationControl($this->paginator, null, array("pagination/dealpagination.tpl","groupbuy"), array("orderby"=>$this->orderby)); ?>

</div>

