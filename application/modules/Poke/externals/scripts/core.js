// CORE.JS
var pokeinfo = function(poke_id, poke_receiver_id)
{
	var total_div = $('count_div').value;
	if(total_div >=  1 ) {
    total_div = total_div - 1;
	}
	var parentnode = $('global_content').getElement('.layout_poke_list_pokeusers').parentNode;
	var childnode =  $('global_content').getElement('.layout_poke_list_pokeusers');
	en4.core.request.send(new Request.HTML({      	
    url : en4.core.baseUrl + 'poke/pokeusers/deletepoke/',
    data : {
      format : 'html',
      pokedelete_id : poke_id, 
      poke_receiverid : poke_receiver_id},
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
			if(total_div == 0) {
				$(poke_id+ '_poke').innerHTML = '';
				parentnode.removeChild(childnode);
				
			} else {
				$(poke_id+ '_poke').innerHTML = '';
				$('count_div').value = total_div;
			}
		}
  }))
};