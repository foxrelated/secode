<div id="location-wrapper" class="form-wrapper">
	<div id="location-label" class="form-label">
		<label>
			<?php echo $this->translate('Address*');?>
		</label>
	</div>
	<div id="location-element" class="form-element">
		<input type="text" name="location" id="location" value="">
		<a class='ynlistings_location_icon' href="javascript:void()" onclick="return getCurrentLocation(this);" >
			<img src="application/modules/Ynlistings/externals/images/icon-search-advform.png">
		</a>			
	</div>
</div>

