<ul class="ynFRaising_LRH3ULLi">
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
	<div class="ynFRaising_rightColViewAll ynFRaising_viewAll">
  	<?php echo $this->htmlLink(array('controller'=>'campaign','action'=>'view-all-supporters', 'campaignId' => $this->campaign->getIdentity(),'route'=>'ynfundraising_extended'), "<span>&rsaquo;</span>".$this->translate("View All"))?>
	</div>
</ul>