<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        
        $CONF['title_header'] = lang('NOTES_title') . " - " . $CONF['name_of_firm'];
        
        include ("head.inc.php");
        include ("navbar.inc.php");
        
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
            $template = $twig->loadTemplate('notes.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            $main_arr = array(
                'NOTES_title' => lang('NOTES_title') ,
                'NOTES_title_ext' => lang('NOTES_title_ext') ,
                'hostname' => $CONF['hostname'],
                'name_of_firm' => $CONF['name_of_firm'],
                'NOTES_title' => lang('NOTES_title') ,
                'NOTES_create' => lang('NOTES_create') ,
                'NOTES_cr' => lang('NOTES_cr') ,
                'NOTES_link' => lang('NOTES_link')
            );
            
            $main_arr = array_merge($main_arr);
            
            echo $template->render($main_arr);
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