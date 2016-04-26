 <?php  $action = $this->action; ?>
 <?php $object=$action->getObject();
    $remove_patern=' &rarr; '.$object->toString(array('class' => 'feed_item_username'))?>
<li class="View_More_Birthday_Feed_<?php echo $action->action_id ?>">
	<div class='feed_item_photo <?php echo 'Hide_' . $action->getSubject()->getType() . "_" . $action->getSubject()->getIdentity() ?>'>    
		<?php echo $this->htmlLink($action->getSubject()->getHref(), $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle())) ?>
 	</div>    
  <div class="feed_item_body">
     <span class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_posted' ) ?>">
      <?php echo str_replace($remove_patern,"",$action->getContent()); ?>
    </span>
    <?php // Icon, time since, action links ?>
      <?php  $canComment = ( $action->getTypeInfo()->commentable &&
            $this->viewer()->getIdentity() &&
            Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'));
      ?>
      <div class='feed_item_date feed_item_icon <?php echo 'activity_icon_'.$action->type ?>'>
       <ul>
         <?php if( $canComment ): ?>           
            <li class="feed_item_option_like">              	
              <?php echo $this->htmlLink($action->getObject()->getHref(), $this->translate('Like'), array()) ?>
              <span>&#183;</span>
            </li>
            <li class="feed_item_option_comment">               
              <?php echo $this->htmlLink($action->getObject()->getHref(), $this->translate('Comment'), array()) ?>
              <span>&#183;</span>
            </li>           
        	<?php endif; ?>   
          <li>
            <?php echo $this->timestamp($action->getTimeValue()) ?>
          </li>
         </ul>
      </div>
  </div> 
</li>
 