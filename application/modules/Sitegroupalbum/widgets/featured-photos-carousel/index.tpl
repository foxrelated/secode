<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
  $this->headLink()
     ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/styles/sitegroup_featured_carousel.css');

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitegroupalbum/externals/styles/style_sitegroupalbum.css');
     
  $this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/sitegroupslideitmoo-1.1_full_source.js');
?>

<a id="group_profile_members_anchor1" style="position:absolute;"></a>
<script language="javascript" type="text/javascript">
  var module = 'Sitegroupalbum';
</script>
<script language="javascript" type="text/javascript">
  var slideshowalbum;
  window.addEvents ({
    'domready': function() { 
      slideshowalbum = new SocialengineSlideItMoo({
        module : 'Sitegroupalbum',
        fwdbck_click:1,
        slide_element_limit:1,
        startindex:-1,
        category_id : <?php echo $this->category_id;?>,
        in_one_row:<?php echo  $this->inOneRow_photo?>,
        no_of_row:<?php echo  $this->noOfRow_photo?>,
        curnt_limit:<?php echo $this->totalItemShow_photo;?>,
        limit:<?php echo $this->totalItemShow_photo*2;?>,
        total:<?php echo $this->totalCount_photo; ?>,
        overallContainer: 'Sitegroupalbum_SlideItMoo_outer',
        elementScrolled: 'Sitegroupalbum_SlideItMoo_inner',
        thumbsContainer: 'Sitegroupalbum_SlideItMoo_items',
        slideVertical: <?php echo $this->vertical?>,
        itemsVisible:1,
        elemsSlide:1,
        call_count:1,
        duration:<?php echo  $this->interval;?>,
        foward:'Sitegroupalbum_SlideItMoo_forward',
        bck:'Sitegroupalbum_SlideItMoo_back',
        itemsSelector: '.' +'Sitegroupalbum_SlideItMoo_element',
        itemWidth:<?php echo 146 * $this->inOneRow_photo?>,
        itemHeight:<?php echo 146 * $this->noOfRow_photo?>,
        showControls:1,
        navs:{ /* starting this version, you'll need to put your back/forward navigators in your HTML */
				fwd:'.Sitegroupalbum_SlideItMoo_forward', /* forward button CSS selector */
				bk:'.Sitegroupalbum_SlideItMoo_back' /* back button CSS selector */
				},
        startIndex:1,
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) { slideshowalbum.options.call_count = 1;
        }
      });

      $('Sitegroupalbum_SlideItMoo_back').addEvent('click', function () {slideshowalbum.sendajax(-1,slideshowalbum,'Sitegroupalbum',"<?php echo $this->url(array('action'=>'featured-photos-carousel'),"sitegroupalbum_extended",true); ?>");
        slideshowalbum.options.call_count = 1;

      });

      $('Sitegroupalbum_SlideItMoo_forward').addEvent('click', function () { slideshowalbum.sendajax(1,slideshowalbum,'Sitegroupalbum',"<?php echo $this->url(array('action'=>'featured-photos-carousel'),"sitegroupalbum_extended",true); ?>");
        slideshowalbum.options.call_count = 1;
      });
     
      if((slideshowalbum.options.total -slideshowalbum.options.curnt_limit)<=0){
        // hidding forward button
       document.getElementById('Sitegroupalbum_SlideItMoo_forward').style.display= 'none';
       document.getElementById('Sitegroupalbum_SlideItMoo_back_disable').style.display= 'none';
      }
    }
  });
</script>
<?php
$module = 'Sitegroupalbum';
$photoSettings=  array();
$photoSettings['class'] = 'thumb';

