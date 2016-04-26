en4.groupbuy = {
	removeCategory : function(a, b) {
		var d = $('add_more_id_' + ( a - 1));
		if(d) {
			d.show();
		}
		var c = $('category_id_' + a);
		if(c) {
			c.selectedIndex = 0;
		}
		var e = $('category_' + a + '-wrapper');
		if(e) {
			e.hide();
		}
	},
	addMoreCategory : function(a, b) {
		var c = $('add_more_id_' + a);
		if(c) {
			c.hide();
		}
		var e = $('category_' + b + '-wrapper');
		if(e) {
			e.show();
		}
	},
	valform : function val(f) {
		var f = f ? f : $('subscription_email_form');
		var data = {};
		var cat = f.category_id;
		var loc = f.location_id;
		var age = f.age;
		var email = f.email;
		data.category_id = cat.options[cat.selectedIndex].value;
		data.location_id = loc.options[loc.selectedIndex].value;
		data.age = age.value.trim();
		data.email = email.value.trim();
		data.url = f.action;
		return data;
	},
	validateSubscription : function(f, warn) {
		var f = f ? f : $('subscription_email_form');
		var data = en4.groupbuy.valform(f);
		if(!data.email.test(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/)) {
			var warning = 'Invalid email address.';
            warn();
			f.email.focus();
			return false;
		}

		if(!data.category_id) {
			warn("Select a category.");
			f.category_id.focus();
			return false;
		}

		if(!data.location_id) {
			warn("Select a location.");
			f.location_id.focus();
			return false;
		}
		if(!data.age.test(/^\d{1,2}$/)) {
			warn("Type your age.");
			f.age.focus();
			return false;
		}

		if(data.age < 2 || data.age > 99) {
			warn("Type your age ");
			f.age.focus();
			return false;
		}

		//f.subscribe.disabled = "disabled";
		return data;
	},
	subscribeEmail : function(f) {
		function warn(msg) {
			$$('form#subscription_email_form p.form-description')[0].innerHTML = msg;
		}

		var data = en4.groupbuy.validateSubscription(f, warn);
		if(!data) {
			return false
		};
		data.format = 'json';
		var request = new Request.JSON({
			'url' : data.url,
			'data' : data,
			onComplete : function(res) {
				console.log(res);
				if(res.error == false) {
					$('subscription_email_form').innerHTML = res.message;
				} else {
					warn(res.message);
					$('subscription_email_form').subscribe.disable = false;
				}

			}
		});
		request.send();
		return false;
	}
}

var initMap = function() {
	var position = {
		lat : 40.675658,
		lng : -73.995287
	};
	if($('longitude') && $('longitude').value) {
		position.lat = parseFloat($('latitude').value);
		position.lng = parseFloat($('longitude').value);
	}
	var myLatlng = new google.maps.LatLng(position.lat, position.lng);
	var myOptions = {
		zoom : 15,
		center : myLatlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	};
	var mapEle = document.createElement('DIV');
	mapEle.id = 'map_canvas_edit';
	$('latitude-element').appendChild(mapEle);
	function deleteMarker() {
		if(marker) {
			marker.setMap(null);
			marker = null;
		}
	}

	function resetMarker(pos) {
		deleteMarker();
		marker = new google.maps.Marker({
			position : pos,
			animation : google.maps.Animation.DROP,
			draggable : true,
			map : map,
			title : "Drag this marker to set position of your deal!"
		});
		updatePosition(pos);
		return marker;
	}

	var map = new google.maps.Map(document.getElementById('map_canvas_edit'), myOptions);

	var input = document.getElementById('address');
	var autocomplete = new google.maps.places.Autocomplete(input);

	var marker = resetMarker(myLatlng);

	google.maps.event.addListener(autocomplete, 'place_changed', function() {
		//infowindow.close();
		var place = autocomplete.getPlace();
		if(place.geometry.viewport) {
			deleteMarker();
			map.fitBounds(place.geometry.viewport);
		} else {
			var pos = place.geometry.location;
			marker = resetMarker(pos);
			map.setCenter(pos);
			map.setZoom(17);
			// Why 17? Because it looks good.
		}
	});
	function showInfo(msg) {

	}

	// Add dragging event listeners.
	google.maps.event.addListener(marker, 'dragstart', function() {
		showInfo('Dragging...');
	});
	google.maps.event.addListener(map, "rightclick", function(event) {resetMarker(event.latLng);
	});
	function updatePosition(pos) {
		if(pos && pos.lat && pos.lng) {
			$('latitude').value = pos.lat();
			$('longitude').value = pos.lng();
		}
	};

	google.maps.event.addListener(marker, 'dragend', function() {
		updatePosition(marker.getPosition());
	});
}
function viewGoogleMap(canvasId) {
	var ele = $(canvasId);
	if(ele == null || ele == undefined) {
		return;
	}
	var lat = ele.getAttribute('latitude');
	var lng = ele.getAttribute('longitude');
	if(lat) {
		lat = parseFloat(lat)
	}
	if(lng) {
		lng = parseFloat(lng)
	}
	if(!lat || !lng) {
		ele.innerHTML = "no position associate with this deal!";
		return;
	}
	var myLatlng = new google.maps.LatLng(lat, lng);
	var myOptions = {
		zoom : 13,
		center : myLatlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById(canvasId), myOptions);
	var marker = new google.maps.Marker({
		position : myLatlng,
		map : map,
		title : "Deal Position"
	});
}

$(window).addEvent('domready', function() {
	//viewGoogleMap('map_canvas');
	viewGoogleMapFromAddress('map_canvas');
});
function viewGoogleMapFromAddress(canvasId) {
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
		if(status == google.maps.GeocoderStatus.OK && results&& results.length){
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
		        content: $('company_component_inforbox').innerHTML
		    });
		    google.maps.event.addListener(marker, 'click', function() {
		      infowindow.open(map,marker);
		    });
			$('groupbuy_loading_google_map').style.display='none';
		} else {
			$('groupbuy_loading_google_map').innerHTML = 'Invalid Address!';	
		}
		
	}
	matchGeoCoder(request);
}