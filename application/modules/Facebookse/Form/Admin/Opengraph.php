<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Opengraph.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Facebookse_Form_Admin_Opengraph extends Engine_Form
{
  public function init($pagelevel_id = 0)
  {
    $this
      ->setTitle('Open Graph Implementation Settings')
      ->setDescription("The Open Graph protocol enables you to integrate your Web pages into the social graph. It is currently designed for Web pages representing profiles of real-world things.<br />Below, you can configure the Open Graph protocol implementation settings for the various content types on your site.<br /> <br /><div class='tip'><span>Note : Facebook maintains a cache of your data and clears it in every 24 hours, so the changes made by you here will be reflected at Facebook side in 24 hours. If any Like is made within this time period, then the feed generated at Facebook side will be with the old settings only.</span></div>");

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND', 'escape' => false));
  }
  
  //SHOWING THE FORM.
  public function showform ($pagelevel_id = 0, $fbmeta_imageUrl = null) { 
  	$this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    $this->addDecorator('HtmlTag', array('tag' => 'br', 'class' => 'zend_form'));
    $fbLikeButton = Engine_Api::_()->getApi("settings", "core")->getSetting('fblike.type', 'default');
   

    // Modules which are taking by us.
		$plugins_array = Engine_Api::_()->getDbtable( 'mixsettings' , 'facebookse' )->getMixLikeItems();
    $plugins_array['user']= 'Member Profile';
		$plugins_array['home']= 'Site Homepage';
		if (isset($plugins_array['sitealbum']))
		  unset($plugins_array['sitealbum']);
		$plugins_array = array_merge(array("Select"), $plugins_array);

    $this->addElement('Select', 'pagelevel_id', array(
      'label' => 'Content Type',
      'multiOptions' => $plugins_array,
      'onchange' => 'javascript:fetchLevelSettings(this.value);',
      'ignore' => true
    ));

		if (!empty($pagelevel_id)) {
		  //GET RESOURCE TYPE TITLE:
      $Module_title = $plugins_array[$pagelevel_id];
      $resource_type = $pagelevel_id;
      $pagelevel_id_temp = explode("_", $pagelevel_id);
      $pagelevel_id = $resource_type;
      $pagelevel_catid = $pagelevel_id_temp[0];
     
		  $plugin_type = Engine_Api::_()->facebookse()->getCategoryPlugin($pagelevel_id);
			switch ($plugin_type) {
				case 'homelike':
				  $plugin_name = 'Home';
				  
				  break;
  			case 'profilelike':
    		  $plugin_name = 'Member Profile';
    		  break;  
				case 'bloglike': 
				   $plugins_temp = str_replace('sitepage', "", $pagelevel_id);
				   if ($plugins_temp == $pagelevel_id) {
				     $plugin_temp1 = explode("sitebusiness", $pagelevel_id);
				     if (!empty($plugin_temp1[0]) && $plugin_temp1[0] == $pagelevel_id) { 
               $plugin_temp1 = explode("sitegroup", $pagelevel_id);
               if (!empty($plugin_temp1[0]) && $plugin_temp1[0] == $pagelevel_id) { 
                 $plugin_temp1 = explode("siteevent", $pagelevel_id);
                 if (!empty($plugin_temp1[0]) && $plugin_temp1[0] == $pagelevel_id) 
                   $plugin_name = $Module_title;
                 else if (empty($plugin_temp1[0]) && $plugin_temp1[1] != $pagelevel_id)
                 $plugin_name = 'Event ' . ucfirst($plugin_temp1[1]);
               }
               else if (empty($plugin_temp1[0]) && $plugin_temp1[1] != $pagelevel_id)
                 $plugin_name = 'Group ' . ucfirst($plugin_temp1[1]);
				     }
				     else if (empty($plugin_temp1[0]) && $plugin_temp1[1] != $pagelevel_id) { 
				       
				       $plugin_name = 'Business ' . ucfirst($plugin_temp1[1]);
				     }
				   }
				   else  { 
				       
             $plugin_temp1 = explode("sitepage", $pagelevel_id);
				     if (!empty($plugin_temp1[0]) && $plugin_temp1[0] == $pagelevel_id) {
				       $plugin_name = $Module_title;
				     }
				     else if (empty($plugin_temp1[0]) && $plugin_temp1[1] != $pagelevel_id) { 
				       
				       $plugin_name = 'Page ' . ucfirst($plugin_temp1[1]);
				     }
           }
				  
				  $plugin_type = 'bloglike';
				  break;
			 
			 
				case 'grouplike': 
				  if ($pagelevel_id == 'list') {
				    $plugin_name = 'Listing';
				  }
				  else if ($pagelevel_id == 'sitepage') {
				    $plugin_name = 'Page';
				  }
				  else if ($pagelevel_id == 'sitebusiness') {
				    $plugin_name = 'Business';
				  }
          else if ($pagelevel_id == 'sitestore') {
				    $plugin_name = 'Store';
				  }
          else if ($pagelevel_id == 'sitestoreproduct') {
				    $plugin_name = 'Store Product';
				  }
          else if ($pagelevel_id == 'sitegroup') {
				    $plugin_name = 'Group';
				  }
          else if ($pagelevel_id == 'siteevent') {
				    $plugin_name = 'Event';
				  }
				  else {
				    $plugin_name = $Module_title;
				  }
				  
				  break;
				default:
					$plugin_type = 'other';
					$plugin_name = $Module_title;
			}
      $typeinfo = '';  
      if ($fbLikeButton == 'default') {
        if ($pagelevel_id == 'blog') {
          $typeinfo = Zend_Registry::get('Zend_Translate')->_('The type  for this will be blog.');
        }
        else if ($pagelevel_id == 'music_playlist' || $pagelevel_id == 'sitepagemusic_playlist') {
          $typeinfo = Zend_Registry::get('Zend_Translate')->_('The type  for this will be song.');
        }
        else if ($pagelevel_id == 'home') {
          $typeinfo = Zend_Registry::get('Zend_Translate')->_('The type  for this will be website.');
        }
      }
      

      $image_desc = 'Upload the default picture for Image meta information for its content. [Note: If its content has a profile picture, then that profile picture will be associated with it as image meta information, otherwise this default image will be associated.]';
      

			if ($pagelevel_id == 'home') {
				$image_desc = Zend_Registry::get('Zend_Translate')->_('Upload the picture for Image meta information for Site Home');
      }

		//FINDING THE DATA TO SHOW PREFIELD IN FORM IF EXIST.
   $show_opengraph = array(1 => 'Yes', 0 => 'No');
    $this->addElement('Radio', 'opengraph_enable', array(
      'label' => 'Enable Opengraph',
      'description' => 'Enable Opengraph for this content type.',
      'multiOptions' => $show_opengraph,
      'value' => 1
    ));

    
    $opengraph_moduleinfo = '<b class="fnone">Open Graph meta information for this content type:</b><br />The Title and Description will be taken from its content\'s information.<br />' . $typeinfo;
		if ($plugin_type == 'homelike' ) {
		  
  		$this->addElement('Text', 'title', array(
  				'label' => 'Title',
  				'description' => '',
  				'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.home.title',  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')),
  				'required' => true,
  				'allowEmpty' => false,
  			));
  	
  			$this->addElement('Textarea', 'description', array(
  				'label' => 'Description',
  				'value'=> Engine_Api::_()->getApi('settings', 'core')->facebookse_home_textarea,
  					
  			));
  			$opengraph_moduleinfo = Zend_Registry::get('Zend_Translate')->_('<b class="fnone">Open Graph meta information for %1s:</b><br />The Title and Description will be taken from values filled above.<br />%2s');
  			$opengraph_moduleinfo = sprintf($opengraph_moduleinfo, $plugin_name, $typeinfo);
  	
		}
		
		if ($plugin_type == 'profilelike' ) {
		  $Displayname = Zend_Registry::get('Zend_Translate')->_("[User Displayname]'s Profile - %1s");
			$Displayname = sprintf($Displayname, Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.home.title',  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')));
  		$this->addElement('Dummy', 'title', array(
  				'label' => 'Title',
  				'content'=> $Displayname,
  				
  			));
  			
  	    $Displayname_desc = Zend_Registry::get('Zend_Translate')->_("[User Displayname]'s Profile on %1s");
			  $Displayname_desc = sprintf($Displayname_desc, Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.home.title',  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')). '. ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.home.title',  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.description')));
  			$this->addElement('Dummy', 'description', array(
  				'label' => 'Description',
  				'content'=> $Displayname_desc,
  					
  			));
  		
  	
		}
 
		if ($plugin_type != 'profilelike' ) {
  		$this->addElement('Dummy', 'opengraph_moduleinfo', array(
  			'description' => $opengraph_moduleinfo
  		));
      $this->opengraph_moduleinfo->getDecorator('Description')->setOptions(array('escape' => false));
		}

		$this->addElement('File', 'ContentPhoto', array(
			'label' => 'Image meta information',
			'description' => $image_desc,
			'destination' => APPLICATION_PATH.'/public/temporary/',
			'multiFile' => 1,
			'validators' => array(
				array('Count', false, 1),
				array('Size', false, 612000),
				array('Extension', false, 'jpg,jpeg,png,gif'),
			),
		));

		$this->ContentPhoto->getDecorator('Description')->setOptions(array('placement' => 'PREPEND','escape' => false));

		$this->addElement('Image', 'img', array(
			'src'=>$fbmeta_imageUrl,
			'style' => 'cursor:default;', 
			'label' => '',
			'decorators' => array('ViewHelper',
				array('HtmlTag', array('tag' => 'div', 'style' => 'float:center;','class'=>'fbconnect_ogs_userthumb'),array(
				'FormElements',
				'DivDivDivWrapper'
			)))
		));

		$this->addDisplayGroup(array('ContentPhoto', 'img'), 'group');
		$button_group = $this->getDisplayGroup('group');
       
 		if ($plugin_type != 'homelike' && $plugin_type != 'profilelike')  {       
      
      $desc = 'Should the creators of its contents be made admins of the corresponding Facebook pages of their contents?[Note: The Facebook user corresponding to the Site Admin is made the admin of every Facebook Page corresponding to the site pages. Admins of such pages will be able to publish Stream Updates on Facebook to the users who have liked the pages on site. There are 2 ways to get to the publishing interface: 1) From your Web page, click Admin Page next to the Like button. 2) From Facebook, click Manage Pages under the Account tab, then click Go To Page next to your page name.]';
			
			$option = 'Yes.[Note: If the creator of the content has not associated his account on site with his Facebook account, then he will not be made the admin.]';

			
			//CHECK IF FACEBOOK COMMENT BOX IS ENABLED.
			$permissionTable_Comments = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getMetainfo('', $resource_type);
      if ($permissionTable_Comments) {
      $permissionTable_Comments = $permissionTable_Comments->toArray();
      }
      $comment_setting = 0;
      if (!empty($permissionTable_Comments)) {
  			$comment_setting = $permissionTable_Comments['commentbox_enable'];
  		 
  		}		
			if ($comment_setting) {
			   $show_alertmesg = Zend_Registry::get('Zend_Translate')->_("As you have enabled Facebook Comments Box for this content type, only the Facebook user corresponding to the Site Admin is made the admin of corresponding Facebook Pages, and content creators cannot be made admins.");
			   $desc = $desc . '<br /><div class="tip"><span>' . $show_alertmesg . '</div></span>';
			}
//			$this->addElement('Radio', 'fbadmin_appid', array(
//				'label' => 'Admin of corresponding Facebook Page',
//				'description' => $desc,
//				'multiOptions' => array(
//					1 => $option,
//					0 => 'No'
//				),
//				'value' => 1,
//			));
//      $this->fbadmin_appid->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

      //CHECK IF ADMIN HAS ENABLED THE CUSTOM LIKE BUTTON THEN WE WILL NOT SHOW THE OBJECT META TYPE INFO HERE. THIS WILL BE TAKEN FROM THE LIKE BUTTON SETTING OF THAT CONTENT TYPE.
      
      $fbLikeButton = Engine_Api::_()->getApi("settings", "core")->getSetting('fblike.type', 'default');
      
      if ($fbLikeButton == 'custom') { 
        if ($plugin_type != 'other' && $pagelevel_id != 'sitestore_store') {
            $show_alertmesg = Zend_Registry::get('Zend_Translate')->_("As you have enabled Custom Facebook Like Button on your website. So, the Object Type meta information for this content will taken from the Facebook Like Button Settings section for this type of content <a href='admin/facebookse/settings/likes/' >here </a>.");
        }
        else if ($plugin_type == 'other' || $pagelevel_id == 'sitestore_store') {
           $show_alertmesg = Zend_Registry::get('Zend_Translate')->_("As you have enabled Custom Facebook Like Button on your website. So, the Object Type meta information for this content will taken from the Facebook Like Button Widget Settings, placed on the layout page of this content type.");
        }
			   $desc = '<br /><div class="tip"><span>' . $show_alertmesg . '</div></span>';
        $this->addElement('Dummy', 'text', array(
          'label' => 'Object Type meta information',
          'description' => $desc
        ));
         $this->text->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
      }
      else {
			if ($plugin_type == 'grouplike') {
        $desc_grouplike = 'Below, you can map the various contents categories with the desired object type. The Open Graph protocol is currently designed to support real-life objects. [Note: You can also leave a particular category as un-mapped; in that case, the corresponding object type for that category will not be set.]';
				$this->addElement('Dummy', 'text', array(
				  'label' => 'Object Type meta information.',
          'description' => $desc_grouplike
				));
				$categories = array();

				
				$extension_array = array('sitepagenote', 'sitepageevent', 'sitepagedocument', 'sitepagereview', 'sitepagediscussion', 'sitepagevideo', 'sitepagepoll', 'sitepagealbum', 'sitebusinessnote', 'sitebusinessevent', 'sitebusinessdocument', 'sitebusinessreview', 'sitebusinessdiscussion', 'sitebusinessvideo', 'sitebusinesspoll', 'sitebusinessalbum' , 'sitegroupnote', 'sitegroupevent', 'sitegroupdocument', 'sitegroupreview', 'sitegroupdiscussion', 'sitegroupvideo', 'sitegrouppoll', 'sitegroupalbum', 'siteeventdocument');
        if ($pagelevel_id == 'sitepage_photo' || $pagelevel_id == 'sitebusiness_photo' || $pagelevel_id == 'sitegroup_photo')
          $temppagelevelid = $pagelevel_catid. 'album';
        else
          $temppagelevelid = $pagelevel_catid;
       
        if (!in_array($temppagelevelid, $extension_array)) {
					$table  = Engine_Api::_()->getDbtable('categories', $pagelevel_catid);
	      }
	      $pagelevel_id = $pagelevel_catid;
				if ($pagelevel_id == 'classified' || $pagelevel_id == 'video' || $pagelevel_id == 'ynvideo' || $pagelevel_id == 'document' || $pagelevel_id == 'list' || $pagelevel_id == 'sitepage' || $pagelevel_id == 'recipe' || $pagelevel_id == 'sitebusiness' || $pagelevel_id == 'sitegroup' || $pagelevel_id == 'sitereview' || $pagelevel_id == 'sitestore' || $pagelevel_id == 'sitestoreproduct'|| $pagelevel_id == 'siteevent') {
					$cat_title = 'category_name';
				}	else {
					$cat_title = 'title';
				}
				
				
				
		    if ($pagelevel_id == 'list' || $pagelevel_id == 'sitepage' || $pagelevel_id == 'sitebusiness'  || $pagelevel_id == 'sitegroup' || $pagelevel_id == 'sitereview' || $pagelevel_id == 'sitestore' || $pagelevel_id == 'sitestoreproduct' || $pagelevel_id == 'siteevent') {
					$select = $table->select()->where('cat_dependency=?', 0);
          if ($pagelevel_id == 'sitereview')
            $select = $select->where('listingtype_id=?', $pagelevel_id_temp[2]);
        }	elseif (!in_array($pagelevel_id, $extension_array)) {
					$select = $table->select();
        }
        
        
           if (!in_array($pagelevel_id, $extension_array)) {
  			  foreach ($table->fetchAll($select) as $row) { 
  					$categories[$row['category_id']] = $row[$cat_title];
  					$ogtype = $this->getOgTypeArray($row);
  			      $this->addElement('Select', $row['category_id'] .'_ogtype', array(
  		          'label' => $row[$cat_title],
  		          'multiOptions' => $ogtype,
  		          'ignore' => true
  			      ));
  			      unset($ogtype);
  		    	}
			   }
	  		}
	  		$pagelevel_id = $pagelevel_catid;
	  		if ($plugin_type == 'bloglike' && ($pagelevel_id == 'poll' || $pagelevel_id == 'album' || $pagelevel_id == 'sitepagenote' || $pagelevel_id == 'sitepageevent'  || $pagelevel_id == 'sitepagedocument' || $pagelevel_id == 'sitepagereview' || $pagelevel_id == 'sitepagediscussion' || $pagelevel_id == 'sitepagevideo' || $pagelevel_id == 'sitepagepoll'  || $resource_type == 'sitepage_photo' || $pagelevel_id == 'sitebusinessnote' || $pagelevel_id == 'sitebusinessevent'  || $pagelevel_id == 'sitebusinessdocument' || $pagelevel_id == 'sitebusinessreview' || $pagelevel_id == 'sitebusinessdiscussion' || $pagelevel_id == 'sitebusinessvideo' || $pagelevel_id == 'sitebusinesspoll'  || $resource_type == 'sitebusiness_photo' || $pagelevel_id == 'sitegroupnote' || $pagelevel_id == 'sitegroupevent'  || $pagelevel_id == 'sitegroupdocument' || $pagelevel_id == 'sitegroupreview' || $pagelevel_id == 'sitegroupdiscussion' || $pagelevel_id == 'sitegroupvideo' || $pagelevel_id == 'sitegrouppoll'  || $resource_type == 'sitegroup_photo' || $pagelevel_id == 'siteeventdocument')) {
          if ($pagelevel_id != 'album' && $resource_type != 'sitepage_photo' && $resource_type != 'sitebusiness_photo' && $resource_type != 'sitegroup_photo') {
						$this->addElement('Dummy', 'text', array (
							'label' => 'Object Type meta information.',
							'description' => 'Below, you can select the desired object type for this content type. The Open Graph protocol is currently designed to support real-life objects.'
						));
          	$meta_type = 'article';
						$DESC = '';
          }
          else if ($pagelevel_id == 'album' || $resource_type == 'sitepage_photo' || $resource_type == 'sitebusiness_photo' || $resource_type == 'sitegroup_photo') { 
          	$meta_type = 'album';
						$DESC = Zend_Registry::get('Zend_Translate')->_('We suggest to choose the meta type of this plugin to album');
         }
          
					$row['category_id'] = 1;
	  			$ogtype = $this->getOgTypeArray($row);
          $LABLE = 'Content meta type';
          $LABLE = sprintf($LABLE, $plugin_name);
	  			$this->addElement('Select', $row['category_id'] .'_ogtype', array(
	          'label' => $LABLE,
	          'description' => $DESC ,
            //'onchange' => 'show_texfield(this);',
	          'multiOptions' => $ogtype,
	          'ignore' => true
			    ));
	  		}
	  		
	  		if ($plugin_type == 'other') { 
          
	  		  $classexists = ucfirst($pagelevel_id) . '_Model_DbTable_Categories';
	  		 
					if (class_exists($classexists) ) { 
					
						$desc_grouplike = 'Below, you can map the various contents categories with the desired object type. The Open Graph protocol is currently designed to support real-life objects. [Note: You can also leave a particular category as un-mapped; in that case, the corresponding object type for that category will not be set.]';
						
						$this->addElement('Dummy', 'text', array(
							'label' => 'Object Type meta information.',
							'description' => $desc_grouplike
						));
						$categories = array();
						
						$table  = Engine_Api::_()->getDbtable('categories', $pagelevel_id);
						$select = $table->select();

						foreach ($table->fetchAll($select) as $row) {
							if (isset($row['category_name'])) {
								$category_name = $row['category_name'];
							} else {
								$category_name = $row['title'];
							}
							$categories[$row['category_id']] = $category_name;
							$ogtype = $this->getOgTypeArray($row);
							
							$this->addElement('Select', $row['category_id'] .'_ogtype', array(
								'label' => $category_name,
								'multiOptions' => $ogtype,
								'ignore' => true
							));
							unset($ogtype);
						}
						
					} else {
					     $this->addElement('Dummy', 'text', array (
							'label' => 'Object Type meta information.',
							'description' => 'Below, you can select the desired object type for this content type. The Open Graph protocol is currently designed to support real-life objects.'
						));
						$DESC = '';
						$row['category_id'] = 1;
						$LABLE = 'Content meta type';
						$this->addElement('Select', $row['category_id'] .'_ogtype', array(
						'label' => $LABLE,
						'description' => $DESC ,
						'multiOptions' => $this->getOgTypeArray($row),
						'ignore' => true
						));
					}
	  		}
      }
 	  	}
	  	
	  	$this->addElement('hidden', 'plugin_type', array(
        'value' => $plugin_type,
        'order' => 1000
    	));
    	
	  	$this->addElement('Button', 'submit', array(
	      'label' => 'Save Settings',
	      'type' => 'submit',
	      'ignore' => true
	    ));
  	}
  	return true;
  }
  
  public function getOgTypeArray($row = null) {
     return array(
			0 => 'Select',
      'Books' => array(
				$row['category_id'] . '-books.author' => 'books.author',
				$row['category_id'] . '-books.book' => 'books.book',
				$row['category_id'] . '-books.genre' => 'books.genre'
			),
      'Businesses' => array(
				$row['category_id'] . '-business.business' => 'business.business',
			),
      'Election' => array(
				$row['category_id'] . '-quick_election.election' => 'quick_election.election',
			),
			'Fitness' => array(
				$row['category_id'] . '-fitness.unit' => 'fitness.unit',
				$row['category_id'] . '-fitness.course' => 'fitness.course'
			),
			'Music' => array(
				$row['category_id'] . '-music.song' => 'music.song',
				$row['category_id'] . '-music.radio_station' => 'music.radio_station',
				$row['category_id'] . '-music.playlist' => 'music.playlist',
        $row['category_id'] . '-music.album' => 'music.album',
        $row['category_id'] . '-music.musician' => 'music.musician',
			),
      'Product' => array(
				$row['category_id'] . '-product.group' => 'product.group',
        $row['category_id'] . '-product.item' => 'product.item',
			),   
			'Restaurant' => array(
				$row['category_id'] . '-restaurant.restaurant' => 'restaurant.restaurant',
				$row['category_id'] . '-restaurant.menu_item' => 'restaurant.menu_item',
				$row['category_id'] . '-restaurant.menu_section' => 'restaurant.menu_section',
				$row['category_id'] . '-restaurant.menu' => 'restaurant.menu'
				
			),
			'Video' => array(
				$row['category_id'] . '-video.other' => 'video.other',
				$row['category_id'] . '-video.tv_show' => 'video.tv_show',
				$row['category_id'] . '-video.movie' => 'video.movie',
				$row['category_id'] . '-video.episode' => 'video.episode'
			),
			'General' => array(
				$row['category_id'] . '-place' => 'place',
				$row['category_id'] . '-website' => 'website',
				$row['category_id'] . '-book' => 'book',
				$row['category_id'] . '-profile' => 'profile',
				$row['category_id'] . '-object' => 'object',
				$row['category_id'] . '-product' => 'product',
				$row['category_id'] . '-article' => 'article',
			),
	  );
   
  }
}
