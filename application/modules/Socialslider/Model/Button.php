<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Button
 *
 * @author isabek
 */
class Socialslider_Model_Button extends Core_Model_Item_Abstract {

    public function __toString() {
        $return = '';

        if ($this->button_type == 'facebook') {
            $return = '<iframe src="//www.facebook.com/plugins/likebox.php?href=' . $this->button_code . '&amp;width=300&amp;height=360&amp;show_faces=true&amp;colorscheme=light&amp;stream=false&amp;border_color&amp;header=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:360px;" allowTransparency="true"></iframe>';
        } elseif ($this->button_type == 'twitter') {
            $return = "<script src='http://widgets.twimg.com/j/2/widget.js'></script> 
                      <script>
                        new TWTR.Widget({
                        version: 2, type: 'profile', rpp: 5, interval: 10000, width: 300, height: 270, 
                        theme: { shell: { background: '#".$this->button_color."', color: '#ffffff'},
                        tweets: { background: '#ffffff', color: '#000000', links: '#eb0707' } },
                        features: { scrollbar: true, loop: true, live: true, hashtags: true, timestamp: false, avatars: false, behavior: 'default'}}).render().setUser('" . $this->button_code . "').start(); 
                      </script> 
                      ";
        } elseif ($this->button_type == 'gplus') {
            $return = '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
                       <g:plus href="https://plus.google.com/' . $this->button_code . '"></g:plus>';
        } elseif ($this->button_type == 'youtube') {
            $return = '<iframe id="fr" src="http://www.youtube.com/subscribe_widget?p=' . $this->button_code . '" style="overflow: hidden; height: 104px; width: 300px; border: 0px;" scrolling="no" frameborder="0" class=""></iframe>';
        } elseif ($this->button_type == 'custom') {
            $return = $this->button_code;
        }

        return $return;
    }
    
    public function getFileUrl(){
        $table = Engine_Api::_()->getDbtable('files', 'storage');
        
        $file = $table->getFile($this->picture_path, $this->getType());
        if ($file) {
            return $file->map();
        } else {
            
            $picture = 'application/modules/Socialslider/externals/images/' . $this->button_type . '.png';
            
            if ($this->button_default == 1 && file_exists($picture)) {
                $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                if($view !== NULL){
                    $picture = $view->layout()->staticBaseUrl . $picture;
                }
                return $picture;
            } else {
                return NULL;
            }
        }
        
        return NULL;
        
    }

}

?>
