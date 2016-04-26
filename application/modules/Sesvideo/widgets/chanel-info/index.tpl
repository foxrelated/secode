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
<div class='sesvideo_profile_fields sesbasic_clearfix'>
  <ul>
		<?php if($this->subject->category_id){ ?>
      <?php $category = Engine_Api::_()->getItem('sesvideo_category',$this->subject->category_id); ?>
       <?php if($category){ ?>
      	<li class="sesbasic_clearfix">
        	<span><?php echo $this->translate("Category"); ?>:</span>
          <span><a href="<?php echo $category->getHref(); ?>"><?php echo $category->category_name; ?></a>
          	<?php $subcategory = Engine_Api::_()->getItem('sesvideo_category',$this->subject->subcat_id); ?>
             <?php if($subcategory){ ?>
                &nbsp;&raquo;&nbsp;<a href="<?php echo $subcategory->getHref(); ?>"><?php echo $subcategory->category_name; ?></a>
            <?php $subsubcategory = Engine_Api::_()->getItem('sesvideo_category',$this->subject->subsubcat_id); ?>
             <?php if($subsubcategory){ ?>
                &nbsp;&raquo;&nbsp;<a href="<?php echo $subsubcategory->getHref(); ?>"><?php echo $subsubcategory->category_name; ?></a>
            <?php } ?>
          <?php } ?>
          </span>
        </li>
      <?php }          
     } ?>
		<li class="sesbasic_clearfix">
			<span><?php echo $this->translate("Tags"); ?>:</span>
			<span>
      	<?php foreach($this->chanelTags as $tag):
            if($tag->getTag()->text != ''){?>
  	          <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>,"<?php echo $tag->getTag()->text; ?>");'><?php echo $tag->getTag()->text ?></a>
	    <?php	 } 
          endforeach;  ?>
    	</span>
		</li>
    <?php if( !empty($this->subject->description) ): ?>
      <li class="sesbasic_clearfix">
        <span><?php echo $this->translate("Description"); ?>:</span>
        <span><?php echo nl2br($this->subject->description) ?></span>
      </li>
    <?php endif ?>
  </ul>
</div>
<div class="sesvideo_channel_info_fields">

</div>

<script type="application/javascript">
var tagAction = window.tagAction = function(tag,value){
	var url = "<?php echo $this->url(array('module' => 'sesvideo','action'=>'browse'), 'sesvideo_chanel', true) ?>?tag_id="+tag+'&tag_name='+value;
 window.location.href = url;
}
</script>