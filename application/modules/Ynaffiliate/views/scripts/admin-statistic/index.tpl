<?php
$this->headScript()
	   ->appendFile($this->layout() -> staticBaseUrl . 'application/modules/Ynaffiliate/externals/scripts/moo.flot.js')
	   ->appendFile($this->layout() -> staticBaseUrl . 'application/modules/Ynaffiliate/externals/scripts/moo.flot.pie.js');
?>
<h2>
  <?php echo $this->translate('Affiliate Plugin') ?>
</h2>
<!-- admin menu -->
<?php echo $this->content()->renderWidget('ynaffiliate.admin-main-menu') ?>
<p>
	<?php echo $this->translate("YNAFFILIATE_VIEWS_SCRIPTS_ADMINSTATISTIC_INDEX_DESCRIPTION") ?>
</p>
<div class="profile_fields">
	<h4><span><?php echo $this->translate('Statistics');?></span></h4>
	<ul>
		<li>
			<span><?php echo $this->translate("Total Affiliates") ?></span>
			<span>
			<?php echo $this->locale()->toNumber($this->totalAffiliates); ?>
			</span>
		</li>
		<li>
			<span><?php echo $this->translate("Total Clients") ?></span>
			<span>
			<?php echo $this->locale()->toNumber($this->totalClients); ?>
			</span>
		</li>
		<?php foreach($this -> rules as $rule):?>
			<li>
				<span><?php echo $this->translate(Engine_Api::_() -> getDbTable('modules', 'core') -> getModule($rule -> module) -> title) . ' - '. $this->translate($rule -> rule_title) ?></span>
				<span>
				<?php echo $this->locale()->toNumber(round(Engine_Api::_()->getDbTable('commissions', 'ynaffiliate') -> getTotalPoints(null, null, array('ruleId' => $rule -> rule_id, 'notStatus' => 'denied')), 2)); ?>
				</span>
			</li>
		<?php endforeach; ?>
		<li>
			<span><?php echo $this->translate("Total Commission Points") ?></span>
			<span>
			<?php echo $this->locale()->toNumber($this->totalCommissions); ?>
			</span>
		</li>
		<li>
			<span><?php echo $this->translate("Total Requested Points") ?></span>
			<span>
			<?php echo $this->locale()->toNumber($this->totalRequested); ?>
			</span>
		</li>
	</ul>	
</div>

<div class="ynaffiliate_stistic_chart">
	<div class="ynaffiliate_stistic_chart_header">
		<?php echo $this -> translate('Total commission points group by')." ";?><span id="statistic_group_by"><?php echo $this -> translate('commission rules')?></span>
	</div>
	<div class="settings">
		<?php echo $this->form->render($this); ?>
	</div>
	<?php echo $this->partial('_chart.tpl', array('userId' => 0));?>
</div>
