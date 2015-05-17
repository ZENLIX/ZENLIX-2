<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if (($priv_val == "2") || ($priv_val == "0")) {
            
            $ulist = array();
            $stmt = $dbConnection->prepare('SELECT fio as label, id as value, unit FROM users where id !=:system and is_client=0 and status!=2 order by fio ASC');
            $stmt->execute(array(
                ':system' => '1'
            ));
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $row) {
                $unit_user = unit_of_user($_SESSION['helpdesk_user_id']);
                $ee = explode(",", $unit_user);
                $ec = explode(",", $row['unit']);
                
                $result = array_intersect($ee, $ec);
                
                if ($result) {
                    
                    //echo($row['label']);
                    $row['label'] = $row['label'];
                    $row['value'] = (int)$row['value'];
                    
                    if (get_user_status_text($row['value']) == "online") {
                        $s = "online";
                    } 
                    else if (get_user_status_text($row['value']) == "offline") {
                        $s = "offline";
                    }
                    
                    array_push($ulist, array(
                        
                        's' => $s,
                        'value' => $row['value'],
                        'nameshort' => nameshort($row['label'])
                    ));
                }
            }
            
            $basedir = dirname(dirname(__FILE__));
            
            ////////////
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
                $template = $twig->loadTemplate('user_stats.view.tmpl');
                
                // передаём в шаблон переменные и значения
                // выводим сформированное содержание
                echo $template->render(array(
                    'hostname' => $CONF['hostname'],
                    'name_of_firm' => $CONF['name_of_firm'],
                    'EXT_graph_user_ext' => lang('EXT_graph_user_ext') ,
                    'EXT_graph_user' => lang('EXT_graph_user') ,
                    't_LIST_worker' => lang('t_LIST_worker') ,
                    'ulist' => $ulist,
                    'date' => date("Y-m-d") ,
                    'STATS_make' => lang('STATS_make') ,
                    'EXT_graph_user_ext2' => lang('EXT_graph_user_ext2') ,
                    'EXT_stats_main_todo' => lang('EXT_stats_main_todo')
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
