<?php
$this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Groupbuy/externals/styles/layout.css')
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Groupbuy/externals/styles/jd.gallery.css')
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Groupbuy/externals/styles/ReMooz.css');
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Groupbuy/externals/scripts/jd.gallery.js')
        ->appendFile($this->baseUrl() . '/application/modules/Groupbuy/externals/scripts/jd.gallery.transitions.js')
        ->appendFile($this->baseUrl() . '/application/modules/Groupbuy/externals/scripts/ReMooz.js');
?>
<?php if(!$this->deal): ?>              
       <div class="tip" style="clear: inherit;">
      <span>
           <?php echo $this->translate('There is no featured deal yet.');?>
      </span>
           <div style="clear: both;"></div>
    </div>
<?php else:?>
  <script type="text/javascript">
  
    function startGallery() {
        var myGallery = new gallery($('myGallery'), {
            timed: 2000,
            defaultTransition: "fadeslideleft",
           // useReMooz: true,
            embedLinks: false
        });
    }
    window.addEvent('domready', startGallery);
    var Timer;
    var TotalSeconds;


    function CreateTimer(TimerID, Time) {
        Timer = document.getElementById(TimerID);
        TotalSeconds = Time;
        
        UpdateTimer()
        window.setTimeout("Tick()", 1000);
    }

    function Tick() {
        if (TotalSeconds == 0) {
         	alert("Time for this deal is up!");
            location.reload(true);
            return;
        }
    	
    	TotalSeconds -= 1;
        UpdateTimer()
        window.setTimeout("Tick()", 1000);
    }


    function UpdateTimer() {
        var Seconds = TotalSeconds;
        
        var Days = Math.floor(Seconds / 86400);
        Seconds -= Days * 86400;

        var Hours = Math.floor(Seconds / 3600);
        Seconds -= Hours * (3600);

        var Minutes = Math.floor(Seconds / 60);
        Seconds -= Minutes * (60);


        var TimeStr = ((Days > 1) ? Days + " <?php echo $this->translate('Days '); ?>" : ((Days > 0) ? Days + " <?php echo $this->translate('Day'); ?> " : "")) + LeadingZero(Hours) + ":" + LeadingZero(Minutes) + ":" + LeadingZero(Seconds);


        Timer.innerHTML = TimeStr;
    }


    function LeadingZero(Time) {

        return (Time < 10) ? "0" + Time : + Time;

    }
</script>
<ul class="generic_list_widget">
<div class = "groupbuy_feature_title" style ="font-size: 1.1em;">
	<?php echo $this->htmlLink($this->deal->getHref(),$this->deal->getTitle())?>
</div>
<div class="groupbuy_browse_info" style="padding-left:10px; padding-bottom:10px;">
	<p class='groupbuy_browse_info_date'>
		<?php echo $this->timestamp($this->deal->creation_date) ?>
	    <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($this->deal->getOwner()->getHref(), $this->deal->getOwner()->getTitle()) ?>
	</p>
</div>
<div class = "groupbuy_feature_slideshow">
<div class = "groupbuy_feature_left">
<div class = "groupbuy_valueprice">
	<div class="groupbuy_valueprice_price">
	<div class = "groupbuy_valueprice_price_price">	
	<?php echo $this->currencyadvgroup($this->deal->price, $this->deal->currency) ?>
	</div>
	 <?php if($this->deal->status > 30 || $this->deal->end_time <= date("Y-m-d H:m:i")): ?>
   <img class="button_buy" src = "application/modules/Groupbuy/externals/images/no_longer.png">
     <?php  elseif($this->deal->status == 30 && $this->deal->published == 20 && $this->deal->stop == 0):?>
	<!--  <a  href = "<?php echo $this->url(array('action'=>'buy-deal' , 'deal' => $this->deal->getIdentity()), 'groupbuy_general', true) ;?>"> <img class="button_buy" src = "application/modules/Groupbuy/externals/images/link_buy.png">
	</a> -->
<div class = "imgbutton">
	<a  class = "txtbuybutton" href = "<?php echo $this->url(array('action'=>'buy-deal' , 'deal' => $this->deal->getIdentity()), 'groupbuy_general', true) ;?>"> <?php echo $this->translate('Buy');?> </a>
	</div>
	
      <?php else: ?>
    <img class="button_buy" src = "application/modules/Groupbuy/externals/images/link_buy.png">
    <?php endif; ?>
	</div>
	
	
