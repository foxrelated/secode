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
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/List/externals/styles/style_list.css');
?>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/scripts/_class.noobSlide.packed.js"></script>

<?php
  // Starting work for "Slide Show".
  $image_var = '';
  $image_text_var = '';
  $pane_var = '';
  $pagination_var = '';
  $thumbnail_var = '';
  $thumb_span_var = '';
  $title_link_var = '';
 
	$title_link_var = "new Element('h4').set('html',";
	if($this->show_link == 'true')
		$title_link_var .= "'<a href=".'"'."'+currentItem.link+'".'"'.">link</a>'";
	if($this->title == 'true')
		$title_link_var .= "+currentItem.title";
	$title_link_var .= ").inject(im_info);";

	$image_count = 1;

	foreach($this->show_slideshow_object as $type => $item)
	{
		$itemPhoto = $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile'));
		
    $content_info = $this->timestamp(strtotime($item->creation_date)).$this->translate(' - posted by ').$this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle());

    $content_info.='<p>';
    $content_info.=$this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)).', ';
		$content_info.=$this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)).', ';    
    $content_info.=$this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)).', ';    
    $content_info.=$this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count));
    $content_info.='</p>';

    $description = $item->body;
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item);
    $custom_fields = $this->fieldValueLoop($item, $fieldStructure);

    $content_link = $this->htmlLink($item->getHref(), $this->translate('View Listing &raquo;'), array('class' => 'featured_slideshow_view_link'));
		
		$image_var .= '<span>'. $itemPhoto .'</span>';
		$pane_var	.=	"<span>Pane ".($image_count+1)."</span>";
		$pagination_var	.=	"<span>".($image_count+1)."</span>";
		$thumbnail_var .= "<div>". $this->itemPhoto($item, 'thumb.icon') ."</div>";
		$thumb_span_var .= "<span></span>";
		$image_text_var .= "<div class='featured_slidebox'>";
		$image_text_var .= "<div class='featured_slidshow_img'>" . $itemPhoto . "</div>";
		
		if(!empty($content_info)) {
			$image_text_var .= "<div class='featured_slidshow_content'>";
		}
			if(!empty($item->title)) {
				$tmpBody = strip_tags($item->title);
			$item->title =  ( Engine_String::strlen($tmpBody) > 45 ? Engine_String::substr($tmpBody, 0, 45) . '..' : $tmpBody );

			$image_text_var .='<h5>'. $this->htmlLink($item->getHref(), $item->title).'</h5>' ;
		}
		
		if(!empty($content_link) ) {
			$image_text_var .= "<h3 style='display:none'><span>" . $image_count++  . '_caption_title:' . $item->title  . '_caption_link:' .$content_link.'</span>' .  "</h3>";
		}
		
		if(!empty($content_info)) {
			$image_text_var .= "<span class='featured_slidshow_info'>" . $content_info . "</span>";
		}
		
		if(!empty($description)) {
			$truncate_description = ( Engine_String::strlen($description) > 253 ? Engine_String::substr($description, 0,250) . '...' : $description );
			$image_text_var .= "<p>" . $truncate_description . " " . $this->htmlLink($item->getHref(), $this->translate('More &raquo;')). "</p>";
		}
		
		$image_text_var .= "</div></div>";
	}

	if(!empty($this->list_featured)) {
	?>

  <script type="text/javascript">
		window.addEvent('domready',function() {
			if (document.getElementsByClassName == undefined) {
				document.getElementsByClassName = function(className)
				{
					var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
					var allElements = document.getElementsByTagName("*");
					var results = [];

					var element;
					for (var i = 0; (element = allElements[i]) != null; i++) {
						var elementClass = element.className;
						if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
							results.push(element);
					}

					return results;
				}
			}

			var width=$('global_content').getElement(".featured_slideshow_wrapper").clientWidth;
			$('global_content').getElement(".featured_slideshow_mask").style.width= (width-10)+"px";
			var divElements=document.getElementsByClassName('featured_slidebox');   
			for(var i=0;i < divElements.length;i++)
				divElements[i].style.width= (width-10)+"px";

			var handles8_more = $$('#handles8_more span');
			var num_of_slidehsow = "<?php echo $this->num_of_slideshow; ?>";
			var nS8 = new noobSlide({
				box: $('list_featured_im_te_advanced_box'),
				items: $$('#list_featured_im_te_advanced_box h3'),
				size: (width-10),
				handles: $$('#handles8 span'),
				addButtons: {previous: $('list_featured_prev8'), stop: $('list_featured_stop8'), play: $('list_featured_play8'), next: $('list_featured_next8') },
				interval: 5000,
				fxOptions: {
					duration: 500,
					transition: '',
					wait: false
				},
				autoPlay: true,
				mode: 'horizontal',
				onWalk: function(currentItem,currentHandle){

					// Finding the current number of index.
					var current_index = this.items[this.currentIndex].innerHTML;
					var current_start_title_index = current_index.indexOf(">");
					var current_last_title_index = current_index.indexOf("</span>");
					// This variable containe "Index number" and "Title" and we are finding index.
					var current_title = current_index.slice(current_start_title_index + 1, current_last_title_index);
					// Find out the current index id.
					var current_index = current_title.indexOf("_");
					// "current_index" is the current index.
					current_index = current_title.substr(0, current_index);

					// Find out the caption title.
					var current_caption_title = current_title.indexOf("_caption_title:") + 15;
					var current_caption_link = current_title.indexOf("_caption_link:");
					// "current_caption_title" is the caption title.
					current_caption_title = current_title.slice(current_caption_title, current_caption_link);
					var caption_title = current_caption_title;
					// "current_caption_link" is the caption title.
					current_caption_link = current_title.slice(current_caption_link + 14);

					var caption_title_lenght = current_caption_title.length;
					if( caption_title_lenght > 30 )
					{
						current_caption_title = current_caption_title.substr(0, 30) + '..';
					}

					if( current_caption_title != null && current_caption_link!= null )
					{
						$('list_featured_caption').innerHTML =   current_caption_link;
					}
					else {
						$('list_featured_caption').innerHTML =  '';
					}

					$('list_featured_current_numbering').innerHTML =  current_index + '/' + num_of_slidehsow ;
				}
			});

			//more handle buttons
			nS8.addHandleButtons(handles8_more);
			//walk to item 3 witouth fx
			nS8.walk(0,false,true);
		});
	</script>
<?php } ?>

<div class="featured_slideshow_wrapper">
  <div class="featured_slideshow_mask">
    <div id="list_featured_im_te_advanced_box" class="featured_slideshow_advanced_box">
			<?php echo $image_text_var ?>
    </div>
  </div>

  <div class="featured_slideshow_option_bar">
  	<div>
	  	<p class="buttons">
	    	<span id="list_featured_prev8" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title="Previous" ></span>
	     	<span id="list_featured_stop8" class="featured_slideshow_controllers-stop featured_slideshow_controllers" title="Stop"></span>
	      <span id="list_featured_play8" class="featured_slideshow_controllers-play featured_slideshow_controllers" title="Play"></span>
	      <span id="list_featured_next8" class="featured_slideshow_controllers-next featured_slideshow_controllers" title="Next" ></span>
	    </p>
   	</div>
    <span id="list_featured_caption"></span>
    <span id="list_featured_current_numbering" class="featured_slideshow_pagination"></span>
  </div>
</div>  