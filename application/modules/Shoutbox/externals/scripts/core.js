/**
 * @author     George Coca
 * @website    geodeveloper.net <info@geodeveloper.net>   
 */
function addShout(msg, identity)
{
    if( en4.core.request.isRequestActive() ) return;
    en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'shoutbox/index/create',
        data : {
        format : 'html',
        nolayout : 'true',
        msg : msg,
        identity : identity
      },
      onRequest : function()
      {
        toggleLoading();
        var shoutbox_msg = $('shoutbox_msg');
        shoutbox_msg.value = '';
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript){
        if(responseHTML) {
            toggleLoading('hide');
            var shout_shoutbox = $('shoutbox_content');
            var li = new Element('li', {html : responseHTML});
            li.inject(shout_shoutbox);
            
            var toWhite = new Fx.Tween(li, {
                duration: 1000,
                property: 'background-color',
                    onComplete : function(){
                    li.removeAttribute("style");
                }
            });

            var highlight = new Fx.Tween(li, {
                duration: 1000,
                property: 'background-color',
                onComplete : function(){
                toWhite.start('#fff2ce', '#ffffff');
                }
            });

            highlight.start('#ffffff', '#fff2ce');
            
               if(shout_shoutbox.getChildren().length >= 10) {
                var firstMessage = shout_shoutbox.getElement('li:first-child');
                firstMessage.dispose();
                }
                
             if($('shoutbox_tip'))
                 $('shoutbox_tip').hide();

        }
      }
    }));
}

function getShouts(global)
{
    if( en4.core.request.isRequestActive() ) return;
    en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'shoutbox/index/getshouts',
        data : {
        format : 'html',
        nolayout : 'true',
        identity : $('shoutbox_identity').value
      },
      onRequest : function()
      {
        toggleLoading();
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript){
        if(responseHTML) {
            var shout_shoutbox = $('shoutbox_content');
            shout_shoutbox.set('html', responseHTML)
        }
        toggleLoading('hide');
      }
    }));
}

function gotoProfile(url)
{
    window.location.replace(url);
    Smoothbox.close();
}

function toggleLoading(task)
{
    var shoutbox_loading = $('shoutbox_loading');
    if(task == "hide")
        shoutbox_loading.setStyle('background-position', '-2000px -2000px');
    else
        shoutbox_loading.setStyle('background-position', 'center center')
}