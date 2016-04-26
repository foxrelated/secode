<?php
$campaign = Engine_Api::_()->getItem('ynfundraising_campaign', $this->campaign_id);
?>
<div class="quicklinks">
	<ul class="navigation ynfundraising_quicklinks_menu">
		<li>
			<a href="<?php echo $this->url(array("action"=>"edit-step-one","campaignId"=>$this->campaign_id), "ynfundraising_general") ?>" class="<?php echo $this->active_menu=="step01"?"active":"" ?> "><?php echo $this->translate("Main Information") ?></a>
		</li>
		
		<li>
			<a href="<?php echo $this->url(array("action"=>"create-step-two","campaignId"=>$this->campaign_id), "ynfundraising_general") ?>" class="<?php echo $this->active_menu=="step02"?"active":"" ?> "><?php echo $this->translate("Gallery") ?></a>
		</li>
		<!--
		<li>
			<a href="<?php echo $this->url(array("action"=>"create-step-three","campaignId"=>$this->campaign_id), "ynfundraising_general") ?>" class="<?php echo $this->active_menu=="step03"?"active":"" ?> "><?php echo $this->translate("Sponsor Levels") ?></a>
		</li>
		-->
		<li>
			<a href="<?php echo $this->url(array("action"=>"create-step-four","campaignId"=>$this->campaign_id), "ynfundraising_general") ?>" class="<?php echo $this->active_menu=="step04"?"active":"" ?> "><?php echo $this->translate("Contact Information") ?></a>
		</li>
		
		<li>
			<a href="<?php echo $this->url(array("action"=>"create-step-five","campaignId"=>$this->campaign_id), "ynfundraising_general") ?>" class="<?php echo $this->active_menu=="step05"?"active":"" ?> "><?php echo $this->translate("Email Message and Conditions") ?></a>
		</li>
		
		<li>
			<a href="<?php echo $this->url(array("action"=>"create-step-six","campaignId"=>$this->campaign_id), "ynfundraising_general") ?>" class="<?php echo $this->active_menu=="step06"?"active":"" ?> "><?php echo $this->translate("Invite Friends") ?></a>
		</li>
		<?php if($campaign->published == 0):?>
		<li>
			<a href="<?php echo $this->url(array("action"=>"create-step-seven","campaignId"=>$this->campaign_id), "ynfundraising_general") ?>" class="<?php echo $this->active_menu=="step07"?"active":"" ?> "><?php echo $this->translate("Publish") ?></a>
		</li>
		<?php endif;?>
	</ul>
</div>