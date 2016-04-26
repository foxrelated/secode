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
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php $randonNumber = $this->identity; ?>
<!--for IE support canvas-->
<!--[if lt IE 9]><script type="text/javascript" src="<?php echo $baseUrl; ?>application/modules/Sesbasic/externals/scripts/excanvas.js"></script><![endif]-->
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.tagcanvas.min.js'); ?>
<div class="sesbasic_cloud_widget sesbasic_clearfix">
  <div id="myCanvasContainer_<?php echo $randonNumber ?>" style="height:<?php echo $this->height;  ?>px">
   <canvas style="width:100%;height:100%;" id="myCanvas_<?php echo $randonNumber ?>">
    <ul>
      <?php foreach($this->paginator as $valueTags){ 
      		if($valueTags['text'] == '' || empty($valueTags['text'] ))
          	continue;
      ?>
      <li>
        <a href="<?php echo $this->url(array('module' =>'sesvideo','controller' => 'index', 'action' => 'browse'),'sesvideo_general',true).'?tag_id='.$valueTags['tag_id'].'&tag_name='.$valueTags['text']  ;?>"><?php echo $valueTags['text'] ?></a>
      </li>
      <?php } ?>
    </ul>
   </canvas>
  </div>
  <a href="<?php echo $this->url(array('action' => 'tags'),'sesvideo_general',true).'?type='.$this->type;?>" class="sesbasic_more_link clear"><?php echo $this->translate("See All Tags");?> &raquo;</a>
</div>
<script type="text/javascript">
window.addEvent('domready', function() {
   if( ! sesJqueryObject ('#myCanvas_<?php echo $randonNumber ?>').tagcanvas({
		textFont: 'Impact,"Arial Black",sans-serif',
		textColour: "<?php echo $this->color; ?>",
		textHeight: "<?php echo $this->textHeight; ?>",
		maxSpeed : 0.03,
		depth : 0.75,
		shape : 'sphere',
		shuffleTags : true,
		reverse : false,
		initial :  [0.1,-0.0],
		minSpeed:.1
   })) {
     // TagCanvas failed to load
     sesJqueryObject ('#myCanvasContainer_<?php echo $randonNumber ?>').hide();
   }
 });
 </script>