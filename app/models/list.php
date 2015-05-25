<?php
//session_start();
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
        } 
        else if ($priv_val == "1") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } 
        else if ($priv_val == "2") {
            $text = $CONF['name_of_firm'];
        }
        
        ob_start();
        
        $_POST['menu'] = "in";
        $_POST['page'] = "1";
        include_once ("list_content.inc.php");
        
        $list_tables = ob_get_contents();
        
        //Grab output
        ob_end_clean();
        
        $cur_sort = get_current_sort('in');
        $cur_sort_p = get_current_sort_p('in');
        
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
    } 
    else if (isset($_GET['out'])) {
        $status_out = "active";
        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if ($priv_val == "0") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } 
        else if ($priv_val == "1") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } 
        else if ($priv_val == "2") {
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
        $cur_sort = get_current_sort('out');
        $cur_sort_p = get_current_sort_p('out');
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
    } 
    else if (isset($_GET['arch'])) {
        $status_arch = "active";
        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if ($priv_val == "0") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } 
        else if ($priv_val == "1") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } 
        else if ($priv_val == "2") {
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
    } 
    else if (isset($_GET['find'])) {
        
        //$status_find="active";
        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if ($priv_val == "0") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } 
        else if ($priv_val == "1") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } 
        else if ($priv_val == "2") {
            $text = $CONF['name_of_firm'];
        }
        
        ob_start();
        
        $_POST['menu'] = "find";
        include_once ("list_content.inc.php");
        
        $list_tables = ob_get_contents();
        
        //Grab output
        ob_end_clean();
    } 
    else {
        $_GET['in'] = '1';
        $status_in = "active";
        
        $cur_sort = get_current_sort('in');
        $cur_sort_p = get_current_sort_p('in');
        
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
        } 
        else if ($priv_val == "1") {
            $text = get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']));
        } 
        else if ($priv_val == "2") {
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
    $c_text=Null;
    if ($priv_val != "2") {
        
        $c_text = count($text);
        $text = view_array($text);
    }
    
    $nn = get_last_ticket($_POST['menu'], $user_id);
    
    try {
        
        // указывае где хранятся шаблоны
        $loader = new Twig_Loader_Filesystem($basedir .'/views');
        
        // инициализируем Twig
        if (get_conf_param('twig_cache') == "true") {
            $twig = new Twig_Environment($loader, array(
                'cache' => $basedir . '/cache',
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
            'priv_val' => $priv_val,
            'text' => $text,
            'c_text' => $c_text,
            'LIST_pin' => lang('LIST_pin') ,
            'cur_sort' => $cur_sort,
            'cur_sort_p' => $cur_sort_p,
            'get_last_ticket_new' => get_last_ticket_new($_SESSION['helpdesk_user_id']) ,
            'status_in' => $status_in,
            'LIST_in' => lang('LIST_in') ,
            'newtickets' => $newtickets,
            'status_out' => $status_out,
            'LIST_out' => lang('LIST_out') ,
            'out_tickets' => $out_tickets,
            'status_arch' => $status_arch,
            'LIST_arch' => lang('LIST_arch') ,
            'LIST_loading' => lang('LIST_loading') ,
            'button_sort_in_main' => $button_sort_in['main'],
            'ticket_sort_def' => lang('ticket_sort_def') ,
            'STATS_t_free' => lang('STATS_t_free') ,
            'button_sort_in_free' => $button_sort_in['free'],
            'ticket_sort_ok' => lang('ticket_sort_ok') ,
            'button_sort_in_ok' => $button_sort_in['ok'],
            'ticket_sort_ilock' => lang('ticket_sort_ilock') ,
            'button_sort_in_ilock' => $button_sort_in['ilock'],
            'ticket_sort_lock' => lang('ticket_sort_lock') ,
            'button_sort_in_lock' => $button_sort_in['lock'],
            'ac_10' => $ac['10'],
            'ac_15' => $ac['15'],
            'ac_20' => $ac['20'],
            'button_sort_out_main' => $button_sort_out['main'],
            'button_sort_out_free' => $button_sort_out['free'],
            'button_sort_out_ok' => $button_sort_out['ok'],
            'button_sort_out_ilock' => $button_sort_out['ilock'],
            'button_sort_out_lock' => $button_sort_out['lock'],
            'nn' => $nn,
            'menu' => $_POST['menu'],
            'r' => $r,
            'get_total_pages_menu' => get_total_pages($_POST['menu'], $user_id) ,
            'last_ticket' => get_last_ticket($_POST['menu'], $user_id) ,
            'list_tables' => $list_tables
        ));
    }
    catch(Exception $e) {
        die('ERROR: ' . $e->getMessage());
    }
    
    include ("footer.inc.php");
?>

<?php
} 
else {
    include 'auth.php';
}
?>