</div>
	<div class = "groupbuy_valueprice_info">
		<dl class = "groupbuy_valueprice_block" id="groupbuy_value_price_block">	
			<dt class = "fixcss"><?php echo $this->translate('Value ');?></dt> 
			<dd><?php echo $this->currencyadvgroup($this->deal->value_deal, $this->deal->currency)?></dd>
		</dl>
		<dl class = "groupbuy_valueprice_block" id="groupbuy_discount_block">	
			<dt><?php echo $this->translate('Discount'); ?></dt> 
			<dd><?php echo $this->deal->discount."%";?></dd>
		</dl>
		<dl class = "groupbuy_valueprice_block" id="groupbuy_save_price_block">	
			<dt><?php echo $this->translate('You Save');?></dt> 
			<dd><?php echo $this->currencyadvgroup($this->deal->value_deal - $this->deal->price, $this->deal->currency)?></dd>
		</dl>
	</div>
<div class="groupbuy_buy_for_friend">
    <div class="groupbuy_buy_it">
    <?php  if($this->deal->status == 30 && $this->deal->published == 20 && $this->deal->stop == 0 && $this->deal->price != 0 && $this->deal->method != 2): ?>
    <a href = "<?php echo $this->url(array('action'=>'buy-deal' , 'deal' => $this->deal->getIdentity(), 'buyff' => '1'), 'groupbuy_general', true) ;?>">
    <?php echo $this->translate('Buy it for friend');?> 
    </a>
    <?php else : ?>
    <?php echo $this->translate('Buy it for friend');?>
    <?php endif;?>
    </div>
</div>
<?php  if($this->deal->stop == 0):?>
<div class = "groupbuy_timerleft">
	<?php if($this->deal->status > 30 || $this->deal->end_time <= date("Y-m-d H:i:s")): ?>
    <div class = "groupbuy_timertext">
        <?php echo $this->translate('This deal ended at:');?>
    </div>
    <div class = "groupbuy_timercountdown" style="font-size: 16pt;" > 
        <?php echo $this->deal->end_time?>
    </div>
    
        <?php elseif($this->deal->status <20 || $this->deal->published <20 ): ?>
    <div class = "groupbuy_timertext">
        <?php //echo $this->translate('This deal will be available at:');?>
    </div>
    <div class = "groupbuy_timercountdown" > 
        <?php 
                 
        echo $this->translate('This deal has not been approved yet!')?>    
        
        </div>
        <?php elseif($this->deal->status == 20 && $this->deal->start_time >= date("Y-m-d H:i:s")): ?>
    <div class = "groupbuy_timertext">
        <?php echo $this->translate('This deal will be available at:');?>
    </div>
    <div class = "groupbuy_timercountdown" > 
        <?php 
                 $options = array();
         $options['format'] = 'Y-M-d H:m:s';
        echo $this->locale()->toDateTime($this->deal->start_time, $options)?>    </div>
<?php  else:?>
    <div class = "groupbuy_timertext">
        <?php echo $this->translate('Time Left To Buy');?>
    </div>
    <div class = "groupbuy_timercountdown" id="timer">  
        <script type="text/javascript">
        CreateTimer("timer",<?php echo $this->difference;?>);
        </script>
    </div> 
<?php endif; ?>
</div>
<?php  else:?>
<div class = "groupbuy_timerleft">
     <div class = "groupbuy_timertext">
        <?php echo $this->translate('This deal was stopped');?>
    </div>
    <div class = "groupbuy_timercountdown" style="font-size: 16pt;" > 
        <?php echo $this->translate("Stopped")?>
    </div>
</div>
<?php endif; ?>
<div class = "groupbuy_timerleft">
	<div class = "groupbuy_timercountdown">
		<?php echo $this->deal->current_sold; echo $this->translate(' Bought')?>
	</div>
	 <?php if ($this->deal->current_sold < $this->deal->min_sold): 
    ?>	
    <div class="groupbuy_number_bought">
        <div id="groupbuy_percent_complete" style="width: <?php 
                if ($this->deal->current_sold <= $this->deal->min_sold) {
        echo (($this->deal->current_sold/$this->deal->min_sold)*100);
        }
        else { echo "100"; };
        ?>%;">
            <div class="percent"></div>
        </div>
        <div class="one"></div>
        <div class="many"></div>
    </div>
     <?php else :
    ?> 
    <div class = "groupbuy_boughtleft">
    	<div>
    		<?php echo $this->translate('The deal is on!');?>
    	</div>
    </div>
    <?php endif;?>
	<div class = "groupbuy_boughtleft">
	 <?php if($this->deal->status > 30 || $this->deal->end_time <= date("Y-m-d H:m:i")): ?>
        <?php  if($this->deal->min_sold > $this->deal->current_sold):?>
          <?php //echo $this->translate('The deal is on!')?>
        <?php //else:?>
         <?php echo $this->translate('The deal is off!')?>
        <?php  endif;?>
    <?php  else:?>
      <?php if($this->deal->min_sold > $this->deal->current_sold): ?>
        <div><?php echo ($this->deal->min_sold - $this->deal->current_sold); echo $this->translate(' more needed to get the deal'); ?></div>
     <?php   endif; ?>
    <?php endif; ?>
	</div>
