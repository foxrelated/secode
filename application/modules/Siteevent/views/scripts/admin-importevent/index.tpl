<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $is_error = 0; ?>

<h2>
    <?php echo $this->translate('Advanced Events Plugin'); ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>


<div>
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteevent', 'controller' => 'log', 'action' => 'index'), $this->translate('Import History'), array('class' => 'buttonlink icon_siteevents_log')) ?>
	<br/><br/>
</div>

<?php if (!empty($this->repeatTypeEvents) && !$this->siteeventRepeatEnabled): ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('This importing is dependent on the "%1$sAdvanced Events - Recurring / Repeating Events Extension%2$s" and requires it to be installed and enabled on your site. Please install this plugin after downloading it from your Client Area on SocialEngineAddOns. You may purchase this plugin "%1$sover here%4$s".', '<a href="http://www.socialengineaddons.com/eventextensions/socialengine-advanced-events-recurring-repeating-events" target="_blank">', '</a>', '<a href="http://www.socialengineaddons.com/eventextensions/socialengine-advanced-events-recurring-repeating-events" target="_blank">', '</a>'); ?>
        </span>
    </div>
<?php else: ?>

    <?php if (($this->eventEnabled || $this->yneventEnabled) && $this->first_event_id && empty($this->repeatTypeEvents)): ?>
        <div class="importlisting_form">
            <div>
                <?php if ($this->eventEnabled): ?>
                    <h3><?php echo $this->translate("Import Events (from SocialEngine Events Plugin) into Advanced Events"); ?></h3>
                    <p>
                        <?php echo $this->translate("This Importing tool is designed to migrate content directly from Events (from YouNet SocialEngine Events Plugin) to Advanced Events. Using this, you can convert all the events on your site into Advanced Events. Please note that we try to import all the data corresponding to an Event but there is a possibility of some data losses too.<br />Below are the conditions which are required to be true for this import. Please check the points carefully and if some condition is yet to be fulfilled then do that first and then start importing your Events.<br />Once the import gets started, it is recommended not to close the lightbox, otherwise it will not be completed successfully and some data losses may occur."); ?>
                    </p>
                <?php elseif ($this->yneventEnabled): ?>
                    <h3><?php echo $this->translate("Import Events (from YouNet Advanced Event Plugin) into Advanced Events"); ?></h3>
                    <p>
                        <?php echo $this->translate("This Importing tool is designed to migrate content directly from Events (from YouNet Advanced Event Plugin) to Advanced Events. Using this, you can convert all the events on your site into Advanced Events. Please note that we try to import all the data corresponding to an Event but there is a possibility of some data losses too.<br />Below are the conditions which are required to be true for this import. Please check the points carefully and if some condition is yet to be fulfilled then do that first and then start importing your Events.<br />Once the import gets started, it is recommended not to close the lightbox, otherwise it will not be completed successfully and some data losses may occur."); ?>
                    </p>
                <?php endif; ?>

                <br />
                <div id="activity_event-wrapper" class="form-wrapper">
                    <div class="form-label" id="activity_event-label">&nbsp;</div>
                    <div id="activity_event-element" class="form-element">
                        <input type="hidden" name="activity_event" value="" /><input type="checkbox" name="activity_event" id="activity_event"/>
                        <label for="activity_event" class="optional"><?php echo $this->translate("Import activity feeds also."); ?></label>
                    </div>
                </div><br/>

                <div id="success_message" class='success-message'></div>
                <div id="unsuccess_message" class="error-message"></div>

                <div class="importlisting_elements" id="importevent_elements" >

                    <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) : ?>
                        <?php $is_error = 1; ?>
                        <?php $error_msg1 = $this->translate("Price is disabled!"); ?>

                        <span class="red" id="price_import_continue">
                            <img src='<?php echo $this->layout()->staticBaseUrl . "application/modules/Siteevent/externals/images/cross.png" ?>' />
                            <b><?php echo $error_msg1; ?></b>
                            <a onclick="continueImporting('price');
                                    return false;">
                                   <?php echo $this->translate('Ok, please continue'); ?>
                            </a>
                        </span>
                    <?php endif; ?>

                    <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) : ?>
                        <?php $is_error = 1; ?>
                        <?php $error_msg1 = $this->translate("Location is disabled!"); ?>

                        <span class="red" id="location_import_continue">
                            <img src='<?php echo $this->layout()->staticBaseUrl . "application/modules/Siteevent/externals/images/cross.png" ?>' />
                            <b><?php echo $error_msg1; ?></b>

                            <a onclick="continueImporting('location');
                                    return false;">
                                   <?php echo $this->translate('Ok, please continue'); ?>
                            </a>
                        </span>
                    <?php endif; ?>

                    <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyallow', 1)) : ?>
                        <?php $is_error = 1; ?>
                        <?php $error_msg1 = $this->translate("Description is disabled!"); ?>

                        <span class="red" id="description_import_continue">
                            <img src='<?php echo $this->layout()->staticBaseUrl . "application/modules/Siteevent/externals/images/cross.png" ?>' />
                            <b><?php echo $error_msg1; ?></b>

                            <a onclick="continueImporting('description');
                                    return false;">
                                   <?php echo $this->translate('Ok, please continue'); ?>
                            </a>
                        </span>
                    <?php endif; ?>

                    <?php if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 0 || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1)) : ?>
                        <?php $is_error = 1; ?>
                        <?php $error_msg1 = $this->translate("Reviews is disabled!"); ?>

                        <span class="red" id="reviews_import_continue">
                            <img src='<?php echo $this->layout()->staticBaseUrl . "application/modules/Siteevent/externals/images/cross.png" ?>' />
                            <b><?php echo $error_msg1; ?></b>

                            <a onclick="continueImporting('reviews');
                                    return false;">
                                   <?php echo $this->translate('Ok, please continue'); ?>
                            </a>
                        </span>
                    <?php endif; ?>

                    <?php $error_msg1 = $this->translate("Owners Reviews is disabled!"); ?>

                    <span class="red" id="owner_reviews_import_continue">
                        <img src='<?php echo $this->layout()->staticBaseUrl . "application/modules/Siteevent/externals/images/cross.png" ?>' />
                        <b><?php echo $error_msg1; ?></b>

                        <a onclick="continueImporting('ownerreviews');
                                return false;">
                               <?php echo $this->translate('Ok, please continue'); ?>
                        </a>
                    </span>

                    <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1)) : ?>
                        <?php $is_error = 1; ?>
                        <?php $error_msg1 = $this->translate("Overview is disabled!"); ?>

                        <span class="red" id="overview_import_continue">
                            <img src='<?php echo $this->layout()->staticBaseUrl . "application/modules/Siteevent/externals/images/cross.png" ?>' />
                            <b><?php echo $error_msg1; ?></b>

                            <a onclick="continueImporting('overview');
                                    return false;">
                                   <?php echo $this->translate('Ok, please continue'); ?>
                            </a>
                        </span>
                    <?php endif; ?>

                </div>

                <div id="import_button" class="import_button" <?php if ($is_error == 0): ?> style="display:block;" <?php else: ?> style="display:none;" <?php endif; ?>>
                    <button type="button" id="continue" name="continue" onclick='startImport();'>
                        <?php echo $this->translate('Start Import'); ?>
                    </button>

                </div>

                <div id="import_again_button" class="import_button" style="display:none;">
                    <?php if ($is_error == 0): ?>
                        <button type="button" id="continue" name="continue" onclick='startImport();'>
                            <?php echo $this->translate('Import Again'); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (($this->sitepageeventEnabled) && $this->first_sitepageevent_id): ?>
    <div class="importlisting_form">
        <div>
            <h3><?php echo $this->translate("Import Directory / Pages - Events into Advanced Events"); ?></h3>
            <p>
                <?php echo $this->translate("This Importing tool is designed to migrate content directly from an Directory / Pages - Events to Advanced Events. Using this, you can convert all the events on your site into Advanced Events. Please note that we try to import all the data corresponding to an Event but there is a possibility of some data losses too.<br />Below are the conditions which are required to be true for this import. Please check the points carefully and if some condition is yet to be fulfilled then do that first and then start importing your Events.<br />Once the import gets started, it is recommended not to close the lightbox, otherwise it will not be completed successfully and some data losses may occur."); ?>
            </p>

            <br />
            <div id="activity_sitepageevent-wrapper" class="form-wrapper">
                <div class="form-label" id="activity_sitepageevent-label">&nbsp;</div>
                <div id="activity_sitepageevent-element" class="form-element">
                    <input type="hidden" name="activity_sitepageevent" value="" /><input type="checkbox" name="activity_sitepageevent" id="activity_sitepageevent"/>
                    <label for="activity_sitepageevent" class="optional"><?php echo $this->translate("Import activity feeds also."); ?></label>
                </div>
            </div>
            <br/>

            <div id="sitepageevent_success_message" class='success-message'></div>
            <div id="sitepageevent_unsuccess_message" class="error-message"></div>
            <div class="importlisting_elements" id="sitepageevent_importevent_elements"  style="display:none;"></div>
            <div id="sitepageevent_import_button" class="import_button" style="display:block;">
                <button type="button" id="sitepageevent_continue" name="continue" onclick='startpageEventImport();'>
                    <?php echo $this->translate('Start Import'); ?>
                </button>
            </div>

            <div id="sitepageevent_import_again_button" class="import_button" style="display:none;">
                <button type="button" id="sitepageevent_continue" name="continue" onclick='startpageEventImport();'>
                    <?php echo $this->translate('Import Again'); ?>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (($this->sitebusinesseventEnabled) && $this->first_sitebusinessevent_id): ?>
    <div class="importlisting_form">
        <div>
            <h3><?php echo $this->translate("Import Directory / Businesses - Events into Advanced Events"); ?></h3>
            <p>
                <?php echo $this->translate("This Importing tool is designed to migrate content directly from an Directory / Businesses - Events to Advanced Events. Using this, you can convert all the events on your site into Advanced Events. Please note that we try to import all the data corresponding to an Event but there is a possibility of some data losses too.<br />Below are the conditions which are required to be true for this import. Please check the points carefully and if some condition is yet to be fulfilled then do that first and then start importing your Events.<br />Once the import gets started, it is recommended not to close the lightbox, otherwise it will not be completed successfully and some data losses may occur."); ?>
            </p>

            <br />
            <div id="activity_sitebusinessevent-wrapper" class="form-wrapper">
                <div class="form-label" id="activity_sitebusinessevent-label">&nbsp;</div>
                <div id="activity_sitebusinessevent-element" class="form-element">
                    <input type="hidden" name="activity_sitebusinessevent" value="" /><input type="checkbox" name="activity_sitebusinessevent" id="activity_sitebusinessevent"/>
                    <label for="activity_sitebusinessevent" class="optional"><?php echo $this->translate("Import activity feeds also."); ?></label>
                </div>
            </div>
            <br/>

            <div id="sitebusinessevent_success_message" class='success-message'></div>
            <div id="sitebusinessevent_unsuccess_message" class="error-message"></div>
            <div class="importlisting_elements" id="sitebusinessevent_importevent_elements"  style="display:none;"></div>
            <div id="sitebusinessevent_import_button" class="import_button" style="display:block;">
                <button type="button" id="sitebusinessevent_continue" name="continue" onclick='startbusinessEventImport();'>
                    <?php echo $this->translate('Start Import'); ?>
                </button>
            </div>

            <div id="sitebusinessevent_import_again_button" class="import_button" style="display:none;">
                <button type="button" id="sitebusinessevent_continue" name="continue" onclick='startbusinessEventImport();'>
                    <?php echo $this->translate('Import Again'); ?>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (($this->sitegroupeventEnabled) && $this->first_sitegroupevent_id): ?>
    <div class="importlisting_form">
        <div>
            <h3><?php echo $this->translate("Import Groups / Communities - Events into Advanced Events"); ?></h3>
            <p>
                <?php echo $this->translate("This Importing tool is designed to migrate content directly from an Groups / Communities - Events to Advanced Events. Using this, you can convert all the events on your site into Advanced Events. Please note that we try to import all the data corresponding to an Event but there is a possibility of some data losses too.<br />Below are the conditions which are required to be true for this import. Please check the points carefully and if some condition is yet to be fulfilled then do that first and then start importing your Events.<br />Once the import gets started, it is recommended not to close the lightbox, otherwise it will not be completed successfully and some data losses may occur."); ?>
            </p>

            <br />
            <div id="activity_sitegroupevent-wrapper" class="form-wrapper">
                <div class="form-label" id="activity_sitegroupevent-label">&nbsp;</div>
                <div id="activity_sitegroupevent-element" class="form-element">
                    <input type="hidden" name="activity_sitegroupevent" value="" /><input type="checkbox" name="activity_sitegroupevent" id="activity_sitegroupevent"/>
                    <label for="activity_sitegroupevent" class="optional"><?php echo $this->translate("Import activity feeds also."); ?></label>
                </div>
            </div>
            <br/>

            <div id="sitegroupevent_success_message" class='success-message'></div>
            <div id="sitegroupevent_unsuccess_message" class="error-message"></div>
            <div class="importlisting_elements" id="sitegroupevent_importevent_elements"  style="display:none;"></div>
            <div id="sitegroupevent_import_button" class="import_button" style="display:block;">
                <button type="button" id="sitegroupevent_continue" name="continue" onclick='startgroupEventImport();'>
                    <?php echo $this->translate('Start Import'); ?>
                </button>
            </div>

            <div id="sitegroupevent_import_again_button" class="import_button" style="display:none;">
                <button type="button" id="sitegroupevent_continue" name="continue" onclick='startgroupEventImport();'>
                    <?php echo $this->translate('Import Again'); ?>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="importlisting_form" style="display:none;">
    <div>
        <h3><?php echo $this->translate('Import Events from a CSV file'); ?></h3>

        <p>
            <?php echo $this->translate("This tool allows you to import Events corresponding to the entries from a .csv file. Here, you can also generate your own .csv template file by selecting the Profile Fields to be included in it by using the “Generate new CSV template file” link below. Before starting to use this tool, please read the following points carefully. "); ?>
        </p>

        <ul class="importevent_form_siteevent">

            <li>
                <?php echo $this->translate("Don't add any new column in the csv file from which importing has to be done."); ?>
            </li>

            <li>
                <?php echo $this->translate("The data in the files should be pipe('|') separated and in a particular format or ordering. So, there should be no pipe('|') in any individual column of the CSV file . If you want to add comma(',') separated data in the CSV file, then you can select the comma(',') option during the CSV file upload process. Note: There is one drawback of using the comma(',') separated data that you will not be able to use comma in fields like description, price, overview etc. for the entries in the CSV file."); ?>
            </li>

            <li>
                <?php echo $this->translate("Event title, description and category are the required fields for all the entries in the file."); ?>
            </li>	

            <li>
                <?php echo $this->translate("Categories, sub-categories and 3rd level categories name should exactly match with the existing categories, sub-categories and 3rd level categories"); ?>
            </li>       


            <li>
                <?php echo $this->translate("Before starting the import process, it is recommended that you should first create Categories, Profile Fields and do Category-Profile mappings from the 'Category-Event Profile Mapping' section."); ?>
            </li>

            <li>
                <?php echo $this->translate("In case you want to insert more than one tag for an entry, then the tags string should be separated by hash('#'). For example, if you want to insert 2 tags for an entry - 'tag1' and 'tag2', then tag string for that will be 'tag1#tag2'."); ?>
            </li>

            <li>
                <?php echo $this->translate("You can import the maximum of 10,000 Events at a time and if you want to import more, you would have to then repeat the whole process. For example, you have to import 15000 Events. Then, you would have to create 2 CSV files - one having 10,000 entries and another having 5,000 entries corresponding to the Events. After that, just import both the files using 'Import Events' option."); ?>
            </li>

            <li>
                <?php echo $this->translate("You can also 'Stop' and 'Rollback' the import process. 'Stop' will just stop the import process going on at that time from that file and 'Rollback' will undo or delete all the Events created from that CSV import file till that time."); ?>
            </li>

            <li>
                <?php echo $this->translate("Files must be in the CSV format to be imported."); ?>
            </li>

        </ul>

        <br />

        <iframe src="about:blank" style="display:none" name="downloadframe"></iframe>

        <a href="<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'importevent', 'action' => 'download-sample')) ?><?php echo '?path=' . urlencode('example_event_import.csv'); ?>" class="buttonlink icon_siteevents_download_csv"><?php echo $this->translate('Download example CSV template file') ?></a> 

        <a href="<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'fields', 'action' => 'show-customfields')) ?>" class="buttonlink icon_siteevents_generate_csv"><?php echo $this->translate('Generate new CSV template file') ?></a>  

        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'siteevent', 'controller' => 'admin-importevent', 'action' => 'import'), $this->translate('Import Events'), array('class' => 'smoothbox buttonlink icon_siteevents_import')) ?>

        <br />
        <br />

    </div>
