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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css'); ?>
<ul class="siteevent_editor_event">
    <?php foreach ($this->editors as $editor): ?>
        <li>
            <div class="siteevent_editor_event_photo">
                <?php echo $this->htmlLink($editor->getHref(), $this->itemPhoto($editor, 'thumb.profile'), array('class' => 'editors_thumb')) ?>
            </div>
            <div class='siteevent_editor_event_info'>
                <div class='siteevent_editor_event_name'>
                    <?php echo $this->htmlLink($editor->getHref(), $editor->getUserTitle($editor->user_id)) ?>
                </div>

                <?php if (!empty($editor->designation)): ?>
                    <div class="siteevent_editor_event_stat"><?php echo $editor->designation; ?></div>
                <?php endif; ?>

                <?php
                $params = array();
                $params['type'] = 'editor';
                $params['owner_id'] = $editor->user_id;
                ?> 
                <?php $totalReviews = Engine_Api::_()->getDbTable('reviews', 'siteevent')->totalReviews($params); ?>
                <div class="siteevent_editor_event_stat seaocore_txt_light"> 
                    <?php echo $this->translate(array('%s Review', '%s Reviews', $totalReviews), $this->locale()->toNumber($totalReviews)); ?>
                </div>          

                <?php if (!$editor->isSelf($this->viewer()) && $editor->getUserEmail($editor->user_id)): ?>
                    <div class="siteevent_editor_event_stat"><b><?php echo $this->htmlLink(array('route' => "siteevent_review_editor", 'action' => 'editor-mail', 'user_id' => $editor->user_id), $this->translate('Email %s', $editor->getUserTitle($editor->user_id)), array('class' => 'smoothbox')) ?></b></div>
                <?php endif; ?>

                <div class="siteevent_editor_event_stat"><b><?php echo $this->htmlLink($editor->getHref(), $this->translate('View Profile &raquo;')) ?></b></div>
            </div>
        </li>
    <?php endforeach; ?>
</ul>