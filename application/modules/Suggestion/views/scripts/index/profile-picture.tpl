<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: profile-picture.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form suggestion-profile-image">
	<div>
		<div>
			<?php
			  $this->headScript()
			    ->appendFile($this->layout()->staticBaseUrl.'externals/mootools/mootools-1.2.2-core-nc.js')
			    ->appendFile($this->layout()->staticBaseUrl.'externals/moolasso/Lasso.js')
			    ->appendFile($this->layout()->staticBaseUrl.'externals/moolasso/Lasso.Crop.js')
			?>
			<h4><?php echo $this->translate("Suggest profile picture for ") . $this->displayname; ?></h4>
			<?php
			  	// Big Image 
			    if (isset($this->image_name)){
			      echo '<img src="' . $this->layout()->staticBaseUrl . 'public/temporary/p_' . $this->image_name . '" alt="" id="lassoImg" />';
			    }
			    else
			      echo '<img src="'. $this->layout()->staticBaseUrl . 'application/modules/User/externals/images/nophoto_user_thumb_profile.png" alt="" id="lassoImg" />';
			 ?>

			<br/>
  		<div id="thumbnail-controller" class="thumbnail-controller"></div>
		  <script type="text/javascript">
		    var loader = new Element('img',{ src:'<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif'});
		    var orginalThumbSrc;
		    var originalSize;
		    var lassoCrop;
		
		    var lassoSetCoords = function(coords)
		    {
		      var delta = (coords.w - 48) / coords.w;
		
		      document.getElementById('coordinates').value =
		        coords.x + ':' + coords.y + ':' + coords.w + ':' + coords.h;
		
		      document.getElementById('previewimage').setStyles({
		        top : -( coords.y - (coords.y * delta) ),
		        left : -( coords.x - (coords.x * delta) ),
		        height : ( originalSize.y - (originalSize.y * delta) ),
		        width : ( originalSize.x - (originalSize.x * delta) )
		      });
		    }
		    var myLasso;
		    var lassoStart = function()
		    {
		      if( !orginalThumbSrc ) orginalThumbSrc = document.getElementById('previewimage').src;
		      originalSize = document.getElementById("lassoImg").getSize();
		
		      //this.style.display = 'none';
		      myLasso = new Lasso.Crop('lassoImg',{
					ratio : [1, 1],
					preset : [10,10,58,58],
					min : [48,48],
					handleSize : 8,
					opacity : .6,
					color : '#7389AE',
					border : '<?php echo $this->layout()->staticBaseUrl . 'externals/moolasso/crop.gif' ?>',
					onResize : lassoSetCoords
		      });
		
		
		      var sourceImg = document.getElementById('lassoImg').src;
		      document.getElementById('previewimage').src = document.getElementById('lassoImg').src;
		      document.getElementById('coordinates').value = 10 + ':' + 10 + ':' + 58+ ':' + 58;
		      document.getElementById('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoEnd();"><?php echo $this->translate('Apply Changes');?></a>';
		    }
		
		    var uploadProfilePhoto =function(){
		      document.getElementById('uploadPhoto').value = true;
		      document.getElementById('thumbnail-controller').innerHTML = "<div><img class='loading_icon' src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif'/><?php echo $this->translate('Loading...');?></div>";
		      document.getElementById('profileform').submit();
		      document.getElementById('Filedata-wrapper').innerHTML = "";		      
		    }
		    var lassoEnd = function(){
		      document.getElementById('lassoImg').setStyle('display', 'block');
		      document.getElementById('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoStart();"><?php echo $this->translate('Edit Thumbnail');?></a>';
		      document.getElementById('lassoMask').destroy();
		    }
		
		  </script>
		  <br>
			<?php echo $this->form->render($this) ?>
			<form method="post" style="margin-top:10px;">
				<input type="hidden" name="image" value="<?php echo $this->image_name; ?>" />
		  	<button type="submit" id="done" name="done"><?php echo $this->translate("Suggest Photo"); ?></button> <?php echo $this->translate("or"); ?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate("Cancel") ?></a>
		 	</form>
 		</div>
 	</div>
</div>
<script type="text/javascript">
var catdiv = document.getElementById('current-wrapper'); 
 var catarea = catdiv.parentNode;
 catarea.removeChild(catdiv);
</script>