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
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/styles/sitegroup-tooltip.css');

include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<ul class="sitegrouplike_users_block">
  <li>
    <?php foreach ($this->featuredowners as $item): ?>
      <div class="likes_member_sitegroup sitegroup_show_owner_tooltip_wrapper">
        <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>	
        <div class='sitegroup_show_owner_tooltip'>
          <img src="./application/modules/Sitegroup/externals/images/tooltip_arrow.png" alt="" />
          <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
        </div>
      </div>
    <?php endforeach; ?>
  </li>	
</ul>