<?php
session_start();
include_once ("functions.inc.php");
$CONF['title_header'] = lang('LIST_title') . " - " . lang('MAIN_TITLE');
if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    include ("head.inc.php");
    include ("client.navbar.inc.php");
    
    $_GET['out'] = '1';
    $status_out = "active";
    
    $get_out = false;
    if (isset($_GET['out'])) {
        $get_out = true;
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
        $loader = new Twig_Loader_Filesystem($basedir . '/inc/views');
        
        // инициализируем Twig
        if (get_conf_param('twig_cache') == "true") {
            $twig = new Twig_Environment($loader, array(
                'cache' => $basedir . '/inc/cache',
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
            'hostname' => $CONF['hostname'],
            'name_of_firm' => $CONF['name_of_firm'],
            'LIST_title' => lang('LIST_title') ,
            'get_last_ticket_new' => get_last_ticket_new($_SESSION['helpdesk_user_id']) ,
            'LIST_loading' => lang('LIST_loading') ,
            'client_list_page' => $client_list_page,
            'get_out' => $get_out,
            'ac_10' => $ac['10'],
            'ac_15' => $ac['15'],
            'ac_20' => $ac['20'],
            'nn' => get_last_ticket('client', $_SESSION['helpdesk_user_id']) ,
            'menu' => $_POST['menu'],
            'r' => $r,
            'get_total_pages' => get_total_pages('clients', $_SESSION['helpdesk_user_id']) ,
            'get_last_ticket' => get_last_ticket('client', $_SESSION['helpdesk_user_id'])
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
