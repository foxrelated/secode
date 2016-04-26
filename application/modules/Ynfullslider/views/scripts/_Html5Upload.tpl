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

                document.getElementById('fileName').innerHTML = "<?php echo Zend_Registry::get('Zend_Translate')->_('Name') ?>" + ": " + file.name;
                document.getElementById('fileSize').innerHTML = "<?php echo Zend_Registry::get('Zend_Translate')->_('Size') ?>" + ": " + fileSize;
                document.getElementById('progress').style.display = 'inline-block';
                document.getElementById('progressNumber').style.display = 'inline-block';
                document.getElementById('demo-upload').style.display = 'inline-block';
            }
        }

        function uploadFile()
        {
            document.getElementById('demo-upload').style.display = 'none';
            var fd = new FormData();
            fd.append('fileToUpload', document.getElementById('fileToUpload').files[0]);
            var xhr = new XMLHttpRequest();
            xhr.upload.addEventListener("progress", ynfullsliderUploadVideoProgress, false);
            xhr.addEventListener("load", ynfullsliderUploadVideoComplete, false);
            xhr.addEventListener("error", ynfullsliderUploadVideoFailed, false);
            xhr.addEventListener("abort", ynfullsliderUploadVideoCanceled, false);
            xhr.open("POST", "<?php echo $this->url(array('module' => 'ynfullslider', 'controller' => 'index', 'action' => 'upload-video'), 'admin_default', true)?>", true);
            xhr.send(fd);
        }

        function ynfullsliderUploadVideoProgress(evt)
        {
            if (evt.lengthComputable)
            {
                var percentComplete = Math.round(evt.loaded * 100 / evt.total);
                $('progressNumber').innerHTML = percentComplete.toString() + '%';
                jQuery('#progress').find('.progress-bar').css('width', percentComplete + '%');
            }
        }

        function ynfullsliderUploadVideoComplete(evt)
        {
            /* This event is raised when the server send back a response */
            var json = JSON.decode(evt.target.responseText);
            var element = document.getElementById('upload_status');
            if (json.status)
            {
                $('video_file_id').value=json.file_id;
                if (null != $('video_file_path') && json.file_path) {
                    $('video_file_path').value = json.file_path;
                }
                if (null != $('photo_id') && json.photo_id) {
                    $('photo_id').value = json.photo_id;
                }
                if (null != $('temp_photo_path') && json.photo_path) {
                    $('temp_photo_path').value = json.photo_path;
                }
                element.set('html', '<span><b><?php echo $this->translate("Upload success") ?></b>');
            }
            else
            {
                element.addClass('tip');
                element.set('html', '<span><b><?php echo $this->translate("Upload has failed: ") ?></b>' + (json.error ? (json.error) : evt.target.responseText)) + "</span>";
                document.getElementById('progress').style.display = 'none';
                document.getElementById('demo-upload').style.display = 'none';
            }
        }

        function ynfullsliderUploadVideoFailed(evt)
        {
            var element = document.getElementById('upload_status');
            element.addClass('tip');
            element.innerHTML = "<span><?php echo $this->translate('There was an error attempting to upload the file.')?></span>";
            document.getElementById('progress').style.display = 'none';
            document.getElementById('demo-upload').style.display = 'none';
        }

        function ynfullsliderUploadVideoCanceled(evt)
        {
            var element = document.getElementById('upload_status');
            element.addClass('tip');
            element.innerHTML = "<span><?php echo $this->translate('The upload has been canceled by the user or the browser dropped the connection.')?></span>";
            document.getElementById('progress').style.display = 'none';
            document.getElementById('demo-upload').style.display = 'none';
        }
    </script>
    <div class="form-label">&nbsp;</div>
    <div class="form-element">
        <div id="demo-status">
            <div class="select_file">
                <input type="file" accept=".mp4, .ogg, .webm"  name="fileToUpload" id="fileToUpload" onchange="fileSelected();"/>
                <label for="fileToUpload"><?php echo $this->translate('Choose file...') ?></label>
            </div>
        </div>
        <div class="file_info" id="file_info">
            <div id="fileName"></div>
            <div id="fileSize"></div>
        </div>
        <div id="upload_status"></div>

        <!--progress bar-->
        <div id="progress" style="display:none;">
            <div class="progress-bar progress-bar-success" style="width: 0%;"></div>
        </div>

        <span style="display:none;" id="progressNumber" class="progress-text">0%</span>

        <div class="ynfullslider-btn-post-video">
            <a class="button" href="javascript:uploadFile();" id="demo-upload" style="display: none;"><i class="fa fa-upload"></i>&nbsp;<?php echo $this->translate('Post Video') ?></a>
        </div>
    </div>

    <div class="ynfullslider-upload-description">
        <?php echo $this->translate('Allow file format MP4, OGG, WEBM') ?>
    </div>
</div>