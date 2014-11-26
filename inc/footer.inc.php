
<div id="footer" style=" padding-bottom: 70px; ">
    <div class="container" style=" padding: 20px; ">
        <div class="col-md-12">
            <p class="text-muted credit pull-right"><small>Developed by <a href="http://zenlix.com/">ZENLIX</a> (с) 2014.</p>
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
    var NODE_PORT = "<?php echo get_conf_param('node_port'); ?>";
</script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery-1.11.0.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/app.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.titlealert.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.noty.packaged.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/ion.sound.min.js"></script>

<!--script src="<?php echo $CONF['hostname'] ?>js/moment-with-locales.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/moment-timezone-with-data-2010-2020.min.js"></script-->

<script src="<?php echo $CONF['hostname'] ?>js/moment.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/moment-timezone-with-data-2010-2020.min.js"></script>


<script src="<?php echo $CONF['hostname'] ?>js/moment-with-langs.js"></script>

<script src="<?php echo $CONF['hostname'] ?>js/jquery-ui-1.10.4.custom.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/chosen.jquery.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootbox.min.js"></script>


<?php
if (get_current_URL_name('create') || get_current_URL_name('scheduler') ) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.ui.autocomplete.js"></script>
<?php
} ?>

<?php
if ((get_current_URL_name('create')) || get_current_URL_name('deps') || get_current_URL_name('scheduler')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<?php
} ?>

<?php
if ((get_current_URL_name('list')) || get_current_URL_name('users')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap-paginator.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<?php
} ?>

<?php
if (get_current_URL_name('clients')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap-paginator.js"></script>
<?php
} ?>



<?php
if (get_current_URL_name('ticket')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/s2/select2.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.autosize.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap.file-input.js"></script>

<?php
} ?>

<?php
if ((get_current_URL_name('create')) || get_current_URL_name('users') || get_current_URL_name('scheduler')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/s2/select2.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.autosize.min.js"></script>

<!-- FOR UPLOADER -->
<?php
    if ($CONF['file_uploads'] == "true") { ?>
<script src="<?php echo $CONF['hostname'] ?>js/tmpl.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/load-image.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/canvas-to-blob.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.fileupload.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.fileupload-ui.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.fileupload-process.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.fileupload-image.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery.fileupload-validate.js"></script>
<?php
    } ?>
<!-- FOR UPLOADER -->
<?php
} ?>


<?php
if ((get_current_URL_name('helper')) || get_current_URL_name('notes')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/summernote.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/summernote-lang.js"></script>
<?php
} ?>

<?php
if (get_current_URL_name('stats')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/highcharts.js"></script>
<?php
} ?>


<?php
if (get_current_URL_name('view_user')) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/jqueryKnob/jquery.knob.js"></script>
<?php
} ?>

<?php
if (get_current_URL_name('user_stats') || get_current_URL_name('scheduler') ) { ?>
<script src="<?php echo $CONF['hostname'] ?>js/daterangepicker.js?v5"></script>
<script src="<?php echo $CONF['hostname'] ?>js/s2/select2.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/jqueryKnob/jquery.knob.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap-timepicker.min.js"></script>

<?php
} ?>




<script src="<?php echo $CONF['hostname'] ?>js/socket.io-1.1.0.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/core.js"></script>



</body>
</html>