?>
<ul class="Sitegroupcontent_featured_slider">
  <li>
		<?php
    $extra_width=0;
    $extra_height=0;    
        if (empty($this->vertical)):
        $typeClass='horizontal';
         if ($this->totalCount_photo > $this->totalItemShow_photo):
          $extra_width = 60;
          endif;
          $prev='back';
          $next='forward';
        else:
        	$typeClass='vertical';
        if ($this->totalCount_photo > $this->totalItemShow_photo):
          $extra_height=50;
          endif;
          $prev='up';
          $next='down';
        endif;
     ?>
    <div id="Sitegroupalbum_SlideItMoo_outer" class="Sitegroupcontent_SlideItMoo_outer Sitegroupcontent_SlideItMoo_outer_<?php echo $typeClass;?>" style="height:<?php echo 146*$this->heightRow+$extra_height;?>px; width:<?php echo (146*$this->inOneRow_photo)+$extra_width;?>px;">
      <div class="Sitegroupcontent_SlideItMoo_back" id="Sitegroupalbum_SlideItMoo_back" style="display:none;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/photo/slider-$prev.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/photo/slider-'.$prev.'-active.png";','onMouseOut'=>'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/photo/slider-'.$prev.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitegroupcontent_SlideItMoo_back" id="Sitegroupalbum_SlideItMoo_back_loding" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Core/externals/images/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitegroupcontent_SlideItMoo_loding'));  ?>
      </div>      
       <div class="Sitegroupcontent_SlideItMoo_back_disable" id="Sitegroupalbum_SlideItMoo_back_disable" style="display:block;cursor:default;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/photo/slider-$prev-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div id="Sitegroupalbum_SlideItMoo_inner" class="Sitegroupcontent_SlideItMoo_inner">
        <div id="Sitegroupalbum_SlideItMoo_items" class="Sitegroupcontent_SlideItMoo_items" style="height:<?php echo 146*$this->heightRow;?>px;">
          <div class="Sitegroupcontent_SlideItMoo_element Sitegroupalbum_SlideItMoo_element" style="width:<?php echo 146*$this->inOneRow_photo;?>px;">
              <div class="Sitegroupcontent_SlideItMoo_contentList">
               <?php  $i=0; ?>
                  <?php foreach ($this->featuredPhotos as $photo):?>
                       <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
												$tab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $photo->group_id, $layout);?>
                       <div class="featured_thumb_content">
                          <a href="<?php echo $photo->getHref() ?>"  <?php if ($this->showLightBox): ?> onclick="openSeaocoreLightBox('<?php echo $photo->getHref()?>');return false;" <?php endif; ?> title="<?php echo $photo->title; ?>" class="thumb_img">
                          	<span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
                          </a>
                          <span class="show_content_des">
                            <?php
								              $owner = $photo->getOwner();
								              $parent = Engine_Api::_()->getItem('sitegroup_album', $photo->album_id);
								              echo $this->translate('in ').
								                  $this->htmlLink($parent->getHref(), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));
														?>
														<?php $sitegroup_object = Engine_Api::_()->getItem('sitegroup_group', $photo->group_id);?>
														<?php
														$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.title.truncation', 18);
														$tmpBody = strip_tags($sitegroup_object->title);
														$group_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
														?>
														<?php echo $this->translate("of ") . $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($photo->group_id, $photo->user_id, $photo->getSlug()),  $group_title,array('title' => $sitegroup_object->title)) ?> 
                            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1)):?>     
															<?php echo $this->translate('by ').
																		$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title' => $owner->getTitle()));?>
                            <?php endif;?>
                          </span>
                      </div>
                   <?php  $i++; ?>
                  <?php endforeach; ?>
                <?php for($i; $i<($this->heightRow *$this->inOneRow_photo);$i++):?>
                <div class="featured_thumb_content"></div>
                <?php endfor; ?>
              </div>
           </div>
        </div>
      </div>
      <div class="Sitegroupcontent_SlideItMoo_forward" id ="Sitegroupalbum_SlideItMoo_forward">
      	<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/photo/slider-$next.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'. $this->layout()->staticBaseUrl        . 'application/modules/Sitegroup/externals/images/photo/slider-'.$next.'-active.png";','onMouseOut'=>'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/photo/slider-'.$next.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitegroupcontent_SlideItMoo_forward" id="Sitegroupalbum_SlideItMoo_forward_loding"  style="display: none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Core/externals/images/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitegroupcontent_SlideItMoo_loding'));  ?>
      </div>
      <div class="Sitegroupcontent_SlideItMoo_forward_disable" id="Sitegroupalbum_SlideItMoo_forward_disable" style="display:none;cursor:default;">
      	<?php  echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/photo/slider-$next-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
    </div>
    <div class="clear"></div>
  </li>
</ul>
