<!DOCTYPE html>
<html lang="ru" class="c">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    
    <title><?php echo $CONF['title_header']; ?></title>


</head>



<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/js/bootstrap/css/bootstrap.min.css?<?=get_conf_param('version');?>">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/jquery-ui.min.css?<?=get_conf_param('version');?>">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/ionicons.min.css?<?=get_conf_param('version');?>">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/style.css?<?=get_conf_param('version');?>">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/font-awesome/css/font-awesome.min.css?<?=get_conf_param('version');?>">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/chosen.min.css?<?=get_conf_param('version');?>">


<?php
if ((get_current_URL_name('create')) || get_current_URL_name('deps') || get_current_URL_name('scheduler')) { ?>
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/js/bootstrap3-editable/css/bootstrap-editable.css?<?=get_conf_param('version');?>">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/daterangepicker-bs3.css?<?=get_conf_param('version');?>">
<?php
} ?>




<?php
if ((get_current_URL_name('create')) || get_current_URL_name('ticket') || get_current_URL_name('users') || get_current_URL_name('user_stats') || get_current_URL_name('scheduler') || get_current_URL_name('main_stats') || get_current_URL_name('config') || get_current_URL_name('mailers')  ) { ?>
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/js/s2/select2.css?<?=get_conf_param('version');?>">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/js/s2/select2-bootstrap.css?<?=get_conf_param('version');?>">
<?php
} ?>

<style type="text/css" media="all">
    .chosen-rtl .chosen-drop { left: -9000px; }
</style>

<?php
if ((get_current_URL_name('helper')) || get_current_URL_name('notes') || get_current_URL_name('mailers')) { ?>
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/summernote-bs3.css?<?=get_conf_param('version');?>">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/summernote.css?v2.5">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/js/bootstrap3-editable/css/bootstrap-editable.css?<?=get_conf_param('version');?>">
<?php
} ?>

<?php
if (get_current_URL_name('create')) { ?>
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/jquery.fileupload.css?<?=get_conf_param('version');?>">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/jquery.fileupload-ui.css?<?=get_conf_param('version');?>">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/uploadfile.css?<?=get_conf_param('version');?>">
<?php
} ?>

<?php
if (get_current_URL_name('user_stats') || get_current_URL_name('scheduler') || get_current_URL_name('main_stats')) { ?>
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/daterangepicker-bs3.css?<?=get_conf_param('version');?>">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/bootstrap-timepicker.min.css?<?=get_conf_param('version');?>">
<?php

}

?>


<link rel="stylesheet"type="text/css" media="print" href="<?php echo $CONF['hostname'] ?>/css/print.css?<?=get_conf_param('version');?>">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/AdminLTE.css?<?=get_conf_param('version');?>">
<body class="skin-blue" style="">
  

  
  
  