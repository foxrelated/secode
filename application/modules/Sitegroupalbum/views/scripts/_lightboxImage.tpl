<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _lightboxImage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
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
   // ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/tagger.js');
     ->appendFile($this->layout()->staticBaseUrl . 'externals/tagger/tagger.js');
  $this->headTranslate(array(
    'Save', 'Cancel', 'delete'
  ));
  $this->headLink()
      ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitegroupalbum/externals/styles/style_sitegroupalbum.css');
?>
<style type="text/css">
.sitegroup_photo_tag{background-image: url(./application/modules/Sitegroup/externals/images/icons/tagged.png);}
.sitegroup_photo_profile{background-image: url(./application/modules/Sitegroup/externals/images/photo/upload.png);}
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
<div class="group_lightbox" id="light" style="display: none;">
  <input type="hidden" id="canReload" value="0" />
  <div class="group_black_overlay"  ></div>
  <div class="sitegroup_lightbox_white_content_wrapper" onclick = "closeLightBox()">
    <div class="sitegroup_lightbox_white_content"  id="white_content_default"  >
      <div id="image_div">
        <div class="" id="group_photo_scroll"></div>
        <div class="group_image_content album_viewmedia_container" id="media_image_div">
         
        </div>
        <div id="group_lightbox_user_options"></div>
        <div class="" id="group_lightbox_user_right_options"></div>
        <div class="group_text_content" id="group_lightbox_text">
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var  baseY = '0';
       
  function openLightBox(imagepath, url){
    document.getElementById('light').style.display='block';   
    document.getElementById('media_image_div').innerHTML= "&nbsp;<img class='lightbox_photo' src="+imagepath+"  />";
    setHtmlScroll("hidden");
    photopaginationDefault(url);
  }

 	window.addEvent('domready', function() {
     $('white_content_default').addEvent('click', function(event) {
      event.stopPropagation();
    });
  });
  
  var closeLightBox = function()
  {
		if(document.getElementById('canReload').value==1){
      window.location.reload(true);
    }
    document.getElementById('light').style.display='none';
    setHtmlScroll("auto");
    
    if(document.getElementById('light_album'))
    document.getElementById('light_album').style.display='none';
    
    if($('album_lightbox'))
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
		if(document.getElementById('photo_view_comment'))
			$('photo_view_comment').innerHTML="";
		if(document.getElementById('album_lightbox'))
			$('album_lightbox').innerHTML="";
		if($("info_comment") && info_comment !="")
			$("info_comment").innerHTML=info_comment;
    
  };

  var photopaginationDefault = function(url)
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
        $('white_content_default').innerHTML = responseHTML;
						if($("info_comment") && info_comment !="")
							$("info_comment").innerHTML="";
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