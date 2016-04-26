<?php

class Ynmobile_Helper_Ynresume_Language extends Ynmobile_Helper_Base{

    public function field_id(){
        $this->data['iLanguageId'] = $this->entry->getIdentity();
    }

    public function field_listing(){
        $this->field_id();
        $this->field_type();

        $item = $language = $this->entry;
        $this->data['sTitle'] = $item->name;
        $view = Zend_Registry::get('Zend_View');

        $proficiencyArr = array(
            'elementary' => $view -> translate('Elementary'),
            'limited working' => $view -> translate('Limited Working'),
            'professional working' => $view -> translate('Professional Working'),
            'fill working' => $view -> translate('Fill Working'),
            'native or bilingual' => $view -> translate('Native or Bilingual')
        );

        $this->data['sProficiency'] = $proficiencyArr[$item->proficiency];
    }
}
