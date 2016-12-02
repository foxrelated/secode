<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-comment.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  parent.$('comment-<?php echo $this->comment_id ?>').destroy();
  setTimeout(function()
  {
    parent.Smoothbox.close();
  }, 1000 );
</script>

<div class="global_form_popup_message">
  <?php echo $this->message ?>
</div>