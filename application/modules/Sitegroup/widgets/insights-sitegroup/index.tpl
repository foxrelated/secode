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
<ul class="sitegroup_sidebar_list sitegroup_sidebar_insights">
  <li>
    <div><?php echo $this->translate(array('<span> %s </span> Monthly Active User', '<span> %s </span> Monthly Active Users', $this->total_users), $this->locale()->toNumber($this->total_users)) ?></div>
  </li>
  <li>  
    <div><?php echo $this->translate(array('<span> %s </span> Like', '<span> %s </span> Likes', $this->sitegroup->like_count), $this->locale()->toNumber($this->sitegroup->like_count)) ?></div>
  </li>
  <?php $showComment = Engine_Api::_()->sitegroup()->displayCommentInsights(); if(!empty($showComment)): ?>
    <li>  
      <div><?php echo $this->translate(array('<span> %s </span> Comment', '<span> %s </span> Comments', $this->sitegroup->comment_count), $this->locale()->toNumber($this->sitegroup->comment_count)) ?></div>
    </li>	
  <?php endif; ?>
  
  <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')): ?>
		<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
		if ($this->sitegroup->member_title && $memberTitle) : ?>
			 <li><div><span><?php echo $this->sitegroup->member_count ?></span><?php  echo  ' ' . $this->sitegroup->member_title; ?></div> </li>  

	<?php else : ?>
		<li>  
			<div><?php echo $this->translate(array('<span> %s </span> Member', '<span> %s </span> Members', $this->sitegroup->member_count), $this->locale()->toNumber($this->sitegroup->member_count)) ?></div>
		</li>
	<?php endif; ?>
	<?php endif; ?>

  
  <li>  
    <div><?php echo $this->translate(array('<span> %s </span> View', '<span> %s </span> Views', $this->sitegroup->view_count), $this->locale()->toNumber($this->sitegroup->view_count)) ?></div>
  </li>	
  <li>
    <?php
    echo $this->htmlLink(
            array('route' => 'sitegroup_insights', 'group_id' => $this->sitegroup->group_id), $this->translate('See All &raquo;')
    )
    ?>
  </li>
</ul>