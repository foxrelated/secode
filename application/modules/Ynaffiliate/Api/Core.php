<?php

class Ynaffiliate_Api_Core {

   static protected $_siteUrl;

   /**
    * @param  User_Model_User   $viewer [optional]
    * @return Ynaffiliate_Model_Account
    */
   public function getAccount($viewer = null) {
      if ($viewer == NULL) {
         $viewer = Engine_Api::_()->user()->getViewer();
      }

      if (!is_object($viewer)) {
         return null;
      }

      //the user have not affiliate account
      $model = Engine_Api::_()->getDbTable('accounts', 'ynaffiliate');
      $select = $model->select()->where('user_id=?', $viewer->user_id);
      return $model->fetchRow($select);
   }

   /**
    * @return array [title, affiliate_url]
    */
   public function getSuggestLinks() {
      $result = array();
      $viewer = Engine_Api::_()->user()->getViewer();
      $suggestsTable = Engine_Api::_()->getDbTable('suggests', 'ynaffiliate');
      $enabledModuleNames = Engine_Api::_() -> getDbtable('modules', 'core')->getEnabledModuleNames();
      $select = $suggestsTable -> select() -> where('module IN(?)', $enabledModuleNames);
      $rows = $suggestsTable -> fetchAll($select);

      foreach ($rows as $row) {
         $plugin = 'Ynaffiliate_Plugin_Suggest::getTargetUrl';
         if ($row->plugin) {
            $plugin = $row->plugin;
         }
         list($class, $method) = explode('::', $plugin);
         $object = new $class;
         if ($object) {
            $row->href = $object->{$method}($viewer, $row);
            $result[] = $row->toArray();
         }
      }
      return $result;
   }

   public function getSiteUrl() {
      if (self::$_siteUrl) {
         return self::$_siteUrl;
      }

      $baseUrl = null;

      if (APPLICATION_ENV == 'development') {
         $request = Zend_Controller_Front::getInstance()->getRequest();
         $baseUrl = sprintf('%s://%s', $request->getScheme(), $request->getHttpHost());
         Engine_Api::_()->getApi('settings', 'core')->setSetting('ynaffiliate.baseUrl', $baseUrl);
      } else {
         $baseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.baseUrl', null);
      }

      if ($baseUrl == null) {
         $request = Zend_Controller_Front::getInstance()->getRequest();
         $baseUrl = sprintf('%s://%s', $request->getScheme(), $request->getHttpHost());
         Engine_Api::_()->getApi('settings', 'core')->setSetting('ynaffiliate.baseUrl', $baseUrl);
      }

      self::$_siteUrl = $baseUrl;

      return $baseUrl;
   }

   /**
    * @ex: http://labs.agiliet.net/af/1/mp3-music
    * @param   string  $href  URI: social-store, mp3-music, profile/namnv
    * @param   int     $user_id
    * @return  string  full url
    */
   public function getAffiliateUrl($href, $user_id = null) {
      $href = base64_encode($href);
      $router = Zend_Controller_Front::getInstance()->getRouter();
      if ($user_id == NULL) {
         $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      }
      $click_url = $router->assemble(array('href' => $href, 'user_id' => $user_id), 'ynaffiliate_click', true);
      return $this->getSiteUrl() . $click_url;
   }

   public function getDynamicLinks() {
      $Links = new Ynaffiliate_Model_DbTable_Links;
      $dynamics_select = $Links->select()->where('is_dynamic = 1');
      $dynamics = $Links->fetchAll($dynamics_select);
      return $dynamics;
   }

   public function getRequestsPaginator($params = array()) {
      $paginator = Zend_Paginator::factory($this->getRequestsSelect($params));

      if (!empty($params['page'])) {
         $paginator->setCurrentPageNumber($params['page']);
      }
      if (!empty($params['limit'])) {
         $paginator->setItemCountPerPage($params['limit']);
      }
      return $paginator;
   }

