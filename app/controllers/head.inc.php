<?php

$rstyle=array();

if (get_current_URL_name('subj')) {
    $rstyle = array(
        'js/bootstrap3-editable/css/bootstrap-editable.css'
    );
}

if (get_current_URL_name('create')) {
    $rstyle = array(
        'js/bootstrap3-editable/css/bootstrap-editable.css',
        'css/daterangepicker-bs3.css',
        'js/s2/select2.css',
        'js/s2/select2-bootstrap.css',
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css'
    );
}


if (get_current_URL_name('news')) {
    $rstyle = array(
        'js/fancybox/jquery.fancybox.css'
    );
}


if (get_current_URL_name('deps')) {
    $rstyle = array(
        'js/bootstrap3-editable/css/bootstrap-editable.css',
        'css/daterangepicker-bs3.css'
    );
}

if (get_current_URL_name('units')) {
    $rstyle = array(
        'js/bootstrap3-editable/css/bootstrap-editable.css'
    );
}


if (get_current_URL_name('scheduler')) {
    $rstyle = array(
        'js/bootstrap3-editable/css/bootstrap-editable.css',
        'css/daterangepicker-bs3.css',
        'js/s2/select2.css',
        'js/s2/select2-bootstrap.css',
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css',
        'css/bootstrap-timepicker.min.css'
    );
}

if (get_current_URL_name('ticket')) {
    $rstyle = array(
        'js/s2/select2.css',
        'js/s2/select2-bootstrap.css',
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css'
    );
}

if (get_current_URL_name('users')) {
    $rstyle = array(
        'js/s2/select2.css',
        'js/s2/select2-bootstrap.css',
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css',
        'css/daterangepicker-bs3.css',
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css'
    );
}

if (get_current_URL_name('user_stats')) {
    $rstyle = array(
        'js/s2/select2.css',
        'js/s2/select2-bootstrap.css',
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css',
        'css/daterangepicker-bs3.css',
        'css/bootstrap-timepicker.min.css'
    );
}

if (get_current_URL_name('main_stats')) {
    $rstyle = array(
        'js/s2/select2.css',
        'js/s2/select2-bootstrap.css',
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css',
        'css/daterangepicker-bs3.css',
        'css/bootstrap-timepicker.min.css'
    );
}

if (get_current_URL_name('config')) {
    $rstyle = array(
        'js/s2/select2.css',
        'js/s2/select2-bootstrap.css',
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css',
        'js/bootstrap3-editable/css/bootstrap-editable.css'
    );
}

if (get_current_URL_name('sla_rep')) {
    $rstyle = array(
        'js/s2/select2.css',
        'js/s2/select2-bootstrap.css',
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css',
        'css/daterangepicker-bs3.css',
        'css/bootstrap-timepicker.min.css'
    );
}

if (get_current_URL_name('view_user')) {
    $rstyle = array(
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css'

    );
}

if (get_current_URL_name('portal')) {
    $rstyle = array(
        'js/s2/select2.css',
        'js/s2/select2-bootstrap.css',
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css'
    );
}

if (get_current_URL_name('profile')) {
    $rstyle = array(
        'js/s2/select2.css',
        'js/s2/select2-bootstrap.css',
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css',
        'css/daterangepicker-bs3.css'
    );
}

if (get_current_URL_name('helper')) {
    $rstyle = array(
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css',
        'css/summernote-bs3.css',
        'css/summernote.css',
        'js/bootstrap3-editable/css/bootstrap-editable.css'
    );
}

if (get_current_URL_name('notes')) {
    $rstyle = array(
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css',
        'css/summernote-bs3.css',
        'css/summernote.css',
        'js/bootstrap3-editable/css/bootstrap-editable.css'
    );
}

if (get_current_URL_name('mailers')) {
    $rstyle = array(
        'js/fancybox/jquery.fancybox.css',
        'css/dropzone.css',
        'css/summernote-bs3.css',
        'css/summernote.css',
        'js/bootstrap3-editable/css/bootstrap-editable.css',
        'js/s2/select2.css',
        'js/s2/select2-bootstrap.css'
    );
}



if (get_current_URL_name('calendar')) {
    $rstyle = array(
        'css/fullcalendar.min.css',
        'css/daterangepicker-bs3.css'
    );
}

$main_styles_start = array(
    'js/bootstrap/css/bootstrap.min.css',
    'css/jquery-ui.min.css',
    'css/ionicons.min.css',
    'css/style.css',
    'css/font-awesome/css/font-awesome.min.css',
    'css/chosen.min.css'
);

$main_styles_end = array(
    'css/print.css',
    'css/AdminLTE.css',
    'css/skin-blue.css'
);

$basedir = dirname(dirname(__FILE__));

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
    $template = $twig->loadTemplate('head.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'title_header' => $CONF['title_header'],
        'hostname' => $CONF['hostname'],
        'main_styles_start' => $main_styles_start,
        'main_styles_end' => $main_styles_end,
        'version' => get_conf_param('version') ,
        'page_style' => $rstyle
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}
?>











  

  
  
  