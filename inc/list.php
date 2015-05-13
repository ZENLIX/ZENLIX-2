<?php
session_start();
include_once ("functions.inc.php");
$CONF['title_header'] = lang('LIST_title') . " - " . lang('MAIN_TITLE');
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    include ("head.inc.php");
    include ("navbar.inc.php");
    
    if (isset($_GET['in'])) {
        $status_in = "active";
        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if ($priv_val == "0") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } else if ($priv_val == "1") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } else if ($priv_val == "2") {
            $text = $CONF['name_of_firm'];
        }


 ob_start();
        
        
       
        $_POST['menu'] = "in";
        $_POST['page'] = "1";
        include_once ("list_content.inc.php");

        

        $list_tables = ob_get_contents();
        
        //Grab output
        ob_end_clean();

    $cur_sort= get_current_sort('in');
    $cur_sort_p= get_current_sort_p('in');

$r = "in";
        
        if (isset($_SESSION['hd.rustem_list_in'])) {
            
            switch ($_SESSION['hd.rustem_list_in']) {
                case '10':
                    $ac['10'] = "active";
                    break;

                case '15':
                    $ac['15'] = "active";
                    break;

                case '20':
                    $ac['20'] = "active";
                    break;

                default:
                    $ac['10'] = "active";
            }
        }
        
        if (isset($_SESSION['hd.rustem_sort_in'])) {
            
            switch ($_SESSION['hd.rustem_sort_in']) {
                case 'ok':
                    $button_sort_in['ok'] = "active";
                    break;

                case 'free':
                    $button_sort_in['free'] = "active";
                    break;

                case 'ilock':
                    $button_sort_in['ilock'] = "active";
                    break;

                case 'lock':
                    $button_sort_in['lock'] = "active";
                    break;

                default:
                    $button_sort_in['main'] = "active";
            }
        }



    } else if (isset($_GET['out'])) {
        $status_out = "active";
        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if ($priv_val == "0") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } else if ($priv_val == "1") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } else if ($priv_val == "2") {
            $text = $CONF['name_of_firm'];
        }

 ob_start();
        

        $_POST['menu'] = "out";
        $_POST['page'] = "1";
        include_once ("list_content.inc.php");

        

        $list_tables = ob_get_contents();
        
        //Grab output
        ob_end_clean();


$r = "out";
         $cur_sort= get_current_sort('out');
    $cur_sort_p= get_current_sort_p('out');
        if (isset($_SESSION['hd.rustem_list_out'])) {
            
            switch ($_SESSION['hd.rustem_list_out']) {
                case '10':
                    $ac['10'] = "active";
                    break;

                case '15':
                    $ac['15'] = "active";
                    break;

                case '20':
                    $ac['20'] = "active";
                    break;

                default:
                    $ac['10'] = "active";
            }
        }
        if (isset($_SESSION['hd.rustem_sort_out'])) {
            
            switch ($_SESSION['hd.rustem_sort_out']) {
                case 'ok':
                    $button_sort_out['ok'] = "active";
                    break;

                case 'free':
                    $button_sort_out['free'] = "active";
                    break;

                case 'ilock':
                    $button_sort_out['ilock'] = "active";
                    break;

                case 'lock':
                    $button_sort_out['lock'] = "active";
                    break;

                default:
                    $button_sort_out['main'] = "active";
            }
        }




    } else if (isset($_GET['arch'])) {
        $status_arch = "active";
        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if ($priv_val == "0") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } else if ($priv_val == "1") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } else if ($priv_val == "2") {
            $text = $CONF['name_of_firm'];
        }

 ob_start();
        

        $_POST['menu'] = "arch";
        $_POST['page'] = "1";
        include_once ("list_content.inc.php");
   

        $list_tables = ob_get_contents();
        
        //Grab output
        ob_end_clean();


