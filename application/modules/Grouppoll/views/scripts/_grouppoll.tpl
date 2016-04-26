<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _grouppoll.tpl 6590 2010-12-08 9:40:21Z SocialEngineAddOns
 * @author     SocialEngineAddOns
 */
?>

<?php
	$this->headScript()
	->appendFile('application/modules/Grouppoll/externals/scripts/core.js');
	$this->headTranslate(array(
	'Show Question', 'Show Result', '%1$s%%', '%1$s vote',
	));
?>
<?php if(($this->grouppoll->end_settings == 1 && ($this->grouppoll->end_time > ($today = date("Y-m-d H:i:s")))&&($this->can_vote == 1)) || $this->grouppoll->end_settings == 0 &&($this->can_vote == 1)) {
				$valid = 1;
			} else { 
					$valid = 0;
			}?>

<script type="text/javascript">
		//<![CDAT
  
		var enddate_setting = <?php echo sprintf('%d', $this->grouppoll->end_settings) ?>; 
		var enddate_valid = <?php echo sprintf('%d', $valid) ?>;
		en4.core.runonce.add(function() {
			var initializeGrouppoll = function() {
				en4.grouppoll.urls.vote = '<?php echo $this->url(array('module' => 'grouppoll', 'controller' => 'index', 'action' => 'vote'), 'default') ?>';
				en4.grouppoll.urls.login = '<?php echo $this->url(array(), 'user_login') ?>';
				en4.grouppoll.addGrouppollData(<?php echo $this->grouppoll->getIdentity() ?>, {
					canVote : <?php echo $this->canVote ? 'true' : 'false' ?>,
					canChangeVote : <?php echo $this->canChangeVote ? 'true' : 'false' ?>,
					hasVoted : <?php echo $this->hasVoted ? 'true' : 'false' ?>
				});

				$$('#grouppoll_form_<?php echo $this->grouppoll->getIdentity() ?> .grouppoll_radio input').removeEvents('click').addEvent('click', function(event) {
					en4.grouppoll.vote(<?php echo $this->grouppoll->getIdentity() ?>, event.target);
				});
			}

			// Dynamic loading for feed
			if( $type(en4) == 'object' && 'grouppoll' in en4 ) {
				initializeGrouppoll();
			} else {
				new Asset.javascript('application/modules/Grouppoll/externals/scripts/core.js', {
					onload: function() {
						initializeGrouppoll();
					}
				});
			}
		});
		//]]>
</script>
<span class="grouppoll_view_single">
	<form id="grouppoll_form_<?php echo $this->grouppoll->getIdentity() ?>" action="<?php echo $this->url() ?>" method="POST" onsubmit="return false;">
		<ul id="grouppoll_options_<?php echo $this->grouppoll->getIdentity() ?>" class="grouppoll_options">
			<?php foreach( $this->grouppollOptions as $i => $option ): ?>
				<li id="grouppoll_item_option_<?php echo $option->poll_option_id ?>">
					<div class="grouppoll_has_voted" <?php if ($valid == 1) { echo ( $this->hasVoted ? '' : 'style="display:none;"' );}  else { echo ( $this->hasVoted ? '' : 'style="display:block;"' ); } ?>>
						<?php $show_option = 0;?>
						<?php if ($valid == 1): ?>
							<div class="grouppoll_option">
                <?php if (($valid == 1)||($this->grouppoll->end_settings == 1 )): ?>
									<?php echo $option->grouppoll_option ?>
									<?php $show_option == 1;?>
                  <?php endif;?>
							</div>
						<?php endif; ?>
						<div class="grouppoll_option">
							<?php if($show_option == 0 && ($this->grouppoll->end_settings == 1 )||$this->can_vote == 0): ?>
								<?php echo $option->grouppoll_option ?>
							<?php endif; ?>
						</div>
						<?php $pct = $this->grouppoll->vote_count
									? floor(100*($option->votes/$this->grouppoll->vote_count))
									: 0;
							if (!$pct)
								$pct = 1;
						?>
						<?php if (($valid == 1)||($this->grouppoll->end_settings == 1 )||$this->can_vote == 0): ?>
							<div id="grouppoll-answer-<?php echo $option->poll_option_id ?>" class='grouppoll_answer grouppoll-answer-<?php echo (($i%8)+1) ?>' style='width:                         <?php echo .7*$pct;?>%;'>
							&nbsp;
							</div>
							<div class="grouppoll_answer_total">
                
								<?php echo $this->translate(array('%1$s vote', '%1$s votes', $option->votes), $this->locale()->toNumber($option->votes)) ?>
								(<?php echo $this->translate('%1$s%%', $this->locale()->toNumber($option->votes ? $pct : 0)) ?>)
							</div>
						<?php endif; ?>
					</div>
					<div class="grouppoll_not_voted" <?php echo ($this->hasVoted?'style="display:none;"':'') ?> >
						<?php if ($valid == 1): ?>
							<div class="grouppoll_radio" id="grouppoll_radio_<?php echo $option->poll_option_id ?>">
								<input id="grouppoll_option_<?php echo $option->poll_option_id ?>"
												type="radio" name="grouppoll_options" value="<?php echo $option->poll_option_id ?>"
										<?php if ($this->hasVoted == $option->poll_option_id): ?>checked="true"<?php endif; ?>
										<?php if (($this->hasVoted && !$this->canChangeVote) || $this->grouppoll->closed): ?>disabled="true"<?php endif; ?>
								/>
							</div>
							<label for="grouppoll_option_<?php echo $option->poll_option_id ?>">
								<?php echo $option->grouppoll_option ?>
							</label>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if( empty($this->hideStats) ): ?>
			<div class="grouppoll_stats">
				<a href='javascript:void(0);' onClick='en4.grouppoll.toggleResults(<?php echo $this->grouppoll->getIdentity() ?>); this.blur();'    class="grouppoll_toggleResultsLink">
				<?php if ($valid) echo $this->translate($this->hasVoted ? 'Show Question' : 'Show Result' ) ?>
				</a> 
        <?php $show = 0; ?>
				<?php if( empty($this->hideLinks) ): ?>
          <?php if($valid == 1):?>
				    &nbsp;|&nbsp;
          <?php endif;?>
					<?php echo $this->htmlLink(array(
					'module'=>'activity',
					'controller'=>'index',
					'action'=>'share',
					'route'=>'default',
					'type'=>'grouppoll_poll',
					'id' => $this->grouppoll->getIdentity(),
					'format' => 'smoothbox'
					), $this->translate("Share"), array('class' => 'smoothbox')); ?>
						&nbsp;|&nbsp;
					<?php echo $this->htmlLink(array(
					'module'=>'core',
					'controller'=>'report',
					'action'=>'create',
					'route'=>'default',
					'subject'=>$this->grouppoll->getGuid(),
					'format' => 'smoothbox'
					), $this->translate("Report"), array('class' => 'smoothbox')); ?>
          <?php $show = 1; ?>
				<?php endif; ?>
        <?php if($valid == 1||$show == 1): ?>
					&nbsp;|&nbsp;
        <?php endif; ?>
				<span class="grouppoll_vote_total">
					<?php echo $this->translate(array('%s vote', '%s votes', $this->grouppoll->vote_count), $this->locale()->toNumber($this->grouppoll->vote_count)) ?>
				</span>
				&nbsp;|&nbsp;
				<?php echo $this->translate(array('%s view', '%s views', $this->grouppoll->views), $this->locale()->toNumber($this->grouppoll->views)) ?>
			</div>
		<?php endif; ?>
	</form>
</span>