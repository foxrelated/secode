<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _hostDetails.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->fieldType == "host_type"): ?>
    <div class="form-wrapper siteevent_create_host" id="siteevent_info_host">
        <div class="form-label">
            <label><?php echo $this->translate("Host"); ?></label>
        </div>
        <div class="form-element" id="host-element">
            <select name="host_type" id="host_type" style="display: none;" class="se_quick_advanced">
                <?php foreach ($this->hostOptions as $k => $v): ?>
                    <option value="<?php echo $k; ?>" label="<?php echo $this->translate($v); ?>"><?php echo $this->translate($v); ?></option>
                <?php endforeach; ?>
            </select>
            <div id="edit_host_info" class="choose_host" style="display: none;">
                <select name="host_type_select" id="host_type_select" onchange="onHostTypeChange();">
                    <?php foreach ($this->hostOptions as $k => $v): ?>
                        <option value="<?php echo $k; ?>" label="<?php echo $this->translate($v); ?>" <?php if ($this->host->getType() == $k): ?> selected="selected" <?php endif; ?>><?php echo $this->translate($v); ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="text" name="host_auto" id="host_auto" placeholder="<?php echo $this->translate("start typing host’s name...") ?>" autocomplete="off" >
                <span class="host_cancel_link">
                    <span>[</span>
                    <?php echo $this->translate("or") ?>
                    <a class="add_new_host_link button_link" href="javascript:void(0)" style="display: none"><?php echo $this->translate("Add new") ?></a>
                    <span class="add_new_sep">|</span>
                    <a class="change_host_cancel button_link" href="javascript:void(0)"><?php echo $this->translate("Cancel") ?></a>
                    <span>]</span>
                </span>
        <!--        <div id="sitemember_desc" class="mtop5 clr dblock"><?php //echo $this->translate("Let your attendees know who's organizing or hosting this event")   ?></div>-->
            </div>
            <div id="host_info">
                <span class="host_deatils">
                    <span class="host_photo">
                        <?php echo $this->itemPhoto($this->host, 'thumb.icon'); ?>
                    </span>
                    <span class="host_title_link">
                        &nbsp;<?php echo $this->htmlLink($this->host, $this->host->getTitle(), array('target' => '_blank')); ?>
                    </span>
                </span>
                <span class="host_cancel_link">
                    <span>[</span>
                    <a class="edit_new_host_link button_link" href="javascript:void(0)" ><?php echo $this->translate("Edit this host") ?></a>
                    <span class="edit_new_sep">|</span>
                    <a class="change_host_link button_link" href="javascript:void(0)"><?php echo $this->translate("Change") ?></a>
                    <span>]</span>
                </span>
            </div>
        </div>
    </div>
    <script type="text/javascript">
                    var onHostTypeChange;
                    en4.core.runonce.add(function() {
    //            if ($('host-host_description')) {
    //              makeRichTextarea('host-host_description');
    //            }
                        if ($('host_type_select'))
                            $('host_type').value = $('host_type_select').value;
                        if ($('add_new_host').value == 0) {
                            $('siteevent_info_host').show();
                            $$('.organizer_info').getParent('.form-wrapper').hide();
                        } else {
                            $('siteevent_info_host').hide();
                            $$('.organizer_info').getParent('.form-wrapper').show();
                        }
                        $$('.add_new_host_link,.add_host_cancel').addEvent('click', function() {
                            $('siteevent_info_host').toggle();
                            $$('.organizer_info').getParent('.form-wrapper').toggle();
                            $('add_new_host').value = $('add_new_host').value == 0 ? 1 : 0;
                        });
                        $$('.change_host_link,.change_host_cancel').addEvent('click', function() {
                            $('edit_host_info').toggle();
                            $('host_info').toggle();
                            $('host_auto').value = '';
                        });

                        $$('.edit_new_host_link').addEvent('click', function() {
                            Smoothbox.open("<?php echo $this->url(array('action' => 'edit', 'controller' => 'organizer', 'parentRefresh' => 0), 'siteevent_extended', true); ?>/type/" + $('host_type').value + '/organizer_id/' + $('host_id').value);
                        });
                        var usersAutocomplete = new Autocompleter.Request.JSON('host_auto', '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'index', 'action' => 'get-host-suggest'), 'default', true) ?>', {
                            'postVar': 'text',
                            'cache': false,
                            'minLength': 1,
                            'selectMode': 'pick',
                            'autocompleteType': 'tag',
                            'className': 'tag-autosuggest seaocore-autosuggest',
                            'customChoices': true,
                            'filterSubset': true,
                            'multiple': false,
                            'postData': {
                                'type': $('host_type_select').value
                            },
                            'injectChoice': function(token) {
                                var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
                                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
                                this.addChoiceEvents(choice).inject(this.choices);
                                choice.store('autocompleteChoice', token);
                            }
                        });
                        usersAutocomplete.addEvent('onSelection', function(element, selected, value, input) {

                            document.getElement('.host_title_link').innerHTML = selected.retrieve('autocompleteChoice').link;
                            document.getElement('.host_photo').innerHTML = selected.retrieve('autocompleteChoice').photo;
                            $('host_id').value = selected.retrieve('autocompleteChoice').id;
                            $('host_type').value = $('host_type_select').value;
                            $('edit_host_info').hide();
                            $('host_info').show();
                            if ($('host_type_select').value === 'siteevent_organizer') {
                                $$('.edit_new_host_link,.edit_new_sep').show();
                            } else {
                                $$('.edit_new_host_link,.edit_new_sep').hide();
                            }
                        });
                        onHostTypeChange = function() {
                            usersAutocomplete.setOptions({postData: {
                                    'type': $('host_type_select').value
                                }});
                            $('host_auto').value = '';
                            if ($('host_type_select').value === 'siteevent_organizer') {
                                $$('.add_new_host_link,.add_new_sep').setStyle("display", "inline-block");
                            } else {
                                $$('.add_new_host_link,.add_new_sep').hide();
                            }
                        }
                        onHostTypeChange();
                        if ($('host_type_select').value === 'siteevent_organizer') {
                            $$('.edit_new_host_link,.edit_new_sep').show();
                        } else {
                            $$('.edit_new_host_link,.edit_new_sep').hide();
                        }
                    });</script>