$r = "arch";
        if (isset($_SESSION['hd.rustem_list_arch'])) {
            
            switch ($_SESSION['hd.rustem_list_arch']) {
                case '10':
                    $ac['10'] = "active";
                    break;

                case '15':
                    $ac['15'] = "active";
                    break;

                case '20':
                    $ac['20'] = "active";
                    break;

                default:
                    $ac['10'] = "active";
            }
        }


    } else if (isset($_GET['find'])) {
        
        //$status_find="active";
        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if ($priv_val == "0") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } else if ($priv_val == "1") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } else if ($priv_val == "2") {
            $text = $CONF['name_of_firm'];
        }



 ob_start();
        
        
        
        $_POST['menu'] = "find";
        include_once ("list_content.inc.php");
    
        

        $list_tables = ob_get_contents();
        
        //Grab output
        ob_end_clean();


    } else {
        $_GET['in'] = '1';
        $status_in = "active";


    $cur_sort= get_current_sort('in');
    $cur_sort_p= get_current_sort_p('in');

$r = "in";
        
        if (isset($_SESSION['hd.rustem_list_in'])) {
            
            switch ($_SESSION['hd.rustem_list_in']) {
                case '10':
                    $ac['10'] = "active";
                    break;

                case '15':
                    $ac['15'] = "active";
                    break;

                case '20':
                    $ac['20'] = "active";
                    break;

                default:
                    $ac['10'] = "active";
            }
        }
        
        if (isset($_SESSION['hd.rustem_sort_in'])) {
            
            switch ($_SESSION['hd.rustem_sort_in']) {
                case 'ok':
                    $button_sort_in['ok'] = "active";
                    break;

                case 'free':
                    $button_sort_in['free'] = "active";
                    break;

                case 'ilock':
                    $button_sort_in['ilock'] = "active";
                    break;

                case 'lock':
                    $button_sort_in['lock'] = "active";
                    break;

                default:
                    $button_sort_in['main'] = "active";
            }
        }


ob_start();
        
        
       
        $_POST['menu'] = "in";
        $_POST['page'] = "1";
        include_once ("list_content.inc.php");

        

        $list_tables = ob_get_contents();
        
        //Grab output
        ob_end_clean();





        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if ($priv_val == "0") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } else if ($priv_val == "1") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } else if ($priv_val == "2") {
            $text = $CONF['name_of_firm'];
        }
    }
    
    $newt = get_total_tickets_free();
    
    if ($newt != 0) {
        $newtickets = "(" . $newt . ")";
    }
    if ($newt == 0) {
        $newtickets = "";
    }
    $outt = get_total_tickets_out_and_success();
    if ($outt != 0) {
        $out_tickets = "(" . $outt . ")";
    }
    if ($outt == 0) {
        $out_tickets = "";
    }






if ($priv_val != "2") {

$c_text=count($text);
$text=view_array($text);

}




$nn = get_last_ticket($_POST['menu'], $user_id);









       





    try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem('inc/views');
            
            // инициализируем Twig
