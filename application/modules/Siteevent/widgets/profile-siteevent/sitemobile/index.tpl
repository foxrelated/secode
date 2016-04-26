<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (empty($this->isajax)) : ?>
<div>
<script type="text/javascript">
    var viewType = '<?php echo $this->viewType; ?>';
    var showEventType = '<?php echo $this->showEventType; ?>';
    function filter_rsvp(rsvp) { 
       $.mobile.loading().loader("show");
        var requestParams = $.extend({
            format: 'html',
           'subject': sm4.core.subject.guid != '' ? sm4.core.subject.guid : $.mobile.activePage.attr("data-subject"),
            isajax: true,
            pagination: 0,
            rsvp: rsvp,
            page: 1,
            is_filtering: true,
            viewType: viewType,
            showEventType: showEventType,
            identity: <?php echo $this->identity; ?>
        }, <?php echo json_encode($this->allParams); ?>);

         $.ajax({
            url: sm4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
            data: requestParams,
            success: function(responseHTML) {
                 $.mobile.loading().loader("hide");
                $.mobile.activePage.find('#event_profile_layout').html(responseHTML);
                sm4.core.runonce.trigger();
                sm4.core.refreshPage();
            }
            });
        }
</script>


    <?php if ($this->paginator->getCurrentPageNumber() < 2) : ?>
      <?php if ($this->showEventType == 'all') : ?>
        <div data-role="tabs" class="tabs">
          <div data-role="navbar">
            <ul>
              <li>
                <a onclick="rsvp = -1;
                  viewType = 'upcoming';
                  filter_rsvp(-1);" class="<?php if ($this->viewType == 'upcoming') echo 'ui-btn-active'; ?> ui-btn"><?php echo $this->translate('Upcoming'); ?></a>
              </li>
              <li>
                <a onclick="rsvp = -1;
                  viewType = 'past';
                filter_rsvp(-1);" class="<?php if ($this->viewType == 'past') echo 'ui-btn-active"'; ?> ui-btn"><?php echo $this->translate('Past'); ?></a>
              </li>
            </ul>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
                

    <?php if ($this->paginator->getCurrentPageNumber() < 2 && $this->showEventFilter && !empty($this->EventFilterTypes) && count($this->EventFilterTypes) > 1) : ?>
      <select name="select_rsvps"  onchange="filter_rsvp(this.value)">
        <option value="-1" id='select_all' <?php if ($this->rsvp == -1) echo "selected"; ?>><?php echo $this->translate('All') ?></option>
        <?php if (!empty($this->EventFilterTypes) && in_array('ledOwner', $this->EventFilterTypes)): ?>
        <option value="-4" id='select_leading'<?php if ($this->rsvp == -4) echo "selected"; ?>><?php echo $this->translate('Leading') ?></option>     
        <?php endif; ?> 
        <?php if (!empty($this->EventFilterTypes) && in_array('host', $this->EventFilterTypes)): ?>
        <option value="-2" id='select_hosting'<?php if ($this->rsvp == -2) echo "selected"; ?>><?php echo $this->translate('Hosting') ?></option>
        <?php endif; ?>
        <?php if (!empty($this->EventFilterTypes) && in_array('joined', $this->EventFilterTypes)): ?>   
        <option value="2" id='select_attending'<?php if ($this->rsvp == 2) echo "selected"; ?>><?php echo $this->translate('Attending') ?></option>
        <?php endif; ?>
        <option value="1" id='select_maybeattending' <?php if ($this->rsvp == 1) echo "selected"; ?>><?php echo $this->translate('Maybe Attending') ?></option>
        <option value="0" id='select_notattending'<?php if ($this->rsvp == 0) echo "selected"; ?>><?php echo $this->translate('Not Attending') ?></option>
        <?php if (!empty($this->EventFilterTypes) && in_array('liked', $this->EventFilterTypes)): ?> 
        <option value="-3" id='select_liked'<?php if ($this->rsvp == -3) echo "selected"; ?>><?php echo $this->translate('Liked') ?></option>
        <?php endif; ?> 
        <?php if (!empty($this->EventFilterTypes) && in_array('userreviews', $this->EventFilterTypes)): ?> 
        <option value="-5" id='select_rated'<?php if ($this->rsvp == -5) echo "selected"; ?>><?php echo $this->translate('Rated') ?></option>
        <?php endif; ?>
      </select>
    <?php endif; ?>
</div>
<?php endif; ?> 

<?php if (empty($this->isajax)) : ?> 
  <div id="event_profile_layout" class="ui-page-content">
  <?php endif; ?>
    <a id="profile_siteevent_anchor"></a>
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

  <?php if ($this->paginator->count()  > 0): ?>
    <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
      <div id="grid_view" class="listing">
        <ul>
          <?php foreach ($this->paginator as $siteevent): ?>
            <li style="height:<?php echo $this->columnHeight ?>px;">
              <a class="list-photo" href="<?php echo $siteevent->getHref(); ?>">
                <?php
                $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_listing_thumb_normal.png';
                $temp_url = $siteevent->getPhotoUrl('thumb.profile');
                if (!empty($temp_url)): $url = $siteevent->getPhotoUrl('thumb.profile');
                endif;
                ?>
                <span style="background-image: url(<?php echo $url; ?>);"> </span>
                <h3 class="list-title">
                  <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationGrid) ?>
                </h3>
              </a>
              <div class="list-info">
                <?php if (!empty($this->statistics)) : ?>
                  <?php echo $this->eventInfoSMApp($siteevent, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation)); ?>
                <?php endif; ?>
                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) && !empty($this->statistics) && in_array('ratingStar', $this->statistics)): ?>
                  <?php if ($ratingValue == 'rating_both'): ?>
                    <span class="list-stats f_small"><?php echo $this->showRatingStarSiteeventSM($siteevent->rating_editor, 'editor', $ratingShow); ?></span>
                    <span class="list-stats f_small"><?php echo $this->showRatingStarSiteeventSM($siteevent->rating_users, 'user', $ratingShow); ?> </span>
                  <?php else: ?>
                    <span class="list-stats f_small"><?php echo $this->showRatingStarSiteeventSM($siteevent->$ratingValue, $ratingType, $ratingShow); ?> </span>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php else :?>
      <div id="list_view" class="sm-content-list">
        <ul data-role="listview" data-inset="false">
          <?php foreach ($this->paginator as $siteevent): ?>
            <li data-icon="arrow-r">
              <a href="<?php echo $siteevent->getHref(); ?>">
                <?php echo $this->itemPhoto($siteevent, 'thumb.icon'); ?>
                <h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncation); ?></h3>
                <?php if (!empty($this->statistics)) : ?>
                  <?php echo $this->eventInfoSM($siteevent, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation)); ?>
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
      </div>
    <?php endif;?>
    
  <?php if ($this->paginator->count() > 1): ?>
    <?php
      $array = array_merge($this->allParams, array('isajax' => true, 'pagination'=>true, 'rsvp' => $this->rsvp, 'viewType' => $this->viewType) );
      echo $this->paginationAjaxControl(
      $this->paginator, $this->identity, 'profile_siteevent_anchor', $array);
    ?>
  <?php endif; ?>
<?php else: ?>
  <div class="tip"> 
    <span>
      <?php echo $this->translate('You do not have any event that match your search criteria.'); ?>
    </span>
  </div>
<?php endif; ?>
<?php if (empty($this->isajax)) : ?> 
  </div>
<?php endif; ?>

