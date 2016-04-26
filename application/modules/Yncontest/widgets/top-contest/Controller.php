<?php
class Yncontest_Widget_TopContestController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){		
		
		$table = Engine_Api::_()->getDbtable('contests', 'yncontest');
        $Name = $table->info('name');
		$limit = $this->_getParam('number',5);
		
		$select = $table->select()->from($Name,"$Name.*,
				(SELECT COUNT(*) FROM engine4_yncontest_membership WHERE engine4_yncontest_membership.resource_id = $Name.contest_id GROUP BY engine4_yncontest_membership.resource_id )AS participants
				,(SELECT COUNT(*) FROM engine4_yncontest_entries en WHERE en.contest_id = $Name.contest_id) AS entries
				,(TIMESTAMPDIFF(YEAR,now(),$Name.end_date)) AS yearleft				
				,(TIMESTAMPDIFF(MONTH,now(),$Name.end_date)) AS monthleft
				,(TIMESTAMPDIFF(DAY,now(),$Name.end_date)) AS dayleft				
				,(TIME_FORMAT(TIMEDIFF(engine4_yncontest_contests.end_date,now()),'%H')) AS hourleft			
				,(TIME_FORMAT(TIMEDIFF(engine4_yncontest_contests.end_date,now()),'%i')) AS minuteleft						
				");
		$select -> where('contest_status=?', 'published') 
				-> where('approve_status=?', 'approved')
				-> order('like_count desc')
				->limit($limit);

		$this -> view -> items = $items = $table -> fetchAll($select);
		if(count($items)==0) {
			$this -> setNoRender();
		}	
	}
}
