 <?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: myfriendlikes.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<script type="text/javascript">
var url = en4.core.baseUrl + 'sitelike/index/myfriendlikes';// URL where send ajax request.
var resource_id = '<?php echo $this->resource_id; ?>';// Resource Id which are send to controller in the 'pagination' & 'searching'.
 var resource_type = '<?php echo $this->resource_type; ?>';// Resource Type which are send to controller in the 'pagination' & 'searching'.


 en4.core.runonce.add(function() {
    // Code for 'searching', where send the request and set the result which are return.
    document.getElementById('like_members_search_input').addEvent('keyup', function(e) {
    $('likes_popup_content').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/spinner.gif" alt="" style="margin-top:10px;" /></center>';


var request = new Request.HTML({
           'url' : url,
        'data' : {
          'format' : 'html',
          'resource_type' : resource_type,
          'resource_id' : resource_id,
          'search' : this.value,
          'is_ajax':1
        },
              onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
								document.getElementById('like').getParent().innerHTML = responseHTML;
                en4.core.runonce.trigger();
      
							}
				});
									
        request.send();
  });

   });
</script>
</div>
<a id='like' class="pabsolute"></a>
<div id="likes_members_popup" class="seaocore_members_popup">
  <div class="top">
		<div class="heading">
		  <?php
					$mixsettingstable = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' );
					$sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?'=> $this->resource_type));
					echo $this->translate("Friends' Who Like This " . $sub_status_select->title_items );
		  ?>
			<div class="seaocore_members_search fright">
				<input id="like_members_search_input" type="text" value="<?php echo $this->search; ?>" onfocus="if(this.value=='')this.value='';" onblur="if(this.value=='')this.value='';"/>
			</div>
		</div>
	</div>  

  <div style="height: 285px;" id="likes_popup_content" class="seaocore_members_popup_content">
	  <?php $count_user = count($this->user_obj);
	        if(!empty($count_user)) { ?>
    	<?php foreach($this->user_obj as $item ):?>
	      <div class="item_member">
	        <div class="item_member_thumb">
	          <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon'), array('title'=>$item->getTitle(), 'target' => '_parent', 'class' => 'seao_common_add_tooltip_link', 'rel'=> 'user'.' '.$item->getIdentity())) ?>
	        </div>
	        <div class="item_member_details">
		        <div class="item_member_name">
		          <?php  $title1 = $item->getTitle(); ?>
		          <?php  $truncatetitle = Engine_String::strlen($title1) > 20 ? Engine_String::substr($title1, 0, 20) . '..' : $title1?>
		          <?php echo $this->htmlLink($item->getHref(), $truncatetitle, array('title' => $item->getTitle(), 'target' => '_parent', 'class' => 'seao_common_add_tooltip_link', 'rel'=> 'user'.' '.$item->getIdentity())); ?>
		        </div>
		      </div>  
	      </div>
	    <?php endforeach; ?>
    <?php } else { ?>
      <div class='tip' style="margin:10px 0 0 140px;">
      	<span>
	        <?php
	          echo $this->no_result_msg;
	        ?>
        </span>
      </div>
    <?php } ?>
  </div>
</div>
<div class="seaocore_members_popup_bottom">
	<button onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
</div>	