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
<ul class="sitegroup_sidebar_list">
  <?php foreach ($this->paginator as $sitegroupmember): ?>
    <li>
			<?php echo $this->htmlLink($sitegroupmember->getHref(), $this->itemPhoto($sitegroupmember->getOwner(), 'thumb.icon')); ?>
			<div class='sitegroup_sidebar_list_info'>
				<div class='sitegroup_sidebar_list_title'>
					<?php echo $this->htmlLink($this->item('user', $sitegroupmember->user_id)->getHref(), $this->user($sitegroupmember->user_id)->displayname, array('title' => $sitegroupmember->displayname, 'target' => '_parent')); ?> 	
				</div>
			  <div class='sitegroup_sidebar_list_details'>
          <?php echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'group-join', 'user_id' => $sitegroupmember->user_id), $this->translate(array('%s Group Joined', '%s Groups Joined', $sitegroupmember->JOINP_COUNT), $this->locale()->toNumber($sitegroupmember->JOINP_COUNT)), array('class' => 'smoothbox')); ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>