</div>		

<script type="text/javascript">
                    var event_assigned_previous_id = '<?php echo $this->event_assigned_previous_id; ?>';
                    var click1 = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0); ?>';
                    var click2 = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>';
                    var click3 = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyallow', 1); ?>';
                    var click4 = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2); ?>';
                    var click5 = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1); ?>';
                    var click6 = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowownerreview', 1); ?>';

                    function startImport()
                    {
                        var import_confirmation = confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to start importing Events ?")) ?>');

                        var activity_event = 0;
                        if ($('activity_event').checked == true) {
                            activity_event = 1;
                        }

                        if (import_confirmation) {

                            Smoothbox.open("<div><center><b>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing Events...")) ?>' + "</b><br /><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/loader.gif' alt='' /></center></div>");

                            en4.core.request.send(new Request.JSON({
                                url: en4.core.baseUrl + 'admin/siteevent/importevent',
                                method: 'get',
                                data: {
                                    'start_import': 1,
                                    'event_assigned_previous_id': event_assigned_previous_id,
                                    'activity_event': activity_event,
                                    'module': 'event',
                                    'format': 'json'
                                },
                                onSuccess: function(responseJSON) {
                                    $('import_button').style.display = 'none';
                                    $('importevent_elements').style.display = 'none';

                                    if (responseJSON.event_assigned_previous_id < responseJSON.last_event_id) {
                                        $('import_again_button').style.display = 'block';
                                        event_assigned_previous_id = responseJSON.event_assigned_previous_id;

                                        $('unsuccess_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/cross.png);'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Sorry for this inconvenience !!")) ?>' + "<br />" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing is interrupted due to some reason. Please click on 'Import Again' button to start the importing from the same point again.")) ?>' + "</span><br />";
                                    }
                                    else {
                                        $('import_again_button').style.display = 'none';
                                        $('unsuccess_message').style.display = 'none';
                                        $('success_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/notice.png);'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing is done succesfully.")) ?>' + "</span><br />";
                                    }
                                    Smoothbox.close();
                                }
                            }))
                        }
                    }

                    function continueImporting(importElement) {

                        if (importElement == 'price') {
                            $('price_import_continue').style.display = "none";
                            click1 = 1;
                        }

                        if (importElement == 'location') {
                            $('location_import_continue').style.display = "none";
                            click2 = 1;
                        }

                        if (importElement == 'description') {
                            $('description_import_continue').style.display = "none";
                            click3 = 1;
                        }

                        if (importElement == 'reviews') {
                            $('reviews_import_continue').style.display = "none";
                            click4 = 1;
                        }

                        if (importElement == 'overview') {
                            $('overview_import_continue').style.display = "none";
                            click5 = 1;
                        }

                        if (importElement == 'ownerreviews') {
                            $('owner_reviews_import_continue').style.display = "none";
                            click6 = 1;
                        }

                        if (click1 == 1 && click2 == 1 && click3 == 1 && (click4 == 1 || click4 == 2 || click4 == 3) && click5 == 1 && click6 == 1) {
                            $('import_button').style.display = 'block';
                            $('importevent_elements').style.display = 'none';
                        }
                    }

                    var is_error = '<?php echo $is_error; ?>';
                    if (is_error == 0) {
                        if ($('importevent_elements'))
                            $('importevent_elements').style.display = 'none';
                    }

                    var sitepageevent_assigned_previous_id = '<?php echo $this->sitepageevent_assigned_previous_id; ?>';
                    function startpageEventImport()
                    {
                        var import_sitepageevent_confirmation = confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to start importing Directory / Pages - Events ?")) ?>');

                        var activity_sitepageevent = 0;
                        if ($('activity_sitepageevent').checked == true) {
                            activity_sitepageevent = 1;
                        }

                        if (import_sitepageevent_confirmation) {

                            Smoothbox.open("<div><center><b>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing Directory / Pages - Events...")) ?>' + "</b><br /><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/loader.gif' alt='' /></center></div>");

                            en4.core.request.send(new Request.JSON({
                                url: en4.core.baseUrl + 'admin/siteevent/importevent',
                                method: 'get',
                                data: {
                                    'start_import': 1,
                                    'sitepageevent_assigned_previous_id': sitepageevent_assigned_previous_id,
                                    'activity_sitepageevent': activity_sitepageevent,
                                    'module': 'sitepageevent',
                                    'format': 'json'
                                },
                                onSuccess: function(responseJSON) {
                                    $('sitepageevent_import_button').style.display = 'none';
                                    $('sitepageevent_importevent_elements').style.display = 'none';

                                    if (responseJSON.sitepageevent_assigned_previous_id < responseJSON.last_sitepageevent_id) {
                                        $('sitepageevent_import_again_button').style.display = 'block';
                                        sitepageevent_assigned_previous_id = responseJSON.sitepageevent_assigned_previous_id;

                                        $('sitepageevent_unsuccess_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/cross.png);'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Sorry for this inconvenience !!")) ?>' + "<br />" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing is interrupted due to some reason. Please click on 'Import Again' button to start the importing from the same point again.")) ?>' + "</span><br />";
                                    }
                                    else {
                                        $('sitepageevent_import_again_button').style.display = 'none';
                                        $('sitepageevent_unsuccess_message').style.display = 'none';
                                        $('sitepageevent_success_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/notice.png);'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing is done succesfully.")) ?>' + "</span><br />";
                                    }
                                    Smoothbox.close();
                                }
                            }))
                        }
                    }

                    var sitebusinessevent_assigned_previous_id = '<?php echo $this->sitebusinessevent_assigned_previous_id; ?>';
                    function startbusinessEventImport()
                    {
                        var import_sitebusinessevent_confirmation = confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to start importing Directory / Businesses - Events ?")) ?>');

                        var activity_sitebusinessevent = 0;
                        if ($('activity_sitebusinessevent').checked == true) {
                            activity_sitebusinessevent = 1;
                        }

                        if (import_sitebusinessevent_confirmation) {

                            Smoothbox.open("<div><center><b>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing Directory / Businesses - Events...")) ?>' + "</b><br /><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/loader.gif' alt='' /></center></div>");

                            en4.core.request.send(new Request.JSON({
                                url: en4.core.baseUrl + 'admin/siteevent/importevent',
                                method: 'get',
                                data: {
                                    'start_import': 1,
                                    'sitebusinessevent_assigned_previous_id': sitebusinessevent_assigned_previous_id,
                                    'activity_sitebusinessevent': activity_sitebusinessevent,
                                    'module': 'sitebusinessevent',
                                    'format': 'json'
                                },
                                onSuccess: function(responseJSON) {
                                    $('sitebusinessevent_import_button').style.display = 'none';
                                    $('sitebusinessevent_importevent_elements').style.display = 'none';

                                    if (responseJSON.sitebusinessevent_assigned_previous_id < responseJSON.last_sitebusinessevent_id) {
                                        $('sitebusinessevent_import_again_button').style.display = 'block';
                                        sitebusinessevent_assigned_previous_id = responseJSON.sitebusinessevent_assigned_previous_id;

                                        $('sitebusinessevent_unsuccess_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/cross.png);'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Sorry for this inconvenience !!")) ?>' + "<br />" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing is interrupted due to some reason. Please click on 'Import Again' button to start the importing from the same point again.")) ?>' + "</span><br />";
                                    }
                                    else {
                                        $('sitebusinessevent_import_again_button').style.display = 'none';
                                        $('sitebusinessevent_unsuccess_message').style.display = 'none';
                                        $('sitebusinessevent_success_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/notice.png);'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing is done succesfully.")) ?>' + "</span><br />";
                                    }
                                    Smoothbox.close();
                                }
                            }))
                        }
                    }

                    var sitegroupevent_assigned_previous_id = '<?php echo $this->sitegroupevent_assigned_previous_id; ?>';
                    function startgroupEventImport()
                    {
                        var import_sitegroupevent_confirmation = confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to start importing Groups / Communities - Events ?")) ?>');

                        var activity_sitegroupevent = 0;
                        if ($('activity_sitegroupevent').checked == true) {
                            activity_sitegroupevent = 1;
                        }

                        if (import_sitegroupevent_confirmation) {

                            Smoothbox.open("<div><center><b>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing Groups / Communities - Events...")) ?>' + "</b><br /><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/loader.gif' alt='' /></center></div>");

                            en4.core.request.send(new Request.JSON({
                                url: en4.core.baseUrl + 'admin/siteevent/importevent',
                                method: 'get',
                                data: {
                                    'start_import': 1,
                                    'sitegroupevent_assigned_previous_id': sitegroupevent_assigned_previous_id,
                                    'activity_sitegroupevent': activity_sitegroupevent,
                                    'module': 'sitegroupevent',
                                    'format': 'json'
                                },
                                onSuccess: function(responseJSON) {
                                    $('sitegroupevent_import_button').style.display = 'none';
                                    $('sitegroupevent_importevent_elements').style.display = 'none';

                                    if (responseJSON.sitegroupevent_assigned_previous_id < responseJSON.last_sitegroupevent_id) {
                                        $('sitegroupevent_import_again_button').style.display = 'block';
                                        sitegroupevent_assigned_previous_id = responseJSON.sitegroupevent_assigned_previous_id;

                                        $('sitegroupevent_unsuccess_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/cross.png);'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Sorry for this inconvenience !!")) ?>' + "<br />" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing is interrupted due to some reason. Please click on 'Import Again' button to start the importing from the same point again.")) ?>' + "</span><br />";
                                    }
                                    else {
                                        $('sitegroupevent_import_again_button').style.display = 'none';
                                        $('sitegroupevent_unsuccess_message').style.display = 'none';
                                        $('sitegroupevent_success_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/notice.png);'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing is done succesfully.")) ?>' + "</span><br />";
                                    }
                                    Smoothbox.close();
                                }
                            }))
                        }
                    }
</script>

<?php if (empty($this->first_event_id) && empty($this->first_sitepageevent_id) && empty($this->first_sitebusinessevent_id) && empty($this->first_sitegroupevent_id)): ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("Currently, you do not have any events to be migrated from any existing Events Plugin to this plugin."); ?>
        </span>
    </div>
<?php endif; ?>