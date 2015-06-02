<?php


$CONF['title_header'] = lang('DASHBOARD_TITLE') . " - " . $CONF['name_of_firm'];

//if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
//    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        //check_unlinked_file();
        //echo get_userlogin_byid($_SESSION['helpdesk_user_id']);
        
        
?>


<?php
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
        get_helper();
        $get_helper = ob_get_contents();
        
        //Grab output
        ob_end_clean();
        
        //Discard output buffer
        
        try {
            $basedir = dirname(dirname(__FILE__));
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/views/');
            
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
            $template = $twig->loadTemplate('dashboard.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'DASHBOARD_TITLE' => lang('DASHBOARD_TITLE') ,
                'DASHBOARD_title_ext' => lang('DASHBOARD_title_ext') ,
                'hostname' => $CONF['hostname'],
                'name_of_firm' => $CONF['name_of_firm'],
                'view_right' => $view_right,
                'global_msg_type' => get_conf_param('global_msg_type') ,
                'gm_type' => $gm_type['icon'],
                'global_msg_data' => get_conf_param('global_msg_data') ,
                'get_total_tickets_free' => get_total_tickets_free() ,
                'DASHBOARD_ticket_in' => lang('DASHBOARD_ticket_in') ,
                'EXT_more_info' => lang('EXT_more_info') ,
                'get_total_tickets_out_and_success' => get_total_tickets_out_and_success() ,
                'DASHBOARD_ticket_out' => lang('DASHBOARD_ticket_out') ,
                'get_total_tickets_lock' => get_total_tickets_lock() ,
                'DASHBOARD_ticket_lock' => lang('DASHBOARD_ticket_lock') ,
                'get_total_tickets_ok' => get_total_tickets_ok() ,
                'LIST_ok_t' => lang('LIST_ok_t') ,
                'DASHBOARD_last_help' => lang('DASHBOARD_last_help') ,
                'get_helper' => $get_helper,
                'DASHBOARD_messages' => lang('DASHBOARD_messages') ,
                'style_msg' => $style_msg,
                'messages_title' => get_user_val('messages_title') ,
                'messages' => get_user_val('messages') ,
                'DASHBOARD_last_in' => lang('DASHBOARD_last_in') ,
                'LIST_loading' => lang('LIST_loading')
            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
        
        include ("footer.inc.php");

?>