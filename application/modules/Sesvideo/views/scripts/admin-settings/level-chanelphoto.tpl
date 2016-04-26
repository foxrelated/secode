<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: level-chanelphoto.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/dismiss_message.tpl';?>

<script type="text/javascript">
  var fetchLevelSettings = function(level_id) {
    window.location.href = en4.core.baseUrl + 'admin/sesvideo/settings/level-chanelphoto/id/' + level_id;
    //alert(level_id);
  }
</script>

<div class='sesbasic-form sesbasic-categories-form'>
  <div>
    <?php if( count($this->subNavigation) ): ?>
    	<div class='sesbasic-admin-sub-tabs'>
    		<?php echo $this->navigation()->menu()->setContainer($this->subNavigation)->render();?>
    	</div>
    <?php endif; ?>
    <div class='settings sesbasic-form-cont sesbasic_admin_form'>
    	<?php echo $this->form->render($this) ?>
    </div>
  </div>
</div>