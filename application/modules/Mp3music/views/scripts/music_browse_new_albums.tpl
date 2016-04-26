<?php 
if(!function_exists('renderMusicPagination'))
{
    function renderMusicPagination($html)
    {
        $posStartHref = strpos($html,"<a href='#'>");
        if ($posStartHref<=0)
            return $html;
        $posEndHref = strpos($html,"</a>",$posStartHref);
        $page = trim(substr($html,$posStartHref+12,$posEndHref-$posStartHref-4));
        $pagenumber = (int)$page;
        if ( ($pagenumber)<0)
            return $html;
        $html = str_replace("href='#'","href='mp3-music/browse/browse_new_albums/".$pagenumber."'",$html);
        return $html;
    }
}
?>
    <?php
	$this->headScript()
		 ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');  
	?>	
	<h3><?php echo $this->translate('All Albums'); ?> 
        </h3>   
<ul class="global_form_box" style="background: none; overflow: auto;">   
<div class="mp3_browse_album">  
        <?php $albums    =  $this->browse->albumPaginator;
        foreach ($albums as $album):?>
           <?php if(count($album->getSongs(null, 1)) > 0):?>
           <li class="mp3music_browsealbums" style="float: none; overflow: hidden;">
           <div class="mp3music_bgalbums" style="float: left" title="<?php echo $album->title;  ?>">
               <a href="javascript:;"  onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
                <?php echo $this->itemPhoto($album, 'thumb.normal'); ?>  
                </a>
            </div>
             <div class="mp3_album_des">
                <div class="mp3_title_link">
                    <a title="<?php echo $album->title;?>" href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',123,565)">
                     <?php echo $this->string()->truncate($album->title, 20);?>
                    </a>
                </div>
                <div class="mp3_album_info" style="width: 380px;">
                    <?php echo $this->translate('Author: ');?><?php echo $album->getOwner() ?> <br/>
                    <?php echo $this->translate('Created: %s',$this->timestamp($album->creation_date)) ?>
                 - 
                <!-- Insert cart shop -->
                 <?php  $user = Engine_Api::_()->user()->getViewer(); ?>
                  <?php if($user->getIdentity()  > 0):?>
                  <span style="height: 14px; margin-top: 4px;">
                   <?php   $hiddencartsalbum = Mp3music_Api_Shop::getHiddenCartItem('album',$user->getIdentity());    
                   $acc = Mp3music_Api_Cart::getFinanceAccount($album->user_id);
                    if($album->price == 0 || in_array($album->album_id,$hiddencartsalbum) || $acc == null):
                        ?>
                          <a href="javascript:alert('The album\'s already in your cartshop or in download list or it is not allowed to sell or owner of item does not have finance account.\n Please check again!')"> <img style="position: absolute;" src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>
                      <?php else: 
                   ?>
                   <span id="album_id_cart_<?php echo $album->album_id; ?>">  
                   <?php $selling_settings = Mp3music_Api_Cart::getSettingsSelling($user->level_id);  
                     if($selling_settings['can_buy_song'] ==  1):?>
                         <a href="javascript:addtocart(<?php echo $album->album_id; ?>,'album')"> <img style="position: absolute;" src="./application/modules/Mp3music/externals/images/addtocart_ic.jpg" /></a>
                         <?php else:?>
                         <a href="#" onclick='alert("You do not have permission to buy it !");return false;'><img style="position: absolute;" src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>
                     <?php endif; ?>
                     </span>
                     <?php endif;?>
                         <span style="color: red;clear: both; padding-left:25px">
                          $ <?php echo number_format($album->price,2) ?>
                         </span>
                    </span>
                    <?php endif; ?>
                    <div style="padding-top: 10px;"> 
                        <?php echo $album->description ?>
                    </div>
                </div>
            </div>
        </li>  
         <?php endif;  endforeach;  ?>    
</div>                       
    <span style="float:right ;"> <?php echo renderMusicPagination($this->paginationControl($this->browse->albumPaginator,null, null, array(
                      'pageAsQuery' => false,
                      'query' => array("search"=>$this->browse->params['search'], "title"=>$this->browse->params['title']),
                    )))?>  </span> 
    <?php if (0 == count($albums) ): ?>
                <div class="tip" style="padding-left: 20px;">
                <span>
                    <?php echo $this->translate('Nobody has uploaded an album yet.') ?> 
                </span> 
                </div>
                <?php endif;  ?> 
</ul>
