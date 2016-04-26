<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphotos.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php echo $this->partial('application/modules/Siteevent/views/sitemobile/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
<div class="dashboard-content">
  <div class="global_form">
    <h3><?php echo $this->translate("Edit Event Photos"); ?></h3>
    <p class="form-description">
      <?php echo $this->translate("Edit and manage the photos of your Event below."); ?>
    </p>
    <?php if(!empty($this->upload_photo)):?>
      <div class="clr">
        <?php echo $this->htmlLink(array('route' => "siteevent_photoalbumupload", 'album_id' => $this->album_id, 'event_id' => $this->event_id), $this->translate('Add New Photos'), array('class' => 'buttonlink icon_photos_new')) ?>
      </div>
    <?php endif;?>

    <?php if( $this->paginator->count() > 0 ): ?>
      <?php echo $this->paginationControl($this->paginator); ?>
    <?php endif; ?>

    <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
      <?php echo $this->form->album_id; ?>
      <ul class='dashboard-content-manage-media' id="photo">
        <?php if(!empty($this->count)): ?>
          <?php foreach ($this->paginator as $photo):?>
            <li class="b_medium">
              <div class="media-img b_medium">
                <?php echo $this->itemPhoto($photo, 'thumb.normal') ?>
              </div>
              <?php
                $key = $photo->getGuid();
                echo $this->form->getSubForm($key)->render($this);
              ?>
              <div class='sr_edit_media_options'>
                <div class="sr_edit_media_options_check">
                  <input id="main_photo_id_<?php echo $photo->photo_id ?>" type="radio" name="cover" value="<?php echo $photo->file_id ?>" <?php if ($this->siteevent->photo_id == $photo->file_id): ?> checked="checked"<?php endif; ?> />
                </div>
                  <div class="sr_edit_media_options_label">
                                                    <label for="main_photo_id_<?php echo $photo->photo_id ?>"><?php echo $this->translate('Main Photo'); ?></label>
                 </div>
              </div>
              <?php if($this->enableVideoPlugin && $this->allowed_upload_video): ?>
                <div class="sr_edit_media_options" class='video_snapshot_id-wrapper'>              
                  <div class="sr_edit_media_options_check">
                    <input id="video_snapshot_id_<?php echo $photo->photo_id ?>" type="radio" name="video_snapshot_id" value="<?php echo $photo->photo_id ?>" <?php if ($siteeventOtherInfo->video_snapshot_id == $photo->photo_id): ?> checked="checked"<?php endif; ?> />
                  </div>
                  <div class="sr_edit_media_options_label">
                    <label for="video_snapshot_id_<?php echo $photo->photo_id ?>"><?php echo $this->translate('Make Video Snapshot');  ?></label>
                  </div>
                </div>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        <?php else:?>
          <div class="tip">
            <span>
              <?php $url = $this->url(array('event_id' => $this->event_id), 'siteevent_photoalbumupload', true); ?>
               <?php echo $this->translate('There are currently no photos in this event. %1$sClick here%2$s to add photos now!', "<a href='$url'>", "</a>"); ?>
            </span>
          </div>
        <?php endif;?>
      </ul>
      <?php if(!empty($this->count)): ?>
        <div class="clr">
          <br />
          <?php echo $this->form->submit->render(); ?>
        </div>
      <?php endif;?>
    </form>
    <?php if( $this->paginator->count() > 0 ): ?>
      <br />
      <?php echo $this->paginationControl($this->paginator); ?>
    <?php endif; ?>
  </div>
</div>