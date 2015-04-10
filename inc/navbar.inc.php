<?php
function echoActiveClassIfRequestMatches($requestUri) {
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");
    $file = $_SERVER['REQUEST_URI'];
    $file = explode("?", basename($file));
    $current_file_name = $file[0];
    
    //$file = $_SERVER['REQUEST_URI'];
    //$file = explode("?", basename($file));
    
    if ($current_file_name == $requestUri) echo 'class="active"';
}




$p1=array('config', 'users', 'deps', 'files', 'scheduler', 'approve', 'posada', 'units', 'subj', 'portal', 'mailers');
$p2=array('main_stats', 'user_stats', 'sla_rep');
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");
    $file = $_SERVER['REQUEST_URI'];
    $file = explode("?", basename($file));
    $current_file_name = $file[0];

$tree_admin_class="";
$tree_stat_class="";


if (in_array($current_file_name, $p1)) {
    $tree_admin_class="active";
}
if (in_array($current_file_name, $p2)) {
    $tree_stat_class="active";
}





$newt = get_total_tickets_free();

if ($newt != 0) {
    $newtickets = "<small id=\"tt_label\"> <small class=\"badge pull-right bg-red\">" . $newt . "</small></small>";
} else if ($newt == 0) {
    $newtickets = "<small id=\"tt_label\"></small>";
}

$ap = get_approve();
if ($ap != 0) {
    $apr = "
    <small class=\"badge pull-right bg-yellow\">" . $ap . "</small>";
} else if ($ap == 0) {
    $apr = "";
}

//get_total_unread_messages
//<small class="badge pull-right bg-yellow">12</small>

$tm = get_total_unread_messages();
if ($tm != 0) {
    $atm = "
    <small id=\"label_msg\"> <small class=\"badge pull-right bg-yellow\">" . $tm . "</small></small>";
    $atm_v = $tm;
} else if ($tm == 0) {
    $atm = "<small id=\"label_msg\"></small>";
    $atm_v = "";
}
?>

<div class="wrapper">

               <header class="main-header">
                                   <?php
                    if ($main_portal == "true") {
                        ?>
            <a href="<?php echo $CONF['hostname'] ?>dashboard" class="logo">
            <?php
        }
        else if ($main_portal == "false") {
            ?>
            <a href="<?php echo $CONF['hostname'] ?>index.php" class="logo">
            <?php
        }
        ?>
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                <img src="<?=get_logo_img('small');?>">
                 <?php echo $CONF['name_of_firm'] ?>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->





          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only"><?php echo lang('EXT_toggle_nav'); ?></span>
          </a>





                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                    <?php
                    if ($main_portal == "true") {
                        ?>
<li class="">
<a href="<?php echo $CONF['hostname'] ?>"><?=lang('PORTAL_title');?></a>
</li>
<?php
}
?>


                    <li class="dropdown messages-menu">
                            <a href="#" id="show_online_users" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-users"></i>
                                <span class="label label-success online_users_label"><?php echo get_total_users_online(); ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header"> <strong class="online_users_label"><?php echo get_total_users_online(); ?></strong> <?php echo lang('EXT_users_online'); ?></li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu" id="online_users_content">
                                    <?php
$stmt = $dbConnection->prepare('select fio,id,uniq_id from users where last_time >= DATE_SUB(:n,INTERVAL 2 MINUTE)');
$stmt->execute(array(':n' => $CONF['now_dt']));
$re = $stmt->fetchAll();

foreach ($re as $rews) {
?>
<li><!-- start message -->
                                            <a href="view_user?<?php echo $rews['uniq_id']; ?>">
                                                <div class="pull-left">
                                                    <img src="<?php echo get_user_img_by_id($rews['id']); ?>" class="img-circle" alt="User Image" />
                                                </div>
                                                <h4>
                                                    <?php echo nameshort(name_of_user_ret_nolink($rews['id'])); ?>
                                                    
                                                    
                                                </h4>
                                                <p><?php echo get_user_val_by_id($rews['id'], 'posada'); ?></p>
                                            </a>
                                        </li><!-- end message -->
                                        <?php
} ?>
                                        
                                        
                                                                            </ul>
                                </li>
                                <li class="footer"><a href="clients"><?php echo lang('EXT_users_all_view'); ?></a></li>
                            </ul>
                        </li>
                        
                        
                        <?php
$stmt = $dbConnection->prepare('SELECT user_from, msg, date_op from messages where user_to=:uto and is_read=0');
$stmt->execute(array(':uto' => $_SESSION['helpdesk_user_id']));

$re = $stmt->fetchAll();

if (!empty($re)) {
    $title = lang('EXT_unread_msg1') . " <strong class=\"label_unread_msg\">" . $atm_v . "</strong> " . lang('EXT_unread_msg2');
} else if (empty($re)) {
    $title = lang('EXT_no_unread_msg');
}
?>
                    
                    
<li class="dropdown messages-menu" id="unread_msg">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-envelope"></i>
                                <span class="label label-warning label_unread_msg"><?php echo $atm_v; ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header" id="nav_t_msgs"><?php echo $title; ?></li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu" id="unread_msgs_content">
                                    
                                    <?php

