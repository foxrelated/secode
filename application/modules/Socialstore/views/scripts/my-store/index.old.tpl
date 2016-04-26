<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class="layout_middle">

	<?php 
	$this->headLink()
			->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Socialstore/externals/styles/prettyPhoto.css');
	$this->headScript()
		   ->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/jquery-1.4.4.min.js')
			->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/jquery.prettyPhoto.js');
	?>

	<h3> <?php echo $this->store->getTitle();?> </h3>

	<div class="store_browse_info">
		<p class='store_browse_info_date'>
			<?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->store->getOwner()->getHref(), $this->store->getOwner()->getTitle()) ?>
			<?php echo $this->timestamp(strtotime($this->store->creation_date)) ?>	    
		</p>
	</div>
	<div class="gallery clearfix">
			<!-- main photo    -->
			 <?php if($this->main_photo): ?>  
					<div class="auction_ga_large_photo">
						<a href="<?php echo $this->store->getPhotoUrl()?>" 
							rel="prettyPhoto[gallery2]" 
							title="<?php echo $this->store->title?>">
							<img src="<?php echo $this->store->getPhotoUrl("thumb.profile")?>" />
						</a>
					</div>    
					<div class="auction_ga_thumb_photo">
					<?php foreach($this->paginator as $photo ): ?>
				<?php if($this->store->photo_id != $photo->file_id && $photo->slideshow == 1): ?>
					<span class="detaillevel">
						<a href="<?php echo $photo->getPhotoUrl()?>" 
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

	<div class="store_contact_detail">
	  <div class= "store_detail">	
		<div class="store_contact_detail_left">
			<table class="store_contact_detail_tb">
				<tr>
					<td class="td_contact_detail_info"> <?php echo $this->translate("Contact person:") ?> </td>
					<td> <?php echo $this->store->contact_name;?> </td>
				</tr>
				<tr>
					<td> <?php echo $this->translate("Address:") ?> </td>
					<td> <?php echo $this->store->contact_address;?> </td>
				</tr>
				<tr>
					<td> <?php echo $this->translate("Website:") ?> </td>
					<td> <?php echo $this->store->contact_website;?> </td>
				</tr>
				<tr>
					<td> <?php echo $this->translate("Email:") ?> </td>
					<td> <?php echo $this->store->contact_email;?> </td>
				</tr>
				<tr>
					<td> <?php echo $this->translate("Tel:") ?> </td>
					<td> <?php echo $this->store->contact_phone;?> </td>
				</tr>
				<tr>
					<td> <?php echo $this->translate("Fax:") ?> </td>
					<td> <?php echo $this->store->contact_fax;?> </td>
				</tr>	
			</table>
			<div 				id="deal_rate">
								
						<?php for($i = 1; $i <= 5; $i++): ?>
						  <img id="rate_<?php print $i;?>"   src="application/modules/Socialstore/externals/images/<?php if ($i <= $this->store->rate_ave): ?>star_full.png<?php elseif( $i > $this->store->rate_ave &&  ($i-1) <  $this->store->rate_ave): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" />
						<?php endfor; ?>
			</div>
			  
			<div>
				<?php $this->store->view_count.' views'; ?>
			</div>

			<div class="store_detail_follow">
				<?php echo $this->follow($this->store);?>
				</div>

			<?php echo $this->addThis() ?>	
		</div>
		<div id="company_component_inforbox" style="display:none;">

			 <strong><?php echo $this->translate("Address:")?></strong>
				<span class="desc434"><?php echo $this->store->contact_address?></span>
			<strong><?php echo $this->translate("Phone:")?></strong>
				<span class="desc434"><?php echo $this->store->contact_phone?></span>


		</div>
		
		<div id="google_map_component">        		
			<div class="map_canvas_featured"  title="<?php echo mysql_escape_string($this->store->contact_address) ?>" id="map_canvas" >
			</div>
			<div id="loading_google_map">
				<img src="<?php echo $this->baseUrl(). '/application/modules/Socialstore/externals/images/loading.gif' ?>" />
				<?php echo $this->translate("Loading Google Map ...") ?>
			</div>
					
		</div>	
		




	  </div> 
	  
	  <div class = "store_detail_description">
		<?php echo $this->store->description;?>
	  </div>



		 <?php echo $this->action("list", "comment", "core", array("type"=>"socialstore_store", "id"=>$this->store->getIdentity())) ?>

	</div> 
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

</div>
