<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: homesponsored.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
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
<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>
<?php if ($this->direction == 1) { ?>
    <?php $j = 0; ?>
    <?php foreach ($this->siteevents as $siteevent): ?>
        <?php
        echo $this->partial(
                'list_carousel.tpl', 'siteevent', array(
            'siteevent' => $siteevent,
            'title_truncation' => $this->title_truncation,
            'ratingShow' => $ratingShow,
            'ratingType' => $ratingType,
            'ratingValue' => $ratingValue,
            'vertical' => $this->vertical,
            'featuredIcon' => $this->featuredIcon,
            'sponsoredIcon' => $this->sponsoredIcon,
            'showOptions' => $this->showOptions,
            'blockHeight' => $this->blockHeight,
            'blockWidth' => $this->blockWidth,
            'newIcon' => $this->newIcon
        ));
        ?>	 
    <?php endforeach; ?>
    <?php if ($j < ($this->sponserdSiteeventsCount)): ?>
        <?php for ($j; $j < ($this->sponserdSiteeventsCount); $j++): ?>
            <li class="siteevent_grid_view siteevent_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
            </li>
        <?php endfor; ?>
    <?php endif; ?>
<?php } else { ?>

    <?php for ($i = $this->sponserdSiteeventsCount; $i < Count($this->siteevents); $i++): ?>
        <?php $siteevent = $this->siteevents[$i]; ?>
        <?php
        echo $this->partial(
                'list_carousel.tpl', 'siteevent', array(
            'siteevent' => $siteevent,
            'title_truncation' => $this->title_truncation,
            'ratingShow' => $ratingShow,
            'ratingType' => $ratingType,
            'ratingValue' => $ratingValue,
            'vertical' => $this->vertical,
            'featuredIcon' => $this->featuredIcon,
            'sponsoredIcon' => $this->sponsoredIcon,
            'showOptions' => $this->showOptions,
            'blockHeight' => $this->blockHeight,
            'blockWidth' => $this->blockWidth,
            'newIcon' => $this->newIcon
        ));
        ?>	
    <?php endfor; ?>

    <?php for ($i = 0; $i < $this->sponserdSiteeventsCount; $i++): ?>
        <?php $siteevent = $this->siteevents[$i]; ?>
        <?php
        echo $this->partial(
                'list_carousel.tpl', 'siteevent', array(
            'siteevent' => $siteevent,
            'title_truncation' => $this->title_truncation,
            'ratingShow' => $ratingShow,
            'ratingType' => $ratingType,
            'ratingValue' => $ratingValue,
            'vertical' => $this->vertical,
            'featuredIcon' => $this->featuredIcon,
            'sponsoredIcon' => $this->sponsoredIcon,
            'showOptions' => $this->showOptions,
            'blockHeight' => $this->blockHeight,
            'blockWidth' => $this->blockWidth,
            'newIcon' => $this->newIcon
        ));
        ?>	
    <?php endfor; ?>
<?php } ?>

