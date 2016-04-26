<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css'); ?>  

<div class="sesbasic_clearfix">
  <ul class="sesvideo_category_grid_listing sesbasic_clearfix clear sesbasic_bxs">	
    <li class="sesvideo_catbase_list_head sesbm" style="display:none;"><?php echo $this->translate("All Categories"); ?></li>
    <?php foreach( $this->paginator as $item ):  ?>
      <li class="sesvideo_category_grid sesbm <?php echo $this->mouse_over_title ? 'sesvideo_category_grid_hover' : ''; ?>" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
        <a href="<?php echo $item->getHref(); ?>">
          <div class="sesvideo_category_grid_img">
            <?php if($item->thumbnail != '' && !is_null($item->thumbnail) && intval($item->thumbnail)){ ?>
              <span class="sesvideo_animation" style="background-image:url(<?php echo  Engine_Api::_()->storage()->get($item->thumbnail)->getPhotoUrl('thumb.thumb'); ?>);"></span>
            <?php } ?>
          </div>
          <div class="sesvideo_category_grid_overlay sesvideo_animation"></div>
          <div class="sesvideo_category_grid_info">
            <div>
              <div class="sesvideo_category_grid_title">
                <?php if(isset($this->icon) && $item->cat_icon != '' && !is_null($item->cat_icon) && intval($item->cat_icon)){ ?>
                  <img class="sesvideo_animation" src="<?php echo  Engine_Api::_()->storage()->get($item->cat_icon)->getPhotoUrl('thumb.icon'); ?>" />
                <?php } ?>
                <?php if(isset($this->title)){ ?>
                <span class="sesvideo_animation"><?php echo $this->translate($item->category_name); ?></span>
                <?php } ?>
                <?php if($this->countVideos){ ?>
                  <span class="sesvideo_category_grid_stats sesvideo_animation"><?php echo $this->translate(array('%s video', '%s videos', $item->total_videos_categories), $this->locale()->toNumber($item->total_videos_categories))?></span>
                <?php } ?>
              </div>
            </div>
          </div>
        </a>
      </li>
    <?php endforeach; ?>
    <?php  if(  count($this->paginator) == 0){  ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('No category found.');?>
        </span>
      </div>
    <?php } ?>
  </ul>
</div>