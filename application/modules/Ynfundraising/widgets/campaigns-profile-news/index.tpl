<?php if($this->campaign->isOwner($this->viewer)):?>
<?php echo $this->form->render($this);?> 

<div class="ynFRaising_PostNew_Submitbtn">
	<a href="javascript:;" onclick="post_news()"><button><?php echo $this->translate("Post")?></button></a>
	<!-- show all news -->
</div>



<?php endif;?>
<div id="list_news">
	<?php 
		$news = $this->partial('_news.tpl', array('campaign_id'=>$this->campaign->getIdentity()));  
		echo $news;
	?>
</div>
<script type="text/javascript">
	var post_news = function()
	{
		var title = $('title').value;
		var link = $('link').value;
		var content = $('content').value;
		
		if(link.trim() != "")
		{
			if(!isValidURL(link.trim()))
			{
				alert('<?php echo $this->translate("The link is invalid!")?>');
				return false;
			}
		}
		
		if(title.trim() == "")
		{
			alert('<?php echo $this->translate("Please enter news headline!")?>');
			return false;
		}
		else if(content.trim() == "")
		{
			alert('<?php echo $this->translate("Please enter content of news!")?>');
			return false;
		}
		
		if(title.length > 256)
			title = title.substring(0, 256) + "...";
		if(link.length > 256)
			link = link.substring(0, 256) + "...";
		if(content.length > 2000)
			content = content.substring(0, 2000) + "...";
		
		var makeRequest = new Request(
            {
                url: en4.core.baseUrl +  "ynfundraising/campaign/add-news/campaign_id/<?php echo $this->campaign->getIdentity()?>/title/"+ title +"/content/" + content + "?link=" + link,
                onComplete: function (respone)
                {              
                	document.getElementById('list_news').innerHTML = respone;  
                	$('title').value = "";
                	$('link').value = "";
                	$('content').value = "";
                }
            }
		    )
		    makeRequest.send();
	}
	var isValidURL = function(url) 
	{
		var pattern = new RegExp("((http|https)(:\/\/))?([a-zA-Z0-9]+[.]{1}){2}[a-zA-z0-9]+(\/{1}[a-zA-Z0-9]+)*\/?", "i"); 
		if (pattern.test(url) != true) {
			return false;
		} else {
			return true;
		}
	}

</script>