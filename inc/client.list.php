<?php
session_start();
include_once ("functions.inc.php");
$CONF['title_header'] = lang('LIST_title') . " - " . lang('MAIN_TITLE');
if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    include ("head.inc.php");
    include ("client.navbar.inc.php");
    
    $_GET['out'] = '1';
    $status_out = "active";








$get_out=false;
    if (isset($_GET['out'])) {
        $get_out=true;
$r = "out";
        ob_start();
        $_POST['menu'] = "out";
        $_POST['page'] = "1";
        //Start output buffer
        include_once ("client.list_content.inc.php");
        $client_list_page = ob_get_contents();
        
        //Grab output
        ob_end_clean();



        ///include_once ("client.list_content.inc.php");



    }









$basedir = dirname(dirname(__FILE__)); 

 try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/inc/views');
            
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
            $template = $twig->loadTemplate('client.list.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
'hostname'=>$CONF['hostname'],
'name_of_firm'=>$CONF['name_of_firm'],
'LIST_title'=>lang('LIST_title'),
'get_last_ticket_new'=>get_last_ticket_new($_SESSION['helpdesk_user_id']),
'LIST_loading'=>lang('LIST_loading'),
'client_list_page'=>$client_list_page,
'get_out'=>$get_out,
'ac_10'=>$ac['10'],
'ac_15'=>$ac['15'],
'ac_20'=>$ac['20'],
'nn'=>get_last_ticket('client', $_SESSION['helpdesk_user_id']),
'menu'=>$_POST['menu'],
'r'=>$r,
'get_total_pages'=>get_total_pages('clients', $_SESSION['helpdesk_user_id']),
'get_last_ticket'=>get_last_ticket('client', $_SESSION['helpdesk_user_id'])





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
                                    

                                </div><!-- /.box-header -->
                                <div class="box-body">
    <input type="hidden" id="main_last_new_ticket" value="<?php echo get_last_ticket_new($_SESSION['helpdesk_user_id']); ?>">
    <div class="">
        


        <div id="spinner" class="well well-large well-transparent lead">
            <center>
                <i class="fa fa-spinner fa-spin icon-2x"></i> <?php echo lang('LIST_loading'); ?> ...
            </center>
        </div>
        <div id="content">



            






            <?php
    
    if (isset($_GET['out'])) {
        $_POST['menu'] = "out";
        $_POST['page'] = "1";
        include_once ("client.list_content.inc.php");
    }
?>


</div>

        <div id="alert-content"></div>
    </div>
</div>
    
                <div class="box-footer clearfix">


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
?>
                
                                                            
                                        
                <div class="text-center">
                                                        
                    <ul id="client_example_out" class="pagination pagination-sm"></ul>
                                            <div class="pull-right">
                            
                            <div class="btn-group btn-group-xs">
  <button id="list_set_ticket" type="button" class="btn btn-default <?php echo $ac['10']; ?>">10</button>
  <button id="list_set_ticket" type="button" class="btn btn-default <?php echo $ac['15']; ?>">15</button>
  <button id="list_set_ticket" type="button" class="btn btn-default <?php echo $ac['20']; ?>">20</button>
</div>
                            
                        </div>
                        
                </div>            <?php
    } ?>

            <?php
    
    $nn = get_last_ticket('client', $_SESSION['helpdesk_user_id']);
    
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
        echo get_total_pages('clients', $_SESSION['helpdesk_user_id']); ?>">
            <input type="hidden" id="last_ticket" value="<?php echo get_last_ticket('client', $_SESSION['helpdesk_user_id']); ?>">

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
