<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: check-in.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php  
	$this->headTranslate(array('Add Photo'));
?>

<?php if($this->show_success_message):?>
  <?php echo $this->show_success_message;?>
  <script type="text/javascript">
    setTimeout(function(){
      window.location.href= '<?php echo $this->href?>';
    }, 100);
  </script>
<?php endif;?>

<?php 

$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/scripts/composer.js');

$isMobile = Engine_Api::_()->seaocore()->isMobile(); 
      $advancedActivity = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
?>
<?php     
	$this->headLink()
	->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css');
	if($advancedActivity):
			$this->headLink()
			->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Advancedactivity/externals/styles/style_advancedactivity.css');
	endif;
		$this->headScript()
			->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js');
?>

<?php if(!$this->show_success_message):?>

  <?php if(!$isMobile) :?>
	<?php
		$this->headScript()
			->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/scripts/composer.js')
			->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
			->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
			->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
			->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooVerticalScroll.js');

			if($advancedActivity):
			$this->headScript()
				->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/scripts/feed-tags-checkin.js');
				//->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/scripts/composer_tag.js');
				$this->headLink()
				->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Advancedactivity/externals/styles/style_statusbar.css');
			endif;
	?>



<script type="text/javascript">
	var composeInstanceCheckin;
	en4.core.runonce.add(function() {
		// @todo integrate this into the composer
		if(  !DetectMobileQuick() && !DetectIpad() ) {
			composeInstanceCheckin = new ComposerCheckin('body', {
				menuElement : 'checkin-compose-menu',
				baseHref : '<?php echo $this->baseUrl() ?>',
				useContentEditable : true,
				lang : {
					'Post Something...' : '<?php echo $this->string()->escapeJavascript($this->translate('Hiiiii.........')) ?>'
				}
			});
		}
	});
</script>

<?php if($advancedActivity):?>
	<script type="text/javascript">
		en4.core.runonce.add(function() {
			composeInstanceCheckin.addPlugin(new ComposerCheckin.Plugin.Aaftag({
				enabled:true,
				suggestOptions : {
					'url' : en4.core.baseUrl+'advancedactivity/friends/suggest-tag/includeSelf/1',
					'data' : {
						'format' : 'json'
					},
					'maxChoices':'<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.feed.suggest.limit',10); ?>'
				},
				'suggestProto' : 'request.json'
			}));
		});
	</script>
<?php endif;?>

