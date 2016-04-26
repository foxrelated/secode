<div class="headline">
  <h2>
    <?php echo $this->translate('Mp3 Music');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
  <?php
  function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
 }       
  ?> 
    <?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');   
 $this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Mp3music/externals/styles/main.css');

       ?>
<?php if (count($this->cartlist)>0): ?>   
<div class="table">    
        <table width="100%">
    <tr>
        <td width="65%">
  <h3  style="margin-bottom: 10px; margin-top: 9px;">
    <?php echo $this->translate('Products'); ?>
    </h3>
  <ul  align="right" class="global_form_box" style="background: none; padding: 0px; margin-bottom: 10px; overflow: auto;">
                <table width="100%" >
                <tr style="background:#EFF4F7 none repeat scroll 0 0;"> 
                    <td height="25px" width="40%" style="font-weight:bold;padding:2px 2px 2px 7px;">
                    <span style="padding-left: 10px"><?php echo $this->translate('Name'); ?></span></td>
                    <td style="font-weight:bold;padding:2px;text-align:center"><?php echo $this->translate('Price'); ?></td>
                    <td style="font-weight:bold;padding:2px;text-align:center"><?php echo $this->translate('Seller'); ?></td>
                </tr>
                    <?php $index = 0;  foreach($this->cartlist as $cartitem): $index++; $album = Engine_Api::_()->getItem('mp3music_album', $cartitem["album_id"]);   ?>
                                    <tr id="car_id_item_<?php echo $index; ?>">
                                    <td width="55%" style="padding:7px;border-bottom:1px solid #E9F4FA;">
                                    <div class="mp3_title_link" style="padding-left: 10px;">
                                            <?php if($cartitem['type'] == 'song'): ?>
                                            <a href="javascript:;" class ='title_thongtin2' onClick="return openPage('<?php echo $this->url(array('album_id'=>$cartitem["album_id"],'song_id'=>$cartitem["item_id"]), 'mp3music_album_song');?>',800,565)"><?php echo $cartitem['title'];?></a>
                                            <?php else: ?>
                                                <?php if(count($album->getSongs()) > 0):?>
                                                <a href="javascript:;" class ='title_thongtin2' onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',800,565)"><?php echo $album->getTitle();?></a>                          
                                                <?php else: ?>
                                               <span style="font-weight: bold;"> <?php echo $album->getTitle();?>  </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                    
                                            <div  class="mp3_album_info" style="padding-top:5px;">
                                              <?php echo $this->translate('Type:'); ?>  <font>   <?php if($cartitem['type'] == 'song'): ?>  <?php echo $this->translate('Song'); ?><?php else: ?><?php echo $this->translate('Album'); ?><?php endif; ?></font>  |  <?php echo $this->translate('Seller: '); ?> <span style="font-weight: bold;"> <?php echo $album->getOwner() ?>  </span>
                                            </div>
                                        </div>          
                                    </td>
                                     <td  align="center" style="padding:7px;border-bottom:1px solid #E9F4FA;">
                                       <div style="text-align:center;" class="profile_blogentry_date" style="padding-top:5px;">
                                               <font color="red">$ <?php echo $cartitem['amount'];?></font>
                                            </div>
                                    </td>
                                     <td  align="center" style="padding:7px;border-bottom:1px solid #E9F4FA;">                                    
                                        <div style="text-align:center;">
                                           <a rel="balloon_album_<?php echo $album->album_id; ?>" href="<?php echo $album->getOwner()->getHref()?>" class ='title_thongtin2'><?php echo $this->itemPhoto($album->getOwner(), 'thumb.icon'); ?></a>   
                                        </div>  
                                    </td>        
                                        
                                </tr>
                                <?php   endforeach;
                                 ?>
                 
                </table>
            </ul>
        </td>
        <td width="35%" align="left" style="vertical-align:text-top; padding-left: 10px;">
         <h3  style="margin-bottom: 10px; margin-top: 9px;">
        <?php echo $this->translate('Payment Infomation'); ?>
        </h3>
            <ul class="global_form_box">
                    <div class="p_4">
                            <table style="width: 100%;">
                            <tbody>
                            <tr style="border-bottom : 1px solid #CCCCCC;">
	                            <td style="text-align: left; padding: 6px; border: none">
	                               <?php echo $this->translate('Product Total');?>      </td>
	                            <td style="text-align: left; padding: 6px; border: none">
	                                <span id="total_product_amount_checkout"><?php echo $this->total; ?></span>
	                            </td>
                            </tr>
                            <tr style="border-bottom : 1px solid #CCCCCC;">
                                <td style="text-align: left; padding: 6px;  border: none">
                                   <?php echo $this->translate('Total');?>
                                </td>
                                <td  style="font-size: 14pt;">
                                    <div style=" padding: 8px; float:left;">
                                        <span id="total_product_amount_sub_checkout"><b style="color: red">$<?php echo number_format($this->total_amount,2);?> </b>  </span>        
                                    </div>
                                </td>
                            </tr>
                           
                            </tbody>
                            
                         </table>
                    </div>
        </ul>
        </td>
    </tr>
 </table>
 </div>
 <form method="post" action="<?php echo $this->escape($this->url(array('action' => 'update-order'), 'mp3music_cart_general', true)) ?>" class="global_form" enctype="application/x-www-form-urlencoded">
					    <div>
					      <div>
					        <div class="form-elements">
					          <div id="buttons-wrapper" class="form-wrapper">
					            <?php foreach( $this->gateways as $gatewayInfo ):
					              $gateway = $gatewayInfo['gateway'];
					              $plugin = $gatewayInfo['plugin'];
					              $first = ( !isset($first) ? true : false );
					              ?>
					              <button style="margin-top: 5px" type="submit" name="gateway_id" value="<?php echo $gateway->gateway_id ?>">
					                <?php echo $this->translate('Pay with')." ".$this->translate($gateway->title) ?>
					              </button>
					               	 <?php echo $this->translate(' or ') ?>
					            <?php endforeach; ?>
								   <a href="<?php echo $this->url(array(),'mp3music_browse',true); ?>"> <?php echo $this->translate('cancel') ?> </a>
					          </div>
					        </div>
					      </div>
					    </div>
				  </form>
 <?php else: ?>
    <div align="center" style="margin: 20px;"> <?php echo $this->translate('There are no items in your cart.');?></div> 
<?php endif; ?>
