<?php

class Ynmobile_Helper_Ynresume_Publication extends Ynmobile_Helper_Base{

    public function field_id(){
        $this->data['iPublicationId'] = $this->entry->getIdentity();
    }

    public function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_timestamp();
        $this->field_resume_photos();

        $item = $publication = $this->entry;

        $this->data['sPublisher'] = $item->publisher;
        $this->data['sUrl'] = $item->url;
        $this->data['sPublicationDate'] = $item->publication_date;

        $authors = $item -> getAuthorObjects();
        $authors_arr = array();
        foreach ($authors as $author) {
            if ($author->user_id > 0) {
                $user = Engine_Api::_()->getItem('user', $author->user_id);
                $authors_arr[] = Ynmobile_AppMeta::_export_one($user, array('simple_array'));
            } else {
                $authors_arr[] = array(
                    'id'=>0,
                    'title'=>$author->name
                );
            }
        }

        $this->data['aAuthors'] = $authors_arr;
        $this->data['sDescription'] = $publication->description;
    }
}
