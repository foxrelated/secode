<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Comments.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_Model_DbTable_Comments extends Core_Model_DbTable_Comments {

    protected $_rowClass = 'Nestedcomment_Model_Comment';
    protected $_serializedColumns = array('params');
    protected $_custom = false;
    protected $_name = 'core_comments';

    public function updateComment(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster, $body) {
        $table = $this->getCommentTable();
        $row = $table->createRow();

        if (isset($row->resource_type)) {
            $row->resource_type = $resource->getType();
        }

        $row->resource_id = $resource->getIdentity();
        $row->poster_type = $poster->getType();
        $row->poster_id = $poster->getIdentity();
        $row->creation_date = date('Y-m-d H:i:s');
        $row->body = $body;
        $row->save();

        return $row;
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments($subject) {
        return new Engine_ProxyObject($subject, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    public function getReplyCount($subject, $comment_id) {
        $commentSelect = $subject->comments()->getCommentSelect('DESC');

        $commentSelect->where('parent_comment_id =?', $comment_id);

        $reply_count = count($commentSelect->query()->fetchAll());
        return $reply_count;
    }

}
