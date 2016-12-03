/* $Id: core.js 2015-05-11 00:00:00Z SocialEngineAddOns Copyright 2015-2016 BigStep Technologies Pvt. Ltd. $ */

function calculateGrandTotal(tax_rate, currencyValue, localeValue, couponReset) { 

  var total_tax = 0;
  var subtotal = 0;
  var totalCouponDiscount = 0;
  var currentRowElement = null;
  
  if(couponReset && $('dynamic_discounttotal') && $('coupon_code_value')) {
      var numberValue = 0;
      $('dynamic_discounttotal').innerHTML = parseFloat(numberValue).toLocaleString(localeValue, { style: 'currency', currency: currencyValue });
      $('coupon_code_value').value = '';
      $('coupon_error_msg').innerHTML = '';
      $('coupon_success_msg').innerHTML = '';
      
        $$('.ticket_dropdown').each(function (elem) {
          $('ticket_detail_' + elem.get('data-ticket_id')).set('value', '');
          $('ticket_detail_' + elem.get('data-ticket_id')).set('data-couponId', '');
          $('ticket_detail_' + elem.get('data-ticket_id')).set('data-couponDiscountType', '');
          $('ticket_detail_' + elem.get('data-ticket_id')).set('data-couponDiscountAmount', '');
        });      
  }  
  
  $$('.ticket_dropdown').each(function (elem) {
    currentRowElement = $('ticket_detail_' + elem.get('data-ticket_id'));

    if (currentRowElement.get('data-couponDiscounttype') == 1) {

      if ($(elem).value > 0) {
        totalCouponDiscount = totalCouponDiscount + currentRowElement.get('data-couponDiscountAmount');
      }
    } else {
      totalCouponDiscount = totalCouponDiscount + (($(elem).get('data-price') * $(elem).value) * currentRowElement.get('data-couponDiscountAmount')) / 100;
    }
    subtotal = subtotal + ($(elem).get('data-price') * $(elem).value);
  });

  subtotal = parseFloat(subtotal).toFixed(2);
  totalCouponDiscount = parseFloat(totalCouponDiscount).toFixed(2);
  $('dynamic_subtotal').innerHTML = parseFloat(subtotal).toLocaleString(localeValue, { style: 'currency', currency: currencyValue });
  $('subtotal').value = subtotal;
  subtotal = parseFloat(subtotal - totalCouponDiscount).toFixed(2);

  if (tax_rate) {
    total_tax = parseFloat(subtotal * (tax_rate / 100)).toFixed(2);
    $('dynamic_tax').innerHTML = parseFloat(total_tax).toLocaleString(localeValue, { style: 'currency', currency: currencyValue });
    $('tax').value = total_tax;
  }
  
  var grandtotal = (parseFloat(subtotal) + parseFloat(total_tax)).toFixed(2);

  $('dynamic_grandtotal').innerHTML = parseFloat(grandtotal).toLocaleString(localeValue, { style: 'currency', currency: currencyValue });
  $('grandtotal').value = grandtotal;
  
  if($('dynamic_discounttotal')) {
    $('dynamic_discounttotal').innerHTML = parseFloat(totalCouponDiscount).toLocaleString(localeValue, { style: 'currency', currency: currencyValue });
  }
  
  if($('discounttotal')) {
    $('discounttotal').value = totalCouponDiscount;
  }
  
  if(capacityApplicable != 0 && ticket_for_sale != 0) {
    checkTicketAvailability(current_occurrence_id);
  }
}

//JS FUNCTION TO CHECK IF NO TICKET SELECTED (0 IN ALL DROPDOWNS)
function validateTicketInformation() {
  var ticket_quantity = '0';
  $$('.ticket_dropdown').each(function (elem) {
    if ($(elem).value > '0') {
      ticket_quantity = '1';
      //break;
    }

  });
  //If viewer not select any payment method then show error message.
  if (ticket_quantity === '0')
  {
    $('no_ticket_selected').innerHTML = en4.core.language.translate("Please select at least one ticket to proceed.");
    return;
  }

  document.getElementById("buy_tickets_form").submit();
}

