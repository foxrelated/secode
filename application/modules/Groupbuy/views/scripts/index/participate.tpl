<div class="headline">
  <h2>
    <?php echo $this->translate('Auction');?>
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
<div class='layout_right'>
  <?php echo $this->form->render($this) ?>

  <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="quicklinks">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->quickNavigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>
<div class='layout_middle'> 
<?php
$homeres = $this->paginator; 
$user_id = $this->viewer_id;
function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
	  $http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'	;
      return $http.$_SERVER['HTTP_HOST'].$server_info."/";
 }      
 ?>
 <?php if(Count($homeres) <= 0): ?>              
       <div class="tip" style="clear: inherit;">
      <span>
           <?php echo $this->translate('There are no auctions.');?>
      </span>
           <div style="clear: both;"></div>
    </div>
<?php else: $index = 0; foreach($homeres as $data): $index++;?>
<div class="baselevel normallevel" style="width: 243px;;">
<div class="item" id ="itemindex<?php echo $index; ?>">
<div class="bb1" style="width: 238px;">
<div class="bb2">
<div class="bb3">
<div class="name" title="Click on the auction for more info" >
<span style="<?php if($data->bold == 1): ?>font-weight: bold;<?php endif;?><?php if($data->italic == 1): ?>font-style: italic;<?php endif;?><?php if($data->highlight == 1): ?>background-color: yellow;<?php endif;?>"><?php echo $this->htmlLink($data->getHref(), $data->getTitle()) ?> </span>
 </div>
 <div id="date" class="auctions_browse_date" style="margin: 4px 14px 4px 0px; color:#7F7F7F;font-size:8pt;">
  <?php $fullname = substr($data->getOwner()->getTitle(), 0, 10);
  if (strlen($data->getOwner()->getTitle())>10): $fullname = $fullname."..."; endif; ?>
         <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($data->getOwner()->getHref(), $fullname) ?>
        <?php echo $this->timestamp($data->creation_date) ?> 
        </div>
<div class="imgprice">
<div class="img">
<?php if($data->display_photo == 1 && $data->getPhotoUrl() != ""): ?>  
<a href="<?php echo $data->getHref()?>" title="Click on the auction for more info"><img src="<?php echo $data->getPhotoUrl("thumb.normal")?>" style = "max-width:100px;max-height:100px" /></a>
  <?php else: ?>
    <img src="./application/modules/Auction/externals/images/nophoto_product_thumb_icon.png" title="<?php echo $this->product->title?>" style = "max-width:100px;max-height:100px" />                                                                                                             
  <?php endif; ?>
</div>

<div class="pricecont">
<div class="label"><?php echo $this->translate('Current price');?></div>
<div class="pricecur" id="pricecur<?php echo $index?>">
  <?php if($data->status != 0|| $data->stop == 1):
 echo "$"; 
  if($data->bid_price < $data->starting_bidprice): 
  $price = $data->starting_bidprice;
  else: $price = $data->bid_price; 
  endif;
  echo number_format($price,2); 
  endif;?>
</div>
<div class="label"><?php echo $this->translate('Increment price');?></div>
<div class="pricecur">
 <?php echo "$"; 
  $increment = Engine_Api::_()->getApi('settings', 'core')->getSetting('auction.increment', 1);
  echo number_format($increment,2); ?>
</div>
<?php if($data->featured == 1): ?>
<div class="pricecur"><img src="./application/modules/Auction/externals/images/featured.png" alt="featured" title="Featured"></div>
<?php endif; ?>
</div>
</div>
<div class="clockcont">
<br/>
<!--<div class="label" style="text-align: center" id = "label<?php echo $index?>"><?php echo $this->translate('Time left');?></div> -->
<div class="clock">
<!--<span class="bidinfo" id=""><img src="./application/modules/Auction/externals/images/clock.gif"></span>-->
<span style="color: #597E13;" id ="clocktime<?php echo $index?>">
 <?php if($data->status != 0):?>
   <font color="silver" style="font-weight: bold;"><?php echo $this->translate('Ended'); ?></font>
 <?php endif;?> 
 <?php if($data->stop == 1 && $data->status == 0):?>
   <font color="silver" style="font-weight: bold;"><?php echo $this->translate('Stopped'); ?></font>
 <?php endif;?>
