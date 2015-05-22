<?php
session_start();
function cur_file_name() {
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");
    $file = $_SERVER['REQUEST_URI'];
    $file = explode("?", basename($file));
    $current_file_name = $file[0];
    
    //$file = $_SERVER['REQUEST_URI'];
    //$file = explode("?", basename($file));
    
    //if ($current_file_name == $requestUri) echo 'class="active"';
    $current_file_name = explode('&', $current_file_name);
    $cfn = $current_file_name[0];
    
    return $cfn;
}

$validate = false;
if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    $validate = true;
}
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    $validate = true;
}
if (validate_admin($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    $validate = true;
}

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
    $template = $twig->loadTemplate('navbar.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'get_logo_img' => get_logo_img('small') ,
        'name_of_firm' => $CONF['name_of_firm'],
        'PORTAL_news' => lang('PORTAL_news') ,
        'PORTAL_versions' => lang('PORTAL_versions') ,
        'PORTAL_help_center' => lang('PORTAL_help_center') ,
        'PORTAL_cats' => lang('PORTAL_cats') ,
        'PORTAL_idea' => lang('PORTAL_idea') ,
        'PORTAL_trouble' => lang('PORTAL_trouble') ,
        'PORTAL_question' => lang('PORTAL_question') ,
        'PORTAL_thank' => lang('PORTAL_thank') ,
        'PORTAL_find' => lang('PORTAL_find') ,
        'validate' => $validate,
        'PORTAL_helpdesk' => lang('PORTAL_helpdesk') ,
        'get_user_img' => get_user_img() ,
        'fio' => nameshort(get_user_val('fio')) ,
        'posada' => get_user_val('posada') ,
        'NAVBAR_profile' => lang('NAVBAR_profile') ,
        'PORTAL_logout' => lang('PORTAL_logout') ,
        'PORTAL_login' => lang('PORTAL_login') ,
        'PORTAL_register' => lang('PORTAL_register') ,
        'cur_file_name' => cur_file_name() ,
        'allow_register' => get_conf_param('allow_register')
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}


?>
  </nav>
</header>