
<?php 
$db = Engine_Db_Table::getDefaultAdapter();
$select = "SELECT * FROM engine4_core_modules WHERE name = 'ynresponsive1'";
$module = $db->fetchRow($select);

$mobile = 0;
if($module['enabled'])
	$mobile = Engine_Api::_()->ynresponsive1()->isMobile();
?>
<?php if( count($this->photos) >  0  || $this->embedVideo != ''): ?>
<?php if(!$mobile):?>
	<?php	$this->headScript()
	         ->appendFile($this->baseUrl() . '/application/modules/Ynfundraising/externals/scripts/jquery-1.9.1.min.js')
			->appendFile($this->baseUrl() . '/application/modules/Ynfundraising/externals/scripts/slides.min.jquery.js');
	$count = 0;
	?>
	<h4><?php echo $this->campaign->title;?></h4>
	<ul class="ynfundraising_fullsize">
		
		<div class="ynfundraising_div">
		<div id="ynfundraising_gallery_slides">
			<div class="slides_container ynfundraising_gallery_slides_container">
					<?php if($this->embedVideo):?>
						<div class="panel" >
							<div class="wrapper">
								<?php echo $this->embedVideo;?>
							</div>
						</div>
					<?php $count ++; endif;?>
		
					<?php foreach($this->photos as $photo):
						if($count < 8):?> 
					<div class="panel" >
						<div class="wrapper" style="background:url(<?php echo $photo->getPhotoUrl();?>) no-repeat center center;display:block;height:299px;width:500px;">
							 <?php //echo $this->itemPhoto($photo)  ?>
						</div>	
					</div>
					<?php $count ++;  endif; endforeach;?>		
			</div>
		
			<ul class="pagination">
			<?php $count = 0;
			 if($this->campaign->video_url):?>
				<li><a href="#">
					<img class="thumb_icon" src="<?php echo $this->image_embed;?>"/>
				</a></li>
			<?php $count ++; endif;?>
				
				<?php foreach($this->photos as $photo):
					if($count < 8):?> 
					<li><a href="#">
						<?php echo $this->itemPhoto($photo,'thumb.icon')  ?>
					</a></li>
				<?php $count++; endif; endforeach;?>
			</ul>		
			<a href="#" class="prev"><?php $this->translate("Previous") ?></a>
			<a href="#" class="next"><?php $this->translate("Next") ?></a>		
		</div>
		</div>
	</ul>
	
	<script type="text/javascript">
	     jQuery.noConflict();
		 jQuery(document).ready(function(){
			// Set starting slide to 1
			var startSlide = 1;
			// Initialize Slides
			jQuery('#ynfundraising_gallery_slides').slides({
				preload: true,
				effect: 'slide',
				crossfade: true,
				slideSpeed: 350,
				fadeSpeed: 500,			
				//auto: true,
				//play: 5000,
				generatePagination: false,
				// Get the starting slide
			});
		});
	</script>
<?php else:?>	
	<?php 		
			$this->headScript()		
			->appendFile($this->baseUrl() . '/application/modules/Ynfundraising/externals/scripts/slideshow/Navigation.js')
			->appendFile($this->baseUrl() . '/application/modules/Ynfundraising/externals/scripts/slideshow/Loop.js')
			->appendFile($this->baseUrl() . '/application/modules/Ynfundraising/externals/scripts/slideshow/SlideShow.js');	
	 ?>
		<div class="ynfundraising_mobile">
		<section id="ynfundraising_navigation" class="demo">
			<div id="ynfundraising_navigation-slideshow" class="slideshowynfundraising">
				<?php 
				$i = 0;
				if($this->embedVideo): $i++;?>		
					<span id="lp<?php echo $i?>">
						<div class="featured_ynfundraisings">
							<div class="featured_ynfundraisings_img_wrapper">
								<div class="featured_ynfundraisings_img">
									
									<?php echo $this->embedVideo;?>
								</div>
							</div>			
						</div>
					</span>				
				<?php 
				
				endif;?>
				<?php
				
				
				foreach ($this->photos as $item):
				$owner = $item->getOwner();
				
				$i ++;
				?>
				<span id="lp<?php echo $i?>">
					<div class="featured_ynfundraisings">
						<div class="featured_ynfundraisings_img_wrapper">
							<div class="featured_ynfundraisings_img">
								
								<img src="<?php echo $item->getPhotoUrl("thumb.main");?>" />
							</div>
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