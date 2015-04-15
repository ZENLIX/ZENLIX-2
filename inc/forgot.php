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
<?php if (get_conf_param('allow_forgot') == "true") { 


if ($_GET['m']) {

$ct=false;

if (!empty($_GET['uc']) && !empty($_GET['ph'])) {

$uniq_code=$_GET['uc'];
$pass_md=$_GET['ph'];

            $stmt = $dbConnection->prepare('select pass from users where uniq_id=:uniq_id limit 1');
            $stmt->execute(array(
                ':uniq_id' => $uniq_code
            ));
            $r = $stmt->fetchAll();

if (!empty($r)) {
    foreach ($r as $v) {
        //echo md5($v['pass'])." == ".$pass_md;
        if (md5($v['pass']) == $pass_md) {
            $ct=true;
        }

    }
}


//echo "change";


}


if ($ct == true) {
    ?>


<body class="bg-navy">

        <div class="form-box" id="login-box">
            <div class="header bg-light-blue" style="">
            <center><img src="<?=get_logo_img(); ?>" width="128"></center>
            <?php echo lang('MAIN_TITLE'); ?></div>
            <form class="form-signin" autocomplete="off" id="main_form_register">
                <div class="body bg-gray">
                <div id="error_result"></div>

                    <div class="form-group">
                        <input type="password" name="pass_new" class="form-control" placeholder="<?php echo lang('P_pass_new'); ?>" id="pass_new">
                    </div>
                                        <div class="form-group">
                        <input type="password" name="pass_new2" class="form-control" placeholder="<?php echo lang('P_pass_new_re'); ?>" id="pass_new2">
                    </div>

                </div>
                <div class="footer bg-gray">                                                               
                    <button id="change_pw" class="btn btn-success btn-block"><i class="fa fa-check-circle"></i>  <?php echo lang('P_do_edit_pass'); ?></button>  
                   
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















</div>

<script src="<?php echo $CONF['hostname'] ?>js/jquery-1.11.0.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/app.js"></script>
<script>
$(document).ready(function() {
            $('body').on('click', 'button#change_pw', function(event) {
            event.preventDefault();
            $('button#change_pw').prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "actions.php",
                    dataType: "json",
                    data: "mode=forgot_pass_change"+
                        "&p1="+$('#pass_new').val()+
                        "&p2="+$('#pass_new2').val()+
                        "&uc="+'<?=$_GET['uc'];?>'+
                        "&ph="+'<?=$_GET['ph'];?>',
                    success: function(html){
                        
                        if (html) {
                             $.each(html, function(i, item) {
                                if (item.check_error == "true") {
                                    $("#error_result").html(item.msg);
                                    $('#pass_new').prop('disabled', true);
                                    $('#pass_new2').prop('disabled', true);
                                    setTimeout(function() {window.location = "./";}, 5000);
                                }
                                else if (item.check_error == "false") {
                                    $("#error_result").html(item.msg);
                                    $('button#change_pw').prop('disabled', false);
                                }
                            });
                            
                            }

                             }
                    });
        
        });
});
</script>


    <?php
}





    }


    else if (!$_GET['m']) {
    ?>

<body class="bg-navy">

        <div class="form-box" id="login-box">
            <div class="header bg-light-blue" style="">
            <center><img src="<?=get_logo_img(); ?>" width="128"></center>
            <?php echo lang('MAIN_TITLE'); ?></div>
            <form class="form-signin" autocomplete="off" id="main_form_register">
                <div class="body bg-gray">
                <div id="error_result"></div>
                                        <div class="form-group">
                        <input type="text" name="name" class="form-control" placeholder="<?php echo lang('USERS_login'); ?>" id="login_name">
                    </div>
                    <div class="form-group">
                        <input type="text" name="userid" class="form-control" placeholder="E-mail" id="login_mail">
                    </div>

                </div>
                <div class="footer bg-gray">                                                               
                    <button id="forgot" class="btn btn-success btn-block"><i class="fa fa-check-circle"></i>  <?php echo lang('FORGOT_button'); ?></button>  
                   <center> <a href="index.php" class="text-center"><?php echo lang('REG_already'); ?></a></center>
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















</div>

<script src="<?php echo $CONF['hostname'] ?>js/jquery-1.11.0.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo $CONF['hostname'] ?>js/app.js"></script>
<script>
$(document).ready(function() {
            $('body').on('click', 'button#forgot', function(event) {
            event.preventDefault();

            $('button#forgot').prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "actions.php",
                    dataType: "json",
                    data: "mode=forgot_pass"+
                        "&login="+$('#login_name').val()+
                        "&mail="+$('#login_mail').val(),
                    success: function(html){
                        
                        if (html) {
                             $.each(html, function(i, item) {
                                if (item.check_error == "true") {
                                    $("#error_result").html(item.msg);
                                    $('#login_name').prop('disabled', true);
                                    $('#login_mail').prop('disabled', true);
                                    setTimeout(function() {window.location = "./";}, 5000);
                                }
                                else if (item.check_error == "false") {
                                    $("#error_result").html(item.msg);
                                    $('button#forgot').prop('disabled',false);
                                }
                            });
                            
                            }

                             }
                    });
        
        });
});
</script>

<?php
}
}
?>