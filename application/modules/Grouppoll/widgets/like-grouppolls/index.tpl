<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<ul class="seaocore_sidebar_list">
  <?php foreach( $this->listLikedPolls as $grouppoll ): ?>
  	<li>
      <?php echo $this->htmlLink(
        $grouppoll->getHref(),
        $this->itemPhoto($grouppoll->getOwner(), 'thumb.icon', $grouppoll->getOwner()->getTitle()),
          array('class' => 'grouppolls_profile_photo', 'title' => $grouppoll->title)
        ) ?>
	    <div class='seaocore_sidebar_list_info'>
	    	<div class='seaocore_sidebar_list_title'>
          <?php echo $this->htmlLink($grouppoll->getHref(), Engine_Api::_()->grouppoll()->turncation($grouppoll->getTitle()), array('title'=> $grouppoll->getTitle()));?>
      	</div>
        <div class='seaocore_sidebar_list_details'>
     			<?php echo $this->translate(array('%s Like', '%s Likes', $grouppoll->count_likes), $this->locale()->toNumber($grouppoll->count_likes)) ?> |
     	 		<?php echo $this->translate(array('%s Vote', '%s Votes', $grouppoll->vote_count), $this->locale()->toNumber($grouppoll->vote_count)) ?>
	    	</div>
	  	</div>
	  </li>
  <?php endforeach; ?>
</ul>