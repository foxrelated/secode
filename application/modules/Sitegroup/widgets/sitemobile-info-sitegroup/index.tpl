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
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1);?>
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

		<?php echo $this->translate("Basic Information") ?>

<?php endif;?>
<!--<div id='id_<?php echo $this->content_id; ?>'>-->
      
<!--<div class='profile_fields'>-->
	<h4 id='show_basicinfo'>
		<span><?php echo $this->translate('Basic Information'); ?></span>
	</h4>
  
	<div class="sm_ui_item_profile_details">
	<table>
		<tbody>
    <?php if($postedBy):?>
     <tr valign="top">
        <td class="label"><div><?php echo $this->translate('Created By:'); ?> </div></td>
        <td><?php echo $this->htmlLink($this->sitegroup->getParent(), $this->sitegroup->getParent()->getTitle()) ?></td>
      </tr>
     <?php endif;?>
    	<tr valign="top">
					<td class="label"><div><?php echo $this->translate('Posted:'); ?></div></td>
      <td><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitegroup->creation_date))) ?></td>
    </tr> 
    <tr valign="top">
					<td class="label"><div><?php echo $this->translate('Last Updated:'); ?></div></td>
			<td><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitegroup->modified_date))) ?></td>
   </tr>
    <?php if(!empty($this->sitegroup->member_count) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')): ?>
    	<tr valign="top">
			<td class="label"><div> <?php echo ($this->sitegroup->member_title && $this->sitegroup->member_count >1 && Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1) ) ? $this->sitegroup->member_title . ':' :$this->translate('Members:'); ?></div></td>
			<td><?php echo $this->sitegroup->member_count ?></td>
    </tr>
    <?php endif; ?>
    <?php if(!empty($this->sitegroup->comment_count)): ?>
    	<tr valign="top">
    		<td class="label"><div><?php echo $this->translate('Comments:'); ?></div></td>
				<td><?php echo $this->sitegroup->comment_count ?></td>
       </tr>
    <?php endif; ?>
    <?php if(!empty($this->sitegroup->view_count)): ?>
    	<tr valign="top">
      <td class="label"><div><?php echo $this->translate('Views:'); ?></div></td>
			<td><?php echo $this->sitegroup->view_count ?></td>
      </tr>
    <?php endif; ?>
    <?php if(!empty($this->sitegroup->like_count)): ?>
    <tr valign="top">
    	<td class="label"><div><?php echo $this->translate('Likes:'); ?></div></td>
			<td><?php echo $this->sitegroup->like_count ?></td>
     </tr>
    <?php endif; ?>
    <?php if(!empty($this->sitegroup->follow_count) && isset($this->sitegroup->follow_count)): ?>
  	<tr valign="top">
    	<td class="label"><div><?php echo $this->translate('Followers:'); ?></div></td>
				<td><?php echo $this->translate( $this->sitegroup->follow_count) ?></td>
    </tr>
    <?php endif; ?>
    <tr valign="top" class="mtop5">
	    <?php if($this->category_name != '' && $this->subcategory_name == '') :?>
		    <td class="label"><div><?php echo $this->translate('Category:'); ?></div></td>		 
		    <td>	
				<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name)), 'sitegroup_general_category'), $this->translate($this->category_name)) ?>
				</td>
	    <?php elseif($this->category_name != '' && $this->subcategory_name != ''): ?> 
		    <td class="label"><div><?php echo $this->translate('Category:'); ?></div></td>	
		    <td>	<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name)), 'sitegroup_general_category'), $this->translate($this->category_name)) ?>
				<?php if(!empty($this->category_name)): echo '&raquo;'; endif; ?>
			  <?php echo $this->htmlLink($this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitegroup->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->subcategory_name)), 'sitegroup_general_subcategory'), $this->translate($this->subcategory_name)) ?>			  
			  <?php if(!empty($this->subsubcategory_name)): echo '&raquo;';?>
        <?php echo $this->htmlLink($this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitegroup->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->sitegroup->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($this->subsubcategory_name)), 'sitegroup_general_subsubcategory'),$this->translate($this->subsubcategory_name)) ?>
	   		<?php endif; ?>
        </td>	
	    <?php endif; ?>
    </tr>	
   <tr valign="top">    	
    	<?php if (count($this->sitegroupTags) >0): $tagCount=0;?>
    		<td class="label"><div><?php echo $this->translate('Tags:'); ?></div></td>	
        <td>
    		 <?php foreach ($this->sitegroupTags as $tag): ?>
					<?php if (!empty($tag->getTag()->text)):?>
						<?php if(empty($tagCount)):?>
              <a href='<?php echo $this->url(array('action' => 'index', 'tag' => $tag->getTag()->tag_id, 'tag_name' => Engine_Api::_()->seaocore()->getSlug($tag->getTag()->text, 225)), "sitegroup_general"); ?>'>#<?php echo $tag->getTag()->text ?></a>
							<?php $tagCount++; else: ?>
							<a href='<?php echo $this->url(array('action' => 'index', 'tag' => $tag->getTag()->tag_id, 'tag_name' => Engine_Api::_()->seaocore()->getSlug($tag->getTag()->text, 225)), "sitegroup_general"); ?>'>#<?php echo $tag->getTag()->text ?></a>
						<?php endif; ?>
					<?php endif; ?>
        <?php endforeach; ?>
        </td>	
			<?php endif; ?>
  </tr>	
    <?php  $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1); ?>
     <?php if($this->sitegroup->price && $enablePrice):?>
    <tr valign="top">    
    	<td class="label"><div><?php echo $this->translate('Price:'); ?></div></td>	
      <td><?php echo $this->locale()->toCurrency($this->sitegroup->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?></td>	
     </tr>	
    <?php endif; ?>
     <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1); ?>
     <?php if($this->sitegroup->location && $enableLocation):?>
    <tr valign="top">    
    	<td class="label"><div><?php echo $this->translate('Location:'); ?></div></td>	
      <td><?php echo $this->htmlLink('https://maps.google.com/?q='.urlencode($this->sitegroup->location), $this->sitegroup->location, array('target' => 'blank')) ?>
      </td>
   </tr>	
    <?php endif; ?>
    <tr valign="top">    
    	<td class="label"><div><?php echo $this->translate('Description:'); ?></div></td>
      <td><?php echo $this->viewMore($this->sitegroup->body,300,5000) ?></td>
    </tr>	
  </tbody>
  </table>
    </div>
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
   <div class="sm_ui_item_profile_details">
	<table>
		<tbody>
          <tr valign="top" style="display:none;"> </tr>
      <?php if(isset($options_create['phone']) && $options_create['phone'] == 'Phone'):?>
        <?php if(!empty($this->sitegroup->phone)):?>
        <tr valign="top"> 
          <td class="label"><div><?php echo $this->translate('Phone:'); ?></div></td>
          <td><?php echo $this->translate(''); ?> <a href="tel:<?php echo $this->sitegroup->phone?>"> <?php echo $this->sitegroup->phone?> </a></td>
        </tr>
        <?php endif; ?>
      <?php endif; ?>

      <?php if(isset($options_create['email']) && $options_create['email'] == 'Email'):?>
        <?php if(!empty($this->sitegroup->email)):?>
        <tr valign="top"> 
          <td class="label"><div><?php echo $this->translate('Email:'); ?></div></td>
          <td><?php echo $this->translate(''); ?>
          <a href='mailto:<?php echo $this->sitegroup->email ?>'><?php echo $this->sitegroup->email ?></a></td>
        </tr>
        <?php endif; ?>
      <?php endif; ?>
      <?php if( isset($options_create['website']) && $options_create['website'] == 'Website'):?>
        <?php if(!empty($this->sitegroup->website)):?>
        <tr valign="top"> 
         <td class="label"><div><?php echo $this->translate('Website:'); ?></div></td>
          <?php if(strstr($this->sitegroup->website, 'http://') || strstr($this->sitegroup->website, 'https://')):?>
          <td><a href='<?php echo $this->sitegroup->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitegroup->website ?></a></td>
          <?php else:?>
          <td><a href='http://<?php echo $this->sitegroup->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->sitegroup->website ?></a></td>
          <?php endif;?>
        </tr>
        <?php endif; ?>
      <?php endif; ?>
    </tbody>
  </table>
     </div>
    <?php endif; ?>
  <?php endif; ?>
 	<?php if(!empty ($profileTypePrivacy)):
            $params = array('custom_field_heading' => 1, 'custom_field_title' => 1, 'customFieldCount' => 1000, 'widgetName' => 'infoProfile');
    $str =  $this->groupProfileFieldValueLoop($this->sitegroup, $this->fieldStructure, $params)?>
		<?php if($str): ?>
			<h4 >
				<span><?php  echo $this->translate('Profile Information');  ?></span>
			</h4>
			<?php echo $this->groupProfileFieldValueLoop($this->sitegroup, $this->fieldStructure, $params) ?>
		<?php endif; ?>
	<?php endif; ?>
	<?php echo $this->content()->renderWidget("sitemobile.comments", array('type' => $this->sitegroup->getType(), 'id' => $this->sitegroup->getIdentity())); ?>