<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphotos.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
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
                    <h3><?php echo $this->translate("Edit Event Photos"); ?></h3>
                    <p class="form-description"><?php echo $this->translate("Edit and manage the photos of your event below."); ?>
                        <?php if ($this->slideShowEnanle): ?>
                            <br />
                            <?php echo $this->translate("An attractive Slideshow will be displayed on your event Profile page. Below, you can choose the photos to be displayed in that slideshow by using the 'Show in Slideshow' option."); ?>
                            <?php if ($this->enableVideoPlugin && $this->allowed_upload_video): ?>
                                <?php echo $this->translate("You can also choose the photo snapshot pic for the video displayed in the slideshow by using 'Make Video Snapshot' option."); ?>
                                <br />
                                <b><?php echo $this->translate("Note: "); ?></b><?php echo $this->translate("You can select the video to be displayed in the Slideshow from the 'Videos' section of this Dashboard."); ?>
                            <?php endif; ?>

                        <?php endif; ?></p>
                    <!--PACKAGE BASED CHECKS-->
                    <?php if(!empty($this->upload_photo)):?>
                    <div class="clr">
                        <?php echo $this->htmlLink(array('route' => "siteevent_photoalbumupload", 'album_id' => $this->album_id, 'event_id' => $this->event_id), $this->translate('Add New Photos'), array('class' => 'buttonlink icon_photos_new')) ?>
                    </div>
                     <?php endif; ?>

                    <?php if ($this->paginator->count() > 0): ?>
                        <?php echo $this->paginationControl($this->paginator); ?>
                    <?php endif; ?>

                    <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
                        <?php echo $this->form->album_id; ?>
                        <ul class='siteevent_edit_media' id="photo">
                            <?php if (!empty($this->count)): ?>
                                <?php foreach ($this->paginator as $photo): ?>
                                    <li>
                                        <div class="siteevent_edit_media_thumb"> <?php echo $this->itemPhoto($photo, 'thumb.normal') ?> </div>
                                        <div class="siteevent_edit_media_info">
                                            <?php
                                            $key = $photo->getGuid();
                                            echo $this->form->getSubForm($key)->render($this);
                                            ?>
                                            <div class='siteevent_edit_media_options'>
                                                <div class="siteevent_edit_media_options_check">
                                                    <input id="main_photo_id_<?php echo $photo->photo_id ?>" type="radio" name="cover" value="<?php echo $photo->file_id ?>" <?php if ($this->siteevent->photo_id == $photo->file_id): ?> checked="checked"<?php endif; ?> />
                                                </div>
                                                <div class="siteevent_edit_media_options_label">
                                                    <label for="main_photo_id_<?php echo $photo->photo_id ?>"><?php echo $this->translate('Main Photo'); ?></label>
                                                </div>
                                            </div>
                                            <?php if ($this->enableVideoPlugin && $this->allowed_upload_video): ?>
                                                <div class="siteevent_edit_media_options" class='video_snapshot_id-wrapper'>              
                                                    <div class="siteevent_edit_media_options_check">
                                                        <?php $siteeventOtherInfo = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getOtherinfo($this->siteevent->event_id); ?>
                                                        <input id="video_snapshot_id_<?php echo $photo->photo_id ?>" type="radio" name="video_snapshot_id" value="<?php echo $photo->photo_id ?>" <?php if ($siteeventOtherInfo->video_snapshot_id == $photo->photo_id): ?> checked="checked"<?php endif; ?> />
                                                    </div>
                                                    <div class="siteevent_edit_media_options_label">
                                                        <label for="video_snapshot_id_<?php echo $photo->photo_id ?>"><?php echo $this->translate('Make Video Snapshot'); ?></label>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?><br />
                                <div class="tip">
                                    <span>
                                        <?php $url = $this->url(array('event_id' => $this->event_id), 'siteevent_photoalbumupload', true); ?>
                                        <?php echo $this->translate('There are currently no photos in this event. %1$sClick here%2$s to add photos now!', "<a href='$url'>", "</a>"); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </ul>
                        <?php if (!empty($this->count)): ?>
                          <div class="siteevent_edit_photos_button">
                            <?php echo $this->form->submit->render(); ?>
                          </div>
                        <?php endif; ?>
                    </form>
                    <?php if ($this->paginator->count() > 0): ?>
                        <br />
                        <?php echo $this->paginationControl($this->paginator); ?>
                    <?php endif; ?>
                </div>		
            </div>
        </div>
    </div>			
</div>