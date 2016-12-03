<div class="headline">
	<h2>
	<?php echo $this->translate('My Settings');?>
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

	<?php //echo $this->form->render($this) ?>

<div>
	<div>
		<h3><?php echo $this->translate('Create a new QR Code');?></h3>
	</div>
	<div id="option-text">
		<p><?php echo $this->translate('Choose option on screen to create QR code');?></p>

		<div class="image-field">
			<div id="qrCode" class="shadow">
				
			</div>
			<div>
			
			</div>
			<div id="display">
				<input type="checkbox" name="display" id="disp"><?php echo $this->translate('Display on profile');?>
			</div>
		</div>


		<div id="right-content">
			<div id="msg">
				<input type="hidden" id="msg">
			</div>
			<fieldset id="radio">
			      <?php $count = 0; ?>
			      <?php $tempVar = 0; ?>
			        <?php foreach($this->values as $value): ?>
			            <?php  if($count==2){
			               			$count++;
			               			$tempVar = $count;
			               		} 
			               		if($count==0 && $value['label']=='phone'){
			               			$count++; 
			               			$tempVar = 1;
			              		 } 
				               if($count==0 && $value['label']=='contact'){
				               		$count = $count +3 ;
				               		$tempVar = 3;
				               } 
				               if($count==1 && $value['label']=='contact'){
				               		$count = $count +2;
				               		$tempVar = 3;
				               }  
				               if(strtolower($value['label']) == 'website'){
				               		$tempVar = 0;
				               }
				               if(strtolower($value['label']) == 'phone'){
				               		$tempVar = 1;
				               }
				                if(strtolower($value['label']) == 'contact'){
				               		$tempVar = 3;
				               }
			            ?>           
			          			<input type="radio" name="field" id="field-<?php echo $tempVar; ?>" value="<?php echo $tempVar; ?>"> 
			          			<label	for="field-0" checked=true><?php echo $this->translate($value['label']);?> </label>
								<?php $count++; ?>
			        <?php endforeach; ?> 
			        <?php if($this->user_status->status): ?><input	type="radio" name="field" id="field-2" value="2"> <label for="field-2"><?php echo $this->translate('Status');?></label><?php endif; ?>	
					<input type="radio" name="field" id="field-4" value="4"> <label for="field-4"><?php echo $this->translate('Profile link');?></label>
					<input type="radio" name="field" id="field-5" value="5" > <label for="field-5"><?php echo $this->translate('Custom url');?></label>
			</fieldset>
			<div id="noinfo">

			</div>
			<div class="info" class="layout-right">

				<h1>
					<div id="infoheading"></div>
				</h1>
					<div id="infocontent"></div>
				<div id="create">
					<button name="Submit" id="generate" type="button"><?php echo $this->translate('Generate QR Code');?></button>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
var resp;
var resp_custom_url;
en4.core.runonce.add(function() {
	var request= new Request.JSON({
	url: en4.core.baseUrl+'qrcode/index/previousimage',
    method: 'GET',
    onSuccess: function( response ) 
    {
       resp_custom_url=response.custom_url;     
       var field='field-'+response.field;
       populateQRCodeFields(response.field);
       // $('qrimage').set('src','public/user/'+response.image_url);	
       if(response.image_url == null){
        $('qrCode').set('html',"<img src='application/modules/Qrcode/externals/images/cross.jpg'>");
       }
       else{
//       $('download').set('html','<a download="image" href='+response+' style="float:left"><img src="application/modules/Qrcode/externals/images/download_button.jpg">&nbsp;</a>');
        $('qrCode').set('html','<img src=public/user/'+response.image_url+'>');
        }
        $(field).set('checked',true);
	    if(response.display == 1){
	   			 $('disp').set('checked',true);
	    }
	    else
	    	 $('disp').set('checked',false);
	   }, onFailure: function(){
    	 console.log('failed');
    }
    });
	request.send();
	 
});
en4.core.runonce.add(function() {
 	
});

function populateQRCodeFields(value)
{
	
		console.log(value);
		var request= new Request.JSON({
		url: en4.core.baseUrl+'qrcode/index/userinfo',
		method: 'GET',
		data: {"qrtype":value},
		
		onSuccess: function( response ) 
		{
			if(response.userdata == '')
			{
			
				if(response.qrtype == 5)
					{
					$('noinfo').hide();
					$('infocontent').innerHTML = "<div><input type='text' id='custom_url'/><p>Ex:- http://www.ipragmatech.com</p></div>";
					$('custom_url').value = resp_custom_url;	
					console.log(resp_custom_url);
					$('create').show();
					
					 resp = response;
					}
				else
				{
		
					$('noinfo').show();
					$('infocontent').innerHTML = "";
			      	$('noinfo').set('html','<a href="<?php echo Zend_Controller_Front::getInstance()->getBaseUrl(); ?>/members/edit/profile">No value in the field.Please edit your profile</a>');
					$('create').hide();
					
				}
			}
			else{
			    $('create').show();
				var userData = response.userdata;
		    	var heading = response.heading;
		    	var hash = new Hash(userData);
		    	console.log(response.userdata);
		    	$('noinfo').hide();
			$('infocontent').innerHTML = "";
			var myTable = new HtmlTable({
			    properties: {
			        border: 1,
			        cellspacing: 8
			    },
			    headers: [heading+" "+'Information'],
			    
			});
			hash.each(function(value, key){
				myTable.push([key,value]);
				});
			
			
			myTable.inject($('infocontent'));
					       resp = response;
					    
					     			    	}
		   },
		
		
		});
		request.send();
}


$$('input[name=field]').addEvent('click',function( event )
		{
			var value = this.value;
			$('msg').hide();
			$('display').hide();
			populateQRCodeFields(value);	
		});

     $('generate').addEvent('click',function( event )
		{
			if($('custom_url'))
			{
		    	var customValue= $('custom_url').value;
		    	$('display').show();
		    }
		  
			$('display').show();
			//$('msg').show();
			resp.customVal = customValue;
			
			$('disp').set('checked',false);
		    var request= new Request({
			url: en4.core.baseUrl+'qrcode/index/embeddedimage',
		    method: 'GET',
		    data: resp,
		    onSuccess: function( response ) 
		    {
		         		      
			    //$('qrimage').set('src',response);
		    	$('qrCode').set('html','<img src='+response+'>');
		    	//$('download').set('html','<a download="image" href='+response+' style="float:left"><img src="application/modules/Qrcode/externals/images/download_button.jpg">Download</a>');
					    
		    }, onFailure: function(){
		    	 console.log('failed');
		    }
	        });
	    	request.send();
	    		});

      $$('input[name=display]').addEvent('click',function( event )
		{
			$('msg').show();
			$('msg').set('html','QR Code generated successfully');
		    var request= new Request.JSON({
			url: en4.core.baseUrl+'qrcode/index/displaycheck',
		    method: 'GET',
		    data: {"display":this.checked},
		    onSuccess: function( response ) 
		    {
			   	$('msg').set('html','QR Code generated successfully');
		    
		    },

		});
	request.send();
		});
function showText()
{
}
function hideText()
{
}

</script>
