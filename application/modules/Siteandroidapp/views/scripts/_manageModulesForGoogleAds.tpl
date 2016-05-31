<?php
$isSelected = true;
$getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
$websiteStr = str_replace(".", "-", $getWebsiteName);
$dirName = 'android-' . $websiteStr . '-app-builder';
$appBuilderBaseFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . $dirName . '/settings.php';
if (file_exists($appBuilderBaseFile)) {
    include $appBuilderBaseFile;
}
if (!isset($appBuilderParams['ad_placement_id']) || empty($appBuilderParams['ad_placement_id'])) {
    echo '<div class="seaocore_tip"><span>'
    . 'Advertising will not work in your app as you have not entered the "Facebook Placement Unit ID" in "App Submission Info" section.".'
    . '</span></div>';
}
?>
<p>
    Below you can configure advertising in your app in a module-wise manner. For each module, you can enable / disable ads, choose the type of ads, and the number of content elements after which ads should be displayed.<br />
This advertising system displays ads in an elegant and visually non-invasive manner.
</p>
<br />

<table class='admin_table' style="width: 700px; height: 400px;">
    <thead>
        <tr>
          <!--<th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>-->
            <th><?php echo $this->translate("Module Name") ?></th>
            <!--<th><?php // echo $this->translate("Ad Type") ?></th>-->
            <th><?php echo $this->translate("Show Ad After") ?></th>
            <th><?php echo $this->translate("Status") ?></th>
            <th><?php echo $this->translate("Options") ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->getEnabledModules as $key => $module): ?>
            <tr>
              <!--<td><input type='checkbox' class='checkbox' name='delete_<?php // echo $item->getIdentity();            ?>' value="<?php // echo $item->getIdentity();            ?>" /></td>-->
                <td title="<?php echo $module ?>"><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($module, 60) ?></td>
                <?php
                $adType = 'Content Based Ads';
                if (isset($this->appBuilderParams['Ads'][$key]['google_ad_type'])) {
                    $adType = ($this->appBuilderParams['Ads'][$key]['google_ad_type'] == 'appinstall') ? 'App Based Ads' : 'Content Based Ads';
                }
                ?>
                <!--<td><?php // echo $adType; ?></td>-->
                <td title="<?php echo isset($this->appBuilderParams['Ads'][$key]['google_ad_show_after']) ? $this->appBuilderParams['Ads'][$key]['google_ad_show_after'] : 8; ?>"><center><?php echo isset($this->appBuilderParams['Ads'][$key]['google_ad_show_after']) ? $this->appBuilderParams['Ads'][$key]['google_ad_show_after'] : 8; ?></center></td>                
    <td>        
        <?php
        $status = isset($this->appBuilderParams['Ads'][$key]['enabled_google_ad']) ? $this->appBuilderParams['Ads'][$key]['enabled_google_ad'] : 0;
        ?>
        <?php echo ( $status ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteandroidapp', 'controller' => 'app-builder', 'action' => 'status', 'key' => $key), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Disable it'))), array('class' => 'smoothbox')) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteandroidapp', 'controller' => 'app-builder', 'action' => 'status', 'key' => $key), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Enable it'))), array('class' => 'smoothbox')) ) ?>
    </td>
    <td>
        <?php
        echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'siteandroidapp', 'controller' => 'app-builder', 'action' => 'edit-google-ad-modules', 'key' => $key), $this->translate("edit"), array('class' => 'smoothbox'))
        ?>
    </td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>
<div class="clr" style="padding-top: 10px;">
    <button name="submit" id="submit" type="submit" onclick="window.location.href = '<?php echo $this->url(array('tab' => 6), 'admin_default', false); ?>'">Save Changes and Continue</button>
</div>