foreach ($re as $rews) {
?>
                                    
                                    
                                        <li><!-- start message -->
                                            <a href="messages?to=<?php echo get_user_val_by_id($rews['user_from'], 'uniq_id'); ?>">
                                                <div class="pull-left">
                                                    <img src="<?php echo get_user_img_by_id($uniq_id); ?>" class="img-circle" alt="User Image"/>
                                                </div>
                                                <h4>
                                                    <?php echo nameshort(name_of_user_ret_nolink($rews['user_from'])); ?>
                                                    
                                                    <small><i class="fa fa-clock-o"></i> <time id="b" datetime="<?php echo $rews['date_op']; ?>"></time> </time></small>
                                                </h4>
                                                <p><?php echo make_html($rews['msg'], 'no'); ?></p>
                                            </a>
                                        </li><!-- end message -->
                                        <?php
}
?>
                                        
                                        
                                        
                                        
                                        
                                                                            </ul>
                                </li>
                                <li class="footer"><a href="messages"><?php echo lang('EXT_all_msgs'); ?></a></li>
                            </ul>
                        </li>
                        
                        
                        
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span><?php echo nameshort(get_user_val('fio')); ?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <img src="<?php echo get_user_img(); ?>" class="img-circle" alt="User Image" style="border: 2px solid;
border-color: transparent;
border-color: rgba(255, 255, 255, 0.2);" />
                                    <p>
                                        <?php echo get_user_val('fio'); ?>
                                        <small><?php echo get_user_val('posada'); ?></small>
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <li class="user-body">
                                    <div class="col-xs-6 text-center">
                                        <a href="<?php echo $CONF['hostname'] ?>stats"><?php echo lang('STATS_TITLE_short'); ?></a>
                                    </div>
                                    <div class="col-xs-6 text-center">
                                        <a href="<?php echo $CONF['hostname'] ?>help"><?php echo lang('NAVBAR_help'); ?></a>
                                    </div>
                                    
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                    
                                        <a href="<?php echo $CONF['hostname'] ?>profile" class="btn btn-default btn-flat"> <i class="fa fa-user"></i> <?php echo lang('NAVBAR_profile'); ?></a>
                                    </div>
                                    <div class="pull-right">
                                    
                                        <a href="<?php echo $CONF['hostname'] ?>index.php?logout" class="btn btn-default btn-flat"> <i class="fa fa-sign-out"></i> <?php echo lang('NAVBAR_logout'); ?></a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        









        
        
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="<?php echo get_user_img(); ?>" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p><?php echo lang('EXT_hello'); ?>, <?php echo get_user_name(get_user_val('fio')); ?></p>

                            <a ><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    <!-- search form -->
                    <form action="<?php echo $CONF['hostname']; ?>list" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input name="t" type="text" class="form-control" placeholder="<?php echo lang('LIST_find_button'); ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('LIST_find_ph'); ?>"/>
                            <span class="input-group-btn">
                                <button type='submit' name='find' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    
                    
                    
                    
                    <ul class="sidebar-menu">
                        <li <?php echo echoActiveClassIfRequestMatches("dashboard") ?> >
                            <a  href="<?php echo $CONF['hostname'] ?>dashboard">
                                <i class="fa fa-dashboard"></i> <span><?php echo lang('DASHBOARD_TITLE'); ?></span>
                            </a>
                        </li>
                        
                        <li <?php echo echoActiveClassIfRequestMatches("create") ?>><a href="<?php echo $CONF['hostname'] ?>create"><i class="fa fa-tag"></i> <?php echo lang('NAVBAR_create_ticket'); ?></a></li>
                        
                             <li <?php echo echoActiveClassIfRequestMatches("news") ?>><a href="<?php echo $CONF['hostname'] ?>news"><i class="fa fa-bullhorn"></i> <?php echo lang('NAVBAR_news'); ?></a></li>                   
                        
                        
                        
                        
                        
            <li <?php echo echoActiveClassIfRequestMatches("list") ?>><a href="<?php echo $CONF['hostname'] ?>list"><i class="fa fa-list-alt"></i> <?php echo lang('NAVBAR_list_ticket'); ?> <?php echo $newtickets ?></a></li>
            
            
                        <li <?php echo echoActiveClassIfRequestMatches("messages") ?>><a href="<?php echo $CONF['hostname'] ?>messages"><i class="fa fa-comments"></i> <?php echo lang('MESSAGES_navbar'); ?> <?php echo $atm; ?></a></li>
            
            
            <li <?php echo echoActiveClassIfRequestMatches("clients") ?>><a href="<?php echo $CONF['hostname'] ?>clients"><i class="fa fa-users"></i>  <?php echo lang('USERS_list'); ?></a></li>
                        
            
            
            
            
            <li <?php echo echoActiveClassIfRequestMatches("helper") ?>><a href="<?php echo $CONF['hostname'] ?>helper"><i class="fa fa-globe"></i> <?php echo lang('NAVBAR_helper'); ?></a></li>
            
                        <li <?php echo echoActiveClassIfRequestMatches("notes") ?>><a href="<?php echo $CONF['hostname'] ?>notes"><i class="fa fa-book"></i> <?php echo lang('NAVBAR_notes'); ?></a></li>
                        
                        
                        <?php
