<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: group-join.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
   $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
            . 'application/modules/Seaocore/externals/styles/style_infotooltip.css');
$settings = Engine_Api::_()->getApi('settings', 'core');
?>

<div class="sm-content-list" id="profile_groups">
  <?php $user = Engine_Api::_()->user()->getUser($this->user_id); ?>
  <h3><?php echo $this->translate('Groups joined by ')?><a href="<?php echo $user->getHref();?>"><?php echo $user->displayname ?></a></h3>
	<ul data-role="listview" data-icon="arrow-r">
		<?php foreach ($this->paginator as $value): ?>
      <?php $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $value->group_id); ?>
			<li>
				<a href="<?php echo $sitegroup->getHref(); ?>">
					<?php echo $this->itemPhoto($sitegroup, 'thumb.icon'); ?>
          <h3><?php echo $this->string()->chunk($this->string()->truncate($sitegroup->getTitle(), 45), 10); ?></h3>
					<p><?php echo $this->translate(array('%s Member', '%s Members', $sitegroup->member_count), $this->locale()->toNumber($sitegroup->member_count)) ?></p>
				</a> 
			</li>
		<?php endforeach; ?>   
	</ul>
</div>  
<?php if ($this->paginator->count() > 1): ?>
	<?php
		echo $this->paginationAjaxControl(
					$this->paginator, $this->identity, 'profile_groups');
	?>
<?php endif; ?>