<div class="stcheckin_checkin_box" id="stcheckin_checkin_box">
	<form method="post" action="<?php echo $this->url(array('module' => 'sitetagcheckin', 'controller' => 'checkin', 'action' => 'check-in',
   'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, "checkin_use" => $this->checkin_use, 'checkin_verb' => $this->checkin_verb, 'checkedinto_verb' => $this->checkedinto_verb, 'tab' => $this->tab), 'default', true) ?>" class="sitetagcheckin_status" enctype="application/x-www-form-urlencoded" id="seaocheckinform">
    <input type="hidden" name="checkin_your" value="<?php echo $this->checkin_your;?>" id="checkin_your" />
    <?php if($this->resource_type != 'user'):?>
			<h3>
				<?php echo $this->translate('Publish an Update for your Action');?>
			</h3>
    <?php else:?>
			<h3>
				<?php echo $this->translate('Add Your Location on Map');?>
			</h3>
    <?php endif;?>
   <div class="stcheckin_checkin_box_cont">
   	<table width="100%" height="100%">
   		<tr>
   			<td valign="top">
          <?php if($this->resource_type == 'user'):?>
          <input type="hidden" name="checkinstr_status" value="" id="checkinstr_status" />
				  <div class="sitetagcheckin_autosuggest_location" id="sitetagcheckin_autosuggest_location">
						<?php
							//RENDER LOCAION WIDGET
							echo $this->content()->renderWidget("sitetagcheckin.location-sitetagcheckin", array('showSuggest' => 0)); 
						?>
					</div>
          <?php endif;?>
					<div class="stcheckin_compose_container">
						<textarea rows="6" cols="45" id="body" name="body" class="compose-textarea" style="display: none;">
              <?php echo $this->translate(Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.default.textarea.text', 'I am here!'));?>
            </textarea>
			       <!-- <a title="Close" class="adv_post_close" onclick="hidestatusbox();" href="javascript:void(0);"><?php //echo $this->translate("Close");?></a>  -->
						<?php if($advancedActivity):?>
							<div class="adv_post_container_tagged_cont">
								<span class="aaf_mdash"></span>  
								<span id="friendas_tag_body_aaf_content" class=""> </span>
								<span class="aaf_dot"></span>
							</div>
				  	<?php endif;?>
				  	<div id="stcheckin-checkin-compose-menu">
			      <?php if($advancedActivity):?>
							<script type="text/javascript">
								<?php $composerOptions = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy")); ?>
								<?php if (in_array("emotions", $composerOptions)) : ?>
									var hideEmotionIconClickEnable=false;
									function setEmoticonsBoard(){
										if(composeInstanceCheckin)
											composeInstanceCheckin.focus();
										$('emotion_lable').innerHTML="";
										$('emotion_symbol').innerHTML="";
										hideEmotionIconClickEnable=true;    
										var  a=$('emoticons-button');
										a.toggleClass('emoticons_active');
										a.toggleClass('');
										var  el=$('emoticons-board');
										el.toggleClass('seaocore_embox_open');
										el.toggleClass('seaocore_embox_closed'); 
									}
			
									function addEmotionIcon(iconCode){ 
										var content =composeInstanceCheckin.elements.body.get('html');   
										content=content.replace(/(<br>)$/g, "");
										content =  content +' '+ iconCode; 
										composeInstanceCheckin.setContent(content);
									}
			
									//hide on body click
									$(document.body).addEvent('click',hideEmotionIconClickEvent.bind());
									function hideEmotionIconClickEvent(){
										if(!hideEmotionIconClickEnable){       
											$('emoticons-board').removeClass('seaocore_embox_open').addClass('seaocore_embox_closed');      
										}
										hideEmotionIconClickEnable=false;
									}  
			
									function setEmotionLabelPlate(lable,symbol){
										$('emotion_lable').innerHTML=lable;
										$('emotion_symbol').innerHTML=symbol;
									}
								<?php endif; ?>
						
								var togglePrivacyPulldownEventEnable=false;
								var togglePrivacyPulldown = function(event, element) {
									event = new Event(event);
									togglePrivacyPulldownEventEnable=true;
									$$('.advancedactivity_privacy_list').each(function(otherElement) {
										if( otherElement.id == 'advancedactivity_friend_list') {
											return;
										}
										var pulldownElement = otherElement.getElement('aaf_privacy_pulldown_active');
										if( pulldownElement ) {
											pulldownElement.addClass('aaf_privacy_pulldown').removeClass('aaf_privacy_pulldown_active');
										}
									});
									if( $(element).hasClass('aaf_privacy_pulldown') ) {
										element.removeClass('aaf_privacy_pulldown').addClass('aaf_privacy_pulldown_active');
									} else {
										element.addClass('aaf_privacy_pulldown').removeClass('aaf_privacy_pulldown_active');
									}
									OverText.update();
								}
									
								//hide on body click
								var togglePrivacyPulldownClickEvent= function() {
									var element=$('pulldown_privacy_list');
									if(!togglePrivacyPulldownEventEnable && element && $(element).hasClass('aaf_privacy_pulldown_active') ){     
										element.addClass('aaf_privacy_pulldown').removeClass('aaf_privacy_pulldown_active');     
									}
									togglePrivacyPulldownEventEnable=false; 
								}
								$(document.body).addEvent('click',togglePrivacyPulldownClickEvent.bind());
						
								var setAuthViewValue =function(value,label,classicon) {  
									var oldValue=$('auth_view').value;
									var oldValueArray = oldValue.split(",");
									for (var i = 0; i < oldValueArray.length; i++){       
										var tempListElement= $('privacy_list_'+oldValueArray[i]); 
										tempListElement.removeClass('aaf_tab_active').addClass('aaf_tab_unactive'); 
									}
									var tempListElement=$('privacy_list_'+value);
									tempListElement.addClass('aaf_tab_active').removeClass('aaf_tab_unactive');
									$('auth_view').value=value;   
									$('show_default').innerHTML= '<i class="aaf_privacy_pulldown_icon '+classicon+' "></i><span>'+label+'</span><i class="aaf_privacy_pulldown_arrow"></i>';
									$("adv_custom_list_privacy_lable_tip").innerHTML=en4.core.language.translate("<?php echo $this->string()->escapeJavascript($this->translate('Share with %s')) ?>",label);
								}

  function addMoreList(){
    Smoothbox.open('<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'add-more-list'), 'default', true) ?>');
     var element=$('pulldown_privacy_list');
      if( $(element).hasClass('aaf_privacy_pulldown') ) {
        element.removeClass('aaf_privacy_pulldown').addClass('aaf_privacy_pulldown_active');
      } else {
        element.addClass('aaf_privacy_pulldown').removeClass('aaf_privacy_pulldown_active');
      }
  }
							</script>	  	
				  	<?php endif;?>
			      <?php if($advancedActivity):?>
				  	  <?php
						  $SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);
						  if (in_array("emotions", $composerOptions) && $SEA_EMOTIONS_TAG && isset($SEA_EMOTIONS_TAG[0])):
						    ?>
						    <span id="emoticons-button"  class="checkin_post_smile sitetag_checkin_post_composer_button"  onclick="setEmoticonsBoard()">
						      <p class="seaocheckin_composer_tip">
						    <?php echo $this->translate("Insert Emoticons") ?>
						        <img alt="" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />
						      </p>
						      <span id="emoticons-board"  class="seaocore_embox seaocore_embox_closed" >
						        <span class="seaocore_embox_arrow"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/tooltip_arrow_top.png" alt="" /></span>
						        <span class="seaocore_embox_title">
						          <span class="fleft" id="emotion_lable"></span>
						          <span class="fright"id="emotion_symbol" ></span>
						        </span>
						          <?php foreach ($SEA_EMOTIONS_TAG[0] as $tag_key => $tag): ?>         
						          <span class="seaocore_embox_icon" onmouseover='setEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag))) ?>","<?php echo $this->string()->escapeJavascript($tag_key) ?>")' onclick='addEmotionIcon("<?php echo $this->string()->escapeJavascript($tag_key) ?>")'  title="<?php echo $this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag)) . "&nbsp;" . $tag_key; ?>"><?php
						      echo preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img src=\"" . $this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" alt=\"$2\" />", $tag);
						      ?></span>
						    <?php endforeach; ?>
						      </span>					
						    </span>
						  <?php endif; ?>
						  <?php endif;?>
						  <?php if($advancedActivity):?>
								<span class="checkin_post_user sitetag_checkin_post_composer_button" onclick="toogleTagWith()" style="display:block;">
									<p class="seaocheckin_composer_tip">
									<?php echo $this->translate("Add People") ?>
										<img alt="" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />
									</p>
								</span>	
						  <?php endif; ?>
				  	</div>
				  </div>	
				  
				  <?php if($advancedActivity):?>
						<div id="adv_post_container_tagging" class="adv_post_container" style="display:none;margin:0;">
							<div class="adv_post_container_tagging" title="<?php echo $this->translate('Who are you with?') ?>" style="margin:0 0 5px;">
								<div class="form-wrapper" id="toValues-wrapper" style="height: auto;">
									<div class="form-label" id="toValues-label"></div>
									<div class="form-element" id="toValues-element" style="height: 0px;">
										<input type="hidden" id="toValues"  name="toValues" />
									</div>
								</div>
								<input type="text" id="friendas_tag_body_aaf" class="compose-textarea"  name="friendas_tag_body_aaf" /> 
							</div>
						</div>	
				  <?php endif;?>
				  
					<div class="stcheckin_post_options" id="seocheckinphotoactivator">
						<?php if($this->checkin_use):?>
			        <div id="sitetagcheckin_current_date" class="stcheckin_current_date" onclick="showDateFields();">
			          <span><a><?php $a = date("F j, Y", strtotime(date('Y-m-d')));
                       $explodeArray = explode(' ', $a);
                         echo $this->translate($explodeArray[0]) . ' ' . $explodeArray[1] . ' ' . $explodeArray[2] 
                        ?></a></span>
			          <span onclick="showDateFields();" class="stcheckin_post_edit_date"></span>
			        </div>
							
							<div class="stcheckin_post_date" style="display:none;" id="sitetag_checkin_post_date">
								<select id="year" name="year">
									<option label="Year" value="Year" disabled="disabled"><?php echo $this->translate('Year');?></option>
									<?php $curYear = date('Y'); ?>
									<?php for ($i = 0; $i <= 110; $i++) :?>
										<option label="<?php echo $curYear;?>" value="<?php echo $curYear;?>" <?php if($i==0):?> selected="selected" <?php endif;?>><?php echo $curYear;?></option>
									<?php $curYear--;?>
									<?php endfor;?>
								</select>
			
								<a  onclick="showMonth(0);" href="javascript:void(0);" id="addmonth" style="display:none;"><?php echo $this->translate('+ Add Month');?></a>
								<select id="month" name="month" onblur="showAddmonth(2)" onclick="showMonth(1)" onchange="showAddday(2)" style="display:block;">
									<option label="Month" value="0"><?php echo $this->translate('Month');?></option>
			            <?php $curMonth = (int) date('m'); ?>
									<?php for ($k = 1; $k <= 12; $k++):?>
										<?php $month = date('F', mktime(0, 0, 0, $k, 1));?>
										<option label="<?php echo $month;?>" value="<?php echo $k;?>" <?php if($k==$curMonth):?> selected="selected" <?php endif;?>><?php echo $this->translate($month);?></option>
									<?php endfor;?>
								</select>
			 
								<a style="display:none;" id="addday"  onclick="showDay(0);" href="javascript:void(0);"><?php echo $this->translate('+ Add Day');?></a>
								<select id="day" name="day" style="display:block;">
			          </select>
							</div>
					  <?php endif;?>
			
			  <?php if($advancedActivity):?>
			  <div class="sitetag_checkin_post_privacy" id="advanced_compose-menu">	
			    <?php $content = $this->showDefaultInPrivacyDropdown; ?>
			    <input type="hidden" id="auth_view" name="auth_view" value="<?php echo $content ?>" />
			    <?php if ($this->showPrivacyDropdown): ?>  
			      <?php $availableLabels = $this->availableLabels; ?>
			      <?php if (empty($this->privacylists)): ?>
			        <?php
			        $showDefaultTip = $showDefault = $availableLabels[$content];
			        $showdefaulclass = "aaf_icon_feed_" . $content;
			      else:
			        $showDefault = $adSeprator = null;
			        foreach ($this->privacylists as $klist => $plist):
			          $showDefault.=$adSeprator . $plist;
			          if (empty($adSeprator)):
			            $adSeprator = ", ";
			          endif;
			        endforeach;
			        $showDefaultTip = $showDefault;
			        $showdefaulclass = "aaf_icon_feed_list";
			        if (count($this->privacylists) > 2):
			          $showDefault = "Custom";
			          $showdefaulclass = "aaf_icon_feed_custom";
			        endif;
			      endif;
			      ?>
			      <div class='advancedactivity_privacy_list' id='advancedactivity_friend_list'>            
			        <span class="aaf_privacy_pulldown" id="pulldown_privacy_list" onClick="togglePrivacyPulldown(event, this)">
			          <p class="adv_privacy_list_tip adv_composer_tip">
			            <span id="adv_custom_list_privacy_lable_tip"> <?php echo $this->string()->escapeJavascript($this->translate("Share with %s", $this->translate($showDefaultTip))) ?></span>
			            <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" alt="" />
			          </p>
			          <a href="javascript:void(0);" id="show_default" class="aaf_privacy_pulldown_button">
			            <i class="aaf_privacy_pulldown_icon <?php echo $this->translate($showdefaulclass) ?>"></i>
			            <span><?php echo $this->translate($showDefault);  ?></span>
			            <i class="aaf_privacy_pulldown_arrow"></i>
			          </a>
			          <div class="aaf_pulldown_contents_wrapper">
			            <div class="aaf_pulldown_contents">
			              <ul> 
											<?php // if($content !='friends' || $this->enableList):  ?> 
											<?php foreach ($availableLabels as $key => $value): ?>
												<li class="<?php echo ( $key == $content ? 'aaf_tab_active' : 'aaf_tab_unactive' ) ?> user_profile_friend_list_<?php echo $key ?> aaf_custom_list" id="privacy_list_<?php echo $key ?>" onclick="setAuthViewValue('<?php echo $key ?>',  '<?php echo $this->string()->escapeJavascript($this->translate($value)); ?>','aaf_icon_feed_<?php echo $key ?>')" title="<?php echo $this->translate("Share with %s", $this->translate($value)); ?>" >
													<i class="aaf_privacy_pulldown_icon aaf_icon_feed_<?php echo $key ?>"></i>             
													<div>
														<?php echo $this->translate($value); ?>
													</div>
												</li>
											<?php endforeach; ?>
											<?php // endif;?>
											<?php if ($this->enableList): ?> 
												<li class="sep"></li>
											<?php endif; ?>
											<?php if ($this->enableList): ?> 
												<?php
												$keyId = 0;
												foreach ($this->lists as $list):
													?>
													<?php
													if (empty($showDefault)):
														$showDefault = $list->title;
														$keyId = $list->list_id;
													endif;
													?>
													<li class="<?php echo ( (!empty($this->privacylists) && isset($this->privacylists[$list->list_id])) ? 'aaf_tab_active' : 'aaf_tab_unactive' ) ?> user_profile_friend_list_<?php echo $list->list_id ?> aaf_custom_list" id="privacy_list_<?php echo $list->list_id ?>" onclick="setAuthViewValue('<?php echo $list->list_id ?>','<?php echo $this->string()->escapeJavascript($this->translate($list->title)) ?>', 'aaf_icon_feed_list')" title="<?php echo $this->translate("Share with %s", $list->title); ?>">
														<i class="aaf_privacy_pulldown_icon aaf_icon_feed_list"></i>                         
														<div>
													<?php echo $this->translate($list->title) ?>
														</div>
													</li>                   
													<?php endforeach; ?>
													<?php if ($this->countList > 1): ?>
														<li class="sep"></li>
													<?php endif; ?>
													<?php if ($this->countList > 1): ?>
														<li onclick="addMoreList();" class="aaf_custom_list"
																id="user_profile_friend_list_custom" title="<?php echo $this->translate("Choose
					multiple Friend Lists to share with."); ?>"><i class="aaf_privacy_pulldown_icon aaf_icon_feed_custom"></i><div
																id="user_profile_friend_list_custom_div"><?php echo $this->translate("Custom"); ?></div></li>
															<?php else: ?>                    
														<li onclick="OpenPrivacySmoothBox(<?php echo $this->countList ?>);"
																class="aaf_custom_list" title="<?php echo $this->translate("Choose multiple Friend Lists to share with.");
																?>"><i class="aaf_privacy_pulldown_icon aaf_icon_feed_custom"></i><div>
														<?php echo
														$this->translate("Custom"); ?></div></li>
												<?php endif; ?>
											<?php endif; ?>
			              </ul>
			            </div>
			          </div>
			        </span>            
			      </div>        
			      <?php //endif; // END LIST CODE  ?>
			    <?php else: ?>
			      <input type="hidden" id="auth_view" name="auth_view" value="<?php echo $this->settingsApi->getSetting('activity.content', 'everyone'); ?>" />
			  <?php endif; ?>
			  </div>	  
			
			  <script type="text/javascript">
			    var OpenPrivacySmoothBox=function(count){
			      var msg="";
			      if(count==0){
			        msg="<div class='aaf_show_popup'><div class='tip'><span>"+
			          "<?php echo $this->string()->escapeJavascript($this->translate('You have currently not organized your friends into lists. To create new friend lists, go to the "Friends" section of ')); ?>"+
			          "<a href='<?php echo $this->viewer()->getHref() ?>' ><?php echo
			  $this->string()->escapeJavascript($this->translate("your profile")) ?></a><?php echo
			  $this->string()->escapeJavascript($this->translate(".")) ?>"+
			          "</span></div><div><button href=\"javascript:void(0);\" onclick=\"javascript:Smoothbox.close()\"><?php echo $this->translate("Close"); ?></button></div>"+
			          "</div></div>";
			      }else{
			        msg="<div class='aaf_show_popup'><div class='tip'><span>"+
			          "<?php echo $this->string()->escapeJavascript($this->translate('You have currently created only one list to organize your friends. Create more friend lists from the "Friends" section of ')); ?>"+
			          "<a target='_parent' href='<?php echo $this->viewer()->getHref() ?>' ><?php echo
			  $this->string()->escapeJavascript($this->translate("your profile")) ?></a><?php echo
			  $this->string()->escapeJavascript($this->translate(".")) ?>"+
			          "</span></div><div><button href=\"javascript:void(0);\" onclick=\"javascript:Smoothbox.close()\"><?php echo $this->translate("Close"); ?></button></div>"+
			          "</div></div>";
			      }
			      Smoothbox.open(msg);
			    } 
			  </script>
			<?php endif; ?>
			</div>
					
						<?php foreach ($this->composePartials as $partial): ?>
							<?php echo $this->partial($partial[0], $partial[1]) ?>
						<?php endforeach; ?>
	  			</td>
	  		</tr>
	  	</table>
	  </div>
		<div id="sitetagcheckin_submit_button" class="sitetag_checkin_post_buttons">
      <?php if($this->resource_type == 'user'):?>
        <button type="submit" id="submit" name="submit"><?php echo $this->translate("Add Location");?></button>
      <?php else:?>
			  <button type="submit" id="submit" name="submit"><?php echo $this->translate($this->checkin_verb);?></button>
      <?php endif;?>
			<?php echo $this->translate(" or "); ?>   
			<a onclick="javascript:parent.Smoothbox.close()" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate("cancel"); ?>
			</a>
		</div>
	</form>
