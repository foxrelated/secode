<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$ratingValue = $this->ratingType;
$ratingShow = 'small-star';
if ($this->ratingType == 'rating_editor') {
  $ratingType = 'editor';
} elseif ($this->ratingType == 'rating_avg') {
  $ratingType = 'overall';
} else {
  $ratingType = 'user';
}
?>
        <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
        <?php if ($this->viewType == 'listview'): ?>
  <div class="sm-content-list">
    <ul data-role="listview" data-inset="false" data-icon="arrow-r" id="list-view">
          <?php foreach ($this->paginator as $siteevent): ?>
        <li data-icon="arrow-r">
          <a href="<?php echo $siteevent->getHref(); ?>">
            <?php echo $this->itemPhoto($siteevent, 'thumb.icon'); ?>
            <h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncation); ?></h3>

            <?php if (!empty($this->statistics)) : ?>
              <?php echo $this->eventInfoSM($siteevent, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->showEventType)); ?>
            <?php endif; ?>
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) && !empty($this->statistics) && in_array('ratingStar', $this->statistics)): ?>
              <?php if ($ratingValue == 'rating_both'): ?>
                <p><?php echo $this->showRatingStarSiteeventSM($siteevent->rating_editor, 'editor', $ratingShow); ?></p>
                <p><?php echo $this->showRatingStarSiteeventSM($siteevent->rating_users, 'user', $ratingShow); ?> </p>
          <?php else: ?>
                <p><?php echo $this->showRatingStarSiteeventSM($siteevent->$ratingValue, $ratingType, $ratingShow); ?> </p>
        <?php endif; ?>
      <?php endif; ?>
          </a>
        </li>
    <?php endforeach; ?>
    </ul>
  <?php if ($this->paginator->count() > 1): ?>
    <?php
    echo $this->paginationAjaxControl(
            $this->paginator, $this->identity, 'list-view', array('count' => $this->count, 'truncation' => $this->title_truncation, 'viewType' => $this->viewType, 'ratingType' => $this->ratingType, 'statistics' => $this->statistics, 'columnHeight' => $this->columnHeight));
    ?>
        <?php endif; ?>
  </div>
<?php else: ?>
  <div class="ui-page-content">
    <div id="grid_view">
      <ul class="p_list_grid">
                <?php foreach ($this->paginator as $siteevent): ?>          
          <li style="height:<?php echo $this->columnHeight ?>px;">
            <a href="<?php echo $siteevent->getHref(); ?>" class="ui-link-inherit">
              <div class="p_list_grid_top_sec">
                <div class="p_list_grid_img">
    <?php
    $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_listing_thumb_normal.png';
    $temp_url = $siteevent->getPhotoUrl($isLarge ? 'thumb.midum' : 'thumb.normal');
    if (!empty($temp_url)): $url = $siteevent->getPhotoUrl('thumb.profile');
    endif;
    ?>
                  <span style="background-image: url(<?php echo $url; ?>);"> </span>
                </div>                 
                <div class="p_list_grid_title">
                  <span><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationGrid) ?></span>
                </div>
              </div>
              <div class="p_list_grid_info">
                <!--NEW LABEL-->
                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.fs.markers', 1)): ?>
                  <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                    <span class="p_list_grid_stats">                
                      <i class="sr_siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                    </span>            
                  <?php endif; ?>
                <?php endif; ?>
                  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) && !empty($this->statistics) && in_array('ratingStar', $this->statistics)): ?>
                  <?php if ($ratingValue == 'rating_both'): ?>
                      <span class="p_list_grid_stats"><?php echo $this->showRatingStarSiteeventSM($siteevent->rating_editor, 'editor', $ratingShow); ?></span>
                      <span class="p_list_grid_stats"><?php echo $this->showRatingStarSiteeventSM($siteevent->rating_users, 'user', $ratingShow); ?></span>
                    <?php else: ?>
                      <span class="p_list_grid_stats"><?php echo $this->showRatingStarSiteeventSM($siteevent->$ratingValue, $ratingType, $ratingShow); ?></span>
                    <?php endif; ?>
    <?php endif; ?> 
                <span class="p_list_grid_stats">                                  
          <?php if (!empty($this->statistics)) : ?>
            <?php echo $this->eventInfoSM($siteevent, $this->statistics, array('view_type' => 'grid_view', 'titlePosition' => $this->titlePosition, 'ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation)); ?>
        <?php endif; ?>
                </span>
              </div> 
            </a>
          </li>
      <?php endforeach; ?>
      </ul>
  <?php if ($this->paginator->count() > 1): ?>
    <?php
    echo $this->paginationAjaxControl(
            $this->paginator, $this->identity, 'grid_view', array('count' => $this->count, 'truncation' => $this->title_truncation, 'viewType' => $this->viewType, 'ratingType' => $this->ratingType, 'statistics' => $this->statistics, 'columnHeight' => $this->columnHeight));
    ?>
  <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<style type="text/css">

  .layout_siteevent_related_events_view_siteevent > h3 {
    display:none;
  }

</style>