<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print-invoice.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket_print.css'); ?>

<?php

if (!empty($this->siteeventticket_view_no_permission)) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("You don't have permission to print the invoice of this order.") ?>
    </span>
  </div>
  <?php
  return;
endif;
if(empty($this->buyerRows)):?>
  <div class="tip">
    <span>
      <?php echo $this->translate("No buyer details available.") ?>
    </span>
  </div>
<?php endif;?>

<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css');
?>
<link href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css' ?>" type="text/css" rel="stylesheet" media="print and orientation: landscape">

<?php if($this->logo){
  $logo_or_title = $this->htmlImage($this->logo);
}else{
  $logo_or_title = $this->site_title;
}?>
<?php 
foreach($this->buyerRows as $buyer): 
  
if($buyer->first_name || $buyer->last_name){
  $username = $buyer->first_name." ".$buyer->last_name;
}else{
  $buyerObj = Engine_Api::_()->getItem('user', $this->orderObj->user_id);
  $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($buyerObj);
  if (!empty($aliasValues)) {
    $firstName = !empty($aliasValues['first_name']) ? $aliasValues['first_name'] : null;
    $lastName = !empty($aliasValues['last_name']) ? $aliasValues['last_name'] : null;
  }
  if($firstName || $lastName){
    $username = $firstName." ".$lastName;
  }else{
    $username = $buyerObj->displayname;
  }
}

$email = $buyer->email;
$buyerTicketId = $buyer->buyer_ticket_id;
$ticketTitle = $buyer->title;
$price = $buyer->price;

if($price > 0.00){
  $free = '';
}else{
  $free = $this->translate('Free!');
  $price = $this->translate('Free');
}

 $QR_code_image = '<img src="'.$this->googleQRCode($buyerTicketId).'">';

 $placehoders = array("[Free]!","[user_name]", "[email]", "[buyer_ticket_id]", "[ticket_title]","[ticket_price]","[site_logo]", "[QR_code_image]");
    $commonValues   = array($free, $username, $email, $buyerTicketId, $ticketTitle, $price,$logo_or_title, $QR_code_image);
    
$printContent = str_replace($placehoders, $commonValues, $this->bodyHTML);
    
echo '<div style="page-break-after: always;">'.$printContent.'</div>';?>

<?php endforeach;?>
 
<?php if(empty($this->generatePdf)): ?>
    <script type="text/javascript">
      window.print();  
    </script>
<?php endif; ?>