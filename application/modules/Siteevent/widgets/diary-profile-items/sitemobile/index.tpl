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

<div class="sr_diary_view">
  <h3>
    <?php echo $this->subject()->title; ?> 
  </h3>
  <p class="sr_diary_view_des mbot10">
    <?php echo $this->subject()->body; ?>
  </p>

  <div class="sm-ui-cont-head">
    <?php if ($this->postedby): ?>
      <div class="sm-ui-cont-author-photo">
        <?php echo $this->htmlLink($this->subject()->getOwner(), $this->itemPhoto($this->subject()->getOwner(), 'thumb.icon')) ?>
      </div>
    <?php endif; ?>
    <div class="sm-ui-cont-cont-info">
      <?php if ($this->postedby): ?>
        <div class="sm-ui-cont-author-name">
          <?php echo $this->htmlLink($this->subject()->getOwner(), $this->subject()->getOwner()->getTitle()) ?>
        </div>
      <?php endif; ?>
      <div class="sm-ui-cont-cont-date">
        <?php echo $this->timestamp($this->subject()->creation_date) ?> 
      </div>
      <?php if (!empty($this->statisticsDiary)): ?>
        <div class="sm-ui-cont-cont-date">
          <?php
          $statistics = array();
          if (in_array('entryCount', $this->statisticsDiary)) {
            $statistics[] = $this->translate(array('<b>%s</b> Event', '<b>%s</b> Events', $this->total_item), $this->locale()->toNumber($this->total_item));
          }

          if (in_array('viewCount', $this->statisticsDiary)) {
            $statistics[] = $this->translate(array('<b>%s</b> View', '<b>%s</b> Views', $this->diary->view_count), $this->locale()->toNumber($this->diary->view_count));
          }
          ?>
          <?php echo join($statistics, ' - '); ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
  <?php if ($this->viewer_id && !empty($this->messageOwner)): ?>
    <div class="seaocore_profile_cover_buttons">
      <table cellpadding="2" cellspacing="0">
        <tbody> 
          <tr>
            <?php if (!empty($this->messageOwner)): ?>
              <td>
                <?php echo $this->htmlLink(array('route' => 'siteevent_diary_general', 'action' => 'message-owner', 'diary_id' => $this->diary->getIdentity()), $this->translate('Message Owner'), array('class' => 'smoothbox icon_siteevents_messageowner', 'data-role' => 'button', 'data-inset' => 'false', 'data-mini' => 'true', 'data-corners' => 'false', 'data-shadow' => 'true', 'data-icon' => 'envelope')) ?>
              </td>
            <?php endif; ?>
          </tr></tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<?php if ($this->total_item > 0): ?>
  <div class="sm-content-list">
    <ul class="sr_reviews_listing" data-role="listview" data-icon="arrow-r">
      <?php foreach ($this->paginator as $event): ?>
        <li>
          <a href="<?php echo $event->getHref(); ?>">
            <?php echo $this->itemPhoto($event, 'thumb.icon'); ?>
            <h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($event->getTitle(), $this->title_truncation); ?></h3>
            <?php if (!empty($this->statistics)) : ?>
              <?php echo $this->eventInfoSM($event, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation)); ?>
            <?php endif; ?>
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) && !empty($this->statistics) && in_array('ratingStar', $this->statistics)): ?>
              <?php if ($ratingValue == 'rating_both'): ?>
                <p><?php echo $this->showRatingStarSiteeventSM($event->rating_editor, 'editor', $ratingShow); ?></p>
                <p><?php echo $this->showRatingStarSiteeventSM($event->rating_users, 'user', $ratingShow); ?> </p>
              <?php else: ?>
                <p><?php echo $this->showRatingStarSiteeventSM($event->$ratingValue, $ratingType, $ratingShow); ?> </p>
              <?php endif; ?>
            <?php endif; ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php if ($this->paginator->count() > 1): ?>
    <br />
    <?php
    echo $this->paginationControl(
            $this->paginator, null, null);
    ?>
  <?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are currently no entries in this diary.'); ?>
    </span> 
  </div>
<?php endif; ?>