<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: friend-request.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooVerticalScroll.js');?>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    $('notifications_main').addEvent('click', function(event){
        event.stop(); //Prevents the browser from following the link.
        friendRequestClick(event);
    });

  });
</script>

<div class="seocore-pulldown-header">
  <a class="ui-icon ui-icon-cog ui-link" id="" href="/members/settings/notifications/module/user"></a>				
  <span class="seocore-pulldown-heading"><?php echo $this->translate("Friend Requests") ?></span>
</div>
<div class='seocore_pulldown_item_list'>
  <?php if( $this->requests->getTotalItemCount() > 0 ): ?>  
  <div id="sitemenu_friend_request_main_right_content_area">
    <div id="sitemenu_friend_request_scroll_main_right_content" class="sitemenu_scroll_content">
      <ul class='requests'>
          <?php foreach( $this->requests as $notification ): ?>
          <?php
            try {
              $getTempHandler = $notification->getTypeInfo()->handler;
              if(empty ($this->showSuggestion) ){
                $getTempHandler = 'user.friends.request-friend';
              }
              $parts = explode('.', $getTempHandler);
              echo $this->action($parts[2], $parts[1], $parts[0], array('notification' => $notification));
            } catch( Exception $e ) {
              if( APPLICATION_ENV === 'development' ) {
                echo $e->__toString();
              }
              continue;
            }
          ?>
          <?php endforeach; ?>
      </ul>
    </div>
  </div>
   <?php else: ?>
  	<div class="sitemenu_pulldown_nocontent_msg">
        <?php echo $this->translate("You have no Friend Requests.") ?>
    </div>
    <?php endif; ?>
</div>
<?php if( $this->requests->getTotalItemCount() > 0 ): ?>
  <div class="seocore-pulldown-footer">
    <center><a href="<?php echo $this->url(array(), 'recent_activity', true) ?>" class="ui-link"><?php echo $this->translate("View All Requests") ?></a></center>
  </div>
<?php endif; ?>
<script type="text/javascript">
  en4.core.runonce.add(function(){
    new SEAOMooVerticalScroll('sitemenu_friend_request_main_right_content_area', 'sitemenu_friend_request_scroll_main_right_content', {});
  });
</script>