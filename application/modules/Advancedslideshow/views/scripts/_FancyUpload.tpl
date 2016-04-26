<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _FancyUpload.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$advancedslideshow = Engine_Api::_()->core()->getSubject();
	$height = $advancedslideshow->height;
	$width = $advancedslideshow->width;
	$type = $advancedslideshow->slideshow_type;
	$advancedslideshow_id = $advancedslideshow->advancedslideshow_id;

	$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
	->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
	->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/FancyUpload2.js');
	$this->headLink()
	->appendStylesheet($this->layout()->staticBaseUrl . 'externals/fancyupload/fancyupload.css');

	$this->headTranslate(array(
	'Overall Progress ({total})', 'File Progress', 'Uploading "{name}"',
	'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.', '{name}',
	'Remove', 'Click to remove this entry.', 'Upload failed',
	'{name} already added.',
	'{name} ({size}) is too small, the minimal file size is {fileSizeMin}.',
	'{name} ({size}) is too big, the maximal file size is {fileSizeMax}.',
	'{name} could not be added, amount of {fileListMax} files exceeded.',
	'{name} ({size}) is too big, overall filesize of {fileListSizeMax} exceeded.',
	'Server returned HTTP-Status <code>#{code}</code>',
	'Security error occurred ({text})',
	'Error caused a send or load operation to fail ({text})',
  ));
?>

<div class="tip" style='width:460px'>
		<span>
			<?php echo $this->translate(" If you are unable to upload slides using FancyUploader, then try uploading slides using ").$this->htmlLink(array(
							'route' => 'admin_default',
							'module' => 'advancedslideshow',
							'controller' => 'image',
							'action' => 'simple-upload',
							'owner_id' => 1,
							'advancedslideshow_id' => $advancedslideshow_id,
							'subject' => $advancedslideshow->getGuid(),
						), $this->translate('basic uploader.'), array(
							//'class' => 'smoothbox'
						)); ?>
		</span>
	</div>	

