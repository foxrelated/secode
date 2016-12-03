<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js' ?>");
    SmoothboxSEAO.addStylesheets.push("<?php echo $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/calendar/styles.css' ?>");
</script>

<?php $this->headLink()
            ->appendStylesheet($this->layout()->staticBaseUrl
                    . 'application/modules/Siteeventticket/externals/styles/style_siteeventcoupon.css'); ?>
                    
<div class="siteevent_viewevents_head">
  <div class="fright">
    <a href='<?php echo $this->url(array('action' => 'manage', 'event_id' => $this->siteevent->event_id), 'siteeventticket_coupon', true) ?>' class='buttonlink siteevent_icon_edit'><?php echo $this->translate('Dashboard');?></a>
  </div>
  <?php echo $this->htmlLink($this->siteevent->getHref(), $this->itemPhoto($this->siteevent, 'thumb.icon', '', array('align' => 'left'))) ?>
  
  <h2>	
      <?php echo $this->siteevent->__toString() ?>	
      <?php echo $this->translate('&raquo; '); ?>
      <?php echo $this->htmlLink($this->siteevent->getHref(array('tab' => $this->eventProfileCouponTabId)), $this->translate('Coupons')) ?>
  </h2>    
</div>

<div class="siteevent_event_form">
    <?php echo $this->form->render($this);
    $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/scripts/ajaxupload.js');
    ?>
</div>

<script type="text/javascript">
    
    window.addEvent('domready', function() {

        var couponCodeContainer = $('coupon_code-element');
        var language = '<?php echo $this->translate($this->string()->escapeJavascript("Check Availability")) ?>';
        var newdiv = document.createElement('div');
        newdiv.id = 'coupon_code_varify';
        newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='couponCodeBlur();return false;' class='check_availability_button'>" + language + "</a> <br />";
        couponCodeContainer.insertBefore(newdiv, couponCodeContainer.childNodes[3]);
    });    
   
    function couponCodeBlur(post) {
			
        if ($('coupon_code_alert') == null) {
            var pageurlcontainer = $('coupon_code-element');
            var newdiv = document.createElement('span');
            newdiv.id = 'coupon_code_alert';
            
            if('undefined' === typeof post){
                    newdiv.innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/loading.gif" />';
                    pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[4]);
            }
            else{
             newdiv.innerHTML = '';
                    pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[4]);
            }
        }
        else {
            if('undefined' === typeof post)
                $('coupon_code_alert').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/loading.gif" />';
            else
                $('coupon_code_alert').innerHTML = '';
        }
        
        var url = '<?php echo $this->url(array('action' => 'coupon-code-validation' ), 'siteeventticket_coupon', true);?>';
        
		en4.core.request.send(new Request.JSON({
            url : url,
			method : 'get',
            data : {
                coupon_code : $('coupon_code').value,
                format : 'html'
            },

            onSuccess : function(responseJSON) {
                if (responseJSON.success == 0) {
                    if('undefined' === typeof post){
                        if ($('coupon_code_alert')) {
                            $('coupon_code_alert').innerHTML = responseJSON.error_msg;
                        }
                   }
                    else{
                        $('coupon_code_alert').innerHTML = responseJSON.error_msg;
                        if ($('coupon_code_alert')) {
                            $('coupon_code_alert').innerHTML = responseJSON.error_msg;
                        }
                        showdetail(false);
                    }
                }
                else{
                    if('undefined' === typeof post){
                        if ($('coupon_code_alert'))
                            $('coupon_code_alert').innerHTML = responseJSON.success_msg;
                    }
                    else{
                        if ($('coupon_code_alert')) {
                            $('coupon_code_alert').innerHTML = '';
                        }
                        showdetail(true);
                    }
                }
            }
		}));
	}   

    if($('discount_type')) {
		$('discount_type').addEvent('change', function(){
			if($('discount_type').value == 1){
                document.getElementById('price-wrapper').style.display = 'block';
                document.getElementById('rate-wrapper').style.display = 'none';
            }else{
                document.getElementById('price-wrapper').style.display = 'none';
                document.getElementById('rate-wrapper').style.display = 'block';
            }
		});
        
		window.addEvent('domready', function() {
			if($('discount_type').value == 1){
                document.getElementById('price-wrapper').style.display = 'block';
                document.getElementById('rate-wrapper').style.display = 'none';
            }else{
                document.getElementById('price-wrapper').style.display = 'none';
                document.getElementById('rate-wrapper').style.display = 'block';
            }
		});
	}
  
    window.addEvent('domready', function() {
   
        if($('end_settings-1').checked) {
            document.getElementById("end_time-wrapper").style.display = "block";
        }
    });
  
    var endsettingss = 0;
    function updateTextFields(value) {
        if (value == 0 && $("end_time-wrapper")){
            $("end_time-wrapper").style.display = "none";
        } else if (value == 1 && $("end_time-wrapper")){ 
            $("end_time-wrapper").style.display = "block";
        }
    }
  
    en4.core.runonce.add(updateTextFields(endsettingss));
</script>