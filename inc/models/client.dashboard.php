<?php
session_start();
include_once ("../functions.inc.php");
$CONF['title_header'] = lang('DASHBOARD_TITLE') . " - " . $CONF['name_of_firm'];
if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("client.navbar.inc.php");
        
        //check_unlinked_file();
        //client.dashboard.view.tmpl
        
        $view_right = false;
        if (get_conf_param('global_msg_status') == "1") {
            
            if (get_conf_param('global_msg_to') == "all") {
                $view_right = true;
            } 
            else if (get_conf_param('global_msg_to') != "all") {
                $list_viewers = explode(",", get_conf_param('global_msg_to'));
                if (in_array($_SESSION['helpdesk_user_id'], $list_viewers)) {
                    $view_right = true;
                }
            }
        }
        
        if (get_conf_param('global_msg_type') == "info") {
            $gm_type['icon'] = "info";
        } 
        else if (get_conf_param('global_msg_type') == "warning") {
            $gm_type['icon'] = "warning";
        } 
        else if (get_conf_param('global_msg_type') == "danger") {
            $gm_type['icon'] = "ban";
        }
        
        if (get_user_val('messages_type') == "0") {
            $style_msg = "info";
        } 
        else if (get_user_val('messages_type') == "1") {
            $style_msg = "warning";
        } 
        else if (get_user_val('messages_type') == "2") {
            $style_msg = "danger";
        }
        
        ob_start();
        
        //Start output buffer
        get_client_helper();
        
        $get_client_helper = ob_get_contents();
        
        //Grab output
        ob_end_clean();
        
        $basedir = dirname(dirname(__FILE__));
        
        try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir . '/views');
            
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
            $template = $twig->loadTemplate('client.dashboard.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'hostname' => $CONF['hostname'],
                'name_of_firm' => $CONF['name_of_firm'],
                'DASHBOARD_TITLE' => lang('DASHBOARD_TITLE') ,
                'DASHBOARD_title_ext' => lang('DASHBOARD_title_ext') ,
                'view_right' => $view_right,
                'global_msg_type' => get_conf_param('global_msg_type') ,
                'gm_type' => $gm_type['icon'],
                'global_msg_data' => get_conf_param('global_msg_data') ,
                'get_total_client_tickets_out' => get_total_client_tickets_out() ,
                'STATS_create' => lang('STATS_create') ,
                'EXT_more_info' => lang('EXT_more_info') ,
                'get_total_client_tickets_lock' => get_total_client_tickets_lock() ,
                'STATS_lock_o' => lang('STATS_lock_o') ,
                'EXT_more_info' => lang('EXT_more_info') ,
                'get_total_client_tickets_ok' => get_total_client_tickets_ok() ,
                'STATS_ok_o' => lang('STATS_ok_o') ,
                'EXT_more_info' => lang('EXT_more_info') ,
                'DASHBOARD_messages' => lang('DASHBOARD_messages') ,
                'style_msg' => $style_msg,
                'messages_title' => get_user_val('messages_title') ,
                'messages' => get_user_val('messages') ,
                'DASHBOARD_last_news' => lang('DASHBOARD_last_news') ,
                'DASHBOARD_last_help' => lang('DASHBOARD_last_help') ,
                'get_client_helper' => $get_client_helper
            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
        
        include ("footer.inc.php");
    }
} 
else {
    include 'auth.php';
}
?>
