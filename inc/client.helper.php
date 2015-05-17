<?php
session_start();
include_once "../functions.inc.php";

if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("client.navbar.inc.php");
        
        if (isset($_GET['h'])) {
            $get_type = "h";
            $h = ($_GET['h']);
            
            $stmt = $dbConnection->prepare('select id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where hashname=:h');
            $stmt->execute(array(
                ':h' => $h
            ));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
        } 
        else if (isset($_GET['cat'])) {
            $get_type = "cat";
            $cat_id = $_GET['cat'];
            
            $stmt = $dbConnection->prepare('SELECT name from helper_cat where id=:p_id');
            $stmt->execute(array(
                ':p_id' => $cat_id
            ));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            ob_start();
            show_item_helper_cat($cat_id);
            $show_item_helper_cat = ob_get_contents();
            ob_end_clean();
        } 
        else {
            $get_type = "else";
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
            $template = $twig->loadTemplate('client.helper.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'hostname' => $CONF['hostname'],
                'name_of_firm' => $CONF['name_of_firm'],
                'get_type' => $get_type,
                'HELPER_title' => lang('HELPER_title') ,
                'HELPER_back' => lang('HELPER_back') ,
                'title' => make_html($fio['title']) ,
                'message' => $fio['message'],
                'HELPER_pub' => lang('HELPER_pub') ,
                'user_init_id' => nameshort(name_of_user_ret($fio['user_init_id'])) ,
                'dt' => $fio['dt'],
                'HELPER_print' => lang('HELPER_print') ,
                'name' => $row['name'],
                'show_item_helper_cat' => $show_item_helper_cat,
                'HELPER_desc' => lang('HELPER_desc') ,
                'HELPER_find' => lang('HELPER_find') ,
                'HELPER_info' => lang('HELPER_info')
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
