 <a name="ratecampaign"></a>
 <div <?php if ($this->can_rate): ?> onmouseout="campaignrating_mouseout()"  <?php endif;?>  id="campaign_rate" class="ynFRaising_campaignRate">
        <?php for($i = 1; $i <= 5; $i++): ?>
          <img id="campaign_rate_<?php print $i;?>"  <?php if ($this->can_rate): ?> style="cursor: pointer;" onclick="campaignrate(<?php echo $i; ?>);" onmouseover="campaignrating_mousehover(<?php echo $i; ?>);"<?php endif; ?> src="application/modules/Ynfundraising/externals/images/<?php if ($i <= $this->avgrating): ?>star_full.png<?php elseif( $i > $this->avgrating &&  ($i-1) <  $this->avgrating): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" />
        <?php endfor; ?>
  </div>
 <div class="ynFRaising_campaignRate"><?php echo $this->translate(array("(%s rate)","(%s rates)",$this->totalRates),$this->totalRates)?></div>
<script type="text/javascript">
    var img_star_full = "application/modules/Ynfundraising/externals/images/star_full.png";
    var img_star_partial = "application/modules/Ynfundraising/externals/images/star_part.png";
    var img_star_none = "application/modules/Ynfundraising/externals/images/star_none.png";  
    
   var campaignrating_mousehover = function(rating) {
        for(var x=1; x<=5; x++) {
          if(x <= rating) {
            $('campaign_rate_'+x).src = img_star_full;
          } else {
            $('campaign_rate_'+x).src = img_star_none;
          }
        }
    }
   var campaignrating_mouseout = function() {
        for(var x=1; x<=5; x++) {
          if(x <= <?php echo $this->avgrating ?>) {
            $('campaign_rate_'+x).src = img_star_full;
          } else if(<?php echo $this->avgrating ?> > (x-1) && x > <?php echo $this->avgrating ?>) {
            $('campaign_rate_'+x).src = img_star_partial;
          } else {
            $('campaign_rate_'+x).src = img_star_none;
          }
        }
    }
   var campaignrate = function(rates){
        window.location = en4.core.baseUrl + 'fundraising/campaign-rate/campaignId/<?php echo $this->campaign->getIdentity()?>/rates/'+rates;
      }
  
</script>