</span></div>
</div>
<div class="btn btnnobuynow" id="btn<?php echo $index?>" >
<?php if($user_id > 0):
        $block =  Engine_Api::_()->getApi('settings', 'core')->getSetting('auction.block', 1);
          $charge =  Engine_Api::_()->getApi('settings', 'core')->getSetting('auction.chargebid', 1);
        $product = Engine_Api::_()->getItem('product', $data->product_id);      
        if($product->user_id == $user_id || ip2long($_SERVER['REMOTE_ADDR']) == $data->creation_ip ):
            if($data->status == 0):
                if($block == 0 || $data->stop == 1): ?> 
                    <div class="active" id="bid<?php echo $index?>" style="background-position: 0 -80px"> <?php echo $this->translate('Bid now');?></div>      
                <?php else: ?>
                     <?php 
                    $info_account = Auction_Api_Account::getCurrentAccount($user_id);   
                    if($charge == 0 || ($charge == 1 && $info_account['bids'] > 0)): ?>
                        <div class="active" id="bid<?php echo $index?>" style="background-position: 0 -120px"><a href="javascript:;" onclick="bid(<?php echo $data->product_id?>,<?php echo $index ?>);"> <?php echo $this->translate('Bid now');?></a></div>  
                    <?php else: ?>
                    <div class="active" id="bid<?php echo $index?>" style="background-position: 0 -120px"><a href="<?php echo selfURL(); ?>auctions/account/buybid" onclick="alert('You should buy the bid to can bid on this auction.');"> <?php echo $this->translate('Bid now');?></a></div> 
                    <?php endif; ?>
                <?php endif; ?>
            <?php elseif($data->status == 1): ?>  
                <div class="active" id="bid<?php echo $index?>" style="background-position: 0 -160px"><?php echo $this->translate('Won');?></div>  
            <?php elseif($data->status == 2): ?> 
                <div class="active" id="bid<?php echo $index?>" style="background-position: 0 -240px"><?php echo $this->translate('Sold');?></div>  
            <?php endif; ?>
     <?php elseif($data->stop == 1 && $data->status == 0): ?> 
            <div class="active" id="bid<?php echo $index?>" style="background-position: 0 -80px"> <?php echo $this->translate('Bid now');?></div>      
    <?php elseif($data->status == 0):?>
        <div class="active" id="bid<?php echo $index?>" style="background-position: 0 -120px"><a href="javascript:;" onclick="bid(<?php echo $data->product_id?>,<?php echo $index ?>);"> <?php echo $this->translate('Bid now');?></a></div>  
    <?php elseif($data->status == 1): ?>  
        <div class="active" id="bid<?php echo $index?>" style="background-position: 0 -160px"><?php echo $this->translate('Won');?></div>  
    <?php elseif($data->status == 2): ?> 
        <div class="active" id="bid<?php echo $index?>" style="background-position: 0 -240px"><?php echo $this->translate('Sold');?></div>  
    <?php endif;
else:
    if($data->stop == 1 && $data->status == 0): ?> 
        <div class="active" id="bid<?php echo $index?>" style="background-position: 0 -80px"> <?php echo $this->translate('Bid now');?></div>      
    <?php elseif($data->status == 0): ?>
        <div class="active" id="bid<?php echo $index?>"> <a href="login" onmouseover="this.innerHTML = 'Login'" onmouseout="this.innerHTML = 'Bid now'"><?php echo $this->translate('Bid now');?></a></div>
    <?php elseif($data->status == 1): ?>  
        <div class="active" id="bid<?php echo $index?>" style="background-position: 0 -160px"><?php echo $this->translate('Won');?></div>  
    <?php elseif($data->status == 2): ?> 
        <div class="active" id="bid<?php echo $index?>" style="background-position: 0 -240px"><?php echo $this->translate('Sold');?></div>  
    <?php endif;?>
<?php endif; ?>
</div>

