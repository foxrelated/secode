<?php $this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');  
       ?> 
<ul class="global_form_box" style="margin-bottom: 15px; overflow: hidden; background:none;">
<div class="mp3_top_album">
         <?php
         $model = new Mp3music_Model_Album(array());
         $albums    =  $model->getTopAlbums();
         $i = 0;
         foreach ($albums as $album):
         if(count($album->getSongs(null, 1)) > 0):
         if($i < 8):
         $i ++;
         ?>
    <li class="mp3music_newsalbums">
         <div style="height: 190px;"> 
            <div class="mp3music_bgalbums" title="<?php echo strip_tags($album->title)  ?>">
                <a href="javascript:;"  onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
                 <?php echo $this->itemPhoto($album, 'thumb.normal'); ?>  
                </a>
            </div>
            <div class="mp3_album_title_link">
                <a title="<?php echo strip_tags($album->title);?>" href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
                <?php echo $this->string()->truncate($album->title, 20);?>
                </a>
            </div>
              <div class="mp3_album_info" style="width: 100px;">   
                <?php
                echo $this->htmlLink($album->getOwner(), $this->string() -> truncate($album->getOwner()->getTitle(), 15)); ?> | 
                <a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
                 <?php echo $this->translate(array('%s play', '%s plays',$album->play_count),$album->play_count)?> </a>
                <!-- Insert cart shop -->
                 <?php  $user = Engine_Api::_()->user()->getViewer(); ?>
                  <?php if($user->getIdentity()  > 0):?>
                  <div style="height: 14px; margin-top: 4px;">
                   <?php   $hiddencartsalbum = Mp3music_Api_Shop::getHiddenCartItem('album',$user->getIdentity());    
                   $acc = Mp3music_Api_Cart::getFinanceAccount($album->user_id);
                    if($album->price == 0 || in_array($album->album_id,$hiddencartsalbum) || $acc == null):
                        ?>
                          <a href="#" onclick = "alert('<?php echo $this->translate('The album already in your cartshop or in download list or it is not allowed to sell or owner of item does not have finance account. Please check again!');?>'); return false;"> <img style="position: absolute;" src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>
                      <?php else: 
                   ?>
                   <span id="album_id_cart_<?php echo $album->album_id; ?>">  
                   <?php $selling_settings = Mp3music_Api_Cart::getSettingsSelling($user->level_id);  
                     if($selling_settings['can_buy_song'] ==  1):?>
                         <a href="javascript:addtocart(<?php echo $album->album_id; ?>,'album')"> <img style="position: absolute;" src="./application/modules/Mp3music/externals/images/addtocart_ic.jpg" /></a>
                         <?php else:?>
                         <a href="#" onclick="alert('<?php echo $this->translate('You do not have permission to buy it !');?>');return false;"><img style="position: absolute;" src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>
                     <?php endif; ?>
                     </span>
                     <?php endif;?>
                         <span style="color: red;clear: both; padding-left:25px">
                          $ <?php echo number_format($album->price,2) ?>
                         </span>
                     </div>
                    <?php endif; ?>
            </div>
         </div>
    </li>  
    <?php endif; endif; endforeach; ?> 
</div>
</ul>