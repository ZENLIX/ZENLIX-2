<?php
include_once ("head.inc.php");

//include("dbconnect.inc.php");

?>






<body class="bg-navy">

        <div class="form-box" id="login-box">
            <div class="header bg-light-blue" style="">
            <center><img src="<?php echo $CONF['hostname'] ?>img/helpdesk-logo.png" width="128"></center>
            <?php echo lang('MAIN_TITLE'); ?></div>
            <form class="form-signin" action="<?php echo $CONF['hostname'] ?>index.php" method="POST" autocomplete="off">
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
                                                                     
                    <button type="submit" class="btn btn-success btn-block"><i class="fa fa-sign-in"></i>  <?php echo lang('log_in'); ?></button>  
                    <?php if (get_conf_param('allow_register') == "true") { ?>
                    <center> <a href="register" class="text-center"><?php echo lang('REG_new'); ?></a> </center>
                    <?php } ?>
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
<script>

</script>
<script src="<?php echo $CONF['hostname'] ?>js/jquery-1.11.0.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/app.js"></script>