$priv_val = priv_status($_SESSION['helpdesk_user_id']);
if (($priv_val == "2") || ($priv_val == "0")) { ?>
 <li class="treeview <?=$tree_stat_class;?>">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i><span> <?php echo lang('EXT_graph'); ?></span><i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                            <li <?php echo echoActiveClassIfRequestMatches("main_stats") ?>><a href="<?php echo $CONF['hostname'] ?>main_stats"><i class="fa fa-line-chart"></i> <?php echo lang('ALLSTATS_main'); ?></a></li>
                            <li <?php echo echoActiveClassIfRequestMatches("user_stats") ?>><a href="<?php echo $CONF['hostname'] ?>user_stats"><i class="fa fa-pie-chart"></i> <?php echo lang('EXT_graph_user'); ?></a></li>

                            <li <?php echo echoActiveClassIfRequestMatches("sla_rep") ?>><a href="<?php echo $CONF['hostname'] ?>sla_rep"><i class="fa fa-bolt"></i> <?php echo lang('SLA_rep'); ?></a></li>


                            </ul>
 </li>
 

                        
                        
                        
                        <?php
} ?>

                        
                         <?php
if (validate_admin($_SESSION['helpdesk_user_id'])) { 




    ?>
                         <li class="treeview <?=$tree_admin_class;?>">
                            <a href="#">
                                <i class="fa fa-shield"></i>
                                <span><?php echo lang('NAVBAR_admin'); ?> </span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                             <li <?php echo echoActiveClassIfRequestMatches("config") ?>><a href="<?php echo $CONF['hostname'] ?>config"><i class="fa fa-cog"></i> <?php echo lang('NAVBAR_conf'); ?></a></li>

                             <li <?php echo echoActiveClassIfRequestMatches("portal") ?>><a href="<?php echo $CONF['hostname'] ?>portal"><i class="icon-svg" style=" padding-right: 6px;"></i> <?php echo lang('PORTAL_title'); ?></a></li>



                    <li <?php echo echoActiveClassIfRequestMatches("users") ?>><a href="<?php echo $CONF['hostname'] ?>users"><i class="fa fa-users"></i> <?php echo lang('NAVBAR_users'); ?></a></li>

                    <li <?php echo echoActiveClassIfRequestMatches("mailers") ?>><a href="<?php echo $CONF['hostname'] ?>mailers"><i class="fa fa-paper-plane-o"></i> <?php echo lang('NAVBAR_mailers'); ?></a></li>

                    <li <?php echo echoActiveClassIfRequestMatches("deps") ?>><a href="<?php echo $CONF['hostname'] ?>deps"><i class="fa fa-sitemap"></i> <?php echo lang('NAVBAR_deps'); ?></a></li>
                    <li <?php echo echoActiveClassIfRequestMatches("units") ?>><a href="<?php echo $CONF['hostname'] ?>units"><i class="fa fa-building-o"></i> <?php echo lang('NAVBAR_units'); ?></a></li>
                    <li <?php echo echoActiveClassIfRequestMatches("files") ?>><a href="<?php echo $CONF['hostname'] ?>files"><i class="fa fa-files-o"></i>  <?php echo lang('NAVBAR_files'); ?></a></li>
                    
                    <li <?php echo echoActiveClassIfRequestMatches("scheduler") ?>><a href="<?php echo $CONF['hostname'] ?>scheduler"><i class="fa fa-clock-o"></i>  <?=lang('cron_navbar');?></a></li>
                    
                                        
                    <li <?php echo echoActiveClassIfRequestMatches("approve") ?>><a href="<?php echo $CONF['hostname'] ?>approve"><i class="fa fa-check-square-o"></i> <?php echo lang('NAVBAR_approve'); ?> <?php echo $apr; ?></a></li>
                            
                            
                            <li <?php echo echoActiveClassIfRequestMatches("posada") ?> ><a href="<?php echo $CONF['hostname'] ?>posada"><i class="fa fa-male"></i> <?php echo lang('NAVBAR_posads'); ?></a></li>
                            
                            
                            
                            </ul>
                            
                         </li>
                         <?php
} ?>
                        
                        
                        
                        
                                            </ul>
                </section>
                <!-- /.sidebar -->
            </aside>
<?php


$style_hide="display:none;";
if (get_current_URL_name('print_ticket')) {
    $style_hide="";
}

?>
            <!-- Right side column. Contains the navbar and content of the page -->
            <div class="content-wrapper" >
<div class="main_i" style="<?=$style_hide;?>">