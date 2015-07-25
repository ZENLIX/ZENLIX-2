<?php
global $lang;

$JS_conf_arr = array(
    'MyHOSTNAME' => site_proto().$_SERVER['HTTP_HOST'].$CONF['hostname'],
    'MyLANG' => $lang,
    'NODE_URL' => get_conf_param('node_port') ,
    'VALIDATE' => check_validate()
);

$main_js_start = array(
    'js/jquery-2.1.3.min.js',
    'js/bootstrap/js/bootstrap.min.js',
    'js/jquery.titlealert.js',
    'js/noty/packaged/jquery.noty.packaged.min.js',
    'js/ion.sound.min.js',
    'js/dropzone.js',
    'js/icheck.min.js',
    'js/bootbox.min.js',
    'js/moment.min.js',
    'js/moment-timezone-with-data-2010-2020.min.js',
    'js/bootstrap-paginator.js',
    'js/moment-with-langs.js',
    'js/jquery-ui-1.10.4.custom.min.js',
    'js/chosen.jquery.min.js',
    'js/summernote.min.js',
    'js/summernote-lang.js',
    'js/jquery.mjs.nestedSortable.js',
    'js/bootstrap3-editable/js/bootstrap-editable.min.js',
    'js/fancybox/jquery.fancybox.js',
    'js/core_portal.js'
);

$basedir = dirname(dirname(dirname(__FILE__)));

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
    $template = $twig->loadTemplate('footer.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'JS_conf_arr' => $JS_conf_arr,
        'version' => get_conf_param('version') ,
        'main_js_start' => $main_js_start,
        'date' => date('Y')
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}
?>
    