<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    
    <title><?php echo $CONF['title_header']; ?></title>


</head>



<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/js/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/jquery-ui.min.css">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/ionicons.min.css">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/style.css?v4">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/chosen.min.css">


<?php
if ((get_current_URL_name('create')) || get_current_URL_name('deps') || get_current_URL_name('scheduler')) { ?>
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/js/bootstrap3-editable/css/bootstrap-editable.css">
<?php
} ?>




<?php
if ((get_current_URL_name('create')) || get_current_URL_name('ticket') || get_current_URL_name('users') || get_current_URL_name('user_stats') || get_current_URL_name('scheduler') || get_current_URL_name('main_stats') ) { ?>
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/js/s2/select2.css?v2">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/js/s2/select2-bootstrap.css">
<?php
} ?>

<style type="text/css" media="all">
    .chosen-rtl .chosen-drop { left: -9000px; }
</style>

<?php
if ((get_current_URL_name('helper')) || get_current_URL_name('notes')) { ?>
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/summernote-bs3.css">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/summernote.css">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/js/bootstrap3-editable/css/bootstrap-editable.css">
<?php
} ?>

<?php
if (get_current_URL_name('create')) { ?>
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/jquery.fileupload.css">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/jquery.fileupload-ui.css">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/uploadfile.css">
<?php
} ?>

<?php
if (get_current_URL_name('user_stats') || get_current_URL_name('scheduler') || get_current_URL_name('main_stats')) { ?>
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/daterangepicker-bs3.css">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/bootstrap-timepicker.min.css">
<?php

}

?>


<link rel="stylesheet"type="text/css" media="print" href="<?php echo $CONF['hostname'] ?>/css/print.css">
<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/AdminLTE.css">
<body class="skin-blue" style="">
  
         
  
  
  