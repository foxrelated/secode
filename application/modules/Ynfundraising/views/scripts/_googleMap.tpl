<?php
$location = Ynfundraising_Model_DbTable_Countries::getCountryName(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfundraising.country', 'VNM'));
?>
<div style="padding: 10px 10px 10px 0px;" class="refresh_map">
<a href="javascript:;" onclick="refresh_map()"><?php echo $this->translate("Refresh map")?></a>
</div>
<div id="ynfundraising_google_map_component">        		
    
</div>
<div>
	<a href="javascript:;" onclick="action_add()"><?php echo $this->translate("Add address/city/zip/country");?></a>
</div>
<script type="text/javascript">
	var update_map_location =function(title)
	{
		title = "," + title;
		var q = title.replace(/\,+/,'');		
		var src = "https://maps.google.com/maps?q="+ q +"&amp;hnear="+ q +"&amp;t=m&amp;ie=UTF8&amp;z=12&amp;output=embed";
		var html = '<iframe id="gmap" name="gmap" width="425" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'+src+'"></iframe>';
		document.getElementById('ynfundraising_google_map_component').innerHTML  = html;
		
	} 
	var refresh_map = function()
	{
		var country = $('country').value;
		if(country == "")
			country = $('location').value;
		var title = $('address').value + "," + $('city').value + "," + country + "," + $('zip_code').value;
		update_map_location(title);	
	}
	var action_add = function()
	{
		parent.$('location').value = $('location').value;
		var country = $('country').value;
		if(country == "")
			country = $('location').value
		parent.$('address').value = $('address').value + "," + $('city').value + "," + country + "," + $('zip_code').value;;
		parent.Smoothbox.close()
	}
	$(window).addEvent('domready', function() {
		var location_str = parent.$('location').value;
		var title = parent.$('address').value;
		var arr_address = title.split(",");
		
		if(arr_address.length > 1)
		{
			$('address').value = arr_address[0];
			$('city').value = arr_address[1];
			$('country').value = arr_address[2];
			$('zip_code').value = arr_address[3];
		}
		else
		{
			$('country').value = '<?php echo $location?>';
		}
		
		if(location_str == "")
		{
			location_str = '<?php echo $location;?>';
		}

		$('location').set('value',location_str);
		
		if(title == "")
		{
			title = location_str;
		}
		update_map_location(title);
	});
</script>