</div>

<?php else:?>

	<?php
	$is_iphone = false;
	if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) :
		$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if (preg_match('/iphone/i', $useragent)) {
			$is_iphone = true;
		}
	endif;
	?>
	<?php if ($is_iphone): ?>
		<style type="text/css"> 
		#compose-photo-activator,
		#compose-sitepagephoto-activator,
		#compose-sitebusinessphoto-activator,
    #compose-sitegroupphoto-activator,#compose-sitestorephoto-activator{
			display: none !important;
		}
		</style>
	<?php else: ?>
		<style type="text/css"> 
		#compose-photo-activator,
		#compose-sitepagephoto-activator,
		#compose-sitebusinessphoto-activator,
    #compose-sitegroupphoto-activator,
    #compose-sitestorephoto-activator{
			display: inline-block !important;
		}
		</style>
	<?php endif; ?>
	<?php
		$this->headLink()
			->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Advancedactivity/externals/styles/mobile_statusbar.css');
	?>

<?php //if( $this->enableComposer ): ?>
  <div class="activity-post-container" id="activity-post-container">
  <?php $composerOptions= Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options',
			array("withtags", "emotions", "userprivacy")); ?>
 
	<form method="post" action="<?php echo $this->url(array('module' => 'sitetagcheckin', 'controller' => 'checkin', 'action' => 'check-in',
   'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, "checkin_use" => $this->checkin_use, 'checkin_verb' => $this->checkin_verb, 'checkedinto_verb' => $this->checkedinto_verb, 'tab' => $this->tab, 'checkin_your' => $this->checkin_your), 'default', true) ?>" class="activity" enctype="application/x-www-form-urlencoded" id="seaocheckinform">
      <textarea id="activity_body" cols="1" rows="1" name="body"><?php echo $this->translate(Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.default.textarea.text', 'I am here!'));?></textarea>
      
		 <div class="stcheckin_post_options" id="seocheckinphotoactivator">
			<?php if($this->checkin_use):?>
				<div id="sitetagcheckin_current_date" class="stcheckin_current_date" onclick="showDateFields();">
					<span><a><?php echo date("F j, Y", strtotime(date('Y-m-d')));?></a></span>
					<span onclick="showDateFields();" class="stcheckin_post_edit_date"></span>
				</div>
				<div class="stcheckin_post_date" style="display:none;" id="sitetag_checkin_post_date">
					<select id="year" name="year">
						<option label="Year" value="Year" disabled="disabled"><?php echo $this->translate('Year');?></option>
						<?php $curYear = date('Y'); ?>
						<?php for ($i = 0; $i <= 110; $i++) :?>
							<option label="<?php echo $curYear;?>" value="<?php echo $curYear;?>" <?php if($i==0):?> selected="selected" <?php endif;?>><?php echo $curYear;?></option>
						<?php $curYear--;?>
						<?php endfor;?>
					</select>

					<a  onclick="showMonth(0);" href="javascript:void(0);" id="addmonth" style="display:none;"><?php echo $this->translate('+ Add Month');?></a>
					<select id="month" name="month" onblur="showAddmonth(2)" onclick="showMonth(1)" onchange="showAddday(2)" style="display:block;">
						<option label="Month" value="0"><?php echo $this->translate('Month');?></option>
						<?php $curMonth = (int) date('m'); ?>
						<?php for ($k = 1; $k <= $curMonth; $k++):?>
							<?php $month = date('F', mktime(0, 0, 0, $k, 1));?>
							<option label="<?php $curMonth = (int) date('m'); ?><?php echo $month;?>" value="<?php echo $k;?>" <?php if($k==$curMonth):?> selected="selected" <?php endif;?>><?php echo $this->translate($month);?></option>
						<?php endfor;?>
					</select>
	
					<a style="display:none;" id="addday"  onclick="showDay(0);" href="javascript:void(0);"><?php echo $this->translate('+ Add Day');?></a>
					<select id="day" name="day" style="display:block;">
					</select>
				</div>
		  <?php endif;?>
      </div>
      
      <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
      <input type="hidden" name="activity_type" value="1" />
      <?php if( $this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())): ?>
        <input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
      <?php endif; ?>
      <?php if( $this->formToken ): ?>
        <input type="hidden" name="token" value="<?php echo $this->formToken ?>" />
      <?php endif ?>
      <?php if($advancedActivity):?>
				<div id="adv_post_container_icons"></div>
				<div class="compose-menu_before" >                    
          <?php 
					$SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);	        
					if (in_array("emotions",$composerOptions) && $SEA_EMOTIONS_TAG && isset ($SEA_EMOTIONS_TAG[0])): ?>
					<span id="emoticons-button"  class="adv_post_smile"  onclick="setEmoticonsBoard()">
						<span id="emoticons-board"  class="seaocore_embox seaocore_embox_closed" >
							<span class="seaocore_embox_arrow"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/tooltip_arrow_top.png" alt="" /></span>
							<span class="seaocore_embox_title">
								<span class="fleft" id="emotion_lable"></span>
								<span class="fright"id="emotion_symbol" ></span>
								</span>
							<?php foreach ($SEA_EMOTIONS_TAG[0] as $tag_key=>$tag):?>         
							<span class="seaocore_embox_icon" onmouseover='setEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/","$3", $tag)))?>","<?php echo $this->string()->escapeJavascript($tag_key)?>")' onclick='addEmotionIcon("<?php echo $this->string()->escapeJavascript($tag_key)?>")'  title="<?php echo $this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/","$3", $tag))."&nbsp;".$tag_key; ?>"><?php 
									echo preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img src=\"".$this->layout()->staticBaseUrl."application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" alt=\"$2\" />", $tag);              
								?></span>
							<?php endforeach;?>
						</span>					
					</span>
					<?php endif; ?>
				</div>
      <?php endif; ?>
      <div id="compose-menu" class="compose-menu" >
        <button id="compose-submit" type="submit"><?php echo $this->translate($this->checkin_verb);?></button>
        <?php if($advancedActivity):?>
        <div id="show_loading_main" class="show_loading" style="display:none;">140</div>
         	<?php if ($this->showPrivacyDropdown): ?> 
           <?php $content = isset ($this->availableLabels[$this->showDefaultInPrivacyDropdown]) ? $this->showDefaultInPrivacyDropdown : $this->settingsApi->getSetting('activity.content', 'everyone');?> 
              <?php $availableLabels = $this->availableLabels; ?>
              <?php
              if (!empty($this->privacylists)):
                foreach ($this->privacylists as $klist => $plist):
                  $showDefault = $plist;
                endforeach;
                if (count($this->privacylists) > 1):
                  $content = "friends";
                endif;
              endif;
              ?>
            <select name="auth_view" value="<?php echo $content; ?>">
              <?php foreach ($availableLabels as $key => $value): ?>
              <option value="<?php echo $key ?>" <?php if($content==$key): ?>selected="selected"<?php endif;?> > <?php echo $this->translate($value); ?></option>
              <?php endforeach; ?>
               <?php if( $this->enableList):?>                  
              <?php foreach( $this->lists as $list ): ?>
              <option value="<?php echo $list->list_id; ?>" <?php if($content==$list->list_id): ?>selected="selected"<?php endif;?>> <?php echo $list->title; ?></option>
              <?php endforeach; ?>
               <?php endif; ?>
            </select>
          <?php else: ?>
            <?php $content = $this->settingsApi->getSetting('activity.content', 'everyone'); ?>       
            <input type="hidden" id="auth_view" name="auth_view" value="<?php echo $content; ?>" />
          <?php endif; ?>                  
        <?php endif;?>
        <div class="aaf_cm_sep"></div>
      </div>
    </form>

    <?php
      $this->headScript()
	      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/scripts/composer-core.js');
    ?>
    
    <script type="text/javascript">
      var Share_Translate="<?php echo $this->string()->escapeJavascript($this->translate("ADVADV_SHARE")); ?>";
      var Who_Are_You_Text="<?php echo $this->string()->escapeJavascript($this->translate("Who are you with?")); ?>";
