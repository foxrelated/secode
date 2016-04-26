/* $Id: sesvideoimageviewerbasic.js  2015-6-16 00:00:000 SocialEngineSolutions $ */
var dataCommentSes = '';
	// store the default browser URL for change state after closing image viewer
	var defaultHashURL = '';
	defaultHashURL = document.URL;
	var firstStartPoint = 0;
	firstStartPointModule = 0;
	var getTagData;
	var mediaTags ;
function defaultLayourForVideoPopup(){
	sesJqueryObject('<div id="ses_media_lightbox_container_video" class="ses_media_lightbox_container_video"><div class="ses_media_lightbox_overlay" id="crossSes"></div> <div class="ses_media_lightbox_content"> <div class="ses_media_lightbox_left"><div class="ses_media_lightbox_item_wrapper "><div class="ses_media_lightbox_item"><img id="video_data_lightbox" src="" alt="" /></div></div> <div class="ses_media_lightbox_nav_btns"><a id="nav-btn-next" style="display:none" class="ses_media_lightbox_nav_btn_next" ></a><a id="nav-btn-prev" class="ses_media_lightbox_nav_btn_prev" style="display:none;" ></a></div> </div><div class="ses_media_lightbox_information"></div><a href="javascript:;" id="fsbutton"  class="cross ses_media_lightbox_close_btn"><i class="fa fa-close sesbasic_text_light"></i></a></div></div>').appendTo('body');	
}
function getRequestedVideoForImageViewer(imageURL,requestedURL){
	defaultLayourForVideoPopup();
	if(openVideoInLightBoxsesVideo == 0){
		window.location.href = requestedURL.replace(videoURLsesvideo+'/imageviewerdetail',videoURLsesvideo);
		return;
	}
	sesJqueryObject('#ses_media_lightbox_container_video').show();
	sesJqueryObject('body').css({ 'overflow': 'hidden' });
 //check function call from image viewer or direct
 if(!dataCommentSes){
		dataCommentSes = sesJqueryObject('.layout_core_comments').html();
		getTagData = sesJqueryObject('#media_photo_div').find('*[id^="tag_"]');
		sesJqueryObject('#media_photo_div').find('*[id^="tag_"]').remove();
		mediaTags =	sesJqueryObject('#media_tags').html();
		sesJqueryObject('#media_tags').html('');
	}
	sesJqueryObject('.layout_core_comments').html('');
	history.pushState(null, null, requestedURL.replace(videoURLsesvideo+'/imageviewerdetail',videoURLsesvideo));
	var height = sesJqueryObject('.ses_media_lightbox_content').height();
	var width = sesJqueryObject('.ses_media_lightbox_left').width();
	sesJqueryObject('#media_photo_next_ses').css('height',height +'px');
	sesJqueryObject('#video_data_lightbox').css('max-height',height +'px');
	sesJqueryObject('#video_data_lightbox').css('max-width',width+'px');
	sesJqueryObject('#video_data_lightbox').attr('src',imageURL);
	sesJqueryObject('.ses_media_lightbox_information').html('');
	sesJqueryObject('.ses_media_lightbox_options').remove();
	sesJqueryObject('#nav-btn-prev').hide();
	sesJqueryObject('.ses_media_lightbox_nav_btn_next').css('display','none');
	requestedURL = changeurlSesVideo(requestedURL);
	getVideoViewerObjectData(imageURL,requestedURL);	
}
function changeurlSesVideo(url){
	if(url.search('imageviewerdetail') == -1){
	  url = url.replace(videoURLsesvideo,videoURLsesvideo+'/imageviewerdetail');
	}
		return url;
}
//Close image viewer
sesJqueryObject(document).on('click','.ses_media_lightbox_overlay, #crossSes, .cross',function (e) {
	if(!checkRequestmoduleIsVideo)
		return;
	if(sesJqueryObject('#ses_media_lightbox_container_video').css('display') != 'none'){
		sesJqueryObject('body').css({ 'overflow': 'initial' });
		history.pushState(null, null, defaultHashURL);
		sesJqueryObject('.layout_core_comments').html(dataCommentSes);
		e.preventDefault();
		firstStartPoint = 0;
		dataCommentSes = '';
		firstStartPointModule = 0;
		sesJqueryObject('#media_photo_next').after(getTagData);
		sesJqueryObject('#media_tags').html(mediaTags);		
		mediaTags = '';
		getTagData = '';
	}
	sesJqueryObject('#ses_media_lightbox_container_video').remove();
	sesJqueryObject('#ses_media_lightbox_container_video').remove();
});
// fullscreen code
function changeImageViewerResolution(type){
	if(!checkRequestmoduleIsVideo)
		return;
	if(type == 'fullscreen'){
		sesJqueryObject('#ses_media_lightbox_container_video').addClass('fullscreen');
	}else{
		sesJqueryObject('#ses_media_lightbox_container_video').removeClass('fullscreen');
	}
	return true;
}
//http://johndyer.name/native-fullscreen-javascript-api-plus-jquery-plugin/
var is_fullscreen = 0;
(function() {
	var 
		fullScreenApi = { 
			supportsFullScreen: false,
			isFullScreen: function() { return false; }, 
			requestFullScreen: function() {}, 
			cancelFullScreen: function() {},
			fullScreenEventName: '',
			prefix: ''
		},
		browserPrefixes = 'webkit moz o ms khtml'.split(' ');
	// check for native support
	if (typeof document.cancelFullScreen != 'undefined') {
		fullScreenApi.supportsFullScreen = true;
	} else {	 
		// check for fullscreen support by vendor prefix
		for (var i = 0, il = browserPrefixes.length; i < il; i++ ) {
			fullScreenApi.prefix = browserPrefixes[i];
			if (typeof document[fullScreenApi.prefix + 'CancelFullScreen' ] != 'undefined' ) {
				fullScreenApi.supportsFullScreen = true;
				break;
			}
		}
	}
	
	// update methods to do something useful
	if (fullScreenApi.supportsFullScreen) {
		fullScreenApi.fullScreenEventName = fullScreenApi.prefix + 'fullscreenchange';
		fullScreenApi.isFullScreen = function() {
			switch (this.prefix) {	
				case '':
					return document.fullScreen;
				case 'webkit':
					return document.webkitIsFullScreen;
				default:
					return document[this.prefix + 'FullScreen'];
			}
		}
		fullScreenApi.requestFullScreen = function(el) {
			return (this.prefix === '') ? el.requestFullScreen() : el[this.prefix + 'RequestFullScreen']();
		}
		fullScreenApi.cancelFullScreen = function(el) {
			return (this.prefix === '') ? document.cancelFullScreen() : document[this.prefix + 'CancelFullScreen']();
		}		
	}

	// jQuery plugin
	if (typeof jQuery != 'undefined') {
		jQuery.fn.requestFullScreen = function() {
			return this.each(function() {
				var el = jQuery(this);
				if (fullScreenApi.supportsFullScreen) {
					fullScreenApi.requestFullScreen(el);
				}
			});
		};
	}
	// export api
	window.fullScreenApi = fullScreenApi;	
})();

