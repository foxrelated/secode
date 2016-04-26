<?php
/**
 * Created by IntelliJ IDEA.
 * User: macpro
 * Date: 7/21/15
 * Time: 5:06 PM
 */
class Ynmobile_Helper_Ynbusinesspages_Location extends Ynmobile_Helper_Base
{

    /**

     */

    // not
    public function field_id()
    {
        $this->data['iLocationId'] = $this->entry->location_id;
    }

    public function field_detail()
    {
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_latlon();
        $this->data['iBusinessId'] = $this->entry->business_id;
        $this->data['sLocation'] = $this->entry->location;
        $this->data['bIsMain'] = $this->entry->main;
    }

    function field_latlon(){
        $this->data['fLatitude'] =  $this->entry->latitude;
        $this->data['fLongitude'] = $this->entry->longitude;
    }
}