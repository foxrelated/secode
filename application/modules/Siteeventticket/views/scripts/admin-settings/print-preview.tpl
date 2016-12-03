<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print-preview.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<link href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css' ?>" type="text/css" rel="stylesheet">

<?php if($this->logo){
  $logo_or_title = $this->htmlImage($this->logo);
}else{
  $logo_or_title = $this->site_title;
}?>

<?php 

    $username = 'Mike Aurther';
    $email = 'buyer@email.com';
    $buyerTicketId = 676767;
    $ticketTitle = 'Ticket Title';
    
    $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $price = Zend_Registry::get('Zend_View')->locale()->toCurrency(99.99, $currency);    
    
    $free = '';

    $QR_code_image = '<img src="'.$this->googleQRCode($buyerTicketId).'">';
//    if(empty(DOMPDF_ENABLE_REMOTE)) {
//        $QR_code_image = '';
//    }     
    $placehoders = array("[Free]!","[user_name]", "[email]", "[buyer_ticket_id]", "[ticket_title]","[ticket_price]","[site_logo]", "[QR_code_image]");
    $commonValues   = array($free, $username, $email, $buyerTicketId, $ticketTitle, $price,$logo_or_title, $QR_code_image);

    $printContent = str_replace($placehoders, $commonValues, $this->bodyHTML);

    echo "<div class='global_form_popup settings'>".html_entity_decode($printContent)."</div>";

?>


 