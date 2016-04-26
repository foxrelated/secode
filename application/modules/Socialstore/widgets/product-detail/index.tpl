<!--  [if IE]>
<?php 
$this->headScript()
		->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/excanvas.js');       
?>
<![endif]-->
<?php 
$this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Socialstore/externals/styles/prettyPhoto.css')
        //->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Socialstore/externals/styles/inlineplayer.css')
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Socialstore/externals/styles/360player.css')
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Socialstore/externals/styles/mojozoom.css')
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Socialstore/externals/styles/flashblock.css');
$this->headScript()
		->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/berniecode-animator.js')       
		->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/jquery-1.4.4.min.js')
       ->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/soundmanager2.js')
       //->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/inlineplayer.js')
       ->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/360player.js')
       ->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/mojozoom.js')
        ->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/jquery.prettyPhoto.js');
		$this->store = $this->product->getStore();
?>
<script type="text/javascript">
soundManager.url = en4.core.baseUrl + 'application/modules/Socialstore/externals/scripts/';
</script>
<ul>
<?php
if ($this->notViewable == 1) :?>
<li>	
	<div class="tip"><span><?php echo $this->translate('You cannot view this product.');?></span></div>
</li>
<?php else: ?>

<li class = "gallery_store_front_1">
	<div class="gallery clearfix">
        <!-- main photo   -->
         <?php if($this->main_photo): ?>  
                <div class="store_ga_large_photo">
                	<a href="<?php echo $this->product->getPhotoUrl()?>" 
                		rel="prettyPhoto[gallery2]" 
                		title="<?php echo $this->product->title?>">
                		<div id="image_size" class="image_size">
                			<img src="<?php echo $this->product->getPhotoUrl("thumb.profile")?>" data-zoomsrc="<?php echo $this->product->getPhotoUrl()?>" />
                		</div>	
                	</a>
                </div>    
                <div class="store_ga_thumb_photo_info">
                <?php foreach($this->paginator as $photo ): ?>
            <?php if($this->store->photo_id != $photo->file_id && $photo->slideshow == 1): ?>
            	<span class="detaillevel">
            		<a href="<?php echo $photo->getPhotoUrl()?>" 
            			rel="prettyPhoto[gallery2]" 
            			title="<?php echo $this->product->title?>">
            		<img src="<?php echo $photo->getPhotoUrl()?>" width="52px" height="71px" />
            		</a>
            	</span>
            <?php endif; ?>
          <?php endforeach;?>
          </div>
         <?php endif; ?>
	</div>
</li>

<li class = "gallery_store_front_2">

	<h3><?php echo $this->product->getTitle();?> </h3>
	<div class="store_browse_info_date">
		<?php echo $this->timestamp(strtotime($this->product->creation_date)) ?>
		<?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($this->product->getOwner()->getHref(), $this->product->getOwner()->getTitle()) ?>
	</div>

<div class = "store_front_rate_share">
<div id="store_info_rate" <?php if ($this->can_rate): ?> 
  									onmouseout="rating_mouseout()" 
  							<?php elseif ($this->viewer->getIdentity() == 0): ?> 
  									onmouseover ="canNotRate(2);" 
  									onmouseout="canNotRate(0);" 
  							<?php else: ?> 
  									onmouseover ="canNotRate(1);" 
  									onmouseout="canNotRate(0);" 
  							<?php endif;?>  
  							id="store_rate">
            <?php for($i = 1; $i <= 5; $i++): ?>
              <img id="rate_<?php print $i;?>"  <?php if ($this->can_rate): ?> style="cursor: pointer;" onclick="rate(<?php echo $i; ?>);" onmouseover="rating_mousehover(<?php echo $i; ?>);"<?php endif; ?> src="application/modules/Socialstore/externals/images/<?php if ($i <= $this->product->rate_ave): ?>star_full.png<?php elseif( $i > $this->product->rate_ave &&  ($i-1) <  $this->product->rate_ave): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" />
            <?php endfor; ?>
            <?php if(!$this->can_rate): ?> <span id='mess_rate'></span>
  <?php endif; ?>
  </div>  
<div class="store_info_share_block">
  	  	<?php echo $this->addThis() ;?>	
</div>
</div>
	<hr class = "store_front_hr">
	<div class="product_detail">
		<span class="product_detail_text"> <?php echo $this->translate('Price');?></span>
		<?php  
				if ($this->discount == 0) :	?>
				<span class = "product_detail_discount"><?php echo $this->currency($this->product->getPretaxPrice())?> </span>
				<?php else: ?>
				<span class = "product_detail_oldprice"><?php echo $this->currency($this->product->pretax_price)?></span>
				<span class = "product_detail_discount"><?php echo $this->currency($this->discount)?> </span>
				<?php endif;?>
	</div>		
	<br />
	<?php if ($this->product->previewfile_id>0) :?>
		<hr class = "store_front_hr">
	
		<div class="ui360">
		<a href="<?php echo $this->product->getPreiewUrl()?>"><?php echo $this->translate('Preview')?></a>
		</div>
	<?php endif;?>
	<hr class = "store_front_hr">
<div class="product_detail_intro">
	<h3> <?php echo $this->translate('Introduction') ?> </h3>
	<?php echo $this->product->description;?>
