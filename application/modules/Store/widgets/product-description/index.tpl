<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>

<h3>
  <?php echo('' != trim($this->product->getTitle()) ? $this->product->getTitle() : '<em>' . $this->translate('Untitled') . '</em>'); ?>
  <?php echo ($this->isLike) ? $this->likeButton($this->product) : ''; ?>
</h3>


  <div class="he-item-desc product_profile_desc">
    <span><?php echo $this->product->getFullDescription(); ?></span>
  </div>

