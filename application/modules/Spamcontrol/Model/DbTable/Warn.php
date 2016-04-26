<?php

class Spamcontrol_Model_DbTable_Warn extends Engine_Db_Table {

    protected $_rowClass = 'Spamcontrol_Model_Warn';

    public function getContentPaginator($params = array()) {


        switch ($params['item']) {
            case 'core_comment':
                $select = $this->getCommentSelect($params);
                break;
            case 'messages_conversation':
                $select = $this->getMessageSelect($params);
                break;
            case 'album_photo':
                $select = $this->getPhotoSelect($params);
                break;
            case 'activity_action';
                $select = $this->getPostSelect($params);
                break;
            case 'blog';
                $select = $this->getBlogSelect($params);
                break;
        }
        $paginator = Zend_Paginator::factory($select);
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('spamcontrol.page', 30);
        $paginator->setItemCountPerPage($items_count);

        return $paginator;
    }

    public function getMessageSelect($params = array()) {
        $contantTable = Engine_Api::_()->getItemTable($params['item']);
        $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
        
        $userTable = Engine_Api::_()->getItemTable('user');
        $select = $contantTable->select()
                ->from(array('t1' => $contantTable->info('name')))
                ->joinLeft(array('t2' => $userTable->info('name')), "`t2`.`user_id` = `t1`.`user_id`", null)
                ->joinLeft(array('t3' => $this->info('name')), "`t3`.`user_id` = `t1`.`user_id`", null)
                ->joinRight(array('t4' => $rName), 't4.conversation_id = t1.conversation_id', null)
              //  ->where('t4.inbox_deleted =?', 0)
                ->where('t4.outbox_deleted =?', 0)
                ->where('t2.level_id <> ?', 1)
                ->where('t2.level_id <> ?', 2)
                ->group('t1.conversation_id')
        ;


        if (!empty($params['sort_by'])) {
            switch ($params['sort_by']) {
                case 'date':

                    $select->order('t1.modified DESC');

                    break;
                case 'user': $select->order('t2.displayname ASC');
                    break;
                case 'warn': $select->order('t3.count DESC');
                    break;
            }
        } else {

            $select->order('t1.modified DESC');
        }

        if (!empty($params['showen'])) {
            switch ($params['showen']) {
                case 0:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30);
                    $select->where("t1.modified >?", $date);
                    break;
                case 1:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30 * 12);
                    $select->where("t1.modified >?", $date);
                    break;
                case 2:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 7);
                    $select->where("t1.modified >?", $date);
                    break;
                case 3:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60);
                    $select->where("t1.modified >?", $date);
                    break;
            }
        }

        if (!empty($values['text'])) {
            if ($values['search'] == 1) {
                $select->where("`{$messageTableName}`.`body` LIKE ?", "%{$values['text']}%");
            } else {
                $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$values['text']}%");
            }
        }
        if (!empty($values['url'])) {
            $url = array('http://', 'www', 'http://www', '.com', '.net', '.org', '.cn', '.in');
            $str = "(`{$postTableName}`.`body` LIKE '%http://%') OR ({$postTableName}`.`body` LIKE '%www%')";
            for ($i = 0; $i < count($url); $i++) {
                if ($i == 0) {
                    $select->where("`{$postTableName}`.`body` LIKE ?", "%{$url[0]}%");
                }
                if ($i > 0) {
                    $select->orWhere("`{$postTableName}`.`body` LIKE ?", "%{$url[$i]}%");                    
                }
            }
        }


        return $select;
    }

    public function getBlogSelect($params = array()) {
        $contantTable = Engine_Api::_()->getItemTable($params['item']);

        $userTable = Engine_Api::_()->getItemTable('user');




        $select = $contantTable->select()
                ->setIntegrityCheck(false)
                ->from(array('t1' => $contantTable->info('name')))
                ->joinLeft(array('t2' => $userTable->info('name')), "`t2`.`user_id` = `t1`.`owner_id`", null)
                ->joinLeft(array('t3' => $this->info('name')), "`t3`.`user_id` = `t1`.`owner_id`", array('count' => 'Count(t3.user_id)'))
                ->where('t2.level_id <> ?', 1)
                ->where('t2.level_id <> ?', 2)
                ->group('t1.blog_id')
        ;


        if (!empty($params['sort_by'])) {
            switch ($params['sort_by']) {
                case 'date':

                    $select->order('t1.creation_date DESC');

                    break;
                case 'user': $select->order('t2.displayname ASC');
                    break;
                case 'warn': $select->order('t3.count DESC');
                    break;
            }
        } else {

            $select->order('t1.creation_date DESC');
        }

        if (!empty($params['showen'])) {
            switch ($params['showen']) {
                case 0:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30);
                    $select->where("t1.creation_date >?", $date);
                    break;
                case 1:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30 * 12);
                    $select->where("t1.creation_date >?", $date);
                    break;
                case 2:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 7);
                    $select->where("t1.creation_date >?", $date);
                    break;
                case 3:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60);
                    $select->where("t1.creation_date >?", $date);
                    break;
            }
        }


        if (!empty($params['text'])) {
            if ($params['search'] == 1) {
                $select->where('t1.body LIKE ?', "%{$params['text']}%");
            } else {
                $select->where("(t2.`username` LIKE ? || t2.`displayname` LIKE ?)", "%{$params['text']}%");
            }
        }

        if (!empty($params['plugins'])) {
            $select->where('t1.resource_type LIKE ?', "%{$params['plugins']}%");
        }

        if (!empty($params['url'])) {
            $url = array('http://', 'www', 'http://www', '.com', '.net', '.org', '.cn', '.in');

            for ($i = 0; $i < count($url); $i++) {
                if ($i == 0) {
                    $select->where("`t1`.`body` LIKE ?", "%{$url[0]}%");
                }
                if ($i > 0) {
                    $select->orWhere("`t1`.`body` LIKE ?", "%{$url[$i]}%");
                }
            }
        }
        return $select;
    }

    public function getPhotoSelect($params = array()) {

        $contantTable = Engine_Api::_()->getItemTable($params['item']);

        $userTable = Engine_Api::_()->getItemTable('user');




        $select = $contantTable->select()
                ->setIntegrityCheck(false)
                ->from(array('t1' => $contantTable->info('name')))
                ->joinLeft(array('t2' => $userTable->info('name')), "`t2`.`user_id` = `t1`.`owner_id`", null)
                ->joinLeft(array('t3' => $this->info('name')), "`t3`.`user_id` = `t1`.`owner_id`", null)
                ->where('t2.level_id <> ?', 1)
                ->where('t2.level_id <> ?', 2)
                ->group('t1.photo_id')
        ;


        if (!empty($params['sort_by'])) {
            switch ($params['sort_by']) {
                case 'date':

                    $select->order('t1.creation_date DESC');

                    break;
                case 'user': $select->order('t2.displayname ASC');
                    break;
                case 'warn': $select->order('t3.count DESC');
                    break;
            }
        } else {

            $select->order('t1.creation_date DESC');
        }

        if (!empty($params['showen'])) {
            switch ($params['showen']) {
                case 0:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30);
                    $select->where("t1.creation_date >?", $date);
                    break;
                case 1:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30 * 12);
                    $select->where("t1.creation_date >?", $date);
                    break;
                case 2:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 7);
                    $select->where("t1.creation_date >?", $date);
                    break;
                case 3:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60);
                    $select->where("t1.creation_date >?", $date);
                    break;
            }
        }


        if (!empty($params['text'])) {
            if ($params['search'] == 1) {
                $select->where('t1.body LIKE ?', "%{$params['text']}%");
            } else {
                $select->where("(t2.`username` LIKE ? || t2.`displayname` LIKE ?)", "%{$params['text']}%");
            }
        }

        if (!empty($params['plugins'])) {
            $select->where('t1.resource_type LIKE ?', "%{$params['plugins']}%");
        }

        if (!empty($params['url'])) {
            $url = array('http://', 'www', 'http://www', '.com', '.net', '.org', '.cn', '.in');

            for ($i = 0; $i < count($url); $i++) {
                if ($i == 0) {
                    $select->where("`t1`.`body` LIKE ?", "%{$url[0]}%");
                }
                if ($i > 0) {
                    $select->orWhere("`t1`.`body` LIKE ?", "%{$url[$i]}%");
                }
            }
        }
        return $select;
    }

    public function getPostSelect($params = array()) {
        $contantTable = Engine_Api::_()->getItemTable($params['item']);

        $userTable = Engine_Api::_()->getItemTable('user');




        $select = $contantTable->select()
                ->setIntegrityCheck(false)
                ->from(array('t1' => $contantTable->info('name')))
                ->joinLeft(array('t2' => $userTable->info('name')), "`t2`.`user_id` = `t1`.`subject_id`", null)
                ->joinLeft(array('t3' => $this->info('name')), "`t3`.`user_id` = `t1`.`subject_id`", array('count' => 'Count(t3.user_id)'))
                ->where('t1.object_type =?', 'user')
                ->where('t1.type IN (?)', new Zend_Db_Expr("'status', 'post'"))
                ->where('t2.level_id <> ?', 1)
                ->where('t2.level_id <> ?', 2)
                ->group('t1.action_id')
        ;


        if (!empty($params['sort_by'])) {
            switch ($params['sort_by']) {
                case 'date':

                    $select->order('t1.date DESC');

                    break;
                case 'user': $select->order('t2.displayname ASC');
                    break;
                case 'warn': $select->order('t3.count DESC');
                    break;
            }
        } else {

            $select->order('t1.date DESC');
        }

        if (!empty($params['showen'])) {
            switch ($params['showen']) {
                case 0:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30);
                    $select->where("t1.date >?", $date);
                    break;
                case 1:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30 * 12);
                    $select->where("t1.date >?", $date);
                    break;
                case 2:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 7);
                    $select->where("t1.date >?", $date);
                    break;
                case 3:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60);
                    $select->where("t1.date >?", $date);
                    break;
            }
        }


        if (!empty($params['text'])) {
            if ($params['search'] == 1) {
                $select->where('t1.body LIKE ?', "%{$params['text']}%");
            } else {
                $select->where("(t2.`username` LIKE ? || t2.`displayname` LIKE ?)", "%{$params['text']}%");
            }
        }

        if (!empty($params['plugins'])) {
            $select->where('t1.resource_type LIKE ?', "%{$params['plugins']}%");
        }

        if (!empty($params['url'])) {
            $url = array('http://', 'www', 'http://www', '.com', '.net', '.org', '.cn', '.in');

            for ($i = 0; $i < count($url); $i++) {
                if ($i == 0) {
                    $select->where("`t1`.`body` LIKE ?", "%{$url[0]}%");
                }
                if ($i > 0) {
                    $select->orWhere("`t1`.`body` LIKE ?", "%{$url[$i]}%");
                }
            }
        }
        return $select;
    }

    public function getCommentSelect($params = array()) {

        $contantTable = Engine_Api::_()->getItemTable($params['item']);

        $userTable = Engine_Api::_()->getItemTable('user');




        $select = $contantTable->select()
                ->setIntegrityCheck(false)
                ->from(array('t1' => $contantTable->info('name')))
                ->joinLeft(array('t2' => $userTable->info('name')), "`t2`.`user_id` = `t1`.`poster_id`", null)
                ->joinLeft(array('t3' => $this->info('name')), "`t3`.`user_id` = `t1`.`poster_id`", array('count' => 'Count(t3.user_id)'))
                ->where('t2.level_id <> ?', 1)
                ->where('t2.level_id <> ?', 2)
                ->group('t1.comment_id')
        ;


        if (!empty($params['sort_by'])) {
            switch ($params['sort_by']) {
                case 'date':

                    $select->order('t1.creation_date DESC');

                    break;
                case 'user': $select->order('t2.displayname ASC');
                    break;
                case 'warn': $select->order('t3.count DESC');
                    break;
            }
        } else {

            $select->order('t1.creation_date DESC');
        }

        if (!empty($params['showen'])) {
            switch ($params['showen']) {
                case 0:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30);
                    $select->where("t1.creation_date >?", $date);
                    break;
                case 1:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30 * 12);
                    $select->where("t1.creation_date >?", $date);
                    break;
                case 2:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 7);
                    $select->where("t1.creation_date >?", $date);
                    break;
                case 3:
                    $date = date('Y-m-d H:i:s', time() - 60 * 60);
                    $select->where("t1.creation_date >?", $date);
                    break;
            }
        }


        if (!empty($params['text'])) {
            if ($params['search'] == 1) {
                $select->where('t1.body LIKE ?', "%{$params['text']}%");
            } else {
                $select->where("(t2.`username` LIKE ? || t2.`displayname` LIKE ?)", "%{$params['text']}%");
            }
        }

        if (!empty($params['plugins'])) {
            $select->where('t1.resource_type LIKE ?', "%{$params['plugins']}%");
        }

        if (!empty($params['url'])) {
            $url = array('http://', 'www', 'http://www', '.com', '.net', '.org', '.cn', '.in');

            for ($i = 0; $i < count($url); $i++) {
                if ($i == 0) {
                    $select->where("`t1`.`body` LIKE ?", "%{$url[0]}%");
                }
                if ($i > 0) {
                    $select->orWhere("`t1`.`body` LIKE ?", "%{$url[$i]}%");
                }
            }
        }
  
        return $select;
    }

    public function getWarnCount(Core_Model_Item_Abstract $user) {
        $count = $this->select()
                ->from($this->info('name'), new Zend_Db_Expr('Count(*)'))
                ->where('user_id = ?', $user->getIdentity())
                ->query()
                ->fetchColumn()
        ;

        return $count;
    }
    
    public function getUserWarn(Core_Model_Item_Abstract $user){
        $select = $this->select()->where('user_id =?', $user->getIdentity());
        return $this->fetchAll($select);
    }

    public function deleteAll(Core_Model_Item_Abstract $item) {
        $table = $item->getTable();

        $select = $table->select()->limit(10);

        switch ($item->getType()) {
            case 'core_comment':
                $select->where('poster_id = ?', $item->getOwner()->getIdentity());
                break;
            case 'blog':
                $select->where('owner_id = ?', $item->getOwner()->getIdentity());
                break;
            case 'album_photo':
                $select->where('owner_id = ?', $item->getOwner()->getIdentity());
                break;
            case 'messages_conversation':
                $select->where('user_id = ?', $item->getOwner()->getIdentity());
                break;
             case 'activity_action':
                $select->where('subject_id = ?', $item->getOwner()->getIdentity());
                break;
            case 'activity_comment':
                $select->where('poster_id = ?', $item->getOwner()->getIdentity());
                break;
            default :
                $select->limit(0);
        }

        $items = $table->fetchAll($select);
        foreach ($items as $content) {
            $content->delete();
        }
    }

    public function setWarn($values = array(), $delete = null) {
        $row = $this->createRow();
        $row->setFromArray($values);
        $row->save();
        $item = Engine_Api::_()->getItem($values['resource_type'], $values['resource_id']);
        
        if (!empty($delete)) {
            $item = Engine_Api::_()->getItem($values['resource_type'], $values['resource_id']);
            $this->deleteAll($item);
        }
        
        $viewer = Engine_Api::_()->user()->getViewer();
        
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

                $notifyApi->addNotification($item->getOwner(), $viewer, $item, 'user_warn');
    }

}

?>
