<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: show-content.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()
					->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteadvsearch/externals/styles/style_siteadvsearch.css') ?>

<?php $this->viewer_id = $this->viewer->getIdentity();?>
<?php $showOptions = $this->statstics;?>

<?php if(!empty($this->text)):?>
  <?php $count = $this->paginator->count();?>
<?php else:?>
  <?php $count = count($this->paginator);?>
<?php endif;?>

<?php $extensionArray = array('sitepagenote', 'sitepagevideo', 'sitepagepoll', 'sitepagemusic', 'sitepagealbum', 'sitepageevent', 'sitepagereview', 'sitepagedocument','sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessnote', 'sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessvideo', 'sitebusinessreview', 'sitegroupalbum', 'sitegroupdocument', 'sitegroupevent', 'sitegroupnote', 'sitegrouppoll', 'sitegroupmusic', 'sitegroupvideo', 'sitegroupreview');?>

<?php $params = array();?>

<script type="text/javascript">
  
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
  
  var vote = function(viewer_id, feedback_id) {
  		if(viewer_id == 0) {
  	  	var browse_url = "<?php echo $this->browse_url ?>";
				window.location = browse_url;
  		}		
 		 if(viewer_id != 0) {
   		// SENDING REQUEST TO AJAX   
   		var request = en4.feedback.voting.createVote(viewer_id, feedback_id);    
   
   		// RESPONCE FROM AJAX
   		request.addEvent('complete', function(responseJSON) 
   		{
   	 		$('feedback_voting_' + feedback_id).innerHTML = responseJSON.total;
      		$('feedback_vote_' + feedback_id).innerHTML = responseJSON.abc;
   
   		});
 		 }
		}
 
  var  removevote = function(vote_id, feedback_id, viewer_Id) {
	  
	  	// SENDING REQUEST TO AJAX   
	  	var request = en4.feedback.voting.removeVote(vote_id, feedback_id, viewer_Id);    
	   
	   	// RESPONCE FROM AJAX
	   	request.addEvent('complete', function(responseJSON)  { 
	       $('feedback_voting_' + feedback_id).innerHTML = responseJSON.total;
	        $('feedback_vote_' + feedback_id).innerHTML = responseJSON.abc;
	   	}); 
		}
  
  var categoryAction = function(category, type) {
       if(type == 'feedback') {
        var url = en4.core.baseUrl + 'feedback'+'?'+'category=' +category;
        window.location.href = url;
       }
  }
