<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: external.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css'); ?>
<?php if( $this->error == 1 ): ?>
  <?php echo $this->translate('Embedding of videos has been disabled.') ?>
  <?php return ?>
<?php elseif( $this->error == 2 ): ?>
  <?php echo $this->translate('Embedding of videos has been disabled for this video.') ?>
  <?php return ?>
<?php elseif( !$this->video || $this->video->status != 1 ): ?>
  <?php echo $this->translate('The video you are looking for does not exist or has not been processed yet.') ?>
  <?php return ?>
<?php endif; ?>

<?php if( $this->video->type == 3 ):
  $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
  ?>
  <script type='text/javascript'>

    flashembed("video_embed", {
      src: "<?php echo $this->layout()->staticBaseUrl?>externals/flowplayer/flowplayer-3.1.5.swf",
      width: 480,
      height: 386,
      wmode: 'transparent'
    }, {
      config: {
        clip: {
          url: "<?php echo $this->video_location;?>",
          autoPlay: false,
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
  </script>
<?php endif ?>

<script type="text/javascript">
  var pre_rate = <?php echo $this->video->rating;?>;
  var video_id = <?php echo $this->video->video_id;?>;
  var total_votes = <?php echo $this->rating_count;?>;
  
  function set_rating() {
    var rating = pre_rate;
    $('rating_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    for(var x=1; x<=parseInt(rating); x++) {
        $('rate_'+x).set('class', 'fa fa-star');
      }

      for(var x=parseInt(rating)+1; x<=5; x++) {
        $('rate_'+x).set('class', 'fa fa fa-star-o star-disable');
      }

      var remainder = Math.round(rating)-rating;
      if (remainder <= 0.5 && remainder !=0){
        var last = parseInt(rating)+1;
        $('rate_'+last).set('class', 'fa fa-star-half-o');
      }
  }

  en4.core.runonce.add(set_rating);
</script>

<div class="sesvideo_video_view_container clear sesbasic_clearfix sesbasic_bxs sesvideo_external_video_preview sesbm">
  <?php if( $this->video->type == 3 ): ?>
    <div id="video_embed" class="sesvideo_view_embed clear sesbasic_clearfix"></div>
  <?php else: ?>
    <div class="sesvideo_view_embed clear sesbasic_clearfix">
      <?php echo $this->videoEmbedded ?>
    </div>
  <?php endif; ?> 
<?php if(!$this->isMap){ ?>
  <div class="sesvideo_external_video_btn_cont clear sesbasic_clearfix">   
    <h2 class="sesvideo_view_title sesbasic_clearfix">
      <?php echo $this->video->getTitle() ?>
    </h2>
    <div class="sesvideo_view_author">
      <div class="sesvideo_view_author_photo">  
        <?php echo $this->htmlLink($this->video->getParent(), $this->itemPhoto($this->video->getParent(), 'thumb.icon')); ?>
      </div>
      <div class="sesvideo_view_author_info">
        <div class="sesvideo_view_author_name sesbasic_text_light">
          <?php echo $this->translate('By') ?>
          <?php echo $this->htmlLink($this->video->getParent(), $this->video->getParent()->getTitle()) ?>
        </div>
        <div class="sesvideo_view_date sesbasic_text_light">
          <?php echo $this->translate('Posted') ?>
          <?php echo $this->timestamp($this->video->creation_date) ?>
        </div>
      </div>
    </div>
    <div class="sesvideo_view_statics">
      <div id="album_rating" class="sesbasic_rating_star sesvideo_view_rating">
        <span id="rate_1" class="fa fa-star"></span>
        <span id="rate_2" class="fa fa-star" ></span>
        <span id="rate_3" class="fa fa-star" ></span>
        <span id="rate_4" class="fa fa-star" ></span>
        <span id="rate_5" class="fa fa-star" ></span>
        <span id="rating_text" class="sesbasic_rating_text"><?php echo $this->translate('click to rate');?></span>
      </div>
      <div class="sesvideo_view_stats sesvideo_list_stats sesbasic_text_light">
        <span><i class="fa fa-thumbs-up"></i><?php echo $this->translate(array('%s like', '%s likes', $this->video->like_count), $this->locale()->toNumber($this->video->like_count)); ?></span>
        <span><i class="fa fa-comment"></i><?php echo $this->translate(array('%s comment', '%s comments', $this->video->comment_count), $this->locale()->toNumber($this->video->comment_count))?></span>
        <span><i class="fa fa-eye"></i><?php echo $this->translate(array('%s view', '%s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?></span>
      </div>
    </div>
    <div class="sesvideo_view_meta sesvideo_list_stats sesbasic_text_light clear sesbasic_clearfix">
      <?php if( $this->category ): ?>
        <span><i class="fa fa-folder-open"></i> <?php echo $this->htmlLink(array('route' => 'sesvideo_general', 'QUERY' => array('category_id' => $this->category->category_id)), $this->translate($this->category->category_name)) ?></span>
      <?php endif; ?>
      <?php if (count($this->videoTags) ): ?>
        <span>
          <i class="fa fa-tag"></i> 
          <?php foreach ($this->videoTags as $tag): ?>
            <a href='javascript:void(0);'>#<?php echo $tag->getTag()->text?></a>&nbsp;
          <?php endforeach; ?>
        </span>
      <?php endif; ?>
    </div>
    <div class="sesvideo_view_desc clear " >
      <?php echo $this->video->description;?>
    </div>
    <?php
      //custom field data
      $customMetaFields = Engine_Api::_()->sesvideo()->getCustomFieldMapData($this->video);
      if(count($customMetaFields)>0){
        echo '<div class="sesvideo_view_fields clear sesbasic_clearfix">';
        foreach($customMetaFields as $valueMeta){
          echo '<div class="clear"><span class="floatL sesvideo_view_field_ques"><b>'. $valueMeta['label']. '</b></span><span class="sesvideo_view_field_value">'. '     '.$valueMeta['value'].'</span></div>';
        }
        echo '</div>';
      }
    ?>
  
    <?php /*
      <div class='sesvideo_view_options sesvideo_options_buttons'>
        <?php if( $this->can_edit ): ?>
          <?php echo $this->htmlLink(array(
            'route' => 'default',
            'module' => 'sesvideo',
            'controller' => 'index',
            'action' => 'edit',
            'video_id' => $this->video->video_id
          ), $this->translate('Edit Video'), array(
            'class' => 'sesbasic_button fa fa-pencil'
          )) ?>
          &nbsp;|&nbsp;
        <?php endif;?>
        <?php if( $this->can_delete && $this->video->status != 2 ): ?>
          <?php echo $this->htmlLink(array(
            'route' => 'default',
            'module' => 'sesvideo',
            'controller' => 'index',
            'action' => 'delete',
            'video_id' => $this->video->video_id,
            'format' => 'smoothbox'
          ), $this->translate('Delete Video'), array(
            'class' => 'sesbasic_button smoothbox fa fa-trash'
          )) ?>
          &nbsp;|&nbsp;
        <?php endif;?>
        <?php echo $this->htmlLink(array(
          'module'=> 'activity',
          'controller' => 'index',
          'action' => 'share',
          'route' => 'default',
          'type' => 'video',
          'id' => $this->video->getIdentity(),
          'format' => 'smoothbox'
        ), $this->translate("Share"), array(
          'class' => 'sesbasic_button smoothbox fa fa-comment'
        )); ?>
        &nbsp;|&nbsp;
        <?php echo $this->htmlLink(array(
          'module'=> 'core',
          'controller' => 'report',
          'action' => 'create',
          'route' => 'default',
          'subject' => $this->video->getGuid(),
          'format' => 'smoothbox'
        ), $this->translate("Report"), array(
          'class' => 'sesbasic_button smoothbox fa fa-flag'
        )); ?>
      	<?php echo $this->action("list", "comment", "core", array("type"=>"video", "id"=>$this->video->video_id)) ?>
     * 
     */ ?>
    </div>
<?php } ?>
	</div>
</div>