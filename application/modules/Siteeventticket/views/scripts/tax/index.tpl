<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript"> 
  Asset.css('<?php echo $this->layout()->staticBaseUrl
	    . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'?>');
</script>
<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="siteevent_dashboard_content">
  <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
  <div class="siteevent_event_form">
    <div class="siteeventticket_manage_event">

      <h3 class="mbot10"><?php echo $this->translate('Default Tax') ?></h3>
      <p>
        <?php echo $this->translate('Enter the default Tax for all Tickets.'); ?>
      </p>
      <br />

      <ul class="form-errors" id="vat_invalid_rate_message" style="display: none">
        <li>
          <?php echo $this->translate("Please enter the Rate(%) value between 0 to 100."); ?>
        </li>                                   
      </ul>

      <ul class="form-errors" id="vat_mandatory_rate_message" style="display: none">
        <li>
          <?php echo $this->translate("Tax is mandatory for ticket creation, please enter the Rate greater than 0."); ?>
        </li>                                   
      </ul>

      <ul class="form-errors" id="tin_mandatory_message" style="display: none">
        <li>
          <?php echo $this->translate("Taxpayer identification number is mandatory for ticket creation, please enter your valid Tax ID No."); ?>
        </li>                                   
      </ul>

      <ul class="form-notices" id="vat_creation_success_message" style="display: none">
        <li>
          <?php echo $this->translate("Changes Successfully Saved."); ?>
        </li>                                   
      </ul>

      <div>
        <?php echo $this->form->render($this); ?>
      </div>

      <script>
        en4.core.runonce.add(function () {


          $("event_tax_form").removeEvents('submit').addEvent('submit', function (e) {
            e.stop();
            en4.core.request.send(new Request.JSON({
              url: en4.core.baseUrl + 'siteeventticket/tax/save-vat-detail/event_id/<?php echo $this->event_id ?>',
              method: 'POST',
              onRequest: function () {
                $('tax_loading_image-wrapper').style.display = 'block';
                $('tax_loading_image-wrapper').style.clear = 'none';
                $("vat_creation_success_message").style.display = 'none';
                $("vat_invalid_rate_message").style.display = 'none';
                $("vat_mandatory_rate_message").style.display = 'none';
                $("tin_mandatory_message").style.display = 'none';
              },
              data: {
                format: 'json',
                eventVatValues: $("event_tax_form").toQueryString()
              },
              onSuccess: function (responseJSON) {
                $('tax_loading_image-wrapper').style.display = 'none';

                if (responseJSON.VATinvalidRateMessage)
                  $("vat_invalid_rate_message").style.display = 'block';

                if (responseJSON.VATMandatoryMessage)
                  $("vat_mandatory_rate_message").style.display = 'block';

                if (responseJSON.TINMandatoryMessage)
                  $("tin_mandatory_message").style.display = 'block';

                if (responseJSON.VATSuccessMessage)
                  $("vat_creation_success_message").style.display = 'block';

              }
            })
                    );
          });
        });

        // DISPLAY OTHER ELEMENTS IF TAX ENABLED
        window.addEvent('domready', function () {
          showOtherElements();
        });

        function showOtherElements()
        {
          if ($('tax_rate-wrapper')) {
            if ($('is_tax_allow').checked) {
              $('tax_rate-wrapper').style.display = 'block';
              $('tax_id_no-wrapper').style.display = 'block';
            } else {
              $('tax_rate-wrapper').style.display = 'none';
              $('tax_id_no-wrapper').style.display = 'none';
            }
          }
        }
      </script>
    </div>
  </div>	
</div>	
</div>