<div class="lastbiddercont" id="winner<?php echo $index?>">
<img src="./application/modules/Auction/externals/images/icon_user.png"/>
<span class="biddername" style="background-image: none; background-color: transparent;">
<span class="lastbidder" id = "lastbidder<?php echo $index?>"> 
<?php               if($data->bider_id >= 0):
                    $bider = Engine_Api::_()->getItem('user', $data->bider_id);
                     if($bider->getIdentity() > 0):  ?>
                     <a href="<?php echo $bider->getHref(); ?>">
                      <?php  echo $bider->username;  ?> </a>
                       <?php  else:
                        echo $this->translate('Nobody');
                     endif;
                     else:
                        echo $this->translate('User have deleted');
                     endif;
                     ?></span></span>
</div>
</div>
<div class="auctions_browse_date" style="text-align: center" ><?php echo $this->translate('End time:')." ".$data->end_time."<br/>".$this->translate('Timezone:')." ".$data->timezone; ?></div>
</div>
</div>
</div>
</div>
 <?php endforeach; ?>
<script language="javascript">
var bid_increment = <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('auction.increment', 1) ?>;  
<?php $index = 0; foreach($homeres as $data): $index++;?>
<?php 
     if($data->bid_price < $data->starting_bidprice): 
      $price = $data->starting_bidprice;
      else: $price = $data->bid_price; 
      endif; ?>
var pricecur<?php echo $index?> = <?php echo $price ?>; 
<?php
     date_default_timezone_set($data->timezone);  
     $now = date('Y-m-d H:i:s');
if($data->start_time <= $now):
$bid = Engine_Api::_()->auction()->getBid($data->product_id);
         if($bid): 
            $timebid = $bid->bid_time;
            $subtime = mktime(date('H',strtotime($now)),date('i',strtotime($now)),date('s',strtotime($now)),date('d',strtotime($now)),date('m',strtotime($now)),date('Y',strtotime($now))) - mktime(date('H',strtotime($timebid)),date('i',strtotime($timebid)),date('s',strtotime($timebid)),date('d',strtotime($timebid)),date('m',strtotime($timebid)),date('Y',strtotime($timebid)));
            $time = $data->bid_time - $subtime%$data->bid_time;
        else:
            $time = $data->bid_time;
         endif;
        $min = floor($time/60);
        $sec = $time%60;
else:
  $start_time = $data->start_time;
  $time = mktime(date('H',strtotime($start_time)),date('i',strtotime($start_time)),date('s',strtotime($start_time)),date('d',strtotime($start_time)),date('m',strtotime($start_time)),date('Y',strtotime($start_time))) - mktime(date('H',strtotime($now)),date('i',strtotime($now)),date('s',strtotime($now)),date('d',strtotime($now)),date('m',strtotime($now)),date('Y',strtotime($now)));
  $min =  floor($time/60);
  $sec = $time%60;
endif;
        ?>