//       var composeInstanceCheckin;
      en4.core.runonce.add(function() {
       en4.core.language.addData({
      "with":"<?php echo $this->string()->escapeJavascript($this->translate("with"));?>",
      "and":"<?php echo $this->string()->escapeJavascript($this->translate("and"));?>",
      "others":"<?php echo $this->string()->escapeJavascript($this->translate("others"));?>"
      });
        // @todo integrate this into the composer
       // if( !DetectMobileQuick() && !DetectIpad() ) {
          composeInstanceCheckin = new ComposerCheckin('activity_body', {
            menuElement : 'compose-menu',
            baseHref : '<?php echo $this->baseUrl() ?>',
            lang : {
              'Post Something...' : '<?php echo $this->string()->escapeJavascript($this->translate('Post Something...')) ?>'
            },
            overText : false,   
            hideSubmitOnBlur : false,   
            useContentEditable : false
          });
          
//        composeInstance.getForm().addEvent('submit', function(e) {
//        composeInstance.fireEvent('editorSubmit');
//      }.bind(this));
      //  }
      });
</script>

<?php if($advancedActivity):?>
	<script type="text/javascript">
  <?php  if (in_array("emotions",$composerOptions) ) : ?>
  var hideEmotionIconClickEnable=false;
   function setEmoticonsBoard(){
   if(composeInstanceCheckin)
    composeInstanceCheckin.focus();
   $('emotion_lable').innerHTML="";
   $('emotion_symbol').innerHTML="";
      hideEmotionIconClickEnable=true;    
      var  a=$('emoticons-button');
        a.toggleClass('emoticons_active');
        a.toggleClass('');
      var  el=$('emoticons-board');
        el.toggleClass('seaocore_embox_open');
        el.toggleClass('seaocore_embox_closed'); 
    }

   function addEmotionIcon(iconCode){ 
     var content =composeInstanceCheckin.getContent();
        content=content.replace(/(<br>)$/g, "");
        content =  content +' '+ iconCode; 
       composeInstanceCheckin.setContent(content);
            
    }
     //hide on body click
    // $(document.body).addEvent('click',hideEmotionIconClickEvent.bind());
   function hideEmotionIconClickEvent(){
     if(!hideEmotionIconClickEnable){       
        $('emoticons-board').removeClass('seaocore_embox_open').addClass('seaocore_embox_closed');      
     }
     hideEmotionIconClickEnable=false;
   }  
   function setEmotionLabelPlate(lable,symbol){
    $('emotion_lable').innerHTML=lable;
    $('emotion_symbol').innerHTML=symbol;
   }
   <?php endif; ?>
    </script>

    <?php //if(in_array("withtags",$composerOptions)): echo $this->partial('_composeAddpeopletagmobile.tpl', 'advancedactivity', array("isAAFWIDGETMobile"=>1));    endif; ?>

