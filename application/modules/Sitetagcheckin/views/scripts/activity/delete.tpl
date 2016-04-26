<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class='global_form_popup'>
  <form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <div>
      <h3>
        <?php if (!empty($this->comment_id)): ?>
          <?php echo $this->translate("Delete Comment?") ?>
        <?php else: ?>
          <?php echo $this->translate("Delete Activity Item?") ?>
        <?php endif; ?>
      </h3>
      <p>
        <?php if (!empty($this->comment_id)): ?>
          <?php echo $this->translate("Are you sure that you want to delete this comment? This action cannot be undone.") ?>
        <?php else: ?>
          <?php echo $this->translate("Are you sure that you want to delete this activity item and all of its comments? This action cannot be undone.") ?>
        <?php endif; ?>
      </p>

      <p>&nbsp;</p>

      <p>
        <input type="hidden" name="action_id" value="<?php echo $this->action_id ?>"/>
        <?php if (!empty($this->comment_id)): ?>
          <input type="hidden" name="comment_id" value="<?php echo $this->comment_id ?>"/>
        <?php endif; ?>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo $this->translate(" or ") ?>
        <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>