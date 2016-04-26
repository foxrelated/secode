<?php
$this->headScript()  
	   ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js')
     ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/slideshow/Navigation.js')
	   ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/slideshow/Loop.js')
	   ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/slideshow/SlideShow.js');
?>
<section id="mp3music_navigation" class="demo">
	<div id="mp3music_navigation-slideshow" class="slideshow">
		<?php
         $i = 0;
		 $user = Engine_Api::_()->user()->getViewer(); 
         foreach ($this->albums as $album):
         if(count($album->getSongs()) > 0):
         if($i < $this->limit):
         $i ++;
         ?>
		    <span id="lp<?php echo $i?>">
		    	<div class="mp3music_album_photo">
	                <a class = "mp3music_photo_main" title="<?php echo $album->title?>" href="javascript:;"  onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
	                 <?php echo $this->itemPhoto($album, ''); ?>  
	                </a>
		            <div class="mp3music_albumfeatured_info">
		            	<div class="mp3music_album_title">
		            		<h3>
		            			<a title="<?php echo strip_tags($album->title);?>" href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
				                <?php echo $this->string()->truncate($album->title, 100);?>
				                </a>
		            		</h3>
		            	</div>
		            	<div class="mp3music_album_info" style="float: left; padding-right: 10px">
		            		<?php echo $this->translate("Posted by %1s on %2s", $album->getOwner(),$this->locale()->toDateTime(strtotime($album->creation_date), array('type' => 'date')));?> - 
		            		<?php echo $this->translate(array("%s play", "%s plays", $album->play_count),$album->play_count)?>
		            	</div>
		            	<div>
		            	  <!-- Insert cart shop -->
		                  <?php if($user->getIdentity()  > 0):?>
		                   <?php $hiddencartsalbum = Mp3music_Api_Shop::getHiddenCartItem('album',$user->getIdentity());    
		                   		$acc = Mp3music_Api_Cart::getFinanceAccount($album->user_id);
			                    if($album->price == 0 || in_array($album->album_id,$hiddencartsalbum) || $acc == null):?>
			                     <div style="float:left; padding-right: 5px">
			                          <a href="#" onclick = "alert('<?php echo $this->translate('The album already in your cartshop or in download list or it is not allowed to sell or owner of item does not have finance account. Please check again!');?>'); return false;"> 
			                          	<img  src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>
			                    </div>
			                    <?php else: ?>
					                   <div id="featuredalbum_id_cart_<?php echo $album->album_id; ?>" style="float:left; padding-right: 5px">  
					                   <?php $selling_settings = Mp3music_Api_Cart::getSettingsSelling($user->level_id);  
					                     if($selling_settings['can_buy_song'] ==  1):?>
					                         <a href="javascript:addtocart(<?php echo $album->album_id; ?>,'album', 'featured')"> <img src="./application/modules/Mp3music/externals/images/addtocart_ic.jpg" /></a>
					                         <?php else:?>
					                         <a href="#" onclick="alert('<?php echo $this->translate('You do not have permission to buy it !');?>');return false;"><img style="position: absolute;" src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>
					                     <?php endif; ?>
					                     </div>
		                     	<?php endif;?>
		                         <div style="color: red;">
		                          $ <?php echo number_format($album->price,2) ?>
		                         </div>
		                    <?php endif; ?>
		               </div>
		            	<p class="mp3music_album_info" style="clear: both">
		            		<?php echo $this->string()->truncate($album->description, 160);?>
		            	</p>
		            </div>
		       </div>
		    </span> 
	
    	<?php endif; endif; endforeach; ?> 
		<ul class="mp3music_pagination" id="mp3music_pagination">
			<li><a class="current" href="#lp1"></a></li>
			<?php for ($j = 2; $j <= $i; $j ++):?>
			<li><a href="#lp<?php echo $j?>"></a></li>
			<?php endfor;?>
		</ul>
	</div>
</section>
