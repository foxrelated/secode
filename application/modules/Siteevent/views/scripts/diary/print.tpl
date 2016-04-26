<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent_print.css'); ?>
<link href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent_print.css' ?>" type="text/css" rel="stylesheet" media="print">

<?php
    $ratingValue = 'rating_editor';
    $ratingShow = 'small-star';
    if ($this->ratingType == 'rating_editor') {
        $ratingType = 'editor';
    } else {
        $ratingType = 'user';
    }
?>

<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>

<div class="siteevent_print_page">
    <div class="siteevent_print_page_header">
        <span>
            <?php $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'Advertisement');
            echo $this->translate($site_title) . ' - ' . $this->translate('Diary');
            ?>
        </span>
        <div id="printdiv">
            <a href="javascript:void(0);" style="background-image: url('<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteevent/externals/images/printer.png');" class="buttonlink" onclick="printData()" align="right"><?php echo $this->translate('Take Print') ?></a>
        </div>
    </div>

    <div class="siteevent_diary_view">
        <div class="siteevent_diary_view_title"> 
            <?php echo $this->diary->title; ?> 
        </div>    
        <div class="siteevent_diary_view_stats">
            <?php echo $this->translate('Created by %s %s', $this->diary->getOwner()->toString(), $this->timestamp($this->diary->creation_date)) ?>
        </div>
        <div class="siteevent_diary_view_stats"> 
            <?php if (!empty($this->statisticsDiary)): ?>
                <?php
                $statistics = '';

                if (in_array('entryCount', $this->statisticsDiary)) {
                    $statistics .= $this->translate(array('%s event', '%s events', $this->total_item), $this->locale()->toNumber($this->total_item)) . ', ';
                }

                if (in_array('viewCount', $this->statisticsDiary)) {
                    $statistics .= $this->translate(array('%s view', '%s views', $this->diary->view_count), $this->locale()->toNumber($this->diary->view_count)) . ', ';
                }

                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');
                ?>
                <?php echo $statistics; ?>
            <?php endif; ?>  
        </div>
        <div class=" siteevent_diary_view_des">
            <?php echo $this->diary->body; ?>
        </div>  
    </div>
    <ul class="seaocore_browse_list">
        <?php foreach ($this->paginator as $event): ?>
            <li>
                <div class='seaocore_browse_list_photo'>
                    <?php echo $this->htmlLink($event->getHref(array('showEventType' => 'all')), $this->itemPhoto($event, 'thumb.normal')) ?>
                </div>
                <div class='seaocore_browse_list_info'>
                    <div class='seaocore_browse_list_info_title'>

                        <?php if ($ratingValue == 'rating_both'): ?>
                            <?php echo $this->ShowRatingStarSiteevent($event->rating_editor, 'editor', $ratingShow); ?>
                            <br/>
                            <?php echo $this->ShowRatingStarSiteevent($event->rating_users, 'user', $ratingShow); ?>
                        <?php else: ?>
                            <?php echo $this->ShowRatingStarSiteevent($event->$ratingValue, $ratingType, $ratingShow); ?>
                        <?php endif; ?>

                        <?php echo $this->htmlLink($event->getHref(array('showEventType' => 'all')), $event->getTitle()); ?>
                    </div>

                    <?php if ($event->category_id): ?>
                        <div class='seaocore_sidebar_list_details'>
                            <a href="<?php echo $this->url(array('category_id' => $event->category_id, 'categoryname' => $event->getCategory()->getCategorySlug()), Engine_Api::_()->siteevent()->getCategoryHomeRoute()); ?>"> 
                                <?php echo $event->getCategory()->getTitle(true) ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($event->price) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)): ?>
                        <div class="seaocore_sidebar_list_details"><?php echo $this->locale()->toCurrency($event->price, $currency); ?></div>
                    <?php endif; ?>

                    <div class='seaocore_browse_list_info_blurb'>
                        <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($event->body, 150); ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
                    TB_close();
    </script>
<?php endif; ?>

<script type="text/javascript">
    function printData() {
        document.getElementById('printdiv').style.display = "none";
        window.print();
        setTimeout(function() {
            document.getElementById('printdiv').style.display = "block";
        }, 500);
    }
</script>