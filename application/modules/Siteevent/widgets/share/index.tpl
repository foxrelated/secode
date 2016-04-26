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

<div class="siteevent_social_share_wrapper siteevent_side_widget">
    <?php if (!empty($this->optionsArray)): ?>
        <div class="siteevent_social_share">
            <?php if ($this->viewer_id && in_array("siteShare", $this->optionsArray)): ?>
                <?php echo $this->htmlLink(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'route' => 'default', 'type' => $this->subject->getType(), 'id' => $this->subject->getIdentity(), 'not_parent_refresh' => '1', 'format' => 'smoothbox'), '', array('class' => 'smoothbox siteevent_share_icon_link seaocore_icon_share', 'title' => $this->translate('Share'))); ?>
            <?php endif; ?>

            <?php if ($this->subject->getType() == 'siteevent_diary' && in_array("friend", $this->optionsArray)): ?>
                <?php echo $this->htmlLink(array('action' => 'tell-a-friend', 'route' => 'siteevent_diary_general', 'type' => $this->subject->getType(), 'diary_id' => $this->subject->getIdentity()), '', array('target' => '_blank', 'class' => 'smoothbox siteevent_share_icon_link icon_siteevents_tellafriend', 'title' => $this->translate('Tell a Friend'))); ?>    
            <?php elseif ($this->subject->getType() == 'siteevent_event' && in_array("friend", $this->optionsArray)): ?>
                <?php echo $this->htmlLink(array('action' => 'tellafriend', 'route' => 'siteevent_specific', 'type' => $this->subject->getType(), 'event_id' => $this->subject->getIdentity()), '', array('target' => '_blank', 'class' => 'smoothbox siteevent_share_icon_link icon_siteevents_tellafriend', 'title' => $this->translate('Tell a Friend'))); ?>    
            <?php endif; ?>  

            <?php if ($this->subject->getType() == 'siteevent_diary' && in_array("print", $this->optionsArray)): ?>
                <?php echo $this->htmlLink(array('action' => 'print', 'route' => 'siteevent_diary_general', 'type' => $this->subject->getType(), 'diary_id' => $this->subject->getIdentity(), 'content_id' => $this->content_id), '', array('target' => '_blank', 'class' => 'siteevent_share_icon_link icon_siteevents_printer', 'title' => $this->translate('Print'))); ?>    
            <?php elseif ($this->subject->getType() == 'siteevent_event' && in_array("print", $this->optionsArray)): ?>
                <?php $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null; ?>
                <?php echo $this->htmlLink(array('action' => 'print', 'route' => 'siteevent_specific', 'event_id' => $this->subject->getIdentity(), 'occurance_id' => $occurrence_id), '', array('target' => '_blank', 'class' => 'siteevent_share_icon_link icon_siteevents_printer', 'title' => $this->translate('Print'))); ?>    
            <?php endif; ?>

            <?php if ($this->viewer_id && in_array("report", $this->optionsArray)): ?>
                <?php echo $this->htmlLink(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->subject->getGuid()), '', array('class' => 'smoothbox siteevent_share_icon_link seaocore_icon_report', 'title' => $this->translate('Report'))); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (in_array("socialShare", $this->optionsArray) && $this->allowSocialSharing): ?>
        <div class="siteevent_social_share">
            <?php echo $this->code; ?>
        </div>
    <?php endif; ?>
</div> 
