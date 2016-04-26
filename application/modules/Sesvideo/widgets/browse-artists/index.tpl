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
<script type="text/javascript">
  function loadMore() {  
    if ($('load_more'))
      $('load_more').style.display = "<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>";
    if(document.getElementById('load_more'))
      document.getElementById('load_more').style.display = 'none';    
    if(document.getElementById('underloading_image'))
     document.getElementById('underloading_image').style.display = '';
    en4.core.request.send(new Request.HTML({
      method: 'post',              
      'url': en4.core.baseUrl + 'widget/index/mod/sesvideo/name/browse-artists',
      'data': {
        format: 'html',
        page: "<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>",
        viewmore: 1,
        params: '<?php echo json_encode($this->all_params); ?>',
        
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('results_data').innerHTML = document.getElementById('results_data').innerHTML + responseHTML;
        if(document.getElementById('load_more'))
          document.getElementById('load_more').destroy();
        if(document.getElementById('underloading_image'))
         document.getElementById('underloading_image').destroy();
        if(document.getElementById('loadmore_list'))
         document.getElementById('loadmore_list').destroy();
      }
    }));
    return false;
  }
</script>
<?php if(count($this->paginator) > 0): ?>
  <?php if (empty($this->viewmore)): ?>
     <h4><?php echo $this->translate(array('%s artist found.', '%s artists found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())); ?></h4>
    <div class="sesvideo_artist_listing sesbasic_bxs sesbasic_clearfix" id= "results_data">
  <?php endif; ?>
  <?php foreach ($this->paginator as $artist): ?>
  <div class="sesvideo_artist_list" style="height:<?php echo $this->height ?>px;width:<?php echo $this->width ?>px;">
    <div class="sesvideo_artist_list_photo">
      <img src="<?php echo $artist->getPhotoUrl(); ?>">
    </div>
    <a class="sesvideo_artist_list_overlay" href="<?php echo $artist->getHref(); ?>"></a>
    <div class="sesvideo_browse_artist_info">
      <div class="sesvideo_browse_artist_title">
        <?php echo $this->htmlLink($artist->getHref(), $artist->name); ?>
      </div>
      <div class="sesvideo_browse_artist_stats sesvideo_list_stats">
        <?php if(!empty($this->information) && in_array('showfavourite', $this->information)): ?>
          <span title="<?php echo $this->translate(array('%s favorite', '%s favorites', $artist->favourite_count), $this->locale()->toNumber($artist->favourite_count)) ?>">
            <i class="fa fa-heart"></i><?php echo $this->locale()->toNumber($artist->favourite_count);?>
          </span>
        <?php endif; ?>
        <?php if($this->showArtistRating && !empty($this->information) && in_array('showrating', $this->information)): ?>
         <span title="<?php echo $this->translate(array('%s rating', '%s ratings', round($artist->rating,1)), $this->locale()->toNumber(round($artist->rating,1)))?>"><i class="fa fa-star"></i><?php echo round($artist->rating,1).'/5';?></span>
        <?php endif; ?>
      </div>
      <div class="sesvideo_browse_artist_btns">
        <?php if(isset($this->artistlink) && in_array('favourite', $this->artistlink) && Engine_Api::_()->user()->getViewer()->getIdentity() != 0 ): ?>
        <?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesvideo')->isFavourite(array('resource_type'=> 'sesvideo_artist','resource_id'=> $artist->artist_id)); ?>
        <a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn floatL sesvideo_favourite_<?php echo 'sesvideo_artist'; ?> <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $artist->artist_id; ?>"><i class="fa fa-heart"></i><span><?php echo $artist->favourite_count; ?></span></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if (!empty($this->paginator) && $this->paginator->count() > 1): ?>
    <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
      <div class="clear" id="loadmore_list"></div>
      <div class="sesbasic_view_more" id="load_more" onclick="loadMore();" style="display: block;">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link', 'class' => 'buttonlink icon_viewmore')); ?>
      </div>
      <div class="sesbasic_view_more_loading" id="underloading_image" style="display: none;">
        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="Loading" />
        <?php echo $this->translate("Loading ...") ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  
<?php if (empty($this->viewmore)): ?>
</div>
<?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are currently no artists.') ?>
    </span>
  </div>
<?php endif; ?>
<?php if($this->paginationType == 1): ?>
  <script type="text/javascript">    
     //Take refrences from: http://mootools-users.660466.n2.nabble.com/Fixing-an-element-on-page-scroll-td1100601.html
    //Take refrences from: http://davidwalsh.name/mootools-scrollspy-load
    en4.core.runonce.add(function() {
    
      var paginatorCount = '<?php echo $this->paginator->count(); ?>';
      var paginatorCurrentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';
      function ScrollLoader() { 
        var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        if($('loadmore_list')) {
          if (scrollTop > 40)
            loadMore();
        }
      }
      window.addEvent('scroll', function() { 
        ScrollLoader(); 
      });
    });    
  </script>
<?php endif; ?>
