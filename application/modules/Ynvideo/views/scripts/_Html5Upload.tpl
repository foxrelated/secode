 <div id="file-wrapper">
 <script type="text/javascript">
    function fileSelected() 
    {
      var file = document.getElementById('fileToUpload').files[0];
      if (file) {
        var fileSize = 0;
        if (file.size > 1024 * 1024)
          fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
        else
          fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';

        document.getElementById('fileName').innerHTML = 'Name: ' + file.name;
        document.getElementById('fileSize').innerHTML = 'Size: ' + fileSize;
        document.getElementById('fileType').innerHTML = 'Type: ' + file.type;
        document.getElementById('demo-status-overall').style.display = 'block';
        document.getElementById('progress').style.backgroundPosition = '-400px 0px';
        document.getElementById('demo-upload').style.display = 'block';
      }
    }

    function uploadFile() 
    {
      document.getElementById('demo-upload').style.display = 'none';
      var fd = new FormData();
      fd.append('fileToUpload', document.getElementById('fileToUpload').files[0]);
      var xhr = new XMLHttpRequest();
      xhr.upload.addEventListener("progress", uploadProgress, false);
      xhr.addEventListener("load", uploadComplete, false);
      xhr.addEventListener("error", uploadFailed, false);
      xhr.addEventListener("abort", uploadCanceled, false);
      xhr.open("POST", "<?php echo $this->url(array('module' => 'ynvideo', 'controller' => 'index', 'action' => 'upload-video'), 'default')?>", true);
      xhr.send(fd);
    }

    function uploadProgress(evt) 
    {
      if (evt.lengthComputable) 
      {
        var percentComplete = Math.round(evt.loaded * 100 / evt.total);
        document.getElementById('progressNumber').innerHTML = percentComplete.toString() + '%';
        var progress = document.getElementById('progress');
        progress.title = percentComplete.toString() + '%';
        progress.style.backgroundPosition  = (percentComplete*2.5 - 400).toString() + 'px 0px';
      }
      else 
      {
        document.getElementById('progressNumber').innerHTML = '<?php echo $this->translate('unable to compute') ?>';
      }
    }

    function uploadComplete(evt) 
    {
      /* This event is raised when the server send back a response */
      var json = JSON.decode(evt.target.responseText);
    	var element = document.getElementById('upload_status');;
      if (json.status == 1) 
      {
          $('code').value=json.code;
          $('id').value=json.video_id;
          $('form-upload').submit();
      } 
      else 
      {
          element.addClass('tip');
          element.set('html', '<span><b>Upload has failed: </b>' + (json.error ? (json.error) : evt.target.responseText)) + "</span>";
          document.getElementById('demo-status-overall').style.display = 'none';
          document.getElementById('demo-upload').style.display = 'none';
      }
    }

    function uploadFailed(evt) 
    {
    	var element = document.getElementById('upload_status');
    	element.addClass('tip')
    	element.innerHTML = "<span><?php echo $this->translate('There was an error attempting to upload the file.')?></span>";
    	document.getElementById('demo-status-overall').style.display = 'none';
    	document.getElementById('demo-upload').style.display = 'none';
    }

    function uploadCanceled(evt) 
    {
    	var element = document.getElementById('upload_status');
    	element.addClass('tip')
    	element.innerHTML = "<span><?php echo $this->translate('The upload has been canceled by the user or the browser dropped the connection.')?></span>";
    	document.getElementById('demo-status-overall').style.display = 'none';
    	document.getElementById('demo-upload').style.display = 'none';
    }
  </script>
  <div class="form-label">&nbsp;</div>
  <div class="form-element">
    <div id="demo-status">
      <div style="padding-bottom: 15px"><?php echo $this->translate('Click "Add Video" to select a video from your computer. After you have selected video, the video will be uploaded. Please wait while your video is being uploaded. When your upload is finished, your video will be processed - you will be notified when it is ready to be viewed.'); ?></div>
      <div class="select_file">
      		<input type="file" accept="video/*"  name="fileToUpload" id="fileToUpload" onchange="fileSelected();"/>
      </div>
    </div>
    <div class="file_info" id="file_info">
	    <div id="fileName"></div>
	    <div id="fileSize"></div>
	    <div id="fileType"></div>
	  </div>
	  <div id="upload_status"></div>
	  <div id="demo-status-overall">
	    	<img src="./externals/fancyupload/assets/progress-bar/bar.gif" id="progress" class="progress overall-progress" title="0%" style="background-position: -400px 0px;">
     		<span id="progressNumber" class="progress-text">0%</span>
     	</div>
    <div style="width: 100px">
      <a class="buttonlink" href="javascript:uploadFile();" id="demo-upload" style="display: none; background-image: url(./application/modules/Video/externals/images/new.png);">Post Video</a>
    </div>
  </div>
 </div>