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

<div class="tip">
    <span> 
        <?php echo $this->translate('No events have been created yet.'); ?>
        <?php if ($this->can_create): ?>
        <?php if (Engine_Api::_()->siteevent()->hasPackageEnable()):?>
        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'index'), "siteevent_package") . '">', '</a>'); ?>
        <?php else:?>
        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create'), "siteevent_general") . '">', '</a>'); ?>
        <?php endif; ?>
      <?php endif; ?>
    </span>
</div>