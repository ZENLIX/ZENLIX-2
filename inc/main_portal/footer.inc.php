
      <footer class="main-footer" style=" padding-bottom: 40px; background: #ECF0F5;">

        <p class="text-muted credit pull-right" style="   padding-right: 30px;"><small>Developed by <a href="http://zenlix.com/">ZENLIX</a> (с) <?=date('Y');?>.</p>
      </footer>
    </div><!-- ./wrapper -->






</div>
</body>

<script type="text/javascript">
    var MyHOSTNAME = "<?php
echo $CONF['hostname']; ?>";
    var MyLANG = "<?php
echo $lang; ?>";
    var VALIDATE="<?=check_validate();?>";
    
    var NODE_URL = "<?php echo get_conf_param('node_port'); ?>";
   
</script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery-2.1.3.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap/js/bootstrap.min.js?<?=get_conf_param('version');?>"></script>

<script src="<?php echo $CONF['hostname'] ?>js/jquery.titlealert.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/noty/packaged/jquery.noty.packaged.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/ion.sound.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/dropzone.js?<?=get_conf_param('version');?>"></script>
<!--script src="<?php echo $CONF['hostname'] ?>js/moment-with-locales.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/moment-timezone-with-data-2010-2020.min.js"></script-->


<script src="<?php echo $CONF['hostname'] ?>js/icheck.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootbox.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/moment.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/moment-timezone-with-data-2010-2020.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap-paginator.js?<?=get_conf_param('version');?>"></script>

<script src="<?php echo $CONF['hostname'] ?>js/moment-with-langs.js?<?=get_conf_param('version');?>"></script>

<script src="<?php echo $CONF['hostname'] ?>js/jquery-ui-1.10.4.custom.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/chosen.jquery.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/summernote.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/summernote-lang.js?<?=get_conf_param('version');?>"></script>

<script src="<?php echo $CONF['hostname'] ?>js/jquery.mjs.nestedSortable.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap3-editable/js/bootstrap-editable.min.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/fancybox/jquery.fancybox.js?<?=get_conf_param('version');?>"></script>
<script src="<?php echo $CONF['hostname'] ?>js/core_portal.js?<?=get_conf_param('version');?>"></script>
</html>