<script type="text/javascript">
	var uploadCount = 0;
	var extraData = <?php echo $this->jsonInline($this->data); ?>;
	//var extraData = {"advancedslideshow_id":15};

	window.addEvent('domready', function() { // wait for the content
		// our uploader instance

		var up = new FancyUpload2(document.getElementById('demo-status'), document.getElementById('demo-list'), { // options object
			// we console.log infos, remove that in production!!
			verbose: false,
			appendCookieData: true,
      
      // set cross-domain policy file
      policyFile : '<?php echo (_ENGINE_SSL ? 'https://' : 'http://') 
          . $_SERVER['HTTP_HOST'] . $this->url(array(
            'controller' => 'cross-domain'), 
            'default', true) ?>',
                  
			// url is read from the form, so you just have to change one place
			url: document.getElementById('form-upload').action + '?ul=1',

			// path to the SWF file
			path: '<?php echo $this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.swf';?>',

			// remove that line to select all files, or edit it, add more items
			typeFilter: {
				'Images (*.jpg, *.jpeg, *.gif, *.png, *.JPG, *.JPEG, *.GIF, *.PNG)': '*.jpg; *.jpeg; *.gif; *.png; *.JPG; *.JPEG; *.GIF; *.PNG'
			},

			// this is our browse button, *target* is overlayed with the Flash movie
			target: 'demo-browse',

									data: extraData,

			// graceful degradation, onLoad is only called if all went well with Flash
			onLoad: function() {
				document.getElementById('demo-status').removeClass('hide'); // we show the actual UI
				document.getElementById('demo-fallback').destroy(); // ... and hide the plain form

				// We relay the interactions with the overlayed flash to the link
				this.target.addEvents({
					click: function() {
						return false;
					},
					mouseenter: function() {
						this.addClass('hover');
					},
					mouseleave: function() {
						this.removeClass('hover');
						this.blur();
					},
					mousedown: function() {
						this.focus();
					}
				});

				// Interactions for the 2 other buttons

				document.getElementById('demo-clear').addEvent('click', function() {
					up.remove(); // remove all files
					var fileids = document.getElementById('fancyuploadfileids');

					fileids.value ="";
					return false;
				});

			},

			// Edit the following lines, it is your custom event handling

			/**
			* Is called when files were not added, "files" is an array of invalid File classes.
			*
			* This example creates a list of error elements directly in the file list, which
			* hide on click.
			*/
			onSelectFail: function(files) {
				files.each(function(file) {
					new Element('li', {
						'class': 'validation-error',
						html: file.validationErrorMessage || file.validationError,
						title: MooTools.lang.get('FancyUpload', 'removeTitle'),
						events: {
							click: function() {
								this.destroy();
							}
						}
					}).inject(this.list, 'top');
				}, this);
			},

			onComplete: function hideProgress() {
				var demostatuscurrent = document.getElementById("demo-status-current");
				var demostatusoverall = document.getElementById("demo-status-overall");
				var demosubmit = document.getElementById("submit-wrapper");

				demostatuscurrent.style.display = "none";
				demostatusoverall.style.display = "none";
				demosubmit.style.display = "block";
			},

			onFileStart: function() {
				uploadCount += 1;
			},
								onFileRemove: function(file) {
				uploadCount -= 1;
				file_id = file.image_id;
										request = new Request.JSON({
										'format' : 'json',
										'url' : '<?php echo $this->url(Array('module'=>'advancedslideshow', 'controller' => 'image', 'action'=>'remove'), 'admin_default') ?>',
										'data': {
											'image_id' : file_id,
											'is_ajax' : 1
										},
										'onSuccess' : function(responseJSON) {
											return false;
										}
									});

										request.send();
										var fileids = document.getElementById('fancyuploadfileids');

				if (uploadCount == 0)
				{
							var democlear = document.getElementById("demo-clear");
							var demolist = document.getElementById("demo-list");
					var demosubmit = document.getElementById("submit-wrapper");
					democlear.style.display = "none";
					demolist.style.display = "none";
					demosubmit.style.display = "none";
				}
				fileids.value = fileids.value.replace(file_id, "");
			},
			onSelectSuccess: function(file) {
										document.getElementById('demo-list').style.display = 'block';
				var democlear = document.getElementById("demo-clear");
				var demostatuscurrent = document.getElementById("demo-status-current");
				var demostatusoverall = document.getElementById("demo-status-overall");

				democlear.style.display = "inline";
				demostatuscurrent.style.display = "block";
				demostatusoverall.style.display = "block";
										up.start();
			} ,
			/**
			* This one was directly in FancyUpload2 before, the event makes it
			* easier for you, to add your own response handling (you probably want
			* to send something else than JSON or different items).
			*/
			onFileSuccess: function(file, response) {
				var json = new Hash(JSON.decode(response, true) || {});

				if (json.get('status') == '1') {
					file.element.addClass('file-success');
					file.info.set('html', '<span><?php echo $this->string()->escapeJavascript($this->translate("Upload complete.")) ?></span>');
					var fileids = document.getElementById('fancyuploadfileids');
					fileids.value = fileids.value + json.get('image_id') + " ";
					file.image_id = json.get('image_id');

				} else {
					file.element.addClass('file-failed');

					file.info.set('html', '<span><?php echo $this->string()->escapeJavascript($this->translate("An error occurred.")) ?></span> ' + (json.get('error') ? (json.get('error')) : response));
					//file.info.set('html', '<span>An error occurred:</span> ' + (json.get('error') ? (json.get('error') + ' #' + json.get('code')) : response));
				}
			},

			/**
			* onFail is called when the Flash movie got bashed by some browser plugin
			* like Adblock or Flashblock.
			*/
			onFail: function(error) {
				switch (error) {
					case 'hidden': // works after enabling the movie and clicking refresh
						case 'hidden': // works after enabling the movie and clicking refresh
						alert('<?php echo $this->string()->escapeJavascript($this->translate("To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).")) ?>');
						break;
					case 'blocked': // This no *full* fail, it works after the user clicks the button
						alert('<?php echo $this->string()->escapeJavascript($this->translate("To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).")) ?>');
						break;
					case 'empty': // Oh oh, wrong path
						alert('<?php echo $this->string()->escapeJavascript($this->translate("A required file was not found, please be patient and we'll fix this.")) ?>');
						break;
					case 'flash': // no flash 9+
						alert("<?php echo $this->translate("To enable the embedded uploader, install the latest Adobe Flash plugin.") ?>");
				}
			}

		});
	});
</script>

<input type="hidden" name="<?php echo $this->name;?>" id="fancyuploadfileids" value ="" />
<fieldset id="demo-fallback">
  <legend><?php echo $this->translate('File Upload');?></legend>
  <p>
    <?php echo $this->translate('This form is just an example fallback for the unobtrusive behaviour of FancyUpload. If this part is not changed, something must be wrong with your code.');?>
  </p>
  <label for="demo-imageupload">
    <?php echo $this->translate('Upload a Picture:');?>
    <input type="file" name="Filedata" />
  </label>
</fieldset>

<div id="demo-status" class="hide">
  <div>
    <?php echo $this->translate("Click 'Add Pictures' to select one or more pictures from your computer. After you have selected the pictures, they will begin to upload right away. When your upload is finished, click the button below your picture list to save them to your slideshow.");?>
  </div>

	<div class="tip" style='width:460px'>
		<span>
			<?php echo $this->translate('You can change the slideshow picture width and height by configuring the slideshow width and height from the ');?> <a href='<?php echo "http://".$_SERVER['HTTP_HOST'].  Zend_Controller_Front::getInstance()->getBaseUrl()."/admin/advancedslideshow/slideshows/edit/slideshowtype/".$type."/advancedslideshow_id/"."$advancedslideshow_id" ?>' target="_parent"><?php echo $this->translate("Edit Slideshow");?></a><?php echo $this->translate(" section.");?>
			<?php echo $this->translate("The current width and height set over here are ") . $width . $this->translate("px and ") . $height . $this->translate("px respectively. It is recommended that you upload pictures of these dimensions.") ?>
		</span>
  </div>
	
  <div class="add-pictures">
    <a href="javascript:void(0);" id="demo-browse"><?php echo $this->translate('Add Pictures');?></a>
    <a class="buttonlink icon_clearlist" href="javascript:void(0);" id="demo-clear" style='display: none;'><?php echo $this->translate('Clear List');?></a>
  </div>
  <div class="demo-status-overall" id="demo-status-overall" style="display: none">
    <div class="overall-title"></div>
    <img src="<?php echo $this->layout()->staticBaseUrl . 'externals/fancyupload/assets/progress-bar/bar.gif';?>" class="progress overall-progress" />
  </div>
  <div class="demo-status-current" id="demo-status-current" style="display: none">
    <div class="current-title"></div>
    <img src="<?php echo $this->layout()->staticBaseUrl . 'externals/fancyupload/assets/progress-bar/bar.gif';?>" class="progress current-progress" />
  </div>
  <div class="current-text"></div>
</div>
<ul id="demo-list"></ul>
<style type="text/css">
.add-pictures a{
	background:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/add.png) no-repeat;
	font-weight:bold;
	padding-left:20px;
}
</style>