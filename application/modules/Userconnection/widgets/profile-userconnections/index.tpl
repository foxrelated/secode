<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

if(!isset($this->level_message)){ ?>	

<!-- Sidebar - Vertical tree starting -->
<?php if($this->tree_structure == 5){?>
	<ul>
	<li>
	<div class="userconnection-left-block">
		<h4><?php echo $this->translate("How you're connected to ") . $this->owner; ?></h4>
		<?php foreach( $this->path_information as $path_info ): ?>
			<div class="ucp-sidebar-user-block">
				<div class="user-photo">
					<?php echo $this->htmlLink($path_info->getHref(), $this->itemPhoto($path_info, 'thumb.icon'), array('class' => 'popularmembers_thumb')) ?><br />
					<?php 
						$temp_name = substr(strip_tags($path_info->getTitle()), 0, 10); 
						 if (strlen($path_info->getTitle())>6) $temp_name .= "..";
						echo $this->htmlLink($path_info->getHref(), $temp_name, array("title" => $path_info->getTitle())).'';
						
					?>
				</div>
				<div class="ucp-userfriendship-Vertical">
				<?php if($path_info->user_id != $this->user_id){ ?>
	      	<a style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);" class="buttonlink" href="<?php echo $this->base_url; ?>/messages/compose/to/<?php echo $path_info->user_id ?>" title="<?php echo $this->translate('Send Message'); ?>"><?php echo $this->translate('Send Message'); ?></a>
					<?php } echo $this->userFriendship($path_info)?>
					<?php if($path_info->user_id != $this->owner_id) ?>
					<?php
						if($path_info->user_id == $this->user_id){
						switch ($this->indicators_color){
							case 5:echo "<div class='indicatorsYellow first'>" . $this->translate("Y<span>ou</span>") . "</div>";break;
							case 6:echo "<div class='indicatorsGreen first'>" . $this->translate("Y<span>ou</span>") . "</div>";break;
							case 7:echo "<div class='indicatorsBrown first'>" . $this->translate("Y<span>ou</span>") . "</div>";break;
							case 8:echo "<div class='indicatorsBlue first'>" . $this->translate("Y<span>ou</span>") . "</div>";break;
						}
					
					}
					if($path_info->user_id == $this->owner_id){
					// Indicator for owner 
						switch ($this->indicators_color){
							case 5:echo "<div class='indicatorsYellow'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
							case 6:echo "<div class='indicatorsGreen'>&nbsp;$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
							case 7:echo "<div class='indicatorsBrown'>&nbsp;$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
							case 8:echo "<div class='indicatorsBlue'>&nbsp;$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
						}
					}
					?>
				</div>
			</div>
			<?php 
				if($path_info->user_id != $this->owner_id){ ?>
					<div class="ucp-sidebar-downarrow userconnection_player_button_launch_wrapper" rel="{content:'focus_tooltip_<?php echo $path_info->user_id;  ?>'}">
			<?php
      $friend_tooltip = '';
      if(!empty($this->userconnection_friend_type[$path_info->user_id]))
        $friend_tooltip = @implode(",", $this->userconnection_friend_type[$path_info->user_id]);
			// Check if tooltip is not empty then we show the dot (..).
			if(!empty($friend_tooltip))
			{
				$friend_dot = '..';
			}
			else {
				$friend_dot = '';
			}
			if(!empty($friend_tooltip))
			{ 
				echo "<div class='userconnection_player_button_launch_tooltip'> $friend_tooltip </div>";
			}?>
			<?php 
						switch ($this->arrow_color){
							case 5:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-down-yellow.gif', '', array('class' => 'icon')). substr(strip_tags($friend_tooltip), 0, 8) . $friend_dot;break;
							case 6:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-down-green.gif', '', array('class' => 'icon')). substr(strip_tags($friend_tooltip), 0, 8) . $friend_dot;break;
							case 7:echo $this->htmlImage( $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-down-orange.gif', '', array('class' => 'icon')). substr(strip_tags($friend_tooltip), 0, 8) . $friend_dot;break;
							case 8:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-down-blue.gif', '', array('class' => 'icon')). substr(strip_tags($friend_tooltip), 0, 8) . $friend_dot;break;
			} ?>
			</div>
			<?php	}	?>
		<?php endforeach;?>
	</div>
	</li>
	</ul>


	
	
	

<?php }elseif ($this->tree_structure==6){?>
	<ul>
	<li>
	<div class="userconnection-left-block">
	<h4><?php echo $this->translate("How you're connected to ") . $this->owner; ?></h4>
	<div style="text-align:center;" align="center">
		<?php foreach( $this->path_information as $path_info ): ?>
	
		<?php
			if($path_info->user_id == $this->user_id){
				
				switch ($this->indicators_color){
					case 5:echo "<div class='indicatorsYellow indicators'>" . $this->translate("Y<spn>ou</span>") . "</div>";break;
					case 6:echo "<div class='indicatorsGreen indicators'>" . $this->translate("Y<spn>ou</span>") . "</div>";break;
					case 7:echo "<div class='indicatorsBrown indicators'>" . $this->translate("Y<spn>ou</span>") . "</div>";break;
					case 8:echo "<div class='indicatorsBlue indicators'>" . $this->translate("Y<spn>ou</span>") . "</div>";break;
				}
			
			}
		?>

		<div class="username-sidebar">
			<?php echo $this->htmlLink($path_info->getHref(), $path_info->getTitle())?>
		</div>
			<?php 
				if($path_info->user_id != $this->owner_id){ ?>
					<div class="ucp-sidebar-downarrow2 userconnection_player_button_launch_wrapper" rel="{content:'focus_tooltip_<?php echo $path_info->user_id;  ?>'}">
			<?php
      if(!empty($this->userconnection_friend_type[$path_info->user_id]))
        $friend_tooltip = @implode(",", $this->userconnection_friend_type[$path_info->user_id]);
			// Check if tooltip is not empty then we show the dot (..).
			if(!empty($friend_tooltip))
			{
				$friend_dot = '..';
			}
			else {
				$friend_dot = '';
			}
			if(!empty($friend_tooltip))
			{ 
				echo "<div class='userconnection_player_button_launch_tooltip'> $friend_tooltip </div>";
			}?>
					<?php 
					switch ($this->arrow_color){
						case 5:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-down-yellow.gif', '', array('class' => 'icon')). substr(strip_tags($friend_tooltip), 0, 6) . $friend_dot ;break;
						case 6:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-down-green.gif', '', array('class' => 'icon')). substr(strip_tags($friend_tooltip), 0, 6) . $friend_dot;break;
						case 7:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-down-orange.gif', '', array('class' => 'icon')). substr(strip_tags($friend_tooltip), 0, 6) . $friend_dot;break;
						case 8:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-down-blue.gif', '', array('class' => 'icon')). substr(strip_tags($friend_tooltip), 0, 6) . $friend_dot;break;
					}	?>
					</div>
				<?php } ?>

		<?php 
		// Indicator for owner
		if($path_info->user_id == $this->owner_id){
			
			switch ($this->indicators_color){
				case 5:echo "<div class='indicatorsYellow indicators'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
				case 6:echo "<div class='indicatorsGreen indicators'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
				case 7:echo "<div class='indicatorsBrown indicators'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
				case 8:echo "<div class='indicatorsBlue indicators'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
			}	
		} ?>
		<?php endforeach;?>
		</div>
	</div>
	</li>
	</ul>


<!-- END OF Sidebar - Vertical with out Image. TREE STRUCTURE  -->
<!-- STARTING OF Horizontal. TREE STRUCTURE  -->
	<?php  } elseif ($this->tree_structure==7) {?>
	<ul>
	<li>
	<div class="userconnection-left-block">
		<h4><?php echo $this->translate("How you're connected to ") . $this->owner; ?></h4>
		<?php foreach( $this->path_information as $path_info ): ?>
		<div class="ucp-sidebar-users" align="center">
			<!-- Give Indicator color by admin -->
			<?php
				echo $this->htmlLink($path_info->getHref(), $this->itemPhoto($path_info, 'thumb.icon'), array('class' => 'popularmembers_thumb')).'<br />';
				 $temp_name = substr(strip_tags($path_info->getTitle()), 0, 10); 
				 if (strlen($path_info->getTitle())>6) $temp_name .= "..";
				echo $this->htmlLink($path_info->getHref(), $temp_name, array("title" => $path_info->getTitle())).'';
	
		?><br>
				<div class="ucp-userfriendship">
					<?php	if($path_info->user_id == $this->owner_id){ ?>
					<div style="float:left;display:inline;font-size:0px;width:37px;"> 
					<?php }else{ ?>
					<div align="center" style="text-align:center;"> 
					<?php } if($path_info->user_id != $this->user_id){?> 
	      	<a style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);" class="buttonlink" href="<?php echo $this->base_url; ?>/messages/compose/to/<?php echo $path_info->user_id ?>" title="<?php echo $this->translate('Send Message'); ?>"><?php echo $this->translate('Send Message'); ?></a>
						<?php } echo $this->userFriendship($path_info)?>
					</div>
					
						<?php	if($path_info->user_id == $this->owner_id){ ?>
							<div class="indicators-last">
						<?php
							// Indicator for owner 
							switch ($this->indicators_color){
								case 5:echo "<div class='indicatorsYellow'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
								case 6:echo "<div class='indicatorsGreen'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
								case 7:echo "<div class='indicatorsBrown'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
								case 8:echo "<div class='indicatorsBlue'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
							} ?>
							</div>		
						<?php }	?>
					
				</div>
				
				<?php
	
				if($path_info->user_id == $this->user_id){ ?>
				<div class="indicators">
				<?php	
					switch ($this->indicators_color){
						case 5:echo "<div class='indicatorsYellow'>" . $this->translate("Y<span>ou</span>") . "</div>";break;
						case 6:echo "<div class='indicatorsGreen'>" . $this->translate("Y<span>ou</span>") . "</div>";break;
						case 7:echo "<div class='indicatorsBrown'>" .$this->translate("Y<span>ou</span>") . "</div>";break;
						case 8:echo "<div class='indicatorsBlue'>" . $this->translate("Y<span>ou</span>") . "</div>";break;
					} ?>
				</div>
				<?php } ?>
				
		<!-- Arrow image selecter in tree -->
	
		</div>
		
		<?php
			if($path_info->user_id != $this->owner_id){ ?>
				<div class="ucp-sidebar-rightarrow userconnection_player_button_launch_wrapper" rel="{content:'focus_tooltip_<?php echo $path_info->user_id;  ?>'}">
				<?php
        if(!empty($this->userconnection_friend_type[$path_info->user_id]))
          $friend_tooltip = @implode(",", $this->userconnection_friend_type[$path_info->user_id]);
				// Check if tooltip is not empty then we show the dot (..).
				if(!empty($friend_tooltip))
				{
					$friend_dot = '..';
				}
				else {
					$friend_dot = '';
				}
				if(!empty($friend_tooltip))
				{ 
					echo "<div class='userconnection_player_button_launch_tooltip'> $friend_tooltip </div>";
				}?>
			<?php 
						switch ($this->arrow_color){
							case 5:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-yellow.gif', '', array('class' => 'icon')) . '<br>' . substr(strip_tags($friend_tooltip), 0, 3) . $friend_dot;break;
							case 6:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-green.gif', '', array('class' => 'icon')) . '<br>' . substr(strip_tags($friend_tooltip), 0, 3) . $friend_dot;break;
							case 7:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-orange.gif', '', array('class' => 'icon')) . '<br>' . substr(strip_tags($friend_tooltip), 0, 3) . $friend_dot;break;
							case 8:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-blue.gif', '', array('class' => 'icon')) . '<br>' . substr(strip_tags($friend_tooltip), 0, 3) . $friend_dot;break;
			}	?>
			</div>
		  <?php }	?>
		<?php endforeach;?>
	</div>
	</li>
	</ul>
<!-- END OF Horizontal. TREE STRUCTURE  -->


<!-- STARTING OF Profile Tab STRUCTURE done  -->
<?php } elseif ($this->tree_structure==8) {?>
<h4><?php echo $this->translate("How you're connected to ") . $this->owner; ?></h4>
	<div class="userconnection-right-block">
		<?php foreach( $this->path_information as $path_info ):?>
		<div class="ucp-users" align="center">
			<!-- Give Indicator color by admin -->
			<?php
				echo $this->htmlLink($path_info->getHref(), $this->itemPhoto($path_info, 'thumb.icon'), array('class' => 'popularmembers_thumb')).'<br />';				
					$temp_name = substr(strip_tags($path_info->getTitle()), 0, 10); 
					 if (strlen($path_info->getTitle())>6) $temp_name .= "..";
					echo $this->htmlLink($path_info->getHref(), $temp_name, array("title" => $path_info->getTitle())).'';
		?><br>
				<div class="ucp-userfriendship">
					<?php	if($path_info->user_id == $this->owner_id){ ?>
					<div style="float:left;display:inline;font-size:0px;width:37px;margin:0;padding:0;"> 
					<?php }else{ ?>
					<div align="center" style="text-align:center;"> 
					<?php } if($path_info->user_id != $this->user_id){?> 
	      	<a style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);" class="buttonlink" href="<?php echo $this->base_url; ?>/messages/compose/to/<?php echo $path_info->user_id ?>" title="<?php echo $this->translate('Send Message'); ?>">/a>
						<?php } echo $this->userFriendship($path_info)?>
					</div>
					
						<?php if($path_info->user_id == $this->owner_id){ ?>
						<div class="indicators-last">
						<?php
						// Indicator for owner 
							switch ($this->indicators_color){
								case 5:echo "<div class='indicatorsYellow'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
								case 6:echo "<div class='indicatorsGreen'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
								case 7:echo "<div class='indicatorsBrown'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
								case 8:echo "<div class='indicatorsBlue'>$this->userconnection_depth<span>$this->userconnection_depth_extention</span></div>";break;
							} ?>
						</div>		
					<?php }	?>
				</div>
				
				<?php	if($path_info->user_id == $this->user_id){ ?>
				<div class="indicators">
					<?php
					switch ($this->indicators_color){
						case 5:echo "<div class='indicatorsYellow'>" . $this->translate("Y<span>ou</span>") . "</div>";break;
						case 6:echo "<div class='indicatorsGreen'>" . $this->translate("Y<span>ou</span>") . "</div>";break;
						case 7:echo "<div class='indicatorsBrown'>" . $this->translate("Y<span>ou</span>") . "</div>";break;
						case 8:echo "<div class='indicatorsBlue'>" . $this->translate("Y<span>ou</span>") . "</div>";break;
					} ?>
					</div>
					<?php	} ?>
				
		<!-- Arrow image selecter in tree -->
	
		</div>
		
		<?php
		if($path_info->user_id != $this->owner_id){ ?>
			<div class="ucp-rightarrow userconnection_player_button_launch_wrapper" rel="{content:'focus_tooltip_<?php echo $path_info->user_id;  ?>'}">
			<?php
      if(!empty($this->userconnection_friend_type[$path_info->user_id]))
        $friend_tooltip = @implode(",", $this->userconnection_friend_type[$path_info->user_id]);
			// Check if tooltip is not empty then we show the dot (..).
			if(!empty($friend_tooltip))
			{
				$friend_dot = '..';
			}
			else {
				$friend_dot = '';
			}
			if(!empty($friend_tooltip))
			{ 
				echo "<div class='userconnection_player_button_launch_tooltip'> $friend_tooltip </div>";
			}?>
			<?php 
						switch ($this->arrow_color){
							case 5:echo $this->htmlImage( $this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-yellow.gif', '', array('class' => 'icon')) . '<br>' . substr(strip_tags($friend_tooltip), 0, 3) . $friend_dot;break;
							case 6:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-green.gif', '', array('class' => 'icon')) . '<br>' . substr(strip_tags($friend_tooltip), 0, 3) . $friend_dot;break;
							case 7:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-orange.gif', '', array('class' => 'icon')) . '<br>' . substr(strip_tags($friend_tooltip), 0, 3) . $friend_dot;break;
							case 8:echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Userconnection/externals/images/icon/userconnection-arrow-blue.gif', '', array('class' => 'icon')) . '<br>' . substr(strip_tags($friend_tooltip), 0, 3) . $friend_dot;break;
			}	?>
			</div>
			<?php }	?>
		<?php endforeach;?>
		<div style="clear:both;"></div>
	</div>
<?php }  ?>
<?php }else{ echo $this->level_message; }  ?>
