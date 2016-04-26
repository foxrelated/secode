<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: index.tpl 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>

<ul class="seaocore_sidebar_list">
	<?php if($this->paginator) : ?>
		<?php foreach( $this->paginator as $user ): ?>
			<li>
				<?php echo $this->htmlLink(Engine_Api::_()->getItem('user', $user->userid)->getHref(), $this->itemPhoto(Engine_Api::_()->getItem('user', $user->userid), 'thumb.icon', '' , array('align'=>'center'))) ?>
				<div class="seaocore_sidebar_list_info">
					<div class="seaocore_sidebar_list_title">
						<?php echo $this->htmlLink(Engine_Api::_()->getItem('user', $user->userid)->getHref(), Engine_Api::_()->poke()->turncation(Engine_Api::_()->getItem('user', $user->userid)->getTitle(), Engine_Api::_()->getApi('settings', 'core')->poke_title_turncation)) ?>
					</div>	
					<div class="seaocore_sidebar_list_details">
          	<?php echo $this->translate('By:');?> <?php echo $this->htmlLink(Engine_Api::_()->getItem('user', $user->resourceid)->getHref(),Engine_Api::_()->getItem('user', $user->resourceid)->getTitle())?>
          </div>
				</div>
			</li>
		<?php endforeach; ?>	
	<?php endif; ?>
</ul>