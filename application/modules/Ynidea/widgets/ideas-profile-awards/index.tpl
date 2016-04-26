<ul class="ideas_frame ideas_browse">
<?php 
foreach ($this->awards as $award): ?>
<?php $trophy = Engine_Api::_()->getItem('ynidea_trophy', $award->trophy_id);
	if($trophy):?>
<li>
	 <div class="ideas_photo">
              <?php echo $this->htmlLink($trophy->getHref(), $this->itemPhoto($trophy, 'thumb.normal')) ?>
            </div> 
            <div class="ideas_info" style="width: 80%">
              <div class="ideas_title" style="width: 50%; float: left">
                <div class="ideas_photo">
                <?php $idea_title = Engine_Api::_()->ynidea()->subPhrase($trophy->getTitle(),60);?>
                <?php echo $this->htmlLink($trophy->getHref(), $trophy->getTitle());?>
                -
                <?php echo $trophy->getOwner();?>
                </div>
                <div class="ideas_desc">
                <?php echo Engine_Api::_()->ynidea()->subPhrase(strip_tags($trophy->description),100); ?>
              	</div>
              	</div>
                <div class="ideas_options" style="float: none; width: auto">
              		<span>
              			<?php if($award->award == 0): ?>
              			<span class="ynidea_glod_medal"></span>
		        		<?php else: ?>
		        		<span class="ynidea_silver_medal"></span>
		        		<div style="padding-top: 37px"><?php echo $this->translate("Comment")?>:</div>
		        		<div>
		        			<?php echo $award->comment?>
		        		</div>
		        		<?php endif; ?>
              		</span>
            	</div>          
            </div>
</li>
<?php endif; endforeach;?>
</ul>