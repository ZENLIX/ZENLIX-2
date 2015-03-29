
<div id="footer" style=" padding-bottom: 70px; ">
    <div class="" style=" padding: 20px; ">
        <div class="col-md-12">
            <p class="text-muted credit pull-right"><small>Developed by <a href="http://zenlix.com/">ZENLIX</a> (с) <?=date('Y');?>.</p>
            </small>
        </div>


    </div>
</div>



</aside><!-- /.right-side -->
</div>     
                
            
<?php
if ($lang == "ua") {
    $lang = "uk";
} ?>
<script type="text/javascript">
    var MyHOSTNAME = "<?php
echo $CONF['hostname']; ?>";
    var MyLANG = "<?php
echo $lang; ?>";
    var USER_HASH = "<?php echo get_user_val('uniq_id'); ?>";
    var NODE_URL = "<?php echo get_conf_param('node_port'); ?>";
    var USER_noty_layot="<?=get_user_val_by_id($_SESSION['helpdesk_user_id'], 'noty_layot');?>";
</script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery-2.1.3.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap/js/bootstrap.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/app.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.titlealert.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/noty/packaged/jquery.noty.packaged.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/ion.sound.min.js?<?=get_conf_param('version');?>"></script>

<!--script src="<?php echo $CONF['hostname'] ?>js/moment-with-locales.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/moment-timezone-with-data-2010-2020.min.js"></script-->

<script src="<?php echo $CONF['hostname'] ?>js/moment.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/moment-timezone-with-data-2010-2020.min.js?<?=get_conf_param('version');?>"></script>


<script src="<?php echo $CONF['hostname'] ?>js/moment-with-langs.js?<?=get_conf_param('version');?>"></script>

<script src="<?php echo $CONF['hostname'] ?>js/jquery-ui-1.10.4.custom.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/chosen.jquery.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootbox.min.js?<?=get_conf_param('version');?>"></script>


<?php
if (get_current_URL_name('create') || get_current_URL_name('scheduler') ) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.ui.autocomplete.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/daterangepicker.js?<?=get_conf_param('version');?>"></script>
<?php
} ?>

<?php
if ((get_current_URL_name('create')) || get_current_URL_name('deps') || get_current_URL_name('scheduler')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap3-editable/js/bootstrap-editable.min.js?<?=get_conf_param('version');?>"></script>

<?php
} ?>

<?php
if ((get_current_URL_name('list')) || get_current_URL_name('users')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap-paginator.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap3-editable/js/bootstrap-editable.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/daterangepicker.js?<?=get_conf_param('version');?>"></script>
<?php
} ?>

<?php
if (get_current_URL_name('clients')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap-paginator.js?<?=get_conf_param('version');?>"></script>
<?php
} ?>



<?php
if (get_current_URL_name('ticket')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/s2/select2.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.autosize.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap.file-input.js?<?=get_conf_param('version');?>"></script>

<?php
} ?>

<?php
if ((get_current_URL_name('create')) || get_current_URL_name('users') || get_current_URL_name('scheduler') || get_current_URL_name('config')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/s2/select2.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.autosize.min.js?<?=get_conf_param('version');?>"></script>

<!-- FOR UPLOADER -->
<?php
    if ($CONF['file_uploads'] == "true") { ?>
<script src="<?php echo $CONF['hostname'] ?>js/tmpl.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/load-image.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/canvas-to-blob.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.fileupload.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.fileupload-ui.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.fileupload-process.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.fileupload-image.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.fileupload-validate.js?<?=get_conf_param('version');?>"></script>
<?php
    } ?>
<!-- FOR UPLOADER -->
<?php
} ?>


<?php
if ((get_current_URL_name('helper')) || get_current_URL_name('notes') || get_current_URL_name('mailers')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/summernote.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/summernote-lang.js?<?=get_conf_param('version');?>"></script>
<?php
} ?>

<?php
if (get_current_URL_name('stats')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/highcharts.js?<?=get_conf_param('version');?>"></script>
<?php
} ?>


<?php
if (get_current_URL_name('view_user')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/jqueryKnob/jquery.knob.js?<?=get_conf_param('version');?>"></script>
<?php
} ?>

<?php
if (get_current_URL_name('user_stats') || get_current_URL_name('scheduler') || get_current_URL_name('main_stats') || get_current_URL_name('mailers') ) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/daterangepicker.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/s2/select2.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jqueryKnob/jquery.knob.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap-timepicker.min.js?<?=get_conf_param('version');?>"></script>

<?php
} ?>

<?php
if ( get_current_URL_name('helper') ) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.mjs.nestedSortable.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap3-editable/js/bootstrap-editable.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootbox.min.js?<?=get_conf_param('version');?>"></script>

<?php
} ?>




<script src="<?php echo $CONF['hostname'] ?>js/socket.io-1.1.0.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/core.js?<?=get_conf_param('version');?>"></script>



</body>
</html>
