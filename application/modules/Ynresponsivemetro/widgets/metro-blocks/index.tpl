<div class="yn-widget-metro-blocks" <?php if($this ->background_image):?> style = "background:url(<?php echo $this -> background_image?>) repeat center center"<?php endif;?>>
	<div>
	<div class="yn-colums">
		<?php if($this -> videoBlock):?>
			<?php if($this -> hasVideo):
				$photoUrl = $this -> videoBlock -> getPhotoUrl("thumb.normal");
				if(!$photoUrl)
				{
					$photoUrl = $this->baseUrl()."/application/modules/Video/externals/images/video.png";
				}?>
			<div class="yn-video">
				<div class="cover-img" style="background-image:url(<?php echo $photoUrl?>)"><a href="<?php echo $this -> videoBlock->getHref()?>"></a></div>
				<div class="yn-info">
					<div class="yn-title">
						<a href="<?php echo $this -> videoBlock->getHref()?>"><?php echo $this -> videoBlock -> title?></a>
					</div>
					<div class="yn-post-by"><?php echo $this -> translate("Posted by %s", $this->htmlLink($this -> videoBlock->getOwner()->getHref(), $this -> videoBlock->getOwner()->getTitle()));?></div>
					<div class="yn-post-time">
						<?php $start_time = strtotime($this -> videoBlock -> creation_date);
						$oldTz = date_default_timezone_get();
						if($this->viewer() && $this->viewer()->getIdentity())
						{
							date_default_timezone_set($this -> viewer() -> timezone);
						}
						else 
						{
							date_default_timezone_set( $this->locale() -> getTimezone());
						}
						echo $this -> translate("on %s",date("F j, Y", $start_time));
		                date_default_timezone_set($oldTz); ?>
					</div>
					<div class="rating">
						<ul>
							<?php for ($x = 1; $x <= $this->videoBlock->rating; $x++): ?>
						        <li class="ynres_metro_rating_star_generic rating_star"></li>
						    <?php endfor; ?>
						    <?php if ((round($this->videoBlock->rating) - $this->videoBlock->rating) > 0): $x ++; ?>
						        <li class="ynres_metro_rating_star_generic rating_star_half"></li>
						    <?php endif; ?>
						    <?php if ($x <= 5) :?>
						        <?php for (; $x <= 5; $x++ ) : ?>
						            <li class="ynres_metro_rating_star_generic rating_star_disabled"></li>
						        <?php endfor; ?>
						    <?php endif; ?>
						</ul>
					</div>
					<div class="views"><?php echo $this -> translate("%s views", $this->videoBlock -> view_count)?></div>
				</div>
			</div>
			<?php else: ?>
				<div class="yn-poll wrap_col3">
					<div class="wrap_col3_left cover-img" style="background-image:url(<?php echo $this -> videoBlock -> getPhotoUrl("thumb.normal", 1)?>)"><a href="<?php echo $this -> videoBlock -> link;?>"></a></div>
					<div class="wrap_col3_center">
						<div class=""><img src="<?php echo $this->videoBlock -> icon?>"/></div>
						<div class="yn-title"><a href="<?php echo $this -> videoBlock -> link;?>"><?php echo $this -> videoBlock -> title?></a></div>
						<div class="yn-post-desc"><?php echo $this -> string() -> truncate(strip_tags($this -> videoBlock -> description), 42);?></div>
					</div>
				</div>
			<?php endif;?>	
		<?php else: ?>
			<!-- show block default-->
			<div class="yn-video blank">
				<div class="cover-img" style="background:#1dacd6 url(application/themes/ynresponsive-metro/images/yn_blank.png) no-repeat center center"></div>
				<div class="yn-info">
					<div><i class="yn-icon yn-question"></i></div>
					<div><?php echo $this -> translate("Add widget...")?></div>
				</div>
			</div>
		<?php endif;?>	
		<?php if($this -> eventBlock):?>
			<?php if($this -> hasEvent):
				$start_time = strtotime($this -> eventBlock -> starttime);
				$oldTz = date_default_timezone_get();
				if($this->viewer() && $this->viewer()->getIdentity())
				{
					date_default_timezone_set($this -> viewer() -> timezone);
				}
				else 
				{
					date_default_timezone_set( $this->locale() -> getTimezone());
				}?>
			<div class="yn-event">
				<div class="header wrap_col3">
					<div class="wrap_col3_left">
						<div>
							<div><?php echo date("j", $start_time)?></div>
							<div><?php echo date("M", $start_time)?></div>
						</div>
					</div>
					<?php date_default_timezone_set($oldTz);?>
					<div class="wrap_col3_center">
						<div class="info">
							<div class="yn-title">
								<a href="<?php echo $this -> eventBlock->getHref()?>"><?php echo $this -> eventBlock -> getTitle()?></a>
							</div>
							<div class="yn-post-by"><?php echo $this -> translate("Host")?> <span><?php echo $this -> translate("by %s", $this->htmlLink($this -> eventBlock->getOwner()->getHref(), $this -> eventBlock->getOwner()->getTitle()))?></span></div>
						</div>
						<div class="yn-action">
							<div>
							<div><?php echo $this -> eventBlock -> member_count?><i class="yn-icon yn-user"></i></div>
							<div><?php echo $this -> eventBlock -> view_count?><i class="yn-icon yn-view"></i></div>
							<?php if(Engine_Api::_() -> hasModuleBootstrap("ynevent")):?>
								<div><?php echo $this -> eventBlock -> likes() -> getLikePaginator() -> getTotalItemCount()?><i class="yn-icon yn-like"></i></div>
								<div><?php echo $this -> eventBlock -> rating?><i class="yn-icon yn-rating"></i></div>
							<?php endif;?>
							</div>
						</div>
					</div>
				</div>
					<?php $photoUrl = $this -> eventBlock -> getPhotoUrl("thumb.main");
						if(!$photoUrl)
						{
							$photoUrl = $this->baseUrl()."/application/modules/Event/externals/images/nophoto_event_thumb_profile.png";
					}?>
					<div class="footer cover-img" style="background-image:url(<?php echo $photoUrl?>)"><a href="<?php echo $this -> eventBlock->getHref()?>"></a></div>
			</div>
			<?php else: ?>
			<div class="yn-other">
				<div class="header">
					<div class="yn-title"><a href="<?php echo $this -> eventBlock -> link;?>"></a><?php echo $this -> eventBlock -> title?></a></div>
					<div class="yn-desc"><?php echo $this -> string() -> truncate(strip_tags($this -> eventBlock -> description), 42)?></div>
				</div>
				<a href="<?php echo $this -> eventBlock -> link;?>">
					<div class="footer cover-img" style="background-image:url(<?php echo $this -> eventBlock -> getPhotoUrl("thumb.profile", 2)?>)"></div>
				</a>
			</div>
			<?php endif;?>	
		<?php else: ?>
			<!-- show block default-->
			<div class="yn-event blank">
				<div class="header">
					<div><?php echo $this -> translate("Add widget...")?></div>
				</div>
				<div class="footer" style="background:#cbd1d5 url(application/themes/ynresponsive-metro/images/photo_blank.png) no-repeat center center"></div>
			</div>
		<?php endif;?>
	</div>
	<div class="yn-colums">
		<div class="yn-photo">
			<ul class="bxslider" id="bxslider">
			<?php if($this -> photosBlock && count($this -> photosBlock)):?>
				<?php foreach($this -> photosBlock as $item):?>
					<li><div class="cover-img" style="background-image:url(<?php echo $item -> getPhotoUrl("thumb.main", 3)?>)"></div></li>
				<?php endforeach;?>
			</ul>
			<?php else:?>
				<li><div class="cover-img" style="background-image:url(http://lorempixel.com/344/319?1)"></div></li>
				<li><div class="cover-img" style="background-image:url(http://lorempixel.com/344/319?2)"></div></li>
				<li><div class="cover-img" style="background-image:url(http://lorempixel.com/344/319?3)"></div></li>
			<?php endif;?>
			</ul>
		</div>
		<?php if($this -> groupBlock):?>
			<?php if($this -> hasGroup):?>
				<div class="yn-group wrap_col3">
					<?php $photoUrl = $this -> groupBlock -> getPhotoUrl("thumb.profile");
						if(!$photoUrl)
						{
							$photoUrl = $this->baseUrl()."/application/modules/Group/externals/images/nophoto_group_thumb_normal.png";
						}?>
					<div class="wrap_col3_left cover-img" style="background-image:url(<?php echo $photoUrl?>)"><a href="<?php echo $this -> groupBlock->getHref()?>"></a></div>
					<div class="wrap_col3_center">
						<div class="yn-title"><a href="<?php echo $this -> groupBlock->getHref()?>"><?php echo $this -> groupBlock->getTitle()?></a></div>
						<div class="yn-post-time"><i class="yn-icon yn-time"></i><?php echo Engine_Api::_() -> ynresponsive1() -> getGroupTimeAgo($this -> groupBlock)?></div>
						<div class="yn-post-member"><i class="yn-icon yn-user"></i> <?php echo $this->translate(array('%s member', '%s members', $this -> groupBlock->member_count), $this->locale()->toNumber($this -> groupBlock->member_count)) ?></div>
						<div class="yn-post-desc"><?php echo $this -> string() -> truncate(strip_tags($this -> groupBlock->getDescription()), 42)?> </div>
					</div>
				</div>
			<?php else: ?>
				<div class="yn-poll wrap_col3">
					<div class="wrap_col3_left cover-img" style="background-image:url(<?php echo $this -> groupBlock -> getPhotoUrl("thumb.profile", 4)?>)"><a href="<?php echo $this -> groupBlock -> link;?>"></a></div>
					<div class="wrap_col3_center">
						<div class=""><img src="<?php echo $this->groupBlock -> icon?>"/></div>
						<div class="yn-title"><a href="<?php echo $this -> groupBlock -> link;?>"><?php echo $this -> groupBlock -> title?></a></div>
						<div class="yn-post-desc"><?php echo $this -> string() -> truncate(strip_tags($this -> groupBlock -> description), 42)?></div>
					</div>
				</div>
		    <?php endif;?>			
		<?php else: ?>
			<!-- show block default-->
			<div class="yn-group blank wrap_col3">
				<div class="wrap_col3_left" style="background:#c9e73b url(application/themes/ynresponsive-metro/images/yn_blank.png) no-repeat center center"></div>
				<div class="wrap_col3_center">
					<div><i class="yn-icon yn-question"></i></div>
					<div><?php echo $this -> translate("Add widget...")?></div>
				</div>
			</div>
		<?php endif;?>
	</div>
	<div class="yn-colums">
		<?php if($this -> oneBlock):?>
			<div class="yn-group wrap_col3">
					<div class="wrap_col3_left cover-img" style="background-image:url(<?php echo $this -> oneBlock -> getPhotoUrl("thumb.profile", 5)?>)">
						<a href="<?php echo $this -> oneBlock -> link;?>"></a>
					</div>
				<div class="wrap_col3_center">
					<div class="">
						<?php if($this->oneBlock -> icon):?>
							<img src="<?php echo $this->oneBlock -> icon?>"/>
						<?php endif;?>
					</div>
					<div class="yn-title">
						<a href="<?php echo $this -> oneBlock -> link;?>"><?php echo $this -> oneBlock -> title?></a>
					</div>
					<div class="yn-post-desc"><?php echo $this -> string() -> truncate(strip_tags($this -> oneBlock -> description), 42)?></div>
				</div>
			</div>
		<?php else: ?>
			<!-- show block default-->
			<div class="yn-group blank wrap_col3">
				<div class="wrap_col3_left" style="background:#6ecd05 url(application/themes/ynresponsive-metro/images/yn_blank.png) no-repeat center center"></div>
				<div class="wrap_col3_center">
					<div><i class="yn-icon yn-question"></i></div>
					<div><?php echo $this -> translate("Add widget...")?></div>
				</div>
			</div>
		<?php endif;?>
		<?php if($this -> twoBlock && $this -> threeBlock):?>
			<div class="yn-group wrap_col3">
					<div class="wrap_col3_left cover-img" style="background-image:url(<?php echo $this -> twoBlock -> getPhotoUrl("thumb.profile", 6)?>)">
						<a href="<?php echo $this -> twoBlock -> link;?>"></a>
					</div>
				<div class="wrap_col3_center">
					<div class="">
						<?php if($this->twoBlock -> icon):?>
						<img src="<?php echo $this->twoBlock -> icon?>"/>
						<?php endif;?>
					</div>
					<div class="yn-title"><a href="<?php echo $this -> twoBlock -> link;?>"><?php echo $this -> twoBlock -> title?></a></div>
					<div class="yn-post-desc"><?php echo $this -> string() -> truncate(strip_tags($this -> twoBlock -> description), 42)?></div>
				</div>
			</div>
		<?php elseif($this -> twoBlock): ?>
			<div class="yn-other">
				<div class="header">
					<div class="yn-title">
						<a href="<?php echo $this -> twoBlock -> link;?>"><?php echo $this -> twoBlock -> title?></a>
					</div>
					<div class="yn-desc"><?php echo $this -> string() -> truncate(strip_tags($this -> twoBlock -> description), 42)?></div>
				</div>
				<div class="footer cover-img" style="background-image:url(<?php echo $this -> twoBlock -> getPhotoUrl("thumb.profile", 6)?>)"><a href="<?php echo $this -> twoBlock -> link;?>">
				</a></div>
			</div>
		<?php else: ?>
			<!-- show block default-->
			<div class="yn-other wrap_col3 blank_small">
				<div class="wrap_col3_left" style="background:#9a4aa1 url(application/themes/ynresponsive-metro/images/yn_blank.png) no-repeat center center"></div>
				<div class="wrap_col3_center">
					<div><i class="yn-icon yn-question"></i></div>
					<div><?php echo $this -> translate("Add widget...")?></div>
				</div>
			</div>
			<?php if(!$this -> threeBlock):?>
				<div class="yn-other wrap_col3 blank_small">
					<div class="wrap_col3_left" style="background:red url(application/themes/ynresponsive-metro/images/yn_blank.png) no-repeat center center"></div>
					<div class="wrap_col3_center">
						<div><i class="yn-icon yn-question"></i></div>
						<div><?php echo $this -> translate("Add widget...")?></div>
					</div>
				</div>
			<?php endif;?>
		<?php endif;?>
		<?php if($this -> threeBlock):?>
			<div class="yn-group wrap_col3">
				<div class="wrap_col3_left cover-img" style="background-image:url(<?php echo $this -> threeBlock -> getPhotoUrl("thumb.profile", 7)?>)"><a href="<?php echo $this -> threeBlock -> link;?>"></a></div>
				<div class="wrap_col3_center">
					<div class="">
						<?php if($this->threeBlock -> icon):?>
							<img src="<?php echo $this->threeBlock -> icon?>"/>
						<?php endif;?>
					</div>
					<div class="yn-title"><a href="<?php echo $this -> threeBlock -> link;?>"><?php echo $this -> threeBlock -> title?></a></div>
					<div class="yn-post-desc"><?php echo $this -> string() -> truncate(strip_tags($this -> threeBlock -> description), 42)?></div>
				</div>
			</div>
		<?php endif;?>
	</div>
	</div>
</div>
<script>
	(function( $ ) {
	  $(function() {
		var slider = $('.yn-photo .bxslider').bxSlider({
			mode: 'fade',
            minSlides: 1,
            maxSlides: 1,
            slideMargin: 0,
            pager: false,
            moveSlides: 1,
            autoHover: true,
            auto: true,
        });
		$(window).resize(function(){
			slider.reloadSlider();
		});
	  });
	})(jQuery);
</script>