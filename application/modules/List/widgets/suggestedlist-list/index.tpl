<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php
  $this->headLink()
  	->prependStylesheet($this->layout()->staticBaseUrl .'application/modules/List/externals/styles/list_tooltip.css');
?>

<ul class="seaocore_sidebar_list jq-list_tooltip">
	<?php foreach ($this->suggestedlist as $list):?>
     <li>
	    <?php echo $this->htmlLink($list->getHref(), $this->itemPhoto($list, 'thumb.icon')) ?>
						
			<div class="suggestlist_tooltip" style="display:none;">
        <div class="suggestlist_tooltip_content_outer">
          <div class="suggestlist_tooltip_content_inner">
            <div class="suggestlist_tooltip_arrow">
              <img src='<?php echo  $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/tooltip_arrow_left.png' alt="" />
            </div>
            <div class='suggestlists_tooltip_info'>
              <div class="title">
	          			<?php echo $this->htmlLink($list->getHref(), $list->getTitle()) ?>
                <span>
				            <?php if ($list->featured == 1): ?>
					            <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
				            <?php endif; ?>
                </span>
                <span>
				            <?php if ($list->sponsored == 1): ?>
					            <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
			            <?php endif; ?>
                </span>
              </div>

	          		<?php if (($list->rating > 0) && $this->ratingShow): ?>
              <span class="clear" title="<?php echo $list->rating.$this->translate(' rating'); ?>">
				            <?php for ($x = 1; $x <= $list->rating; $x++): ?>
                <span class="rating_star_generic rating_star" ></span>
				            <?php endfor; ?>
				            <?php if ((round($list->rating) - $list->rating) > 0): ?>
                <span class="rating_star_generic rating_star_half" ></span>
				            <?php endif; ?>
              </span>
			          <?php endif; ?>

              <div class='suggestlists_tooltip_info_date' class="clear">
					      	<?php echo $this->timestamp(strtotime($list->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
                  <?php echo $this->htmlLink($list->getOwner()->getHref(), $list->getOwner()->getTitle()) ?>
              </div>
              <div class='suggestlists_tooltip_info_date'>
					      	<?php echo $this->translate(array('%s comment', '%s comments', $list->comment_count), $this->locale()->toNumber($list->comment_count)) ?>,
									<?php echo $this->translate(array('%s review', '%s reviews', $list->review_count), $this->locale()->toNumber($list->review_count)) ?>,
					        <?php echo $this->translate(array('%s view', '%s views', $list->view_count), $this->locale()->toNumber($list->view_count)) ?>,
                  <?php echo $this->translate(array('%s like', '%s likes', $list->like_count), $this->locale()->toNumber($list->like_count)) ?>
              </div>
              <div class='suggestlists_tooltip_info_date'>
                 <?php echo $this->fieldValueLoop($list, $this->fieldStructure) ?>
              </div>
            </div>
          </div>
        </div>
      </div>
	    
	    <div class='seaocore_sidebar_list_info'>
         <div class='seaocore_sidebar_list_title'>
				 <?php $list_title = strip_tags($list->title);
					$list_title = Engine_String::strlen($list_title) > 40 ? Engine_String::substr($list_title, 0, 40) . '..' : $list_title; ?>   
          <?php echo $this->htmlLink($list->getHref(),$list_title) ?>
				</div>
	    </div>
	     
    </li>
   <?php endforeach; ?>
</ul>

<script type="text/javascript">
  /* moo style */
  window.addEvent('domready',function() {
    //opacity / display fix
    $$('.suggestlist_tooltip').setStyles({
      opacity: 0,
      display: 'block'
    });
    //put the effect in place
    $$('.jq-list_tooltip li').each(function(el,i) {
      el.addEvents({
        'mouseenter': function() {
          el.getElement('div').fade('in');
        },
        'mouseleave': function() {
          el.getElement('div').fade('out');
        }
      });
    });

  });

</script>