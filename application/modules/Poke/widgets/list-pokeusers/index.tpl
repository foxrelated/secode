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

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Poke/externals/scripts/core.js');
?>

<ul class="seaocore_sidebar_list">
		<?php     
			$send = Engine_Api::_()->authorization()->getPermission(Engine_Api::_()->user()->getViewer()->level_id, 'poke', 'send');
			$div_id = 0; 
		?>
	<?php if(empty($this->user_photo)):?>
	<li>
		<?php if($this->paginator) : ?>
			<div class="poke_icon">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Poke/externals/images/poke_icon.png', '') ?>
			</div>
		<?php endif;?>
		<div class="users_poked">	
		  <?php foreach( $this->paginator as $user ): ?>
	  	  <?php $div_id++; ?>
     		<div class="users_poked_list" id='<?php echo $user->pokeuser_id ?>_poke'>
					<div title="<?php echo $user->getTitle(). ' ' . $this->translate('poked you on') . ' ' . date("d M Y", $user->created)?>">
						 <?php echo $this->htmlLink($user->getHref(), Engine_Api::_()->poke()->turncation($user->getTitle(), Engine_Api::_()->getApi('settings', 'core')->poke_title_turncation)) ?> <?php if($send):?>| <?php endif;?>
          </div>
					<?php echo '<a class="poke_remove fright" style="top:5px;" title="' . $this->translate('Do not show this poke') . '" href="javascript:void(0);" onclick="pokeinfo(' . $user->pokeuser_id . ', \'' . $user->user_id. '\');"></a>'; ?>
					<?php if($send):?>
						<div title="<?php echo $this->translate('Poke Back');?>">
						 	<?php   echo $this->htmlLink(array('route' => 'poke_general', 'controller' => 'pokeusers', 'action'=>'pokeuser', 'pokeuser_id' => $user->user_id), $this->translate('Poke Back'), array(
						'class'=>'smoothbox',
								) )
						  ?>
						</div>
					<?php endif;?>
       	</div>
  		<?php endforeach; ?>
		</div>	
	</li>
	<?php else:?>
	  <?php foreach( $this->paginator as $user ): ?>
  	  <?php $div_id++; ?>
  	  	<li class="users_poked_list" id='<?php echo $user->pokeuser_id ?>_poke'>
  	  		<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', '' , array('align'=>'left'))) ?>
	 				<div class="seaocore_sidebar_list_info">
	 					<?php echo '<a class="poke_remove fright"  style="top:7px;" title="' . $this->translate('Do not show this poke') . '" href="javascript:void(0);" onclick="pokeinfo(' . $user->pokeuser_id . ', \'' . $user->user_id. '\');"></a>'; ?>
						<div class="seaocore_sidebar_list_title" title="<?php echo $user->getTitle(). ' ' . $this->translate('poked you on') . ' ' . date("d M Y", $user->created)?>">
					 		<?php echo $this->htmlLink($user->getHref(), Engine_Api::_()->poke()->turncation($user->getTitle(), Engine_Api::_()->getApi('settings', 'core')->poke_title_turncation)) ?> 
        		</div>
        		<div class="seaocore_sidebar_list_details">
        			<?php echo $this->translate('has poked you');?>
        		</div>
						<?php if($send):?>
							<div title="<?php echo $this->translate('Poke Back');?>" class="seaocore_sidebar_list_details">
							 	<?php echo $this->htmlLink(array('route' => 'poke_general', 'controller' => 'pokeusers', 'action'=>'pokeuser', 'pokeuser_id' => $user->user_id), $this->translate('Poke Back'), array(
							'class'=>'smoothbox poke_type_icon',
									) )
							  ?>
							</div>
						<?php endif;?>		  
     			</div>
     		</li>	
		<?php endforeach; ?>
	<?php endif;?>
	<?php  $item = count($this->paginator) ?>
	<input type="hidden" id='count_div' value='<?php echo $item ?>'>
</ul>
<script type="text/javascript">
function closesmoothbox() {
	parent.Smoothbox.close();
}
</script>