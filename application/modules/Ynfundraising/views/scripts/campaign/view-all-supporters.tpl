<?php
$menu = $this->partial('_menu.tpl', array());
echo $menu;
?>
<div class="layout_left">
	<?php echo $this->htmlLink($this->campaign->getHref(), $this->translate("Back to Campaign"),array('class'=>'buttonlink ynFRaising_icon_back'))?>
	<h3>
		<?php echo $this->translate(array("%s Supporter With","%s Supporters With",$this->supporters->getTotalItemCount()),$this->supporters->getTotalItemCount())?>
	</h3>
</div>
<div class="ynfundraising_create_right_menu">
	<div class="quicklinks">
		<ul class="navigation ynfundraising_quicklinks_menu">
			<li>
				<?php echo $this->htmlLink(array('controller'=>'campaign','action'=>'view-all-donors', 'campaignId' => $this->campaign->getIdentity(),'route'=>'ynfundraising_extended'),$this->translate("Donors"))?>
			</li>
			<li>
				<?php echo $this->htmlLink(array('controller'=>'campaign','action'=>'view-all-supporters', 'campaignId' => $this->campaign->getIdentity(),'route'=>'ynfundraising_extended'),$this->translate("Supporters"), array('class'=>'active'))?>
			</li>
		</ul>
	</div>
</div>
<?php echo $this->form->render($this);?>
<?php if(count($this->supporters) > 0):?>
<ul class="ynFRaising_viewGeneralUL ynFRaising_viewAllUL">
<?php foreach( $this->supporters as $supporter ):
  	$user = Engine_Api::_ ()->getItem ( 'user', $supporter->user_id )?>
    <li>
			<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title'=>$user->getTitle(), 'class' => 'ynFRaising_LRH3ULLi_thumb')) ?>
			<div class='ynFRaising_LRH3ULLi_info'>
				<div class='ynFRaising_LRH3ULLi_name'><?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title'=>$user->getTitle())) ?></div>
				<div class='ynFRaising_LRH3ULLi_date'><?php echo $this->translate(array("%s click"," %s clicks",$supporter->click_count), $supporter->click_count);?></div>
			</div>
    </li>
 <?php endforeach; ?>
</ul>
 <?php elseif($this->formValues['name']): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('This campaign does not have any supporters that match your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('This campaign does not have any supporters.');?>
      </span>
    </div>
  <?php endif; ?>
 <?php echo $this->paginationControl($this->supporters, null, null,array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>

<script type="text/javascript">
en4.core.runonce.add(function(){
    if($('name')){
      new OverText($('name'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }
 });

</script>