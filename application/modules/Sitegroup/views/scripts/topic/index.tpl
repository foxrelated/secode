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
<?php 
  include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/Adintegration.tpl';
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitegroupdiscussion/externals/styles/style_sitegroupdiscussion.css')
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitegroup_viewgroups_head">
	<?php echo $this->htmlLink($this->sitegroup->getHref(), $this->itemPhoto($this->sitegroup, 'thumb.icon', '' , array('align'=>'left'))) ?>
	<h2>	
	  <?php echo $this->sitegroup->__toString() ?>	
	  <?php echo $this->translate('&raquo; ');?>
    <?php echo $this->htmlLink(array( 'route' => 'sitegroup_entry_view', 'group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($this->sitegroup->group_id), 'tab' => $this->tab_selected_id), $this->translate('Discussions')) ?>
  </h2>  
</div>
<!--RIGHT AD START HERE-->
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addiscussionview', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)):?>
	<div class="layout_right" id="communityad_topicindex">
		<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addiscussionview', 3),"loaded_by_ajax"=>1,'widgetId'=>'group_topicindex'))?>
	</div>
<?php endif;?>
<!--RIGHT AD END HERE-->
<div class="sitegroup_discussion_view">
	<?php if( $this->paginator->count() > 1 ): ?>
	  <div>
	    <br />
	    <?php echo $this->paginationControl($this->paginator) ?>
	    <br />
	  </div>
	<?php endif; ?>
	
	<ul class="sitegroup_sitegroups">
	  <?php foreach( $this->paginator as $topic ):
	      $lastpost = $topic->getLastPost();
	      $lastposter = $topic->getLastPoster();
	      ?>
	    <li>
	      <div class="sitegroup_sitegroups_replies">
	        <span>
	          <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
	        </span>
	        <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
	      </div>
	      <div class="sitegroup_sitegroups_lastreply">
	        <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
	        <div class="sitegroup_sitegroups_lastreply_info">
	          <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> <?php echo $this->translate('by');?> <?php echo $lastposter->__toString() ?>
	          <br />
	          <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'sitegroup_sitegroups_lastreply_info_date')) ?>
	        </div>
	      </div>
	      <div class="sitegroup_sitegroups_info">
	        <h3<?php if( $topic->sticky ): ?> class='sitegroup_sitegroups_sticky'<?php endif; ?>>
	          <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
            <?php if(($resource=$topic->getResource())!=null):?>
            <span style="float: right;">
            <?php echo $this->translate("In ".$resource->getMediaType().":") ?>
            <?php echo $this->htmlLink($resource->getHref(), $resource->getTitle()) ?>
          </span>
          <?php endif;?>
	        </h3>
          
	        <div class="sitegroup_sitegroups_blurb">
	          <?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
	        </div>
	      </div>
	    </li>
	  <?php endforeach; ?>
	</ul>
	
	<?php if( $this->paginator->count() > 1 ): ?>
	  <div>
	    <?php echo $this->paginationControl($this->paginator) ?>
	  </div>
	<?php endif; ?>
</div>	