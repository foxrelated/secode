<h2>
    <?php echo $this->translate('Android Mobile Application') ?>
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
        echo "<div class='tip' style='position:relative;'><span>" . "Note: You do not have the latest version of the '<span style='font-weight:bold;'>" . @ucfirst($modName) . "</span>'. Please <a href='" . $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'upgrade'), 'admin_default', true) . "'>click here</a> to upgrade it and other modules to the latest version to enable its integration with Android Mobile Application." . "</span></div>";
    }
    ?>
<?php endif; ?>

<p>
    <?php echo 'This section is useful for you only if you want your app to be multi-lingual (in multiple languages), or if you want to change any of the existing English language phrases in your app. Below, you can manage a particular language’s phrases after click on its "edit phrases" link.<br />[Note: If you do not upload any other language files from here, then English will be the default language of your App.]'; ?>
</p>

<?php
//  $settings = Engine_Api::_()->getApi('settings', 'core');
//  if( $settings->getSetting('user.support.links', 0) == 1 ) {
//    echo 'More info: <a href="http://support.socialengine.com/questions/218/Admin-Panel-Layout-Language-Manager" target="_blank">See KB article</a>.';	
//  } 
?>	

<br />

<script type="text/javascript">
    var changeDefaultLanguage = function (locale) {
        var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'language', 'action' => 'default')) ?>';

        var request = new Request.JSON({
            url: url,
            data: {
                locale: locale,
                format: 'json'
            },
            onComplete: function () {
                window.location.replace(window.location.href);
            }
        });
        request.send();
    }
</script>

<!--<br />-->

<!--<div class="admin_language_options">
  <a href="<?php // echo $this->url(array('action' => 'create'))    ?>" class="buttonlink admin_language_options_new"><?php // echo $this->translate("Create New Pack")    ?></a>
  <a href="<?php // echo $this->url(array('action' => 'upload'))    ?>" class="buttonlink admin_language_options_upload"><?php // echo $this->translate("Upload New Pack")    ?></a>
</div>-->

<!--<br />-->

<table class="admin_table admin_languages">
    <thead>
        <tr>
            <th><?php echo $this->translate("Language") ?></th>
            <th><?php echo $this->translate("Available for App") ?></th>
            <th><?php echo $this->translate("Options") ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->languageNameList as $locale => $translatedLanguageTitle): ?>
            <tr>
                <td>
                    <?php echo $translatedLanguageTitle ?>
                </td>
                <td>
        <center>
            <?php echo (array_key_exists($locale, $this->getAPPLanguageDetailsForUpload))? "Yes": "<i>No</i>"; ?>
        </center>
    </td>
    <td class="admin_table_options">
        <a href="<?php echo $this->url(array('action' => 'language-edit', 'locale' => $locale)) ?>"><?php 
        if(array_key_exists($locale, $this->getAPPLanguageDetailsForUpload)):
            echo $this->translate("edit phrases");
        elseif(array_key_exists($locale, $this->getAPPCSVLanguageDetailsForUpload)):
            echo $this->translate("edit phrases");
        else:
            echo $this->translate("create language for app");
        endif;
//        echo (array_key_exists($locale, $this->getAPPLanguageDetailsForUpload))? $this->translate("edit phrases"): $this->translate("create language for app"); 
        
        ?></a>
        <?php echo (array_key_exists($locale, $this->getAPPLanguageDetailsForUpload))? ' | ' . $this->htmlLink(array('module' => 'siteandroidapp', 'controller' => 'app-builder', 'action' => 'language-delete', 'locale' => $locale), $this->translate('delete'), array('class' => 'smoothbox')): '' ?>
    <!--          | <a href="<?php // echo $this->url(array('action' => 'export', 'locale' => $locale))    ?>"><?php // echo $this->translate("export")    ?></a>
        <?php // if( $this->defaultLanguage != $locale ): ?>
          | <?php // echo $this->htmlLink('javascript:void(0);', $this->translate('make default'), array('onclick' => 'changeDefaultLanguage(\'' . $locale . '\');'))    ?>
          | <?php // echo $this->htmlLink(array('module'=>'core','controller'=>'language','action'=>'delete',  'locale'=>$locale), $this->translate('delete'), array('class'=>'smoothbox'))    ?>
        <?php // else: ?>
          | <?php // echo $this->translate("default")    ?>
        <?php // endif; ?>-->

    </td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>

<div class="clr" style="padding-top: 10px;">
    <button name="submit" id="submit" type="submit" onclick="window.location.href = '<?php echo $this->url(array('tab' => 5), 'admin_default', false); ?>'">Save Changes and Continue</button>
</div>
