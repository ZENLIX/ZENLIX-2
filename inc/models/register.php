<?php
include_once ("head.inc.php");

//include("dbconnect.inc.php");

$allow_register = false;
if (get_conf_param('allow_register') == "true") {
    $allow_register = true;
}

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
    $template = $twig->loadTemplate('register.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'name_of_firm' => $CONF['name_of_firm'],
        'allow_register' => $allow_register,
        'get_logo_img' => get_logo_img() ,
        'MAIN_TITLE' => lang('MAIN_TITLE') ,
        'USERS_fio_full' => lang('USERS_fio_full') ,
        'USERS_login' => lang('USERS_login') ,
        'REG_button' => lang('REG_button') ,
        'REG_already' => lang('REG_already') ,
        'va' => $va,
        'error_auth' => lang('error_auth') ,
        'req' => $_SERVER['REQUEST_URI']
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}
?>
