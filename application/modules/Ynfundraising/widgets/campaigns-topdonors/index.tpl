<ul class="ynFRaising_LRH3ULLi">
  <?php foreach( $this->donors as $donor ):
  	$user = Engine_Api::_ ()->getItem ( 'user', $donor->user_id);
  	$total = $donor->total_amount;?>
    <li>
		<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title'=>$user->getTitle(), 'class' => 'ynFRaising_LRH3ULLi_thumb')) ?>
		<div class='ynFRaising_LRH3ULLi_info'>
			<div class='ynFRaising_LRH3ULLi_name'><?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title'=>$user->getTitle())) ?></div>
			<div class='ynFRaising_LRH3ULLi_date'><?php echo $this->translate(array("Donated in %s campaign", "Donated in %s campaigns", $total), $total);?></div>
		</div>
    </li>
  <?php endforeach; ?>
	<div class="ynFRaising_rightColViewAll ynFRaising_viewAll">
		<?php echo $this->htmlLink(array('controller'=>'campaign','action'=>'view-all-donors', 'route'=>'ynfundraising_extended'), "<span>&rsaquo;</span>".$this->translate("View All"))?>
	</div>  
</ul>