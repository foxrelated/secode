<?php 
$mobile = 0;
if(Engine_Api::_() -> hasModuleBootstrap('ynresponsive1'))
	$mobile = Engine_Api::_()->ynresponsive1()->isMobile();

?>
<?php if( $this->campaigns->getTotalItemCount() >  0): ?>
<?php if(!$mobile):?>
	<ul class="ynfundraising_slideshow_container" id ="ynfundraising_slideshow_container">
	 <li class="ynFRaising_EachSlide">
	 	<div id ="slide-runner-widget" class='slideshow'>
		<?php
		 foreach($this->campaigns as $item):
		 $goal =  $item->goal;
		 $amount =  $item->total_amount?$item->total_amount:'0';
		 $percent  = ($goal!=0)?round(($amount*100)/$goal,2):'0';
		 $percent = ($percent>100)?100:$percent;
		 $values = array("campaign" => $item->getIdentity(), 'limit' => 3);
		 $donors = Engine_Api::_()->getApi('core', 'ynfundraising')->getDonorPaginator($values);
		 ?>
		 <div class="slide featured_campaign">
			<div class="ynfundraising_campaign_photo ynFRaising_FeatureGenneral">
				 <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item,"img.main")) ?>
				 <div class="ynfundraising_campaign_featured_info">
					<div class="ynfundraising_browse_info_title">
						<h3> <?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->getTitle(), 60), array('title' => $this->string()->stripTags($item->getTitle()))) ?></h3>
					</div>
					<p class="ynfundraising_browse_info_owner">
						<?php echo $this->translate("Created by %s",$item->getOwner());?>
					</p>
					<div class="ynfundraising_browse_info_donate_mobile ynfundraising_browse_info_owner">
						<p> <?php echo $this->translate("Fund: %s | Goal: %s | Process: %s ", $this->currencyfund($item->total_amount?$item->total_amount:'0',$item->currency), $this->currencyfund($item->goal, $item->currency),$percent.'%' )?></p>
						<p><?php echo $this->translate(array('%s donor','%s donors',$item->getTotalDonors()),$item->getTotalDonors() );
							echo " - ".$this->translate(array('%s like','%s likes', $item->like_count), $item->like_count);
							echo " - ".$this->translate(array('%s view','%s views',$item->view_count),$item->view_count);
						?>
						</p>
					</div>
					<p class="ynfundraising_browse_info_blurb">
						<?php echo $this->string()->truncate($this->string()->stripTags($item->short_description), 150);?>
					</p>
				 </div>
			</div>
			<div class="ynFRaising_campaign_FeaturePrice ynFRaising_FeatureGenneral">
				<div class="ynFRaising_FeaturePriceRaised">
					<div class="ynFRaising_FeaturePrice">
						<?php echo  $this->currencyfund($item->total_amount?$item->total_amount:'0',$item->currency)?>
					</div>
					<div class="ynFRaising_FeatureRaisedOf">
						<?php echo $this->translate("Raised of %s Goal",$this->currencyfund($item->goal, $item->currency))?>
					</div>
				</div>
				<div class="ynfundraising-highligh-detail">
					<div class="meter-wrap-l">
						<div class="meter-wrap-r">
							<div class="meter-wrap">
								<div class="meter-value" style="width: <?php echo ($percent/100)*170?>px">
									<?php echo $percent."%"; ?>
								</div>
							</div>
						</div>
					</div>
			   </div>
			   <?php if($item->expiry_date && $item->expiry_date != "0000-00-00 00:00:00" && $item->expiry_date != "1970-01-01 00:00:00" && $item->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS): ?>
					<div class="ynfundraising-time">
						<img src="" class="ynfundraising_timeClockIcon"/>
						<span class="ynfundraising_timeInner"><?php echo $item->getLimited();?></span>
					</div>
				<?php endif;?>
	
				<div class="ynfundraising-info">
					<?php echo $this->translate(array('%s donor','%s donors',$item->getTotalDonors()),$item->getTotalDonors() );
						echo " - ".$this->translate(array('%s like','%s likes', $item->like_count), $item->like_count);
						echo " - ".$this->translate(array('%s view','%s views',$item->view_count),$item->view_count);
					?>
				</div>
				<?php if(count($donors)):?>
				<div class="ynfundraising_donors">
					<div class="ynFRaising_DonorsLabel"> <?php echo $this->translate("Thank you, Donors");?> </div>
					<div class="ynFRaising_thumbavatarDonors">
					<?php
					foreach( $donors as $donor ):
						if($donor->user_id > 0):
							$user = Engine_Api::_ ()->getItem ( 'user', $donor->user_id )?>
							<?php if(Engine_Api::_()->getApi('core', 'ynfundraising')->getLatestAnonymous($donor->user_id, $item->getIdentity())->is_anonymous == 0):?>
								<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title'=>$user->getTitle(),'target'=> '_blank')) ?>
							<?php else: ?>
								<a href="javascript:void(0);" >
									<img src="./application/modules/User/externals/images/nophoto_user_thumb_icon.png"
										class="thumb_icon item_photo_user item_nophoto"
										title='<?php echo $this->translate("Anonymous")?>'>
								</a>
							<?php endif; ?>
						<?php else: ?>
							<?php
							$title = $this->translate("Anonymous");
							if(Engine_Api::_()->getApi('core', 'ynfundraising')->getGuestAnonymous($donor->guest_name, $donor->guest_email, $item->getIdentity())->is_anonymous == 0):
								$title = ($donor->guest_name == "")?$this->translate("Guest"):$donor->guest_name;
							?>
							<?php endif;?>
							<a href="javascript:void(0);" >
								<img src="./application/modules/User/externals/images/nophoto_user_thumb_icon.png"
									class="thumb_icon item_photo_user item_nophoto"
									title='<?php echo $title ?>' >
							</a>
						<?php endif; ?>
					<?php endforeach; ?>
					</div>
				</div>
				<?php endif;?>
			</div>
		 </div>
		<?php endforeach;
		?>
		</div>
	  </li>
	</ul>
	 <script type="text/javascript">
	    jQuery(document).ready(function(){
	    	var list_middle = $$('.layout_main .layout_middle');
	    	var position = list_middle[0].getCoordinates();
	        var slideWidth = position.width;
	        /* call divSlideShow without parameters */
	        jQuery('.slideshow').divSlideShow({
	        width: slideWidth,
	        height:290,
	        loop:4000,
	        arrow:'begin',
	        controlClass:'slideshow_action',
	        controlActiveClass:'slideshow_action_active'
	        });
	    });
	</script>
