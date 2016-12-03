<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
  $baseUrl = $this->layout()->staticBaseUrl;
  $this->headScript()
    ->appendFile($baseUrl . 'externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($baseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($baseUrl . 'externals/fancyupload/FancyUpload2.js');
  $this->headLink()
    ->appendStylesheet($baseUrl . 'externals/fancyupload/fancyupload.css');
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

<script type="text/javascript">

  var up;
  var swfPath = '<?php echo $baseUrl . 'externals/fancyupload/Swiff.Uploader.swf' ?>';
  var extraData = {
    format : 'json',
    path : '<?php echo $this->relPath ?>'
  };
  var successCount = 0;
  var failureCount = 0;
  window.addEvent('domready', function() {
    up = new FancyUpload2($('demo-status'), $('demo-list'), {
      verbose: true,
      appendCookieData: true,
      // set cross-domain policy file
      policyFile : '<?php echo (_ENGINE_SSL ? 'https://' : 'http://') 
          . $_SERVER['HTTP_HOST'] . $this->url(array(
            'controller' => 'cross-domain'), 
            'default', true) ?>',
      url: $('form-upload').action + '?ul=1',
      path: swfPath,
      typeFilter: {
        'TAR Archive (*.tar.gz)': '*.tar.gz'
      },
      target: 'demo-browse',
      data: extraData,
      onLoad : function() {
        $('demo-status').setStyle('display', '');
        $('demo-fallback').destroy();
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
        $('demo-clear').addEvent('click', function() {
          up.remove(); // remove all files
          return false;
        });
      },
      onSelectFail : function(files) {
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
      onComplete : function() {
        // Custom
        //window.location = window.location.href;
        $('demo-complete-message').setStyle('display', '');
      },
      onFileStart : function() {
        // @todo
      },
      onFileRemove : function(file) {
        // @todo
      },
      onSelectSuccess : function() {
        $('uploader-container').setStyle('display', '');
        $('demo-list').setStyle('display', '');
        $('demo-status-current').setStyle('display', '');
        $('demo-status-overall').setStyle('display', '');
        up.start();
      },
      onFileSuccess : function(file, response) {
        var json = new Hash(JSON.decode(response, true) || {});
        if (json.get('status') == '1') {
          successCount++;
          file.element.addClass('file-success');
          file.info.set('html', '<span>Upload complete.</span>');
        } else {
          failureCount++;
          file.element.addClass('file-failed');
          file.info.set('html', '<span>' + (json.get('error') ? (json.get('error')) : 'An unknown error has occurred.')) + '</span>';
        }
      },
      onFail : function(error) {
        switch( error ) {
          case 'hidden': // works after enabling the movie and clicking refresh
            alert("To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).");
            break;
          case 'blocked': // This no *full* fail, it works after the user clicks the button
            alert("To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).");
            break;
          case 'empty': // Oh oh, wrong path
            alert("A required file was not found, please be patient and we'll fix this.");
            break;
          case 'flash': // no flash 9+
            alert("To enable the embedded uploader, install the latest Adobe Flash plugin.");
        }
      }
    });
  });

  var showFallbackUploader = function()
  {
    $('uploader-container').setStyle('display', 'block');
  }

</script>

<h2>
  <?php echo 'Advanced Events Plugin'; ?>
</h2>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
  </div>
<?php endif; ?>

<h3><?php echo "Upload Dompdf Library" ?></h3>
<br/>

<?php if (file_exists('application/libraries/dompdf/dompdf_config.inc.php')): ?>
    <div class="tip">
        <span>
          It seems that you have already uploaded the dompdf library. Still you can upload it again using below tool if earlier upload was not uploaded properly.
        </span>
    </div>
    <br/>
<?php endif; ?>

<div class="importlisting_form">
	<div>
		<h3><?php echo "Upload Using Below Link" ?></h3>
		<p>
				<?php echo sprintf("Please download dompdf library (dompdf_0-6-0_beta3.tar.gz) by clicking %s.", '<a href="https://code.google.com/p/dompdf/downloads/detail?name=dompdf_0-6-0_beta3.tar.gz&can=2&q=" target="_blank">here</a>'); ?>
				<br/>
				<?php echo "Upload the downloaded file (dompdf_0-6-0_beta3.tar.gz) using the 'Upload Dompdf Library' link given below."?>
		</p>
		</br>
		<div>
			<?php echo $this->htmlLink('javascript:void(0);', 'Upload File', array('id' => 'demo-browse', 'class' => 'buttonlink admin_files_upload', 'onclick' => 'showFallbackUploader();')) ?>
		</div>

		<div id="uploader-container" class="uploader admin_files_uploader" style="display: none;">
			<div id="demo-fallback">
				<form action="<?php echo $this->url(array('action' => 'upload')) ?>" method="post" id="form-upload" enctype="multipart/form-data">
					<input type="file" name="Filedata" />
					<br />
					<br />
					<button type="submit">Upload</button>
					<input type="hidden" name="ul" value="1" />
					<input type="hidden" name="ut" value="standard" />
				</form>
			</div>
			<div id="demo-status" style="display: none;">
				<div style="display: none;">
					<a href="javascript:void(0);" id="demo-clear" style='display: none;'>Clear List</a>
				</div>
				<div class="demo-status-overall" id="demo-status-overall" style="display:none">
					<div class="overall-title"></div>
					<img alt="" src="<?php echo $baseUrl . 'externals/fancyupload/assets/progress-bar/bar.gif' ?>" class="progress overall-progress" />
				</div>
				<div class="demo-status-current" id="demo-status-current" style="display: none">
					<div class="current-title"></div>
					<img alt="" src="<?php echo $baseUrl . 'externals/fancyupload/assets/progress-bar/bar.gif' ?>" class="progress current-progress" />
				</div>
				<div class="current-text"></div>
			</div>
			<ul id="demo-list">
			</ul>
			<div id="demo-complete-message" style="color: green;display:none;">
				<br/>  
				<?php echo "Upload procedure has been completed succesfully.".$this->htmlLink($this->url(array('module' => 'siteeventticket', 'controller' => 'settings', 'action' => 'format')), '<b>Click here</b>').' to check the "Ticket PDF Preview". [Note: For viewing "Ticket PDF Preview" link, you must have install the "SocialEngineAddOns - Email Templates" plugin.]'; ?>
			</div>
		</div>
	</div>
</div>

<div class="importlisting_form">
	<div>
		<h3>Upload Manually</h3>
        <p>If you are facing any difficulty's using above tool than you can upload dompdf library by following below instructions.</p>
		<ul class="importlisting_form_list">
			<li><?php echo sprintf("Please download dompdf library (dompdf_0-6-0_beta3.tar.gz) by clicking %s.", '<a href="https://code.google.com/p/dompdf/downloads/detail?name=dompdf_0-6-0_beta3.tar.gz&can=2&q=" target="_blank">here</a>'); ?></li>
			<li><?php echo "Untar/extract the downloaed file and paste the extracted folder in 'application/libraries' path."?></li>		 
			<li><?php echo "Now open the file 'application/libraries/dompdf/dompdf_config.inc.php' and search the below code:"; ?>
				<div class="tip"><span style="background-image: none; margin: 5px 0px; padding: 7px;"><?php echo 'def("DOMPDF_ENABLE_REMOTE", false);'; ?></span></div>
				<?php echo "Replace the above code with below code and save the file."; ?>
				<div class="tip"><span style="background-image: none; margin: 5px 0px; padding: 7px;"><?php echo 'def("DOMPDF_ENABLE_REMOTE", true);'; ?></span></div></li>
		</ul>
	</div>
</div>
