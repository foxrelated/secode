<nav class="navbar navbar-default<?php echo $this->viewer()->getIdentity()?'':' not-login'?>" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <div class="ybo_logo animated bounce">
		<?php
		$site_name = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
		if($this -> site_name)
		{
			$site_name = $this -> site_name;
		}
		$logo  = $this->logo ? $this->logo: false;
		$route = $this->viewer()->getIdentity()
					 ? array('route'=>'user_general', 'action'=>'home')
					 : array('route'=>'default');
		echo ($logo)
			 ? $this->htmlLink(($this -> logo_link)?$this -> logo_link:$route, $this->htmlImage($logo, array('class' => 'navbar-brand')))
			 : $this->htmlLink(($this -> site_link)?$this -> site_link:$route, $site_name, array('class' => 'navbar-brand'));
		?>
	</div>
    <button class="main-button-search navbar-toggle" data-toggle="collapse" data-target="#ynevent_form_browse_filter">
        <span class="glyphicon glyphicon-search"></span>
    </button>
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex8-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>	
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse navbar-ex8-collapse<?php echo $this->menu_type == 1?' small_menu':' large_menu'; echo $this->fix_menu_position == 1?' menu_fixed':' menu_absolute'?>">
    <ul class="nav navbar-nav navbar-right">
		<button class="showall">ShowAll</button>
		<?php $count = 0;
		$limit = $this -> number_items;
		$bg_random = array('1dacd6', 'f54545', 'ffb600', '6ecd05', '9a4aa1', '747a82', 'eb6f63', 'a3815b', '84cfda', 'f75aa8', 'f1d729', '05cd9c', '466ee0', 'aeaeae', '19afba', '407197');
		$index = 0;
		foreach( $this->navigationMain as $item ): $index++;?>
			<?php
			$check_active = $item->active;
			$request = Zend_Controller_Front::getInstance()->getRequest();
			$module = $request->getModuleName();
			$module_class = explode("_", $item->class);
			$icon = 'application/themes/ynresponsive-metro/images/module/default.png';
			if(in_array(end($module_class), array('event','ynevent', 'blog', 'ynblog','album','advalbum','video','ynvideo','group', 'advgroup','forum','ynforum','ynauction','classified','yncontest','dashboard','ynfundraising','groupbuy','home','invite','mp3music','music','poll','socialstore','ultimatenews','user','ynidea','ynfilesharing','ynwiki')))
			{
				$icon = 'application/themes/ynresponsive-metro/images/module/'.end($module_class).'.png';
			}
			$icon = (isset($item -> advparams['icon']) && $item -> advparams['icon'] != ''? $item -> advparams['icon'] : $icon);
			$hover_active_icon = (isset($item -> advparams['hover_active_icon'])? $item -> advparams['hover_active_icon'] : "");
			$background_color = (isset($item -> advparams['background_color']) && $item -> advparams['background_color'] != 'transparent')? $item -> advparams['background_color']: $bg_random[array_rand($bg_random)];
			$text_color = (isset($item -> advparams['text_color']) && $item -> advparams['text_color'] != 'transparent')? $item -> advparams['text_color'] : "";
			$hover_color = (isset($item -> advparams['hover_color']) && $item -> advparams['hover_color'] != 'transparent')? $item -> advparams['hover_color'] : "";
            if(end($module_class) == $module && $module != 'user' && $module != 'core')
			{
				$check_active = true;
			}
			if($count < $limit):
				 $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
			        'reset_params', 'route', 'module', 'controller', 'action', 'type',
			        'visible', 'label', 'href'
			        )));
				?>
		     <li<?php echo($check_active?' class="active '.$item->class.'"':' class="'.$item->class.'"')?>>
          		<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
				<div class="menu_icon"><?php echo $this->htmlLink($item->getHref()) ?></div>
        	</li>
			<style rel="stylesheet" type="text/css">
				<?php 
					$li_class = implode('.',explode(' ', $item->class));
				?>
				#global_header .layout_page_header .navbar-default .navbar-collapse .navbar-nav > li.<?php echo $li_class?> > .menu_icon{
					background: #<?php echo $background_color;?> url('<?php echo $icon;?>') center no-repeat;
				}
				<?php if($text_color):?>
				#global_header .layout_page_header .navbar-default .navbar-collapse .navbar-nav > li.<?php echo $li_class?> > a{
					color:#<?php echo $text_color;?>;
				}
				<?php endif;?>
				<?php if($hover_color):?>
				#global_header .layout_page_header .navbar-default .navbar-collapse .navbar-nav > li.<?php echo $li_class?> > a:hover{
					color:#<?php echo $hover_color;?>;
				}
				<?php endif;?>
				<?php if($hover_active_icon != ''):?>
					#global_header .layout_page_header .navbar-default .navbar-collapse .navbar-nav > li.<?php echo $li_class?>:hover .menu_icon, #global_header .layout_page_header .navbar-default .navbar-collapse .navbar-nav > li.active.<?php echo $li_class?> .menu_icon{
						background-image:url(<?php echo $hover_active_icon?>) ;
					}
				<?php endif?>
			</style>			
    <?php else:?>
    	 <?php if($count == $limit):?>
    	 		<li class="dropdown <?php echo $item->class;?>">
	        		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this -> translate("More");?> 
	        			<span class="glyphicon glyphicon-chevron-down btn-xs"></span>
	        		</a>
					<div class="menu_icon"></div>
        			<ul class="dropdown-menu">
    	 	<?php endif;?>
    	 	<?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
				        'reset_params', 'route', 'module', 'controller', 'action', 'type',
				        'visible', 'label', 'href'
				        )));
					?>
			     <li<?php echo($check_active?' class="active '.$item->class.'"':' class="'.$item->class.'"')?>>
	          		<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
	        	</li>	
				<style rel="stylesheet" type="text/css">
					<?php 
						$li_class = implode('.',explode(' ', $item->class));
					?>
					#global_header .layout_page_header .navbar-default .navbar-collapse .navbar-nav .dropdown .dropdown-menu > li.<?php echo $li_class;?> > a{
						color:#<?php echo $text_color;?>;
					}
					#global_header .layout_page_header .navbar-default .navbar-collapse .navbar-nav .dropdown .dropdown-menu > li.<?php echo $li_class;?> > a:hover{
						color:#<?php echo $hover_color;?>;
					}
				</style>	
    	 	<?php if($count > $limit && $count == count($this->navigationMain)):?>
    	 	</ul>
    	 </li>
    	 	<?php endif;?>
		<?php endif;
		$count ++;
		endforeach;?>
    </ul>
  </div><!-- /.navbar-collapse -->
</nav>
<?php if(!$this->viewer()->getIdentity()):?>
<script>
$$('html').set('id', 'not-logged-in');
</script>
<?php endif?>
<script>
$$('.layout_ynresponsivemetro_metro_main_menu .navbar-nav .showall').addEvent('click', function(event){
    this.getParent('.navbar-collapse').toggleClass('active');
});
$$('#global_header .navbar-default .navbar-collapse .navbar-nav > li.dropdown').addEvent('click', function(event){
   this.toggleClass('active');
});
</script>
<?php $min_height = 57 * ($this -> number_items + 1)?>
<style type="text/css">
	#global_wrapper
	{
		min-height: <?php echo $min_height?>px;
	}
</style>