<?php
$this->headTranslate(array('Add People', 'with :'));

    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/scripts/feed-tags-mobile.js');
 
?>
<script type="text/javascript">
var composeInstance;
  en4.core.runonce.add(function() {
   //new Asset.javascript('');
    composeInstanceCheckin.addPlugin(new Composer.Plugin.AddFriendTag({
      title : '<?php echo $this->string()->escapeJavascript($this->translate('Add People')) ?>',
      enabled: true,    
      lang : {
        'Add People' : '<?php echo $this->string()->escapeJavascript($this->translate('Add People')) ?>',
        'with :' : '<?php echo $this->string()->escapeJavascript($this->translate('with :')) ?>',
        'Enter the user name': '<?php echo $this->string()->escapeJavascript($this->translate('Enter the user name')) ?>'
          
      }
     
    }));
  });
  function initCheckinSitetag() {}
</script>
		<?php endif; ?>
    <?php foreach( $this->composePartials as $partial ): ?>
      <?php echo $this->partial($partial[0], $partial[1], array("isAAFWIDGETMobile"=>1)) ?>
    <?php endforeach; ?>

  </div>
  <div class="clr" style="height:250px;"></div>
<?php //endif; ?>

<?php endif;?>
<script type="text/javascript">

  var addDay=0;
  var addMonth=0;

  function showMonth(month) {
    addMonth=month;
    document.getElementById('addmonth').style.display = 'none';
    document.getElementById('month').style.display = 'block';
    var sel = document.getElementById("month");
    var year = document.getElementById("year");
    var selectedTextYear = year.options[year.selectedIndex].text;
    var selectedValueYear = year.options[year.selectedIndex].value;
    var currentYear = '<?php echo (int) date("Y"); ?>'
    //get the selected option
    var selectedTextMonth = sel.options[sel.selectedIndex].text;
    var selectedValueMonth = sel.options[sel.selectedIndex].value;

    var selday = document.getElementById("day");
    //get the selected option
    selday.options[sel.selectedIndex].text = 0;
    selday.options[sel.selectedIndex].value = 0;   

    if(selectedTextMonth != 'Month') {
      if(parseInt(selectedValueMonth) > '<?php echo (int) date("m"); ?>' && (currentYear == parseInt(selectedTextYear))) {
        sel.selectedIndex="Month";
        document.getElementById('addday').style.display = 'none';
        document.getElementById('day').style.display = 'none';
document.getElementById('day').value= 0;
      } 
      else {
        document.getElementById('addday').style.display = 'block';
        document.getElementById('day').style.display = 'none';
document.getElementById('day').value= 0;
      }
    } else {
      document.getElementById('addday').style.display = 'none';
      document.getElementById('day').style.display = 'none';
    }
  }

  function setTimeMonth() {
    setTimeout("showAddmonth(1)", 100);
  }

  function showAddmonth(month) {
    if(addMonth == 0 || month == 2) { 
      addMonth = 0;
      var sel = document.getElementById("month");
      //get the selected option
      var selectedText = sel.options[sel.selectedIndex].text;
      if(selectedText == 'Month') {
        document.getElementById('addmonth').style.display = 'block';
        document.getElementById('month').style.display = 'none';
        document.getElementById('addday').style.display = 'none';
        document.getElementById('day').style.display = 'none';
document.getElementById('day').value= 0;
      }
    }
  }

  function showDay(day) {
    addDay=day;
    clear('day');
    document.getElementById('addday').style.display = 'none';
    document.getElementById('day').style.display = 'block';
    addOption($('day'), '<?php echo $this->translate("Day"); ?>', 0);
		var month_day = document.getElementById('month').value;
		var year_day = document.getElementById('year').value;
    var num = new Date(year_day, month_day, 0).getDate();
    <?php $curMonth = (int) date('m'); ?>
    var currentDate = '<?php echo (int) date('d'); ?>';
    if(month_day == '<?php echo (int) date("m"); ?>') {
			for(i=1; i<= currentDate; i++) {
				addOption($('day'), i, i);
			}
    } else {
			for(i=1; i<= num; i++) {
				addOption($('day'), i, i);
			}
    }
  }

	if($('day')) {

		$('day').removeEvents().addEvent('blur', function(event){
			showAddday(2)
		});

		$('day').removeEvents().addEvent('click', function(event){
			showDay(1);
		});

		$('day').removeEvents().addEvent('change', function(event){
			showAddday(2)
		});
	}

	function addOption(selectbox,text,value )
	{  
		var optn = document.createElement("OPTION");
		optn.text = text;
		optn.value = value;
		selectbox.options.add(optn);
	}

	function clear(ddName)
	{
		for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
		{ 
			document.getElementById(ddName).options[ i ]=null; 
		} 
	}	

  function setTime() {
    setTimeout("showAddday(1)", 500);
  }

  function showAddday(day) {
    if(addDay == 0 || day == 2) { 
      addDay = 0;
      var sel = document.getElementById("day");
      //get the selected option
      var selectedText = sel.options[sel.selectedIndex].text;
      var selectedValue = sel.options[sel.selectedIndex].value;
      var selYear = document.getElementById("year");
      var currentYear = '<?php echo (int) date("Y"); ?>'
      var selectedTextYear = selYear.options[selYear.selectedIndex].text;
      var selectedYearValue = selYear.options[selYear.selectedIndex].value; 
      var selMonth = document.getElementById("month");
      var currentMonth = selMonth.options[selMonth.selectedIndex].text;
      var selectedMonthValue = selMonth.options[selMonth.selectedIndex].value; 
      if(selectedText == 'Day') {
        document.getElementById('addday').style.display = 'block';
        document.getElementById('day').style.display = 'none';
      } 
      else {
        if(parseInt(selectedValue) > '<?php echo (int) date("d"); ?>' && (currentYear == parseInt(selectedTextYear)) && parseInt(selectedMonthValue) == '<?php echo (int) date("m"); ?>') {
          sel.selectedIndex="Day";
sel.value= 0;
        } 
        else {
          document.getElementById('addday').style.display = 'none';
          document.getElementById('day').style.display = 'block';
        }
      }
    }
  }

  function showDateFields() {
     $('sitetag_checkin_post_date').style.display ="block";
     $('sitetagcheckin_current_date').style.display ="none";
  }
  
  <?php if($this->checkin_use) :?>
		window.addEvent('domready', function() {
			showDay(0);
			var sel = document.getElementById("day");
			var currentDate = '<?php echo (int) date("d");?>';
			sel.value= currentDate;
		});
  <?php endif;?>
</script>

<?php endif;?>


<?php if($this->resource_type == 'user'):?>
	<script type="text/javascript">
		document.getElementById('seaocheckinform').addEvent('submit', function(event){
			if($('location_sitetagcheckin_autosuggest_location') && $('location_sitetagcheckin_autosuggest_location').value == '') {
				event.stop();
				en4.core.showError("<div class='sitetagcheckin_show_popup'><p>" + en4.core.language.translate("Please enter the location.") + '</p><button onclick="Smoothbox.close()">Close</button></div>');
				return false;
			} else if($('location_sitetagcheckin_autosuggest_location') && $('location_sitetagcheckin_autosuggest_location').value != '' && $('checkinstr_status').value == '') {
        event.stop();
				en4.core.showError("<div class='sitetagcheckin_show_popup'><p>" + en4.core.language.translate("Please select the location.") + '</p><button onclick="Smoothbox.close()">Close</button></div>');
				return false;
      }
		});
	</script>
<?php endif;?>
