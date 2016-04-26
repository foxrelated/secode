<?php
$this->headScript()
		->appendFile('//maps.googleapis.com/maps/api/js?sensor=false&libraries=places')
        ->appendFile($this->baseUrl() . '/application/modules/Ynfundraising/externals/scripts/core.js');
?>
<ul>
	<h4><span><?php echo $this->translate("Short description")?></span></h4>
	<p class="ynFRaising_Detaildesc">
		<?php echo wordwrap($this->campaign->short_description, 105, "\n", true)?>
	</p>
	<?php if($this->campaign->address):?>
	<h4><span><?php echo $this->translate("Location")?></h4></span>
	<strong><?php echo $this->campaign->address;?></strong>
	<div>
		<div id="ynfundraising_google_map_component">        		
			<div class="ynfundraising_map_canvas_featured" location="<?php echo $this->campaign->location?>" title="<?php echo $this->campaign->address?>" id="map_canvas">
			</div>
		</div>
	</div>
	<?php endif;?>
	<div class="ynFRaising_Detaildesc">
		<?php echo wordwrap($this->campaign->main_description, 105, "\n", true)?>
	</div>
	<div class="ynFRaising_DetailComment">
		<?php echo $this->action("list", "comment", "core", array("type"=>"ynfundraising_campaign", "id"=>$this->campaign->getIdentity())) ?>
	</div>
</ul>
<script type="text/javascript">
	$(window).addEvent('domready', function() {
		viewGoogleMapFromAddress('map_canvas');
	});
</script>
