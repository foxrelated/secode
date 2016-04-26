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
<?php echo $this->doctype()->__toString() ?>
<?php
$locale = $this->locale()->getLocale()->__toString();
$orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' );
?>
<?php if (empty($this->preview)): ?>
    <html id="smoothbox_window" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
        <head>
            <base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/') . '/' ?>" />
            <title></title>
            <?php
            $this->headMeta()
	      ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
	      ->appendHttpEquiv('Content-Language', $this->locale()->getLocale()->__toString());
            ?>
	<?php echo $this->headMeta()->toString()."\n" ?>
        </head>
        <body>
        <?php endif; ?>
        <?php if ($this->siteevent): ?>
            <div class="se_badge  <?php if (in_array('gridview', $this->options)): ?>se_badge_gridview<?php else: ?> se_badge_listview<?php endif; ?>">
                <div class="se_b_thumb">
                    <a href="<?php echo $this->siteevent->getHref() ?>" class ="" target="_blank">
                        <?php
                        $url = $this->siteevent->getPhotoUrl('thumb.profile');
                        if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_event_thumb_normal.png';
                        endif;
                        ?>
                        <span style="background-image: url(<?php echo $url; ?>);"></span>
                    </a>

                </div>
                <div class="se_b_info">
                    <?php if (in_array('title', $this->options)): ?>
                        <div class="se_b_title">
                            <?php echo $this->htmlLink($this->siteevent->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->siteevent->getTitle(), 150), array('title' => $this->siteevent->getTitle(), 'target' => "_blank")) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (in_array('venue', $this->options)): ?>
                        <?php if ($this->siteevent->venue_name): ?>
                            <div class="se_b_vanue se_b_stats">
                                <i class="se_b_strips se_b_icon se_b_icon_venue" title="<?php echo $this->translate("Venue") ?>"></i>
                                <div>
                                    <?php echo $this->siteevent->venue_name ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (in_array('location', $this->options)): ?>
                        <?php if ($this->siteevent->location): ?>
                            <div class="se_b_location se_b_stats">
                                <i class="se_b_strips se_b_icon se_b_icon_location" title="<?php echo $this->translate("Location") ?>"></i>
                                <div>
                                    <?php echo $this->siteevent->location ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (in_array('starttime', $this->options)): ?>
                        <?php $date = $this->siteevent->getStartEndDate($this->occurrence_id); ?>
                        <div class="se_b_starttime se_b_stats">
                            <i class="se_b_strips se_b_icon se_b_icon_time" title="<?php echo in_array('endtime', $this->options) ? $this->translate("Start & End Date") : $this->translate("Start Date") ?>"></i>
                            <div>
                                <?php echo $date['starttime'] ?>
                                <?php if (in_array('endtime', $this->options)): ?>
                                    <span class="se_b_endtime">
                                        <?php if ($date['starttime'] == $date['endtime']): ?>
                                            -<?php echo $date['endtime'] ?>
                                        <?php else: ?>
                                            <br/><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/arrow-right.png" alt="" title="<?php $this->translate("End Date") ?>"/>
                                            <?php echo $date['endtime'] ?>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (in_array('host', $this->options)): ?>
                        <?php if ($this->siteevent->host_type): ?>
                            <div class="se_b_host_title se_b_stats">
                                <i class="se_b_strips se_b_icon se_b_icon_host" title="<?php echo $this->translate("Host") ?>"></i>
                                <div>
                                    <?php $host = $this->siteevent->getHost() ?>
                                    <?php echo $this->htmlLink($host->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($host->getTitle(), 150), array('title' => $host->getTitle(), 'target' => "_blank")) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (in_array('attending', $this->options)): ?>
                        <div class="se_b_attending se_b_stats">
                            <i class="se_b_strips se_b_icon se_b_icon_users" title="<?php echo $this->translate("Guests") ?>"></i>
                            <div>
                                <?php echo $this->translate(array('%s attending', '%s attending', $this->siteevent->getAttendingCount()), $this->locale()->toNumber($this->siteevent->getAttendingCount())) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (in_array('desc', $this->options)): ?>
                        <div class="se_b_desc">
                            <?php echo $this->siteevent->getDescription() ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (empty($this->preview)): ?>
        </body>
    </html>
<?php endif; ?>

<style type="text/css">
    .se_badge {
        font-family: tahoma,arial,verdana,sans-serif;
        color: <?php echo $this->text_color ?>;
        border-color: <?php echo $this->border_color ?>;
        background-color: <?php echo $this->background_color ?>;
        overflow:hidden;
        margin:0px;
        padding:0px;
        font-size: 10pt;
        border-width: 1px;
        border-style: solid;
        border-radius: 5px;
        display: inline-block;
        position: relative;
    }
    .se_badge a:link, .se_badge a:visited {
        color:<?php echo $this->link_color ?>;
        text-decoration: none;
    }
    .se_b_title{
        font-weight: bold;
        margin-bottom: 5px;
    }
    .se_b_stats{
        clear: both;
        margin-bottom: 3px;
    }
    .se_b_info{
        padding: 5px 7px;
        overflow: hidden;
    }
    .se_b_desc{
        margin-top: 5px;
        font-size: 11px;
    }
    .se_b_stats i{
        float: left;
        margin-right: 3px;
    }
    .se_b_stats i + div{
        font-size: 11px;
        overflow: hidden;
    }
    .se_b_strips{
        background-image:url(./application/modules/Siteevent/externals/images/img-strip.png);
    }
    .se_b_icon{
        width: 16px;
        height: 16px;
        display: inline-block;
    }
    .se_b_icon_users {
        background-position: -146px -30px;
    }
    .se_b_icon_location{
        background-position:-165px -7px;
    }
    .se_b_icon_host{
        background-position:-208px -30px;
    }
    .se_b_icon_venue{
        background-position:-187px -31px;
    }
    .se_b_icon_user{
        background-position:-224px -30px;
    }
    .se_b_icon_time {
        background-position: -145px -8px;
    }
    .se_badge_gridview{
        width: <?php if (!empty($this->preview)): ?>200px;<?php else: ?>100%<?php endif; ?>;
    }
    /*Grid View*/
    .se_badge_gridview .se_b_thumb {
        border-bottom: 1px solid <?php echo $this->border_color ?>;
        border-radius: 5px 5px 0 0;
        float: left;
        position: relative;
        height: 160px;
        width: 100%;
    }
    /*List View*/
    .se_badge_listview{
        min-height: 160px;
        width: <?php if (!empty($this->preview)): ?>400px<?php else: ?>100%<?php endif; ?>;
    }
    .se_badge_listview .se_b_thumb {
        border-right: 1px solid <?php echo $this->border_color ?>;
        border-radius: 5px 0 0 5px;
        bottom: 0;
        left: 0;
        position: absolute;
        top: 0;
        width: 200px;
    }
    .se_badge_listview .se_b_info{
        margin-left:200px;
    }
    /*Event Thumb*/
    .se_b_thumb > a > span{
        background-position: center 50%;
        background-color: #444;
        background-repeat: no-repeat;
        background-size: cover;
        display: block;
        height: 100%;
        width: 100%;
    }
</style>
