//MAP CODE 
//initialize default values
var map;
var infowindow;
var marker;
var mapLoad = true;
function initializeSesVideoMap() {
  var mapOptions = {
    center: new google.maps.LatLng(-33.8688, 151.2195),
    zoom: 17
  };
   map = new google.maps.Map(document.getElementById('map-canvas'),
    mapOptions);

  var input = document.getElementById('locationSes');

  var autocomplete = new google.maps.places.Autocomplete(input);
  autocomplete.bindTo('bounds', map);

   infowindow = new google.maps.InfoWindow();
   marker = new google.maps.Marker({
    map: map,
    anchorPoint: new google.maps.Point(0, -29)
  });

  google.maps.event.addListener(autocomplete, 'place_changed', function() {
    infowindow.close();
    marker.setVisible(false);
    var place = autocomplete.getPlace();
    if (!place.geometry) {
      return;
    }

    // If the place has a geometry, then present it on a map.
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17);  // Why 17? Because it looks good.
    }
    marker.setIcon(/** @type {google.maps.Icon} */({
      url: place.icon,
      size: new google.maps.Size(71, 71),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(17, 34),
      scaledSize: new google.maps.Size(35, 35)
    }));
		document.getElementById('lngSes').value = place.geometry.location.lng();
		document.getElementById('latSes').value = place.geometry.location.lat();
    marker.setPosition(place.geometry.location);
    marker.setVisible(true);

    var address = '';
    if (place.address_components) {
      address = [
        (place.address_components[0] && place.address_components[0].short_name || ''),
        (place.address_components[1] && place.address_components[1].short_name || ''),
        (place.address_components[2] && place.address_components[2].short_name || '')
      ].join(' ');
    }
    infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
    infowindow.open(map, marker);
		return false;
  }); 
	google.maps.event.addDomListener(window, 'load', initializeSesVideoMap);
}
function editMarkerOnMapVideoEdit(){
	geocoder = new google.maps.Geocoder();
	var address = trim(document.getElementById('locationSes').value);
	var lat = document.getElementById('latSes').value;
	var lng = document.getElementById('lngSes').value;
  var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
          map.setZoom(17);
          marker = new google.maps.Marker({
              position: latlng,
              map: map
          });
          infowindow.setContent(results[0].formatted_address);
          infowindow.open(map, marker);
      } else {
        //console.log("Map failed to show location due to: " + status);
      }
    });

}
function editSetMarkerOnMapVideo(){
	geocoder = new google.maps.Geocoder();
	var address = trim(document.getElementById('ses_location_data').innerHTML);
	var lat = document.getElementById('lngSes').value;
	var lng = document.getElementById('latSes').value;
  var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
          map.setZoom(17);
          marker = new google.maps.Marker({
              position: latlng,
              map: map
          });
          infowindow.setContent(results[0].formatted_address);
          infowindow.open(map, marker);
      } else {
        //console.log("Map failed to show location due to: " + status);
      }
    });

}
//list page map 
function initializeSesVideoMapList() {
if(mapLoad){
  var mapOptions = {
    center: new google.maps.LatLng(-33.8688, 151.2195),
    zoom: 17
  };
   map = new google.maps.Map(document.getElementById('map-canvas-list'),
    mapOptions);
}
  var input =document.getElementById('locationSesList');

  var autocomplete = new google.maps.places.Autocomplete(input);
if(mapLoad)
  autocomplete.bindTo('bounds', map);

if(mapLoad){
   infowindow = new google.maps.InfoWindow();
   marker = new google.maps.Marker({
    map: map,
    anchorPoint: new google.maps.Point(0, -29)
  });
}
  google.maps.event.addListener(autocomplete, 'place_changed', function() {
	
	if(mapLoad){
    infowindow.close();
    marker.setVisible(false);
	}
    var place = autocomplete.getPlace();
    if (!place.geometry) {
      return;
    }
	if(mapLoad){
    // If the place has a geometry, then present it on a map.
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17);  // Why 17? Because it looks good.
    }
    marker.setIcon(/** @type {google.maps.Icon} */({
      url: place.icon,
      size: new google.maps.Size(71, 71),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(17, 34),
      scaledSize: new google.maps.Size(35, 35)
    }));
	}
		document.getElementById('lngSesList').value = place.geometry.location.lng();
		document.getElementById('latSesList').value = place.geometry.location.lat();
if(mapLoad){
    marker.setPosition(place.geometry.location);
    marker.setVisible(true);
}
    var address = '';
    if (place.address_components) {
      address = [
        (place.address_components[0] && place.address_components[0].short_name || ''),
        (place.address_components[1] && place.address_components[1].short_name || ''),
        (place.address_components[2] && place.address_components[2].short_name || '')
      ].join(' ');
    }
  if(mapLoad){
	  infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
    infowindow.open(map, marker);
		return false;
	}
	}); 
	if(mapLoad){
		google.maps.event.addDomListener(window, 'load', initializeSesVideoMapList);
	}
}

