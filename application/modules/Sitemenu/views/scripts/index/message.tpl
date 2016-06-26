<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: message.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<div class="seocore-pulldown-header">
    <a title="<?php echo $this->translate("Compose Message") ?>" class="seocore-pulldown-icon" id="" href="<?php echo $this->url(array('action' => 'compose'), 'messages_general') ?>"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemenu/externals/images/new-message.png" style="height:16px;float:right;" /></a>				
    <span class="seocore-pulldown-heading"><?php echo $this->translate('Messages');?></span>
</div>
  
<?php if( count($this->paginator) ): ?>
    
  <div class="seocore_pulldown_item_list">
  <div id="sitemenu_message_main_right_content_area">
    <div id="sitemenu_message_scroll_main_right_content" class="sitemenu_scroll_content">
      <ul>
      <?php foreach( $this->paginator as $conversation ): 
        $message = $conversation->getInboxMessage($this->viewer());
        $recipient = $conversation->getRecipientInfo($this->viewer());
        $resource = "";
        $sender   = "";
        if( $conversation->hasResource() &&
                  ($resource = $conversation->getResource()) ) {
          $sender = $resource;
        } else if( $conversation->recipients > 1 ) {
          $sender = $this->viewer();
        } else {
          foreach( $conversation->getRecipients() as $tmpUser ) {
            if( $tmpUser->getIdentity() != $this->viewer()->getIdentity() ) {
              $sender = $tmpUser;
            }
          }
        }
        if( (!isset($sender) || !$sender) && $this->viewer()->getIdentity() !== $conversation->user_id ){
          $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
        }
        if( !isset($sender) || !$sender ) {
          //continue;
          $sender = new User_Model_User(array());
        }
        ?>
        <li<?php if( !$recipient->inbox_read ): ?> class='seocore_pulldown_item_list_new'<?php endif; ?> id="message_conversation_<?php echo $conversation->conversation_id ?>" >
          <div onclick="messageConversation('<?php echo $conversation->getHref(); ?>');">
            <div class="seocore_pulldown_item_list_photo">
              <?php echo $this->htmlLink($sender->getHref(), $this->itemPhoto($sender, 'thumb.icon')) ?>
            </div>
            <div class="seocore_pulldown_item_list_from">
              <p class="seocore_pulldown_item_list_from_name">
                <?php if( !empty($resource) ): ?>
                  <?php echo $resource->toString() ?>
                <?php elseif( $conversation->recipients == 1 ): ?>
                  <?php echo $this->htmlLink($sender->getHref(), $sender->getTitle()) ?>
                <?php else: ?>
                  <?php echo $this->translate(array('%s person', '%s people', $conversation->recipients),
                      $this->locale()->toNumber($conversation->recipients)) ?>
                <?php endif; ?>
              </p>
              <p class="seocore_pulldown_item_list_info">
                <div style="display:inline-block">
                  <span class="seocore_pulldown_item_list_info_title">
                    <?php
                      ! ( isset($message) && '' != ($title = trim($message->getTitle())) ||
                      ! isset($conversation) && '' != ($title = trim($conversation->getTitle())) ||
                      $title = '<em>' . $this->translate('(No Subject)') . '</em>' );
                    ?>
                    <?php echo $this->htmlLink($conversation->getHref(), $title) ?></span>:&nbsp;
                <span class="seocore_pulldown_item_list_info_body">
                  <?php echo  Engine_Api::_()->seaocore()->seaocoreTruncateText(html_entity_decode($message->body), 25) ?>
                </span>
                </div>
            </p>
            
              <p class="seocore_pulldown_item_list_from_date f_small seaocore_txt_light">
                <?php echo $this->timestamp($message->date) ?>
              </p>
              </div>
          </div>	
          <div class="fright"><span id="sitemenu_message_icon_<?php echo $conversation->conversation_id ?>" onmouseover="advancedMenuAddMessageIconHtml(<?php echo $conversation->conversation_id ?>);" onmouseout="advancedMenuRemoveMessageIconHtml(<?php echo $conversation->conversation_id ?>);" onclick="advancedMenuMarkMessageReadUnread(<?php echo $conversation->conversation_id ?>)" class="seocore_message_icon"></span></div>
        </li>
      <?php endforeach; ?>
    </ul>
    </div>
  </div>
  </div>
  
  <div class="seocore-pulldown-footer">
    <center>
      <a href="<?php echo $this->url(array('action' => 'inbox'), 'messages_general', true) ?>" class="ui-link"><?php echo $this->translate("View All Messages") ?></a>
    </center>
  </div>
<?php endif; ?>

<?php if( $this->paginator->getTotalItemCount() <= 0 ): ?>
  <div class="tip m10">
    <span>
      <?php echo $this->translate('Tip: %1$sClick here%2$s to send your first message!', "<a href='".$this->url(array('action' => 'compose'), 'messages_general')."'>", '</a>'); ?>
    </span>
  </div>
<?php endif; ?>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    new SEAOMooVerticalScroll('sitemenu_message_main_right_content_area', 'sitemenu_message_scroll_main_right_content', {});
  });
</script>