function checkTicketAvailability(occurrence_id) {

    if($('orderNowButton')) {
        $('orderNowButton').setStyle('display', 'none');
    }

    if($('eventIsFull')) {
        $('eventIsFull').setStyle('display', 'none');
    }    

    var totalTicketsInCart = 0;
    $$('.ticket_dropdown').each(function (elem) {
      totalTicketsInCart = parseFloat(totalTicketsInCart) + parseFloat($(elem).value);
    });    
    
    var request = new Request.JSON({
      'url': en4.core.baseUrl + 'siteevent/waitlist/check-ticket-availability',
      'data': $merge({
        'format': 'json',
        'subject': en4.core.subject.guid,
        'occurrence_id': occurrence_id,
        'totalTicketsInCart' : totalTicketsInCart,
      }),
      onSuccess: function (responseJSON) {
        if(responseJSON.eventIsFull == 1) {
            var orderNowButton = 'none';
            var eventIsFull = 'block'; 
        }else {
            var orderNowButton = 'block';
            var eventIsFull = 'none';
        }  
        
        if($('orderNowButton')) {
            $('orderNowButton').setStyle('display', orderNowButton);
        }

        if($('eventIsFull')) {
            $('eventIsFull').setStyle('display', eventIsFull);
        }
      }
    });
    request.send();
}

//BUYERS DETAIL VALIDATION
function validateBuyerInformation() {
    
    var form_validation_error = 0;
    $$('.buyer_detail_subform').each(function (elem) {

    if($(elem).getElement('.buyer_detail_fname')) {
        var isValidFname = validateNames($(elem).getElement('.buyer_detail_fname').value);
        if(isValidFname != true) {
            $(elem).getElement('.buyer_detail_fname_error').style.display = 'block';
            form_validation_error = 1;
        }
        else {
            $(elem).getElement('.buyer_detail_fname_error').style.display = 'none';
        }
    }

    if($(elem).getElement('.buyer_detail_lname')) {
        var isValidLname = validateNames($(elem).getElement('.buyer_detail_lname').value);
        if(isValidLname != true) {
            $(elem).getElement('.buyer_detail_lname_error').style.display = 'block';
            form_validation_error = 1;
        }
        else {
            $(elem).getElement('.buyer_detail_lname_error').style.display = 'none';
        }        
    }      
      
    if($(elem).getElement('.buyer_detail_email')) {
        var isValidEmail = validateEmail($(elem).getElement('.buyer_detail_email').value);
        if(isValidEmail != true) {
             $(elem).getElement('.buyer_detail_email_error').style.display = 'block';
             form_validation_error = 1;     
        }
         else {
         $(elem).getElement('.buyer_detail_email_error').style.display = 'none';
        }
    }         
 });

 if(form_validation_error == 0) {
  document.getElementById("buyer_details_form").submit();
 }
}

function copyDetails(){

    $$('.buyer_detail_subform').each(function (elem) {
    if($('isCopiedDetails').checked){
      if($(elem).getElement('.buyer_detail_fname'))
      $(elem).getElement('.buyer_detail_fname').value = $('user_fname').value;
    
      if($(elem).getElement('.buyer_detail_lname'))
      $(elem).getElement('.buyer_detail_lname').value = $('user_lname').value;
    
      if($(elem).getElement('.buyer_detail_email'))
      $(elem).getElement('.buyer_detail_email').value = $('user_email').value;
    
      if($(elem).getElement('.buyer_detail_contact'))
      $(elem).getElement('.buyer_detail_contact').value = $('user_contact').value;
    }else{
    if($(elem).getElement('.buyer_detail_fname'))
    $(elem).getElement('.buyer_detail_fname').value = "";
  
    if($(elem).getElement('.buyer_detail_lname'))
    $(elem).getElement('.buyer_detail_lname').value = "";
  
    if($(elem).getElement('.buyer_detail_email'))
    $(elem).getElement('.buyer_detail_email').value = "";
  
    if($(elem).getElement('.buyer_detail_contact'))
    $(elem).getElement('.buyer_detail_contact').value = "";
  }     
 });
  
}

function filter_tickets(viewType) {
    
    $('manage_tickets').innerHTML = '<div class="seaocore_content_loader"></div>';
    var url = en4.core.baseUrl + 'widget/index/mod/siteeventticket/name/my-tickets';
    var request = new Request.HTML({
        url: url,
        data: {
            format: 'html',
            subject: en4.core.subject.guid,
            isajax: true,
            pagination: 0,
            viewType: viewType
        },
        evalScripts: true,
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('siteevent_manage_event').innerHTML = responseHTML;
            en4.core.runonce.trigger();
        }
    });
    request.send();
}

function validateEmail(email) {
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return filter.test(email);
}

function validateNames(name) {
    
    //var filter = /^[a-zA-Z ]{2,30}$/;
    //return filter.test(name);
    
    var name_length = (name.trim()).length;
    
    return ((name_length > 1 && name_length < 31) ? true : false);
}
