<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndonation
 * @author     YouNet Company
 */
?>
<?php if (count($this->paginator) > 0):?>
  <table class="ynfundraising_transaction_table">
    <thead>
      <tr>
        <th><?php echo $this->translate("Requested Date") ?></th>
        <th><?php echo $this->translate("Title") ?></th>
        <th><?php echo $this->translate("Requested") ?></th>
		<th><?php echo $this->translate("Status") ?></th>
		<th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      	<?php
      	foreach ($this->paginator as $item):
      		$status = $item->status;
      	?>
	        <tr>
	        	<td>
					<?php
					// requested date
					echo $this->locale()->toDateTime($item->request_date)
					//echo $this->timestamp($item->transaction_date);
				  	?>
	        	</td>
			  	<td>
				  	<?php
				  	// idea (trophy) title
				  	$parent = Engine_Api::_()->ynfundraising()->getItemFromType($item);
					if($parent)
				  		echo $this->htmlLink($parent->getHref(), $parent->getTitle());
					else {
						echo $this->translate("Deleted");
					}
				  	?>
			  	</td>
			  	<td>
					<?php
						// owner
					$user = Engine_Api::_ ()->getItem ( 'user', $item->requester_id);
					echo $this->htmlLink($user->getHref(), $user->getTitle());
					?>
			  	</td>
			  	<td>
			  		<?php echo $this->translate(ucfirst($status))?>
			  	</td>
			  	<td>

					<?php
					switch ($status) {
						case Ynfundraising_Plugin_Constants::REQUEST_WAITING_STATUS:
							echo $this->htmlLink(
			                	array('route' => 'ynfundraising_extended',
			                	'controller' => 'request',
			                	'action' => 'approve',
			                	'request_id' => $item->getIdentity(),
			                	),
			                  	$this->translate('Approve'),
			                  	array ('class' => 'smoothbox')
							);
							echo ' | ';
							echo $this->htmlLink(
			                	array('route' => 'ynfundraising_extended',
								'controller' => 'request',
			                	'action' => 'deny',
			                	'request_id' => $item->getIdentity(),
			                	),
			                  	$this->translate('Deny'),
			                  	array ('class' => 'smoothbox')
							);
							echo ' | ';
							echo $this->htmlLink(
			                	array('route' => 'ynfundraising_extended',
								'controller' => 'request',
								'action' => 'delete',
								'request_id' => $item->getIdentity()
								),
			                  	$this->translate('Delete'),
			                  	array('class' => 'smoothbox')
							);

							break;
						case Ynfundraising_Plugin_Constants::REQUEST_APPROVED_STATUS:
							// check if campaign from this request is exist
 							$parent_obj = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getItemFromType ( $item->toArray() );
 							if ($parent_obj && $campaign = $parent_obj->checkExistCampaign()) {
 								if ($campaign->published == 1)
 								{
 									echo $this->htmlLink($campaign->getHref(), $this->translate('View Campaign'));
 								}
 							}
						case Ynfundraising_Plugin_Constants::REQUEST_DENIED_STATUS:
							if (trim($item->reason) != '') {
								//echo " | ";
								echo $this->htmlLink(
										array('route' => 'ynfundraising_extended', 'controller' => 'request', 'action' => 'view-reason', 'request_id' => $item->getIdentity()),
										$this->translate('View Reason'),
										array('class' => 'smoothbox')
								);
							}
							break;
						default:
							break;
					};
          			?>
			  	</td>

	        </tr>

      	<?php endforeach; ?>

    </tbody>
  </table>
	<div class="ynfundraising_paginator" style="margin-top: 10px">
		  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
	</div>
<?php elseif(isset($this->formValues['title']) || isset($this->formValues['start_date']) || isset($this->formValues['end_date']) || isset($this->formValues['status'])) :?>
	<div class="tip">
    	<span>
        	<?php echo $this->translate('There are no requests that match your search query.'); ?>
        </span>
	</div>
  <?php else :?>
	<div class="tip">
    	<span>
        	<?php echo $this->translate('There is no request.'); ?>
        </span>
	</div>
<?php endif; ?>
