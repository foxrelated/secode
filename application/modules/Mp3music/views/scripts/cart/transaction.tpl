<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');   
       ?>
<table cellpading="0" cellspacing="0" border="0" width="100%">
     <tr>
         <td>
             <div class="box_ys2" id="song_list_frame">
               <div class="top_right_box" >
                  <div class="top_left_box" ></div>
                 <div class="title_box" style="padding-top:7px; padding-left:2px"><?php echo $this->translate('Transaction Listing');?></div>    
                 </div>    
                                      
               <div class="t">
                  <div class="l">
                      <div class="r" style="padding:1px">
                  <div> 
                      <table cellpadding="0" cellspacing="0" width="100%">
                          <tr style="background:#2C2C2C none repeat scroll 0 0;">
                             
                              <td height="25px" width="10%" style="font-weight:bold;color:#FFF;padding:2px 2px 2px 7px;"><?php echo $this->translate('Date');?>  </td>
                              <td class= "head_seller" style="font-weight:bold;color:#FFF;padding:2px;text-align:center"><?php echo $this->translate('Seller');?>  </td>
                              <td style="font-weight:bold;color:#FFF;padding:2px;text-align:center"><?php echo $this->translate('Buyer');?>  </td>
                              <td width="10%" style="font-weight:bold;color:#FFF;padding:2px;text-align:center"><?php echo $this->translate('Item ID');?>  </td>
                              <td  width="15%" style="font-weight:bold;color:#FFF;padding:2px;text-align:center"><?php echo $this->translate('Item Type');?>  </td>
                              <td  width="10%" style="font-weight:bold;color:#FFF;padding:2px;text-align:center"><?php echo $this->translate('Amount');?>  </td>
                              <td class = "head_type"  width="10%"style="font-weight:bold;color:#FFF;padding:2px;text-align:center"><?php echo $this->translate('Type Tracking');?>  </td>
                              <td  width="10%" style="font-weight:bold;color:#FFF;padding:2px;text-align:center"><?php echo $this->translate('Status');?>  </td>
                      
                          </tr>
                       <?php foreach($this->history as $track):?>
                          <tr style="border:1px solid">
                              <td class="stat_number" style="border: 1px solid black;text-align:center" text-align="center" ><?php echo $track->pDate ?> </td>
                              <td class="stat_number seller" style="border: 1px solid black;text-align:center" align="center" ><?php if($track->seller_user_name): echo $track->seller_user_name; else: echo "N/A"; endif;?> </td>
                              <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php if($track->buyer_user_name): echo $track->buyer_user_name; else: echo "N/A"; endif; ?></td>
                              <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php echo $track->item_id ?> </td>
                              <td class="stat_number" style="border: 1px solid black;text-align:center" align="center" ><?php if($track->item_type): echo $track->item_type; else: echo "N/A"; endif;?> </td>
                              <td class="stat_number" style="color: red;border: 1px solid black;text-align:center" align="center" ><?php echo $track->amount ?></td>
                              <!--<td class="stat_number" style="color: red;border: 1px solid black;" align="center" >{$track.account_seller_email} </td>   -->
                             <!-- <td class="stat_number" style="color: red;border: 1px solid black;" align="center" >{$track.account_buyer_email} </td>   -->         
                              <td class="stat_number tracking_type" style="border: 1px solid black;text-align:center" align="center" ><?php echo $this->translate($track->params) ?> </td>
                              <td class="stat_number" style="color: red;border: 1px solid black;text-align:center" align="center" ><?php if ($track->transaction_status == 1): echo $this->translate('Succ'); else: echo $this->translate('Fail'); endif; ?> </td>
                          </tr>
                     
                     <?php endforeach; ?>
                      </table>  
                  </div>
                     </div>
                  </div>
              </div>
               
               <div class="b">
                    <div class="l">
                        <div class="r">
                            <div class="bl">
                                <div class="br" style="height:7px">
                                </div>
                            </div>
                        </div>
                    </div>
               </div> 
              </div>
         </td>               
     </tr>
   </table>   
<?php echo  $this->paginationControl($this->history); ?>  
