<?php
$api = Engine_Api::_()->socialpublisher();
$viewer = Engine_Api::_()->user()->getViewer();
?>

<div class="clear">
	<div class="settings">
		<form enctype="application/x-www-form-urlencoded" class="global_form"
			action="<?php echo $this->url(array('action' => 'settings'))?>"
			method="post">
		    <h3><?php echo $this->translate('Manage Types')?></h3>
		    <p><?php echo $this->translate("SOCIALPUBLISHER_USERMANAGE_INDEX_DESCRIPTION")?></p>
			<div>
			    <br/>
				<?php if ($this->is_post):?>
				<ul class="form-notices">
					<li><?php echo $this->translate('Your changes have been saved.')?></li>
				</ul>
				<?php endif;?>
				<?php if (count($this->types) > 0):?>
				<div class="form-elements">
					<br>
					<?php foreach ($this->types as $type):?>
			            <?php $admin_settings = $api->getTypeSettings($type);?>
    					<?php $user_settings = $api->getUserTypeSettings($viewer->getIdentity(), $type);?>
					    <?php $module_name = $api->getModuleTitle($type);
					    // values array to save user settings
                        $options = array (
                                Socialpublisher_Plugin_Constants::OPTION_ASK => '',
                                Socialpublisher_Plugin_Constants::OPTION_AUTO => '',
                                Socialpublisher_Plugin_Constants::OPTION_NOT_ASK => ''
                                );

                        switch ($user_settings['option']) 
                        {
                            case Socialpublisher_Plugin_Constants::OPTION_ASK:
                                $options[Socialpublisher_Plugin_Constants::OPTION_ASK] = 'checked';
                                break;
                            case Socialpublisher_Plugin_Constants::OPTION_AUTO:
                                $options[Socialpublisher_Plugin_Constants::OPTION_AUTO] = 'checked';
                                break;
                            case Socialpublisher_Plugin_Constants::OPTION_NOT_ASK:
                                $options[Socialpublisher_Plugin_Constants::OPTION_NOT_ASK] = 'checked';
				                break;
                        }
					    ?>
                        <div class='socialpublisher_header'><?php echo $this->translate(strtoupper($module_name)) ?></div>
    					<!-- Publish Providers -->
    					<div id="<?php echo $module_name ?>_providers-wrapper" class="socialpublisher_form_wrapper">
    						<div id="<?php echo $module_name ?>_providers-label" class="form-label">
    							<label for="<?php echo $module_name ?>_providers" class="optional" style='float:left;'><?php echo $this->translate('Publish Providers')?></label>
    						</div>
    						<div id="<?php echo $module_name ?>_providers-element" class="form-element">
					            <?php if (count($admin_settings['providers'])): ?>
        							<ul class="">
        								<li class="socialpublisher_module_providers">
        					                <?php if (in_array('facebook', $admin_settings['providers'])): ?>
            								    <input type="checkbox" id="<?php echo $module_name ?>_provider_facebook" name="values[<?php echo $type ?>][providers][]" value="facebook" <?php echo in_array('facebook', $user_settings['providers'])?'checked':'' ?>>
            									<label for="<?php echo $module_name ?>_provider_facebook" ><?php echo $this->translate('Facebook')?></label>
            				            	<?php else: ?>
            							        <?php echo '&nbsp;'?>
            							    <?php endif;?>
        								</li>
        								<li class="socialpublisher_module_providers">
        					                <?php if (in_array('twitter', $admin_settings['providers'])): ?>
            								    <input type="checkbox" id="<?php echo $module_name ?>_provider_twitter" name="values[<?php echo $type ?>][providers][]" value="twitter" <?php echo in_array('twitter', $user_settings['providers'])?'checked':'' ?>>
            									<label for="<?php echo $module_name ?>_provider_twitter"><?php echo $this->translate('Twitter')?></label>
            				            	<?php else: ?>
            							        <?php echo '&nbsp;'?>
            							    <?php endif;?>
        								</li>
        								<li class="socialpublisher_module_providers">
        					                <?php if (in_array('linkedin', $admin_settings['providers'])): ?>
            								    <input type="checkbox" id="<?php echo $module_name ?>_provider_linkedin" name="values[<?php echo $type ?>][providers][]" value="linkedin" <?php echo in_array('linkedin', $user_settings['providers'])?'checked':'' ?>>
            									<label for="<?php echo $module_name ?>_provider_linkedin"><?php echo $this->translate('LinkedIn')?></label>
            				            	<?php else: ?>
            							        <?php echo '&nbsp;'?>
            							    <?php endif;?>
        								</li>
        							</ul>
    							<?php endif;?>
    						</div>
    					</div>
					<!-- End Publish Providers -->
					<!-- Options -->
					<div id="<?php echo $module_name ?>_options-wrapper" class="socialpublisher_form_wrapper">
						<div id="<?php echo $module_name ?>_options-label" class="form-label">
							<label for="<?php echo $module_name ?>_options" class="optional" style="float:left;"><?php echo $this->translate('Options')?></label>
						</div>
						<div id="<?php echo $module_name ?>_options-element" class="form-element">
							<ul class="">
								
								<li class="socialpublisher_module_options">
								    <label for="<?php echo $module_name ?>_1"><input type="radio" name="values[<?php echo $type ?>][option]" id="<?php echo $module_name ?>_1" value="1" <?php echo $options[Socialpublisher_Plugin_Constants::OPTION_AUTO] ?> ><?php echo $this->translate("Auto publish") ?></label>
								</li>
								<li class="socialpublisher_module_options">
								    <label for="<?php echo $module_name ?>_2"><input type="radio" name="values[<?php echo $type ?>][option]" id="<?php echo $module_name ?>_2" value="2" <?php echo $options[Socialpublisher_Plugin_Constants::OPTION_NOT_ASK] ?> ><?php echo $this->translate("Don&#39;t publish")?></label>
							    </li>
							    <li class="socialpublisher_module_options">
								    <label for="<?php echo $module_name ?>_0"><input type="radio" name="values[<?php echo $type ?>][option]" id="<?php echo $module_name ?>_0" value="0" <?php echo $options[Socialpublisher_Plugin_Constants::OPTION_ASK] ?> ><?php echo $this->translate("Ask me everytime")?></label>
							    </li>
							</ul>
						</div>
					</div>
                    <!-- End Options -->
					<?php endforeach;?>
					<div id="submit-wrapper" class="form-wrapper">
					    <br/>
						<div id="submit-element" class="form-element">
							<button name="submit" id="submit" type="submit"><?php echo $this->translate('Save Changes')?></button>
						</div>
					</div>
				</div>
				<?php endif;?>
			</div>
		</form>
	</div>
</div>