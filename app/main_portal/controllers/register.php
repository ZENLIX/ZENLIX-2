

<?php
include "head.inc.php";

include "navbar.inc.php";

if (get_conf_param('allow_register') == "true") {
    
    $basedir = dirname(dirname(dirname(__FILE__)));
    
    try {
        
        // указывае где хранятся шаблоны
        $loader = new Twig_Loader_Filesystem($basedir . '/main_portal/views');
        
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
        $template = $twig->loadTemplate('register.view.tmpl');
        
        // передаём в шаблон переменные и значения
        // выводим сформированное содержание
        echo $template->render(array(
            'hostname' => $CONF['hostname'],
            'PORTAL_reg_user' => lang('PORTAL_reg_user') ,
            'get_logo_img' => get_logo_img() ,
            'PORTAL_fio' => lang('PORTAL_fio') ,
            'PORTAL_login_name' => lang('PORTAL_login_name') ,
            'PORTAL_email' => lang('PORTAL_email') ,
            'PORTAL_reg' => lang('PORTAL_reg')
        ));
    }
    catch(Exception $e) {
        die('ERROR: ' . $e->getMessage());
    }
}
include "footer.inc.php";
?>