<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitegroupalbum/externals/styles/style_sitegroupalbum.css');

include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $this->albumOfDay->group_id, $layout);?>
<ul class="generic_list_widget generic_list_widget_large_photo">
	<li>
		<div class="photo">
		  <?php echo $this->htmlLink($this->albumOfDay->getHref(array('tab' => $tab_id)), $this->itemPhoto($this->albumOfDay), array('title' => $this->albumOfDay->getTitle())); ?>
		</div>
		<div class="info">
			<div class="title">
			  <?php echo $this->htmlLink($this->albumOfDay->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($this->albumOfDay->getTitle(), 45), 10),array('title' => $this->albumOfDay->getTitle())) ?>
			</div>
	    <div class="owner">
				<?php $sitegroup_object = Engine_Api::_()->getItem('sitegroup_group', $this->albumOfDay->group_id);?>
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.title.truncation', 18);
				$tmpBody = strip_tags($sitegroup_object->title);
				$group_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>	
			<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->albumOfDay->group_id, $this->albumOfDay->owner_id, $this->albumOfDay->getSlug()),  $group_title,array('title' => $sitegroup_object->title)) ?>      
			</div>	
			<div class="stats">
			  <?php echo $this->translate(array('%s photo', '%s photos', $this->albumOfDay->count()), $this->locale()->toNumber($this->albumOfDay->count())); ?>
			</div>
		</div>
	</li>
</ul>		