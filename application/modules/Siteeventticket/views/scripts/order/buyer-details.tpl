<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: buyer-details.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/scripts/core.js');

$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css');
?>

<script type="text/javascript">
    var noconfirmation = 0;
</script>    

<!--Your information form-->
<section class="seao_buyer_details fleft">
	<div class="siteevent_viewevents_head">
    <?php echo $this->htmlLink($this->siteevent->getHref(), $this->itemPhoto($this->siteevent, 'thumb.icon', '', array('align' => 'left'))) ?>
    <h2>
    	<?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle()) ?>
    </h2>
  </div>	
  <div class="seao_buyer_info o_hidden">
    <h3><?php echo $this->translate("Your Information"); ?></h3>
    <div>
    	<div>
        <?php
        $buyerDetailArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.buyer.details', array('fname', 'lname', 'email'));
        $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($this->viewer);
            if (!empty($aliasValues)) {
              $firstName = !empty($aliasValues['first_name']) ? $aliasValues['first_name'] : null;
              $lastName = !empty($aliasValues['last_name']) ? $aliasValues['last_name'] : null;
            }
        if (in_array('fname', $buyerDetailArray)):
          ?>
          <div class="seao_buyer_details_col">
            <label class="seao_label"><?php echo $this->translate("First Name"); ?></label>
            <input type="text" id="user_fname" name="user_fname" value="<?php echo $firstName;?>"/>
          </div> 
        <?php endif; ?>
        <?php if (in_array('lname', $buyerDetailArray)): ?>
          <div class="seao_buyer_details_col">
            <label class="seao_label"><?php echo $this->translate("Last Name"); ?></label>
            <input type="text"  id="user_lname" name="user_lname" value="<?php echo $lastName;?>"/>
          </div> 
        <?php endif; ?>
      </div>
      <div>
        <?php if (in_array('email', $buyerDetailArray)): ?>
          <div class="seao_buyer_details_col">
            <label class="seao_label"><?php echo $this->translate("Email"); ?></label>
            <input type="email"  id="user_email" name="user_email" value="<?php echo $this->viewer->email; ?>"/>
          </div>
        <?php endif; ?>
        <?php //if (in_array('contact', $buyerDetailArray)): ?>
<!--          <div class="seao_buyer_details_col">
            <label class="seao_label"><?php echo $this->translate("Contact No."); ?></label>
            <input type="text"  id="user_contact" name="user_contact"/>
          </div>-->
        <?php //endif; ?>
      </div>
  	</div>
  </div>

  <div class="seao_ticket_holder">
  	<h3><?php echo $this->translate("Ticket Holders"); ?></h3>
  	<p><?php echo $this->translate("Provide the owner information for each ticket"); ?></p>
    <form method="post" id ="buyer_details_form" action="<?php echo $this->url(array('action' => 'checkout', 'event_id' => $this->event_id, 'buyer_details' => true), "siteeventticket_order", true); ?>">
      <div class="seao_copy_ticket_info">  
        <input type="checkbox" onclick="copyDetails()"  id="isCopiedDetails" name="isCopiedDetails"><label for="isCopiedDetails"><?php echo $this->translate("Copy purchaser's information for all tickets.") ?></label>
      </div>
    	
      <ul>
        <?php
        $k = 0;$ticketsDetail = $this->formValues;
        foreach ($ticketsDetail['ticket_column'] as $key => $row):
          $ticket_id = $key;
        
          $ticketObj = Engine_Api::_()->getItem('siteeventticket_ticket', $ticket_id);
          if (isset($row[1]) && $row[1] > 0):
            $price = $row[0];
            $quantity = $row[1];
        
            for ($i = 1; $i <= $quantity; $i++):
              ?>
              <li class="buyer_detail_subform" id="buyer_detail_subform[<?php echo $k ?>]">
                <div class="seao_ticket_heading">
                  <span class="fleft"><?php echo $this->translate("Ticket"). " " . $i . " - " . $ticketObj->title; ?></span>
<!--                  <span class="fright">
                  <?php
                    if ($price > '0.00'): echo $this->locale()->toCurrency($price, $currency);
                    else: echo $this->translate("Free");
                    endif;
                    ?>
                  </span>-->
                </div>
                <div>
                	<div class="clr">
                    <?php
                    if (in_array('fname', $buyerDetailArray)):
                      ?>
                      <div class="seao_buyer_details_col">
                        <label class="seao_label"><?php echo $this->translate("First Name"); ?></label>
                        <input type="text" id="buyer_detail[<?php echo $ticket_id ?>][<?php echo $i ?>][fname]" name="buyer_detail[<?php echo $ticket_id ?>][<?php echo $i ?>][fname]" class="buyer_detail_fname" <?php if($k=='0'):?>value="<?php echo $firstName;?>"<?php endif;?>/>
                        <span style="display:none;color:red;" class="buyer_detail_fname_error f_small"><?php echo $this->translate("*Please enter the valid name.");?></span>
                      </div> 
                    <?php endif; ?>
                    <?php if (in_array('lname', $buyerDetailArray)): ?>
                      <div class="seao_buyer_details_col">
                        <label class="seao_label"><?php echo $this->translate("Last Name"); ?></label>
                        <input type="text"  id="buyer_detail[<?php echo $ticket_id ?>][<?php echo $i ?>][lname]" name="buyer_detail[<?php echo $ticket_id ?>][<?php echo $i ?>][lname]" class="buyer_detail_lname" <?php if($k=='0'):?>value="<?php echo $lastName;?>"<?php endif;?>/>
                        <span style="display:none;color:red;" class="buyer_detail_lname_error f_small"><?php echo $this->translate("*Please enter the valid name.");?></span>
                      </div> 
                    <?php endif; ?>
                  </div>
                  <div class="clr">
                    <?php if (in_array('email', $buyerDetailArray)): ?>
                      <div class="seao_buyer_details_col">
                        <label class="seao_label"><?php echo $this->translate("Email"); ?></label>
                        <input type="email" id="buyer_detail[<?php echo $ticket_id ?>][<?php echo $i ?>][email]" name="buyer_detail[<?php echo $ticket_id ?>][<?php echo $i ?>][email]" class="buyer_detail_email" <?php if($k=='0'):?>value="<?php echo $this->viewer->email;?>"<?php endif;?>/>
                        <span style="display:none;color:red;" class="buyer_detail_email_error f_small"><?php echo $this->translate("*Please enter the valid email address.");?></span>
                      </div>
                    <?php endif; ?>
                    <?php //if (in_array('contact', $buyerDetailArray)): ?>
