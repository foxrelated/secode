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
				<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', '' , array('align'=>'center'))) ?>
				<div class="seaocore_sidebar_list_info">
					<div class="seaocore_sidebar_list_title">
          	<?php echo $this->htmlLink($user->getHref(), Engine_Api::_()->poke()->turncation($user->getTitle(), Engine_Api::_()->getApi('settings', 'core')->poke_title_turncation)) ?>
          </div>
          <div class="seaocore_sidebar_list_details">	
          	<span class="poke_type_icon"><?php echo $this->translate(array('%s Poke', '%s Pokes', $user->poke_count), $this->locale()->toNumber($user->poke_count))?></span>
					</div>
				</div>
			</li>
		<?php endforeach; ?>	
	<?php endif; ?>
</ul>