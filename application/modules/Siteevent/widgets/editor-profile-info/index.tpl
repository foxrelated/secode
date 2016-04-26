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

<ul class="seaocore_sidebar_list siteevent_editor_profile_info">
    <li>
        <div class="siteevent_editor_profile_details o_hidden">
            <?php if (!empty($this->badge_photo_id)): ?>
                <?php $thumb_path = Engine_Api::_()->storage()->get($this->badge_photo_id, '')->getPhotoUrl(); ?>
                <?php if (!empty($thumb_path)): ?>
                    <img width="50px" src='<?php echo $thumb_path ?>' alt="" class="fleft" />
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($this->editor->details)): ?>    
                <div class="siteevent_editor_profile_stats">
                    <?php echo $this->viewMore($this->editor->details, 500, 5000); ?>
                </div>          
            <?php endif; ?>    

            <?php if (!empty($this->editor->designation) && $this->show_designation): ?>
                <div class="siteevent_editor_profile_stats o_hidden">
                    <span><i><?php echo $this->translate("Designation:"); ?></i></span><br />
                    <span><b><?php echo $this->editor->designation; ?></b></span>
                </div>
            <?php endif; ?>

        </div>
        <?php echo $this->content()->renderWidget("siteevent.write-siteevent", array("removeContent" => true)); ?>
    </li>
</ul>