</script>
<div id="list_view">
  <ul class="seaocore_browse_list"  id="profile_content">
    <?php if($count > 0):?>
      <?php foreach ($this->paginator as $item): ?>
        <?php $showDescription = 1;?>
        <?php $itemType = $item->type;?>
        <?php if(!Engine_Api::_()->hasItemType($itemType)):?>
          <?php continue;?>
        <?php endif;?>
        <?php $object = Engine_Api::_()->getItem($itemType, $item->id);?>
        <?php if(empty($object) || $itemType == 'classified_album'):?>
          <?php continue; ?>
        <?php endif;?>
        <?php $moduleName = strtolower($object->getModuleName());?>
        <?php $modules = Engine_Api::_()->getDbtable('contents', 'siteadvsearch')->getIncludedModules();?>
        <?php if(in_array($itemType, $modules) || ($itemType == 'sitereview_listing')):?>
          <?php $contentItem = Engine_Api::_()->seaocore()->getModuleItem($itemType, $item->id);?>
        <?php else:?>
          <?php $contentItem = $object;?>
        <?php endif;?>
        <li class="b_medium prelative">
          <?php if($itemType == 'feedback'):?>
            <div class="feedbacks_list_vote_button">
              <div class="feedback_votes_counts">
                <p id="feedback_voting_<?php echo $contentItem->feedback_id; ?>"> 
                <?php echo $contentItem->total_votes ;?>
                </p>
                <?php echo $this->translate('votes');?>
              </div>
              <div id="feedback_vote_<?php echo $contentItem->feedback_id; ?>" class="feedback_vote_button">
                <?php if($contentItem->vote_id == NULL):?>
                  <a href="javascript:void(0);" onClick="vote('<?php echo $this->viewer_id; ?>', '<?php echo $contentItem->feedback_id; ?>');"><?php echo $this->translate('Vote'); ?></a>
                <?php elseif($this->viewer_id != 0): ?>
                  <a href="javascript:void(0);" onClick="removevote('<?php echo $contentItem->vote_id; ?>',  '<?php echo $contentItem->feedback_id; ?>', '<?php echo $this->viewer_id; ?>');"><?php echo $this->translate('Remove'); ?></a>
                <?php endif; ?>
              </div>
            </div>
          <?php else:?>
            <div class="siteadvsearch_browse_list_photo">
              <?php if(isset($contentItem->photo_id)):?>
                <?php if(!empty($contentItem->photo_id)):?>
                  <?php echo $this->htmlLink($object->getHref(), $this->itemPhoto($object, 'thumb.profile')) ?>
                <?php elseif($itemType == 'document'):?>
                  <?php if(!empty($contentItem->thumbnail)):?>
                    <?php echo $this->htmlLink($contentItem->getHref(), '<img src="'. $contentItem->thumbnail .'" class="thumb_profile" />', array('title' => $contentItem->document_title) ) ?>
                  <?php else:?>
                    <?php $imageType = Engine_Api::_()->siteadvsearch()->getDefaultPhoto($itemType);?>
                    <?php echo $this->htmlLink($object->getHref(), $this->itemPhoto($object, $imageType)) ?>
                  <?php endif;?>
                <?php else:?>
                  <?php $imageType = Engine_Api::_()->siteadvsearch()->getDefaultPhoto($itemType);?>
                  <?php echo $this->htmlLink($object->getHref(), $this->itemPhoto($object, $imageType)) ?>
                <?php endif;?>
              <?php else:?>
                <?php $defaultImage = Engine_Api::_()->siteadvsearch()->getDefaultPhoto($itemType);?>
                <?php if($moduleName == 'sitepagedocument' || $moduleName == 'sitebusinessdocument' || $moduleName == 'sitegroupdocument'):?>
                  <?php 
                  //SSL WORK
                  $this->https = 0;
                  if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
                  $this->https = 1;
                  }
                  ?>
                  <?php if(!empty($contentItem->thumbnail)): ?>
                    <?php if($this->https):?>
                       <?php $manifestPath = Engine_Api::_()->getApi('settings', 'core')->getSetting($moduleName.'.manifestUrl', "page-documents");?>
                      <?php $contentItem->thumbnail = $this->baseUrl().'/'.$manifestPath."/ssl?url=".urlencode($contentItem->thumbnail); ?>
                    <?php endif; ?>
                    <?php echo $this->htmlLink($contentItem->getHref(), '<img src="'. $contentItem->thumbnail .'" />') ?>
                  <?php else: ?>
                    <?php echo $this->htmlLink($object->getHref(), $this->itemPhoto($object, $defaultImage)) ?>
                  <?php endif;?>
                <?php elseif($moduleName == 'forum' || $itemType == 'sitepage_topic' || $itemType == 'sitebusiness_topic' || $itemType == 'sitegroup_topic'):?>
                  <?php $defaultphoto = $this->layout()->staticBaseUrl.'application/modules/Siteadvsearch/externals/images/forum_default_photo.png';?>
                  <?php echo $this->htmlLink($object->getHref(), '<img src= "'.$defaultphoto.'" class="thumb_profile" />') ?>
                  <?php elseif($moduleName == 'blog'):?>
                  <?php $defaultphoto = $this->layout()->staticBaseUrl.'application/modules/Siteadvsearch/externals/images/blog_default_photo.png';?>
                  <?php echo $this->htmlLink($object->getHref(), '<img src= "'.$defaultphoto.'" class="thumb_profile" />') ?>
                  
                <?php elseif($defaultImage != ' '):?>
                  <?php echo $this->htmlLink($object->getHref(), $this->itemPhoto($object, $defaultImage)) ?>
                <?php else:?>
                  <?php $defaultphoto = $this->layout()->staticBaseUrl.'application/modules/Siteadvsearch/externals/images/default_photo.png';?>
                  <?php echo $this->htmlLink($object->getHref(), '<img src= "'.$defaultphoto.'" class="thumb_profile" />') ?>
                <?php endif;?>
              <?php endif;?>
              <?php switch($itemType):
              case 'sitepage_page':
                if (!empty($contentItem->sponsored) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.image', 1)) {
                $color = Engine_Api::_()->getApi("settings", "core")->getSetting("sitepage.sponsored.color", "#fc0505");
                echo '<div class="seaocore_list_sponsored_label" style="background: '.$color.'">'.$this->translate('SPONSORED').'</div>';
                }
                break;
              case 'sitebusiness_business':
                if (!empty($contentItem->sponsored) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebusiness.sponsored.image', 1)) {
                $color = Engine_Api::_()->getApi("settings", "core")->getSetting("sitebusiness.sponsored.color", "#fc0505");
                echo '<div class="seaocore_list_sponsored_label" style="background: '.$color.'">'.$this->translate('SPONSORED').'</div>';
                }
                break;
              case 'sitegroup_group':
                if (!empty($contentItem->sponsored) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.image', 1)) {
                $color = Engine_Api::_()->getApi("settings", "core")->getSetting("sitegroup.sponsored.color", "#fc0505");
                echo '<div class="seaocore_list_sponsored_label" style="background: '.$color.'">'.$this->translate('SPONSORED').'</div>';
                }
                break;
              case 'sitestore_store':
                if (!empty($contentItem->sponsored) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.image', 1)) {
                $color = Engine_Api::_()->getApi("settings", "core")->getSetting("sitestore.sponsored.color", "#fc0505");
                echo '<div class="seaocore_list_sponsored_label" style="background: '.$color.'">'.$this->translate('SPONSORED').'</div>';
                }
                break;
              case 'siteevent_event':
                if (!empty($contentItem->sponsored)) {
                $color = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505');
                echo '<div class="seaocore_list_sponsored_label" style="background: '.$color.'">'.$this->translate('SPONSORED').'</div>';
                }
                break;
              case 'sitestoreproduct_product':
                if (!empty($contentItem->sponsored)) {
                $color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505');
                echo '<div class="seaocore_list_sponsored_label" style="background: '.$color.'">'.$this->translate('SPONSORED').'</div>';
                }
                break;
              endswitch; ?>
            </div>
          <?php endif;?>
          <div class="seaocore_browse_list_info">  
            <div class="sr_browse_list_info_header o_hidden">
              <div class="seaocore_browse_list_info_title">
                <span>
                  <?php if(isset ($contentItem->closed)):?>
                    <?php if( $contentItem->closed ): ?>
                      <img alt="close" src='<?php echo $this->layout()->staticBaseUrl?>application/modules/Siteadvsearch/externals/images/close.png'/>
                    <?php endif;?>  
                  <?php endif;?>
                  <?php if(in_array('sponsored', $showOptions) && isset ($contentItem->sponsored)):?>
                    <?php if ($contentItem->sponsored == 1): ?>
                      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteadvsearch/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                    <?php endif; ?>
                  <?php endif;?>  
                  <?php if (in_array('featured', $showOptions) && isset($contentItem->featured )): ?>
                    <?php if ($contentItem->featured == 1): ?>
                      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteadvsearch/externals/images/list_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </span>
                <?php if(in_array('rating', $showOptions) && isset($contentItem->rating)):?>
                  <span class="list_rating_star" title="<?php echo $contentItem->rating.$this->translate(' rating'); ?>">
                   <?php if (($contentItem->rating > 0) && Engine_Api::_()->getApi('settings', 'core')->getSetting($object->getModuleName().'.rating', 1)): ?>
                    <?php for ($x = 1; $x <= $contentItem->rating; $x++): ?>
                     <span class="seao_rating_star_generic rating_star_y" ></span>
                    <?php endfor; ?>
                    <?php if ((round($contentItem->rating) - $contentItem->rating) > 0): ?>
                     <span class="seao_rating_star_generic rating_star_half_y" ></span>
                    <?php endif; ?>
                   <?php endif; ?>
                  </span>
                <?php endif;?>
                <h3><?php echo $this->htmlLink($object->getHref(), $object->getTitle()) ?></h3>
                <?php if(in_array('contenttype', $showOptions)):?>
                  <?php if(isset($contentItem->listingtype_id)):?>
                    <?php $listingTypeId = $contentItem->listingtype_id;?>
                  <?php else:?>
                    <?php $listingTypeId = 0;?>
                  <?php endif;?>
                  <?php //$resourceTitle = Engine_Api::_()->getDbTable('contents', 'siteadvsearch')->getResourceTitle($itemType, $listingTypeId);?>
                  
                  <?php $resourceTitle = $contentItem->getShortType();
                    if($contentItem->getShortType() == 'user') {
                        $resourceTitle = 'member';  
                    }
                   ?>
                  
                  <div class="seao_list_short_title"><?php echo $this->translate(ucfirst($resourceTitle));?></div>
                  
                <?php endif;?>
                <div class="clear"></div>
              </div>
              <?php if(in_array($moduleName, $extensionArray) || ($itemType == 'sitepage_album' || $itemType == 'sitebusiness_album' || $itemType == 'sitegroup_album')):?>
                <?php if(isset ($contentItem->page_id)):?>
                  <?php $parentId = $contentItem->page_id;?>
                  <?php $apiModule = 'sitepage';?>
                  <?php $parentTitle = Engine_Api::_()->getitem('sitepage_page', $parentId)->title;?>
                <?php elseif(isset ($contentItem->business_id)):?>
                  <?php $parentId = $contentItem->business_id;?>
                  <?php $apiModule = 'sitebusiness';?>
                  <?php $parentTitle = Engine_Api::_()->getitem('sitebusiness_business', $parentId)->title;?>
                <?php elseif(isset ($contentItem->group_id)):?>
                  <?php $parentId = $contentItem->group_id;?>
                  <?php $apiModule = 'sitegroup';?>
                  <?php $parentTitle = Engine_Api::_()->getitem('sitegroup_group', $parentId)->title;?>
                <?php endif;?>
                <div class="seaocore_browse_list_info_date">
                <?php $owner_id = 0;?>
                <?php if(isset ($contentItem->owner_id)):?>
                  <?php $owner_id = $contentItem->owner_id;?>
                <?php elseif(isset ($contentItem->user_id)):?>
                  <?php $owner_id = $contentItem->user_id;?>
                <?php endif;?>
                <?php if(!empty($owner_id)):?>
                  <?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->$apiModule()->getHref($parentId, $owner_id, $contentItem->getSlug()),  $parentTitle) ?>
                  <?php endif;?>
                </div>
                <?php echo Engine_Api::_()->siteadvsearch()->getStatstics($contentItem, $moduleName);?>
                <?php if(isset ($contentItem->pros)):?>
                  <?php $ratingData = Engine_Api::_()->getDbtable('ratings', $moduleName)->profileRatingbyCategory($contentItem->review_id); ?>
                  <div class="seaocore_overallrating seaocore_browse_overallrating">		
                   <?php foreach($ratingData as $reviewcat): ?>
                    <div class="seaocore_overallrating_rate">
                     <div class="title">
                      <?php if(!empty($reviewcat['reviewcat_name'])): ?>
                       <?php 
                        switch($reviewcat['rating']) {
                         case 0:
                           $rating_value = '';
                           break;
                         case $reviewcat['rating'] <= .5:
                           $rating_value = 'halfstar-small-box';
                           break;
                         case $reviewcat['rating'] <= 1:
                           $rating_value = 'onestar-small-box';
                           break;
                         case $reviewcat['rating'] <= 1.5:
                           $rating_value = 'onehalfstar-small-box';
                           break;
                         case $reviewcat['rating'] <= 2:
                           $rating_value = 'twostar-small-box';
                           break;
                         case $reviewcat['rating'] <= 2.5:
                           $rating_value = 'twohalfstar-small-box';
                           break;
                         case $reviewcat['rating'] <= 3:
                           $rating_value = 'threestar-small-box';
                           break;
                         case $reviewcat['rating'] <= 3.5:
                           $rating_value = 'threehalfstar-small-box';
                           break;
                         case $reviewcat['rating'] <= 4:
                           $rating_value = 'fourstar-small-box';
                           break;
                         case $reviewcat['rating'] <= 4.5:
                           $rating_value = 'fourhalfstar-small-box';
                           break;
                         case $reviewcat['rating'] <= 5:
                           $rating_value = 'fivestar-small-box ';
                           break;
                        }
                       ?>
                       <?php echo $this->translate($reviewcat['reviewcat_name']); ?>

                      <?php else:?>
                       <?php 
                        switch($reviewcat['rating']) {
                         case 0:
                           $rating_value = '';
                           break;
                         case $reviewcat['rating'] <= .5:
                           $rating_value = 'halfstar';
                           break;
                         case $reviewcat['rating'] <= 1:
                           $rating_value = 'onestar';
                           break;
                         case $reviewcat['rating'] <= 1.5:
                           $rating_value = 'onehalfstar';
                           break;
                         case $reviewcat['rating'] <= 2:
                           $rating_value = 'twostar';
                           break;
                         case $reviewcat['rating'] <= 2.5:
                           $rating_value = 'twohalfstar';
                           break;
                         case $reviewcat['rating'] <= 3:
                           $rating_value = 'threestar';
                           break;
                         case $reviewcat['rating'] <= 3.5:
                           $rating_value = 'threehalfstar';
                           break;
                         case $reviewcat['rating'] <= 4:
                           $rating_value = 'fourstar';
                           break;
                         case $reviewcat['rating'] <= 4.5:
                           $rating_value = 'fourhalfstar';
                           break;
                         case $reviewcat['rating'] <= 5:
                           $rating_value = 'fivestar';
                           break;
                        }
                       ?>
                       <b><?php echo $this->translate("Overall Rating");?></b>
                      <?php endif; ?>
                     </div>
                     <?php if(!empty($reviewcat['reviewcat_name'])): ?>
                      <div class="rates">
                       <ul class='rating-box-small <?php echo $rating_value; ?>'>
                        <li id="1" class="rate one">1</li>
                        <li id="2" class="rate two">2</li>
                        <li id="3" class="rate three">3</li>
                        <li id="4" class="rate four">4</li>
                        <li id="5" class="rate five">5</li>
                       </ul>
                      </div>
                     <?php else:?>
                      <div class="rates">
                       <!--<ul title="<?php echo $reviewcat['rating'].$this->translate(" rating"); ?>" class='rating <?php echo $rating_value; ?>' style="background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteadvsearch/externals/images/show-star-matrix.png);">-->
                       <ul title="<?php echo $reviewcat['rating'].$this->translate(" rating"); ?>" class='sr_sitestoreproduct_us_rating <?php echo $rating_value; ?>'>
                        <li id="1" class="rate one">1</li>
                        <li id="2" class="rate two">2</li>
                        <li id="3" class="rate three">3</li>
                        <li id="4" class="rate four">4</li>
                        <li id="5" class="rate five">5</li>
                       </ul>
                      </div>
                    <?php endif;?>
                    </div>
                   <?php endforeach; ?>
                  </div>
                  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting($moduleName.'.proscons', 1)):?>
                    <div class="seaocore_browse_list_info_blurb">
                      <?php echo '<b>' .$this->translate("Pros: "). '</b>' .$this->viewMore($contentItem->pros) ?>
                    </div>
                    <div class="seaocore_browse_list_info_blurb">
                      <?php echo '<b>' .$this->translate("Cons: "). '</b>' .$this->viewMore($contentItem->cons) ?>
                    </div>
                  <?php endif;?>
                  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting($moduleName.'.recommend', 1)):?>
                    <div class='seaocore_browse_list_info_blurb'>
                      <?php if($contentItem->recommend):?>
                        <?php echo $this->translate("<b>Member's Recommendation:</b> Yes"); ?>
                      <?php else: ?>
                        <?php echo $this->translate("<b>Member's Recommendation:</b> No"); ?>
                      <?php endif;?>
                    </div>
                  <?php endif;?>
                <?php endif;?>
              <?php endif;?>
              <?php if($itemType == 'sitepageoffer_offer' || $itemType == 'sitebusinessoffer_offer' || $itemType == 'sitegroupoffer_offer'):?>
                <?php if($itemType == 'sitepageoffer_offer'):?>
                  <?php $apiModule = 'sitepage';?>
                  <?php $parentId = $contentItem->page_id;?>
                  <?php $parentTitle = Engine_Api::_()->getitem('sitepage_page', $parentId)->title;?>
                <?php elseif($itemType == 'sitebusinessoffer_offer'):?>
                  <?php $apiModule = 'sitebusiness';?>
                  <?php $parentId = $contentItem->business_id;?>
                  <?php $parentTitle = Engine_Api::_()->getitem('sitebusiness_business', $parentId)->title;?>
                <?php elseif($itemType == 'sitegroupoffer_offer'):?>
                  <?php $apiModule = 'sitegroup';?>
                  <?php $parentId = $contentItem->group_id;?>
                  <?php $parentTitle = Engine_Api::_()->getitem('sitegroup_group', $parentId)->title;?>
                <?php endif;?>
                <div class="seaocore_browse_list_info_date">
                  <?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->$apiModule()->getHref($parentId, $contentItem->owner_id, $contentItem->getSlug()),  $parentTitle) ?>
                </div>
                <div class="seaocore_browse_list_info_date">
                  <span><?php echo $this->translate('End date:');?></span>
                  <?php if($contentItem->end_settings == 1):?><span><?php echo $this->translate( gmdate('M d, Y', strtotime($contentItem->end_time))) ?></span><?php else:?><span><?php echo $this->translate('Never Expires');?></span><?php endif;?>
                </div>
              <?php endif;?>
            </div>
            <?php if(in_array($itemType, $modules) || ($itemType == 'sitereview_listing')):?>
              <div class="seaocore_browse_list_info_date seaocore_txt_light">
              <?php switch($itemType):
                case 'sitereview_listing':
                  $showDescription = 0;
                  $title_singular = Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypeColumn($contentItem->listingtype_id, 'title_singular');
                  if(in_array('postedby', $showOptions)) {
                    $showStatics = $this->timestamp(strtotime($contentItem->creation_date));
                    $showStatics .= ' - '. $this->translate(strtoupper($title_singular). '_posted_by');
                    $showStatics .= ' '.$this->htmlLink($contentItem->getOwner()->getHref(), $contentItem->getOwner()->getTitle());
                    $showStatics .= ', ';
                  }
                  if(in_array('commentcount', $showOptions) && isset($contentItem->comment_count)) {
                    $showStatics .= $this->translate(array('%s comment', '%s comments', $contentItem->comment_count), $this->locale()->toNumber($contentItem->comment_count)).', ';
                  }
                  if(in_array('reviewcount', $showOptions) && isset($contentItem->review_count)) {
                    $showStatics .= $this->translate(array('%s review', '%s reviews', $contentItem->review_count), $this->locale()->toNumber($contentItem->review_count)).', ';
                  }
                  if(in_array('viewcount', $showOptions) && isset($contentItem->view_count)) {
                    $showStatics .= $this->translate(array('%s view', '%s views', $contentItem->view_count), $this->locale()->toNumber($contentItem->view_count)).', ';
                  }
                  if(in_array('likecount', $showOptions) && isset($contentItem->like_count)) {
                    $showStatics .= $this->translate(array('%s like', '%s likes', $contentItem->like_count), $this->locale()->toNumber($contentItem->like_count));
                  }
                  $showStatics .= '</div>';
                  echo $showStatics;
                  if(in_array('category', $showOptions)) {
                    $url = $this->url(array('category_id' => $contentItem->category_id, 'categoryname' => $contentItem->getCategory()->getCategorySlug()), "sitereview_general_category_listtype_" . $contentItem->listingtype_id);
                    echo '<div class="seaocore_browse_list_info_date seaocore_txt_light">'.'<a href="'.$url.'">'.$this->translate($contentItem->getCategory()->getTitle(true)).'</a>'.'</div>';
                  }
                  if(in_array('description', $showOptions)) {
                  echo  '<div class="seaocore_browse_list_info_blurb">'.$this->viewMore($item->description).'</div>';
                  }
                  $price = Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypeColumn($contentItem->listingtype_id, 'price');
                 if($contentItem->price > 0 && $price) {
                   $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                    echo '<div class="sr_browse_list_info_stat">'.
                      '<b>'.$this->locale()->toCurrency($contentItem->price, $currency).'</b>'.
                    '</div>';
                  }

                  $location = Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypeColumn($contentItem->listingtype_id, 'location');
                  if(in_array('location', $showOptions) && !empty($contentItem->location) && $location) {
                    echo '<div class="sr_browse_list_info_stat seaocore_txt_light">'.
                      $this->translate($contentItem->location).
                      ' - <b>'.$this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $contentItem->listing_id, 'resouce_type' => 'sitereview_listing'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')).'</b>'.
                    '</div>';
                  }

                  echo '<div class="seaocore_browse_list_info_date">'.$this->compareButton($contentItem);
                  echo $this->addToWishlist($contentItem, array('classIcon' => 'sr_wishlist_href_link', 'classLink' => '')) . '</div>'; 
                  break;
                case 'sitepage_page':
                  $params['module_name'] = 'sitepage';
                  $params['resource_type'] = 'sitepage_page';
                  $params['content_type_id'] = 'page_id';
                  Engine_Api::_()->siteadvsearch()->getCommonContentItem($contentItem, $params);
                  break;
                case 'sitebusiness_business':
                  $params['module_name'] = 'sitebusiness';
                  $params['resource_type'] = 'sitebusiness_business';
                  $params['content_type_id'] = 'business_id';
                  Engine_Api::_()->siteadvsearch()->getCommonContentItem($contentItem, $params);
                  break;
                case 'sitegroup_group':
                  $params['module_name'] = 'sitegroup';
                  $params['resource_type'] = 'sitegroup_group';
                  $params['content_type_id'] = 'group_id';
                  Engine_Api::_()->siteadvsearch()->getCommonContentItem($contentItem, $params);
                  break;
                case 'list_listing':
                  echo Engine_Api::_()->siteadvsearch()->getStatstics($contentItem, 'list');
                  if(in_array('location', $showOptions) && !empty($contentItem->location)  &&  Engine_Api::_()->authorization()->isAllowed($contentItem, $this->viewer(), 'view')) {
                    echo '<div class="seaocore_browse_list_info_date">'.$this->translate($contentItem->location).'&nbsp;- <b>'.$this->htmlLink(array("route" => "seaocore_viewmap", "id" => $contentItem->listing_id, "resouce_type" => "list_listing"), $this->translate("Get Directions"), array("onclick" => "owner(this);return false")).'</b></div>';
                  }
                  break;
                case 'recipe':
                  echo Engine_Api::_()->siteadvsearch()->getStatstics($contentItem, 'recipe');
                  if(in_array('location', $showOptions) && !empty($contentItem->location)  &&  Engine_Api::_()->authorization()->isAllowed($contentItem, $this->viewer(), 'view')) {
                    echo '<div class="seaocore_browse_list_info_date">'.$this->translate($contentItem->location).'&nbsp;- <b>'.$this->htmlLink(array("route" => "seaocore_viewmap", "id" => $contentItem->recipe_id, "resouce_type" => "recipe"), $this->translate("Get Directions"), array("onclick" => "owner(this);return false")).'</b></div>';
                  }
                  break;
                case 'sitestore_store':
                  $params['module_name'] = 'sitestore';
                  $params['resource_type'] = 'sitestore_store';
                  $params['content_type_id'] = 'store_id';
                  Engine_Api::_()->siteadvsearch()->getCommonContentItem($contentItem, $params);
                  break;
                case 'sitestoreproduct_product':
                  $showDescription = 0;
                  if(in_array('category', $showOptions)) {
                  $categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
                  $url = $this->url(array('category_id' => $contentItem->category_id, 'categoryname' => $contentItem->getCategory()->getCategorySlug()), "". $categoryRouteName ."");
                  echo '<div class="sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light">'.'<a href="'.$url.'">'.$contentItem->getCategory()->getTitle(true).'</a></div>';
                  }
                  if(in_array('description', $showOptions)) {
                  echo  '<div class="seaocore_browse_list_info_blurb">'.$this->viewMore($item->description).'</div>';
                  }
                  echo $this->getProductInfo($contentItem, 0, 'list_view', 1, 1, true);
                  echo Engine_Api::_()->siteadvsearch()->getStatstics($contentItem, 'sitestoreproduct');
                  echo '<div class="sr_sitestoreproduct_browse_list_info_footer clr o_hidden">'.'<div>'.$this->compareButtonSitestoreproduct($contentItem, 0).'</div><div>'.$this->addToWishlistSitestoreproduct($contentItem, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => '')).'</div></div>'; 
                  break;
               case 'siteevent_event':
                  if (in_array('siteevent_event', $modules)) {
                    $eventitem = '';
                    if($contentItem->featured) {
                      $eventitem .= '<i class="siteevent_list_featured_label" title="'.$this->translate('Featured').'"></i>';
                    }
                    $this->statistics = array("hostName","categoryLink","featuredLabel","sponsoredLabel","newLabel","startDate","endDate","ledBy","price","venueName","location","directionLink","viewCount","likeCount","commentCount","memberCount","reviewCount","ratingStar");
                    $ratingValue = 'rating_both';
                    $ratingShow = 'small-star';
                    $ratingType = 'user';
                    $eventitem .= '<div class="siteevent_browse_list_information fleft">';
                    $eventitem .= $this->eventInfo($contentItem, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => '50')); 
                    $eventitem .= '</div>';
                    echo $eventitem;
                  }
                  break;
                case 'classified':
                  echo Engine_Api::_()->siteadvsearch()->getStatstics($object, $moduleName);
                  $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($contentItem);           
                  $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
                  echo "<div class='classifieds_browse_info_blurb'>".$this->fieldValueLoop($contentItem, $fieldStructure).'</div>';
                  break;
                case 'group':
                  echo $this->translate(array('%s member', '%s members', $contentItem->membership()->getMemberCount()),$this->locale()->toNumber($contentItem->membership()->getMemberCount())).$this->translate(' led by ') .$this->htmlLink($contentItem->getOwner()->getHref(), $contentItem->getOwner()->getTitle());
                  break;
                case 'event':
                  echo '<div class="events_members">'.$this->locale()->toDateTime($contentItem->starttime).'</div><div class="events_members">'.$this->translate(array('%s guest', '%s guests', $contentItem->membership()->getMemberCount()),$this->locale()->toNumber($contentItem->membership()->getMemberCount())).$this->translate(' led by ').$this->htmlLink($contentItem->getOwner()->getHref(), $contentItem->getOwner()->getTitle()).'</div>';
                  break;
                case 'music_playlist':
                  echo '<div class="music_browse_info_date">'.$this->translate('Created %s by ', $this->timestamp($contentItem->creation_date)).$this->htmlLink($contentItem->getOwner(), $contentItem->getOwner()->getTitle()).' - '.$this->htmlLink($contentItem->getHref(),  $this->translate(array('%s comment', '%s comments', $contentItem->getCommentCount()), $this->locale()->toNumber($contentItem->getCommentCount()))).'</div>';
                  break;
                default:
                  if($itemType != 'sitefaq_faq')
                  echo Engine_Api::_()->siteadvsearch()->getStatstics($object, $moduleName);
               endswitch; ?>
            <?php endif;?>
            <?php if($itemType == 'feedback'):?>
              <div>
                <?php if (in_array('category', $showOptions) && $contentItem->category_id && ($category = Engine_Api::_()->getItem('feedback_category', $contentItem->category_id))): ?>
                  <?php echo $this->translate('Category:');?> <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $category->category_id?>, "feedback");'><?php echo $category->category_name ?></a>
                <?php endif;?>
              </div>
            <?php endif;?>
            <?php if($itemType == 'sitefaq_faq'):?><br />
              <?php if(!empty($this->viewer->viewer_id)):?>
                <?php $level_id = $this->viewer->level_id;?>
              <?php else:?>
                <?php $level_id = 0;?>
              <?php endif;?>
              <?php $this->can_share = Engine_Api::_()->authorization()->getPermission($level_id, 'sitefaq_faq', 'share');?>
              <?php echo $this->htmlLink($contentItem->getHref(), $this->translate('Permalink')) ?> |
              <?php if(!empty($this->viewer_id) && $contentItem->draft == 0 && $contentItem->approved == 1 && $contentItem->search == 1 && !empty($this->can_share)): ?>
                <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'sitefaq_faq', 'id' => $contentItem->faq_id), $this->translate("Share"), array('onclick' => 'owner(this);return false')); ?> |
              <?php endif;?>
              <?php echo Engine_Api::_()->siteadvsearch()->getStatstics($contentItem, strtolower($contentItem->getModuleName())); ?>
            <?php endif;?>
            <?php if(in_array('description', $showOptions) && !empty($showDescription)):?>
              <div class='seaocore_browse_list_info_blurb'>
                <?php echo $this->viewMore($item->description) ?>
              </div>
           <?php endif;?>
          </div>
        </li> 
      <?php endforeach; ?>
    <?php else:?>
      <div class="tip" >
        <?php if(!empty($this->text)):?><br />
        <span><?php echo $this->translate('Sorry, no results containing your search terms were found.');?></span>
       <?php else:?><br />
         <span><?php echo $this->translate('Please enter your search query and search again.');?></span>
       <?php endif;?>
      </div>
    <?php endif;?>
  </ul>
