<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>
<?php if ($this->is_ajax_load): ?>
    <?php
    if ($this->viewType == 'gridview'):
        $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
    endif;
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

    <?php if ($this->viewType == 'listview'): ?>
        <ul class="seaocore_sidebar_list">
            <?php foreach ($this->events as $siteevent): ?>
                <li> 
                    <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), $this->itemPhoto($siteevent, 'thumb.icon')) ?>
                    <div class='seaocore_sidebar_list_info'>
                        <div class='seaocore_sidebar_list_title'>
                            <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->truncation), array('title' => $siteevent->getTitle())) ?>
                        </div>

                        <?php if (!empty($this->statistics)) : ?>
                            <?php echo $this->eventInfo($siteevent, $this->statistics, array('most_discuss_widget' => true, 'ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->showEventType)); ?>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <?php $isLarge = ($this->columnWidth > 170); ?>
        <ul class="siteevent_grid_view_sidebar o_hidden"> 
            <?php foreach ($this->events as $siteevent): ?>
                <li class="siteevent_grid_view" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
                    <div class="siteevent_grid_thumb">
                        <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                            <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                        <?php endif; ?>
                        <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $siteevent->featured): ?>
                            <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                        <?php endif; ?>
                        <a href="<?php echo $siteevent->getHref(array('showEventType' => $this->showEventType)) ?>" class ="siteevent_thumb">
                            <?php
                            $url = $siteevent->getPhotoUrl($isLarge ? 'thumb.profile' : 'thumb.profile');
                            if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_event_thumb_normal.png';
                            endif;
                            ?>
                            <span style="background-image: url(<?php echo $url; ?>); <?php if ($isLarge): ?> height:160px; <?php endif; ?> "></span>
                        </a>
                        <?php if (!empty($this->titlePosition)) : ?>
                            <div class="siteevent_grid_title">
                                <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->truncation), array('title' => $siteevent->getTitle())) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($siteevent->sponsored)): ?>
                        <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsored.color', '#fc0505'); ?>;'>
                            <?php echo $this->translate('SPONSORED'); ?>     				
                        </div>
                    <?php endif; ?>
                    <div class="siteevent_grid_info">
                        <?php if (empty($this->titlePosition)) : ?>
                            <div class="bold">
                                <?php echo $this->htmlLink($siteevent->getHref(array('showEventType' => $this->showEventType)), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->truncation), array('title' => $siteevent->getTitle())) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($this->statistics)) : ?>
                            <?php echo $this->eventInfo($siteevent, $this->statistics, array('view_type' => 'grid_view', 'titlePosition' => $this->titlePosition, 'most_discuss_widget' => true, 'ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation, 'showEventType' => $this->showEventType)); ?>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php else: ?>

    <div id="layout_siteevent_most_discussed_events_<?php echo $this->identity; ?>">
        <!--    <div class="seaocore_content_loader"></div>-->
    </div>

    <script type="text/javascript">
        var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
        var params = {
            'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer': 'layout_siteevent_most_discussed_events_<?php echo $this->identity; ?>',
            requestParams: requestParams
        };

        en4.seaocore.locationBased.startReq(params);
    </script>  

<?php endif; ?>