// do something interesting with fullscreen support
var fsButton = document.getElementById('fsbutton');
function toogle(){
if(is_fullscreen == 0)
	window.fullScreenApi.requestFullScreen(document.body);	
else
	window.fullScreenApi.cancelFullScreen(document.body);
}
if (window.fullScreenApi.supportsFullScreen) {	
	document.addEventListener(fullScreenApi.fullScreenEventName, function() {
		if (fullScreenApi.isFullScreen()) {
			is_fullscreen = 1;
			sesJqueryObject('.sesJqueryObject_toogle_screen').css('backgroundPosition','-44px 0');
			sesJqueryObject('#ses_media_lightbox_container_video').addClass('fullscreen');
			sesJqueryObject('.ses_media_lightbox_information').hide();
			var height = sesJqueryObject('.ses_media_lightbox_content').height();
			var width = sesJqueryObject('.ses_media_lightbox_left').width();
			sesJqueryObject('#media_photo_next_ses').css('height',height+'px');
			sesJqueryObject('#video_data_lightbox').css('max-height',height+'px');
			sesJqueryObject('#video_data_lightbox').css('max-width',width+'px');
			sesJqueryObject('#heightOfImageViewerContent').css('height', sesJqueryObject('.ses_media_lightbox_content').height()+'px');
			sesJqueryObject('#video_data_lightbox').attr('src',sesJqueryObject('#image-src-sesJqueryObject-lightbox-hidden').html());
		} else {
			is_fullscreen = 0;
			sesJqueryObject('.ses_media_lightbox_information').show();
			sesJqueryObject('.sesJqueryObject_toogle_screen').css('backgroundPosition','0 0');
			sesJqueryObject('#ses_media_lightbox_container_video').removeClass('fullscreen');
			var height = sesJqueryObject('.ses_media_lightbox_content').height();
			var width = sesJqueryObject('.ses_media_lightbox_left').width();
			sesJqueryObject('#media_photo_next_ses').css('height',height+'px');
			sesJqueryObject('#video_data_lightbox').css('max-height',height+'px');
			sesJqueryObject('#video_data_lightbox').css('max-width',width+'px');
			sesJqueryObject('#heightOfImageViewerContent').css('height', sesJqueryObject('.ses_media_lightbox_content').height()+'px');
		}
	}, true);
} else {
	sesJqueryObject('#fsbutton').hide();
}
//Key Events
sesJqueryObject(document).on('keyup', function (e) {
	if(!checkRequestmoduleIsVideo)
		return;
		e.preventDefault();
		//Close popup on esc
		if (e.keyCode === 27) { document.getElementById('crossSes').click();return false; }
		//Next Img On Right Arrow Click
		if (e.keyCode === 39) { 
			if(sesJqueryObject('#'+e.target.id).prop('tagName') == 'INPUT' || sesJqueryObject('#'+e.target.id).prop('tagName') == 'TEXTAREA')
				return;
			NextImageViewerVideo();return false; 
		}
		// like code
		if (e.keyCode === 76) {
			if(sesJqueryObject('#'+e.target.id).prop('tagName') == 'INPUT' || sesJqueryObject('#'+e.target.id).prop('tagName') == 'TEXTAREA')
				return;
			sesJqueryObject('#sesLightboxLikeUnlikeButton').trigger('click');
		}
		// favourite code
		if (e.keyCode === 70) {
			if(sesJqueryObject('#'+e.target.id).prop('tagName') == 'INPUT' || sesJqueryObject('#'+e.target.id).prop('tagName') == 'TEXTAREA')
				return;
			sesJqueryObject('#sesJqueryObject_favourite').trigger('click');
		}
		//Prev Img on Left Arrow Click
		if (e.keyCode === 37) { 
			if(sesJqueryObject('#'+e.target.id).prop('tagName') == 'INPUT' || sesJqueryObject('#'+e.target.id).prop('tagName') == 'TEXTAREA')
				return;
			PrevImageViewerVideo(); return false;
		}
});
function NextImageViewerVideo(){
	if(sesJqueryObject('#ses_media_lightbox_container_video').css('display') == 'none'){
			return false;;	
	}
	if(sesJqueryObject('#nav-btn-next').length){
			document.getElementById('nav-btn-next').click();
	}
	return false;
}
function PrevImageViewerVideo(){
	if(sesJqueryObject('#ses_media_lightbox_container_video').css('display') == 'none'){
			return false;;	
	}
	if(sesJqueryObject('#nav-btn-prev').length){
		document.getElementById('nav-btn-prev').click();
	}
	return false;
}
function getVideoViewerObjectData(imageURL,requestedURL){
		 imageViewerGetRequest = new Request.HTML({
      url :requestedURL,
      data : {
        format : 'html',
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript)
      {
					sesJqueryObject('.ses_media_lightbox_content').html('');
      		sesJqueryObject('.ses_media_lightbox_content').html(responseHTML);
					var height = sesJqueryObject('.ses_media_lightbox_item_wrapper').height();
					var width = sesJqueryObject('.ses_media_lightbox_item_wrapper').width();
					if(sesJqueryObject('#media_photo_next_ses').find('#video_data_lightbox').hasClass('ses-private-image')){
							sesJqueryObject('#video_data_lightbox').remove();
							sesJqueryObject('.ses_media_lightbox_options_btns').hide();
							sesJqueryObject('.ses_media_lightbox_tag_btn').hide();
							sesJqueryObject('.ses_media_lightbox_share_btn').hide();
							sesJqueryObject('.ses_media_lightbox_more_btn').hide();
							sesJqueryObject('.ses_media_lightbox_information').hide();
							sesJqueryObject('#video_data_lightbox').remove();
							sesJqueryObject('#gallery-img').show();
					}else	if(sesJqueryObject('#media_photo_next_ses').find('#video_data_lightbox').hasClass('ses-blocked-video')){
							sesJqueryObject('.ses_media_lightbox_information').hide();
							sespromptPasswordCheck();
					}else{
							sesJqueryObject('.ses_media_lightbox_options_btns').show();
							sesJqueryObject('.ses_media_lightbox_tag_btn').show();
							sesJqueryObject('.ses_media_lightbox_share_btn').show();
							sesJqueryObject('.ses_media_lightbox_more_btn').show();
							sesJqueryObject('.ses_media_lightbox_information').show();
							sesJqueryObject('#video_data_lightbox').show();
							sesJqueryObject('#gallery-img').hide();
					}
					sesJqueryObject('#video_data_lightbox').css('height',height+'px');
					//sesJqueryObject('.sesvideo_view_embed ').find('iframe').css('height',height+'px');
					//sesJqueryObject('.sesvideo_view_embed').find('iframe').css('width',width+'px');
					sesJqueryObject('#video_data_lightbox').css('max-height',height+'px');
					sesJqueryObject('#video_data_lightbox').css('max-width',width+'px');
					sesJqueryObject('#video_data_lightbox').css('width',width+'px');	
					//var height = sesJqueryObject('.pswp__zoom-wrap').height();
//					var width = sesJqueryObject('.pswp__zoom-wrap').width();
					var marginTop = sesJqueryObject('.ses_media_lightbox_options').height();
					if(sesJqueryObject('.sesvideo_view_embed').find('iframe').contents().length){
						sesJqueryObject('.sesvideo_view_embed').find('iframe').css('height',parseInt(height-(marginTop*2))+'px');
						sesJqueryObject('.sesvideo_view_embed').find('iframe').css('width',width+'px');
						sesJqueryObject('.sesvideo_view_embed').find('iframe').css('margin-top',marginTop+'px');
						sesJqueryObject('.sesvideo_view_embed').find('iframe').css('margin-bottom',marginTop+'px');		
					}else{
						sesJqueryObject('.sesvideo_view_embed').find('video').css('margin-top',(height/4)+'px');
					}
					if(sesJqueryObject('#map-canvas').length>0)
						initializeSesVideoMap();
					return true;
      }
			});
			en4.core.request.send(imageViewerGetRequest, {
				'force': true
			});
}
function changePosition(){
	if(!checkRequestmoduleIsVideo)
		return;
	if(sesJqueryObject('#nav-btn-next').length){
		document.getElementById('nav-btn-next').click();
	}else{
		toogle();
	}
}
sesJqueryObject(document).on('click','#editDetailsLinkVideo',function(e){
		e.preventDefault();
		sesJqueryObject('#titleSes').val(trim(sesJqueryObject('#ses_title_get').html(),' '));
		sesJqueryObject('#descriptionSes').val(trim(sesJqueryObject('#ses_title_description').html(),' '));
	if(sesJqueryObject('#locationSes').length >0){
		sesJqueryObject('#locationSes').val(trim(sesJqueryObject('#ses_location_data').html()));
		editSetMarkerOnMapVideo();
		google.maps.event.trigger(map, 'resize');
	}
		sesJqueryObject('#editDetailsFormVideo').css('display','block');
		sesJqueryObject('#ses_media_lightbox_media_info').css('display','none');
});
sesJqueryObject(document).on('click','#cancelDetailsSesVideo',function(e){
		e.preventDefault();
		sesJqueryObject('#editDetailsFormVideo').css('display','none');
		sesJqueryObject('#ses_media_lightbox_media_info').css('display','block');
});
sesJqueryObject(document).on('click','#saveDetailsSesVideo',function(e){
	e.preventDefault();
	var thisObject = this;
	sesJqueryObject(thisObject).prop("disabled",true);
	var video_id = sesJqueryObject('#video_id_ses').val();
	var formData =  sesJqueryObject("#changePhotoDetailsVideo").serializeArray();
	sesJqueryObject.ajax({  
    type: "POST",  
    url: en4.core.baseUrl+'sesvideo/index/edit-detail/video_id/'+video_id,  
    data: formData,  
    success: function(response) {  
      var data = JSON.parse(response);
			if(data.status && !data.error){
				sesJqueryObject(thisObject).prop("disabled",false);
				sesJqueryObject('#ses_title_get').html(sesJqueryObject('#titleSes').val());
				sesJqueryObject('#ses_title_description').html(sesJqueryObject('#descriptionSes').val());
				sesJqueryObject('#ses_location_data').html(sesJqueryObject('#locationSes').val());
				sesJqueryObject('#editDetailsFormVideo').css('display','none')
				sesJqueryObject('#ses_media_lightbox_media_info').css('display','block');
			if(sesJqueryObject('#locationSes').val() != '')
				sesJqueryObject('#seslocationIn').html('In');
			else
				sesJqueryObject('#seslocationIn').html('');
				return false;
			}else{
				alert(en4.core.language.translate('Something went wrong,try again later.'));	
				return false;
			}
    }
});
	return false;
});
sesJqueryObject(document).on("click", '.sesvideo_thumb_img', function (e) {
	e.preventDefault();
	var imageObject = sesJqueryObject(this);
  var getImageHref = imageObject.attr('href');
	var imageSource = imageObject.find('span').css('background-image').replace('url(','').replace(')','').replace('"','').replace('"','');
	getImageHref = getImageHref.replace(videoURLsesvideo,videoURLsesvideo+'/imageviewerdetail');
	if(openVideoInLightBoxsesVideo == 0 ){
		window.location.href = getImageHref;
	}
	getRequestedVideoForImageViewer(imageSource,getImageHref);
});
sesJqueryObject(document).on('click','#comments .comments_options a',function(event){
	if(!checkRequestmoduleIsVideo)
		return;
	var thisObject = this;
	var htmlOnclick = sesJqueryObject(this).attr('onclick');
	if(htmlOnclick.search('video') == -1 && htmlOnclick.search('sesvideo_chanelphoto') == -1)
		return true;
	if(htmlOnclick.search('comments') != -1){
		// unlike code
		var currentURL = window.location.href;
		if(currentURL.search('video_id') != -1)
			var itemType = 'Chanel';
		else if(currentURL.search('chanel_id') != -1)
			var itemType = 'Chanel Photo';
		else
			var itemType = 'Video';
		if(htmlOnclick.search('unlike') != -1){
		 if(sesJqueryObject('#ses_media_lightbox_container_video').css('display') == 'block'){
		 	sesJqueryObject('#sesLightboxLikeUnlikeButtonVideo').removeClass('button_active');
			sesJqueryObject('#sesLightboxLikeUnlikeButtonVideo').find('#like_unlike_count').html(parseInt(sesJqueryObject('#sesLightboxLikeUnlikeButtonVideo').find('#like_unlike_count').html())-1);
		 }
		 showTooltip(10,10,'<i class="fa fa-thumbs-up"></i><span>'+en4.core.language.translate(itemType+" removed from like successfully")+'</span>');
		}else{
			if(sesJqueryObject('#ses_media_lightbox_container_video').css('display') == 'block'){
		 		sesJqueryObject('#sesLightboxLikeUnlikeButtonVideo').addClass('button_active');
				sesJqueryObject('#sesLightboxLikeUnlikeButtonVideo').find('#like_unlike_count').html(parseInt(sesJqueryObject('#sesLightboxLikeUnlikeButtonVideo').find('#like_unlike_count').html())+1);
			}
			showTooltip(10,10,'<i class="fa fa-thumbs-up"></i><span>'+en4.core.language.translate(itemType+" Like Successfully")+'</span>', 'sesbasic_liked_notification');
		}
	}
});