</div>
<?php 
if (($this->options && count($this->options) > 0) || (count($this->product->getAttributeText()) > 0)): ?>
<br />
<hr class = "store_front_hr">
<div class = "ynstore_product_attribute">
<h3> <?php echo $this->translate('Options') ?> </h3>
<?php 
if (count($this->options) > 0) :
foreach ($this->options as $type => $option) :?>
<ul class = "ynstore_attr_options">
	<li class = "ynstore_attr_option">
		<span class = "ynstore_attr_label"><?php echo Engine_Api::_()->getApi('attribute','socialstore')->getTypeLabel($type); ?></span>: 
		<span class = "ynstore_attr_opt">
			<select id = "ynstore_attr_option_<?php echo $type?>" onchange = "javascript:en4.socialstore.attrUpdatePrice(<?php echo $type?>,this.options[selectedIndex].value, '<?php Engine_Api::_()->getApi("core","socialstore")->getCurrencySymbol();?>')">
				<?php foreach ($option as $key => $opt) :?>
				<option value = "<?php echo $key?>"><?php echo $opt['label']?></option>
				<?php endforeach;?>
			</select>
		</span>
		<span class = "ynstore_attr_price" id = "ynstore_attr_price_<?php echo $type?>">
			<?php 
				$default_option = Engine_Api::_()->getApi('attribute','socialstore')->getDefaultOption($this->product->product_id, $type);
				if ($default_option != null) {
					echo $this->currency($default_option->adjust_price);
				}
			?>
		</span>
	</li>
</ul>
<?php endforeach; 
		endif;?>
<?php if (count($this->product->getAttributeText()) > 0) : ?>
<?php foreach ($this->product->getAttributeText() as $attrText) : ?>
<ul class = "ynstore_attr_options">
	<li class = "ynstore_attr_textvalue">
		<span class = "ynstore_attr_label"><?php echo Engine_Api::_()->getApi('attribute','socialstore')->getTypeLabel($attrText->type_id); ?></span>: 
		<span class = "ynstore_attr_opt"><?php echo $attrText->value;?></span>
	</li>
</ul>
<?php endforeach;?>
<?php endif;?>
</div>
 <?php endif;?>

<div class="store_product_favourite">
		<?php echo $this->favourite($this->product) ?>
</div>
<div class = "store_product_cart">
		<?php if ($this->stock != 0) : ?>
						<a class="store_product_addtocart" href="javascript:en4.store.cart.addProductBox(<?php echo $this->product->product_id;?>)"><span><?php echo $this->translate("Add To Cart")?></span></a> 
					<?php  else: ?>
						<div class="store_product_outofstock"><span class = "store_outofstock_text"><?php echo $this->translate("Out Of Stock")?></span></div>
					<?php  endif;?>
</div>

<!-- 2012/05/14 -->
<?php
    if(Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection()):
        if($this->product->gda && $this->product->approve_status == 'approved' && $this->viewer->getIdentity()):?>
<br/>
<br/>
<div>
<?php echo $this->htmlLink(array(
                  'action' => 'add-gda',
                  'controller' => 'gda',
                  'productId' => $this->product->product_id,
                  'route' => 'socialstore_extended',
                  'reset' => true,
                ), $this->translate('Request Deal'), array('class' => 'smoothbox store_product_addgda'
                )) ?>
</div>
<?php endif;
    endif;
?>
<!-- 2012/05/14 -->

</li>
<li>
	
	<div style="clear: both">&nbsp;</div>

</li>

<?php endif;?>
</ul>
<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function(){
    jQuery("a[rel^='prettyPhoto']").prettyPhoto();
        
    jQuery(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'normal',theme:'facebook',slideshow:5000, autoplay_slideshow: true});
    jQuery(".gallery:gt(0) a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast',slideshow:10000, hideflash: true});

    jQuery("#custom_content a[rel^='prettyPhoto']:first").prettyPhoto({
            custom_markup: '<div id="map_canvas" style="width:260px; height:265px"></div>',
            changepicturecallback: function(){ initialize(); }
        });

    jQuery("#custom_content a[rel^='prettyPhoto']:last").prettyPhoto({
            custom_markup: '<div id="bsap_1259344" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div><div id="bsap_1237859" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6" style="height:260px"></div><div id="bsap_1251710" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div>',
            changepicturecallback: function(){ _bsap.exec(); }
        });
    });
</script> 

<script type="text/javascript">
    var img_star_full = "application/modules/Socialstore/externals/images/star_full.png";
    var img_star_partial = "application/modules/Socialstore/externals/images/star_part.png";
    var img_star_none = "application/modules/Socialstore/externals/images/star_none.png";  
    var route = '<?php echo $this->route;?>';
    function rating_mousehover(rating) {
        for(var x=1; x<=5; x++) {
          if(x <= rating) {
            $('rate_'+x).src = img_star_full;
          } else {
            $('rate_'+x).src = img_star_none;
          }
        }
    }
     function canNotRate(status) {
     if(status == 1)
     {
            $('mess_rate').innerHTML = '<?php echo $this->translate("You cannot rate!"); ?>';
     }
     else if(status == 2)
     {
            $('mess_rate').innerHTML = '<?php echo $this->translate("You have to log in to rate this product!");?>';
     }
     else
     {
         $('mess_rate').innerHTML = "";
     }
     }
    function rating_mouseout() {
        for(var x=1; x<=5; x++) {
          if(x <= <?php echo $this->product->rate_ave ?>) {
            $('rate_'+x).src = img_star_full;
          } else if(<?php echo $this->product->rate_ave ?> > (x-1) && x > <?php echo $this->product->rate_ave ?>) {
            $('rate_'+x).src = img_star_partial;
          } else {
            $('rate_'+x).src = img_star_none;
          }
        }
    }
    function rate(rates){
        $('store_info_rate').onmouseout = null;
        window.location = en4.core.baseUrl + route + '/product/rate-product/product_id/<?php echo $this->product->getIdentity();?>/rates/'+rates;
      }
  
</script>