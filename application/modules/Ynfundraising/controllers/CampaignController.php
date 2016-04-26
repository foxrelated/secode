<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfundraising
 * @author     YouNet Company
 */
class Ynfundraising_CampaignController extends Core_Controller_Action_Standard {
	protected $_periods = array (
			Zend_Date::DAY, // dd
			Zend_Date::WEEK, // ww
			Zend_Date::MONTH, // MM
			Zend_Date::YEAR  // y
	);
	protected $_allPeriods = array (
			Zend_Date::SECOND,
			Zend_Date::MINUTE,
			Zend_Date::HOUR,
			Zend_Date::DAY,
			Zend_Date::WEEK,
			Zend_Date::MONTH,
			Zend_Date::YEAR
	);
	protected $_periodMap = array (
			Zend_Date::DAY => array (
					Zend_Date::SECOND => 0,
					Zend_Date::MINUTE => 0,
					Zend_Date::HOUR => 0
			),
			Zend_Date::WEEK => array (
					Zend_Date::SECOND => 0,
					Zend_Date::MINUTE => 0,
					Zend_Date::HOUR => 0,
					Zend_Date::WEEKDAY_8601 => 1
			),
			Zend_Date::MONTH => array (
					Zend_Date::SECOND => 0,
					Zend_Date::MINUTE => 0,
					Zend_Date::HOUR => 0,
					Zend_Date::DAY => 1
			),
			Zend_Date::YEAR => array (
					Zend_Date::SECOND => 0,
					Zend_Date::MINUTE => 0,
					Zend_Date::HOUR => 0,
					Zend_Date::DAY => 1,
					Zend_Date::MONTH => 1
			)
	);
	/**
	 * init check exist Ynidea plugin enable
	 */
	public function init() {
		/*
		 * if(!Engine_Api::_ ()->getApi ( 'core', 'ynfundraising'
		 * )->checkIdeaboxPlugin ()) { return
		 * $this->_helper->requireAuth->forward (); }
		 */
	}
	public function indexAction() {
		// Check authoraiztion permisstion
		if (! $this->_helper->requireUser ()->isValid ()) {
			return;
		}
		if (! $this->_helper->requireAuth ()->setAuthParams ( 'ynfundraising_campaign', null, 'view' )->isValid ()) {
			return;
		}
		// Preload info
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		Engine_Api::_ ()->core ()->setSubject ( $viewer );

		if (! $this->_helper->requireSubject ()->isValid ()) {
			return;
		}

		// Prepare data
		$this->view->form = $form = new Ynfundraising_Form_CampaignSearch ();

		// Process form
		$form->isValid ( $this->_getAllParams () );
		$values = $form->getValues ();
		$values['mycontest'] = 1;
		$this->view->formValues = array_filter ( $values );
		$values ['user_id'] = $viewer->getIdentity ();
		if (! isset ( $values ['show'] )) {
			$values ['show'] = 1;
		}
		
		// Get campaign paginator
		$this->view->paginator = $paginator = Engine_Api::_ ()->ynfundraising ()->getCampaignPaginator ( $values );

		// render
		$this->_helper->content->setEnabled ();
	}
	public function shareAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( TRUE );
		$campaign_id = $this->_getParam ( 'campaign_id' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		if (! $campaign) {
			return $this->_helper->requireAuth->forward ();
		}
		$campaign->share_count ++;
		$campaign->save ();
		echo '{"share":"' . $campaign->share_count . '"}';
	}
	public function addNewsAction() {
		if (! $this->_helper->requireUser ()->isValid ()) {
			return;
		}
		$this->_helper->layout->disableLayout ();
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$this->_helper->viewRenderer->setNoRender ( TRUE );
		$campaign_id = $this->getRequest ()->getParam ( 'campaign_id' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		$title = $this->getRequest ()->getParam ( 'title' );
		$content = $this->getRequest ()->getParam ( 'content' );
		$link = $this->getRequest ()->getParam ( 'link' );
		if ($title != "" && $content != "") {
			$newsTable = Engine_Api::_ ()->getDbtable ( 'news', 'ynfundraising' );
			$db = $newsTable->getAdapter ();
			$db->beginTransaction ();
			try {
				$news = $newsTable->createRow ();
				$news->user_id = $viewer->getIdentity ();
				$news->campaign_id = $campaign_id;
				$news->title = $title;
				$news->content = $content;
				$news->link = $link;
				$news->save ();
				// Commit
				$db->commit ();

				// send notification to followe
				$notificationTbl = Engine_Api::_ ()->getDbtable ( 'notifications', 'activity' );
				$follows = Engine_Api::_ ()->ynfundraising ()->getFollowers ( $campaign->getIdentity () );
				if (count ( $follows ) > 0) {
					foreach ( $follows as $follow ) {
						if ($follow->user_id != $viewer->getIdentity ()) {
							$notificationTbl->addNotification ( $follow->getOwner (), $viewer, $campaign, 'ynfundraising_notify_news', array () );
						}
					}
				}
			}

			catch ( Exception $e ) {
				$db->rollBack ();
				throw $e;
			}
		}
		echo $this->view->partial ( '_news.tpl', array (
				'campaign_id' => $campaign_id
		) );
		return;
	}
	public function editNewsAction() {
		if (! $this->_helper->requireUser ()->isValid ()) {
			return;
		}
		$news_id = $this->getRequest ()->getParam ( 'news_id' );
		$news = Engine_Api::_ ()->getItem ( 'ynfundraising_new', $news_id );
		$this->view->form = $form = new Ynfundraising_Form_PostNews ();
		$form->setAttrib ( 'class', 'global_form_popup' );
		$form->populate ( $news->toArray () );
		$this->view->news_id = $news_id;
	}
	public function deleteNewsAction() {
		if (! $this->_helper->requireUser ()->isValid ()) {
			return;
		}
		$this->_helper->layout->disableLayout ();
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$this->_helper->viewRenderer->setNoRender ( TRUE );
		$news_id = $this->getRequest ()->getParam ( 'news_id' );
		$news = Engine_Api::_ ()->getItem ( 'ynfundraising_new', $news_id );
		$campaign_id = $news->campaign_id;
		$newsTable = Engine_Api::_ ()->getDbtable ( 'news', 'ynfundraising' );
		$db = $newsTable->getAdapter ();
		$db->beginTransaction ();
		try {
			$news->delete ();
			// Commit
			$db->commit ();
		}

		catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		echo $this->view->partial ( '_news.tpl', array (
				'campaign_id' => $campaign_id
		) );
		return;
	}
	public function editAjaxNewsAction() {
		if (! $this->_helper->requireUser ()->isValid ()) {
			return;
		}
		// tat di layout
		$this->_helper->layout->disableLayout ();
		// khong su dung view
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$this->_helper->viewRenderer->setNoRender ( TRUE );
		$news_id = $this->getRequest ()->getParam ( 'news_id' );
		$news = Engine_Api::_ ()->getItem ( 'ynfundraising_new', $news_id );
		$campaign_id = $news->campaign_id;
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		$title = $this->getRequest ()->getParam ( 'title' );
		$content = $this->getRequest ()->getParam ( 'content' );
		$link = $this->getRequest ()->getParam ( 'link' );
		if ($title != "" && $content != "") {
			$newsTable = Engine_Api::_ ()->getDbtable ( 'news', 'ynfundraising' );
			$db = $newsTable->getAdapter ();
			$db->beginTransaction ();
			try {
				$news->title = $title;
				$news->content = $content;
				$news->link = $link;
				$news->save ();
				// Commit
				$db->commit ();

				// send notification to followe
				$notificationTbl = Engine_Api::_ ()->getDbtable ( 'notifications', 'activity' );
				$follows = Engine_Api::_ ()->ynfundraising ()->getFollowers ( $campaign->getIdentity () );
				if (count ( $follows ) > 0) {
					foreach ( $follows as $follow ) {
						if ($follow->user_id != $viewer->getIdentity ()) {
							$notificationTbl->addNotification ( $follow->getOwner (), $viewer, $campaign, 'ynfundraising_notify_news', array () );
						}
					}
				}
			}

			catch ( Exception $e ) {
				$db->rollBack ();
				throw $e;
			}
		}
		echo $this->view->partial ( '_news.tpl', array (
				'campaign_id' => $campaign_id
		) );
		return;
	}
	public function followAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( TRUE );
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$campaign_id = ( int ) $this->_getParam ( 'id' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		if (! $campaign)
			return $this->_helper->requireAuth->forward ();
		if (! $this->_helper->requireAuth ()->setAuthParams ( $campaign, $viewer, 'view' )->isValid ()) {
			return;
		}
		$db = Engine_Api::_ ()->getDbtable ( 'follows', 'ynfundraising' )->getAdapter ();
		$db->beginTransaction ();
		try {
			if ($campaign) {
				if ($campaign->checkFollow ()) {
					$follow_table = Engine_Api::_ ()->getItemTable ( 'ynfundraising_follow' );
					$follow = $follow_table->createRow ();
					$follow->campaign_id = $campaign->campaign_id;
					$follow->user_id = $viewer->getIdentity ();
					$follow->save ();
					$db->commit ();
					echo Zend_Json::encode ( array (
							'success' => 1
					) );
				}
			}
		} catch ( Exception $e ) {
			$db->rollback ();
			$this->view->success = false;
			throw $e;
		}
	}
	public function unFollowAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( TRUE );
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$campaign_id = ( int ) $this->_getParam ( 'id' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		if (! $campaign)
			return $this->_helper->requireAuth->forward ();
		if (! $this->_helper->requireAuth ()->setAuthParams ( $campaign, $viewer, 'view' )->isValid ()) {
			return;
		}
		$db = Engine_Api::_ ()->getDbtable ( 'follows', 'ynfundraising' )->getAdapter ();
		$db->beginTransaction ();
		try {
			if ($campaign) {
				if (! $campaign->checkFollow ()) {
					$follow = Engine_Api::_ ()->ynfundraising ()->getFollow ( $viewer->getIdentity (), $campaign_id );
					$follow->delete ();
					$db->commit ();
				}
				echo Zend_Json::encode ( array (
						'success' => 1
				) );
			}
		} catch ( Exception $e ) {
			$db->rollback ();
			$this->view->success = false;
			throw $e;
		}
	}
	public function viewAllSupportersAction() {
		$campaign_id = ( int ) $this->_getParam ( 'campaignId' );
		$this->view->form = $form = new Ynfundraising_Form_SearchSupporters ();
		$params = $this -> _getAllParams();
		if ($campaign_id) {
			$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
			if (! $campaign)
				return $this->_helper->requireAuth->forward ();
			$this->view->campaign = $campaign;
			$params ['campaign'] = $campaign->getIdentity ();
		}
		$this->view->formValues = $params;
		$this->view->supporters = $supporters = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getSupporterPaginator ( $params );
	}
	public function viewAllDonorsAction() {
		$campaign_id = ( int ) $this->_getParam ( 'campaignId' );
		$this->view->form = $form = new Ynfundraising_Form_SearchSupporters ();
		$params = $this -> _getAllParams();
		if ($campaign_id) {
			$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
			if (! $campaign)
				return $this->_helper->requireAuth->forward ();
			$params ['campaign'] = $campaign->getIdentity ();
			$this->view->campaign = $campaign;
		} else {
			$params ['top'] = true;
		}
		$this->view->formValues = $params;
		$this->view->donors = $donors = Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->getDonorPaginator ( $params );
	}
	public function viewStatisticsChartAction() {
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$campaign_id = ( int ) $this->_getParam ( 'campaign_id' );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $campaign_id );
		if (! $campaign || (! $campaign->isOwner ( $viewer ) && ! $viewer->isAdmin ())) {
			return $this->_helper->requireAuth->forward ();
		}
		$this->view->campaign = $campaign;
		$this->view->filterForm = $filterForm = new Core_Form_Admin_Statistics_Filter ();
		$filterForm->removeElement ( 'mode' );
		$filterForm->removeElement ( 'type' );
		$filterForm->submit->setLabel ( $this->view->translate ( "Show" ) );
		$filterForm->period->setAttrib ( 'onchange', 'return processChange($(this).getParent("form"))' );
		$filterForm->chunk->setMultiOptions ( array (
				Zend_Date::DAY => Zend_Registry::get ( 'Zend_Translate' )->_ ( 'By day' )
		) );
	}
	public function chartDataAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );

		// Get params
		$campaign_id = $this->_getParam ( 'campaign_id', 0 );
		$start = $this->_getParam ( 'start', 0 );
		$offset = $this->_getParam ( 'offset', 0 );
		$chunk = $this->_getParam ( 'chunk', 'dd' );
		$period = $this->_getParam ( 'period' );
		$periodCount = $this->_getParam ( 'periodCount', 1 );

		// Validate chunk/period
		if (! $chunk || ! in_array ( $chunk, $this->_periods )) {
			$chunk = Zend_Date::DAY;
		}
		if (! $period || ! in_array ( $period, $this->_periods )) {
			$period = Zend_Date::MONTH;
		}
		if (array_search ( $chunk, $this->_periods ) >= array_search ( $period, $this->_periods )) {
			die ( 'whoops' );
			return;
		}

		// Validate start
		if ($start && ! is_numeric ( $start )) {
			$start = strtotime ( $start );
		}
		if (! $start) {
			$start = time ();
		}

		// Fixes issues with month view
		Zend_Date::setOptions ( array (
				'extend_month' => true
		) );

		// Get timezone
		$timezone = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'core_locale_timezone', 'GMT' );
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if ($viewer && $viewer->getIdentity () && ! empty ( $viewer->timezone )) {
			$timezone = $viewer->timezone;
		}

		// Make start fit to period?
		$startObject = new Zend_Date ( $start );
		$startObject->setTimezone ( $timezone );

		$partMaps = $this->_periodMap [$period];
		foreach ( $partMaps as $partType => $partValue ) {
			$startObject->set ( $partValue, $partType );
		}

		// Do offset
		if ($offset != 0) {
			$startObject->add ( $offset, $period );
		}

		// Get end time
		$endObject = new Zend_Date ( $startObject->getTimestamp () );
		$endObject->setTimezone ( $timezone );
		$endObject->add ( $periodCount, $period );
		$endObject->sub ( 1, Zend_Date::SECOND ); // Subtract one second

		// Get data
		$statsTable = Engine_Api::_ ()->getDbtable ( 'donations', 'ynfundraising' );
		$statsSelect = $statsTable->select ()->where ( 'engine4_ynfundraising_donations.campaign_id = ?', $campaign_id )->where ( 'engine4_ynfundraising_donations.status = 1' )->where ( 'donation_date >= ?', gmdate ( 'Y-m-d H:i:s', $startObject->getTimestamp () ) )->where ( 'donation_date < ?', gmdate ( 'Y-m-d H:i:s', $endObject->getTimestamp () ) )->order ( 'donation_date ASC' );
		$rawData = $statsTable->fetchAll ( $statsSelect );

		// Now create data structure
		$currentObject = clone $startObject;
		$nextObject = clone $startObject;
		$data = array ();
		$dataLabels = array ();
		$cumulative = 0;
		$previous = 0;

		do {
			$nextObject->add ( 1, $chunk );

			$currentObjectTimestamp = $currentObject->getTimestamp ();
			$nextObjectTimestamp = $nextObject->getTimestamp ();

			$data [$currentObjectTimestamp] = $cumulative;

			// Get everything that matches
			$currentPeriodCount = 0;
			foreach ( $rawData as $rawDatum ) {
				$rawDatumDate = strtotime ( $rawDatum->donation_date );
				if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) {
					$currentPeriodCount += $rawDatum->amount;
				}
			}

			// Now do stuff with it
			$data [$currentObjectTimestamp] = $currentPeriodCount;

			$currentObject->add ( 1, $chunk );
		} while ( $currentObject->getTimestamp () < $endObject->getTimestamp () );

		// Reprocess label
		$labelStrings = array ();
		$labelDate = new Zend_Date ();
		foreach ( $data as $key => $value ) {
			$labelDate->set ( $key );
			$labelStrings [] = $this->view->locale ()->toDate ( $labelDate, array (
					'size' => 'short'
			) );
		}

		// Let's expand them by 1.1 just for some nice spacing
		$minVal = min ( $data );
		$maxVal = max ( $data );
		$minVal = floor ( $minVal * ($minVal < 0 ? 1.1 : (1 / 1.1)) / 10 ) * 10;
		$maxVal = ceil ( $maxVal * ($maxVal > 0 ? 1.1 : (1 / 1.1)) / 10 ) * 10;

		// Remove some labels if there are too many
		$xlabelsteps = 1;
		if (count ( $data ) > 10) {
			$xlabelsteps = ceil ( count ( $data ) / 10 );
		}

		// Remove some grid lines if there are too many
		$xsteps = 1;
		if (count ( $data ) > 100) {
			$xsteps = ceil ( count ( $data ) / 100 );
		}

		if ($maxVal < 200) {
			$maxVal = 250;
		}

		// Create base chart
		require_once 'OFC/OFC_Chart.php';

		// Make x axis labels
		$x_axis_labels = new OFC_Elements_Axis_X_Label_Set ();
		$x_axis_labels->set_steps ( $xlabelsteps );
		$x_axis_labels->set_labels ( $labelStrings );
		$x_axis_labels->set_vertical();
		
		// Make x axis
		$labels = new OFC_Elements_Axis_X ();
		$labels->set_labels ( $x_axis_labels );
		$labels->set_colour ( "#416b86" );
		$labels->set_grid_colour ( "#dddddd" );
		$labels->set_steps ( $xsteps );

		// Make y axis
		$yaxis = new OFC_Elements_Axis_Y ();
		$yaxis->set_range ( $minVal, $maxVal/*, $steps*/);
		$yaxis->set_colour ( "#416b86" );
		$yaxis->set_grid_colour ( "#dddddd" );

		// Make data
		$graph = new OFC_Charts_Line ();
		$graph->set_values ( array_values ( $data ) );
		$graph->set_colour ( "#5ba1cd" );

		// Make title
		$locale = Zend_Registry::get ( 'Locale' );
		$translate = Zend_Registry::get ( 'Zend_Translate' );
		$titleStr = $this->view->translate ( 'Donation Statistics' );
		$title = new OFC_Elements_Title ( $titleStr . ': ' . $this->view->locale ()->toDateTime ( $startObject, array('size' => 'short') ) . $this->view->translate ( ' to ' ) . $this->view->locale ()->toDateTime ( $endObject, array('size' => 'short') ) );
		$title->set_style ( "{font-size: 12px;font-weight: bold; margin-bottom: 10px; color: #717171; text-align: left}" );

		// Make full chart
		$chart = new OFC_Chart ();
		$chart->set_bg_colour ( '#ffffff' );

		$chart->set_x_axis ( $labels );
		$chart->add_y_axis ( $yaxis );
		$chart->add_element ( $graph );
		$chart->set_title ( null );

		// Send
		$this->getResponse ()->setBody ( $chart->toPrettyString () );
	}
	public function viewStatisticsListAction() {
		// Check authoraiztion permisstion
		if (! $this->_helper->requireUser ()->isValid ()) {
			return;
		}
		// Prepare data
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$form = new Ynfundraising_Form_StatisticsSearch ();

		// Process form
		$form->isValid ( $this->_getAllParams () );
		$values = array_merge ( $form->getValues (), $this->_getAllParams () );

		if (empty ( $values ['orderby'] ))
			$values ['orderby'] = 'donation_date';
		if (empty ( $values ['direction'] ))
			$values ['direction'] = 'DESC';

		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $values ['campaign_id'] );
		if (! $campaign || (! $campaign->isOwner ( $viewer ) && ! $viewer->isAdmin ())) {
			return $this->_helper->requireAuth->forward ();
		}
		$this->view->formValues = array_filter ( $values );
		$this->view->campaign = $campaign;
		$values ['user_id'] = $viewer->getIdentity ();
		// Get campaign paginator
		$items_count = ( int ) Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynfundraising.page', 10 );
		$paginator = Engine_Api::_ ()->ynfundraising ()->getDonationPaginator ( $values );
		$paginator->setItemCountPerPage ( $items_count );
		$this->view->paginator = $paginator;
		// render
		$this->_helper->content->setEnabled ();
	}
	public function viewStatisticsDetailAction() {
		// Check authoraiztion permisstion
		if (! $this->_helper->requireUser ()->isValid ()) {
			return;
		}
		// Prepare data
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$values = array (
				'donation_id' => $this->_getParam ( 'donation_id' )
		);
		$donation = Engine_Api::_ ()->getItem ( 'ynfundraising_donation', $values ['donation_id'] );
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $donation->campaign_id );
		if (! $campaign || (! $campaign->isOwner ( $viewer ) && ! $viewer->isAdmin ())) {
			return $this->_helper->requireAuth->forward ();
		}
		$transactions = Engine_Api::_ ()->ynfundraising ()->getDonationPaginator ( $values );
		$this->view->transactions = $transactions;
		$this->view->campaign = $campaign;
	}
}