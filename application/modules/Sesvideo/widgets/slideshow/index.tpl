<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>

<script type="application/javascript">
window.addEvent('domready', function() {
	var htmlElement = document.getElementsByTagName("body")[0];
  htmlElement.addClass('sesvideo_slideshow');
});
var logoSesvideo = sesJqueryObject('.layout_core_menu_logo').html();
<?php if($this->main_navigation): ?>
sesJqueryObject('#global_header').remove();
<?php endif; ?>
<?php if($this->main_navigation): ?>
sesJqueryObject('#global_wrapper').css('padding-top','0px');
<?php endif; ?>
<?php if($this->full_width): ?>
window.addEvent('domready', function() {
	var htmlElement = document.getElementsByTagName("body")[0];
  htmlElement.addClass('sesvideo_slideshow_full_width');
});
<?php endif; ?>
</script>
<?php
  $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/slideshowstyle.css');
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/scripts/slideshowmodernizr.js');
?>
<div class="sesvideo_slideshow_wrapper sesbasic_clearfix" style="height:<?php echo $this->height.'px'; ?>;">
	<div class="sesvideo_slideshow_container">
      <header class="cd-header sesbasic_bxs">
      <div id="cd-logo"></div>
    	<?php if($this->main_navigation && count($this->navigation)): ?>
        <nav class="cd-primary-nav">
        	<a href="javascript:void(0);" class="cd-primary-nav-browse-btn">
          	<span><?php echo $this->translate("Browse"); ?></span>
          	<i class="fa fa-angle-down"></i>
          </a>
          <a href="javascript:void(0);" class="cd-primary-nav-mobile-browse-btn">
          	<i class="fa fa-bars"></i>
          </a>
        	<ul class="cd-primary-nav-dropdown">
        		<?php foreach( $this->navigation as $navigationMenu ): ?>
        			<li>
        				<?php if ($navigationMenu->action):  ?>
									
										<a class= "<?php echo $navigationMenu->class ?>" href='<?php echo empty($navigationMenu->uri) ? $this->url(array('action' => $navigationMenu->action), $navigationMenu->route, true) : $navigationMenu->uri ?>'><?php echo $this->translate($navigationMenu->label); ?></a>
									
                <?php else : ?>
                <?php $classArray = explode(' ', $navigationMenu->class); 
									if($classArray[1] == 'core_main_home' && $this->viewer->getIdentity() != 0):  ?>
										<a class= "<?php echo $navigationMenu->class ?>" href='<?php echo $this->url(array('action' => 'home'), $navigationMenu->route, true) ?>'><?php echo $this->translate($navigationMenu->label); ?></a>
									<?php elseif($classArray[1] == 'core_main_invite' && $this->viewer->getIdentity() != 0): ?>
				            <a class= "<?php echo $navigationMenu->class ?>" href='<?php echo $this->url(array('module' => 'invite'), $navigationMenu->route, true) ?>'><?php echo $this->translate($navigationMenu->label); ?></a>
									<?php else: ?>
                  <a class= "<?php echo $navigationMenu->class ?>" href='<?php echo empty($navigationMenu->uri) ? $this->url(array(), $navigationMenu->route, true) : $navigationMenu->uri ?>'><?php echo $this->translate($navigationMenu->label); ?></a>
                  <?php endif; ?>
                <?php endif; ?>
        			</li>
        		<?php endforeach; ?>
        	</ul>
        </nav>
      <?php endif; ?>
      <?php if($this->mini_navigation && count($this->menumininavigation)): ?>
      	<div id='core_menu_mini_menu' class="cd-mini-menu">
          <?php
            $count = count($this->menumininavigation);
            foreach( $this->menumininavigation->getPages() as $item ) $item->setOrder(--$count);
          ?>
          <ul>
            <?php if( $this->viewer->getIdentity()) :?>
            <li id='core_menu_mini_menu_update'>
              <span onclick="toggleUpdatesPulldown(event, this, '4');" style="display: inline-block;" class="updates_pulldown">
                <div class="pulldown_contents_wrapper">
                  <div class="pulldown_contents">
                    <ul class="notifications_menu" id="notifications_menu">
                      <div class="notifications_loading" id="notifications_loading">
                        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
                        <?php echo $this->translate("Loading ...") ?>
                      </div>
                    </ul>
                  </div>
                  <div class="pulldown_options">
                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
                       $this->translate('View All Updates'),
                       array('id' => 'notifications_viewall_link')) ?>
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
                      'id' => 'notifications_markread_link',
                    )) ?>
                  </div>
                </div>
                <a href="javascript:void(0);" id="updates_toggle" <?php if( $this->notificationCount ):?> class="new_updates"<?php endif;?>><?php echo $this->translate(array('%s Update', '%s Updates', $this->notificationCount), $this->locale()->toNumber($this->notificationCount)) ?></a>
              </span>
            </li>
            <?php endif; ?>
            <?php foreach( $this->menumininavigation as $item ): ?>
              <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
                'class' => ( !empty($item->class) ? $item->class : null ),
                'alt' => ( !empty($item->alt) ? $item->alt : null ),
                'target' => ( !empty($item->target) ? $item->target : null ),
              ))) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
			<?php endif; ?>
    </header> 
    <section class="cd-hero sesbasic_bxs">
      <ul class="cd-hero-slider <?php echo $this->autoplay ? 'autoplay' : '' ; ?>" style="height:<?php echo $this->height.'px'; ?>;">
    <?php 
      $counter = 0;
      foreach($this->paginator as $itemdata){ ?>	
      <?php 
        $item = Engine_Api::_()->getItem('sesvideo_slide',$itemdata->slide_id); 
        ?>
    <?php if($item->file_type == 'mp4'){ ?>
        
        <li class="cd-bg-video <?php echo $counter == 0 ? 'selected' : '' ; ?> form-in-<?php echo $item->position_register_form == 'right' ? 'right' : 'left'; ?>">
          <?php if($item->show_register_form && Engine_Api::_()->user()->getViewer()->getIdentity() == 0){ ?>
            <div class="cd-half-width cd-signupform-container">
              <div class="cd-signupform">
                <?php echo $this->action("index", "signup", "sesvideo", array()); ?>  		
              </div>
            </div>
          <?php } ?>
          <?php 
          			$fullWidth = true;
            		if($item->show_register_form == 0){
                    	$fullWidth = true;
          			 }else{
                 		if(Engine_Api::_()->user()->getViewer()->getIdentity() == 0)
                    	$fullWidth = false;
                     else
                     	 $fullWidth = true;
           				} 
          ?>
          <div class="<?php echo $fullWidth ? 'cd-full-width' : 'cd-half-width' ; ?>">
            <?php if($item->title != '' || $item->description  != ''){ ?>	
              <?php if($item->title != ''){ ?>
                <h2 style="color:#<?php echo $item->title_button_color; ?>"><?php echo $this->translate($item->title); ?></h2>
              <?php } ?>
            <?php } ?>
            <?php if($item->description  != ''){ ?>
              <p style="color:#<?php echo $item->description_button_color; ?>"><?php echo $this->translate(nl2br($item->description)) ; ?></p>
            <?php } ?>
            <?php if($item->login_button && Engine_Api::_()->user()->getViewer()->getIdentity() == 0){ ?>
              <a href="<?php echo $this->layout()->staticBaseUrl; ?>login" class="cd-btn"  onMouseOver="this.style.backgroundColor='#<?php echo $item->login_button_mouseover_color; ?>'"   onMouseOut="this.style.backgroundColor='#<?php echo $item->login_button_color; ?>'" style="color:#<?php echo $item->login_button_text_color; ?>; background-color:#<?php echo $item->login_button_color; ?>"><?php echo $this->translate($item->login_button_text); ?></a>
            <?php } ?>
            <?php if($item->signup_button && Engine_Api::_()->user()->getViewer()->getIdentity() == 0){ ?>
              <a href="<?php echo $this->layout()->staticBaseUrl; ?>signup" class="cd-btn secondary"  onMouseOver="this.style.backgroundColor='#<?php echo $item->signup_button_mouseover_color; ?>'"   onMouseOut="this.style.backgroundColor='#<?php echo $item->signup_button_color; ?>'" style="color:#<?php echo $item->signup_button_text_color; ?>;background-color:#<?php echo $item->signup_button_color; ?>"><?php echo $this->translate($item->signup_button_text); ?></a>
            <?php } ?>
            <?php if($item->extra_button){ ?>
              <a href="<?php echo $item->extra_button_link ? $item->extra_button_link : 'javascript:void(0)'; ?>" class="cd-btn secondary"  onMouseOver="this.style.backgroundColor='#<?php echo $item->extra_button_mouseover_color; ?>'"   onMouseOut="this.style.backgroundColor='#<?php echo $item->extra_button_color; ?>'" style="color:#<?php echo $item->extra_button_text_color; ?>;background-color:#<?php echo $item->extra_button_color; ?>"><?php echo $this->translate($item->extra_button_text); ?></a>
            <?php } ?> 
          </div>
          <div class="cd-bg-video-wrapper" data-image="<?php echo $item->getFilePath('thumb_icon'); ?>" data-video="<?php echo $item->getFilePath('file_id'); ?>">
          </div> <!-- .cd-bg-video-wrapper -->
        </li>
    <?php }else{ ?>
       <li class="<?php echo $counter == 0 ? 'selected' : '' ; ?> form-in-<?php echo $item->position_register_form == 'right' ? 'right' : 'left'; ?>" style="background-image:url(<?php echo $item->getFilePath('file_id') ?>);">
          <?php if($item->show_register_form && Engine_Api::_()->user()->getViewer()->getIdentity() == 0){ ?>
            <div class="cd-half-width cd-signupform-container">
              <div class="cd-signupform">
                <?php echo $this->action("index", "signup", "sesvideo", array()); ?>
              </div>
            </div>
          <?php } ?>
          <?php 
          			$fullWidth = true;
            		if($item->show_register_form == 0){
                    	$fullWidth = true;
          			 }else{
                 		if(Engine_Api::_()->user()->getViewer()->getIdentity() == 0)
                    	$fullWidth = false;
                     else
                     	 $fullWidth = true;
           				} 
          ?>
          <div class="<?php echo $fullWidth ? 'cd-full-width' : 'cd-half-width' ?>">
            <?php if($item->title != '' || $item->description != ''){ ?>	
              <?php if($item->title != ''){ ?>
                  <h2 style="color:#<?php echo $item->title_button_color; ?>"><?php echo $this->translate($item->title); ?></h2>
              <?php } ?>
            <?php } ?>
            <?php if($item->description != ''){ ?>
                <p style="color:#<?php echo $item->description_button_color; ?>"><?php echo $this->translate(nl2br($item->description)); ?></p>
            <?php } ?>
            <?php if($item->login_button && Engine_Api::_()->user()->getViewer()->getIdentity() == 0){ ?>
              <a href="<?php echo $this->layout()->staticBaseUrl; ?>login" class="cd-btn"  onMouseOver="this.style.backgroundColor='#<?php echo $item->login_button_mouseover_color; ?>'"   onMouseOut="this.style.backgroundColor='#<?php echo $item->login_button_color; ?>'" style="color:#<?php echo $item->login_button_text_color; ?>; background-color:#<?php echo $item->login_button_color; ?>"><?php echo $this->translate($item->login_button_text); ?></a>
            <?php } ?>
            <?php if($item->signup_button && Engine_Api::_()->user()->getViewer()->getIdentity() == 0){ ?>
              <a href="<?php echo $this->layout()->staticBaseUrl; ?>signup" class="cd-btn secondary"  onMouseOver="this.style.backgroundColor='#<?php echo $item->signup_button_mouseover_color; ?>'"   onMouseOut="this.style.backgroundColor='#<?php echo $item->signup_button_color; ?>'" style="color:#<?php echo $item->signup_button_text_color; ?>;background-color:#<?php echo $item->signup_button_color; ?>"><?php echo $this->translate($item->signup_button_text); ?></a>
            <?php } ?>
            <?php if($item->extra_button){ ?>
              <a href="<?php echo $item->extra_button_link != '' ? $item->extra_button_link : 'javascript:void(0)'; ?>" class="cd-btn secondary"  onMouseOver="this.style.backgroundColor='#<?php echo $item->extra_button_mouseover_color; ?>'"   onMouseOut="this.style.backgroundColor='#<?php echo $item->extra_button_color; ?>'" style="color:#<?php echo $item->extra_button_text_color; ?>;background-color:#<?php echo $item->extra_button_color; ?>"><?php echo $this->translate($item->extra_button_text); ?></a>
            <?php } ?> 
          </div>
        </li>
   <?php } ?>
    <?php 
      $counter++;
        } ?>
      </ul> <!-- .cd-hero-slider -->
     <?php if($this->searchEnable){ ?>
        <div class="cd-slider-searchbox <?php echo ($this->paginator->getTotalItemCount() < 2 || !$this->thumbnail) ? '' : 'isnav' ?>" >
          <?php echo $this->content()->renderWidget('sesvideo.search'); ?>
        </div>
     <?php } ?>
     <?php if($this->paginator->getTotalItemCount()>1 && $this->thumbnail){ ?>
      <div class="cd-slider-nav">
        <nav>
          <!--<span class="cd-marker item-1"></span>-->
          <ul>
          <?php $counter = 0; ?>
          <?php foreach($this->paginator as $item){ ?>
            <li class="<?php echo $counter == 0 ? 'selected' : ''; ?> ">
              <a href="javascript:;" style="background-image:url(<?php echo $item->getFilePath('thumb_icon'); ?>)"></a>
            </li>
          <?php $counter++;
              } ?>
          </ul>
        </nav> 
      </div> <!-- .cd-slider-nav -->
     <?php } ?>
    </section> 
	 </div>
