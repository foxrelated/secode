<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _lightboxImageAlbum.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
   // ->appendFile($this->seaddonsBaseUrl() . '/application/modules/Sitegroup/externals/scripts/tagger.js');
     ->appendFile($this->layout()->staticBaseUrl . 'externals/tagger/tagger.js');
  $this->headTranslate(array(
    'Save', 'Cancel', 'delete'
  ));
?>
<style type="text/css">
.sitegroup_photo_tag{background-image: url(./application/modules/Sitegroup/externals/images/icons/tagged.png);}
.sitegroup_lightbox_photos_delete{background-image: url(./application/modules/Sitegroup/externals/images/photo/album_delete.png);}
.sitegroup_lightbox_photos_download{background-image: url(./application/modules/Sitegroup/externals/images/icons/download.png);}
.sitegroup_lightbox_like{background-image: url(./application/modules/Sitegroup/externals/images/icons/thumb_up.png);}
.sitegroup_lightbox_unlike{background-image: url(./application/modules/Sitegroup/externals/images/icons/thumb_down.png);}
.sitegroup_lightbox_comment{background-image: url(./application/modules/Sitegroup/externals/images/icons/lightbox_comment.png);}
.group_image_content {background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.photolightbox.bgcolor', '#000000'); ?>;}
.group_lightbox_options a.close{background-image: url(./application/modules/Sitegroup/externals/images/closebox.png);}
.group_lightbox_options a.nxt{background-image: url(./application/modules/Sitegroup/externals/images/icons/group-photo-nxt.png);}
.group_lightbox_options a.pre{background-image: url(./application/modules/Sitegroup/externals/images/icons/group-photo-prev.png);}
.group_lightbox_user_options{background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.photolightbox.bgcolor', '#000000'); ?>;}
.group_lightbox_user_right_options{background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.photolightbox.bgcolor', '#000000'); ?>;}
.lightbox_photo_detail{background:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.photolightbox.bgcolor', '#000000'); ?>;}
.group_photo_lightbox_content .lightbox_photo_description_edit_icon a{background:url(./application/modules/Sitegroup/externals/images/write_edit_icon.png);}
.group_photo_lightbox_content .lightbox_photo_description_edit_icon a:hover{
	background:url(./application/modules/Sitegroup/externals/images/write_edit_icon.png) 0 18px;
}
.group_lightbox_user_options a,
.lightbox_photo_detail,
.lightbox_photo_detail a{color:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.photolightbox.fontcolor','#FFFFFF') ?>;}
</style>
<div class="group_lightbox" id="light_album" style="display: none;">
  <input type="hidden" id="canReload" value="0" />
  <div class="group_black_overlay"  ></div>
  <div class="sitegroup_lightbox_white_content_wrapper" onclick = "closeLightBoxSitegroupAlbum()">
    <div class="sitegroup_lightbox_white_content"  id="white_content_default_album"  >
      <div class="group_image_content album_viewmedia_container" id="media_image_div_album">
      </div>
      <div id="album_lightbox">
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var  baseY = '0';
       
  function openLightBoxSitegroupAlbum(imagepath, url){
    document.getElementById('light_album').style.display='block';    
    document.getElementById('media_image_div_album').style.display="block";
    document.getElementById('media_image_div_album').innerHTML= "&nbsp;<img class='lightbox_photo' src="+imagepath+"  />";
    setHtmlScroll("hidden");
    photopaginationDefaultSitegroupAlbum(url);
  }

 	window.addEvent('domready', function() {
     $('white_content_default_album').addEvent('click', function(event) {
      event.stopPropagation();
    });
  });
  
  var closeLightBoxSitegroupAlbum = function()
  {
    document.getElementById('light_album').style.display='none';
    setHtmlScroll("auto");
    $('album_lightbox').innerHTML ="";
     if(document.getElementById('group_lightbox_text')){
       document.getElementById('group_lightbox_text').innerHTML="";
    document.getElementById('group_lightbox_text').style.display="none";
     }
    if(document.getElementById('group_lightbox_user_options'))
    document.getElementById('group_lightbox_user_options').style.display="none";
    if(document.getElementById('group_photo_scroll'))
    document.getElementById('group_photo_scroll').style.display="none";
   if(document.getElementById('group_lightbox_user_right_options'))
    document.getElementById('group_lightbox_user_right_options').style.display="none";

    if(document.getElementById('canReload').value==1){
      window.location.reload(true);
    }
		if(document.getElementById('album_lightbox'))
		 $('album_lightbox').innerHTML="";
  };

  var photopaginationDefaultSitegroupAlbum = function(url)
  {
    if(document.getElementById('lightbox_photo_detail'))
         document.getElementById('lightbox_photo_detail').style.display="none";
    en4.core.request.send(new Request.HTML({
      url : url,
      data : {
        format : 'html',
        isajax : 0
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('album_lightbox').innerHTML = responseHTML;
         document.getElementById('media_image_div_album').innerHTML="";
         document.getElementById('media_image_div_album').style.display="none";
          if($('ads') && $('ads_hidden')){
            $('ads').innerHTML =  $('ads_hidden').innerHTML;
             $('ads_hidden').innerHTML='';
          }
      }
    }));
  };

  function setHtmlScroll(cssCode) {
    $$('html').setStyle('overflow',cssCode);
    
  }
  function setImageScroll(cssCode) {
    $$('.sitegroup_lightbox_white_content_wrapper').setStyle('overflow',cssCode);

  }
</script>