<?php
session_start();

include_once ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if (($priv_val == "2") || ($priv_val == "0")) {
            
            $u_arr = array();
            $usr_units = explode(",", unit_of_user($_SESSION['helpdesk_user_id']));
            
            $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps');
            $stmt->execute();
            $result = $stmt->fetchAll();
            foreach ($result as $row) {
                
                if (in_array($row['value'], $usr_units)) {
                    
                    $row['label'] = $row['label'];
                    $row['value'] = (int)$row['value'];
                    
                    array_push($u_arr, array(
                        
                        'value' => $row['value'],
                        'label' => $row['label']
                    ));
                }
            }
            
            $user_arr = array();
            $unit_user = unit_of_user($_SESSION['helpdesk_user_id']);
            $ee = explode(",", $unit_user);
            foreach ($ee as $key => $value) {
                array_push($user_arr, array(
                    
                    'get_unit_name_return4news' => get_unit_name_return4news($value) ,
                    'get_unit_stat_create' => get_unit_stat_create($value) ,
                    'get_unit_stat_free' => get_unit_stat_free($value) ,
                    'get_unit_stat_lock' => get_unit_stat_lock($value) ,
                    'get_unit_stat_ok' => get_unit_stat_ok($value)
                ));
            }
            
            $stat_arr = array();
            
            //$ee - массив id отделов, на которые у меня есть права
            //$ec - массив id отделов пользователей
            //если какой-то отдел совпадает вывести
            $stmt = $dbConnection->prepare('SELECT id, unit from users where is_client=0 and status!=2');
            $stmt->execute();
            $result = $stmt->fetchAll();
            if (!empty($result)) {
                
                foreach ($result as $row) {
                    $ec = explode(",", $row['unit']);
                    
                    $result = array_intersect($ee, $ec);
                    if ($result) {
                        
                        array_push($stat_arr, array(
                            
                            'name_of_user_ret' => name_of_user_ret($row['id']) ,
                            'get_user_status' => get_user_status($row['id']) ,
                            'get_total_tickets_free' => get_total_tickets_free($row['id']) ,
                            'get_total_tickets_lock' => get_total_tickets_lock($row['id']) ,
                            'get_total_tickets_ok' => get_total_tickets_ok($row['id']) ,
                            'get_total_tickets_out' => get_total_tickets_out($row['id']) ,
                            'get_total_tickets_out_and_success' => get_total_tickets_out_and_success($row['id'])
                        ));
                    }
                }
            }
            
            $basedir = dirname(dirname(__FILE__));
            
            ////////////
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
                $template = $twig->loadTemplate('all_stats.view.tmpl');
                
                // передаём в шаблон переменные и значения
                // выводим сформированное содержание
                echo $template->render(array(
                    'hostname' => $CONF['hostname'],
                    'name_of_firm' => $CONF['name_of_firm'],
                    'ALLSTATS_main' => lang('ALLSTATS_main') ,
                    'ALLSTATS_main_ext' => lang('ALLSTATS_main_ext') ,
                    'EXT_graph_user_ext' => lang('EXT_graph_user_ext') ,
                    'HELP_all' => lang('HELP_all') ,
                    'u_arr' => $u_arr,
                    'date' => date("Y-m-d") ,
                    'STATS_make' => lang('STATS_make') ,
                    'ALLSTATS_help' => lang('ALLSTATS_help') ,
                    'ALLSTATS_unit' => lang('ALLSTATS_unit') ,
                    'ALLSTATS_unit_out' => lang('ALLSTATS_unit_out') ,
                    'ALLSTATS_unit_free' => lang('ALLSTATS_unit_free') ,
                    'ALLSTATS_unit_lock' => lang('ALLSTATS_unit_lock') ,
                    'ALLSTATS_unit_ok' => lang('ALLSTATS_unit_ok') ,
                    'user_arr' => $user_arr,
                    'ALLSTATS_user' => lang('ALLSTATS_user') ,
                    'ALLSTATS_user_fio' => lang('ALLSTATS_user_fio') ,
                    't_LIST_status' => lang('t_LIST_status') ,
                    'ALLSTATS_user_free' => lang('ALLSTATS_user_free') ,
                    'ALLSTATS_user_lock' => lang('ALLSTATS_user_lock') ,
                    'ALLSTATS_user_ok' => lang('ALLSTATS_user_ok') ,
                    'ALLSTATS_user_out_all' => lang('ALLSTATS_user_out_all') ,
                    'ALLSTATS_user_out_all_not' => lang('ALLSTATS_user_out_all_not') ,
                    'stat_arr' => $stat_arr
                ));
            }
            catch(Exception $e) {
                die('ERROR: ' . $e->getMessage());
            }
            
         
            include ("footer.inc.php");
?>

<?php
        }
    }
} 
else {
    include '../auth.php';
}
?>
