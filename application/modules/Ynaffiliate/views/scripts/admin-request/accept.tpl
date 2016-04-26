<h2><?php echo $this->translate("Affiliate Plugin") ?></h2>
<!-- admin menu -->
<?php echo $this->content()->renderWidget('ynaffiliate.admin-main-menu') ?>
<div class="clear">
   <div class="settings">
      <form enctype="application/x-www-form-urlencoded" class="global_form" action="<?php echo $this->formUrl ?>" method="post">
         <div>
            <h3><?php echo $this->translate("Send Money")?></h3>
            <p class="form-description">
            </p>
            <div class="form-elements">
               <div id="store_currency-wrapper">
                  <div class="form-wrapper">
                     <div class="form-label">
                        <label class="optional"><?php echo $this->translate('Request Amount:') ?></label>
                     </div>
                     <div id="store_product_rate-element" class="form-element">
                        <?php echo $this->locale()->toNumber(round($this->request->request_amount, 2)) . ' ' . $this->currency; ?>
                     </div>
                  </div>
                  <div class="form-wrapper">
                     <div class="form-label">
                        <label class="optional"><?php echo $this->translate('Account Request:') ?></label>
                     </div>
                     <div id="store_product_rate-element" class="form-element">
                        <?php echo $this->account_email ?>
                     </div>
                  </div>
                  <div class="form-wrapper">
                     <div class="form-label">
                        <label class="optional"><?php echo $this->translate('Message Response:') ?></label>
                     </div>
                     <div id="store_product_rate-element" class="form-element">
                        <textarea id="description" name="description"></textarea>
                     </div>
                  </div>
                  <input type="hidden" name="no_shipping" value="1"/>
                  <input TYPE="hidden" name="cmd" VALUE="_xclick">
                  <input TYPE="hidden" name="business" VALUE=" <?php echo $this->account_email; ?>">
                  <input TYPE="hidden" name="amount" VALUE="<?php echo $this->request->request_amount; ?>">
                  <input TYPE="hidden" name="currency_code" VALUE="<?php echo $this->currency; ?>">
                  <input type="hidden" name="notify_url" value="<?php echo $this->notifyUrl; ?>"/>
                  <input type="hidden" name="return" value="<?php echo $this->returnUrl ?>"/>
                  <input type="hidden" name="cancel_return" value="<?php echo $this->cancelUrl ?>"/>
                  <div class="form-wrapper">
                     <div id="submit-label" class="form-label">
                        &nbsp;
                     </div>
                     <div id="submit-element" class="form-element">
                        <button name="submit" id="submit" type="submit" onclick="saveMessage()">
                           <?php echo $this->translate("Send Money") ?>
                        </button> or <a href="<?php echo $this->url(array('action' => 'index')) ?>"><?php echo $this->translate("cancel") ?></a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </div>
</div>
<script type="text/javascript">
	var saveMessage = function()
	{
		var message = $('description').value;
		var id = <?php echo $this->request-> getIdentity()?>;
		var request = new Request.JSON(
         {
            'format' : 'json',
            'url' : '<?php echo $this->url(array('controller' => 'admin-request', 'action' => 'save-response-message'), 'ynaffiliate_extended', true) ?>',
            'data' : {
               'id' : id,
               'message': message
            },
            'onSuccess' : function(response) {
            }
         });
         request.send();
	}
</script>