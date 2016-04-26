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

<div class="event_profile_buttons">
    <a href='<?php echo $this->url(array('event_id' => $this->siteevent->event_id, 'action' => 'add'), "siteevent_diary_general", true) ?>'  class='smoothbox siteevent_buttonlink mbot5'><?php echo $this->translate('Add to Diary'); ?></a>
    <?php if ($this->diaryAddCount && $this->totalDiaryAddCount): ?>
        <div class="f_small">
            <?php if ($this->totalDiaryAddCount == 1): ?>
                <?php echo $this->translate('%s person has added this event to diary.', $this->totalDiaryAddCount); ?>
            <?php else: ?>
                <?php echo $this->translate('%s people have added this event to their diaries.', $this->totalDiaryAddCount); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