<!--                      <div class="seao_buyer_details_col">
                        <label class="seao_label"><?php echo $this->translate("Contact No."); ?></label>
                        <input type="text"  id="buyer_detail[<?php echo $ticket_id ?>][<?php echo $i ?>][contact]" name="buyer_detail[<?php echo $ticket_id ?>][<?php echo $i ?>][contact]" class="buyer_detail_contact" />
                      </div>-->
              			<?php //endif; ?>
                  </div>
                </div>
              </li>
              <?php
              $k++;
            endfor;
          endif;
          ?>
        <?php endforeach; ?>
      </ul>
      <button type="button" onclick=" noconfirmation = 1; validateBuyerInformation()">
    <?php echo $this->translate("Continue"); ?>
      </button>
    </form>
  </div>
</section>

<aside id="order_summary_block" class="seao_order_summary fleft">
  <div>
    <h3 class="fleft"><?php echo $this->translate("Order Summary"); ?></b></h3>
    <p class="fright">
    	<a class="changeticket_icon" href="<?php echo $this->url(array("action" => "buy", 'event_id' => $this->event_id), "siteeventticket_ticket", true) ?>" title="<?php echo $this->translate("Change my tickets"); ?>"><i></i></a>
   	</p>
    <ul>
      <?php
      $ticketsDetail = $this->formValues;
      $totalOrderPrice = $ticketsDetail['grandtotal'];
      foreach ($ticketsDetail['ticket_column'] as $key => $row):
        $ticket_id = $key;
    
        $ticketObj = Engine_Api::_()->getItem('siteeventticket_ticket', $ticket_id);
        if (isset($row[1]) && $row[1] > 0):
          $price = $row[0];
          $quantity = $row[1];
          ?>
          <li>
            <div class="seao_info_col_1 seaocore_txt_light"><span><?php echo $quantity; ?></span></div>
            <div class="seao_info_col_2 seaocore_txt_light">
              <span><?php echo $this->translate($ticketObj->title); ?></span>
              <span>
                <?php if ($price > 0):echo $this->locale()->toCurrency($price, $currency);
                 else: echo $this->translate('(Free)');
                 endif; ?>
              </span>
            </div>
            <div class="seao_info_col_3 seaocore_txt_light"><span><?php echo $this->locale()->toCurrency($price * $quantity, $currency);?></span></div>
          </li> 
        <?php endif; ?>
        <?php endforeach; ?>
        <?php if(isset($this->formValues['discounttotal'])&& !empty($this->formValues['discounttotal']) && $this->formValues['discounttotal'] > 0.00): ?>  
            <li class="seao_order_summary_total">
              <div class="seaocore_txt_light"><?php echo $this->translate("Discount Total"); ?></div>
              <div class="seaocore_txt_light"><?php echo $this->locale()->toCurrency($this->formValues['discounttotal'], $currency); ?></div>
            </li>
       <?php endif; ?>           
        <?php if($this->tax_rate && $totalOrderPrice > 0): ?>  
            <li class="seao_order_summary_total">
              <div class="seaocore_txt_light"><?php echo $this->translate("Tax"); ?></div>
              <div class="seaocore_txt_light"><?php echo $this->locale()->toCurrency($ticketsDetail['tax'], $currency); ?></div>
            </li>
       <?php endif; ?>           
      <li class="seao_order_summary_total bold">
        <div><?php echo $this->translate("Grand Total"); ?></div>
        <div><?php echo $this->locale()->toCurrency($totalOrderPrice, $currency); ?></div>
      </li>
    </ul>
  </div>
</aside>
<script type="text/javascript">
window.addEvent('scroll', function() {
  var el = $('order_summary_block'), topElement = el.getParent();
        var elementPostionY = 0;
        if (typeof(topElement.offsetParent) != 'undefined') {
          elementPostionY = topElement.offsetTop;
        } else {
          elementPostionY = topElement.y;
        }
  
if(elementPostionY < window.getScrollTop()){
  el.addClass("position_fixed");
} else if (el.hasClass('position_fixed')){
 el.removeClass("position_fixed");
}
      
});

window.onbeforeunload = function() {
    if(noconfirmation == 0)
        return '';
};
</script>
