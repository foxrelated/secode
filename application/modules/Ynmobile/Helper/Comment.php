<?php

class Ynmobile_Helper_Comment extends Ynmobile_Helper_Base{

    function getYnmobileApi(){
        return Engine_Api::_()->getApi('comment','ynmobile');
    }

    function field_id(){
        $this->data['iCommentId'] = $this->entry->getIdentity();
    }

    function field_content(){
        $this->data['sContent'] = $this->entry->body;
    }

    // $fields = explode(',','id,type,content,user,stats');
    function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_content();
        $this->field_canLike();
        $this->field_canDelete();
        $this->field_canEdit();
        $this->field_timestamp();
        $this->field_liked();
        $this->field_disliked();
        $this->field_totalLike();
        $this->field_user();
        $this->field_parentCommentId();
        $this->field_totalDislike();
        $this->field_canHide();
        $this->field_canReport();
        $this->field_attachments();
    }

    function field_canLike(){
        // $viewer = Engine_Api::_() -> user() -> getViewer();
//        $bCanComment  = (Engine_Api::_() -> authorization() -> isAllowed($this->entry, null, 'comment')) ? 1 : 0;
        $this->data['bCanLike'] = 1;
    }

    function field_canDelete(){

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $comment = $this->entry;
        $deletable = 1;
//        $editable =  (Engine_Api::_() -> authorization() -> isAllowed($this->entry, null, 'edit')) ? 1 : 0;;
//        $isPoster = $this->entry ->getPoster()->isSelf($viewer);

        if ($this->entry->isOwner($viewer)) {
            $this->data['bCanDelete'] = 1;
        } else {
            $this->data['bCanDelete'] = 0;
        }

        if( $comment->getType() == 'core_comment' ) {
            $type = $comment->resource_type;
            $id = $comment->resource_id;
        } else {
            $type = $comment->getType();
            $id = $comment->getIdentity();
        }
        $item = Engine_Api::_()->getItem($type, $id);

        if( !$item->authorization()->isAllowed($viewer, 'edit') &&
            ($comment->poster_type != $viewer->getType() ||
                $comment->poster_id != $viewer->getIdentity()) ) {
            $deletable = 0;
            return;
        }

        $this->data['bCanDelete'] = $deletable;
    }

    function field_canEdit(){

        $viewer = Engine_Api::_() -> user() -> getViewer();

        $this->data['bCanEdit'] =$this->entry->isOwner($viewer)?1:0;
    }

    function field_detail(){
        $this->field_listing();
    }

    function field_parentCommentId(){
        $this->data['iParentCommentId'] = '';
        if (Engine_Api::_()->hasModuleBootstrap('yncomment')) {
            $this->data['iParentCommentId'] = $this->entry->parent_comment_id;
        }
    }

    function field_totalDislike(){
        $this->data['iTotalDislike'] = 0;

        if(Engine_Api::_()->hasModuleBootstrap('yncomment')){
            $resource_type = $this->entry->getType();
            $resource_id = $this->entry->getIdentity();
            $this->data['iTotalDislike'] = Engine_Api::_()->getApi('dislike', 'yncomment')->dislikeCount($resource_type, $resource_id, 0);
        }
    }

    function field_disliked(){
        if (Engine_Api::_()->hasModuleBootstrap('yncomment')) {
            $this->data['bIsDisliked'] = Engine_Api::_()->getDbtable('dislikes', 'yncomment')->isDislike($this->entry, $this->getViewer()) ? 1 : 0;
        }
    }

    function field_attachments(){

        $this->data['aAttachments'] = array();

        if(!Engine_Api::_()->hasModuleBootstrap('yncomment')){
            return false;
        }

        $comment = $this -> entry;

        if ($comment -> attachment_type){
            $attachment = Engine_Api::_() -> getItem($comment -> attachment_type, $comment -> attachment_id);
            $attachmentData = Ynmobile_AppMeta::_export_one($attachment, array('as_attachment'));
            $this->data['aAttachments'][] = $attachmentData;
        }
    }

    function field_canHide(){

        $editable =  (Engine_Api::_() -> authorization() -> isAllowed($this->entry, null, 'edit')) ? 1 : 0;;

        $this->data['bCanHide'] = $editable?0:1;
    }

    function field_canReport(){

        $viewer = Engine_Api::_() -> user() -> getViewer();

        $comment = $this->entry;
        $deletable = 1;
//        $editable =  (Engine_Api::_() -> authorization() -> isAllowed($this->entry, null, 'edit')) ? 1 : 0;;
//        $isPoster = $this->entry ->getPoster()->isSelf($viewer);

        if ($this->entry->isOwner($viewer)) {
            $this->data['bCanReport'] = 0;
        } else {
            $this->data['bCanReport'] = 1;
        }


        if( $comment->getType() == 'core_comment' ) {
            $type = $comment->resource_type;
            $id = $comment->resource_id;
        } else {
            $type = $comment->getType();
            $id = $comment->getIdentity();
        }
        $item = Engine_Api::_()->getItem($type, $id);

        if( !$item->authorization()->isAllowed($viewer, 'edit') &&
            ($comment->poster_type != $viewer->getType() ||
                $comment->poster_id != $viewer->getIdentity()) ) {
            $deletable = 0;
            return;
        }

        $this->data['bCanReport'] = $deletable ? 0 : 1;
    }
}
