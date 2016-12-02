<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl .
'application/modules/Seaocore/externals/scripts/core.js');
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/Adintegration.tpl';
?> 
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1);?>
<script type="text/javascript">

	var group_communityads;
	var contentinformtion;
	var group_showtitle;
	//prev_tab_id = '<?php //echo $this->content_id; ?>';	
  if(contentinformtion == 0) {
		if($('global_content').getElement('.layout_activity_feed')) {
			$('global_content').getElement('.layout_activity_feed').style.display = 'none';
		}		
		if($('global_content').getElement('.layout_sitegroup_location_sitegroup')) {
			$('global_content').getElement('.layout_sitegroup_location_sitegroup').style.display = 'none';
		}	
		if($('global_content').getElement('.layout_sitegroup_info_sitegroup')) {
			$('global_content').getElement('.layout_sitegroup_info_sitegroup').style.display = 'block';
		}	
		if($('global_content').getElement('.layout_core_profile_links')) {
			$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
		}
		if($('global_content').getElement('.layout_sitegroup_overview_sitegroup')) {
			$('global_content').getElement('.layout_sitegroup_overview_sitegroup').style.display = 'none';
	  }
    if($('global_content').getElement('.layout_sitegroupintegration_profile_items')) {
			$('global_content').getElement('.layout_sitegroupintegration_profile_items').style.display = 'none';
	  }
  }
  
</script>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')):?>
  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adinfowidget', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)) : ?>
    <?php $flag = 1; ?>
  <?php else:?>
    <?php $flag = 0; ?>
  <?php endif;?>
<?php endif;?>
<?php
$contactPrivacy=0;
$profileTypePrivacy=0;
$isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($this->sitegroup, 'contact');
	if(!empty($isManageAdmin)) {
		$contactPrivacy = 1;
	}

  // PROFILE TYPE PRIVACY
  $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($this->sitegroup, 'profile');
		if(!empty($isManageAdmin)) {
			$profileTypePrivacy = 1;
		}
?>

<?php if($this->showtoptitle == 1):?>
	<div class="layout_simple_head" id="layout_info">
		<?php echo $this->translate("Basic Information") ?>
	</div>
