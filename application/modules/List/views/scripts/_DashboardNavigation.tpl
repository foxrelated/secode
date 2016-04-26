<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: _DashboardNavigation.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');

	$viewer = Engine_Api::_()->user()->getViewer();
	$style_allow = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('list_listing', $viewer->level_id, 'style');
?>

<div class="headline listting_dashboard_navigation">
  <h2> <?php echo $this->translate('Listing Dashboard'); ?> </h2>
  <div class="tabs">
    <ul class="navigation">
      <li <?php if($this->TabActive =="edit"): ?> class="active" <?php endif; ?> >
      	<a href='<?php echo $this->url(array('action' => 'edit', 'listing_id' => $this->list->listing_id), 'list_specific', true) ?>' ><?php echo $this->translate('Info'); ?></a>
      </li>
      <?php if ($this->allow_overview_of_owner): ?>
				<li <?php if($this->TabActive =="overview"): ?> class="active" <?php endif; ?> >
					<a href='<?php echo $this->url(array('action' => 'overview', 'listing_id' => $this->list->listing_id), 'list_specific', true) ?>' ><?php echo $this->translate('Overview'); ?></a>
				</li>
      <?php endif; ?>
       <?php if ($style_allow): ?>
       <li <?php if($this->TabActive =="style"): ?> class="active" <?php endif; ?> >
      	<a href='<?php echo $this->url(array('action' => 'editstyle', 'listing_id' => $this->list->listing_id), 'list_specific', true) ?>' ><?php echo $this->translate('Style'); ?></a>
      </li>
      <?php endif;?>
       <?php if (Engine_Api::_()->list()->enableLocation()): ?>
      <li <?php if($this->TabActive =="location"): ?> class="active" <?php endif; ?> >
        <a href='<?php echo $this->url(array('action' => 'editlocation', 'listing_id' => $this->list->listing_id), 'list_specific', true) ?>' ><?php echo $this->translate('Location'); ?></a>
      </li>
      <?php endif; ?>
      <?php if ($this->allowed_upload_photo): ?>
      	<li <?php if($this->TabActive =="photo"): ?> class="active" <?php endif; ?>>
      		<a href='<?php echo $this->url(array('listing_id' => $this->list->listing_id), 'list_albumspecific', true) ?>'><?php echo $this->translate('Photos'); ?></a>
      	</li>
      <?php endif; ?>
      <?php if ($this->allowed_upload_video): ?>
      	<li <?php if($this->TabActive =="video"): ?> class="active" <?php endif; ?>>
      		<a href='<?php echo $this->url(array('listing_id' => $this->list->listing_id), 'list_videospecific', true) ?>'><?php echo $this->translate('Videos'); ?></a>
      	</li>
      <?php endif; ?>

      <li>
      	<a href='<?php echo $this->url(array('action' => 'manage'),  'list_general',true) ?>'><?php echo $this->translate('My Listings'); ?></a>
      </li>
      <li>
      	<a href='<?php echo $this->url(array('listing_id' => $this->list->listing_id,'user_id' => $this->list->owner_id, 'slug' => $this->list->getSlug()), 'list_entry_view', true) ?>'><?php echo $this->translate('View Listing'); ?></a>
      </li>
    </ul>
  </div>
</div>