</div>  
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/scripts/slideshowmain.js'); ?>
<script type="application/javascript">
sesJqueryObject(document).ready(function(e){
	sesJqueryObject('#signup_account_form input,#signup_account_form input[type=email]').each(
				function(index){
						var input = sesJqueryObject(this);
						if(sesJqueryObject(this).closest('div').parent().css('display') != 'none' && sesJqueryObject(this).closest('div').parent().find('.form-label').find('label').first().length && sesJqueryObject(this).prop('type') != 'hidden' && sesJqueryObject(this).closest('div').parent().attr('class') != 'form-elements'){	
						  if(sesJqueryObject(this).prop('type') == 'email' || sesJqueryObject(this).prop('type') == 'text' || sesJqueryObject(this).prop('type') == 'password'){
								sesJqueryObject(this).attr('placeholder',sesJqueryObject(this).closest('div').parent().find('.form-label').find('label').html());
							}
						}
				}
			)	
});
<?php if($this->autoplay){ ?>
sesJqueryObject("#signup_account_form").mouseenter(function(e) {
	 clearInterval(autoPlayId);
	 clearInterval(clearIntvalSesVideoSlideshow1);
	 clearInterval(clearIntvalSesVideoSlideshow1);
	 isOnRegister = true;
}).mouseleave(function(e) {
   isOnRegister = false; 
	 if(IsfinishVideoNext){
	 	clearIntvalSesVideoSlideshow1=window.setTimeout(function(){finishVideoNext()}, 3000);
	 }else if(!sesJqueryObject('.cd-hero-slider').find('li.selected').hasClass('cd-bg-video')){
			clearIntvalSesVideoSlideshow1=window.setTimeout(function(){finishVideoNext()}, 3000);
		}
});
<?php } ?>
	<?php if($this->logo): ?>
		sesJqueryObject('#cd-logo').html(logoSesvideo);
	<?php endif; ?>
<?php if($this->autoplay){ ?>
	function finishVideoNext(){
		if(isOnRegister){
				IsfinishVideoNext = true;
				clearIntvalSesVideoSlideshow2 = window.setTimeout(function(){finishVideoNext()}, 5000);
				return;
		}
		IsfinishVideoNext = false;
		var indexSelectedVal = sesJqueryObject('.cd-slider-nav').find('li.selected').index();
		var totalLi = sesJqueryObject('.cd-slider-nav').find('nav').find('ul').children('li');
		if(indexSelectedVal >= totalLi.length - 1 ){
			indexSelectedVal = 0;
		}else
			indexSelectedVal++;
		sesJqueryObject(totalLi).eq(indexSelectedVal).trigger('click');
	}
<?php }else{ ?>
function finishVideoNext(){
	return true;	
}
<?php } ?>
</script>