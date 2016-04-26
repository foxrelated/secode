function loadMessageFromRequest(id,url)
{
    var l = document.getElementById('message_request_'+id);
    l.innerHTML = '<img src="./application/modules/Groupbuy/externals/images/ajax-loader.gif"/>';
    var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'group-buy/account/load-message',
            'data' : {
                'item_id' : id 
            },
            'onComplete':function(responseObject)
            {  
                if( typeof(responseObject)!="object")
                {
                     alert('ERR');
                }
                else
                { 
                      document.getElementById('message_request_'+id).innerHTML =  responseObject.html;
                }
        	
            }
        });
        request.send();  
}
function close(id)
{
    document.getElementById('message_request_'+id).innerHTML =  '';
}
function requestMoney()
{
    var currentmoney = document.getElementById('txtrequest_money').value; 
    var reason = document.getElementById('textarea_request').value; 
    var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'group-buy/account/requestmoney',
            'data' : {
                'currentmoney' : currentmoney, 
                'reason' : reason
            },
            'onComplete':function(responseObject)
            {  
                if( typeof(responseObject)!="object")
                {
                     alert('ERR');
                }
                else
                {   
                      document.getElementById('request').innerHTML = responseObject.html;
                      if(responseObject.current_request_money)
                      {
                          parent.document.getElementById('current_request_money').innerHTML = responseObject.current_request_money;
                          parent.document.getElementById('current_money_money').innerHTML = responseObject.current_money_money;
                      }
                }
            }
        });
        request.send();   
}
function requestRefund()
{
    var currentmoney = document.getElementById('txtrequest_money').value; 
    var reason = document.getElementById('textarea_request').value; 
    var buy_id = document.getElementById('txtrequest_buy').value; 
    var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'group-buy/index/refund',
            'data' : {
                'currentmoney' : currentmoney, 
                'reason' : reason,
                'buy_id' : buy_id
            },
            'onComplete':function(responseObject)
            {  
                if( typeof(responseObject)!="object")
                {
                     alert('ERR');
                }
                else
                {   
                      document.getElementById('request').innerHTML = responseObject.html;
                      parent.location.reload(true);
                      parent.Smoothbox.close();
                }
            }
        });
        request.send();   
}