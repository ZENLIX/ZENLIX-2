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
.form-box {
  width: 360px;
  margin: 90px auto 0 auto;
}
.form-box .header {
  -webkit-border-top-left-radius: 4px;
  -webkit-border-top-right-radius: 4px;
  -webkit-border-bottom-right-radius: 0;
  -webkit-border-bottom-left-radius: 0;
  -moz-border-radius-topleft: 4px;
  -moz-border-radius-topright: 4px;
  -moz-border-radius-bottomright: 0;
  -moz-border-radius-bottomleft: 0;
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
  background: #3d9970;
  box-shadow: inset 0px -3px 0px rgba(0, 0, 0, 0.2);
  padding: 20px 10px;
  text-align: center;
  font-size: 26px;
  font-weight: 300;
  color: #fff;
}
.form-box .body,
.form-box .footer {
  padding: 10px 20px;
  background: #fff;
  color: #444;
}
.form-box .body > .form-group,
.form-box .footer > .form-group {
  margin-top: 20px;
}
.form-box .body > .form-group > input,
.form-box .footer > .form-group > input {
  border: #fff;
}
.form-box .body > .btn,
.form-box .footer > .btn {
  margin-bottom: 10px;
}
.form-box .footer {
  -webkit-border-top-left-radius: 0;
  -webkit-border-top-right-radius: 0;
  -webkit-border-bottom-right-radius: 4px;
  -webkit-border-bottom-left-radius: 4px;
  -moz-border-radius-topleft: 0;
  -moz-border-radius-topright: 0;
  -moz-border-radius-bottomright: 4px;
  -moz-border-radius-bottomleft: 4px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
  border-bottom-right-radius: 4px;
  border-bottom-left-radius: 4px;
}
@media (max-width: 767px) {
  .form-box {
    width: 90%;
  }
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