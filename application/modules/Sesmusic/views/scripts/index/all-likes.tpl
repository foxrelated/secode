<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: all-likes.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<script type="text/javascript">

  function loadMore() {
    
    if ($('load_more'))
      $('load_more').style.display = "<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>";
      
    document.getElementById('load_more').style.display = 'none';
    document.getElementById('underloading_image').style.display = '';
  
    var id = '<?php echo $this->id; ?>';
    var type = '<?php echo $this->type; ?>';
    var showUsers = '<?php echo $this->showUsers; ?>';
    
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'sesmusic/index/all-likes/id/' + id + '/type/' + type + '/showUsers/' + showUsers,
      'data': {
        format: 'html',
        page: "<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>",
        viewmore: 1        
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('results_data').innerHTML = document.getElementById('results_data').innerHTML + responseHTML;
        document.getElementById('load_more').destroy();
        document.getElementById('underloading_image').style.display = 'none';
      }
    }));
    return false;
  }
</script>

<?php if (empty($this->viewmore)): ?>
  <div class="sesbasic_items_listing_popup">
    <div class="sesbasic_items_listing_header">
      <?php if($this->showUsers == 'all'): ?>
        <?php if($this->type == 'sesmusic_album'): ?>
          <?php echo $this->translate('Members Who Like This Music Album') ?>
        <?php else: ?>
          <?php echo $this->translate('Members Who Liked This Song') ?>
        <?php endif; ?>
        
      <?php else: ?>
        <?php echo $this->translate('Friends Likes') ?>
      <?php endif; ?>
      <a class="fa fa-close" href="javascript:;" onclick='smoothboxclose();' title="<?php echo $this->translate('Close') ?>"></a>
    </div>
    <div class="sesbasic_items_listing_cont" id="results_data">
    <?php endif; ?>

    <?php if (count($this->paginator) > 0) : ?>
      <?php foreach ($this->paginator as $value): ?>
        <?php $user = Engine_Api::_()->getItem('user', $value->poster_id); ?>
        <div class="item_list">
          <div class="item_list_thumb">
            <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('title' => $user->getTitle(), 'target' => '_parent')); ?>
          </div>
          <div class="item_list_info">
            <div class="item_list_title">
              <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title' => $user->getTitle(), 'target' => '_parent')); ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('There are no members yet.'); ?>
        </span>
      </div>
    <?php endif; ?>      
    <?php if (!empty($this->paginator) && $this->paginator->count() > 1): ?>
      <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
        <div id="load_more" class="sesbasic_view_more"  onclick="loadMore();" >
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link', 'class' => 'buttonlink icon_viewmore')); ?>
        </div>
        <div id="underloading_image" class="sesbasic_view_more_loading" style="display: none;">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
          <?php echo $this->translate("Loading ...") ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
<?php endif; ?>
<script type="text/javascript">
  function smoothboxclose() {
    parent.Smoothbox.close();
  }
</script>