<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: featured deals
 * @author     Minh Nguyen
 */
class Groupbuy_Widget_FeaturedDealsController extends Groupbuy_Content_Widget_Detail {
	public function indexAction() {

		$cur_time = Groupbuy_Api_Core::getCurrentServerTime();
		$where = " status in (20,30) AND published = 20 AND featured=1 AND stop = 0 and is_delete = 0 and start_time <= '$cur_time' and end_time >= '$cur_time'";

		$data = Groupbuy_Model_Deal::getDeals($where, 'rand()', 1);
		if (count($data) > 0) {
			$data = $data[0];
			$_SESSION['yngroupbuy_featured'] = $data -> deal_id;
			if ($data -> status == 20 && $data -> start_time <= date('Y-m-d H:i:s')) {
				$data -> status = 30;
				$data -> save();
			}
			$this -> view -> deal = $data;
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$this -> view -> owner = $owner = Engine_Api::_() -> getItem('user', $data -> user_id);
			// check can rate
			if (!$viewer -> getIdentity())
				$this -> view -> can_rate = $can_rate = 0;
			else
				$this -> view -> can_rate = $can_rate = Engine_Api::_() -> groupbuy() -> canRate($data, $viewer -> getIdentity());
			if ($data -> photo_id) {
				$this -> view -> main_photo = $data -> getPhoto($data -> photo_id);
			}
			$the_countdown_date = $data -> end_time;
			$date = $the_countdown_date;
			$difference = strtotime($date) - time();
			$this -> view -> difference = $difference;
			// album material
			$this -> view -> album = $album = $data -> getSingletonAlbum();
			$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
			$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
			$paginator -> setItemCountPerPage(100);
			// Load fields view helpers
			$view = $this -> view;
			$view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
			$this -> view -> fieldStructure = $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($data);
		} else {
			$_SESSION['yngroupbuy_featured'] = 0;
			$this -> view -> deal = "";
		}
	}

}
?>