var mins<?php echo $index?> =  <?php echo  $min?>;
var secs<?php echo $index?> =  <?php echo  $sec?>;
var bid_time<?php echo $index?> = <?php echo $data->bid_time ?>;
var cd<?php echo $index?> = null; 
var up<?php echo $index?> = null; 
var flag<?php echo $index?> = false;  
var flagc<?php echo $index?> = false;  
var flagStart<?php echo $index?> = 0;
<?php endforeach;?> 
var temp = "";
var p;
var btnindex = 0;
function bid(auction_id,index)
{   
    temp = $('btn'+ index).innerHTML;
    btnindex = index;
    $('btn'+ index).innerHTML = '<div class="active"  style="background-position: 0 -240px"><?php echo $this->translate('Bid now');?></div>' ;
	var price = $('pricecur'+ index).innerHTML;
    var request = new Request.JSON({
            'method' : 'post',
            'url' :  '<?php echo $this->url(array('module' => 'auction', 'action' => 'bid'), 'auction_general') ?>',
            'data' : {
                'product_id' : auction_id,
                'price' : price            
            },
            'onComplete':function(responseObject)
            {  
                 redo(index,1);
                 p = setTimeout("viewBid()",1500);
            }
        });
        request.send();
		if(flagc<?php echo $index?> == false)
			flagStart<?php echo $index?> = 1;
}
function viewBid()
{
    $('btn'+ btnindex).innerHTML = temp;
    clearTimeout(p);
}
function update(index) {
 	//listting auctions
	<?php $index = 0; foreach($homeres as $data): $index++;
	if($data->bid_time > 0 && $data->display_home == 1 && $data->status == 0 && $data->stop == 0): ?>  
    if(index == <?php echo $index?> && flag<?php echo $index?> == false)
    { 
       
        var request<?php echo $index?> = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'auctions/update',
            'data' : {
                'product_id' : <?php echo $data->product_id ?>,
				'flagStart'  : flagStart<?php echo $index?>
            },
            'onComplete':function(responseObject)
            {  
                if(typeof(responseObject)=="object")
                {                               
                        if(responseObject.min != 0 || responseObject.sec != 0)
                       {                          
                           mins<?php echo $index?> = 1 * m(responseObject.min);
                           secs<?php echo $index?> = 0 + s(":"+responseObject.sec);
                       }
                       pricecur<?php echo $index?> =  responseObject.price;
                       if($('lastbidder<?php echo $index?>') && responseObject.username != "")
                         $('lastbidder<?php echo $index?>').innerHTML = responseObject.username; 
                       else if(responseObject.userDelete && responseObject.userDelete == 1)
                            $('lastbidder<?php echo $index?>').innerHTML = "<?php echo $this->translate('User have deleted'); ?>";
                        else
                          $('lastbidder<?php echo $index?>').innerHTML = "<?php echo $this->translate('Nobody'); ?>"; 
                       if(responseObject.flag == "1")
                            flagc<?php echo $index?> = true;
                       if(responseObject.remove == "1")
                            clearTimeout(cd<?php echo $index ?>);
                }
            }
        });
        request<?php echo $index?>.send();
        clearTimeout(cd<?php echo $index?>); 
        redo(<?php echo $index?>,0);      
        up<?php echo $index?> = setTimeout("update(<?php echo $index?>)",2000);   
    }  
 	<?php endif; endforeach;?>
     
}

function m(obj) {
 	for(var i = 0; i < obj.length; i++) {
  		if(obj.substring(i, i + 1) == ":")
  		break;
 	}
 	return(obj.substring(0, i));
}

function s(obj) {
 	for(var i = 0; i < obj.length; i++) {
  		if(obj.substring(i, i + 1) == ":")
  		break;
 	}
 	return(obj.substring(i + 1, obj.length));
}

function daysInMonth(month,year) {
    return new Date(year, month, 0).getDate();
}

