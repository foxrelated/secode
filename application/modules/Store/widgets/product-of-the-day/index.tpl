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

<h3><?php echo $this->translate($this->widget_title)?></h3>

<div class="store-widget">

  <div style="font-size: 16px; font-weight: 500; margin-bottom: 5px;">

<?php if($this->product->sponsored): ?>
      <img title="<?php echo $this->translate('STORE_Sponsored'); ?>" class="of-the-day" src="application/modules/Store/externals/images/admin/sponsored1.png">
    <?php endif; ?>
    
    <?php
      echo $this->htmlLink(
        $this->product->getHref(),
        $this->product->getTitle()
      );
    ?>

  </div>


    
  <?php
    echo $this->htmlLink(
      $this->product->getHref(),
      $this->itemPhoto($this->product, 'thumb.normal', '', array('class' => 'img-of-the-day', 'style' => 'display: block;'))
    );
  ?>

<span id="product_owner">
	<?php
	$subject = Engine_Api::_()->user()->getUser($this->product->owner_id);
	?>
	<a href="/profile/<?php echo($subject->username);?>" target="_blank" ><?php echo($subject->username);?></a>
</span>

</div>