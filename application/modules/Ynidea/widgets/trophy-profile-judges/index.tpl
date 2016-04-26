<ul class='judges_browse' id="list_judges">
<?php $viewer = Engine_Api::_()->user()->getViewer();
if($this->trophy->user_id == $viewer->getIdentity()):?>
    <li style="border-bottom-width: 1px; margin-bottom: 10px">
    <?php echo $this->htmlLink(array(
                  'action' => 'assign',
                  'id' => $this->trophy->getIdentity(),
                  'route' => 'ynidea_trophies',
                  'reset' => true,
                ), $this->translate('Assign New Judges'), array('class'=>'buttonlink menu_ynidea_assign smoothbox'
                )) ?>
    </li>
     <?php endif;?>
<?php if(count($this->judges) > 0): ?>
<?php foreach($this->judges as $judge): ?>
	<li>
		<?php $user = Engine_Api::_()->getItem('user', $judge->user_id);?>
		<div class="judge_photo">
          <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
        </div> 
        <div class="judge_info" style="font-weight: bold">
			<?php echo $user;?>
		</div>
		<?php if($this->trophy->user_id == $viewer->getIdentity()):?>
		<div style="float: right; padding-right:20px; ">
			<?php echo $this->htmlLink(array(
                  'action' => 'delete-judge',
                  'id' => $this->trophy->getIdentity(),
                  'judge_id' => $judge->getIdentity(),
                  'route' => 'ynidea_trophies',
                  'reset' => true,
                ), $this->translate('Remove'), array('class'=>'buttonlink menu_ynidea_delete smoothbox',
                )) ?>
		</div>
		<?php endif;?>
	</li>
<?php endforeach;?>
</ul>
<div>
  <div id="list_judges_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="list_judges_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    var anchor = $('list_judges').getParent();
    $('list_judges_previous').style.display = '<?php echo ( $this->judges->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('list_judges_next').style.display = '<?php echo ( $this->judges->count() <= $this->judges->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('list_judges_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->judges->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('list_judges_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->judges->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
  });
  
</script>
<?php else: ?>
<div class="tip">
    <span>
        <?php echo $this->translate('There are no judges.') ?>
    </span>
</div>
<?php endif; ?>