function dis(mins,secs) {
     var disp = "";
     var d = new Date();
     var m = d.getMonth();
     var y = d.getFullYear();  
     if(mins >= 1440)
     {
         var day = Math.floor(mins/1440);
         if(Math.floor(day/daysInMonth(m,y)) > 0)
         {
             if(Math.floor(day/daysInMonth(m,y)) <= 9)
                disp =  "0" + Math.floor(day/daysInMonth(m,y)) + ":";
             else
                 disp = Math.floor(day/daysInMonth(m,y)) + ":";
         }
         mins = mins - day*1440; 
     }
     if(mins >= 60)
     {
         var h = Math.floor(mins/60);
         if(h <= 9)
            disp +=  "0" + h + ":";
         else
             disp += h + ":";
         mins = mins - h*60;
     }
     if(mins <= 9) {
          disp += "0";
     } else {
          disp += "";
     }
     disp += mins + ":";
     if(secs <= 9) {
          disp += "0" + secs;
     } else {
          disp += secs;
     }
     return(disp);
}
function numberFormat(nStr){
  nStr += '';
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1))
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
  return x1 + x2;
}
function price(prices) {
     var disp;
     var fprice = Math.floor(prices);
     if(fprice <= 9) {
          disp = "0";
     } else {
          disp = "";
     }
     disp += numberFormat(fprice) + ".";
   
     var sprice = roundNumber(Math.abs(prices - fprice)*100,2);
     if(sprice <= 9)
        disp += "0" + sprice;
     else
        disp +=  sprice;      
     return('$'+disp);
}
function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}
function redo(index,bid) {
 	<?php $index = 0; foreach($homeres as $data): $index++;
 	if($data->bid_time > 0):
 	?>
 	if(index == <?php echo $index?>)
 	{
        if(bid == 1)
        {
            var request<?php echo $index?> = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'auctions/update',
            'data' : {
                'product_id' : <?php echo $data->product_id ?>           
            },
            'onComplete':function(responseObject)
            {  
                if(typeof(responseObject)=="object")
                {                               
                       if(responseObject.min != 0 || responseObject.sec != 0)
                       {                          
                           mins<?php echo $index?> = 1 * m(responseObject.min);
                           secs<?php echo $index?> = 0 + s(":"+responseObject.sec);
                       }
                       pricecur<?php echo $index?> =  responseObject.price;
                       if(responseObject.username != "")
                        $('lastbidder<?php echo $index?>').innerHTML = responseObject.username; 
                       else if(responseObject.userDelete && responseObject.userDelete == 1)
                            $('lastbidder<?php echo $index?>').innerHTML = "<?php echo $this->translate('User have deleted'); ?>";
                       else
                        $('lastbidder<?php echo $index?>').innerHTML = "<?php echo $this->translate('Nobody'); ?>"; 
                }
            }
            });
            request<?php echo $index?>.send(); 
        }
        else
        {
	 	    secs<?php echo $index ?>--;
	 	    if(secs<?php echo $index ?> == -1) {
	  		    secs<?php echo $index ?> = 59;
	  		    mins<?php echo $index ?>--;
	 	    }
             $('pricecur<?php echo $index ?>').innerHTML = price(roundNumber(pricecur<?php echo $index ?>,2));
	 	   if(flagc<?php echo $index ?> == true)
           {
                $('clocktime<?php echo $index ?>').innerHTML =  "<span style='color:#666666'>"+ dis(mins<?php echo $index?>,secs<?php echo $index?>) + "</span>";
              // $('label<?php echo $index ?>').innerHTML = "<?php echo $this->translate('Time to start');?>";
           }
           else if(secs<?php echo $index ?> <=  10 && mins<?php echo $index ?> == 0)
			{
              $('clocktime<?php echo $index ?>').innerHTML =  "<span style='color:#CF0000'>"+ dis(mins<?php echo $index?>,secs<?php echo $index?>) + "</span>"; 
			} 
	 	   else
                $('clocktime<?php echo $index ?>').innerHTML = "<span style='color:#597E13'>"+  dis(mins<?php echo $index?>,secs<?php echo $index?>) + "</span>";
	 	    if((mins<?php echo $index ?> == 0) && (secs<?php echo $index ?> == 0) && flagc<?php echo $index ?> == false) {
	 		     flag<?php echo $index?> = true;
                clearTimeout(cd<?php echo $index ?>);
                $('btn<?php echo $index ?>').innerHTML = '<div class="active" style="background-position: 0 -240px"><?php echo $this->translate('Won');?></div>';   
                // $('label<?php echo $index ?>').innerHTML = "";            
                 $('clocktime<?php echo $index ?>').innerHTML = '<font color="silver" style="font-weight: bold;"><?php echo $this->translate('Ended'); ?></font>';
                var request0 = new Request.JSON({
                'method' : 'post',
                'url' :  en4.core.baseUrl + 'auctions/win',
                'data' : {
                    'product_id' : <?php echo $data->product_id ?>            
                }
                });
                 request0.send();         
		      } else if(mins<?php echo $index ?> == 0 && secs<?php echo $index ?> == 0 && flagc<?php echo $index ?> == true)
			 {
			    mins<?php echo $index?> = Math.floor(bid_time<?php echo $index?>/60);
				secs<?php echo $index?> = bid_time<?php echo $index?>%60;  
				cd<?php echo $index ?> = setTimeout("redo(<?php echo $index ?>,0)",1000);
				flagc<?php echo $index ?>  = false;
			 }
			 else {
	 		    cd<?php echo $index ?> = setTimeout("redo(<?php echo $index ?>,0)",1000);
	 	    }
        }
 	}
 	<?php endif; endforeach;?>
 	
}

function init() {
    <?php $index = 0; foreach($homeres as $data): $index++;?>   
        update(<?php echo $index ?>);
  <?php endforeach;?>
   if($('secactive'))     
        updateFeatured(); 
}
window.onload = init;
</script>
 <?php  endif; ?>
 <br/>
<div>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination/auctionpagination.tpl","auction"), array("orderby"=>$this->orderby)); ?>                                                                                                                                                                               
</div>
 <script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
</script>
</div>