</div>
          
<div class="clr" id="scroll_bar_height"></div>
<?php if (empty($this->is_ajax) && $count > 0) : ?>
  <div class = "seaocore_view_more mtop10" id="seaocore_view_more" style="display: none;">
    <?php
    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
        'id' => '',
        'class' => 'buttonlink icon_viewmore'
    ))
    ?>
  </div>
  <div class="seaocore_view_more" id="loding_image" style="display: none;">
    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
    <?php echo $this->translate("Loading ...") ?>
  </div>
  <div id="hideResponse_div"> </div>
<?php endif; ?>

<?php if (empty($this->is_ajax)) : ?>
  <script type="text/javascript">
    function viewMoreResult()
    { 
      $('seaocore_view_more').style.display = 'none';
      $('loding_image').style.display = '';
      var params = {
        requestParams:<?php echo json_encode($this->params) ?>
      };
      setTimeout(function() {
        en4.core.request.send(new Request.HTML({
          method: 'get',
          'url': en4.core.baseUrl + 'siteadvsearch/index/show-content',
          data: $merge(params.requestParams, {
            format: 'html',
            subject: en4.core.subject.guid,
            page: getNextPage(),
            isajax: 1,
            show_content: '<?php echo $this->showContent;?>',
            loaded_by_ajax: true
          }),
          evalScripts: true,
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('hideResponse_div').innerHTML = responseHTML;
            $('list_view').getElement('.seaocore_browse_list').innerHTML = $('list_view').getElement('.seaocore_browse_list').innerHTML + $('hideResponse_div').getElement('.seaocore_browse_list').innerHTML;
            $('hideResponse_div').innerHTML = '';
            $('loding_image').style.display = 'none';
          }
        }));
      }, 800);

      return false;
    }
  </script>
