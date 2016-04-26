<div class="yn-widget-introduction">
	<div>
		<div class="yn-title"><span><?php echo strip_tags($this -> title)?></span></div>
		<div class="yn-desc"><?php echo strip_tags($this -> content)?></div>
	</div>
	<div class="wrap-info wrap_col3">
		<div class="wrap_col3_left">
			<div class="item responsive">
				<div class="yn-header"><?php if($this -> oneBlock){ echo strip_tags($this -> oneBlock -> title);}?></div>
				<div class="yn-desc"><?php if($this -> oneBlock){ echo $this -> string() -> truncate(strip_tags($this -> oneBlock -> description), 120);}?></div>
				<?php $color = "cacaca"; $background = "application/themes/ynresponsive-metro/images/video_type.png";?>
				<?php if($this -> oneBlock && $this -> oneBlock -> link && $this -> oneBlock -> link != 'transparent')
				{
					$color = $this -> oneBlock -> link;
				}
				if($this -> oneBlock && $this -> oneBlock -> icon && $this -> oneBlock -> icon != '')
				{
					$background = $this -> oneBlock -> icon;
				}?>
				<div class="item-icon top_right" style="background:#<?php echo $color?> url(<?php echo $background?>) no-repeat center center"></div>
			</div>
			<div class="item tablet">
				<div class="yn-header"><?php if($this -> twoBlock){ echo strip_tags($this -> twoBlock -> title);}?></div>
				<div class="yn-desc"><?php if($this -> twoBlock){ echo $this -> string() -> truncate(strip_tags($this -> twoBlock -> description), 120);}?></div>
				<?php $color = "cacaca"; $background = "application/themes/ynresponsive-metro/images/video_type.png";?>
				<?php if($this -> twoBlock && $this -> twoBlock -> link && $this -> twoBlock -> link != 'transparent')
				{
					$color = $this -> twoBlock -> link;
				}
				if($this -> twoBlock && $this -> twoBlock -> icon && $this -> twoBlock -> icon != '')
				{
					$background = $this -> twoBlock -> icon;
				}?>
				<div class="item-icon top_right" style="background:#<?php echo $color?> url(<?php echo $background?>) no-repeat center center"></div>
			</div>
		</div>
		<div class="wrap_col3_right">
			<div class="item multi_user">
				<div class="yn-header"><?php if($this -> threeBlock){ echo strip_tags($this -> threeBlock -> title);}?></div>
				<div class="yn-desc"><?php if($this -> threeBlock){ echo $this -> string() -> truncate(strip_tags($this -> threeBlock -> description), 120);}?></div>
				<?php $color = "cacaca"; $background = "application/themes/ynresponsive-metro/images/video_type.png";?>
				<?php if($this -> threeBlock && $this -> threeBlock -> link && $this -> threeBlock -> link != 'transparent')
				{
					$color = $this -> threeBlock -> link;
				}
				if($this -> threeBlock && $this -> threeBlock -> icon && $this -> threeBlock -> icon != '')
				{
					$background = $this -> threeBlock -> icon;
				}?>
				<div class="item-icon top_left" style="background:#<?php echo $color?> url(<?php echo $background?>) no-repeat center center"></div>
			</div>
			<div class="item social_network">
				<div class="yn-header"><?php if($this -> fourBlock){ echo strip_tags($this -> fourBlock -> title);}?></div>
				<div class="yn-desc"><?php if($this -> fourBlock){ echo $this -> string() -> truncate(strip_tags($this -> fourBlock -> description), 120);}?></div>
				<?php $color = "cacaca"; $background = "application/themes/ynresponsive-metro/images/video_type.png";?>
				<?php if($this -> fourBlock && $this -> fourBlock -> link && $this -> fourBlock -> link != 'transparent')
				{
					$color = $this -> fourBlock -> link;
				}
				if($this -> fourBlock && $this -> fourBlock -> icon && $this -> fourBlock -> icon != '')
				{
					$background = $this -> fourBlock -> icon;
				}?>
				<div class="item-icon top_left" style="background:#<?php echo $color?> url(<?php echo $background?>) no-repeat center center"></div>
			</div>
		</div>
		<div class="wrap_col3_center" <?php if($this -> background_image):?> style = "background: url(<?php echo $this -> background_image?>) no-repeat top center" <?php endif;?>>
			
		</div>
	</div>
</div>