   public function getRequestsSelect($params = array()) {
      $table = new Ynaffiliate_Model_DbTable_Requests;
      $select = $table->select()->setIntegrityCheck(false)
          ->from(array('req' => 'engine4_ynaffiliate_requests'))
          ->join(array('u' => 'engine4_users'), 'u.user_id=req.user_id');


      if (@$params['affiliate_name'] && $params['affiliate_name'] != '') {
         $select->where('u.displayname LIKE ?', '%' . $params['affiliate_name'] . '%');
      }
      if (@$params['request_status'] && $params['request_status'] != '') {
         $select->where("req.request_status = ? ", $params['request_status']);
      }
      if (@$params['From_Date'] && $params['From_Date'] != '') {
         $select->where("req.request_date >= ? ", $params['From_Date']);
      }

      if (@$params['To_Date'] && $params['To_Date'] != '') {
         $params['To_Date'] = $params['To_Date'].' 23:59:59';
         $select->where("req.request_date <= ? ", $params['To_Date']);
      }

      if (@$params['user_id'] && $params['user_id'] != '') {
         $select->where("u.user_id = ?", $params['user_id']);
      }

      if (!empty($params['order']) && $params['order'] != '') {
         $select->order($params['order'] . ' ' . $params['direction']);
      } else {
         $select->order("req.request_date DESC");
      }


      if (getenv('DEVMODE') == 'localdev') {
         print_r($params);
         echo $select;
      }
      //echo $select; die;
      return $select;
   }

   public function getAssocId($new_user_id) {
//      Zend_Registry::get('Zend_Log')->log(print_r('vao getassoc',true), 7);
      $Assoc = new Ynaffiliate_Model_DbTable_Assoc;
      $result = $Assoc->select()->where('new_user_id = ?', $new_user_id)->where('approved = 1');
      return $Assoc->fetchRow($result);
   }

   public function getRelationshipIds($from_user_id, $user_id = null) {
      // view self or not
      if (!$user_id) {
         $viewer = Engine_Api::_()->user()->getViewer();
         $user_id = $viewer->getIdentity();
      }
      $result = array();
      $Assoc = new Ynaffiliate_Model_DbTable_Assoc;

      // track up the assoc tree
      while ($user_id != $from_user_id) {

         // insert first user id as client id, next is middle assoc
         $result[] = $from_user_id;
         $select = $Assoc->select()->where('new_user_id = ?', $from_user_id)->where('approved = 1');
         $assoc = $Assoc->fetchRow($select);
         $from_user_id = $assoc -> user_id;
      }

      return $result;
   }

   public function getAffiliatesPaginator($params = array()) {
      $paginator = Zend_Paginator::factory($this->getAffiliatesSelect($params));

      if (!empty($params['page'])) {
         $paginator->setCurrentPageNumber($params['page']);
      }
      if (!empty($params['limit'])) {
         $paginator->setItemCountPerPage($params['limit']);
      }
      return $paginator;
   }

   public function getAffiliatesSelect($params = array()) {
      $table = Engine_Api::_()->getDbTable('accounts', 'ynaffiliate');
      $select = $table->select();
      if (@$params['name'] && $params['name'] != '') {
         $select->where('contact_name like ?', '%' . $params['name'] . '%');
      }
      if ($params['status'] != '') {
         $select->where('approved=?', $params['status']);
      }
      if (!empty($params['order']) && $params['order'] != '') {
         $select->order($params['order'] . ' ' . $params['direction']);
      } else {
         $select->order("creation_date DESC");
      }

      return $select;
   }

   /**
    * @param  int $user_id
    * @param  int $new_user
    * @param  int $link_id
    */
   public function addAssoc($user_id, $new_user_id, $link_id = 0, $invite_id = 0, $invite_code= '', $invited_date = null) {
      $model = new Ynaffiliate_Model_DbTable_Assoc;
      $assoc = $model->fetchNew();
      $assoc->setFromArray(array(
              'user_id' => $user_id,
              'new_user_id' => $new_user_id,
              'link_id' => $link_id,
              'invite_id' => $invite_id,
              'invite_code' => $invite_code,
              'invited_date' => $invited_date,
          )
      );
      $assoc->save();

      if ($link_id) {
         $link = Engine_Api::_()->getDbTable('Links', 'Ynaffiliate')->find($link_id)->current();
         if (is_object($link)) {
            $link->success_count++;
            $link->last_user_id = $user_id;
            $link->last_registered = date('Y-m-d H:i:s');
            $link->save();
         }
      }
      return $assoc;
   }

