<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
?>
<?php if($this->editOverview){ ?>
	<?php 
   if($this->subject->overview)
   	$overviewtext = $this->translate("Change Overview");
   else
   	$overviewtext = $this->translate("Add Overview");
  ?>
  <div class="sesbasic_profile_tabs_top sesbasic_clearfix">
    <a href="<?php echo $this->url(array('chanel_id' =>$this->subject->chanel_id,'action'=>'overview'),'sesvideo_chanel'); ?>" class="smoothbox sesbasic_button fa fa-plus">
      <?php echo  $overviewtext; ?>
    </a>
  </div>
<?php } ?>
<div class="sesbasic_html_block">
	<?php if($this->subject->overview) {
  					echo $this->subject->overview;
  			}else{ ?>
       		<div class="tip">
            <span>
              <?php echo $this->translate("There are currently no Overview.");?>
            </span>
          </div>     
  <?php   } ?>
</div>
