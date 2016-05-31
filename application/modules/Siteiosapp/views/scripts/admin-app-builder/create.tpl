<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    create.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');
?>

<h2>
    <?php echo $this->translate('iOS Mobile Application - iPhone and iPad') ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<?php if (count($this->subnavigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->subnavigation)->render()
        ?>
    </div>
<?php endif; ?>
<?php if (!empty($this->doWeHaveLatestVersion)): ?>
    <?php
    foreach ($this->doWeHaveLatestVersion as $modName) {
        echo "<div class='tip' style='position:relative;'><span>" . "Note: You do not have the latest version of the '<span style='font-weight:bold;'>" . @ucfirst($modName) . "</span>'. Please <a href='" . $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'upgrade'), 'admin_default', true) . "'>click here</a> to upgrade it and other modules to the latest version to enable its integration with iOS Mobile Application - iPhone and iPad." . "</span></div>";
    }
    ?>
<?php endif; ?>
<?php if ($this->showDownloadTip): ?>
    <div class="seaocore_tip">
        <span>
            Limit of "upload_max_filesize" is <?php echo $this->upload_max_filesize; ?>M and generated .tar file length is <?php echo $this->tarFileSize; ?>M. In this case you may get the problem to download the .tar file. Please contact your hosting company and increase the "upload_max_filesize" size or minimize the length of .tar file.
        </span>
    </div>
<?php endif; ?>
<?php if (!empty($this->errorMessage)): ?>
    <div class="seaocore_tip">
        <span>
            <?php echo $this->errorMessage; ?>
        </span>
    </div>
<?php else: ?>
    <div class="seaocore_settings_form">
        <div class='settings'>
            <?php echo $this->form->render($this); ?>
        </div>
    </div>
<?php endif; ?>

<script type="text/javascript" >
    function selectPackage() {
        if ($("package") && $("package").value) {
            var url = '<?php echo $this->url(array('module' => 'siteiosapp', 'controller' => 'app-builder', 'action' => 'create', 'clientId' => $this->getUserInfo['clientId'], 'email' => $this->getUserInfo['email']), 'admin_default', true) ?>' + '/package/' + $("package").value;
            window.location.href = url;
        }
    }

    function openUplodedImage(url) {
        window.open(url, '_blank');
    }

    function showImage(id) {
        if ($(id))
            $(id).style.display = 'block';
    }

    function hideImage(id) {
        if ($(id))
            $(id).style.display = 'none';
    }

<?php if (!empty($this->downloadTar)): ?>
        window.addEvent('domready', function () {
            setTimeout("downloadTarFile()", 2000);
        });

        function downloadTarFile() {
            parent.window.location.href = '<?php echo $this->url(array('module' => 'siteiosapp', 'controller' => 'app-builder', 'action' => 'download-tar', 'package' => $this->package, 'downloadTar' => 1), 'admin_default', true); ?>';
        }
<?php endif; ?>

    window.addEvent('domready', function () {
        show_required_star();
        showUdid();
    });

//    window.addEvent('onload', function () {
//        show_required_star();
//    }); 
//    
    function show_required_star() {
<?php foreach ($this->requiredFormFields as $element): ?>
    <?php if (isset($this->form->$element)): ?>
                if ($('<?php echo $element ?>-label') && $('<?php echo $element ?>-label').innerHTML)
                    $('<?php echo $element ?>-label').innerHTML = '<label for="<?php echo $element ?>" class="required"> <?php echo $this->form->$element->getLabel(); ?> <span style="color:RED">*</span></label>';
    <?php endif; ?>
<?php endforeach; ?>;
    }

    function showUdid() {
        if ($('publish_app-0').checked) {
            $('phone_udid-wrapper').style.display = 'block';
        } else {
            $('phone_udid-wrapper').style.display = 'none';
        }
    }

    function promptMessage() {
        var a = prompt("Please enter your name", "Harry Potter");
        alert(a);
    }

</script>