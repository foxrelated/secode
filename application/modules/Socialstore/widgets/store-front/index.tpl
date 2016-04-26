<?php 
$this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Socialstore/externals/styles/prettyPhoto.css');
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/jquery-1.4.4.min.js')
       ->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/jquery.prettyPhoto.js')
;
function finalizeUrl($url)
{
	if ($url)
	{
		if (strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE)
		{
			$pageURL = 'http';
			if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
			{
				$pageURL .= "s";
			}
			$pageURL .= "://";
			$pageURL .= $_SERVER["SERVER_NAME"];
			$url = $pageURL . '/'. ltrim( $url, '/');
		}
	}

	return $url;
}
$this->doctype('XHTML1_RDFA');
$this->headMeta() -> setProperty('og:image', finalizeUrl($this->store->getPhotoUrl()));
$this->headMeta() -> setProperty('og:url', finalizeUrl($this->store->getHref()));	
$this->headMeta() -> setProperty('og:type', 'website');	
$this->headMeta() -> setProperty('og:title', $this->store->getTitle());	
$this->headMeta() -> setProperty('og:description', $this->store->getDescription());	
$this->headMeta() -> setProperty('og:updated_time', strtotime($this->store->modified_date));	
?>
<ul>
<?php if (($this->store->deleted == 1) || ($this->store->view_status != 'show' 
		&& ( ($this->isAdmin != 1) && ($this->store->owner_id != $this->viewer_id)) ) 
		|| ($this->store->approve_status != 'approved' 
		&& ( ($this->isAdmin != 1) && ($this->store->owner_id != $this->viewer_id)) )) :?>
	<li><div class="tip"><span><?php echo $this->translate('You cannot view this store.');?></span></div>
</li>
<?php else: ?>

<li>
<h3> <?php echo $this->store->getTitle();?> </h3>
<div class="store_browse_info_date">
	<?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->store->getOwner()->getHref(), $this->store->getOwner()->getTitle()) ?>
	<?php echo $this->timestamp(strtotime($this->store->creation_date)) ?>
</div>
</li>

<li class = "store_front_rate_share">
<div id="store_front_rate">
            <?php for($i = 1; $i <= 5; $i++): ?>
              <img id="rate_<?php print $i;?>"  
              	 src="application/modules/Socialstore/externals/images/<?php if ($i <= $this->store->rate_ave): ?>star_full.png<?php elseif( $i > $this->store->rate_ave &&  ($i-1) <  $this->store->rate_ave): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" />
            <?php endfor; ?>
            <span id='mess_rate'></span>
  </div>
<div class="store_front_share_block">
  	  	<?php echo $this->addThis() ?>	
</div>
</li>

<li class = "gallery_store_front_1">
	<div class="gallery clearfix_front">
        <!-- main photo    -->
         <?php if($this->main_photo): ?>  
                <div class="store_ga_large_photo">
                	<a href="<?php echo $this->store->getPhotoUrl("thumb.profile")?>" 
                		rel="prettyPhoto[gallery2]" 
                		title="<?php echo $this->store->title?>">
                		<img src="<?php echo $this->store->getPhotoUrl("thumb.profile")?>" />
                	</a>
                </div>    
                <div class="store_ga_thumb_photo_front">
                <?php foreach($this->paginator as $photo ): ?>
            <?php if($this->store->photo_id != $photo->file_id && $photo->slideshow == 1): ?>
            	<span class="detaillevel">
            		<a href="<?php echo $photo->getPhotoUrl("thumb.profile")?>" 
            			rel="prettyPhoto[gallery2]" 
            			title="<?php echo $this->store->title?>">
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
  	<div class="store_contact_front_right">
  		<div> <span class = "store_contact_front_title"><?php echo $this->translate('Contact Information');?></span></div>
		<div class="field_listings_front">
			<ul>
				<li>
					<span class="store_front_span_title"><?php echo $this->translate("Contact person") ?></span>
					<span><?php echo $this->store->contact_name;?></span>
				</li>
				<li>
					<span class="store_front_span_title"><?php echo $this->translate("Address") ?></span>
					<span><?php echo $this->store->contact_address;?></span>
				</li>
				<li>
					<span class="store_front_span_title"><?php echo $this->translate("Website") ?></span>
					<span><a href= "<?php echo $this->store->contact_website;?>" target="_blank"><?php echo $this->store->contact_website;?></a></span>
				</li>
				<li>
					<span class="store_front_span_title"><?php echo $this->translate("Email") ?></span>
					<span><a href="mailto:<?php echo $this->store->contact_email;?>"><?php echo $this->store->contact_email;?></a></span>
				</li>
				<li>
					<span class="store_front_span_title"><?php echo $this->translate("Telephone") ?></span>
					<span><?php echo $this->store->contact_phone;?></span>
				</li>
				<li>
					<span class="store_front_span_title"><?php echo $this->translate("Fax") ?></span>
					<span><?php echo $this->store->contact_fax;?></span>
				</li>
				<li>
					<?php echo $this->fieldValueLoop($this->store, $this->fieldStructure) ?>
				</li>
			</ul>
		</div>			
	</div>
	<div>
	<hr class = "store_front_hr">
	</div>
	<div>
		<div> <span class = "store_contact_front_title"><?php echo $this->translate("Short Description")?></span> </div>
		<div class="store_front_description">
		    <?php echo $this->store->getDescription();?>
		</div>
		<div class="store_follow_front">
  			<?php echo $this->follow($this->store) ?>
  		</div>
		<div class="store_front_viewdetail">
		    <?php echo $this->htmlLink(array(
              'store_id'=>$this->store->store_id,
			  'slug'=>$this->store->slug,
              'route' => 'socialstore_detail',
              'reset' => true,
            ), $this->translate('View Details'), array(
            )) ?>
  		</div>
	</div>
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