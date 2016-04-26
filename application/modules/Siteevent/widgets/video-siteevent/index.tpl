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

<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_siteevent_video_siteevent')
        }
        en4.siteevent.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>

<?php if ($this->showContent): ?>

    <?php if ($this->allowed_upload_video && $this->viewer_id): ?>
        <div class="seaocore_add clear">
            <?php if ($this->type_video): ?>
                <a href='<?php echo $this->url(array('action' => 'index', 'event_id' => $this->siteevent->event_id, 'content_id' => $this->identity), "siteevent_video_upload", true) ?>'  class='buttonlink icon_siteevents_video_new'><?php echo $this->translate('Add Video'); ?></a>
            <?php else: ?>
                <?php echo $this->htmlLink(array('route' => "siteevent_video_create", 'event_id' => $this->siteevent->event_id, 'content_id' => $this->identity), $this->translate('Add Video'), array('class' => 'buttonlink icon_siteevents_video_new')) ?>
            <?php endif; ?>

            <?php if ($this->can_edit && count($this->paginator) > 0): ?>
                <a href='<?php echo $this->url(array('event_id' => $this->siteevent->event_id), "siteevent_videospecific", true) ?>'  class='buttonlink seaocore_icon_edit'><?php echo $this->translate('Edit Videos'); ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if (count($this->paginator) > 0): ?>
        <ul class="siteevent_profile_videos">


            <?php foreach ($this->paginator as $item): ?>
                <li>

                    <?php $videoEmbedded = null; ?>
                    <div class="siteevent_video_thumb_wrapper">
                        <?php if ($item->duration): ?>
                            <span class="siteevent_video_length">
                                <?php
                                if ($item->duration >= 3600) {
                                    $duration = gmdate("H:i:s", $item->duration);
                                } else {
                                    $duration = gmdate("i:s", $item->duration);
                                }
                                echo $duration;
                                ?>
                            </span>
                        <?php endif ?>
                        <?php
                        if ($item->photo_id) {
                            echo $this->htmlLink($item->getHref(array('content_id' => $this->identity)), $this->itemPhoto($item, 'thumb.normal'));
                        } else {
                            echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
                        }
                        ?>
                    </div>

                    <div class="siteevent_profile_video_info o_hidden clr">
                        <div class="siteevent_profile_video_title">
                            <?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), $this->title_truncation), array('class' => 'video_title')) ?>
                        </div>

                        <div class="siteevent_profile_video_options clr">
                            <?php if (($this->can_edit || ($this->viewer_id) == ($item->owner_id))): ?>
                                <?php if (!$this->type_video): ?>
                                    <a href='<?php echo $this->url(array('event_id' => $this->siteevent->event_id, 'video_id' => $item->video_id, 'tab' => $this->identity), "siteevent_video_edit", true) ?>' title="<?php echo $this->translate('Edit Video'); ?>"><i class="siteevent_icon seaocore_icon_edit"></i></a>
                                <?php elseif ($this->can_edit): ?>
                                    <?php echo $this->htmlLink(Array('action' => 'edit', 'route' => "siteevent_videospecific", 'event_id' => $this->siteevent->getIdentity(), 'video_id' => $item->video_id), "<i class='siteevent_icon seaocore_icon_edit'></i>", array('title' => $this->translate("Edit Video"))); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (($this->can_edit || ($this->viewer_id) == ($item->owner_id))): ?>
                                <?php if ($this->type_video): ?>
                                    <?php echo $this->htmlLink(Array('action' => 'delete', 'route' => "siteevent_videospecific", 'event_id' => $this->siteevent->getIdentity(), 'video_id' => $item->video_id), "<i class='siteevent_icon seaocore_icon_delete'></i>", array('class' => 'smoothbox', 'title' => $this->translate("Delete Video"))); ?>
                                <?php else: ?>  
                                    <?php echo $this->htmlLink(Array('route' => "siteevent_video_delete", 'event_id' => $this->siteevent->getIdentity(), 'video_id' => $item->video_id, 'format' => 'smoothbox'), "<i class='siteevent_icon seaocore_icon_delete'></i>", array('class' => 'smoothbox', 'title' => $this->translate("Delete Video"))); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>	
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <?php if ($this->allowed_upload_video && $this->viewer_id): ?>
            <div class="tip">
                <span>    
                    <?php if ($this->type_video): ?>
                        <?php $url = $this->url(array('action' => 'index', 'event_id' => $this->siteevent->event_id, 'content_id' => $this->identity), "siteevent_video_upload", true); ?>
                        <?php echo $this->translate('You have not added any video in your event. %1$sClick here%2$s to add your first video.', "<a href='$url'>", "</a>"); ?>
                    <?php else: ?>
                        <?php $url = $this->url(array('event_id' => $this->siteevent->event_id, 'content_id' => $this->identity), "siteevent_video_create", true); ?>
                        <?php echo $this->translate('There are currently no videos in this event. %1$sClick here%2$s to add your first video.', "<a href='$url'>", "</a>"); ?>
                    <?php endif; ?>
                </span>
            </div>
            <br />
        <?php else: ?>    
            <div class="tip">
                <?php echo $this->translate("There are currently no videos in this event."); ?>
            </div>    
        <?php endif; ?>
    <?php endif; ?>

    <div>
        <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
            <div id="user_group_members_previous" class="paginator_previous">
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'paginateSiteeventVideo(siteeventVideoPage - 1)', 'class' => 'buttonlink icon_previous')); ?>
            </div>
        <?php endif; ?>
        <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
            <div id="user_group_members_next" class="paginator_next">
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'paginateSiteeventVideo(siteeventVideoPage + 1)', 'class' => 'buttonlink_right icon_next')); ?>
            </div>
        <?php endif; ?>
    </div>

    <a id="siteevent_video_anchor" style="position:absolute;"></a>

    <script type="text/javascript">
        var siteeventVideoPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
        var paginateSiteeventVideo = function(page) {
            var params = {
                requestParams:<?php echo json_encode($this->params) ?>,
                responseContainer: $$('.layout_siteevent_video_siteevent')
            }
            params.requestParams.content_id = <?php echo sprintf('%d', $this->identity) ?>;
            params.requestParams.page = page;
            en4.siteevent.ajaxTab.sendReq(params);
        }

        en4.core.runonce.add(function() {
            if (en4.sitevideoview) {
                en4.sitevideoview.attachClickEvent(Array('video_title', 'item_photo_siteevent_video', 'item_photo_video'));
            }
        });
    </script>
<?php endif; ?>