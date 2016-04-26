<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<div class="tip">
  <span> <?php echo $this->translate('No Groups have been created yet.'); ?>
    <?php if ($this->can_create): ?>
      <?php
      if (Engine_Api::_()->sitegroup()->hasPackageEnable()):
        $createUrl = $this->url(array('action' => 'index'), 'sitegroup_packages');
      else:
        $createUrl = $this->url(array('action' => 'create'), 'sitegroup_general');
      endif;
      ?>
  <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $createUrl . '">', '</a>'); ?>
<?php endif; ?>
  </span>
</div>