<h2><?php echo $this->translate("Mp3 Music Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
</div>
<?php endif; ?>
<div class='clear'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
       
    </div>
</div>
<script type="text/javascript">
    //<![CDATA[
   $('level_id').addEvent('change', function(){
        window.location.href = en4.core.baseUrl + 'admin/mp3-music/level/'+this.get('value');
    });
    /* $$('input[type=radio]:not([name=moderator]):not([name=is_download])').addEvent('click', function(e){
        $(this).getParent('.form-wrapper').getAllNext(':not([id^=submit])').setStyle('display', ($(this).get('value')>0?'block':'none'));
    });
    $('view-0').addEvent('click', function(){
        $('create-0').click();
    });
    if ($type($('moderator-1'))) {
        $('moderator-1').addEvent('click', function(){
            $('create-1').click();
            $('view-1').click();
        });
    }
    window.addEvent('domready', function(){
        //if ($type(console))
        //    console.log('create-0: %o | view-0: %o', $('create-0').get('checked'), $('view-0').get('checked'));
        if ($('create-0').get('checked'))
            $('create-0').getParent('.form-wrapper').getAllNext(':not([id^=submit])').hide();
        //$$('#max_songs-wrapper, #max_filesize-wrapper, #max_storage-wrapper, #auth_view-wrapper, #auth_comment-wrapper').hide();
        if ($('view-0').get('checked'))
            $('view-0').getParent('.form-wrapper').getAllNext(':not([id^=submit])').hide();
    });*/
    //]]>
    function checkIt(evt) {
        evt = (evt) ? evt : window.event
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            status = "This field accepts numbers only."
            return false
        }
        status = ""
        return true
    }
</script>
<style type="text/css">

.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
}
</style>