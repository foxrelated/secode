<?php
if (!empty($this->is_preview)):  
  $this->widget_place = 'middle1';
  ?>
  <div class="layout_advancedslideshow_<?php echo $this->widget_place; ?>_advancedslideshows">
    <?php
  elseif (empty($this->advancedslideshow->widget_content_id)):
    $this->widget_place = $this->advancedslideshow->advancedslideshow_id;
    ?>
    <div class="layout_advancedslideshow_<?php echo $this->widget_place; ?>_advancedslideshows"> <?php
endif;
$noob_elements = @unserialize($this->noob_elements);
if ($noob_elements['noob_effect'] == "bounced") {
  $transition = 'Fx.Transitions.Bounce.easeOut';
} else if ($noob_elements['noob_effect'] == "elastic") {
  $transition = 'Fx.Transitions.Elastic.easeOut';
} else {
  $transition = 'null';
}

$this->autoPlay = true;
$image_text_var = empty($isDemo) ? '' : $image_text_var;
//$image_text_var = '';
$title_link_var = $thumb_span_var = $thumbnail_var = $pagination_var = $pane_var = $image_var = '';
$image_count = 1;
$baseUrl = $this->layout()->staticBaseUrl;
$slideshow_id = empty($isDemo) ? $this->advancedslideshow->getIdentity() : 1;
$this->num_of_slideshow = empty($isDemo) ? @COUNT($this->getContentArray) : 5;

$slidesPerRow = floor($this->width / 50);
$totalRow = ceil(count($this->getContentArray) / $slidesPerRow);

$slidesPerColumn = floor($this->height / 50);
$totalColumn = ceil(count($this->getContentArray) / $slidesPerColumn);