   public function getLevelId($user_id) {
      $user = Engine_Api::_()->getItem('user', $user_id);
      $level_id = $user -> level_id;
      return $level_id;
   }

   public function getMyAffiliatesPaginator($params = array()) {
      $paginator = Zend_Paginator::factory($this->getMyAffiliatesSelect($params));

      if (!empty($params['page'])) {
         $paginator->setCurrentPageNumber($params['page']);
      }
      if (!empty($params['limit'])) {
         $paginator->setItemCountPerPage($params['limit']);
      }
      return $paginator;
   }


   public function getMyAffiliatesSelect($params = array()) {
      $table = Engine_Api::_()->getDbTable('commissions', 'ynaffiliate');
      $db = $table->getAdapter();
      $select = $db->select();
      $select->from(array('as' => 'engine4_ynaffiliate_assoc'), array('as.user_id as affiliate_id', '*'))
          ->join(array('a' => 'engine4_ynaffiliate_accounts'), 'as.user_id = a.user_id')
          ->join(array('u' => 'engine4_users'), 'as.new_user_id = u.user_id');

      if (!empty($params['From_Date'])) {
         $select->where("u.creation_date >= ? ", $params['From_Date']);
      }

      if (!empty($params['To_Date'])) {
         $params['To_Date'] = $params['To_Date'].' 23:59:59';
         $select->where("u.creation_date <= ? ", $params['To_Date']);
      }

      if (!empty($params['client_name']))
      {
         $client_name = $params['client_name'];
         $select->where("u.displayname LIKE ? OR u.email LIKE ?", "%{$client_name}%");
      }
      if (!empty($params['affiliate_name']))
      {
         $Users = new User_Model_DbTable_Users;
         $affiliate_name = $params['affiliate_name'];
         $user_select = $Users->select()->where('displayname LIKE ? OR email LIKE ?', "%{$affiliate_name}%");
         $client = $Users->fetchRow($user_select);
         if (count($client) > 0) {
            $select->where('as.user_id = ?', $client->user_id);
         }
      }

      if (!empty($params['user_id'])) {
         $select->where("as.user_id = ?", $params['user_id']);
      }
      $select->where("as.approved = 1");

      if (!empty($params['order']) && $params['order'] != '') {

         $select->order($params['order'] . ' ' . $params['direction']);
      } else {

         $select->order("u.creation_date DESC");
      }
      return $select;
   }

   static public function isSandboxMode() {
      return Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.mode', 1);
   }

   public function getCommissionsPaginator($params = array()) {
      $paginator = Zend_Paginator::factory($this->getCommissionsSelect($params));

      if (!empty($params['page'])) {
         $paginator->setCurrentPageNumber($params['page']);
      }
      if (!empty($params['limit'])) {
         $paginator->setItemCountPerPage($params['limit']);
      }
      return $paginator;
   }

   public function getCommissionsSelect($params = array()) {
      $table = new Ynaffiliate_Model_DbTable_Commissions;
      $select = $table->select()->setIntegrityCheck(false)
          ->from(array('com' => 'engine4_ynaffiliate_commissions'),array('com.creation_date as purchase_date', '*'))
          ->join(array('u' => 'engine4_users'), 'u.user_id=com.user_id')
          ->join(array('rule' => 'engine4_ynaffiliate_rules'), 'com.rule_id=rule.rule_id')
          ->order('com.creation_date desc');

      if (!empty($params['affiliate_name']))
      {
         $affiliate_name = $params['affiliate_name'];
         $select->where('u.displayname LIKE ?', "%{$affiliate_name}%");
      }
      if (!empty($params['client_name']))
      {
         $client_name = $params['client_name'];
         $Users = new User_Model_DbTable_Users;
         $user_select = $Users->select()->from($Users, 'user_id')->where('displayname LIKE ?', '%' . $client_name . '%');

         $userIds = $user_select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
         if (!empty($userIds)) {
            $select->where('com.from_user_id IN (?)', $userIds);
         } else {
            $select->where('com.from_user_id = ?', '');
         }
      }

      if (!empty($params['type'])) {
         $select->where("rule.rule_name = ? ", $params['type']);
      }

      if (!empty($params['approve_stat'])) {
         $select->where("com.approve_stat = ? ", $params['approve_stat']);
      }

      if (!empty($params['From_Date'])) {
         $select->where("com.creation_date >= ? ", $params['From_Date']);
      }

      if (!empty($params['To_Date'])) {
         $params['To_Date'] = $params['To_Date'].' 23:59:59';
         $select->where("com.creation_date <= ? ", $params['To_Date']);
      }

      if (!empty($params['user_id'])) {
         $select->where("u.user_id = ?", $params['user_id']);
      }

      if (getenv('DEVMODE') == 'localdev') {
      }
      return $select;
   }

