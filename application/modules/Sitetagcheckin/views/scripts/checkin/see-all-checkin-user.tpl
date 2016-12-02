<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sse-all-checkin-user.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $paginater_vari = 0;
if (!empty($this->user_obj)) { $paginater_vari = $this->user_obj->getCurrentPageNumber(); } ?>
  
  <script type="text/javascript">
    var checkinMemberPage = <?php if (empty($this->no_result_msg)) { echo sprintf('%d', $paginater_vari); } else { echo 1;} ?>;
    var call_status = '<?php echo $this->call_status; ?>';
    var resource_id = '<?php echo $this->resource_id; ?>';
    var resource_type = '<?php echo $this->resource_type; ?>';
    var url = en4.core.baseUrl + 'sitetagcheckin/checkin/see-all-checkin-user';// URL where send ajax request.
    en4.core.runonce.add(function() {
      showSearchResult();
    });

    function showSearchResult() {
      document.getElementById('checkin_members_search_input').addEvent('keyup', function(e) {
      $('checkins_popup_content').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitetagcheckin/externals/images/spinner.gif" alt="" style="margin-top:10px;" /></center>';
      var request = new Request.HTML({
        'url' : url,
        'data' : {
          'format' : 'html',
          'resource_type' : resource_type,
          'resource_id' : resource_id,
          'call_status' : call_status,
          'search' : this.value,
          'is_ajax':1,
          'checkedin_see_all_heading': '<?php echo $this->checkedin_see_all_heading;?>'
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          document.getElementById('checkins_popup_content').innerHTML = responseHTML;
        }
      });				
      request.send();
      });
    }
 
    var paginateCheckinMembers = function(page, call_status) {
      var search_value = $('checkin_members_search_input').value;
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
          'is_ajax':1,
          'checkedin_item_count': '<?php echo $this->checkedin_item_count ?>',
          'checkedin_see_all_heading': '<?php echo $this->checkedin_see_all_heading;?>'
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          document.getElementById('checkins_popup_content').innerHTML = responseHTML;
        }
      });

      request.send();
    }

  var checkinStatus = function(call_status) {
    var request = new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'resource_type' : resource_type,
        'resource_id' : resource_id,
        'call_status' : call_status,
        'checkedin_see_all_heading': '<?php echo $this->checkedin_see_all_heading;?>'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('sitetagcheckin_members_profile').getParent().innerHTML = responseHTML;
        en4.core.runonce.trigger();
      }
    });
    request.send();
  }

</script>



<?php if (empty($this->is_ajax)) { ?>
  <a id="sitetagcheckin_members_profile" class="pabsolute;"></a>
  <div class="seaocore_members_popup">
    <div class="top">
    	<div class="heading">
    		<?php echo $this->checkedin_see_all_heading;?>
    	</div>
      <div class="seaocore_members_search_box">
        <div class="link">
          <a href="javascript:void(0);" class="<?php if ($this->call_status == 'public') { echo 'selected'; } ?>" id="show_all" onclick="checkinStatus('public');"><?php echo $this->translate('All '); ?>(<?php echo number_format($this->public_count); ?>)
          </a>
          <a href="javascript:void(0);" class="<?php if ($this->call_status == 'friend') {
      echo 'selected'; } ?>" onclick="checkinStatus('friend');"><?php echo $this->translate('Friends '); ?>(<?php echo number_format($this->friend_count); ?>)
          </a>
        </div>
        <div class="seaocore_members_search fright">
          <input id="checkin_members_search_input" type="text" value="<?php echo $this->search; ?>" onfocus="if(this.value=='')this.value='';" onblur="if(this.value=='')this.value='';"/>
        </div>
      </div>
    </div>
 <div class="seaocore_members_popup_content" id="checkins_popup_content">
<?php } ?>
<?php if (!empty($this->user_obj) && $this->user_obj->count() > 1): ?>
  <?php if ($this->user_obj->getCurrentPageNumber() > 1): ?>
    <div class="seaocore_members_popup_paging">
      <div id="user_sitetagcheckin_members_previous" class="paginator_previous">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
          'onclick' => 'paginateCheckinMembers(checkinMemberPage - 1, call_status)'
        ));
        ?>
      </div>
    </div>
  <?php endif; ?>
<?php endif; ?>
<?php
$count_user = count($this->user_obj);
if (!empty($count_user)) { ?>
  <?php foreach ($this->user_obj as $user_info) { ?>
    <div class="item_member">
      <div class="item_member_thumb">
        <?php echo $this->htmlLink($user_info->getHref(), $this->itemPhoto($user_info, 'thumb.icon', $user_info->getTitle()), array('class' => 'item_photo', 'target' => '_parent', 'title' => $user_info->getTitle())); ?>
      </div>
      <div class="item_member_details">
        <div class="item_member_name">
    <?php $title1 = $user_info->getTitle(); ?>
    <?php $truncatetitle = Engine_String::strlen($title1) > 20 ? Engine_String::substr($title1, 0, 20) . '..' : $title1 ?>
    <?php echo $this->htmlLink($user_info->getHref(), $truncatetitle, array('title' => $user_info->getTitle(), 'target' => '_parent')); ?>
        </div>
      </div>	
    </div>
  <?php }
} else { ?>
<div class='tip' style="margin:10px 0 0 140px;">
  <span>
    <?php
    echo $this->no_result_msg;
    ?>
  </span>
</div>
<?php } ?>
<?php if (!empty($this->user_obj) && $this->user_obj->count() > 1): ?>
  <?php if ($this->user_obj->getCurrentPageNumber() < $this->user_obj->count()): ?>
    <div class="seaocore_members_popup_paging">
      <div id="user_sitetagcheckin_members_next" class="paginator_next" style="border-top-width:1px;">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
            'onclick' => 'paginateCheckinMembers(checkinMemberPage + 1, call_status)'
        ));
        ?>
      </div>
    </div>
  <?php endif; ?>
<?php endif; ?>
<?php if (empty($this->is_ajax)) { ?>
    </div>
  </div>
  <div class="seaocore_members_popup_bottom">
    <button onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
  </div>
<?php } ?>