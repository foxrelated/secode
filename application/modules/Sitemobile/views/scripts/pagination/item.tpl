<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: item.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->pageCount): ?>
<div class="paginationControl">
<?php echo $this->firstItemNumber; ?> - <?php echo $this->lastItemNumber; ?>
of <?php echo $this->totalItemCount; ?>

<!-- First page link -->
<?php if (isset($this->previous)): ?>
  <a href="<?php echo $this->url(array('page' => $this->first)); ?>">
    <?php echo $this->translate('First') ?>
  </a> |
<?php else: ?>
  <span class="disabled"><?php echo $this->translate('First') ?></span> |
<?php endif; ?>

<!-- Previous page link -->
<?php if (isset($this->previous)): ?>
  <a href="<?php echo $this->url(array('page' => $this->previous)); ?>">
    <?php echo $this->translate('Previous') ?>
  </a> |
<?php else: ?>
  <span class="disabled"><?php echo $this->translate('Previous') ?></span> |
<?php endif; ?>

<!-- Next page link -->
<?php if (isset($this->next)): ?>
  <a href="<?php echo $this->url(array('page' => $this->next)); ?>">
    <?php echo $this->translate('Next') ?>
  </a> |
<?php else: ?>
  <span class="disabled"><?php echo $this->translate('Next') ?></span> |
<?php endif; ?>

<!-- Last page link -->
<?php if (isset($this->next)): ?>
  <a href="<?php echo $this->url(array('page' => $this->last)); ?>">
    <?php echo $this->translate('Last') ?>
  </a>
<?php else: ?>
  <span class="disabled"><?php echo $this->translate('Last') ?></span>
<?php endif; ?>

</div>
<?php endif; ?>