   public function getLinksPaginator($params = array()) {
      $paginator = Zend_Paginator::factory($this->getLinksSelect($params));

      if (!empty($params['page'])) {
         $paginator->setCurrentPageNumber($params['page']);
      }
      if (!empty($params['limit'])) {
         $paginator->setItemCountPerPage($params['limit']);
      }
      return $paginator;
   }

   public function getLinksSelect($params= array()) {
      $Links = new Ynaffiliate_Model_DbTable_Links;
      $select = $Links->select();
      if (!empty($params['user_id'])) {
         $select->where("user_id = ?", $params['user_id']);
      }

      if (!empty($params['From_Date'])) {
         $select->where("last_click >= ? ", $params['From_Date']);
      }

      if (!empty($params['To_Date'])) {
         $params['To_Date'] = $params['To_Date'].' 23:59:59';
         $select->where("last_click <= ? ", $params['To_Date']);
      }

      if (!empty($params['order'])) {

         $select->order($params['order'] . ' ' . $params['direction']);
      } else {

         $select->order("last_click DESC");
      }
      return $select;
   }

   public function getTargetURL($user_id, $client_id) {

      $table = new Ynaffiliate_Model_DbTable_Links;
      $db = $table->getAdapter();
      $select = $db->select();
      $select->from(array('l' => 'engine4_ynaffiliate_links'), array('*'))
          ->join(array('a' => 'engine4_ynaffiliate_assoc'), 'l.link_id = a.link_id')
          ->where("a.user_id=?", $user_id)
          ->where("a.new_user_id=?", $client_id);
      $link = $db->fetchRow($select);

      return $link['target_url'];
   }

   public function getCommissionPointsByClient($client_id, $affiliate_id) {
      $Commissions = new Ynaffiliate_Model_DbTable_Commissions;
      $select = $Commissions->select()->where('user_id = ?', $affiliate_id)->where('from_user_id = ?', $client_id)->where('approve_stat = "approved"');
      $results = $Commissions->fetchAll($select);
      $points = 0;
      if (count($results) > 0) {
         foreach ($results as $result) {
            $points += $result->commission_points;
         }
      }
      return floor($points);
   }

   public function getCommissionRulesSelect($level_id) {
      $rule_table = Engine_Api::_()->getDbTable('rules', 'ynaffiliate');
      $rulemap_table = Engine_Api::_()->getDbTable('rulemaps', 'ynaffiliate');
      $enabledModuleNames = Engine_Api::_() -> getDbtable('modules', 'core')->getEnabledModuleNames();
      $db = $rule_table->getAdapter();
      $select = $db->select();

      $table1 = $rulemap_table->select()->where('level_id=?', $level_id);
      $select->from(array('r' => 'engine4_ynaffiliate_rules'), array('r.rule_id as ruleid', '*'))
          -> joinleft(array('m' => $table1), 'r.rule_id = m.rule_id')
          -> joinleft(array('d' => 'engine4_ynaffiliate_rulemapdetails'), 'm.rulemap_id =d.rule_map')
          -> where('r.module IN(?)', $enabledModuleNames)
          -> order('r.rule_id asc');
      return $select;
   }

}