<?php elseif ($this->fieldType == "host_links"): ?>
    <?php
    $facebook_url = isset($_POST['host_facebook']) && $_POST['host_facebook'] ? $_POST['host_facebook'] : '';
    $twitter_url = isset($_POST['host_twitter']) && $_POST['host_twitter'] ? $_POST['host_twitter'] : '';
    $web_url = isset($_POST['host_website']) && $_POST['host_website'] ? $_POST['host_website'] : '';
    $host_link = isset($_POST['host_link']) && $_POST['host_link'] ? true : false;

    if ($this->host):
        if (empty($facebook_url))
            $facebook_url = $this->host->facebook_url;
        if (empty($twitter_url))
            $twitter_url = $this->host->twitter_url;
        if (empty($web_url))
            $web_url = $this->host->web_url;

        if ($facebook_url || $twitter_url || $web_url)
            $host_link = true;
    endif;
    ?>
    <div class="form-wrapper">
        <div class="form-label">
            <label for="host_title" class="optional">
                <?php echo $this->translate($this->label); ?>
            </label>
        </div>
        <div class="form-element organizer_info">
            <input type="checkbox" id="host_link" name="host_link" onclick="$('host_links').toggle();"  <?php if ($host_link): ?> checked="checked" <?php endif; ?>>
            <label for="host_link" class="optional" for="host_link"><?php echo $this->translate('Include a link to host social pages e.g. Facebook, Twitter or Website'); ?></label>
            <div id="host_links" style="<?php if ($host_link): ?> display:block; <?php else: ?> display:none;<?php endif; ?>margin-top: 10px;">
                <ul class="host-links">
                    <li>
                        <label for="hostlinks[host_facebook]">https://facebook.com/</label>
                        <input type="text" name="host_facebook" style="width: 150px;" value="<?php
                        echo $facebook_url;
                        ?>" />
                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/help.png" title="<?php echo $this->translate('Enter your Facebook address to include a link to your facebook page'); ?>" >	
                    </li>
                    <li>
                        <label for="hostlinks[host_twitter]">https://twitter.com/</label>
                        <input type="text" name="host_twitter"  style="width: 150px;" value="<?php
                        echo $twitter_url;
                        ?>"/>
                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/help.png" title="<?php echo $this->translate('Enter your Twitter address to include a link to your twitter page') ?>" >	
                    </li>
                    <li>
                        <label for="hostlinks[host_website]"><?php echo $this->translate('Website'); ?>:</label>
                        <input type="text" name="host_website" style="width: 150px;" value="<?php echo $web_url; ?>" />
                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/help.png" title="<?php echo $this->translate("Enter host’s Website address to include a link to it from this event.") ?>" >	
                    </li>
                </ul>
            </div>
        </div>
    </div>
<?php elseif ($this->fieldType == "host_title"): ?>
    <?php
    $host_title = '';
    if (isset($_POST['host_title']) && $_POST['host_title']): $host_title = $_POST['host_title'];
    endif;
    ?>
    <div id="host_title-wrapper" class="form-wrapper" style="display: block;">
        <div id="host_title-label" class="form-label">
            <label for="host_title" class="optional">
                <?php echo $this->translate($this->label); ?>
            </label></div>
        <div id="host_title-element" class="form-element">
            <input type="text" name="host_title" id="host_title" value="<?php echo $host_title ?>" class="organizer_info">
            <span>
                <a class="add_host_cancel button_link" href="javascript:void(0)" ><?php echo $this->translate("Cancel") ?></a>
            </span>
        </div></div>
<?php endif; ?>
