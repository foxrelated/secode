<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: likelist.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $paginater_vari = 0; if( !empty($this->user_obj)) {  $paginater_vari = $this->user_obj->getCurrentPageNumber(); }  ?>


<script type="text/javascript">
// Function for Searching.
 var likeMemberPage = <?php if(empty($this->no_result_msg)){ echo sprintf('%d', $paginater_vari); } else { echo 1; } ?>;
 var call_status = '<?php echo $this->call_status; ?>';
 var resource_id = '<?php echo $this->resource_id; ?>';// Resource Id which are send to controller in the 'pagination' & 'searching'.
 var resource_type = '<?php echo $this->resource_type; ?>';// Resource Type which are send to controller in the 'pagination' & 'searching'.
 var url = en4.core.baseUrl + 'sitelike/index/likelist';// URL where send ajax request.
function show_myfriend () {
  $('likes_popup_content').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/spinner.gif" /></center>';

    var request = new Request.HTML({
   'url' : url,
        'data' : {
          'format' : 'html',
          'resource_type' : resource_type,
          'resource_id' : resource_id,
          'call_status' : call_status,
          'search' : this.value,
          'is_ajax':1
        },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      document.getElementById('likes_popup_content').innerHTML = responseHTML;
      }
				});
									
        request.send();

    }

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
          'call_status' : call_status,
          'search' : this.value,
          'is_ajax':1
        },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      document.getElementById('likes_popup_content').innerHTML = responseHTML;
      }
				});
									
        request.send();

    });
  });

 // Code for 'Pagination' which decide that how many entry will show in popup.
 var paginateLikeMembers = function(page, call_status) {
    var search_value = $('like_members_search_input').value;
    if (search_value == '') {
      search_value = '';
    }


    var request = new Request.HTML({
			'url' : url,
      'data' : {
        'format' : 'html',
        'resource_type' : resource_type,
        'resource_id' : resource_id,
        'search' : search_value,
        'call_status' : call_status,
        'page' : page,
        'is_ajax':1
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      document.getElementById('likes_popup_content').innerHTML = responseHTML;
      }
				});
									
        request.send();

  }

 //Showing 'friend' which liked this content.
 var likedStatus = function(call_status) {

   var request = new Request.HTML({
           'url' : url,
      'data' : {
        'format' : 'html',
        'resource_type' : resource_type,
        'resource_id' : resource_id,
        'call_status' : call_status
      },
              onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
								document.getElementById('like_members_profile').getParent().innerHTML = responseHTML;
                
      
							}
				});
									
        request.send();
  }
</script>
</div>
<?php //THIS IS USE FOR SHOW FRIENDS WHO LIKE MY CONTENT .
include_once APPLICATION_PATH . '/application/modules/Sitelike/views/scripts/friend_mycontent_likelist.tpl'; ?>
