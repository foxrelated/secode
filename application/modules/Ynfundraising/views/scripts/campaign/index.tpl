<?php $viewer = Engine_Api::_()->user()->getViewer() ?>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="ynfundraising_campaigns_browse">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class='ynfundraising_campaigns_browse_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item)) ?>
            <?php if(!$item->activated):?>
            	<div class="wrap_link">
					<div class="link"><span class="inactivated"><?php echo $this->translate("INACTIVED")?></span></div>	
				</div>
            <?php endif;?>
          </div>
          <div class='ynfundraising_campaigns_browse_options'>
            <?php
            if ($item->user_id == $viewer->user_id && $item->canEdit()) {
	            echo $this->htmlLink(array(
	              'route' => 'ynfundraising_general',
	              'action' => 'edit-step-one',
	              'campaignId' => $item->getIdentity(),
	              'reset' => true,
	            ), $this->translate('Edit'), array(
	              'class' => 'buttonlink icon_ynfundraising_edit',
	            ));
			}
			?>

            <?php
            if ($item->user_id == $viewer->user_id) {
				if ($item->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS) {
		            echo $this->htmlLink(array(
		                'route' => 'ynfundraising_general',
		                'action' => 'close',
		                'campaignId' => $item->getIdentity(),
		                'format' => 'smoothbox'
		                ), $this->translate('Close'), array(
		              'class' => 'buttonlink smoothbox icon_ynfundraising_close'
		            ));
				}
				if ($item->status == Ynfundraising_Plugin_Constants::CAMPAIGN_DRAFT_STATUS) {
					echo $this->htmlLink(array(
							'route' => 'ynfundraising_general',
							'action' => 'close',
							'campaignId' => $item->getIdentity(),
							'format' => 'smoothbox'
					), $this->translate('Delete'), array(
							'class' => 'buttonlink smoothbox icon_ynfundraising_close'
					));
				}
			}
			?>
		<?php
			if ($item->user_id == $viewer->user_id && $item->status != Ynfundraising_Plugin_Constants::CAMPAIGN_DRAFT_STATUS) {
				echo $this->htmlLink(array(
					'route' => 'ynfundraising_extended',
					'controller' => 'campaign',
					'action' => 'view-statistics-chart',
					'campaign_id' => $item->getIdentity()
				),
				$this->translate('View Statistics'),
				array('class' => 'buttonlink icon_ynfundraising_statistics'));
			}
		?>
          </div>
          <div class='ynfundraising_campaigns_browse_info'>
            <div class='ynfundraising_campaigns_browse_info_title'>
              <b><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></b>
              -
                <font class ="ynfundraising_campaigns_browse_info_status_">
                   <?php echo $this->translate(ucfirst($item->status));?>
                </font>
            </div>
            <p class='ynfundraising_campaigns_browse_info_date'>
		<?php
				//echo $this->translate(array('%1s donor - %2s like - %3s view','%1s donors - %2s likes - %3s views', $item->getTotalDonors(), $item->like_count, $item->view_count),$item->getTotalDonors() , $item->like_count, $item->view_count);
				echo $this->translate(array('%s donor','%s donors',$item->getTotalDonors()),$item->getTotalDonors() );
				echo " - ".$this->translate(array('%s like','%s likes', $item->like_count), $item->like_count);
				echo " - ".$this->translate(array('%s view','%s views',$item->view_count),$item->view_count);
		?>

            </p>
            <?php if($item->expiry_date && $item->expiry_date != "0000-00-00 00:00:00" && $item->expiry_date != "1970-01-01 00:00:00" && $item->getLimited()): ?>
            <p class=''>
              <?php
              	echo $this->translate("Limited: %s left", $item->getLimited())
              ?>
            </p>
            <?php endif; ?>
            <p class='ynfundraising_campaigns_browse_info_blurb'>
              <?php echo $this->string()->truncate($this->string()->stripTags($item->short_description), 300) ?>
            </p>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php elseif(isset($this->formValues['search'])): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any campaigns that match your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any campaign.');?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
