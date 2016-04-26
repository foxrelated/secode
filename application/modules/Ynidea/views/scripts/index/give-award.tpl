<div style="width: 350px">
<?php echo $this->form->render($this);?>
</div>

<script>
var award = 0;
$('comment-wrapper').style.display = 'none';
function showcomment(e){
    if(e.value == 1){
        $('comment-wrapper').style.display = 'block';
    }    
    if(e.value == 0){
        $('comment-wrapper').style.display = 'none';
    } 
    award = e.value;
}
function award_idea()
   {
   	   var comment = $('comment').value;
   	   if(<?php echo $this->onlysilver ?> == 1 && award == 0)
   	   {
   	   		alert('<?php echo $this->translate("Please select award!")?>');
   	   		return false;
   	   }
   	   $('submit').style.display = 'none';
       var request = new Request.JSON({
            'url' :  en4.core.baseUrl + 'ynidea/ideas/give-award-ajax',
            'data' : {
                'trophy_id' : <?php echo $this->trophy_id?>,
                'idea_id'   : <?php echo $this->idea_id?>,
                'award'     : award,
                'comment'   : comment
            },
            'onComplete':function(responseObject)
            {  
                //$$('award_idea_<?php echo $this->idea_id?>').set('text',award);
                var arr = parent.document.getElementsByClassName('award_idea_<?php echo $this->idea_id?>');
                for(var i = 0; i < arr.length; i ++)
                {
                	if(award == 0)
                		arr[i].innerHTML = '<?php echo $this->translate("Gold")?>';
                	else
                		arr[i].innerHTML = '<?php echo $this->translate("Silver")?>';
                }
                parent.location.reload(true); 
                parent.Smoothbox.close();	
            }
        });
        request.send(); 
	return false;
   } 
</script>