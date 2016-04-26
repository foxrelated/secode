<script type="text/javascript">
function openauthsocialbridge(pageURL)
{
    var w = 800;
    var h = 500;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var newwindow = window.open (pageURL, '', 'toolbar=no,location=no,directories=no,status=no,menubar=no, scrollbars=yes,resizable=yes,copyhistory=no,width='+w+',height='+h+',top='+top+',left='+left);
    if (window.focus) {newwindow.focus();}
    return newwindow;
}
</script>
<style>
html, body {
	/*overflow-y: hidden;*/
}
</style>
<div>
	<form method="post" action="<?php echo $this->url()?>" class=''>
	    <h3><?php echo $this->translate('Publish')?></h3>
		<div class="socialpublisher_clear">&nbsp;</div>
		<div class="socialpublisher_user_photo" style="width: 510px">
			<?php echo $this->itemPhoto($this->viewer, 'thumb.icon')?>
			<textarea placeholder = '<?php echo $this -> translate("What’s happening?")?>' rows="" cols="" id='message' name='message' style='width: 448px;resize: none;height: 45px;'></textarea>			
		</div>

		<div class="socialpublisher_user_options">
			<div class="socialpublisher_clear">&nbsp;</div>
			<fieldset class="socialpublishers_content">
    			<legend style='font-weight:bold;'><?php echo $this->translate('Share Content')?></legend>
    			<div class="socialpublishers_content">
    				<div class="socialpublisher_object_photo">
    					<img src="<?php echo $this->photo_url?>" />
    				</div>
    				<div class="socialpublisher_object_content" style='word-break: break-all; -ms-word-wrap: break-word;'>
	    				<div style="font-weight: bold">
    	    				<?php if($this->title):?>
    		    				<a href="<?php echo $this->shareLink ?>" target = '_blank' ><?php echo $this->title ?></a>
    		    			<?php else:?>
    		    				<?php echo $this->htmlLink($this->shareLink, $this->shareLink, array('target' => '_blank'))?>
    	    				<?php endif;?>
	    				</div>
	    				<?php if($this->des):?>
		 					<div>
		    				<?php echo $this->des ?>
		    				</div>
		    			<?php endif;?>
    				</div>
				</div>
			</fieldset>
        <?php
        $api = Engine_Api::_()->socialpublisher();
        // values array to save user settings
        $module_settings = $this->module_settings;
        ?>
		<?php if (count($module_settings['providers'])): ?>
            <ul class="socialpublisher_share_providers">
            <?php foreach($module_settings['providers'] as $provider):?>
            <?php
                $provider_settings = $api->isValidProvider($provider);
                if(!$provider_settings)
                {
                    continue;
                }
            ?>
            <?php
            	$obj = Socialbridge_Api_Core::getInstance($provider);
            	$me = null;
            	switch ($provider)
            	{
            		case 'facebook':
            			if(!empty($_SESSION['socialbridge_session']['facebook'])) 
            			{
            			    try {
            			        //check permission
            				    $me = $obj->getOwnerInfo(array('access_token' => $_SESSION['socialbridge_session']['facebook']['access_token']));
            				    $uid = $me['id'];
            				    $permissions = $obj->hasPermission(array(
            				            'uid' => $uid,
            				            'access_token' => $_SESSION['socialbridge_session']['facebook']['access_token']
            				            ));
            				    if (empty($permissions[0]['publish_actions'])) {
            				        $url = $obj->getConnectUrl() .
            				        '&scope=publish_actions'.
            				        '&' . http_build_query(array(
            				                'callbackUrl' => $this->callbackUrl.'?service='.$provider,
            				                'is_from_socialpublisher' => 1
            				        ));
            				        $me = null;
                                }

            			    }
            			    catch(Exception $e) {
                                $me = null;
            			    }
            			}
            			else {
            				$url = $obj->getConnectUrl() .
            					'&scope=user_photos,publish_actions'.
            					'&' . http_build_query(array(
            					'callbackUrl' => $this->callbackUrl.'?service='.$provider,
                                'is_from_socialpublisher' => 1
            				));
            			}
            			break;
            		case 'twitter':
                        if (!empty($_SESSION['socialbridge_session']['twitter'])) {
                                try {
                                    $me = $obj->getOwnerInfo($_SESSION['socialbridge_session']['twitter']);
                                } catch(Exception $e) {
                                    $me = null;
                                }
                        }
                        else {
                            $url = $obj->getConnectUrl() .
                            '&' . http_build_query(array(
                                    'callbackUrl' => $this->callbackUrl.'?service='.$provider,
                                    'is_from_socialpublisher' => 1
                            ));
                        }
            			break;
            		case 'linkedin':
            			if(!empty($_SESSION['socialbridge_session']['linkedin']))
            			{
            				try {
                                    $me = $obj->getOwnerInfo($_SESSION['socialbridge_session']['linkedin']);
                                } catch(Exception $e) {
                                    $me = null;
                                }
            			}
            			else {
            				$url = $obj->getConnectUrl() .
            					'&scope=r_basicprofile,w_share'.
            					'&' . http_build_query(array(
            					'callbackUrl' => $this->callbackUrl.'?service='.$provider,
                                'is_from_socialpublisher' => 1
            				));
            			}
            			break;
            	}

            ?>
            <?php if ($me != null): ?>
				<li class="socialpublisher_provider_icon socialpublisher_<?php echo $provider?>">
					<div class="socialpublishers_share_providers_checkbox">
                   		<input type="checkbox" value="<?php echo $provider?>" name="providers[]" <?php echo in_array($provider, $module_settings['providers'])?'checked':''?>>
                   		<span id="showpopup_span_connected_<?php echo $provider?>"><?php echo $this->translate('Connected as %s', $me['displayname'])?></span>
                   	</div>
		        </li>
		    <?php else: ?>
		        <li class="socialpublisher_provider_icon socialpublisher_<?php echo $provider?>">
					<div class="socialpublishers_share_providers_checkbox">
                   		<input onclick="openauthsocialbridge('<?php echo $url ?>',this);" type="checkbox" value="<?php echo $provider?>" name="providers[]" <?php echo (in_array($provider, $module_settings['providers']) && $me)?'checked':''?>>
                   		<span id="showpopup_span_connected_<?php echo $provider?>"><?php echo $this->translate("Not connected")?> (<a href="javascript:void(0);" onclick="openauthsocialbridge('<?php echo $url ?>',this);"><?php echo $this->translate('connect')?></a>)</span>
                   	</div>
		        </li>
		    <?php endif; ?>
        <?php endforeach; ?>
        </ul>
    <?php endif;?>
		</div>
		<div class="socialpublisher_clear"></div>
		<div class="socialpublisher_elements" style="padding-left: 10px;">
			<div>
				<button type="submit" name="publish"><?php echo $this->translate('Publish')?></button>
				<button type="submit" name="cancel"><?php echo $this->translate('Cancel')?></button>
			</div>
			<div class="socialpublishers_share_providers_checkbox" style="margin-top: 7px;">
			<input type="checkbox" name="check" id="socialpublisher_check">
			<span><?php echo $this->translate("Don&#39;t ask me again")?></span>
			</div>
		</div>
	</form>
</div>