$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_noobslideshow.css');
if ($this->num_of_slideshow):
  $this->headScript()
          ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');

  // Starting work for "Slide Show".
  if (empty($isDemo)) {
    foreach ($this->getContentArray as $item) {
      $slideContent = null;
      if (array_key_exists("slide_html", $item)) {
        $slideContent = $item["slide_html"];
        $caption = null;
        $caption = $item["caption"];
      } else {
        $listing_image_id = $item["image_id"];
        $mainImage = $item['mainImage'];
        $caption = null;
        $caption = $item["caption"];
        if (empty($item["url"])) {
          $slideContent = '<img class="" alt="" src="' . $mainImage . '" width=" ' . $this->width . ' " height=" ' . $this->height . ' ">';
        } else {
          $temp_target = '';
          if (!empty($this->target))
            $temp_target = 'target="_blank"';
          $slideContent = '<a href="http://' . $item['url'] . '" ' . $temp_target . '><img class="" alt="" src="' . $mainImage . '" width=" ' . $this->width . ' " height=" ' . $this->height . ' "></a>';
        }
      }
      if ($this->caption == "true") {
        if (!empty($item['caption'])) {
          $slide_caption = '<div class="noob_slidebox_caption">' . $caption . '</div> ';
        } else {
          $slide_caption = '';
        }
      }

      $image_text_var .= '<div class="noob_slidebox" style="width: ' . $this->width . 'px; height: ' . $this->height . 'px;">
                          <div class="noob_slidebox_photo">
                            ' . $slideContent . $slide_caption . '            
                          </div>
                       </div>     
                       <div class="noob_slideshow_contents">
                         <h3 style="display:none">
                          <span>' . $image_count++ . '_caption_title:' . $caption . ' _caption_link:</span>
                        </h3>
                      </div>';
    }
  }

    ?>
      <script type="text/javascript">
        var url_base = '/sev4v/';
        en4.core.runonce.add(function() {                                                  
          var height='<?php echo $this->height ?>';
          var width='<?php echo $this->width ?>';
          var num_of_slidehsow = "<?php echo $this->num_of_slideshow; ?>";
          var handles8_more = $$('.handles8_more span');
                                    
  <?php if ($this->mouseEnterEvent): ?>
          var handle_event = 'mouseenter';
  <?php else: ?>  
          var handle_event = '';
  <?php endif; ?>  
        
                  
        handles8_more = document.getElement('.layout_advancedslideshow_<?php echo $this->widget_place; ?>_advancedslideshows').getElements('.handles8_more span');
                                        
        var nS8 = new noobSlide({                                                                                   
          box: $('noob_featured_im_te_advanced_box_<?php echo $slideshow_id; ?>'),
          items: $$('#noob_featured_im_te_advanced_box_<?php echo $slideshow_id; ?> h3'),
          size: (width),
          handles: $$('#handles8 span'),
          handle_event: handle_event,          
          addButtons: {previous: $('noob_featured_prev8_<?php echo $slideshow_id; ?>'),next: $('noob_featured_next8_<?php echo $slideshow_id; ?>'), stop: $('noob_featured_stop8_<?php echo $slideshow_id; ?>'), play: $('noob_featured_play8_<?php echo $slideshow_id; ?>') },
          interval: '<?php echo $this->delay; ?>',
          fxOptions: {
            duration: '<?php echo $this->duration ?>',
            transition: <?php echo $transition; ?>,
            wait: false
          },
          autoPlay: <?php echo $noob_elements['noob_autoplay']; ?>,
          mode: 'horizontal',
          onWalk:  function(currentItem, currentHandle){
            // Finding the current number of index.
            var current_index = this.items[this.currentIndex].innerHTML;
            var current_start_title_index = current_index.indexOf(">");
            var current_last_title_index = current_index.indexOf("</span>");
            // This variable containe "Index number" and "Title" and we are finding index.
            var current_title = current_index.slice(current_start_title_index + 1, current_last_title_index);
            // Find out the current index id.
            var current_index = current_title.indexOf("_");
            // "current_index" is the current index.
            current_index = current_title.substr(0, current_index);

            handles8_more.removeClass('checked');
            if($('handles8_more_' + current_index + '_' + '<?php echo $slideshow_id; ?>')) {
              $('handles8_more_' + current_index + '_' + '<?php echo $slideshow_id; ?>').getElement('span').addClass('checked');
            }
          }
        });

        //more handle buttons
        nS8.addHandleButtons(handles8_more);
        //walk to item 3 witouth fx
        nS8.walk(0,false,true);
      });
      </script>

      <div id="slideshow_wrapper_<?php echo $slideshow_id; ?>" class="noob_slideshow" style="width:<?php echo $this->width; ?>px;height:<?php echo $this->height; ?>px;">
        <div class="noob_slideshow_mask" style="width: <?php echo $this->width; ?>px; height:<?php echo $this->height; ?>px;">
          <div id="noob_featured_im_te_advanced_box_<?php echo $slideshow_id; ?>" class="featured_slideshow_advanced_box">
            <?php echo $image_text_var; ?>
          </div>
        </div>

        <div class="noob_slideshow_options"  style="<?php if ($this->num_of_slideshow < 2 || $this->controller == 'false'): ?> display:none;<?php endif; ?>"  >
          <ul class="buttons" >
            <li id="noob_featured_prev8_<?php echo $slideshow_id; ?>" class="noob_pre noob_pre_nxt noob_slideshow_controller" title="<?php echo $this->translate("Previous") ?>" ><i></i></li>
            <li id="noob_featured_stop8_<?php echo $slideshow_id; ?>" class="noob_stop noob_stop_play  noob_slideshow_controller" title="Stop"><i></i></li>
            <li id="noob_featured_play8_<?php echo $slideshow_id; ?>" class="noob_play noob_stop_play noob_slideshow_controller" title="Play"><i></i></li>
            <li id="noob_featured_next8_<?php echo $slideshow_id; ?>" class="noob_nxt noob_pre_nxt noob_slideshow_controller" title="<?php echo $this->translate("Next") ?>"><i></i></li>
          </ul>
        </div>
        <?php if (empty($noob_elements['noob_walk']) && ($this->thumb == 'true')): ?>
          <div class="sr_media_slideshow_paging_thumbs" style='width:<?php echo $this->width; ?>px;bottom:-<?php echo $totalRow * 50 + 10 ?>px;top:0;position:relative'>

            <div class="noob_slideshow_paging_thumbs" style='width:<?php echo $this->width; ?>px;'>

              <div class="wrapper" style="<?php if ($this->num_of_slideshow < 2): ?> display:none;<?php endif; ?>" >
                <?php
                $i = 1;
                foreach ($this->getContentArray as $key => $item):
                  ?>
                  <p  class="handles8_more" id="handles8_more_<?php echo $i; ?>_<?php echo $slideshow_id; ?>"> 
                    <?php
                    if (array_key_exists("image_id", $item)) {
                      $thumbImage = $item['thumbImage'];  
                      $itemPhoto = '<img class="" alt="" src="' . $thumbImage . '" width="48" height="48">';
                    } else {
                      if (!empty($item["thumb_id"])) {
                        $html_thumbnails = Engine_Api::_()->advancedslideshow()->displayPhoto($item["thumb_id"], 'thumb.icon');
                        $itemPhoto = '<img class="" alt="" src="' . $html_thumbnails . '" width="48" height="48">';
                      } else {
                        $itemPhoto = '<img class="" alt="" src="" width="48" height="48">';
                      }
                    }
                    ?>
                    <span class="<?php echo $i == 1 ? 'checked' : ''; ?>">
                      <?php echo $itemPhoto; ?>
                    </span>
                  </p>
                  <?php
                  $i++;
                endforeach;
                ?>
              </div>             
            </div>  
          <?php elseif (!empty($noob_elements['noob_walk'])) : ?>
            <div class="noob_slideshow_paging_buttons <?php
        if ($noob_elements['noob_walk_position'] == 'left') {
          echo "fleft";
        } else if ($noob_elements['noob_walk_position'] == 'right') {
          echo "fright";
        }
            ?>" style="
                 margin-top:<?php
             if (!empty($noob_elements['noob_walkDiv']))
               echo "-30";else
               echo "0";
            ?>px;
                 ">
              <div class="wrapper" style="<?php if ($this->num_of_slideshow < 2): ?> display:none;<?php endif; ?>" >
                <?php for ($i = 1; $i <= $this->num_of_slideshow; $i++): ?>
                  <p  class="handles8_more" id="handles8_more_<?php echo $i; ?>_<?php echo $slideshow_id; ?>">
                    <span class="<?php echo $i == 1 ? 'checked' : ''; ?> <?php if (empty($noob_elements['noob_walkIcon']))
              echo 'walking_button_square' ?>" style="
                          height:<?php echo empty($noob_elements['noob_walkSize']) ? 8 : $noob_elements['noob_walkSize']; ?>px;
                          width:<?php echo empty($noob_elements['noob_walkSize']) ? 8 : $noob_elements['noob_walkSize']; ?>px;
                          "></span>
                  </p>
                <?php endfor; ?>
              </div>
            </div>      
          <?php endif; ?>  
        </div>
      <?php endif; ?>


      <?php if ($this->position_caption == 2 || $this->position_caption == 3): ?>
        <style type="text/css">
          .noob_slidebox_caption {
            background:<?php echo $this->colorback_caption; ?>;
            width: 29%;
            padding: 22px;
            bottom:100px;
            white-space:inherit;
            position:absolute;
            ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=75)";
            filter: alpha(opacity=75);
            -khtml-opacity:0.75;
            -moz-opacity: 0.75;
            opacity: 0.75;
          }
          .noob_slidebox_caption > h1 {
            color:#fff;
            font-size: 26px;
            font-weight: bold;
          }
          .noob_slidebox_caption > p {
            font-size: 13px;
            line-height: 20px;
            margin: 8px 0 0;
            color:#fff;
          }

        </style>
        <?php if ($this->position_caption == 2): ?>
          <style type="text/css">
            .noob_slidebox_caption {
              left :0px;
            }  </style>
          <?php elseif ($this->position_caption == 3): ?>
          <style type="text/css">
            .noob_slidebox_caption {       
              right:0px;
            }    
          </style>
        <?php endif; ?>
      <?php else : ?>
        <style type="text/css">
          .noob_slidebox_caption{
            background:<?php echo $this->colorback_caption; ?>;
            <?php
            if (!empty($this->position_caption)) {
              if (empty($noob_elements['noob_walk']) && ($this->thumb == 'true'))
                echo "bottom: 63px;";
              else if (!empty($noob_elements['noob_walk']) && ($this->position_caption == 1)){
                echo "bottom: 30px;";
              } else
                echo "bottom:0px;";
            }else {
              echo "top :0px;";
            }
            ?>
            ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=75)";
            filter: alpha(opacity=75);
            -khtml-opacity:0.75;
            -moz-opacity: 0.75;
            opacity: 0.75;
            position:absolute;
            white-space: nowrap;
            width:100%;
            padding:3px 10px;
          }
          .noob_slidebox_caption p{
            color: White;
            font-size:15px;
            overflow:hidden;
            text-overflow: ellipsis;
          }
        </style>
      <?php endif; ?>
      <style type="text/css">
        .noob_slidebox_photo td img {
          max-height: <?php echo $this->height; ?>px;
          max-width: <?php echo $this->width; ?>px;
        }
        .noob_slidebox_photo{
          height:100%;
          position:relative;
        }
        .noob_slideshow_mask{
          height : <?php echo $this->height; ?>px;
          width : <?php echo $this->width; ?>px;
        }

        .noob_slidebox_photo td{
          text-align:center;
          vertical-align:middle;
          height:100%;
          width:100%;
        }
        .noob_slidebox_photo td img{
          border:none;
          max-height:<?php echo $this->height; ?>px;
          max-width:<?php echo $this->width; ?>px;
        }
        .noob_slideshow_paging_thumbs .wrapper p span{
          margin: 2px;
          border: 2px solid <?php echo $this->thumb_bord_active ?>;
        }
        .noob_slideshow_paging_thumbs{
          background-color:<?php echo $this->thumb_back_color ?>;
          opacity :<?php echo $noob_elements['opacity']; ?>;
        }
        .noob_slideshow_paging_thumbs .wrapper p span.checked{
          border: 2px solid <?php echo $this->thumb_bord_color ?>;
        }
        .noob_slideshow_paging_buttons{
          text-align:center;
        }

        .featured_slideshow_advanced_box{
          position: absolute;
        }

        .noob_slideshow_paging_buttons .wrapper p span:hover,
        .noob_slideshow_paging_buttons .wrapper p span.checked{
          background:<?php echo $noob_elements['noob_bulletactivecolor'] ?>;
          opacity:.9;
          filter:alpha(opacity=900);
        }
        .noob_slideshow_paging_buttons .wrapper p span{
          background-color:<?php echo $noob_elements['noob_bulletcolor'] ?>;
          border-radius:8px;
          box-shadow:0 1px 0 #FFFFFF;
          cursor:pointer;
          float:left;
          font-size:0;
          height:12px;
          margin:0 1px;
          width:12px;
        }

      </style>
      <?php if (!empty($this->is_preview) || empty($this->advancedslideshow->widget_content_id)): ?> </div> <?php endif; ?>