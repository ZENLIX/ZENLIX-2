

<?php
include "head.inc.php";


include "navbar.inc.php";




if ($_SESSION['z.times'] >= 5 ){
//$vart = "bf";
$rt=time()-$_SESSION['z.times_lt'];
if ($rt > $CONF['bf_pass'])
{
                      //показать форму логина
                $login_form=true;
                        unset($_SESSION['z.times']);
                        unset($_SESSION['z.times_lt']);
}
else if ($rt <= $CONF['bf_pass'])
{
  $login_form=false;
                        //не показать форму логина
}

}
else if ($_SESSION['z.times'] < 5 ){

//показать форму логина
  $login_form=true;
}



if ($CONF['main_portal'] == true) {
$link="auth";
}
else if ($CONF['main_portal'] == false) {
    $link="index.php";
}


$basedir = dirname(dirname(dirname(__FILE__))); 
            ////////////
    try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/inc/main_portal/views');
            
            // инициализируем Twig
            $twig = new Twig_Environment($loader);
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('auth.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'hostname'=>$CONF['hostname'],
                'PORTAL_auth'=>lang('PORTAL_auth'),
                'get_logo_img'=>get_logo_img(),
                'login_form'=>$login_form,
                'LOGIN_ERROR_title'=>lang('LOGIN_ERROR_title'),
                'LOGIN_ERROR_desc'=>lang('LOGIN_ERROR_desc'),
                'link'=>$link,
                'login'=>lang('login'),
                'pass'=>lang('pass'),
                'remember_me'=>lang('remember_me'),
                'log_in'=>lang('log_in'),
                'allow_register'=>get_conf_param('allow_register'),
                'allow_forgot'=>get_conf_param('allow_forgot'),
                'REG_new'=>lang('REG_new'),
                'Forgot_pass_me'=>lang('Forgot_pass_me'),
                'va'=>$va,
                'error_auth'=>lang('error_auth'),
                'REQUEST_URI'=>$_SERVER['REQUEST_URI']






            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
        
/*

?>
<div class="content-wrapper">
<section class="content">





<section class="invoice">
          <!-- title row -->
          <div class="row">



<div class="col-md-4 col-md-offset-4">
<div class="box box-default">
                <div class="box-header with-border">
                  <center><h3 class="box-title"><?=lang('PORTAL_auth');?></h3></center>
<div class="box-tools pull-right">
                   
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                <div class="row">
                <div class="col-sm-12">
<center><img src="<?=get_logo_img(); ?>" width="128"></center><br><hr>
</div>






<?php


if ($_SESSION['z.times'] >= 5 ){
//$vart = "bf";
$rt=time()-$_SESSION['z.times_lt'];
if ($rt > $CONF['bf_pass'])
{
                      //показать форму логина
                $login_form=true;
                        unset($_SESSION['z.times']);
                        unset($_SESSION['z.times_lt']);
}
else if ($rt <= $CONF['bf_pass'])
{
  $login_form=false;
                        //не показать форму логина
}

}
else if ($_SESSION['z.times'] < 5 ){

//показать форму логина
  $login_form=true;
}



if ($login_form==false) {
?>
<div class="col-sm-12">
<br>
<div class="form-box" id="login-box">
<div class="alert alert-warning alert-dismissable">
                    
                    <h4><i class="icon fa fa-warning"></i> <?php echo lang('LOGIN_ERROR_title'); ?>!</h4>
                    <?=lang('LOGIN_ERROR_desc');?>
                  </div>
                  </div>
                  </div>

<?php
}


if ($login_form==true) {

  ?>










<?php
if ($CONF['main_portal'] == true) {
$link="auth";
}
else if ($CONF['main_portal'] == false) {
    $link="index.php";
}
?>
<form class="form-horizontal" action="<?php echo $CONF['hostname'] . $link; ?>" method="POST" autocomplete="off">


<div class="col-sm-12">
<input type="text" name="login" autocomplete="off" autocapitalize="off" autocorrect="off" class="form-control input-lg" placeholder="<?php echo lang('login'); ?>"/><br>
</div>

<div class="col-sm-12">
 <input type="password" name="password" class="form-control input-lg" placeholder="<?php echo lang('pass'); ?>"/>
</div>

<div class="col-sm-12">
 <div class="form-group">




                        <div class="checkbox">
                <center><label>
                
                    <input id="mc" name="remember_me" value="1" type="checkbox"> <?php echo lang('remember_me'); ?>
                
                </label></center>
            </div>
                    </div>
</div>

<div class="col-sm-12">
  <button class="btn btn-block btn-success btn-lg"><i class="fa fa-sign-in"></i>  <?php echo lang('log_in'); ?> </button>
</div>

<div class="col-sm-12">
  <br>
<center>
                    <small>
                    <?php if (get_conf_param('allow_register') == "true") { ?>
                     <a href="register" class="text-center"><?php echo lang('REG_new'); ?></a> 
                    <?php } ?>
                    <?php if (get_conf_param('allow_forgot') == "true") { ?>
                     | <a href="forgot" class="text-center"><?php echo lang('Forgot_pass_me'); ?></a> 
                    <?php } ?>
                    </small>
                    </center>
</div>
<div class="col-sm-12" id="error_result">


<?php
if ($va == 'error') { ?>
            <div class="alert alert-danger" style="margin:20px;">
                <center><?php echo lang('error_auth'); ?></center>
            </div> <?php
} ?>
                    </div>
                </div>
                <input type="hidden" name="req_url" value="<?php
echo $_SERVER['REQUEST_URI']; ?>">
</form>

<?php
}
?>

                </div><!-- /.footer -->
                </div>
                </div>

</div>






          </div>
          <!-- info row -->
          
        </section>




</section>
</div>


<?php
*/
include "footer.inc.php";
?>