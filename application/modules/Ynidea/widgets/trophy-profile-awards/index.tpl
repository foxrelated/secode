<ul id="list_idea_awards" class='ideas_browse ideas_list_tab'>
    <?php foreach( $this->paginator as $idea ):?>
      <li>
        <div class="trophy_ideas_photo">
          <?php echo $this->htmlLink($idea->getHref(), $this->itemPhoto($idea, 'thumb.normal')) ?>
        </div> 
        <div class="trophy_ideas_info">
          <div class="ideas_title" style="overflow: hidden">
            <div class="ideas_photo">
            <?php $idea_title = Engine_Api::_()->ynidea()->subPhrase($idea->getTitle(),60);?>
            <?php echo $this->htmlLink($idea->getHref(), $idea_title);?>
             | 
            <span class="ynidea_score_<?php echo $idea->idea_id;?>"><?php echo $this->translate("Score: %s/10",number_format($idea->score,2)); ?></span>          
            </div>
			<span class="ynidea_medal"><?php $award = $idea->checkAward($this->trophy->trophy_id)->award;
        		if($award == 0):
				 ?>
				 <span class="ynidea_glod_medal"></span>
        		<?php else: ?>
        		<span class="ynidea_silver_medal"></span>
        		<?php endif;?> 
            </span>             
          </div>              
          <div class="ideas_desc" style="margin-top: 8px;">
            <?php echo Engine_Api::_()->ynidea()->subPhrase(strip_tags($idea->description),200); ?>
            <?php //echo $this->htmlLink($idea->getHref(), $this->translate("View more"));?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
</ul>
<div>
  <div id="list_idea_awards_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="list_idea_awards_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    var anchor = $('list_idea_awards').getParent();
    $('list_idea_awards_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('list_idea_awards_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('list_idea_awards_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('list_idea_awards_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
  });
</script>