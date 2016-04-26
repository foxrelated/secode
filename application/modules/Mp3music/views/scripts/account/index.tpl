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
<img src='./application/modules/Mp3music/externals/images/music/account.jpg' width="48px" height="48px" border='0' class='icon_big' style="margin-bottom: 15px;">
<div class='page_header'><?php echo $this->translate('My Account'); ?></div>
<div>
      <?php echo $this->translate('Personal Finance Account Management.'); ?><span><a href="<?php echo $this->url(array(),'mp3music_cart_transaction');?>"> <?php echo $this->translate('View my transaction history'); ?></a></span><br />
</div>
<?php $info_user = $this->info_user; ?>
<div class="mp3_space-line"></div>

<div style="margin-bottom: 10px;">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
            <td style="width: 50%;margin-top: 8px; padding:10px 1px; vertical-align: top;">
             <h3 style="margin-bottom: 10px;">
             <?php echo $this->translate('User Information'); ?>
              </h3>
             <ul align="left" class="global_form_box" style="background: none; margin-bottom: 10px; overflow: auto;">
                    <form method="post" action="<?php echo $this->url(array(),'mp3music_account_edit'); ?>">
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr class="mp3_account">
                        <td width="35%" >
                        <?php echo $this->translate('Username'); ?>:
                        </td>
                        <td>
                         <?php echo $info_user['username']?>
                        </td>
                    </tr>
                    <tr class="mp3_account">
                        <td >
                        <?php echo $this->translate('Full name'); ?>:
                        </td>
                        <td>
                        <?php echo $info_user['displayname']?>
                        </td>
                    </tr>
                    <tr class="mp3_account">
                        <td>
                        <?php echo $this->translate('Email'); ?>:
                        </td>
                        <td>
                        <?php echo $info_user['email'] ?>
                        </td>
                    </tr>
                    <tr class="mp3_account">
                        <td>
                        <?php echo $this->translate('Status'); ?>:
                        </td>
                        <td>
                        <?php if ($info_user['status'] != ''): echo $info_user['status']; else:  echo $this->translate('Not update'); endif; ?>
                        </td>
                    </tr>
                    <tr class="mp3_account" style="height: 60px;">
                        <td colspan="2">
                            <span style="padding-top: 3px;"><?php echo $this->translate('Please read carefully all policy of music selling'); ?></span> 
                            <div class="p_4"></div>
                            <div style="float: left; text-align: center;margin-right: 5px;">
                            <?php echo $this->htmlLink(
                                       $this->url(array('type'=>'1', 'format' => 'smoothbox'), 'mp3music_account_policy'),
                                       $this->translate('General Policy'),
                                        array('class'=>'smoothbox') );   ?>
                            |</div>
                        <div style="float: left;">
                             <?php echo $this->htmlLink(
                                       $this->url(array('type'=>'2', 'format' => 'smoothbox'), 'mp3music_account_policy'),
                                       $this->translate('Request Policy'),
                                        array('class'=>'smoothbox') );   ?>
                            </div>
                        </td>
                    </tr>
                    
                    <tr style="height: 50px;">
                        <td align="right">
                            <div class="p_4">
                                <button style="width: 75px" type="submit" name="editperionalinfo"><?php echo $this->translate('Edit'); ?> </button>
                            </div>  
                        </td>
                    </tr>
                </table>
             </form>
        </ul>
        <h3 style="margin-bottom: 10px;">
        <?php echo $this->translate('Summary'); ?> 
        </h3>
        <ul align="left" class="global_form_box" style="background: none; margin-bottom: 10px; overflow: auto;">   
         <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr class="mp3_account">
            <td width="35%">
                <?php echo $this->translate('Account'); ?>:
            </td>
            <td>
             <?php if($this->info_account): ?>
                 <?php echo $this->htmlLink(
                           $this->url(array('id'=>$this->info_account["user_id"],'username'=>'null'), 'mp3music_cart_viewtransaction'),
                            $this->info_account["account_username"],
                            array('class'=>'smoothbox') );   ?>  
                <?php endif; ?>
            </td>
        </tr>
        <tr class="mp3_account">
            <td>
                <?php echo $this->translate('Accumulated'); ?>:
            </td>
            <td>
            <span style="color: red;"><?php echo number_format($this->info_account['total_amount'],2)?> USD </span>
            </td>
        </tr>
        <tr class="mp3_account">
            <td >
            <?php echo $this->translate('Waiting'); ?>:
            </td>
            <td>
            <span style="color: red;"><span id="current_request_money"><?php echo number_format($this->requested_amount,2); ?> </span> USD  </span>
            </td>
        </tr>
        <tr class="mp3_account">
            <td>
            <?php echo $this->translate('Current Amount'); ?>:
            </td>
            <td>
             <span style="color: red;"><span id="current_money_money"><?php echo number_format($this->current_amount,2); ?>  </span> USD</span>
            </td>
        </tr>
         <tr class="mp3_account">
            <td colspan="2">
            <?php echo $this->translate('Minimum amount in your account'); ?>:
            <span style="color: red;"> <?php  echo number_format($this->min_payout,2); ?> USD </span>
            </td>
        </tr>
        <tr style="height: 30px;">
            <td>
                <?php echo $this->translate('Maximum to request'); ?>:
            </td>
            <td>
            <span style="color: red;"> <?php if ($this->max_payout == -1): echo $this->translate('Unlimited'); else: echo number_format($this->max_payout,2); echo ' USD'; endif; ?></span>
            </td>
        </tr>
       
        <tr>
            <td colspan="2">
            <div class="p_4">
            <div style="float:left; padding-right: 10px;">
                <?php if($this->allow_request == 0): 
                    if ($this->info_account['payment_type'] != 1):   ?>
                     <?php if(number_format($this->current_amount,2)!=0.00):?>
                        <a class="smoothbox" href="<?php echo $this->url(array('user_id'=>'1'), 'mp3music_payment_threshold') ?>" title="<?php echo $this->translate('Request'); ?>" ><button  name="request"><?php echo $this->translate('Request'); ?></button></a>
                     <?php endif;?>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($this->info_account['payment_type'] != 1):?>
                          <?php if(number_format($this->current_amount,2)!=0.00):?>
                              <a class="smoothbox" href="<?php echo $this->url(array('user_id'=>'1'), 'mp3music_payment_threshold') ?>" title="<?php echo $this->translate('Request'); ?>" ><button  name="request"><?php echo $this->translate('Request'); ?></button></a>
                          <?php endif;?>
                    <?php else: ?>
                    <?php echo $this->translate("You're admin.You cannot request money"); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>    
                <?php if (count($this->info_account) == 0):
                    if ($this->info_account['payment_type'] != 1):   ?>
                    <a title="Add account" onclick="window.location.href=this.href;" href="<?php echo $this->url(array(),'mp3music_account_create');  ?>"  > <button name="addaccount"><?php echo $this->translate('Add account'); ?></button></a> 
                   <?php endif; ?>
                <?php endif; ?>
            </div>
            </td>
        </tr>
        </table>
        </ul> 
        <div style="width:100%; font-weight: bold;">
            <a href="javascript:loadMessageFromRequest(<?php echo $this->info_user['user_id']?>,'<?php echo selfURL(); ?>')"><?php echo $this->translate('Click here to view message from admin with your request(s)'); ?></a>
        </div>
        <div style="margin-top: 10px;" id="message_request_<?php echo $this->info_user['user_id']?>">
        </div> 
        </td>
        <td style="width: 50%; margin-top: 8px; vertical-align: top; padding-left: 15px;">
        <h3  style="margin-bottom: 10px; margin-top: 9px;">
        <?php echo $this->translate('Sold Items Summary'); ?>
        </h3>
            <ul  align="right" class="global_form_box" style="background: none; padding: 0px; margin-bottom: 10px; overflow: auto;">
                <table cellpadding="0" cellspacing="0" width="100%">
                <tr style="background:#E9F4FA none repeat scroll 0 0;">
                    <td height="25px" width="60%" style="font-weight:bold;padding:2px 2px 2px 7px;"><?php echo $this->translate('Name'); ?></td>
                    <td style="font-weight:bold;"><?php echo $this->translate('Type'); ?></td>
                    <td style="font-weight:bold;"><?php echo $this->translate('Boughts'); ?></td>
                </tr>
                <?php   $index = 0;
                    foreach($this->HistorySeller as $iSong):  $index++;  ?>
                         <tr id="download_list_id_<?php echo $index?>">
                            <td width="70%" style="padding:7px;border-bottom:1px solid #E9F4FA;">
                            <div class="mp3_title_link">
                                <?php if ($iSong->album_title == ""): ?>
                                    <a href="javascript:;" class ='title_thongtin2' onClick="return openPage('<?php echo $this->url(array('album_id'=>$iSong->album_id,'song_id'=>$iSong->song_id), 'mp3music_album_song');?>',800,565)"><?php echo $iSong->title;?></a> 
                               <?php else: 
                               $album = Engine_Api::_()->getItem('mp3music_album', $iSong->album);?> <!-- Album Item-->
                               <a href="javascript:;"  class ='title_thongtin2'  onClick="return openPage('<?php echo $this->url(array('album_id'=>$iSong->album), 'mp3music_album');?>',800,565)"><?php echo $album->getTitle();?></a>  
                               <?php endif; ?>        
                            </div>  
                            </td> 
                            <td width="15%" style="padding:7px;border-bottom:1px solid #E9F4FA;">
                            <?php 
                             if ($iSong->album_title == ""):
                                echo $this->translate("Song");
                             else:
                                echo $this->translate("Album");
                             endif;
                            ?>
                            </td>   
                            <td  align="center" style="padding:7px;border-bottom:1px solid #E9F4FA;">
                                <?php echo $iSong->count; ?>

                            </td>
                              
                        </tr>
                    <?php endforeach; ?>                          
                <tr>

                <td style=" padding: 10px; text-align: right" colspan="3" align="right">
                     <?php echo  $this->paginationControl($this->HistorySeller); ?>
                </td>
               

             </tr>
            </table>
                              
            </ul>
        </td>
            </tr>
            </table>
</div>