if (get_conf_param('twig_cache') == "true") {
$twig = new Twig_Environment($loader,array(
    'cache' => $basedir.'/inc/cache',
));
            }
            else {
$twig = new Twig_Environment($loader);
            }
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('list.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'LIST_title' => lang('LIST_title') ,
                'hostname' => $CONF['hostname'],
                'name_of_firm' => $CONF['name_of_firm'],
                'priv_val'=>$priv_val,
                'text'=>$text,
                'c_text'=>$c_text,
                'LIST_pin'=>lang('LIST_pin'),
                'cur_sort'=>$cur_sort,
                'cur_sort_p'=>$cur_sort_p,
                'get_last_ticket_new'=>get_last_ticket_new($_SESSION['helpdesk_user_id']),
                'status_in'=>$status_in,
                'LIST_in'=>lang('LIST_in'),
                'newtickets'=>$newtickets,
                'status_out'=>$status_out,
                'LIST_out'=>lang('LIST_out'),
                'out_tickets'=>$out_tickets,
                'status_arch'=>$status_arch,
                'LIST_arch'=>lang('LIST_arch'),
                'LIST_loading'=>lang('LIST_loading'),
                'button_sort_in_main'=>$button_sort_in['main'],
                'ticket_sort_def'=>lang('ticket_sort_def'),
                'STATS_t_free'=>lang('STATS_t_free'),
                'button_sort_in_free'=>$button_sort_in['free'],
                'ticket_sort_ok'=>lang('ticket_sort_ok'),
                'button_sort_in_ok'=>$button_sort_in['ok'],
                'ticket_sort_ilock'=>lang('ticket_sort_ilock'),
                'button_sort_in_ilock'=>$button_sort_in['ilock'],
                'ticket_sort_lock'=>lang('ticket_sort_lock'),
                'button_sort_in_lock'=>$button_sort_in['lock'],
                'ac_10'=>$ac['10'],
                'ac_15'=>$ac['15'],
                'ac_20'=>$ac['20'],
                'button_sort_out_main'=>$button_sort_out['main'],
                'button_sort_out_free'=>$button_sort_out['free'],
                'button_sort_out_ok'=>$button_sort_out['ok'],
                'button_sort_out_ilock'=>$button_sort_out['ilock'],
                'button_sort_out_lock'=>$button_sort_out['lock'],
                'nn'=>$nn,
                'menu'=>$_POST['menu'],
                'r'=>$r,
                'get_total_pages_menu'=>get_total_pages($_POST['menu'], $user_id),
                'last_ticket'=>get_last_ticket($_POST['menu'], $user_id),
                'list_tables'=>$list_tables



























            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
        /*
?>



<section class="content-header">
                    <h1>
                        <i class="fa fa-list-alt"></i> <?php echo lang('LIST_title'); ?>
                        <small>
                            
                            <?php
    if ($priv_val != "2") { ?>
                                <span data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo view_array($text); ?>"><?php echo lang('LIST_pin') ?>: <?php echo count($text); ?>
                                </span>
                                <?php
    } else if ($priv_val == "2") {
        echo $text;
    } ?>
                            
                        </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('LIST_title'); ?></li>
                    </ol>
                </section>
                
<section class="content">
<div class="row">
<div class="col-md-12">
    
    
    <div class="box box-solid">
                                <div class="box-header">
                                    
                                    <div class="box-tools">
                                    
                                    <div class="pull-left" >


<?php

if (isset($_GET['in'])) {
    echo get_current_sort('in');
    echo get_current_sort_p('in');

 }
if (isset($_GET['out'])) {
    echo get_current_sort('out');
    echo get_current_sort_p('out');
}


?>

                                    </div>

                                    
                                    
                                    
                                        
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">
    <input type="hidden" id="main_last_new_ticket" value="<?php echo get_last_ticket_new($_SESSION['helpdesk_user_id']); ?>">
    <div class="">
        
        <div class="btn-group btn-group-justified">
            <a class="btn btn-default btn-sm btn-flat <?php echo $status_in ?>" role="button" href="?in"><i
                    class="fa fa-download"></i> <?php echo lang('LIST_in'); ?> <span
                    id="label_list_in"><?php echo $newtickets ?></span></a>
            <a class="btn btn-default btn-sm btn-flat <?php echo $status_out ?>" role="button" id="link_out" href="?out"><i
                    class="fa fa-upload"></i> <?php echo lang('LIST_out'); ?> <span
                    id="label_list_out"><?php echo $out_tickets ?></span> </a>
            <a class="btn btn-default btn-sm btn-flat <?php echo $status_arch ?>" role="button" href="?arch"><i
                    class="fa fa-archive"></i> <?php echo lang('LIST_arch'); ?></a>
        </div>




        <br>

        <div id="spinner" class="well well-large well-transparent lead">
            <center>
                <i class="fa fa-spinner fa-spin icon-2x"></i> <?php echo lang('LIST_loading'); ?> ...
            </center>
        </div>
        <div id="content">


            <?php
    
    if (isset($_GET['in'])) {
        $_POST['menu'] = "in";
        $_POST['page'] = "1";
        include_once ("list_content.inc.php");
?>



            <?php
    }
    
    if (isset($_GET['out'])) {
        $_POST['menu'] = "out";
        $_POST['page'] = "1";
        include_once ("list_content.inc.php");
    }
    
    if (isset($_GET['arch'])) {
        $_POST['menu'] = "arch";
        $_POST['page'] = "1";
        include_once ("list_content.inc.php");
    }
    
    if (isset($_GET['find'])) {
        $_POST['menu'] = "find";
        include_once ("list_content.inc.php");
    }
?>


        </div>

        <div id="alert-content"></div>
    </div>
</div>
    
                <div class="box-footer clearfix">
        <?php
    
    if (isset($_GET['in'])) {
        $r = "in";
        
        if (isset($_SESSION['hd.rustem_list_in'])) {
            
            switch ($_SESSION['hd.rustem_list_in']) {
                case '10':
                    $ac['10'] = "active";
                    break;

                case '15':
                    $ac['15'] = "active";
                    break;

                case '20':
                    $ac['20'] = "active";
                    break;

                default:
                    $ac['10'] = "active";
            }
        }
        
        if (isset($_SESSION['hd.rustem_sort_in'])) {
            
            switch ($_SESSION['hd.rustem_sort_in']) {
                case 'ok':
                    $button_sort_in['ok'] = "active";
                    break;

                case 'free':
                    $button_sort_in['free'] = "active";
                    break;

                case 'ilock':
                    $button_sort_in['ilock'] = "active";
                    break;

                case 'lock':
                    $button_sort_in['lock'] = "active";
                    break;

                default:
                    $button_sort_in['main'] = "active";
            }
        }
?>
    

                                
                                
        
                                
                    
                    <div class="pull-left">
                    
<div class="btn-group btn-group-xs">
  
  <button  id="sort_list" value="main" type="button" class="btn btn-primary <?php echo $button_sort_in['main']; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('ticket_sort_def') ?>"><i class="fa fa-home"></i> </button>
  
    <button  id="sort_list" value="free" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('STATS_t_free') ?>" type="button" class="btn btn-info <?php echo $button_sort_in['free']; ?>"><i class="fa fa-circle-thin"></i> </button>
    
  <button  id="sort_list" value="ok" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('ticket_sort_ok') ?>" type="button" class="btn btn-success <?php echo $button_sort_in['ok']; ?>"><i class="fa fa-check-circle"></i> </button>
  
  <button  id="sort_list" value="ilock" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('ticket_sort_ilock') ?>" type="button" class="btn btn-warning <?php echo $button_sort_in['ilock']; ?>"><i class="fa fa-gavel"></i> </button>
  
  <button  id="sort_list" value="lock" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('ticket_sort_lock') ?>" type="button" class="btn btn-default <?php echo $button_sort_in['lock']; ?>"><i class="fa fa-gavel"></i> </button>

</div>
                    
                    
                    
                                        </div>
                    <div class="text-center">
                        
                    
                        
                        <ul id="example_in" class="pagination pagination-sm"></ul>
                        <div class="pull-right">
                            
                            <div class="btn-group btn-group-xs">
  <button id="list_set_ticket" type="button" class="btn btn-default <?php echo $ac['10']; ?>">10</button>
  <button id="list_set_ticket" type="button" class="btn btn-default <?php echo $ac['15']; ?>">15</button>
  <button id="list_set_ticket" type="button" class="btn btn-default <?php echo $ac['20']; ?>">20</button>
</div>
                            
                        </div>
                    </div></div>
                
            <?php
    } ?>
            <?php
    if (isset($_GET['out'])) {
        $r = "out";
        
        if (isset($_SESSION['hd.rustem_list_out'])) {
            
            switch ($_SESSION['hd.rustem_list_out']) {
                case '10':
                    $ac['10'] = "active";
                    break;

                case '15':
                    $ac['15'] = "active";
                    break;

                case '20':
                    $ac['20'] = "active";
                    break;

                default:
                    $ac['10'] = "active";
            }
        }
        if (isset($_SESSION['hd.rustem_sort_out'])) {
            
            switch ($_SESSION['hd.rustem_sort_out']) {
                case 'ok':
                    $button_sort_out['ok'] = "active";
                    break;

                case 'free':
                    $button_sort_out['free'] = "active";
                    break;

                case 'ilock':
                    $button_sort_out['ilock'] = "active";
                    break;

                case 'lock':
                    $button_sort_out['lock'] = "active";
                    break;

                default:
                    $button_sort_out['main'] = "active";
            }
        }
?>
                
                    <div class="pull-left">
                    
<div class="btn-group btn-group-xs">
  
  <button id="sort_list" value="main" type="button" class="btn btn-primary <?php echo $button_sort_out['main']; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('ticket_sort_def') ?>"><i class="fa fa-home"></i> </button>
  
        <button  id="sort_list" value="free" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('STATS_t_free') ?>" type="button" class="btn btn-info <?php echo $button_sort_out['free']; ?>"><i class="fa fa-circle-thin"></i> </button>
        
  <button id="sort_list" value="ok" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('ticket_sort_ok') ?>" type="button" class="btn btn-success <?php echo $button_sort_out['ok']; ?>"><i class="fa fa-check-circle"></i> </button>
  
  <button id="sort_list" value="ilock" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('ticket_sort_ilock') ?>" type="button" class="btn btn-warning <?php echo $button_sort_out['ilock']; ?>"><i class="fa fa-gavel"></i> </button>
  
  <button id="sort_list" value="lock" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('ticket_sort_lock') ?>" type="button" class="btn btn-default <?php echo $button_sort_out['lock']; ?>"><i class="fa fa-gavel"></i> </button>

</div>
                    
                    
                    
                                        </div>
                                        
                                        
                <div class="text-center">
                                                        
                    <ul id="example_out" class="pagination pagination-sm"></ul>
                                            <div class="pull-right">
                            
                            <div class="btn-group btn-group-xs">
  <button id="list_set_ticket" type="button" class="btn btn-default <?php echo $ac['10']; ?>">10</button>
  <button id="list_set_ticket" type="button" class="btn btn-default <?php echo $ac['15']; ?>">15</button>
  <button id="list_set_ticket" type="button" class="btn btn-default <?php echo $ac['20']; ?>">20</button>
</div>
                            
                        </div>
                        
                </div>
            <?php
    } ?>
            <?php
    if (isset($_GET['arch'])) {
        $r = "arch";
        if (isset($_SESSION['hd.rustem_list_arch'])) {
            
            switch ($_SESSION['hd.rustem_list_arch']) {
                case '10':
                    $ac['10'] = "active";
                    break;

                case '15':
                    $ac['15'] = "active";
                    break;

                case '20':
                    $ac['20'] = "active";
                    break;

                default:
                    $ac['10'] = "active";
            }
        }
?>
                
                <div class="text-center">
                                                        <ul id="example_arch" class="pagination pagination-sm"></ul>
                    
                                            <div class="pull-right">
                                            <div class="btn-group btn-group-xs">
  <button id="list_set_ticket" type="button" class="btn btn-default <?php echo $ac['10']; ?>">10</button>
  <button id="list_set_ticket" type="button" class="btn btn-default <?php echo $ac['15']; ?>">15</button>
  <button id="list_set_ticket" type="button" class="btn btn-default <?php echo $ac['20']; ?>">20</button>
</div></div>


                </div>
            <?php
    }
    
    $nn = get_last_ticket($_POST['menu'], $user_id);
    
    if ($nn == 0) {
?>
            <input type="hidden" id="curent_page" value="null">
            <input type="hidden" id="page_type" value="<?php echo $_POST['menu'] ?>">
        <?php
    } else if ($nn <> 0) {
?>


            <input type="hidden" id="page_type" value="<?php echo $r; ?>">
            <input type="hidden" id="curent_page" value="1">
            <input type="hidden" id="cur_page" value="1">


            <input type="hidden" id="total_pages" value="<?php
        echo get_total_pages($_POST['menu'], $user_id); ?>">
            <input type="hidden" id="last_ticket" value="<?php echo get_last_ticket($_POST['menu'], $user_id); ?>">

        <?php
    } ?>



                            
                            
                            
                            
                            
    






</div>
</div>
    </section>
    <?php
    */
    include ("footer.inc.php");
?>

<?php
} else {
    include 'auth.php';
}
?>
