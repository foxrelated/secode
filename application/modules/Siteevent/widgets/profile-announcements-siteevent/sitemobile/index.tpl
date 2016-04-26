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

<div id='id_<?php echo $this->content_id; ?>'>
    <?php if (count($this->announcements) > 0): ?>
        <ul class="siteevent_profile_announcements">
            <?php foreach ($this->announcements as $item): ?>
                <li>
                    <?php if ($this->showTitle): ?>
                        <div class="siteevent_profile_announcement_title mbot5"><?php echo $item->title; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($item->body)): ?>
                        <div class="siteevent_profile_list_info_des show_content_body">
                            <?php echo $item->body; ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="tip">
            <span>
                <?php echo $this->translate('No announcements have been created yet.'); ?>
            </span>
        </div>
    <?php endif; ?>
</div>