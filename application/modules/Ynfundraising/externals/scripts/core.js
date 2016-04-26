function viewGoogleMapFromAddress(canvasId) 
{
	var position = {
		lat : 40.675658,
		lng : -73.995287
	};
	var ele = $(canvasId);
	if(ele == null || ele == undefined) {
		return;
	}
	var request = {
		address: ele.getAttribute('title') + ' - ' + ele.getAttribute('location')
	};
	var myLatlng = new google.maps.LatLng(position.lat, position.lng);
	var myOptions = {
		zoom : 15,
		center : myLatlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	};
	var map = null; 
	geocoder = new google.maps.Geocoder();
	
	function matchGeoCoder(request) {
		geocoder.geocode(request, showResults);
	}
	function showResults(results, status) {
		if(status == google.maps.GeocoderStatus.OK && results&& results.length)
		{
			var result =  results[0];			
			var latlng =  result.geometry.location;
			map =  new google.maps.Map(document.getElementById(canvasId), {
				zoom : 15,
				center : latlng,
				mapTypeId : google.maps.MapTypeId.ROADMAP
			});
			var marker = new google.maps.Marker({
				position : latlng,
				map : map,
				title : result.formatted_address
			});
			 var infowindow = new google.maps.InfoWindow({
		        //content: $('company_component_inforbox').innerHTML
		    });
		    google.maps.event.addListener(marker, 'click', function() {
		      infowindow.open(map,marker);
		    });
		} 
		
	}
	matchGeoCoder(request);
}