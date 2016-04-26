<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list_carousel.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$siteevent = $this->siteevent;
$ratingShow = $this->ratingShow;
$ratingType = $this->ratingType;
$ratingValue = $this->ratingValue;

if (!isset($this->showEventType)) {
    $this->showEventType = '';
}
?>

<?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>

<li class="siteevent_grid_view siteevent_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
    <div class="siteevent_grid_thumb">
        <?php if (!empty($this->showOptions) && in_array('newLabel', $this->showOptions) && $siteevent->newlabel): ?>
            <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
        <?php endif; ?>
        <a href="<?php echo $siteevent->getHref(array('showEventType' => $this->showEventType)) ?>" class ="siteevent_thumb" title="<?php echo $siteevent->getTitle() ?>">
            <?php
            $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_event_thumb_main.png';
            $temp_url = $siteevent->getPhotoUrl('thumb.main');
            if (!empty($temp_url)): $url = $siteevent->getPhotoUrl('thumb.main');
            endif;
            ?>
            <span style="background-image: url(<?php echo $url; ?>); "></span>
        </a>
        <?php if (!empty($this->showOptions) && in_array('featuredLabel', $this->showOptions) && $siteevent->featured): ?>
            <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
        <?php endif; ?>
        <div class="siteevent_grid_title">
            <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncation), array('title' => $siteevent->getTitle())) ?>
        </div>
    </div>
    <?php if (!empty($this->showOptions) && in_array('sponsoredLabel', $this->showOptions) && !empty($siteevent->sponsored)): ?>
        <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsored.color', '#fc0505'); ?>;'>
            <?php echo $this->translate('SPONSORED'); ?>     				
        </div>
    <?php endif; ?>
    <div class="siteevent_grid_info">
        <?php if (!empty($this->showOptions)) : ?>
            <?php echo $this->eventInfo($siteevent, $this->showOptions, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->showEventType)); ?>
        <?php endif; ?>
    </div>
</li>