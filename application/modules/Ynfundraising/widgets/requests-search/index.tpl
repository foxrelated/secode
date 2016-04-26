<link type="text/css" href="application/modules/Ynfundraising/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script src="application/modules/Ynfundraising/externals/scripts/jquery-1.7.1.min.js"></script>
<script src="application/modules/Ynfundraising/externals/scripts/jquery-ui-1.8.17.custom.min.js"></script>

<script type="text/javascript">
    jQuery.noConflict();
    jQuery(document).ready(function(){
        // Datepicker
        jQuery('#start_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynfundraising/externals/images/calendar.png',
            buttonImageOnly: true
        });
        jQuery('#end_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynfundraising/externals/images/calendar.png',
            buttonImageOnly: true
        });

    });


</script>
<div>
  <?php echo $this->form->render($this) ?>
</div>
