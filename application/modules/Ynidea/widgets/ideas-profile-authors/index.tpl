<ul class='judges_browse' id="list_coauthors">
<?php $viewer = Engine_Api::_()->user()->getViewer();
foreach($this->coauthors as $coauthor): ?>
	<li>
		<?php $user = Engine_Api::_()->getItem('user', $coauthor->user_id);?>
		<div class="judge_photo">
          <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
        </div> 
        <div class="judge_info" style="font-weight: bold">
			<?php echo $user;?>
		</div>
		<?php if($this->idea->user_id == $viewer->getIdentity()):?>
		<div style="float: right; padding-right:20px; ">
			<?php echo $this->htmlLink(array(
                  'action' => 'remove-coauthor',
                  'id' => $this->idea->getIdentity(),
                  'author_id' => $coauthor->getIdentity(),
                  'route' => 'ynidea_specific',
                  'reset' => true,
                ), $this->translate('Remove'), array('class'=>'buttonlink menu_ynidea_delete smoothbox',
                )) ?>
		</div>
		<?php endif;?>
	</li>
<?php endforeach;?>
</ul>
<div>
  <div id="list_coauthors_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="list_coauthors_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    var anchor = $('list_coauthors').getParent();
    $('list_coauthors_previous').style.display = '<?php echo ( $this->coauthors->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('list_coauthors_next').style.display = '<?php echo ( $this->coauthors->count() == $this->coauthors->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('list_coauthors_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->coauthors->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('list_coauthors_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->coauthors->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
  });
</script>