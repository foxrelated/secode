<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<script type="text/javascript">

  var viewer = <?php echo $this->viewer_id;?>;
  var list_rate_previous = <?php echo $this->list->rating;?>;
  var listing_id = <?php echo $this->list->listing_id;?>;
  var list_total_rating = <?php echo $this->rating_count;?>;
  var list_rated = '<?php echo $this->list_rated;?>';
	var check_rating = 0;
	var current_total_rate;
	var rating_var = '<?php echo $this->string()->escapeJavascript($this->translate(" rating")) ?>';
	
  function rate(rating) {
	    $('rating_text').innerHTML = "<?php echo $this->translate('Thank you for rating to this Listing!');?>";
	    for(var x=1; x<=5; x++) {
	      $('rate_'+x).set('onclick', '');
	    }
	    (new Request.JSON({
	      'format': 'json',
	      'url' : '<?php echo $this->url(array('module' => 'list', 'controller' => 'index', 'action' => 'rating'), 'default', true) ?>',
	      'data' : {
	        'format' : 'json',
	        'rating' : rating,
	        'listing_id': listing_id
	      },
	      'onRequest' : function(){
	        list_rated = 1;
	        list_total_rating = list_total_rating + 1;
	        list_rate_previous = (list_rate_previous+rating)/list_total_rating;
	        list_set_rating();
	      },
	      'onSuccess' : function(responseJSON, responseText)
	      {
	        $('rating_text').innerHTML = responseJSON[0].total+rating_var;
	        current_total_rate = responseJSON[0].total;
	        check_rating = 1;
	      }
	    })).send();
	    
	  }
  
  function list_rating_out() {
	  $('rating_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    if (list_rate_previous != 0){
      list_set_rating();
    }
    else {
      for(var x=1; x<=5; x++) {
        $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
      }
    }
  }

  function list_set_rating() {
    var rating = list_rate_previous;
    if(check_rating == 1) {
      if(current_total_rate == 1) {
    	  $('rating_text').innerHTML = current_total_rate+rating_var;
      }
      else {      
		  	$('rating_text').innerHTML = current_total_rate+rating_var;
    	}
	  }
	  else { 
    	$('rating_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
	  }
    for(var x=1; x<=parseInt(rating); x++) {
      $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
    }

    for(var x=parseInt(rating)+1; x<=5; x++) {
      $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
    }

    var remainder = Math.round(rating)-rating;
    if (remainder <= 0.5 && remainder !=0){
      var last = parseInt(rating)+1;
      $('rate_'+last).set('class', 'rating_star_big_generic rating_star_big_half');
    }
  }

  function list_rating_over(rating) {
	    if (list_rated == 1){
	      $('rating_text').innerHTML = "<?php echo $this->translate('you have already rated this Listing');?>";
	      //list_set_rating();
	    }
	    else if (viewer == 0){
	      $('rating_text').innerHTML = "<?php echo $this->translate('Only logged-in user can rate');?>";
	    }
	    else{
	      $('rating_text').innerHTML = "<?php echo $this->translate('Please click to rate');?>";
	      for(var x=1; x<=5; x++) {
	        if(x <= rating) {
	          $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
	        } else {
	          $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
	        }
	      }
	    }
	  }
	  
  en4.core.runonce.add(list_set_rating);

</script>

<ul class="seaocore_sidebar_list">
  <li>
		<?php if(!empty($this->viewer_id)): ?>
			<div id="list_rating" class="rating" onmouseout="list_rating_out();">
				<?php for($i = 1; $i <= 5; $i++): ?>
				<span id="rate_<?php echo $i; ?>" <?php if (!$this->list_rated && $this->viewer_id):?>onclick="rate(<?php echo $i; ?>);"<?php endif; ?> onmouseover="list_rating_over(<?php echo $i; ?>);"></span>
				<?php endfor;?>
				<br/>
				<span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate');?></span>
			</div>
		<?php elseif($this->list->rating > 0): ?>
			<?php 
				$currentRatingValue = $this->list->rating;
				$difference = $currentRatingValue- (int)$currentRatingValue;
				if($difference < .5) {
					$finalRatingValue = (int)$currentRatingValue;
				}
				else {
					$finalRatingValue = (int)$currentRatingValue + .5;
				}	
			?>
			<div id="list_rating" class="rating list_rating" onmouseout="list_rating_out();">
				<?php for($i = 1; $i <= 5; $i++): ?>
					<span id="rate_<?php echo $i;?>" title="<?php echo $this->translate('%1.1f rating', $finalRatingValue); ?>"></span>
				<?php endfor;?>
				<br/>
				<span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate');?></span>
			</div>
		<?php endif; ?>	
  </li>
</ul>