function editSetMarkerOnMapListVideo(){
	geocoder = new google.maps.Geocoder();
if(mapLoad){
	if(document.getElementById('ses_location_data_list'))
		var address = trim(document.getElementById('ses_location_data_list').innerHTML);
}else{
	if(document.getElementById('locationSesList'))
		var address = trim(document.getElementById('locationSesList').innerHTML);	
}
	var lat = document.getElementById('lngSesList').value;
	var lng = document.getElementById('latSesList').value;
  var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
          map.setCenter(results[0].geometry.location);
          marker = new google.maps.Marker({
              position: results[0].geometry.location,
              map: map
          });
          infowindow.setContent(results[0].formatted_address);
          infowindow.open(map, marker);
      } else {
        //console.log("Map failed to show location due to: " + status);
      }
    });
}
function openURLinSmoothBox(openURLsmoothbox){
	Smoothbox.open(openURLsmoothbox);
	parent.Smoothbox.close;
	return false;
}
function showTooltip(x, y, contents, className) {
	if(sesJqueryObject('.sesbasic_notification').length > 0)
		sesJqueryObject('.sesbasic_notification').hide();
	sesJqueryObject('<div class="sesbasic_notification '+className+'">' + contents + '</div>').css( {
		display: 'block',
	}).appendTo("body").fadeOut(5000,'',function(){
		sesJqueryObject(this).remove();	
	});
}
sesJqueryObject(document).on('click','#sesLightboxLikeUnlikeButtonVideo',function(){
	 if(!checkRequestmoduleIsVideo()){
			 return;
	 };
		sesJqueryObject('#comments .comments_options').find("a:eq(1)").trigger('click');		
		return false;
});
function trim(str, chr) {
  var rgxtrim = (!chr) ? new RegExp('^\\s+|\\s+$', 'g') : new RegExp('^'+chr+'+|'+chr+'+$', 'g');
  return str.replace(rgxtrim, '');
}
sesJqueryObject(document).on('click','#openVideoInLightbox',function(){
	var URL = window.location.href;
	URL = URL.replace(videoURLsesvideo,videoURLsesvideo+'/imageviewerdetail')
	var image = sesJqueryObject('#sesvideo_image_video_url').attr('data-src');
	getRequestedVideoForImageViewer(image,URL);
});
sesJqueryObject(document).on('click','.ses-video-viewer',function(e){
		e.preventDefault();
});
function checkRequestmoduleIsVideo(){
	if(sesJqueryObject('#ses_media_lightbox_container_video').length > 0)
		return true;
	else
		return false;
}
sesJqueryObject(document).on('click','.sesbasic_form_opn',function(e){
		 sesJqueryObject(this).parent().parent().find('form').show();
		 sesJqueryObject(this).parent().parent().find('form').focus();
		 var widget_id = sesJqueryObject(this).data('rel');
		 if(widget_id)
				eval("pinboardLayout_"+widget_id+"()");			
});
//send quick share link
function sessendQuickShare(url){
	if(!url)
		return;
	sesJqueryObject('.sesbasic_popup_slide_close').trigger('click');
	(new Request.HTML({
      method: 'post',
      'url': url,
      'data': {
        format: 'html',
				is_ajax : 1
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        //keep Silence
				showTooltip('10','10','<i class="fa fa-envelope"></i><span>'+(en4.core.language.translate("Quick share successfully"))+'</span>','sesbasic_message_notification');
      }
    })).send();
}
//check embed function exists for message
function checkFunctionEmbed(){
	if( typeof flashembed == 'function'){
		//silence
	}else{
		 if(sesJqueryObject('.sesvideo_attachment_info').length){
				var href = sesJqueryObject('.sesvideo_attachment_info').find('a').attr('href');
				window.location.href = href;
				return false;
		 }
	}
	return;	
}
//open url in smoothbox
function opensmoothboxurl(openURLsmoothbox){
	Smoothbox.open(openURLsmoothbox);
	parent.Smoothbox.close;
	return false;
}
sesJqueryObject(document).on('click','.sesvideo_list_option_toggle',function(){
  if(sesJqueryObject(this).hasClass('open')){
    sesJqueryObject(this).removeClass('open');
  }else{
    sesJqueryObject(this).addClass('open');
  }
    return false;
});
//add to watch later function ajax.
sesJqueryObject(document).on('click','.sesvideo_watch_later',function(){
		var that = this;
		if(!sesJqueryObject(this).attr('data-url'))
			return;
		if(sesJqueryObject(this).hasClass('selectedWatchlater')){
				sesJqueryObject(this).removeClass('selectedWatchlater');
		}else
				sesJqueryObject(this).addClass('selectedWatchlater');
		 (new Request.HTML({
      method: 'post',
      'url':  en4.core.baseUrl + 'sesvideo/watchlater/add/',
      'data': {
        format: 'html',
        id: sesJqueryObject(this).attr('data-url'),
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        var response =jQuery.parseJSON( responseHTML );
				if(response.error)
					alert(en4.core.language.translate('Something went wrong,please try again later'));
				else{
					if(response.status == 'delete'){
						showTooltip('10','10','<i class="fa fa-clock-o"></i><span>'+(en4.core.language.translate("Video removed successfully from watch later"))+'</span>');
							sesJqueryObject(that).removeClass('selectedWatchlater');
					}else{
						showTooltip('10','10','<i class="fa fa-clock-o"></i><span>'+(en4.core.language.translate("Video successfully added to watch later"))+'</span>','sesbasic_watchlater_notification');
							sesJqueryObject(that).addClass('selectedWatchlater');
					}
				}
					return true;
      }
    })).send();
		
});
//common function for like comment ajax
function like_favourite_data(element,functionName,itemType,likeNoti,unLikeNoti,className){
		if(!sesJqueryObject(element).attr('data-url'))
			return;
		if(sesJqueryObject(element).hasClass('button_active')){
				sesJqueryObject(element).removeClass('button_active');
		}else
				sesJqueryObject(element).addClass('button_active');
		 (new Request.HTML({
      method: 'post',
      'url':  en4.core.baseUrl + 'sesvideo/index/'+functionName,
      'data': {
        format: 'html',
        id: sesJqueryObject(element).attr('data-url'),
				type:itemType,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        var response =jQuery.parseJSON( responseHTML );
				if(response.error)
					alert(en4.core.language.translate('Something went wrong,please try again later'));
				else{
					sesJqueryObject(element).find('span').html(response.count);
					if(response.condition == 'reduced'){
							sesJqueryObject(element).removeClass('button_active');
							showTooltip(10,10,unLikeNoti)
							return true;
					}else{
							sesJqueryObject(element).addClass('button_active');
							showTooltip(10,10,likeNoti,className)
							return false;
					}
				}
      }
    })).send();
}
sesJqueryObject(document).on('click','.sesvideo_favourite_sesvideo_video',function(){
	like_favourite_data(this,'favourite','sesvideo_video','<i class="fa fa-heart"></i><span>'+(en4.core.language.translate("Video added as Favourite successfully"))+'</span>','<i class="fa fa-heart"></i><span>'+(en4.core.language.translate("Video Unfavourited successfully"))+'</span>','sesbasic_favourites_notification');
});
sesJqueryObject(document).on('click','.sesvideo_like_sesvideo_video',function(){
	like_favourite_data(this,'like','sesvideo_video','<i class="fa fa-thumbs-up"></i><span>'+(en4.core.language.translate("Video Liked successfully"))+'</span>','<i class="fa fa-thumbs-up"></i><span>'+(en4.core.language.translate("Video Unliked successfully"))+'</span>','sesbasic_liked_notification');
});
sesJqueryObject(document).on('click','.sesvideo_like_sesvideo_playlist',function(){
	like_favourite_data(this,'like','sesvideo_playlist','<i class="fa fa-thumbs-up"></i><span>'+(en4.core.language.translate("Playlist Liked successfully"))+'</span>','<i class="fa fa-thumbs-up"></i><span>'+(en4.core.language.translate("Playlist Unliked successfully"))+'</span>','sesbasic_liked_notification');
});
sesJqueryObject(document).on('click','.sesvideo_favourite_sesvideo_playlist',function(){
	like_favourite_data(this,'favourite','sesvideo_playlist','<i class="fa fa-heart"></i><span>'+(en4.core.language.translate("Playlist added as Favourite successfully"))+'</span>','<i class="fa fa-heart"></i><span>'+(en4.core.language.translate("Playlist Unfavourited successfully"))+'</span>','sesbasic_favourites_notification');		
});
sesJqueryObject(document).on('click','.sesvideo_favourite_sesvideo_chanel',function(){
	like_favourite_data(this,'favourite','sesvideo_chanel','<i class="fa fa-heart"></i><span>'+(en4.core.language.translate("Channel added as Favourite successfully"))+'</span>','<i class="fa fa-heart"></i><span>'+(en4.core.language.translate("Channel Unfavourited successfully"))+'</span>','sesbasic_favourites_notification');
});
sesJqueryObject(document).on('click','.sesvideo_like_sesvideo_chanel',function(){
	like_favourite_data(this,'like','sesvideo_chanel','<i class="fa fa-thumbs-up"></i><span>'+(en4.core.language.translate("Channel Liked successfully"))+'</span>','<i class="fa fa-thumbs-up"></i><span>'+(en4.core.language.translate("Channel Unliked successfully"))+'</span>','sesbasic_liked_notification');
});
sesJqueryObject(document).on('click','.sesvideo_favourite_sesvideo_artist',function(){
	like_favourite_data(this,'favourite','sesvideo_artist','<i class="fa fa-heart"></i><span>'+(en4.core.language.translate("Artist added as Favourite successfully"))+'</span>','<i class="fa fa-heart"></i><span>'+(en4.core.language.translate("Artist Unfavourited successfully"))+'</span>','sesbasic_favourites_notification');
});
sesJqueryObject(document).on('click','.sesvideo_chanel_follow',function(){
		if(!sesJqueryObject(this).attr('data-url'))
			return;
		if(sesJqueryObject(this).hasClass('button_active')){
				sesJqueryObject(this).removeClass('button_active');
				if(sesJqueryObject(this).hasClass('button_chanel'))
					sesJqueryObject(this).html(en4.core.language.translate("Follow"));
		}else{
				sesJqueryObject(this).addClass('button_active');
				if(sesJqueryObject(this).hasClass('button_chanel'))
					sesJqueryObject(this).html(en4.core.language.translate("Un-Follow"));
		}
			var that = this;
		 (new Request.HTML({
      method: 'post',
      'url':  en4.core.baseUrl + "sesvideo/chanel/follow/chanel_id/"+sesJqueryObject(this).attr('data-url'),
      'data': {
        format: 'html',
        chanel_id:sesJqueryObject(this).attr('data-url'),
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        var response =jQuery.parseJSON( responseHTML );
				if(response.error)
					alert(en4.core.language.translate('Something went wrong,please try again later'));
				else{
					sesJqueryObject(that).find('span').html(response.count);
					if(response.condition == 'reduced'){
							showTooltip('10','10','<i class="fa fa-check"></i><span>'+(en4.core.language.translate("Channel un-follow successfully"))+'</span>','sesbasic_favourites_notification');
							sesJqueryObject(that).removeClass('button_active');
							if(sesJqueryObject(that).hasClass('button_chanel'))
								sesJqueryObject(that).html(en4.core.language.translate("Follow"));
					}else{
							sesJqueryObject(that).addClass('button_active');
							showTooltip('10','10','<i class="fa fa-check"></i><span>'+(en4.core.language.translate("Channel follow successfully"))+'</span>','sesbasic_follow_notification');
							if(sesJqueryObject(that).hasClass('button_chanel'))
								sesJqueryObject(that).html(en4.core.language.translate("Un-Follow"));
					}
				}
					return true;
      }
    })).send();
});