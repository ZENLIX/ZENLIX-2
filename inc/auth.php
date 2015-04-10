<?php
include_once ("head.inc.php");

//include("dbconnect.inc.php");

?>


 
<style type="text/css" media="screen">
.c {
background: url(img/login_bg.jpg) no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;

  }
body {
background-color: transparent;
}

  /*
body {
background: url(img/login_bg.jpg);
background-size: 100% 100% auto;
background-repeat: no-repeat;
}   */ 
</style>
<?php
if ($CONF['main_portal'] == true) {
$link="auth";
}
else if ($CONF['main_portal'] == false) {
    $link="index.php";
}
?>

<body class="bg-navy">

        <div class="form-box" id="login-box">
            <div class="header bg-light-blue" style="">
            <center><img src="<?=get_logo_img(); ?>" width="128"></center>
            <?php echo lang('MAIN_TITLE'); ?></div>
            <form class="form-signin" action="<?php echo $CONF['hostname'] . $link; ?>" method="POST" autocomplete="off">
                <div class="body bg-gray">
                    <div class="form-group">
                        <input type="text" name="login" autocomplete="off" autocapitalize="off" autocorrect="off" class="form-control" placeholder="<?php echo lang('login'); ?>"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="<?php echo lang('pass'); ?>"/>
                    </div>          
                    <div class="form-group">
                        <div class="checkbox">
                <center><label>
                
                    <input id="mc" name="remember_me" value="1" type="checkbox"> <?php echo lang('remember_me'); ?>
                
                </label></center>
            </div>
                    </div>
                </div>
                <div class="footer bg-gray">         
                                                                     
                    <button type="submit" class="btn btn-success btn-block"><i class="fa fa-sign-in"></i>  <?php echo lang('log_in'); ?></button>  <center>
                    <small>
                    <?php if (get_conf_param('allow_register') == "true") { ?>
                     <a href="register" class="text-center"><?php echo lang('REG_new'); ?></a> 
                    <?php } ?>
                    <?php if (get_conf_param('allow_forgot') == "true") { ?>
                     | <a href="forgot" class="text-center"><?php echo lang('Forgot_pass_me'); ?></a> 
                    <?php } ?>
                    </small>
                    </center>
                    <!--p>Используйте Ваши LDAP-учётные данные для входа</p-->
                    <?php
if ($va == 'error') { ?>
            <div class="alert alert-danger">
                <center><?php echo lang('error_auth'); ?></center>
            </div> <?php
} ?>
                    
                </div>
                <input type="hidden" name="req_url" value="<?php
echo $_SERVER['REQUEST_URI']; ?>">
            </form>

            
        </div>

<div style="color:white; position: fixed; bottom: 0; width:100%; text-align: right;">
<right>
<h4></h4>
<p style=" margin-right: 20px; "> <?=get_conf_param('name_of_firm');?> (c) <?php echo date("Y"); ?></p>
</right></div>













<?php
if (ini_get('short_open_tag') == false) { ?>
<div class="alert alert-danger" role="alert">PHP-error: <em>short_open_tag</em> must be enable in your php configuration. <br> Details: <a href="http://php.net//manual/ru/language.basic-syntax.phptags.php">http://php.net//manual/ru/language.basic-syntax.phptags.php</a></div>
    <?php
} ?>
    

<?php
$filename = realpath(dirname(dirname(__FILE__))) . "/.htaccess";
if (!file_exists($filename)) { ?>
    <div class="alert alert-danger" role="alert">.htaccess error: <em><?php echo $filename
?></em> file not exist</div>
    <?php
}

// "mod_rewrite module is not enabled";

?>
<?php
$filename = realpath(dirname(dirname(__FILE__))) . "/upload_files/";
if (!is_writable($filename)) { ?>
    <div class="alert alert-danger" role="alert">Permission-error: <em><?php echo $filename
?></em> is not writable. <br> Add access to write.</a></div>
    <?php
}

// "mod_rewrite module is not enabled";

?>
</div>

<script src="<?php echo $CONF['hostname'] ?>js/jquery-1.11.0.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/app.js"></script>
<script>
$(document).ready(function() {
$("html").css("display", "none");
$("body").css("display", "none");
$("html").fadeIn(800);
$("body").fadeIn(800);
});
</script>