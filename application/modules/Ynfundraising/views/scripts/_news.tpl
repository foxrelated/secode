<?php $news = Engine_Api::_()->getApi('core', 'ynfundraising')->getNewsPaginator(array('campaign_id'=>$this->campaign_id));
$viewer = Engine_Api::_()->user()->getViewer();
if($news->getTotalItemCount()):
?>
<?php foreach($news as $item):?>
	<div class="ynfundraising_news_item" id="news_<?php echo $item->new_id?>">
		<h3><?php echo $item->title;?></h3>
		<?php if($item->user_id == $viewer->getIdentity()):?>
			<div class="ynFRaising_LRH3ULLi_date">
				<a href="javascript:;" onclick = "Smoothbox.open('<?php echo $this->url(array('controller'=>'campaign', 'action'=>'edit-news', 'news_id'=>$item->new_id),'ynfundraising_extended')?>')"><?php echo $this->translate("Edit");?></a>/
				<a href="javascript:;" onclick="delete_news('<?php echo $item->new_id?>')"><?php echo $this->translate("Delete");?></a>
			</div>
		<?php endif;?>
		<div class="ynFRaising_LRH3ULLi_date"><?php echo $this->locale()->toDateTime($item->creation_date)?></div>
		<p><?php echo $item->content;?></p>
		<?php if(trim($item->link)):?>
			<div class="ynFRaising_LRH3ULLi_date"><?php echo $this->translate("More at")?> <a href="<?php echo $item->link;?>"><?php echo $item->link?></a></div>
		<?php endif;?>
	</div>
<?php endforeach;?>
<?php else:?>
	<div class="tip">
      <span>
        <?php echo $this->translate('You do not have any news.');?>
      </span>
    </div>
<?php endif; ?>
<script type="text/javascript">
	var delete_news = function(news_id)
	{
		if(confirm('<?php echo $this->translate("Are you sure you want to delete this news?")?>'))
		{
			var makeRequest = new Request(
	        {
	            url: en4.core.baseUrl +  "ynfundraising/campaign/delete-news/news_id/" + news_id,
	            onComplete: function (respone)
	            {              
	            	document.getElementById('list_news').innerHTML = respone;  
	            	alert("<?php echo $this->translate("Delete news successfully!")?>");
	            }
	        }
		    )
		    makeRequest.send();
	   }
	}
</script>