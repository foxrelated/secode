<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="siteevent_dashboard_content">
    <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <div class="siteevent_event_form">
        <div class="global_form">
            <div>
                <div>
                    <h3> <?php echo $this->translate("Edit Event Videos"); ?></h3>
                    <p class="form-description"><?php echo $this->translate("Edit and manage the videos of your event below."); ?>
                        <?php if ($this->slideShowEnanle): ?>
                            <br />
                            <?php echo $this->translate("An attractive Slideshow will be displayed on your event Profile page. Below, you can select a video to be displayed in that slideshow by using the 'Show in Slideshow' option."); ?>
                            <br />
                            <b><?php echo $this->translate("Note: ") ?></b><?php echo $this->translate("You can choose a snapshot pic for selected video by visiting the 'Photos' section of this Dashboard.") ?>
                        <?php endif; ?>
                    </p>

                    <div class="clr">
                      <?php if($this->upload_video):?>
                         <?php if(!$this->integratedWithVideo):?>
                        <?php if ($this->type_video): ?>
                            <?php echo $this->htmlLink(array('route' => "siteevent_video_upload", 'action' => 'index', 'event_id' => $this->siteevent->event_id), $this->translate('Add New Video'), array('class' => 'buttonlink icon_siteevents_video_new')) ?>
                        <?php else: ?>
                            <?php echo $this->htmlLink(array('route' => "siteevent_video_create", 'event_id' => $this->siteevent->event_id), $this->translate('Add New Video'), array('class' => 'buttonlink icon_siteevents_video_new')) ?>
                        <?php endif; ?>
                        <?php else:?>
                        <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
                            <?php echo $this->htmlLink(array('route' => "sitevideo_video_general", 'action' => 'create',  'parent_type' => $this->siteevent->getType(), 'parent_id' => $this->siteevent->event_id), $this->translate('Add New Video'), array('data-SmoothboxSEAOClass' => 'seao_add_video_lightbox', 'class' => 'seao_smoothbox buttonlink icon_siteevents_video_new')) ?>
                        
                        <?php else:?>
                        <?php echo $this->htmlLink(array('route' => "sitevideo_video_general", 'action' => 'create',  'parent_type' => $this->siteevent->getType(), 'parent_id' => $this->siteevent->event_id), $this->translate('Add New Video'), array('class' => 'buttonlink icon_siteevents_video_new')) ?>
                        <?php endif;?>
                        <?php endif;?>
                      <?php endif; ?>
                    </div>

                    <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="">
                        <div>
                            <div>
                                <ul class='siteevent_edit_media' id="video">
                                    <?php if (!empty($this->count)): ?>
                                        <?php foreach ($this->videos as $item): ?>
                                            <li>
                                                <div class="siteevent_video_thumb_wrapper">
                                                    <?php if ($item->duration): ?>
                                                        <span class="siteevent_video_length">
                                                            <?php
                                                            if ($item->duration > 360)
                                                                $duration = gmdate("H:i:s", $item->duration);
                                                            else
                                                                $duration = gmdate("i:s", $item->duration);
                                                            if ($duration[0] == '0')
                                                                $duration = substr($duration, 1); echo $duration;
                                                            ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php
                                                    if ($item->photo_id)
                                                        echo $this->htmlLink($item->getHref(array('content_id' => $this->content_id)), $this->itemPhoto($item, 'thumb.normal'), array());
                                                    else
                                                        echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Video/externals/images/video.png">';
                                                    ?>
                                                </div>
                                                <div class="siteevent_edit_media_info">
                                                    <?php
                                                    $key = $item->getGuid();
                                                    echo $this->form->getSubForm($key)->render($this);
                                                    ?>
                                                    <?php if ($this->slideShowEnanle): ?>
                                                        <div class="siteevent_edit_media_options">
                                                            <div class="siteevent_edit_media_options_check">
                                                                <?php if ($this->type_video): ?>
                                                                    <?php $cover = 'corevideo_cover'; ?>
                                                                <?php else: ?>
                                                                    <?php $cover = 'reviewvideo_cover'; ?>
                                                                <?php endif; ?>
                                                                <input id="show_slideshow_id_<?php echo $item->video_id ?>" type="radio" name="<?php echo $cover; ?>" value="<?php echo $item->video_id ?>" <?php if ($this->main_video_id == $item->video_id): ?> checked="checked"<?php endif; ?> />
                                                            </div>
                                                            <div class="siteevent_edit_media_options_label">
                                                                <label for="show_slideshow_id_<?php echo $item->video_id ?>" ><?php echo $this->translate('Show in Slideshow'); ?></label>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?><br />
                                        <div class="tip">
                                            <span>
                                                <?php if(!$this->integratedWithVideo):?>
                                                <?php if ($this->type_video): ?>
                                                    <?php $url = $this->url(array('action' => 'index', 'event_id' => $this->siteevent->event_id, 'content_id' => $this->identity), "siteevent_video_upload", true); ?>
                                                    <?php echo $this->translate('You have not added any video in your event. %1$sClick here%2$s to add your first video.', "<a href='$url'>", "</a>"); ?>
                                                <?php else: ?>
                                                    <?php $url = $this->url(array('event_id' => $this->siteevent->event_id, 'content_id' => $this->identity), "siteevent_video_create", true); ?>
                                                    <?php echo $this->translate('There are currently no videos in this event. %1$sClick here%2$s to add your first video.', "<a href='$url'>", "</a>"); ?>
                                                <?php endif; ?>
                                                <?php else:?>
                                                <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
                                                <?php if ($this->type_video): ?>
                                                    <?php $url = $this->url(array('action' => 'create',  'parent_type' => $this->siteevent->getType(), 'parent_id' => $this->siteevent->event_id, 'content_id' => $this->identity), "sitevideo_video_general", true); ?>
                                                    <?php echo $this->translate('You have not added any video in your event. %1$sClick here%2$s to add your first video.', "<a class='seao_smoothbox' data-SmoothboxSEAOClass='seao_add_video_lightbox' href='$url'>", "</a>"); ?>
                                                <?php else: ?>
                                                    <?php $url = $this->url(array('action' => 'create',  'parent_type' => $this->siteevent->getType(), 'parent_id' => $this->siteevent->event_id, 'content_id' => $this->identity), "sitevideo_video_general", true); ?>
                                                    <?php echo $this->translate('There are currently no videos in this event. %1$sClick here%2$s to add your first video.', "<a class='seao_smoothbox' data-SmoothboxSEAOClass='seao_add_video_lightbox' href='$url'>", "</a>"); ?>
                                                <?php endif; ?>
                                                <?php else:?>
                                                <?php if ($this->type_video): ?>
                                                    <?php $url = $this->url(array('action' => 'create',  'parent_type' => $this->siteevent->getType(), 'parent_id' => $this->siteevent->event_id, 'content_id' => $this->identity), "sitevideo_video_general", true); ?>
                                                    <?php echo $this->translate('You have not added any video in your event. %1$sClick here%2$s to add your first video.', "<a href='$url'>", "</a>"); ?>
                                                <?php else: ?>
                                                    <?php $url = $this->url(array('action' => 'create',  'parent_type' => $this->siteevent->getType(), 'parent_id' => $this->siteevent->event_id, 'content_id' => $this->identity), "sitevideo_video_general", true); ?>
                                                    <?php echo $this->translate('There are currently no videos in this event. %1$sClick here%2$s to add your first video.', "<a href='$url'>", "</a>"); ?>
                                                <?php endif; ?>
                                                <?php endif;?>
                                                
                                                <?php endif;?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </ul>
                                <?php if (!empty($this->count)): ?>
                                  <div class="siteevent_edit_videos_button">
                                    <?php echo $this->form->button ?>
                                  </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>	
</div>			