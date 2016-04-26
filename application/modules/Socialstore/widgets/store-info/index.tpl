<?php 
	
        ?>
<ul>
<?php if (($this->store->view_status != 'show' 
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

<br/>
<div class="store_detail_description">
		    <?php echo $this->store->getDescription();?>
</div>

</li>
<hr class = "store_front_hr">
<li>
  	<div id="store_google_map_component">
		<div style= "width: 372px" class="store_map_canvas_featured"  title="<?php echo mysql_escape_string($this->store->contact_address) ?>" id="map_canvas" >
		</div>
		<div id="store_loading_google_map">
			<img src="<?php echo $this->baseUrl(). '/application/modules/Socialstore/externals/images/loading.gif' ?>" />
			<?php echo $this->translate("Loading Google Map ...") ?>
		</div>
	</div>
	<div class="store_contact_detail_left">
		<div class="field_listings_info">
			<ul>
				<li>
					<span class="store_span_info_title"><?php echo $this->translate("Store") ?></span>
					<span><?php echo $this->store;?></span>
				</li>
				<li>
					<span class="store_span_info_title"><?php echo $this->translate("Contact Person") ?></span>
					<span><?php echo $this->store->contact_name;?></span>
				</li>
				<li>
					<span class="store_span_info_title"><?php echo $this->translate("Address") ?></span>
					<span><?php echo $this->store->contact_address;?></span>
				</li>
				<li>
					<span class="store_span_info_title"><?php echo $this->translate("Website") ?></span>
					<span><a href= "<?php echo $this->store->contact_website;?>" target="_blank"><?php echo $this->store->contact_website;?></a></span>
				</li>
				<li>
					<span class="store_span_info_title"><?php echo $this->translate("Email") ?></span>
					<span><a href="mailto:<?php echo $this->store->contact_email;?>"><?php echo $this->store->contact_email;?></a></span>
				</li>
				<li>
					<span class="store_span_info_title"><?php echo $this->translate("Telephone") ?></span>
					<span><?php echo $this->store->contact_phone;?></span>
				</li>
				<li>
					<span class="store_span_info_title"><?php echo $this->translate("Fax") ?></span>
					<span><?php echo $this->store->contact_fax;?></span>
				</li>
			</ul>
		</div>
		
	</div>
	<div style="clear: both">&nbsp;</div>
	<div id="company_component_inforbox" style="display:none;">
     <strong><?php echo $this->translate("Address:")?></strong>
     	<span class="desc434"><?php echo $this->store->contact_address?></span>
	<strong><?php echo $this->translate("Phone:")?></strong>
   		<span class="desc434"><?php echo $this->store->contact_phone?></span>
	</div>
  </li>

<li>
  <?php endif;?>
  </ul>
<script type="text/javascript">
document.getElementsByClassName('tab_layout_socialstore_store_info')[0].firstChild.addEvent('click',function(){
	google.maps.event.trigger($('map_canvas'),'resize');
});
$(window).addEvent('domready', function() {
	// viewGoogleMap('map_canvas');
	viewGoogleMapFromAddress('map_canvas');
});
</script>