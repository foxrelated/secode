<div style="padding: 10px; border-width: 0" class='polls_view'>
  <h3>
    <?php echo $this->poll->title ?>
  </h3>
  <div class="poll_vote_total">
    <i class="fa fa-thumbs-o-up"></i>
    <?php echo $this->translate(array('%s vote', '%s votes', $this->poll->vote_count), $this->locale()->toNumber($this->poll->vote_count)) ?>
  </div>
  <div class="poll_desc">
    <?php echo $this->poll->description ?>
  </div>

  <?php
    // poll, pollOptions, canVote, canChangeVote, hasVoted, showPieChart
	 echo $this->partial('_poll.tpl', 'ynfeedback', array(
	 	'poll' => $this->poll ,
	    'owner' => $this->owner ,
	    'viewer' => $this->viewer ,
	    'pollOptions' => $this->pollOptions,
	    'hasVoted' => $this->hasVoted,
	    'showPieChart' => $this->showPieChart,
	    'canVote' => $this->canVote ,
	    'canChangeVote' => $this->canChangeVote,
	    'hideVote' => true
	));
  ?>
</div>


<script type="text/javascript">
  $$('.core_main_poll').getParent().addClass('active');
  window.addEvent('domready', function(){
  	document.getElementById('poll_toggleResultsLink').click();
  });
</script>