</div>

            </div>

            <div id="myGallery">
            <?php if($this->main_photo):?>
                <div class="imageElement">
                    <h3><?php echo $this->main_photo->image_title; ?></h3>
                    <p><?php echo $this->main_photo->image_description ?></p>
                    <a href="<?php echo $this->main_photo->getPhotoUrl() ?>" title="open image" class="open"></a>
                    <img src="<?php echo $this->main_photo->getPhotoUrl('thumb.profile') ?>" class="full" />
                    <img src="<?php echo $this->main_photo->getPhotoUrl('thumb.normal') ?>" class="thumbnail" />
                </div>
             <?php endif; ?>
             <?php foreach($this->paginator as $photo ): ?>
            <?php if($this->deal->photo_id != $photo->file_id):?>
            <div class="imageElement">
                    <h3><?php echo $photo->image_title; ?></h3>
                    <p><?php echo $photo->image_description ?></p>
                    <a href="<?php echo $photo->getPhotoUrl() ?>" title="open image" class="open"></a>
                    <img src="<?php echo $photo->getPhotoUrl('thumb.profile') ?>" class="full" />
                    <img src="<?php echo $photo->getPhotoUrl('thumb.normal') ?>" class="thumbnail" />
                </div>
            <?php endif; ?>
          <?php endforeach;?>
            </div>
   <div class="groupbuy_feature_content">
	<div class="groupbuy_feature_fineprint">
		<div class ="groupbuy_feature_fineprint_title"><?php echo $this->translate('The Fine Print');?></div>
          <div><?php echo $this->deal->fine_print;?></div>
  	</div>
	<div class="groupbuy_feature_hightlight">
		<div class ="groupbuy_feature_fineprint_title"><?php echo $this->translate('Highlight Features');?></div>
		<div> <?php echo $this->deal->features;?> </div>
	</div>
    <div class="groupbuy_view_more">
            <a href="<?php echo $this->deal->getHref() ?>"><?php echo $this->translate("&rsaquo; View More")?></a>
    </div>
	</div>
</div>
</ul>

  <script type="text/javascript">
  
    var img_star_full = "application/modules/Groupbuy/externals/images/star_full.png";
    var img_star_partial = "application/modules/Groupbuy/externals/images/star_part.png";
    var img_star_none = "application/modules/Groupbuy/externals/images/star_none.png";  
    
    function rating_mousehover(rating) {
        for(var x=1; x<=5; x++) {
          if(x <= rating) {
            $('rate_'+x).src = img_star_full;
          } else {
            $('rate_'+x).src = img_star_none;
          }
        }
    }
     function canNotRate(status) {
     if(status == 1)
     {
            $('mess_rate').innerHTML = 'You have just rated this deal or you are the creator of this deal!';
     }
     else
     {
         $('mess_rate').innerHTML = "";
     }
     }
    function rating_mouseout() {
        for(var x=1; x<=5; x++) {
          if(x <= <?php echo $this->deal->rates ?>) {
            $('rate_'+x).src = img_star_full;
          } else if(<?php echo $this->deal->rates ?> > (x-1) && x > <?php echo $this->deal->rates ?>) {
            $('rate_'+x).src = img_star_partial;
          } else {
            $('rate_'+x).src = img_star_none;
          }
        }
    }
    function rate(rates){
        $('deal_rate').onmouseout = null;
        window.location = en4.core.baseUrl + 'group-buy/rate/deal_id/<?php echo $this->deal->getIdentity();?>/rates/'+rates;
      }
   
</script>
 <?php endif; ?>

<style type="text/css">
div.imgbutton {
    background-image:url(application/modules/Groupbuy/externals/images/buybg.png);
	background-repeat:no-repeat;
    float: left;
    margin-top: 20px;
    padding-bottom: 10px;
	height:53px;
	padding-left: 22px;
    width: 100px;
}
a.txtbuybutton {
	color: #FFFFFF;
	text-decoration:none;
	font-size: 2.8em;
    font-weight: bold;
}
</style>