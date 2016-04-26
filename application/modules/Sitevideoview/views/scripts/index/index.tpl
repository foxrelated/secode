<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideoview
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2012-06-028 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->showLink): ?>
    <div class="photo_lightbox_options" id="seaocore_photo_scroll">
      <div class="photo_lightbox_pre" onclick='getSiteviewPrevVideo()' title="<?php echo $this->translate('Previous'); ?>" ><i></i></div>
      <div onclick='getSiteviewNextVideo()'  title="<?php echo $this->translate('Next'); ?>" class="photo_lightbox_nxt"><i></i></div>     
    </div>
  <?php endif; ?>
<?php if ($this->viewPermission): ?>
  <div class="photo_lightbox_cont">
    <div class="photo_lightbox_left" id="siteviewvideo_lightbox_left" style="background-color: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideoview.lightbox.bgcolor', '#0A0A0A') ?>">
     <?php if($this->module_name == 'ynvideo') : ?>
<?php /*       @Reference : 'YouNet Video Plugin' by author YouNet Company
 * @Comments : We have modified our code to make Video Lightbox Viewer Plugin compatible to their plugin.
 */?>
      <div class="ynvideo_video_view_description">
          <div class="video_button_add_to_area" style="position: absolute; right: 5px; top: 5px; margin-top: 0px;">
            <button  class="ynvideo_uix_button ynvideo_add_button" id="ynvideo_btn_video_<?php echo $this->video->getIdentity() ?>" video-id="<?php echo $this->video->getIdentity() ?>" style="padding:0 18px 0 0 !important;">
              <div>
                <?php echo $this->translate('Add To') ?></div>
            </button>
          </div>
        </div>
      <?php endif; ?>
      <table width="100%" height="100%">
        <tr>
          <td width="100%" height="100%" valign="middle">
            <div class="photo_lightbox_image" id='media_image_div_seaocore'>
              <?php
              if ($this->video->type == 3): ?>                 
                <script type='text/javascript'>
                  en4.core.runonce.add(function() {                    
                   var flashembedAdd = function(){
                    flashembed("sitevideoview_video_embed", {
                      src: "<?php echo $this->layout()->staticBaseUrl ?>externals/flowplayer/flowplayer-3.1.5.swf",
                      width: 480,
                      height: 386,
                      wmode: 'transparent'
                    }, {
                      config: {
                        clip: {
                          url: "<?php echo $this->video_location; ?>",
                          autoPlay: true,
                          duration: "<?php echo $this->video->duration ?>",
                          autoBuffering: true
                        },
                        plugins: {
                          controls: {
                            background: '#000000',
                            bufferColor: '#333333',
                            progressColor: '#444444',
                            buttonColor: '#444444',
                            buttonOverColor: '#666666'
                          }
                        },
                        canvas: {
                          backgroundColor:'#000000'
                        }
                      }
                    });
                  }
                  
                    if(!(typeof flashembed == 'function')){
                      new Asset.javascript( en4.core.staticBaseUrl+'externals/flowplayer/flashembed-1.0.1.pack.js',{
                      onLoad :flashembedAdd
                      });
                    } else {
                      flashembedAdd();
                    }
                  });
                      
                </script>
              <?php endif ?>

              <script type="text/javascript">
                en4.core.runonce.add(function() {
                  var sitevideoview_pre_rate = <?php echo $this->video->rating; ?>;
                  var sitevideoview_rated = '<?php echo $this->rated; ?>';
                  var sitevideoview_video_id = <?php echo $this->video->video_id; ?>;
                  var sitevideoview_total_votes = <?php echo $this->rating_count; ?>;
                  var viewer = <?php echo $this->viewer_id; ?>;
                  var sitevideoview_check_rating = 0;
	                var sitevideoview_current_total_rate;
                  <?php if(empty($this->rating_count)): ?>
                  var sitevideoview_rating_var =  '<?php echo $this->string()->escapeJavascript($this->translate(" rating")) ?>';
                  <?php else: ?>
                  var sitevideoview_rating_var =  '<?php echo $this->string()->escapeJavascript($this->translate(" ratings")) ?>';
                   <?php endif; ?>

                  var sitevideoview_rating_over = window.sitevideoview_rating_over = function(rating) {
                    if( sitevideoview_rated == 1 ) {
                      $('sitevideoview_rating_text').innerHTML = "<?php echo $this->translate('you already rated'); ?>";
                      
                    } else if (sitevideoview_rated == 2)
                    { <?php // @Reference : 'All-in-one Video - Platinum Edition' by author myseplugins.com
                      //@Comments : We have modified our code to make Video Lightbox Viewer Plugin compatible to their plugin. ?>
                      $('sitevideoview_rating_text').innerHTML = "<?php echo $this->translate('you can\'t rate your own video');?>";
                     } else if( viewer == 0 ) {
                      $('sitevideoview_rating_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
                    } else {
                      $('sitevideoview_rating_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
                      for(var x=1; x<=5; x++) {
                        if(x <= rating) {
                          $('sitevideoview_rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
                        } else {
                          $('sitevideoview_rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
                        }
                      }
                    }
                  }
      
                  var sitevideoviewrating_out = window.sitevideoviewrating_out = function() {
                    $('sitevideoview_rating_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>";
                    if (sitevideoview_pre_rate != 0){
                      sitevideo_view_set_rating();
                    }
                    else {
                      for(var x=1; x<=5; x++) {
                        $('sitevideoview_rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
                      }
                    }
                  }

                  var sitevideo_view_set_rating = window.sitevideo_view_set_rating = function() {
                    var rating = sitevideoview_pre_rate;
    
                    if(sitevideoview_check_rating == 1) {                      
                        $('sitevideoview_rating_text').innerHTML = sitevideoview_current_total_rate+sitevideoview_rating_var;              
                      } else {
                        $('sitevideoview_rating_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>";
                      }
                    for(var x=1; x<=parseInt(rating); x++) {
                      $('sitevideoview_rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
                    }

                    for(var x=parseInt(rating)+1; x<=5; x++) {
                      $('sitevideoview_rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
                    }

                    var remainder = Math.round(rating)-rating;
                    if (remainder <= 0.5 && remainder !=0){
                      var last = parseInt(rating)+1;
                      $('sitevideoview_rate_'+last).set('class', 'rating_star_big_generic rating_star_big_half');
                    }
                  }

                  var sitevideoviewrate = window.sitevideoviewrate = function(rating) {
                    $('sitevideoview_rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
                    for(var x=1; x<=5; x++) {
                      $('sitevideoview_rate_'+x).set('onclick', '');
                    }
                    (new Request.JSON({
                      'format': 'json',
                      'url' : '<?php echo $this->url($this->rateLinkParamsArray, 'default', true) ?>',
                      'data' : {
                        'format' : 'json',
                        'rating' : rating,
                        'video_id': sitevideoview_video_id
                      },
                      'onRequest' : function(){
                        sitevideoview_rated = 1;
                        sitevideoview_total_votes = sitevideoview_total_votes+1;
                        sitevideoview_pre_rate = (sitevideoview_pre_rate+rating)/sitevideoview_total_votes;
                        sitevideo_view_set_rating();
                      },
                      'onSuccess' : function(responseJSON, responseText)
                      { 
                      <?php if ($this->module_name == 'avp'): ?>
                        <?php  // @Reference : 'All-in-one Video - Platinum Edition' by author myseplugins.com
                       // @Comments : We have modified our code to make Video Lightbox Viewer Plugin compatible to their plugin. ?>
                        var total_json_rate = responseJSON.total;
                      <?php else: ?>
                        var total_json_rate = responseJSON[0].total;
                      <?php endif; ?>
                        
                        $('sitevideoview_rating_text').innerHTML = total_json_rate+sitevideoview_rating_var;
                        sitevideoview_current_total_rate =  total_json_rate;
                         sitevideoview_check_rating = 1;
                      }
                    })).send();

                  }

                  var tagActionSVV = window.tagActionSVV = function(tag){
                    $('sitevideoview_tag').value = tag;
                    $('sitevideoview_filter_form').submit();
                  }
                  var tagActionWithUrl =  window.tagActionWithUrl = function(tag, url){
                  $('sitevideoview_tag').value = tag;
                  window.location.href = url;
                  }
                  sitevideo_view_set_rating();                


                  if($type(keyUpLikeEventSitevideoView))
                    document.removeEvent("keyup" , keyUpLikeEventSitevideoView);
                  var keyUpLikeEventSitevideoView=function(e) {      
    
                  <?php if ($this->canComment): ?>
                          if( e.key == 'l' && (
                          e.target.get('tag') == 'html' ||
                            e.target.get('tag') == 'div' ||
                            e.target.get('tag') == 'span' ||
                            e.target.get('tag') == 'a' ||
                            e.target.get('tag') == 'body' )) {
                            var  video_like_id="<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>";
                            if($(video_like_id+"unlike_link") && $(video_like_id+"unlike_link").style.display == "none"){
                              en4.seaocore.likes.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>','1');
                            } else if($(video_like_id+"like_link") && $(video_like_id+"like_link").style.display == "none"){
                              en4.seaocore.likes.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>','1');
                            }
                          }
                  <?php endif; ?>
          
                };
                document.addEvents({   
                  'keyup': keyUpLikeEventSitevideoView
                });
              });
              </script>

              <form id='sitevideoview_filter_form' class='global_form_box' method='post' action='<?php echo ($this->tagFilterUrlArray) ? $this->url($this->tagFilterUrlArray, $this->tag_filter_url_route, true) :''?>' style='display:none;'>
                <input type="hidden" id="sitevideoview_tag" name="tag" value=""/>
              </form>

              <div class="video_view video_view_container"> 

                <?php if ($this->video->type == 3): ?>
                  <div id="sitevideoview_video_embed" class="video_embed">
                  </div>
                <?php else: ?>
                  <div class="video_embed">
                    <?php echo $this->videoEmbedded ?>
                  </div>
                <?php endif; ?>
         
              </div>
              <div class="video_viewer_stats">
								<div id="sitevideoviewvideo_rating" class="rating fleft" onmouseout="sitevideoviewrating_out();">                
		              <span id="sitevideoview_rate_1" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="sitevideoviewrate(1);"<?php endif; ?> onmouseover="sitevideoview_rating_over(1);"></span>
		              <span id="sitevideoview_rate_2" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="sitevideoviewrate(2);"<?php endif; ?> onmouseover="sitevideoview_rating_over(2);"></span>
		              <span id="sitevideoview_rate_3" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="sitevideoviewrate(3);"<?php endif; ?> onmouseover="sitevideoview_rating_over(3);"></span>
		              <span id="sitevideoview_rate_4" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="sitevideoviewrate(4);"<?php endif; ?> onmouseover="sitevideoview_rating_over(4);"></span>
		              <span id="sitevideoview_rate_5" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="sitevideoviewrate(5);"<?php endif; ?> onmouseover="sitevideoview_rating_over(5);"></span>
		              <span id="sitevideoview_rating_text" class="rating_text"><?php echo $this->translate('click to rate'); ?></span>
		            </div>
		            <div class="fright">
		            	<?php echo $this->translate(array('%s Like', '%s Likes', $this->video->likes()->getLikeCount()), $this->locale()->toNumber($this->video->likes()->getLikeCount())) ?> |
		            	<?php echo $this->translate(array('%s Comment', '%s Comments', $this->video->comments()->getCommentCount()), $this->locale()->toNumber($this->video->comments()->getCommentCount())) ?> | 
		            	<?php echo $this->translate(array('%s View', '%s Views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
		            	<?php // @Reference : 'YouNet Video Plugin' by author YouNet Company
                  //@Comments : We have modified our code to make Video Lightbox Viewer Plugin compatible to their plugin.
                  ?>
		            	<?php if ($this->module_name == 'ynvideo' && isset($this->video->favorite_count) ): ?>
          					| <?php echo $this->translate(array('%s Favorite', '%s Favorites', $this->video->favorite_count), $this->locale()->toNumber($this->video->favorite_count)) ?>
       						<?php endif; ?>
		            </div>                
		          </div>
              <?php /*  @Reference : 'All-in-one Video - Platinum Edition' by author myseplugins.com
              @Comments : We have modified our code to make Video Lightbox Viewer Plugin compatible to their plugin. */?>
                <?php if ($this->module_name == 'avp' && $this->can_playlist && !$this->video->hasAutoplaySupport()): ?>
                <div class="clr avp-playlist-no-autoplay" style="float: right;">
                  <i><?php echo $this->translate("This video breaks autoplay in playlists"); ?></i>
                </div>
                <?php endif; ?>
            </div>               
          </td>
        </tr>     
      </table>
    </div>
    <div class="photo_lightbox_right" id="photo_lightbox_right_content"> 
      <div id="main_right_content_area"  style="height: 100%">
        <div id="main_right_content" class="scroll_content">        
          <div id="photo_right_content" class="photo_lightbox_right_content">
            <div class='photo_right_content_top'>
              <div class='photo_right_content_top_l'>
                <?php
                 echo $this->htmlLink($this->video->getOwner()->getHref(), $this->itemPhoto($this->video->getOwner(), 'thumb.icon', '', array('align' => 'left')), array('class' => 'item_photo'));
                ?>
            </div>
            <div class='photo_right_content_top_r'>
                <?php               
                  echo $this->video->getOwner()->__toString();               
                ?>             
            </div>
              <div class="photo_right_content_top_title" style="margin-top:5px;">
		          <?php if ($this->can_edit || !empty($this->video->title)): ?>
		      			<div id="link_svvideo_title" style="display:block;">
		        			<span id="svvideo_title">
		        				<?php if (!empty($this->video->title)): ?>
		  								<?php echo $this->video->getTitle() ?>
										<?php elseif ($this->can_edit): ?>
		            			<?php echo $this->translate('Add a title'); ?>
		        				<?php endif; ?>
		        			</span>
		        			<?php if ($this->can_edit): ?>
		            		<a href="javascript:void(0);" onclick="en4.sitevideoview.switchEditMode('title','edit')" class="photo_right_content_top_title_edit"><?php echo $this->translate('Edit'); ?></a>
		        			<?php endif; ?>
		      			</div>
		        	<?php endif; ?>
        			<div id="edit_svvideo_title" class="photo_right_content_top_edit" style="display: none;">
          			<input type="text"  name="edit_title" id="editor_svvideo_title" title="<?php echo $this->translate('Add a title'); ?>" value="<?php echo $this->video->title; ?>" />
	          		<div class="buttons">
	            		<button name="save" onclick="en4.sitevideoview.saveContent('<?php echo $this->video->getGuid() ?>','title')"><?php echo $this->translate('Save'); ?></button>
	            		<button name="cancel" onclick="en4.sitevideoview.switchEditMode('title','display')"><?php echo $this->translate('Cancel'); ?></button>
	          		</div>
        			</div>
	        		<div id="svvideo_loading_title" style="display: none;" >
	          		<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
	        		</div>
	        	</div>
            </div>
            <div class="photo_right_content_top_title photo_right_content_top_caption">              
              <?php if($this->can_edit  || !empty($this->video->description)  ): ?>
		          <div id="link_svvideo_description" style="display:block;">                
		            <span id="svvideo_description" class="lightbox_photo_description">
		              <?php if (!empty($this->video->description) ): ?>
		                 <?php echo $this->viewMore($this->video->getDescription(),400,5000,400,true); ?>
		              <?php elseif($this->can_edit): ?>
		                <?php echo $this->translate('Add a description');?>
		              <?php endif; ?>
		            </span>
		            <?php if($this->can_edit): ?>
		            	<a href="javascript:void(0);" onclick="en4.sitevideoview.switchEditMode('description','edit')" class="photo_right_content_top_title_edit"><?php echo $this->translate('Edit');?></a>
		            <?php endif; ?>
		          </div>
	          <?php endif; ?>
              <div id="edit_svvideo_description" class="photo_right_content_top_edit" style="display: none;">
                  <textarea rows="2" cols="10"  name="edit_description" id="editor_svvideo_description" title="<?php echo $this->translate('Add a description'); ?>" ><?php echo $this->video->description; ?></textarea>
                  <div class="buttons">
                    <button name="save" onclick="en4.sitevideoview.saveContent('<?php echo $this->video->getGuid() ?>','description')"><?php echo $this->translate('Save'); ?></button>
                    <button name="cancel" onclick="en4.sitevideoview.switchEditMode('description','display')"><?php echo $this->translate('Cancel'); ?></button>
                  </div>
                </div>
                <div id="svvideo_loading_description" style="display: none;" >
                  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
                </div>             
            </div>
            <div class="photo_right_content_tags" style="margin-bottom:5px;">      
              <?php if ($this->category): ?>
                <b><?php echo $this->translate('Category:') ?></b>
                <?php
                echo $this->htmlLink(array(
                    'route' => 'video_general',
                    'QUERY' => array('category' => $this->category->category_id)
                        ), $this->translate($this->category->category_name)
                )
                ?>
                <?php elseif($this->categories && $this->video->category_id ):?>
                <b> <?php echo $this->translate('Category:') ?></b>
               <?php                $category = $this->categories[$this->video->category_id];
                    echo $this->htmlLink($category->getHref(), $category->category_name);
                    if ($this->video->category_id != $this->video->subcategory_id && $this->video->subcategory_id):
                      echo ' &#187; ';
                      $subCategory = $this->categories[$this->video->subcategory_id];
                      echo $this->htmlLink($subCategory->getHref(), $subCategory->category_name);
                    endif; ?>
              <?php endif; ?>
                <?php /* @Reference : 'All-in-one Video - Platinum Edition' by author myseplugins.com
                @Comments : We have modified our code to make Video Lightbox Viewer Plugin compatible to their plugin. */ ?>
               <?php if ($this->module_name == 'avp'): ?>
                <?php  if (count($this->fieldStructure) > 0): 
                  $additional_info = $this->fieldValueLoop($this->subject(), $this->fieldStructure);
                  if (!empty($additional_info)): ?>
                   <br /><br />
                  <?php   echo $additional_info;
                  endif;
                endif; ?>
                <?php
                  $event = Engine_Hooks_Dispatcher::_()->callEvent('Avp_onVideoInfo', array(
                        'video' => $this->video
                  )); ?>
                <?php if (!empty($this->video->category_id)): ?>
                   
                 <b><?php echo $this->translate('Category:') ?></b>                  
                      <?php
                      if (is_numeric($this->video->category_id)):                  
                            echo $this->avpCategoryLine($this->video->category_id);                  
                      else:   ?>
                 <div class="avp_group">
                     <?php   $categories = Zend_Json::decode($this->video->category_id);
                        $total_categories = count($categories);
                        $i = 0;

                        foreach ($categories as $category):
                          $i++;
                          $visibility = ($i > 1 ? 'hidden' : 'visible');
                          echo $this->htmlImage('application/modules/Avp/externals/images/avp_group.png', $this->translate('Video Category'), array('class' => 'avp_category_icon', 'style' => "visibility: {$visibility};")) . $this->avpCategoryLine($category);
                        endforeach; ?>
                        </div>
                    <?php endif; ?>                  
                <?php endif; ?>
             <?php endif;?> 
           	</div>   
            <?php if (count($this->videoTags)): ?>
            	<div class="photo_right_content_tags">
	              <b> <?php echo $this->translate('Tags:') ?></b>
	              <?php foreach ($this->videoTags as $tag): ?>
	             <?php if(!empty($tag->getTag()->text)):  ?>
	                      <?php if ($this->module_name == 'sitepagevideo'): ?>
	                        <a href='javascript:void(0);' onclick="javascript:tagActionWithUrl('<?php echo $tag->getTag()->tag_id; ?>', '<?php echo $this->url(array('tag' => $tag->getTag()->tag_id), 'sitepagevideo_browse', true); ?>');">
	                        #<?php echo $tag->getTag()->text ?></a>
	                      <?php elseif ($this->module_name == 'sitebusinessvideo'): ?>
	                        <a href='javascript:void(0);' onclick="javascript:tagActionWithUrl('<?php echo $tag->getTag()->tag_id; ?>', '<?php echo $this->url(array('tag' => $tag->getTag()->tag_id), 'sitebusinessvideo_browse', true); ?>');">
	                          #<?php echo $tag->getTag()->text ?></a>
                        <?php elseif ($this->module_name == 'sitegroupvideo'): ?>
	                        <a href='javascript:void(0);' onclick="javascript:tagActionWithUrl('<?php echo $tag->getTag()->tag_id; ?>', '<?php echo $this->url(array('tag' => $tag->getTag()->tag_id), 'sitegroupvideo_browse', true); ?>');">
	                        #<?php echo $tag->getTag()->text ?></a>
                      <?php elseif ($this->module_name == 'sitestorevideo'): ?>
	                        <a href='javascript:void(0);' onclick="javascript:tagActionWithUrl('<?php echo $tag->getTag()->tag_id; ?>', '<?php echo $this->url(array('tag' => $tag->getTag()->tag_id), 'sitestorevideo_browse', true); ?>');">
	                        #<?php echo $tag->getTag()->text ?></a>
	                      <?php else: ?>
	                        <a href='javascript:void(0);' onclick='javascript:tagActionSVV(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text ?></a>
	                      <?php endif; ?>&nbsp;
	                    <?php endif; ?>
	              <?php endforeach; ?>
	            </div>  
            <?php endif; ?>
              <?php if ($this->module_name == 'avp'): ?>
               <?php /* @Reference : 'All-in-one Video - Platinum Edition' by author myseplugins.com
                * @Comments : We have modified our code to make Video Lightbox Viewer Plugin compatible to their plugin.
                */ ?>
                <?php $tags = Zend_Json::decode($this->video->tags); ?>
                <?php if (count($tags) > 0): ?>
                  <div class="photo_right_content_tags">             
                    <b> <?php echo $this->translate('Tags:') ?></b>                      
                     <?php foreach ($tags as $tag): ?>
                  <a href='javascript:void(0);' onclick="javascript:$('search_query').value='<?php echo $this->string()->escapeJavascript($tag); ?>';$('search_tag').submit();">
                  #<?php echo $tag; ?></a>&nbsp;
                  <?php endforeach; ?>
                  </div>
                  <form action='<?php echo $this->url(array(), 'avp_general', true); ?>' method='post' id='search_tag'><input type='hidden' id='search_query' name='query'></form>
                <?php endif; ?>
              <?php endif; ?>
            <div id="photo_view_comment" class="photo_right_content_comments">
              <?php echo $this->action("list", "comment", "seaocore", array("type" => $this->video->getType(), "id" => $this->video->getIdentity(), "show_bottom_post" => 1)); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="photo_right_content_add" id="ads">
      </div>
    </div>
  </div>
  <div class="lightbox_btm_bl">
    <div class="lightbox_btm_bl_left">
      <div id="photo_owner_lb_fullscreen" class='lightbox_btm_bl_left_photo'>      
        <?php $parent='';        
        if(   $this->video->getType() != 'video' || ($this->video->getType() == 'video' && !empty ($this->video->parent_type)) )
        $parent= $this->video->getParent();        
//          echo $this->htmlLink($this->video->getOwner()->getHref(), $this->itemPhoto($this->video->getOwner(), 'thumb.icon', '', array('align' => 'left')), array('class' => 'item_photo'));       
        ?>
      </div>
      <div class="lightbox_btm_bl_left_links">
        <div class="lbbll_ml" >
          <?php 
            if (!empty($parent) && $parent->getType() != 'user'):
              $belongsTo =$parent;
            else :
              $belongsTo =$this->video->getOwner();
            endif;
            echo $this->translate("%s's Videos", $this->htmlLink($belongsTo, $belongsTo->getTitle()));
            ?>
        </div>     
        <br class="clr" />
        <div class="lbbll_ol">
          <?php echo $this->timestamp($this->video->creation_date) ?>
        </div>
        <div class="lbbll_s">-</div>        
        <div class="lbbll_ol p_r">
          <div id="photos_options_area" class="lbbll_ol_uop">
            <?php if ($this->can_edit): ?>
              <?php echo $this->htmlLink($this->editLinkParamsArray, $this->translate('Edit Video'), array()) ?>            <?php endif; ?>
            <?php if ($this->can_delete && $this->video->status != 2): ?>
              <?php echo $this->htmlLink($this->deleteLinkParamsArray, $this->translate('Delete Video'), array(
                  'class' => 'smoothbox' )) ?>
            <?php endif; ?>
            <?php if ($this->allowViewSitepage): ?>    
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagevideo', 'controller' => 'index', 'action' => 'add-video-of-day', 'video_id' => $this->video->video_id, 'format' => 'smoothbox'), $this->translate('Make Video of the Day'), array(
                    'class' => 'smoothbox')) ?>
            <?php elseif($this->allowViewSitebusiness):?>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebusinessvideo', 'controller' => 'index', 'action' => 'add-video-of-day', 'video_id' => $this->video->video_id, 'format' => 'smoothbox'), $this->translate('Make Video of the Day'), array(
                    'class' => 'smoothbox'
                ))
                ?>
            <?php elseif ($this->allowViewSitegroup): ?>    
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitegroupvideo', 'controller' => 'index', 'action' => 'add-video-of-day', 'video_id' => $this->video->video_id, 'format' => 'smoothbox'), $this->translate('Make Video of the Day'), array(
                    'class' => 'smoothbox')) ?>
            <?php elseif ($this->allowViewSitestore): ?>    
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitestorevideo', 'controller' => 'index', 'action' => 'add-video-of-day', 'video_id' => $this->video->video_id, 'format' => 'smoothbox'), $this->translate('Make Video of the Day'), array(
                    'class' => 'smoothbox')) ?>
            <?php endif; ?>
            <?php if(($this->allowMakeFeatured) && isset ($this->video->featured)):?>
            <a href="javascript:void(0)" onclick="en4.sitevideoview.setFeatured($(this),'<?php echo $this->video->getGuid()?>')" id="siteviedoview_featured"> <?php echo ($this->video->featured == 1) ? $this->translate("Make Un-Featured") :  $this->translate("Make Featured") ?> </a>
            <?php endif; ?>
              
            <?php if(!empty($this->can_edit) && $this->canMakeHighlighted && isset ($this->video->highlighted)):?>
            <a href="javascript:void(0)" onclick="en4.sitevideoview.setHighlighted($(this),'<?php echo $this->video->getGuid()?>')" > <?php echo ($this->video->highlighted == 1) ? $this->translate("Make Un-highlighted") :  $this->translate("Make highlighted") ?> </a>
            <?php endif; ?>
            <?php /* @Reference : 'All-in-one Video - Platinum Edition' by author myseplugins.com
             * @Comments : We have modified our code to make Video Lightbox Viewer Plugin compatible to their plugin.
             */ ?>
           <?php if($this->module_name == 'avp' && $this->can_playlist):?>
           <?php echo $this->htmlLink(array('action' => 'do-playlist', 'id' => $this->video->getIdentity(), 'route' => 'avp_general', 'format' => 'smoothbox'), $this->translate("Add to Playlist"), array('id' => 'avp_playlist_btn', 'class' => 'smoothbox')); ?>
          <?php endif; ?>
            <?php if (!$this->video->isOwner($this->viewer) && $this->viewer_id): ?>
                <?php if ($this->module_name == 'avp'): ?>
                <?php /*  @Reference : 'All-in-one Video - Platinum Edition' by author myseplugins.com 
                 * @Comments : We have modified our code to make Video Lightbox Viewer Plugin compatible to their plugin.
                 */ ?>
                  <?php echo $this->htmlLink(array('action' => 'do-favorite', 'id' => $this->video->getIdentity(), 'route' => 'avp_general', 'format' => 'smoothbox'), ($this->favorite ? $this->translate("Remove from Favorites") : $this->translate("Add to Favorites")), array('id' => 'avp_rate_btn', 'class' => 'smoothbox avp_favorite')); ?>                
                <?php endif; ?>
              <?php
              echo $this->htmlLink(array(
                  'module' => 'core',
                  'controller' => 'report',
                  'action' => 'create',
                  'route' => 'default',
                  'subject' => $this->video->getGuid(),
                  'format' => 'smoothbox'
                      ), $this->translate("Report"), array(
                  'class' => 'smoothbox'
              ));
              ?>
            <?php endif ?>
            <?php if ($this->can_embed): ?>
              <?php $titleLink = $this->module_name == 'ynvideo' ? 'HTML Code':'Embed';
              echo $this->htmlLink($this->embedLinkParamsArray, $this->translate($titleLink), array(
                  'class' => 'smoothbox'
              ));
              ?>
            <?php endif ?> 
          </div>
          <span  class="op_box">
            <?php echo $this->translate('Options'); ?>
            <span class="sea_pl_at"></span>        
          </span> 
        </div>
      </div>
    </div>
    <div class="lightbox_btm_bl_right"> 
      <?php if($this->canShowSuggestFriendLink):?>
      <span class="lightbox_btm_bl_btn"  onclick="Smoothbox.open('<?php 
        if ($this->module_name == 'sitepagevideo') :
          echo $this->escape($this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $this->video->getIdentity(), 'sugg_type' => 'page_video'), 'default', true));
         elseif ($this->module_name == 'sitebusinessvideo'):
          echo $this->escape($this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $this->video->getIdentity(), 'sugg_type' => 'business_video'), 'default', true));
         elseif ($this->module_name == 'sitegroupvideo') :
          echo $this->escape($this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $this->video->getIdentity(), 'sugg_type' => 'group_video'), 'default', true));
          elseif ($this->module_name == 'sitestorevideo') :
          echo $this->escape($this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $this->video->getIdentity(), 'sugg_type' => 'store_video'), 'default', true));
         else :
          echo $this->escape($this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' => 'switch-popup', 'modName' => $this->module_name, 'modContentId' => $this->video->getIdentity(), 'modError' => 1, 'format' => 'smoothbox'), 'default', true));
        endif;?>'); return false;" >
          <?php echo $this->translate("Suggest to Friend") ?>
        </span>
      <?php endif; ?>
      <?php
       if ($this->canComment): ?>   
        <span class="lightbox_btm_bl_btn" id="<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>like_link" <?php if ($this->subject()->likes()->isLike($this->viewer)): ?>style="display: none;" <?php endif; ?>onclick="en4.seaocore.likes.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>','1');" title="<?php echo $this->translate('Press L to Like'); ?>"><?php echo $this->translate('Like'); ?></span>

        <span class="lightbox_btm_bl_btn" id="<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>unlike_link" <?php if (!$this->subject()->likes()->isLike($this->viewer)): ?>style="display:none;" <?php endif; ?> onclick="en4.seaocore.likes.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>','1');" title="<?php echo $this->translate('Press L to Unlike'); ?>"><?php echo $this->translate('Unlike'); ?></span>

        <span class="lightbox_btm_bl_btn" onclick="if(fullmode_photo){ switchFullModePhotoSEAO(false); }
            if($('comment-form-open-li_<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>')){
              $('comment-form-open-li_<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>').style.display = 'none';
            }
            $('comment-form_<?php echo $this->subject()->getType() . "_" . $this->subject()->getIdentity() ?>').style.display = '';$('comment-form_<?php echo $this->subject()->getType() . "_" . $this->subject()->getIdentity() ?>').body.focus();"  ><?php echo $this->translate('Comments'); ?></span>  
        <?php endif; ?>  

        <?php if (!empty($this->viewer_id)): ?>
        <?php if($this->module_name == 'avp'):?> <?php /*  @Reference : 'All-in-one Video - Platinum Edition' by author myseplugins.com
         * @Comments : We have modified our code to make Video Lightbox Viewer Plugin compatible to their plugin.
         */ ?>
        <span class="lightbox_btm_bl_btn"  onclick="Smoothbox.open($('avp_video_share').innerHTML);" > <?php echo $this->translate("Social Share") ?>
        </span>
        <?php endif; ?>     
        <span class="lightbox_btm_bl_btn"  onclick="Smoothbox.open('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $this->video->getType(), 'id' => $this->video->getIdentity(), 'format' => 'smoothbox', 'not_parent_refresh' => 1), 'default', true)); ?>'); return false;" > <?php echo $this->translate("Share") ?>
        </span>
        <?php endif; ?>
        <a href="<?php echo $this->subject()->getHref() ?>" target="_blank" style="text-decoration:none;">
        	<span class="lightbox_btm_bl_btn lightbox_btm_bl_btn_video"> 
        		<i></i>
        		<?php echo $this->translate("Go to Video") ?>
        	</span>
        </a>
    </div>
  </div>
<?php else: ?>
<div  class="photo_lightbox_cont" style="background-color: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideoview.lightbox.bgcolor', '#0A0A0A') ?>">
  <div class="video_viewer_video_content" >
    <h4><?php echo $this->translate("Private Page") ?></h4>
      <div class="video_viewer_thumb_wrapper">
        <?php echo $this->htmlLink($this->subject()->getHref(), $this->itemPhoto($this->subject(), 'thumb.normal'), array('class' => 'thumb')) ?>
      </div>
      <div class="video_viewer_video_info">  
        <b><?php echo $this->htmlLink($this->subject()->getHref(), $this->subject()->getTitle()) ?></b>
        <?php
	         $parent='';        
	        if(   $this->video->getType() != 'video' || ($this->video->getType() == 'video' && !empty ($this->video->parent_type)) )
	        $parent= $this->video->getParent(); ?>
	
           <?php if (!empty($parent) && $parent->getType() != 'user'):?>	           
        <div style="margin-bottom:1px;"><b><?php echo $this->htmlLink($parent, $parent->getTitle()); ?></b></div>
	         <?php endif; ?>
      	<div class="video_viewer_video_author">					
	         <?php echo $this->htmlLink($this->video->getOwner()->getHref(), $this->itemPhoto($this->video->getOwner(), 'thumb.icon', '', array('align' => 'left')), array('class' => 'item_photo'));
	        ?>
	        <?php  echo $this->htmlLink($this->video->getOwner(), $this->video->getOwner()->getTitle()); ?>
	      </div>  
      </div>          
      <div class="video_viewer_privacy_msg clr">
        <img src="./application/modules/Seaocore/externals/images/notice.png" alt="" style="width: 20px;" />
        <span>
        <?php if (!$this->video):
           echo $this->translate('The video you are looking for does not exist.');
        elseif ($this->video->status != 1):
          echo $this->translate('The video you are looking for has not been processed yet.');
        elseif($this->module_name== 'avp' && $this->error_message):
          echo $this->error_message;
        else:
        echo $this->translate("You do not have permission to view this private page.");
              endif; ?></span>
      </div>
    </div>
  </div>
  <div class="lightbox_btm_bl">    
  </div>
<?php endif; ?>
<script type="text/javascript">
  en4.sitevideoview.count=<?php echo $this->count;?>;
  en4.core.runonce.add(function() {
	en4.core.language.addData({
      "Close":"<?php echo $this->string()->escapeJavascript($this->translate("Close")); ?>",
      "Press Esc to Close":"<?php echo $this->string()->escapeJavascript($this->translate("Press Esc to Close")); ?>"
    }); 
  if($('editor_svvideo_description'))
   $('editor_svvideo_description').autogrow();
  <?php /*@Reference : 'YouNet Video Plugin' by author YouNet Company
   * @Comments : We have modified our code to make Video Lightbox Viewer Plugin compatible to their plugin.
   */?>
  <?php if ($this->module_name == 'ynvideo') : ?>
        addEventForButtonAddTo();
  <?php endif; ?>
  });
     <?php if ($this->showLink): ?>
    function getSiteviewPrevVideo(){
      en4.sitevideoview.showVideo("<?php echo $this->escape($this->prevVideo->getHref()) ?>",<?php echo $this->jsonInline(array_merge($this->params, array('offset' => $this->PrevOffset, 'is_ajax_lightbox' => 1))); ?>);
    }

    function getSiteviewNextVideo(){
      en4.sitevideoview.showVideo("<?php echo $this->escape($this->nextVideo->getHref()) ?>",<?php echo $this->jsonInline(array_merge($this->params, array('offset' => $this->NextOffset, 'is_ajax_lightbox' => 1))); ?>);
    }
    <?php endif; ?>
  </script>
<?php if(empty($this->is_ajax_lightbox)): ?>
<div id="ads_hidden_siteviewvideo" style="display: none;" >
  <?php echo $this->content()->renderWidget("seaocore.lightbox-ads", array('limit' => 1)) ?>
</div>
<?php endif; ?>
  <div id="avp_video_share" style="display: none;"> 
    <?php if($this->module_name == 'avp'):?>
    <div style="height: 150px; width: 750px;"> 
<?php  /* @Reference : 'All-in-one Video - Platinum Edition' by author myseplugins.com
 * @Comments : We have modified our code to make Video Lightbox Viewer Plugin compatible to their plugin.
 */?>
   <?php echo $this->content()->renderWidget("avp.video-share", array()) ?>
    <div class="fright">      
      <button  onclick="Smoothbox.close();"><?php echo $this->translate('Cancel'); ?></button>
    </div>
    </div>   
    <?php endif;?>
    </div>  
<style type="text/css">
  .video_embed embed, .video_embed > iframe, .video_embed embed, 
    .video_embed > object {
      width: 750px; 
      height: 440px;
    }
    .video_viewer_stats * {
      color: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideoview.lightbox.fontcolor', '#FFFFFF') ?>;
    }
		.lightbox_btm_bl_btn_video{
			background:url(./application/modules/Seaocore/externals/images/plbbi.png) repeat-x bottom;
			text-decoration:none;
			color:#FFFFFF;
		}
		.lightbox_btm_bl_btn_video i {
	    background-image: url(./application/modules/Sitevideoview/externals/images/video.png);
	    background-position:top;
	    float: left;
	    height: 10px;
	    margin-right: 6px;
	    margin-top: 8px;
	    width: 18px;
		}
		.lightbox_btm_bl_btn_video:hover i {
	    background-position:bottom;
		}
</style>
