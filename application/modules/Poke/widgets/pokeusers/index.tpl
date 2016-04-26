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

<?php if(empty($this->isajax)):?>
	<?php echo $this->form->render($this); ?>
<?php endif;?>

<?php if($this->paginator->getTotalItemCount() > 0):?>
  <div class="sm-content-list" id="poke_users">	
		<ul class='sm-ui-lists' data-role="listview" data-icon="none" >
			<?php foreach($this->paginator as $value):?>
				<li>
					<div class="ui-btn">
            <?php	echo  $this->itemPhoto(Engine_Api::_()->getItem('user', $value->resourceid), 'thumb.icon') ; ?>
						<h3>
							<a href="<?php echo Engine_Api::_()->getItem('user', $value->resourceid)->getHref(); ?>"><strong><?php echo Engine_Api::_()->getItem('user', $value->resourceid)->getTitle() ?></strong></a>
						</h3>
						<p class="sm-ui-lists-action">
							<?php 
								$user_level = Engine_Api::_()->user()->getViewer()->level_id;
								$send = Engine_Api::_()->authorization()->getPermission($user_level, 'poke', 'send');
							?>
							<?php if($send):?> <!--onclick="pokeback(<?php echo $value->resourceid?>)"-->
								<a class="smoothbox poke_type_icon" href="<?php echo $this->url(array('module'=>'poke', 'controller' => 'pokeusers', 'action' => 'pokeuser', 'pokeuser_id' => $value->resourceid), 'poke_general', true);?>" >
									<?php echo $this->translate('Poke Back');?>
								</a>
								<?php echo $this->translate(' or ');?>
							<?php endif;?>
							<a href="<?php echo $this->url(array('module'=>'poke', 'controller' => 'pokeusers', 'action' => 'cancelpoke', 'pokedelete_id' => $value['pokeuser_id'], 'poke_receiverid' => $value->resourceid), 'poke_general', true);?>" >
								<?php echo $this->translate('ignore this');?>
							</a>
						</p>
					</div>
				</li>
			<?php endforeach;?>
		</ul>
  </div>
	<?php if ($this->paginator->count() > 1): ?>
		<?php
			echo $this->paginationAjaxControl(
						$this->paginator, $this->identity, 'poke_users');
		?>
	<?php endif; ?>
<?php else:?>
		<div class="no-pokes">
			<i class="no_pokes_icon"></i>
			<span><?php echo $this->translate('No Pokes') ?></span>
		</div>	
<?php endif;?>


<script type="text/javascript">
  sm4.core.runonce.add(function() {  
    var url = sm4.core.baseUrl + 'poke/friends/suggest';
    sm4.core.Module.autoCompleter.attach("search", url, {
      'singletextbox': true, 
      'limit':10, 
      'minLength': 1, 
      'showPhoto' : true, 
      'search' : 'search',
      'poke':1
    }, 'toValues');
  }); 
</script>