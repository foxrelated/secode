<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: profile.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript">
  var srtabContainerSwitch = window.srtabContainerSwitch= function(element) {
    if( element.tagName.toLowerCase() == 'a' ) {
      element = element.getParent('li');
    }
    var myContainer = element.getParent('.tabs_parent').getParent();
    myContainer.getElements('ul > li').removeClass('active');
    element.get('class').split(' ').each(function(className){
      className = className.trim();
      if( className.match(/^tab_[0-9]+$/) ) {
        element.addClass('active');
        window.location.hash='sr_sitestoreproduct_'+className;
      }
    });
  }
  en4.core.runonce.add(function() {
    var myContainer = $('main_tabs').getParent('.tabs_parent').getParent();
    myContainer.getChildren('div:not(.tabs_alt)').setStyle('display', null);
    myContainer.getChildren('div:not(.tabs_alt)').each(function(el){
      el.get('class').split(' ').each(function(className){
        className = className.trim();
        if( className.match(/^tab_[0-9]+$/) ) {
          var liContent=$('main_tabs').getElement('li.' + className);
          var link=liContent.getElement('a');
            if(link==null)
              link=liContent;
          var onClick=link.get('onclick');
          if(onClick){
            link.set('onclick','sr'+onClick);
          }
          var html= link.get('html');
          new Element('h3',{
            'id':'sr_sitestoreproduct_'+className,
            'class':'sr_sitestoreproduct_tab_heading',
            'html':link.get('html')
          }).inject(el, 'top');
         
        }
      });
    });
  });
</script>
