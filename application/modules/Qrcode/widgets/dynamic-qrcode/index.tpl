
		<div class="image-field">
			<h3>Dynamic QR Code For URL</h3>
		
			<div id="qrCode" class="shadow"></div>
			
			<div id="download" style="margin-top: 10px;"></div>
						
			<div id="display">
				<input type="checkbox" name="display" id="disp"><?php echo $this->translate('Display on profile');?>
			</div>
		</div>


		


<script type="text/javascript">

var fbShare = {
						url: '<?php echo  ( _ENGINE_SSL ? 'https://' : 'http://' ). $_SERVER['HTTP_HOST'].$this->url()?>',
						size: 'small',
						badge_text: 'C0C0C0',
						badge_color: 'CC00FF',
						google_analytics: 'false'
						}
						
var resp = {};


 
            resp.customVal = '<?php echo  ( _ENGINE_SSL ? 'https://' : 'http://' ). $_SERVER['HTTP_HOST'].$this->url()?>';
			console.log(resp.customVal);
			var request= new Request({
			url: en4.core.baseUrl+'qrcode/index/embeddedimage',
		    method: 'GET',
		    data: resp,
		    onSuccess: function( response ) 
		    {
		         		      
			    //$('qrimage').set('src',response);
		    	$('qrCode').set('html','<img alt="image" src='+response+'>');
		    	$('download').set('html','<a download="image" href='+response+' style="float:left;margin-left: 20px;"><img alt="Download" src="application/modules/Qrcode/externals/images/download_button.jpg">&nbsp;</a>');
					    
		    }, onFailure: function(){
		    	 console.log('failed');
		    }
	        });
	    	request.send();
	
	 
</script>
<script type="text/javascript" src="http://widgets.fbshare.me/files/fbshare.js"></script>