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
<div class="sesbasic_tags sesbasic_clearfix sesbasic_bxs">
  <?php foreach($this->tagCloudData as $valueTags){ 
  if($valueTags['text'] == '' && empty($valueTags['text']))
  continue;
  ?>
  <?php if($this->type == 'video') :?>
    <a href="<?php echo $this->url(array('module' =>'sesvideo','controller' => 'index', 'action' => 'browse'),'sesvideo_general',true).'?tag_id='.$valueTags['tag_id'].'&tag_name='.$valueTags['text']  ;?>">
    <?php else: ?>
    <a href="<?php echo $this->url(array('module' =>'sesvideo','controller' => 'chanel', 'action' => 'browse'),'sesvideo_chanel',true).'?tag_id='.$valueTags['tag_id'].'&tag_name='.$valueTags['text']  ;?>">
      <?php endif; ?>
      <b><?php echo $valueTags['text'] ?></b><sup><?php echo $valueTags['itemCount']; ?></sup></a>
    <?php } ?>
</div>