<?php endif; ?>

<?php if ($this->showContent == 3): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'block';
      hideViewMoreLink('<?php echo $this->showContent; ?>');
    });</script>
<?php elseif ($this->showContent == 2): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'block';
      hideViewMoreLink('<?php echo $this->showContent; ?>');
    });</script>
<?php else: ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'none';
    });
  </script>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "siteadvsearch"), array("query" => $this->formValues)); ?>
<?php endif; ?>
<?php if ($count):?>
<script type="text/javascript">

  function getNextPage() {
    return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
  }

  function hideViewMoreLink(showContent) {

    if (showContent == 3) {
      $('seaocore_view_more').style.display = 'none';
      var totalCount = '<?php echo $this->paginator->count(); ?>';
      var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

      function doOnScrollLoadPage()
      {
        if (typeof($('scroll_bar_height').offsetParent) != 'undefined') {
          var elementPostionY = $('scroll_bar_height').offsetTop;
        } else {
          var elementPostionY = $('scroll_bar_height').y;
        }
        if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
          if ((totalCount != currentPageNumber) && (totalCount != 0))
            viewMoreResult();
        }
      }
      
      window.onscroll = doOnScrollLoadPage;

    }
    else if (showContent == 2)
    {
      var view_more_content = $('seaocore_view_more');
      view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
      view_more_content.removeEvents('click');
      view_more_content.addEvent('click', function() {
        viewMoreResult();
      });
    }
  }
</script>
<?php endif; ?>