<?php endif;?>
<div id='id_<?php echo $this->content_id; ?>'>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')):?>
<?php if(Engine_Api::_()->getApi('SubCore', 'sitegroup')->getSampleAdWidgetEnabled($this->sitegroup) || $flag ) : ?>
	<div class="layout_right" >
	<?php endif; ?>
		<?php if(!empty($this->isManageAdmin) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adcreatelink', 1)) : ?>
			<?php 
				echo $this->content()->renderWidget("communityad.getconnection-link");
		  ?>
    <span class="adpreview_seprator"></span>
		<?php endif; ?>
		<?php if(!empty($this->isManageAdmin) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adpreview', 1)) : ?>
			<?php
				// Render Sample Group Ad widget
				echo $this->content()->renderWidget("communityad.pagead-preview"); 
			?>
    <span class="adpreview_seprator"></span>
		<?php endif; ?>		
		<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adinfowidget', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)) : ?>
			<div id="communityad_info" >
				<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adinfowidget', 3),"loaded_by_ajax"=>0,'widgetId'=>'group_info'))?>
			</div>
		<?php endif; ?>
		<?php if(Engine_Api::_()->getApi('SubCore', 'sitegroup')->getSampleAdWidgetEnabled($this->sitegroup) || $flag ) : ?>
	</div>
	<?php endif; ?>
	<div class="layout_middle">
<?php endif;?>
<div class='profile_fields'>
	<h4 id='show_basicinfo'>
		<span><?php echo $this->translate('Basic Information'); ?></span>
	</h4>
	<ul>
    <?php if($postedBy):?>
      <li>
        <span><?php echo $this->translate('Created By:'); ?> </span>
        <span><?php echo $this->htmlLink($this->sitegroup->getParent(), $this->sitegroup->getParent()->getTitle()) ?></span>
      </li>
     <?php endif;?>
    <li>
    	<span><?php echo $this->translate('Posted:'); ?></span>
      <span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitegroup->creation_date))) ?></span>
    </li>    
    <li>
    	<span><?php echo $this->translate('Last Updated:'); ?></span>
			<span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitegroup->modified_date))) ?></span>
    </li>
    <?php if(!empty($this->sitegroup->member_count) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')): ?>
    	<li>
			<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1); ?>
			<?php if ($this->sitegroup->member_title && $memberTitle) : ?>

					<span><?php echo $this->sitegroup->member_title . ':' ?></span>
					<span><?php echo $this->sitegroup->member_count ?></span>

			<?php else: ?>
			<span><?php echo $this->translate('Members:'); ?></span>
			<span><?php echo $this->sitegroup->member_count ?></span>
			<?php endif; ?>
    </li>
    <?php endif; ?>
    <?php if(!empty($this->sitegroup->comment_count)): ?>
    	<li>
    		<span><?php echo $this->translate('Comments:'); ?></span>
				<span><?php echo $this->sitegroup->comment_count ?></span>
      </li>
    <?php endif; ?>
    <?php if(!empty($this->sitegroup->view_count)): ?>
      <li>
      	<span><?php echo $this->translate('Views:'); ?></span>
				<span><?php echo $this->sitegroup->view_count ?></span>
      </li>
    <?php endif; ?>
    <?php if(!empty($this->sitegroup->like_count)): ?>
    	<li>
    		<span><?php echo $this->translate('Likes:'); ?></span>
				<span><?php echo $this->sitegroup->like_count ?></span>
      </li>
    <?php endif; ?>
    <?php if(!empty($this->sitegroup->follow_count) && isset($this->sitegroup->follow_count)): ?>
    	<li>
    		<span><?php echo $this->translate('Followers:'); ?></span>
				<span><?php echo $this->translate( $this->sitegroup->follow_count) ?></span>
      </li>
    <?php endif; ?>
    <form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'sitegroup','action' => 'index'), 'sitegroup_general', true) ?>' style='display: none;'>
      <input type="hidden" id="tag" name="tag" value=""/>
      <input type="hidden" id="category" name="category" value=""/>
      <input type="hidden" id="subcategory" name="subcategory" value=""/>
      <input type="hidden" id="categoryname" name="categoryname" value=""/>
      <input type="hidden" id="subcategoryname" name="subcategoryname" value=""/>
      <input type="hidden" id="subsubcategory" name="subsubcategory" value=""/>
      <input type="hidden" id="subsubcategoryname" name="subsubcategoryname" value=""/>
      <input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date)
        echo $this->start_date; ?>"/>
      <input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date)
               echo $this->end_date; ?>"/>
    </form>
    <li class="mtop5">
	    <?php if($this->category_name != '' && $this->subcategory_name == '') :?>
		    <span><?php echo $this->translate('Category:'); ?></span> 
		    <span>
		    				
				<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name)), 'sitegroup_general_category'), $this->translate($this->category_name)) ?>
				
		    </span>
	    <?php elseif($this->category_name != '' && $this->subcategory_name != ''): ?> 
		    <span><?php echo $this->translate('Category:'); ?></span>
		    <span><?php echo $this->htmlLink($this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name)), 'sitegroup_general_category'), $this->translate($this->category_name)) ?>
				<?php if(!empty($this->category_name)): echo '&raquo;'; endif; ?>
			  <?php echo $this->htmlLink($this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitegroup->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->subcategory_name)), 'sitegroup_general_subcategory'), $this->translate($this->subcategory_name)) ?>			  
			  <?php if(!empty($this->subsubcategory_name)): echo '&raquo;';?>
        <?php echo $this->htmlLink($this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitegroup->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->sitegroup->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->subsubcategory_name)), 'sitegroup_general_subsubcategory'),$this->translate($this->subsubcategory_name)) ?>
	   		<?php endif; ?>
        </span>
	    <?php endif; ?>
    </li>
    <li>
    	<?php if (count($this->sitegroupTags) >0): $tagCount=0;?>
    		<span><?php echo $this->translate('Tags:'); ?></span>
        <span>
    		 <?php foreach ($this->sitegroupTags as $tag): ?>
					<?php if (!empty($tag->getTag()->text)):?>
						<?php if(empty($tagCount)):?>
						<a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>
							<?php $tagCount++; else: ?>
						<a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>
						<?php endif; ?>
					<?php endif; ?>
        <?php endforeach; ?>
        </span>
			<?php endif; ?>
    </li>
  
    <?php  $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1); ?>
     <?php if($this->sitegroup->price && $enablePrice):?>
    <li>
    	<span><?php echo $this->translate('Price:'); ?></span>
      <span><?php echo $this->locale()->toCurrency($this->sitegroup->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?></span>
    </li>
    <?php endif; ?>
     <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1); ?>
     <?php if($this->sitegroup->location && $enableLocation):?>
    <li>
    	<span><?php echo $this->translate('Location:'); ?></span>
      <span><?php echo $this->sitegroup->location ?>&nbsp; - 
      <b>
				<?php $location_id = Engine_Api::_()->getDbTable('locations', 'sitegroup')->getLocationId($this->sitegroup->group_id, $this->sitegroup->location);
				echo  $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->sitegroup->group_id, 'resouce_type' => 'sitegroup_group', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('class' => 'smoothbox')) ;?>
      </b>
      </span>
    </li>
    <?php endif; ?>
     <li>
    	<span><?php echo $this->translate('Description:'); ?></span>
      <span><?php echo $this->viewMore($this->sitegroup->body,300,5000) ?></span>
    </li>	  
  </ul>
  <?php
		$user = Engine_Api::_()->user()->getUser($this->sitegroup->owner_id);
		$view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'contact_detail');
    $availableLabels = array('phone' => 'Phone','website' => 'Website','email' => 'Email');		
    $options_create = array_intersect_key($availableLabels, array_flip($view_options));
  ?>
 <?php if(!empty($contactPrivacy)): ?>
  <?php if(!empty($options_create) && (!empty($this->sitegroup->email) || !empty($this->sitegroup->website) || !empty($this->sitegroup->phone))):?>
  <h4>
		<span><?php echo $this->translate('Contact Details');  ?></span>
	</h4>  	
    <ul>
    	<li style="display:none;"></li>
      <?php if(isset($options_create['phone']) && $options_create['phone'] == 'Phone'):?>
        <?php if(!empty($this->sitegroup->phone)):?>
        <li>
          <span><?php echo $this->translate('Phone:'); ?></span>
          <span><?php echo $this->translate(''); ?> <?php echo $this->sitegroup->phone ?></span>
        </li>
        <?php endif; ?>
      <?php endif; ?>

      <?php if(isset($options_create['email']) && $options_create['email'] == 'Email'):?>
        <?php if(!empty($this->sitegroup->email)):?>
        <li>
          <span><?php echo $this->translate('Email:'); ?></span>
          <span><?php echo $this->translate(''); ?>
						<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitegroup_profilegroup', 'module' => 'sitegroup', 'controller' => 'profile', 'action' => 'email-me', "id" => $this->sitegroup->group_id), 'default' , true)); ?>'); return false;"><?php echo $this->sitegroup->getTitle(); ?></a>
          </span>
        </li>
        <?php endif; ?>
      <?php endif; ?>
      <?php if( isset($options_create['website']) && $options_create['website'] == 'Website'):?>
        <?php if(!empty($this->sitegroup->website)):?>
        <li>
          <span><?php echo $this->translate('Website:'); ?></span>
          <?php if(strstr($this->sitegroup->website, 'http://') || strstr($this->sitegroup->website, 'https://')):?>
          <span><a href='<?php echo $this->sitegroup->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitegroup->website ?></a></span>
          <?php else:?>
          <span><a href='http://<?php echo $this->sitegroup->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitegroup->website ?></a></span>
          <?php endif;?>
        </li>
        <?php endif; ?>
      <?php endif; ?>
    </ul>
    <?php endif; ?>
  <?php endif; ?>
 	<?php if(!empty ($profileTypePrivacy)):
     $params = array('custom_field_heading' => 1, 'custom_field_title' => 1, 'customFieldCount' => 1000, 'widgetName' => 'infoProfile');
     $str =  $this->groupProfileFieldValueLoop($this->sitegroup, $this->fieldStructure, $params); ?>
		<?php if($str): ?>
			<h4 >
				<span><?php  echo $this->translate('Profile Information');  ?></span>
			</h4>
			<?php echo $this->groupProfileFieldValueLoop($this->sitegroup, $this->fieldStructure, $params) ?>
		<?php endif; ?>
	<?php endif; ?>

	<br />
	<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.checkcomment.widgets', 1)):
		
	 ?>
		<div id="info_comment">
		<?php 
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl';
    ?>
		</div>
	<?php endif; ?>
