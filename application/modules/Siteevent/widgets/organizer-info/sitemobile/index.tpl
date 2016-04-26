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
<?php if (in_array('photo', $this->showInfo)): ?>
  <div class="sm_profile_item_photo">
    <?php echo $this->itemPhoto($this->organizer, 'thumb.profile') ?>
  </div>
<?php endif; ?>

<?php if (in_array('options', $this->showInfo) && $this->viewer_id && ($this->viewer_id == $this->organizer->creator_id || $this->level_id == 1)): ?>
  <!--         <div class="thread_options fright" data-role="controlgroup" data-type="horizontal" data-mini="true">
  <?php
  echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'organizer', 'action' => 'edit', 'type' => $this->organizer->getType(), 'organizer_id' => $this->organizer->getIdentity(), 'format' => 'smoothbox'), $this->translate('Edit'), array(
      'data-role' => "button", 'data-icon' => "edit", 'data-iconpos' => 'notext', 'data-ajax' => 'false'
  ))
  ?>
  <?php
  echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'organizer', 'action' => 'delete', 'type' => $this->organizer->getType(), 'organizer_id' => $this->organizer->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array(
      'data-role' => "button", 'data-icon' => "delete", 'data-iconpos' => 'notext'
  ))
  ?>
          </div>-->
<?php endif; ?>
<div class="sm_profile_item_info">
  <?php if (in_array('title', $this->showInfo)): ?>
    <div class="sm_profile_item_title">
      <?php echo $this->organizer->getTitle(); ?>
    </div>
  <?php endif; ?> 
</div>

<?php if (in_array('creator', $this->showInfo)): ?>   
  <div class='sm_ui_item_profile_details t_light'>
    <?php echo $this->translate("Added by %s", $this->htmlLink($this->organizer->getOwner()->getHref(), $this->organizer->getOwner()->getTitle())); ?>
  </div>
<?php endif; ?>
<?php if ($this->showInfo && (in_array('totalevent', $this->showInfo) || in_array('totalguest', $this->showInfo) || in_array('totalrating', $this->showInfo) || in_array('description', $this->showInfo))): ?> 
  <div class="sm_ui_item_profile_details t_light">
    <?php if (in_array('totalevent', $this->showInfo)): ?>
      <?php $countOrganizedEvent = $this->organizer->countOrganizedEvent(); ?> 
      <?php echo $this->translate(array('<b>%s</b> event hosted', '<b>%s</b> events hosted', $countOrganizedEvent), $this->locale()->toNumber($countOrganizedEvent)); ?><?php if (in_array('totalguest', $this->showInfo)): ?>,
        <?php echo $this->translate(array('<b>%s</b> guest joined', '<b>%s</b> guests joined', $this->totalGuest), $this->locale()->toNumber($this->totalGuest)); ?>  
      <?php endif; ?> 
    <?php endif; ?>
    <?php if ($this->totalRating && in_array('totalrating', $this->showInfo)): ?>
      <?php echo $this->translate("Total ratings:"); ?>
      <div>
        <?php echo $this->showRatingStarSiteevent($this->totalRating, 'overall', 'big-star'); ?>
      </div>
    <?php endif; ?>
  </div>
  <?php if (in_array('body', $this->allowedInfo) && in_array('description', $this->showInfo)): ?> 
    <div class='sm_ui_item_profile_details'>
      <?php echo $this->organizer->description; ?>
    </div>
  <?php endif; ?>

<?php endif; ?>