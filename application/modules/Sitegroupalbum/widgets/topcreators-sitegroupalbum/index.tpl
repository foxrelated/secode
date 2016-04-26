<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<ul class="sitegroup_sidebar_list">
  <?php foreach ($this->paginator as $album):?>
        <li>
					<?php $sitegroup_object = Engine_Api::_()->getItem('sitegroup_group', $album['group_id']);?>
					<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
					$tab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $album['group_id'], $layout);?>
					<a href="<?php echo Engine_Api::_()->sitegroup()->getHref($sitegroup_object->group_id, $sitegroup_object->owner_id, $sitegroup_object->getSlug()); ?>">
						<?php echo $this->itemPhoto($sitegroup_object, 'thumb.icon', $sitegroup_object->getTitle()) ?>
					</a>
					<div class="sitegroup_sidebar_list_info">
						<div class="sitegroup_sidebar_list_title">
            <?php echo $this->htmlLink($sitegroup_object->getHref(), Engine_Api::_()->sitegroup()->truncation($sitegroup_object->getTitle()), array('title' => $sitegroup_object->getTitle())); ?> 

						</div>
						<?php $category = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroup_object->category_id);?>
						<?php $subCategory = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroup_object->subcategory_id);?>
						<?php if ($category): ?>
							<div class="sitegroup_sidebar_list_details">
								<?php echo $category->category_name;?>
								<?php if ($subCategory): ?> &raquo;
								<?php echo $subCategory->category_name; ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>
            </div>
						<div class="sitegroup_sidebar_list_details">
						 <?php echo $this->htmlLink($sitegroup_object->getHref(array('tab'=> $tab_id)),$album['item_count'].' photos'); ?>
				    </div>	
      </li>
	<?php endforeach; ?>
</ul>