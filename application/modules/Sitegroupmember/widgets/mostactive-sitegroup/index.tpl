<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<ul class="sitegroup_sidebar_list">
	<?php foreach ($this->sitegroups as $sitegroup): $statistics = ''; ?>
		<li>
			<?php  $this->partial()->setObjectKey('sitegroup');
				echo $this->partial('application/modules/Sitegroup/views/scripts/partial_widget.tpl', $sitegroup);
			?>
			<?php if(is_array($this->statistics) && in_array("comments", $this->statistics)):?>
				<?php $statistics .= $this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count)) . ', ' ?>
			<?php endif;?>
			<?php if(is_array($this->statistics) && in_array("likes", $this->statistics)):?>
				<?php $statistics .= $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count)) . ', '  ?>
			<?php endif;?>
			<?php if(is_array($this->statistics) && in_array("views", $this->statistics)):?>
				<?php $statistics .= $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count)) . ', ' ?>
			<?php endif;?>
      <?php if(is_array($this->statistics) && in_array("members", $this->statistics)):?>
				<?php $statistics .=  $this->translate(array('%s member', '%s members', $sitegroup->member_count), $this->locale()->toNumber($sitegroup->member_count)) ?>
      <?php endif;?>
      <?php
        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');
      ?>
      <?php echo $statistics;?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>