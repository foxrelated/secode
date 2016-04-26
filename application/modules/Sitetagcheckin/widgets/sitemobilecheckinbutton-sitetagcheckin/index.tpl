<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$url = $this->url(array('action' => 'check-in', 'module' => 'sitetagcheckin', 'controller' => 'checkin', 'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'checkin_use' => $this->checkin_use, 'checkin_verb' => $this->checkin_verb,  'checkedinto_verb' => $this->checkedinto_verb, 'checkin_your' => $this->checkin_your), 'default', true);
?>
<div class="sm-widget-block">
  <?php if($this->checkin_button_sidebar) :?>
    <div>
      <?php if ($this->checkin_button): ?>
        <a href="<?php echo $url; ?>" class="stchecckin_button ui-btn ui-btn-b" data-ajax="true">
          <div>
            <?php if($this->checkin_icon):?>
              <span class="ui-icon ui-icon-map-marker"></span>
            <?php else:?>
              <span class="ui-icon ui-icon-ok"></span>
            <?php endif;?>
            <span class="stcheckin_check"><?php echo $this->translate($this->checkin_button_link); ?></span>
          </div>
        </a>
      <?php else: ?>
        <div class="stchecckin_link">
          <?php if($this->checkin_icon):?>
          	<span class="ui-icon ui-icon-map-marker"></span>
            <a href='<?php echo $url; ?>' data-ajax="true">
              <?php echo $this->translate($this->checkin_button_link); ?>
            </a> 
          <?php else:?>
          	<span class="ui-icon ui-icon-ok"></span>
            <a href='<?php echo $url; ?>' data-ajax="true">
              <?php echo $this->translate($this->checkin_button_link); ?>
            </a> 
          <?php endif;?>
        </div>
      <?php endif; ?>
      <div class="stcheck_ckeckin_stat clr">
        <?php echo $this->translate(array('%1$s %2$s time.', '%1$s %2$s times.', $this->user_check_in_count), $this->translate($this->checkin_your), $this->locale()->toNumber($this->user_check_in_count)) ?>
      </div>
      <div class="stcheck_ckeckin_stat clr">
        <?php echo $this->translate(array('%1$s %2$s time.', '%1$s %2$s times.', $this->check_in_count), $this->translate($this->checkin_total), $this->locale()->toNumber($this->check_in_count)) ?>
      </div>
    </div>
  <?php else:?>
    <div class="stchecckin_button_show_full">
      <?php if ($this->checkin_button): ?>
        <a href="<?php echo $url; ?>" class="stchecckin_button ui-btn-default ui-btn-action" data-ajax="true">
          <div>
            <?php if($this->checkin_icon):?>
              <span class="ui-icon ui-icon-map-marker"></span>
            <?php else:?>
              <span class="ui-icon ui-icon-ok"></span>
            <?php endif;?>
            <span class="stcheckin_check"><?php echo $this->translate($this->checkin_button_link); ?></span>
          </div>
        </a>
      <?php else: ?>
        <div class="stchecckin_link">
          <?php if($this->checkin_icon):?>
          	<span class="ui-icon ui-icon-map-marker"></span>
            <a href='<?php echo $url; ?>' data-ajax="true">
              <?php echo $this->translate($this->checkin_button_link); ?>
            </a> 
          <?php else:?>
          	<span class="ui-icon ui-icon-ok"></span>
            <a href='<?php echo $url; ?>' data-ajax="true">
              <?php echo $this->translate($this->checkin_button_link); ?>
            </a> 
          <?php endif;?>
        </div>
      <?php endif; ?>
      <div class="stcheck_ckeckin_stat">
        <?php echo $this->translate(array('%1$s %2$s time.', '%1$s %2$s times.', $this->user_check_in_count), $this->translate($this->checkin_your), $this->locale()->toNumber($this->user_check_in_count)) ?>
      </div>
      <div class="stcheck_ckeckin_sep">-</div>
      <div class="stcheck_ckeckin_stat">
        <?php echo $this->translate(array('%1$s %2$s time.', '%1$s %2$s times.', $this->check_in_count), $this->translate($this->checkin_total), $this->locale()->toNumber($this->check_in_count)) ?>
      </div>
    </div>
  <?php endif;?>
</div>