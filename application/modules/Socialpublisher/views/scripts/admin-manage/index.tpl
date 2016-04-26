<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Social Publisher
 * @copyright  Copyright 2013-2014 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @author     trunglt
 */
?>
<h2>
  <?php echo $this->translate('Social Publisher')?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class="tabs">
    <?php
    // Render the menu
    // ->setUlClass()
    echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render()?>
  </div>
<?php endif; ?>

<div class="clear"><div class="settings">
    <form enctype="application/x-www-form-urlencoded" class="global_form"
    action="<?php echo $this->url(array('module' => 'socialpublisher',"controller" => "manage")) ?>" method="post">
    <div>
        <h3><?php echo $this->translate('Manage Types')?></h3>
        <h4>
          <?php echo $this->translate("SOCIALPUBLISHER_ADMINMANAGE_INDEX_DESCRIPTION")?>
        </h4>
        <?php if (isset($this->is_post)): ?>
            <ul class="form-notices"><li><?php echo $this->translate('Your changes have been saved.')?></li></ul>
        <?php endif; ?>
        <?php if (count($this->types)): ?>
        <div class="form-elements">
            <?php foreach($this->types as $type): ?>
                <?php $api = Engine_Api::_()->socialpublisher(); ?>
                <?php $settings = $api->getTypeSettings($type)?>
                <?php $module_name = $api->getModuleTitle($type); ?>
                <br/>
                <div class="socialpublisher_header"><?php echo $this->translate(strtoupper($module_name)) ?></div>
                <?php
                $facebook_checked = in_array('facebook', $settings['providers'])?'checked':'';
                $twitter_checked = in_array('twitter', $settings['providers'])?'checked':'';
                $linkedin_checked = in_array('linkedin', $settings['providers'])?'checked':'';
                ?>

                <?php foreach ($settings['types'] as $settings_type):?>
                <input type='hidden' value='<?php echo $settings_type?>' name="values[<?php echo $type ?>][types][]">
                <?php endforeach; ?>
                <input type='hidden' value='<?php echo $settings['title']?>' name="values[<?php echo $type ?>][title]">
                <div id="<?php echo $type ?>_active-wrapper" class="form-wrapper">
                	<div id="<?php echo $type ?>_active-label" class="form-label">
                		<label for="<?php echo $type ?>_active" class="optional"><?php echo $this->translate('Active')?></label>
                	</div>
                	<div id="<?php echo $type ?>_active-element" class="form-element">
                		<ul class="form-options-wrapper">
                			<li class="socialpublisher_module_active">
                			    <input type="radio" name="values[<?php echo $type ?>][active]"
                			        id="<?php echo $type ?>_1" value="1" <?php echo $settings['active']?'checked':'' ?>>
                				<label for="<?php echo $type ?>_1"><?php echo $this->translate('Yes')?></label>
                			</li>
                			<li class="socialpublisher_module_active">
                			    <input type="radio" name="values[<?php echo $type ?>][active]"
                				    id="<?php echo $type ?>_0" value="0" <?php echo $settings['active']?'':'checked' ?> >
                				<label for="<?php echo $type ?>_0"><?php echo $this->translate('No')?></label>
                			</li>
                		</ul>
                	</div>
                </div>
                <div id="<?php echo $type ?>_providers-wrapper" class="form-wrapper">
                	<div id="<?php echo $type ?>_providers-label" class="form-label">
                		<label for="<?php echo $type ?>_providers" class="optional"><?php echo $this->translate('Publish Providers')?></label>
                	</div>
                	<div id="<?php echo $type ?>_providers-element" class="form-element">
                		<ul class="form-options-wrapper">
                            <li class="socialpublisher_module_active">
                                <input type="checkbox" id="<?php echo $type ?>_providers_fb" name="values[<?php echo $type ?>][providers][]" value="facebook" <?php echo $facebook_checked?> >
                                <label for="<?php echo $type ?>_providers_fb"><?php echo $this->translate("Facebook")?></label>
                            </li>
                            <li class="socialpublisher_module_active">
                                <input type="checkbox" id="<?php echo $type ?>_providers_tw" name="values[<?php echo $type ?>][providers][]" value="twitter" <?php echo $twitter_checked?> >
                                <label for="<?php echo $type ?>_providers_tw"><?php echo $this->translate("Twitter")?></label>
                            </li>
                            <li class="socialpublisher_module_active">
                                <input type="checkbox" id="<?php echo $type ?>_providers_li"name="values[<?php echo $type ?>][providers][]" value="linkedin" <?php echo $linkedin_checked?> >
                                <label for="<?php echo $type ?>_providers_li"><?php echo $this->translate("LinkedIn")?></label>
                            </li>
                        </ul>
                	</div>
                </div>
            <?php endforeach; ?>
            <div id="submit-wrapper" class="form-wrapper">
                <div id="submit-label" class="form-label">&nbsp;</div>
                <div id="submit-element" class="form-element">
                    <button name="submit" id="submit" type="submit"><?php echo $this->translate('Save Changes')?></button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        </div>
    </form>
</div></div>