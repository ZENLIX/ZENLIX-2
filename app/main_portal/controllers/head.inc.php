<?php

$main_styles_start = array(
    'js/bootstrap/css/bootstrap.min.css',
    'css/jquery-ui.min.css',
    'css/ionicons.min.css',
    'css/style.css',
    'css/font-awesome/css/font-awesome.min.css',
    'css/chosen.min.css',
    'css/print.css',
    'js/fancybox/jquery.fancybox.css',
    'app/main_portal/css/AdminLTE.min.css',
    'app/main_portal/css/skins/_all-skins.min.css',
    'css/dropzone.css',
    'js/bootstrap3-editable/css/bootstrap-editable.css',
    'css/summernote-bs3.css',
    'css/summernote.css'
);

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
    $template = $twig->loadTemplate('head.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'title_header' => $CONF['title_header'],
        'hostname' => $CONF['hostname'],
        'main_styles_start' => $main_styles_start,
        'version' => get_conf_param('version') ,
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}
?>