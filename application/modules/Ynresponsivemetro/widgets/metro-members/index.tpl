<div class="widget-member">
	<div class="member">
		<ul>
			<?php foreach( $this->members as $user ): 
			$photoUrl = $user -> getPhotoUrl('thumb.profile');
			if(!$photoUrl)
			{
				$photoUrl = $this->baseUrl().'/application/modules/User/externals/images/nophoto_user_thumb_profile.png';
			}?>
			<li>
				<div class="flipper">
					<div class="cover-img front" style="background-image:url(<?php echo $photoUrl?>)">
					</div>
					<div class="cover-img back">
						<div class="info"><div><a href="<?php echo $user->getHref()?>"><?php echo $user->getTitle()?></a></div></div>
					</div>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<div class="footer">
		<?php echo $this -> translate(array("%s New Users Registered", "%s New Users Registered", $this -> total_members), $this -> total_members);?>
	</div>
</div>