<?php else:?>	
	<?php 		
			$this->headScript()		
			->appendFile($this->baseUrl() . '/application/modules/Ynfundraising/externals/scripts/slideshow/Navigation2.js')
			->appendFile($this->baseUrl() . '/application/modules/Ynfundraising/externals/scripts/slideshow/Loop.js')
			->appendFile($this->baseUrl() . '/application/modules/Ynfundraising/externals/scripts/slideshow/SlideShow.js');	
	 ?>
		<div class="ynfundraising_mobile">
		<section id="ynfundraising_navigation" class="demo">
			<div id="ynfundraising_navigation-slideshow" class="slideshowynfundraising">
				<?php 
				$i = 0;				
				foreach ($this->campaigns as $item):
				$owner = $item->getOwner();
				$goal =  $item->goal;
				 $amount =  $item->total_amount?$item->total_amount:'0';
				 $percent  = ($goal!=0)?round(($amount*100)/$goal,2):'0';
				 $percent = ($percent>100)?100:$percent;
				$i ++;
				?>
				<span id="lp<?php echo $i?>">
					<div class="featured_ynfundraisings">
						<div class="featured_ynfundraisings_img_wrapper">
							<div class="featured_ynfundraisings_img">
								<a href="<?php echo $item->getHref()?>"> 									
									<img src="<?php echo $item->getPhotoUrl("thumb.main");?>" /> 
								</a>
							</div>
						</div>
						<div class="ynfundraising_info">
							<div class="ynfundraising_title" style="font-size: 15px; color: #3BA3D0">
								<b><?php echo $item ?> </b>
							</div>
							<div class="ynfundraising_owner" style="font-size: 11px; color: #7E7E7E;">
								<?php echo $this->translate("Created by");?>
		
								<?php echo $this->htmlLink($owner->getHref(),$owner->getTitle());?>												
							</div>
							<div class="ynfundraising_browse_info_donate_mobile ynfundraising_browse_info_owner">
								<p> <?php echo $this->translate("Fund: %s | Goal: %s | Process: %s ", $this->currencyfund($item->total_amount?$item->total_amount:'0',$item->currency), $this->currencyfund($item->goal, $item->currency),$percent.'%' )?></p>
								<p><?php echo $this->translate(array('%s donor','%s donors',$item->getTotalDonors()),$item->getTotalDonors() );
									echo " - ".$this->translate(array('%s like','%s likes', $item->like_count), $item->like_count);
									echo " - ".$this->translate(array('%s view','%s views',$item->view_count),$item->view_count);
								?>
								</p>
							</div>
							<p class="ynfundraising_description">
								<?php echo $this->string()->truncate($this->string()->stripTags($item->short_description), 150);?>
							</p>
							
		
						</div>
					</div>
				</span>
				<?php  endforeach; ?>
				<ul class="ynfundraising_pagination" id="ynfundraising_pagination">
					<li><a class="current" href="#lp1"></a></li>
					<?php for ($j = 2; $j <= $i; $j ++):?>
					<li><a href="#lp<?php echo $j?>"></a></li>
					<?php endfor;?>
				</ul>
			</div>
		</section>
		</div> 
		<?php endif;?>
<?php endif;?>