<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');
       function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
 } 
       ?>
  <h3  style="margin-bottom: 10px; margin-top: 9px;">
    <?php echo $this->translate('My Cart'); ?>
    </h3>
  <ul id='cart_list_info'  align="right" class="global_form_box" style="background: none; padding: 0px; margin-bottom: 10px; overflow: auto;">
    <?php if ($this->total_amount > 0): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url();?>">   
        <table cellpadding="0" cellspacing="0" width="100%">
        <tr style="background:#EFF4F7 none repeat scroll 0 0;"> 
            <td height="25px" width="40%" style="font-weight:bold;padding:2px 2px 2px 10px;">
            <input type="checkbox" name="checkCartItem" onclick="checkAll(this);"/>
            <span style="padding-left: 10px"><?php echo $this->translate('Name'); ?></span></td>
            <td style="font-weight:bold;padding:2px;text-align:center"><?php echo $this->translate('Price'); ?></td>
            <td class="cart_seller" style="font-weight:bold;padding:2px;text-align:center"><?php echo $this->translate('Seller'); ?></td>
            <td style="font-weight:bold;padding:2px;text-align:center"><?php echo $this->translate('Action'); ?></td>
        </tr>
         <?php  $index = 0;
           foreach($this->cartlist as $cartitem): $index++; $album = Engine_Api::_()->getItem('mp3music_album', $cartitem["album_id"]);   ?>
              <tr id="car_id_item_<?php echo $index; ?>" >
              
            <td width="50%" style="padding:10px;border-bottom:1px solid #E9F4FA;border-right:1px solid #E9F4FA;">
                    <input type="checkbox" value="<?php echo $cartitem['item_id']; ?>" id="car_id_item_<?php echo $cartitem['item_id']; ?>" name="car_id_item_<?php echo $cartitem['item_id']; ?>-<?php echo $cartitem['type'] ?>"/>
                    <div style="padding-left: 28px;" class="mp3_title_link">
                    <?php if($cartitem['type'] == 'song'): ?>
                     <a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$cartitem["album_id"],'song_id'=>$cartitem["item_id"]), 'mp3music_album_song');?>',800,565)"><?php echo $cartitem['title'];?></a>
                    <?php else: ?>
                        <?php if(count($album->getSongs()) > 0):?>
                        <a href="javascript:;"  onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',800,565)"><?php echo $album->getTitle();?></a>                          
                        <?php else: ?>
                       <span style="font-weight: bold;"> <?php echo $album->getTitle();?>  </span>
                        <?php endif; ?>
                    <?php endif; ?>
                     
                    <div  class="mp3_album_info" style="padding-top:5px; ">
                      <?php echo $this->translate('Type:'); ?> <?php if($cartitem['type'] == 'song'): ?>  <?php echo $this->translate('Song'); ?><?php else: ?><?php echo $this->translate('Album'); ?><?php endif; ?> |  <?php echo $this->translate('Seller: '); echo $album->getOwner() ?> 
                    </div>
                    </div> 
            </td>
             <td  align="center" style="padding:7px;border-bottom:1px solid #E9F4FA;border-right:1px solid #E9F4FA">
               <div style="text-align:center;" class="profile_blogentry_date" style="padding-top:5px;">
                       <font color="red" style="font-weight: bold;">$ <?php echo number_format($cartitem['amount'],2);?></font>
                    </div>
            </td>
           <td class="cart_seller"  align="center" style="padding:7px;border-bottom:1px solid #E9F4FA;border-right:1px solid #E9F4FA">                                    
                <div style="text-align:center;">
                   <a rel="balloon_album_<?php echo $album->album_id; ?>" href="<?php echo $album->getOwner()->getHref()?>" class ='title_thongtin2'><?php echo $this->itemPhoto($album->getOwner(), 'thumb.icon'); ?></a>   
                </div>  
            </td>        
                <td  align="center" style="padding:2px;border-bottom:1px solid #E9F4FA;">
                <div style="text-align:center;">
                      <?php if($cartitem['type'] == 'song'): 
                      	$playTooltip = $this->translate("Play");
					  	if($cartitem['amount'] != 0)
					  	 	$playTooltip = $playTooltip." ".$this->translate("Preview");?>     
                      <a class="mp3music_playmusic" href="javascript:;" title="<?php echo $playTooltip;?>" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id,'song_id'=>$cartitem["item_id"]), 'mp3music_album_song');?>',800,565)"><img src="./application/modules/Mp3music/externals/images/music/icon_play.png" /></a>
                         <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != "0"):
                          echo $this->htmlLink(
                           $this->url(array('playlist_id'=>'0','song_id'=>$cartitem["item_id"]), 'mp3music_playlist_append'),
                            '<img src="./application/modules/Mp3music/externals/images/music/icon_add.png" />',
                            array('class'=>'smoothbox music_player_tracks_add','title'=> $this->translate('Add to Playlist')) );  endif;?>
                      <?php else:?>
                        <a class="mp3music_playmusic" href="javascript:;" title="<?php echo $this->translate("Play");?>" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',800,565)"><img src="./application/modules/Mp3music/externals/images/music/icon_play.png" /></a>                                                                          
                      <?php endif; ?>
                              
                     <a title="<?php echo $this->translate('Remove Item') ?>" href="javascript:removecartitem(<?php echo $cartitem['item_id'] ?>,'<?php echo $cartitem['type'] ?>',<?php echo $index; ?>)" ><img src="./application/modules/Mp3music/externals/images/music/icon_delete.png" /></a>               
                  </div>     
                </td>           
        </tr>

        <?php   endforeach;
         ?>
        </form>
     <tr style="background:#EFF4F7 none repeat scroll 0 0;">
        <td style="padding-left: 10px;">
        <input style="margin-top: 8px;" id = 'check_all' type="checkbox"  onclick="checkAll(this);"/> 
        <span class="cart_select" style="font-weight: bold; padding-left: 10px; padding-right: 10px;" >
        <a style="text-decoration: none">
        <?php echo $this->translate("Select all"); ?>
        </a>
        </span>
        <button class="cart_remove"  style="width:100px" onclick="return multiDelete()" type="submit"><?php echo $this->translate('Remove'); ?></button>
        </td>
        <td>
            <div style=""><b><?php echo $this->translate('Total'); ?></b> <span id="total_product_amount_in"  style="font-weight: bold;color:red">
            <span style="padding-left: 10px" id="total_pro_index">$ <?php echo number_format($this->total_amount,2); ?></span>  </span></div>
        </td>
            
        <td class="cart_checkout" colspan="2" class="met" style=" padding: 10px 30px 10px 10px; text-align: right; " >
             <form method="post" action="<?php echo $this->url(array(),'mp3music_cart_checkout'); ?>">
             <input type="hidden" value="<?php echo session_id();?>" name="session_id" />
            <button style="width:100px" type="submit"><?php echo $this->translate('Check Out'); ?></button>
            </form>
        </td>
     </tr>  
    </table>
    <?php  else: ?>
    <?php if($this->moveitem == 1): ?>
     <div align="center" style="text-align: center ;margin: 20px;"><?php echo $this->translate('Your items moved to your '); ?> <a href="<?php echo $this->url(array(),'mp3music_cart_deletedownload'); ?>"><?php echo $this->translate('download list '); ?></a>.</div>
    <?php else: ?>
     <div align="center" style="text-align: center ; margin: 20px;"><?php echo $this->translate('There are no items in your cart.'); ?></div>
   <?php endif; ?>

   <?php endif; ?>
   </ul>
<script type="text/javascript">
function multiDelete()
{
     if(confirm("<?php echo $this->translate('Are you sure you want to delete the selected carts?');?>"))
     	$('multidelete_form').submit();
}

function checkAll(obj)
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 0; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = obj.checked;
    }
  }
  $('check_all').checked = obj.checked;
}
</script>