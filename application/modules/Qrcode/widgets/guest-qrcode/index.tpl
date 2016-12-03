<script type="text/javascript">

var fbShare = {
                        url: '<?php echo  ( _ENGINE_SSL ? 'https://' : 'http://' ). $_SERVER['HTTP_HOST'].$this->url()?>',
						size: 'small',
						badge_text: 'C0C0C0',
						badge_color: 'CC00FF',
						google_analytics: 'false'
						}
						
</script>

		<div class="image-field">
			<h3>QR Code For Guest</h3>
		    <input value="http://www.ipragmatech.com" type="text" id="custom_url">
			<div id="qrCode" class="shadow">
			
			</div>
			
			<div id="download" style="margin-top: 10px;">
			</div>
			
			
			
			<div id="display">
				<input type="checkbox" name="display" id="disp"><?php echo $this->translate('Display on profile');?>
			</div>
					
			<div id="create">
				<button name="Submit" id="generate" type="button" style="margin-right:33px;"><?php echo $this->translate('Generate QR Code');?></button>
			</div>
			
		</div>


		
<script>

$('generate').addEvent('click',function( event )
		{
		
			var customValue= $('custom_url').value;
			var request= new Request({
			url: en4.core.baseUrl+'qrcode/index/embeddedimage',
		    method: 'GET',
		    data: {"customVal" : customValue},
		    onSuccess: function( response ) 
		    {
		         		      
			    //$('qrimage').set('src',response);
			    $$('.facebook-share').show();
		    	$('qrCode').set('html','<img alt="image" src='+response+'>');
		    	$('download').set('html','<a download="image" href='+response+' style="float:left;margin-left: 20px;"><img alt="Download" src="application/modules/Qrcode/externals/images/download_button.jpg">&nbsp;</a>');
					    
		    }, onFailure: function(){
		    	 console.log('failed');
		    }
	        });
	    	request.send();
});	
 
</script>
<div class="facebook-share" style="display:none;">
			<script type="text/javascript" src="http://widgets.fbshare.me/files/fbshare.js"></script>
			</div>