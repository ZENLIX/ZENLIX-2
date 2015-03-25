<?php
session_start();
function echoActiveClassIfRequestMatches($requestUri) {
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");
    $file = $_SERVER['REQUEST_URI'];
    $file = explode("?", basename($file));
    $current_file_name = $file[0];
    
    //$file = $_SERVER['REQUEST_URI'];
    //$file = explode("?", basename($file));
    
    if ($current_file_name == $requestUri) echo 'class="active"';
}

$validate=false;
if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
$validate=true;
  }
  if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
$validate=true;
  }
  if (validate_admin($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
$validate=true;
  }
?>

<header class="main-header">               
  <nav class="navbar navbar-static-top">
    <div class="container-fluid">
    <div class="navbar-header" style="border-right: 1px solid #eee;">
      <a href="<?php echo $CONF['hostname'] ?>index.php" class="logo" style="
    color: black;
">

      <img src="<?=get_logo_img('small');?>"> <?php echo $CONF['name_of_firm'] ?>

      </a>




      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
        <i class="fa fa-bars"></i>
      </button>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="navbar-collapse">



      <ul class="nav navbar-nav">
        <li <?php echo echoActiveClassIfRequestMatches("feed") ?>><a href="<?php echo $CONF['hostname'] ?>feed"><?=lang('PORTAL_news');?> </a></li>
        <li <?php echo echoActiveClassIfRequestMatches("version") ?>><a href="<?php echo $CONF['hostname'] ?>version"><?=lang('PORTAL_versions');?> </a></li>
        <li <?php echo echoActiveClassIfRequestMatches("manual") ?> ><a href="<?php echo $CONF['hostname'] ?>manual"><?=lang('PORTAL_help_center');?></a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=lang('PORTAL_cats');?> <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="<?=$CONF['hostname']."cat?1";?>"><?=lang('PORTAL_idea');?></a></li>
            <li><a href="<?=$CONF['hostname']."cat?2";?>"><?=lang('PORTAL_trouble');?></a></li>
            <li><a href="<?=$CONF['hostname']."cat?3";?>"><?=lang('PORTAL_question');?></a></li>
            <li><a href="<?=$CONF['hostname']."cat?4";?>"><?=lang('PORTAL_thank');?></a></li>
          </ul>
        </li>
      </ul>
      <form class="navbar-form navbar-left" role="search" method="get" action="manual">
        <div class="form-group">
          <input type="text" name="find" class="form-control" id="navbar-search-input" placeholder="<?=lang('PORTAL_find');?>">
        </div>
      </form>



      <ul class="nav navbar-nav navbar-right">

<?php
if ($validate == true) {
?>      





<li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  <img src="<?php echo get_user_img(); ?>" class="user-image" alt="User Image">
                  <span><?php echo nameshort(get_user_val('fio')); ?></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header ">
                    <img src="<?php echo get_user_img(); ?>" class="img-circle" alt="User Image">
                    <p>
                      <?php echo nameshort(get_user_val('fio')); ?> 
                      <small><?php echo get_user_val('posada'); ?></small>
                    </p>
                  </li>
                  <!-- Menu Body -->
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="<?php echo $CONF['hostname'] ?>dashboard" class="btn btn-default btn-flat"><?=lang('PORTAL_helpdesk');?></a>
                    </div>
                    <div class="pull-right">
                      <a href="<?php echo $CONF['hostname'] ?>index.php?logout" class="btn btn-default btn-flat"><?=lang('PORTAL_logout');?></a>
                    </div>
                  </li>
                </ul>
              </li>
<?php } ?>
<?php
if ($validate == false) {
?>      
<li><a href="auth"><i class="fa fa-user"></i> <?=lang('PORTAL_login');?></a></li>
<?php if (get_conf_param('allow_register') == "true") { ?>
<li><a href="register"><i class="fa fa-user"></i> <?=lang('PORTAL_register');?></a></li>
<?php } ?>
<?php } ?>
        
        <!--li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
          </ul>
        </li-->
      </ul>
    </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>
</header>