</div>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')):?>
	</div>
<?php endif; ?>
</div>

<script type="text/javascript">

  $$('li.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function(event) 
  {	
     if($('global_content').getElement('.layout_sitegroupintegration_profile_items')) {
			  $('global_content').getElement('.layout_sitegroupintegration_profile_items').style.display = 'none';
	   }

     if(group_showtitle != 0 ) {
			if($('profile_status')) {
				$('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitegroup->getTitle())?></h2>";
			}	  
			if($('layout_info')) {
				$('layout_info').style.display = 'block';
				$('show_basicinfo').style.display = 'none';
			}	  	
    }
    
    hideWidgetsForModule('sitegroupinfo');

    if($('global_content').getElement('.layout_sitegroup_contactdetails_sitegroup')) {
      $('global_content').getElement('.layout_sitegroup_contactdetails_sitegroup').style.display = 'block';
    }

  	$('id_' + <?php echo $this->content_id ?>).style.display = "block";
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {    	
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }

    prev_tab_id = '<?php echo $this->content_id; ?>';	
    prev_tab_class = 'layout_sitegroup_info_sitegroup';
		setLeftLayoutForGroup(); 
		if($(event.target).get('tag') !='div' && ($(event.target).getParent('.layout_sitegroup_info_sitegroup')==null)){
      scrollToTopForGroup($("global_content").getElement(".layout_sitegroup_info_sitegroup"));
    }	        
  });
                    
  var tagAction =function(tag)
  {    
    $('tag').value = tag;
    $('filter_form').submit();
  }
	if($("info_comment"))
  var info_comment = $("info_comment").innerHTML;

function showSmoothBox(url)
{
  Smoothbox.open(url);
  parent.Smoothbox.close;
}
</script>