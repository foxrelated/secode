<div id="formborder" class="ynaffiliate_dynamic_links">
    <div class="yntable noborder">
        
        <div class="ynaffiliate_dynamic_links_title">
            <?php echo $this->translate('Destination URL:') ?>
        </div>
        
        <div class="ynaffiliate_dynamic_links_row">
            <span class="ynaffiliate_label"><?php echo $this->translate('Please enter links within this domain:') ?></span>
            <input id = "dynamic_target_link" onkeyup="javascript:getAffiliateLink($('dynamic_target_link').value)" class="ynaffiliate_text" type="text" name="lname" />
        </div>

        <div class="ynaffiliate_dynamic_links_row">
            <span class="ynaffiliate_label"><?php echo $this->translate('Referred URL:') ?></span>
            <input id = "dynamic_affiliate_link" onclick="javascript:SelectAll($(this));" class="ynaffiliate_text "type="text" name="lname" />
        </div>

        
        <div class="tip" id ="error_message" style="display: none">
            <span id = "ynaff_error_message">
                <?php echo $this->translate('The Url is not valid!'); ?>
            </span>
        </div>

        <div class="yntable-item">
            <?php echo $this->translate('You can copy and share the Referred URL with others'); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
   function getAffiliateLink(target_link) {
      if (target_link != '') {
         var request = new Request.JSON(
         {
            'format' : 'json',
            'url' : '<?php echo $this->url(array('controller' => 'sources', 'action' => 'get-affiliate-link'), 'ynaffiliate_extended', true) ?>',
            'data' : {
               'target_link' : target_link
            },
            'onSuccess' : function(response) {
               if (response.error == 1) {
                  $('error_message').setStyle('display','block');
                  $('ynaff_error_message').set('text', response.text);
                  $('dynamic_affiliate_link').value = '';
               }
               else {
                  if ($('error_message').getStyle('display') == 'block') {
                     $('error_message').setStyle('display','none');
                  }
                  $('dynamic_affiliate_link').value = response.affiliate_url;
               }
            }
         });
         request.send();
      }
   }
   function SelectAll(id)
   {
      id.focus();
      id.select();
   }
</script>