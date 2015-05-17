 
<?php
global $lang;

if ($lang == "ua") {
    $lang = "uk";
}

if (get_current_URL_name('create')) {
    $page_js_res = array(
        'js/jquery.ui.autocomplete.js',
        'js/daterangepicker.js',
        'js/dropzone.js',
        'js/bootstrap3-editable/js/bootstrap-editable.min.js',
        'js/s2/select2.min.js',
        'js/jquery.autosize.min.js'
    );
}
if (get_current_URL_name('scheduler')) {
    $page_js_res = array(
        'js/jquery.ui.autocomplete.js',
        'js/daterangepicker.js',
        'js/dropzone.js',
        'js/bootstrap3-editable/js/bootstrap-editable.min.js',
        'js/s2/select2.min.js',
        'js/jquery.autosize.min.js',
        'js/jqueryKnob/jquery.knob.js',
        'js/bootstrap-timepicker.min.js'
    );
}
if (get_current_URL_name('deps')) {
    $page_js_res = array(
        'js/bootstrap3-editable/js/bootstrap-editable.min.js'
    );
}
if (get_current_URL_name('list')) {
    $page_js_res = array(
        'js/bootstrap-paginator.js',
        'js/bootstrap3-editable/js/bootstrap-editable.min.js',
        'js/daterangepicker.js'
    );
}
if (get_current_URL_name('users')) {
    $page_js_res = array(
        'js/bootstrap-paginator.js',
        'js/bootstrap3-editable/js/bootstrap-editable.min.js',
        'js/daterangepicker.js',
        'js/s2/select2.min.js',
        'js/jquery.autosize.min.js'
    );
}
if (get_current_URL_name('clients')) {
    $page_js_res = array(
        'js/bootstrap-paginator.js'
    );
}
if (get_current_URL_name('ticket')) {
    $page_js_res = array(
        'js/s2/select2.min.js',
        'js/jquery.autosize.min.js',
        'js/bootstrap.file-input.js',
        'js/fancybox/jquery.fancybox.js',
        'js/moment-duration-format.js'
    );
}
if (get_current_URL_name('config')) {
    $page_js_res = array(
        'js/s2/select2.min.js',
        'js/jquery.autosize.min.js',
        'js/jquery.mjs.nestedSortable.js',
        'js/bootstrap3-editable/js/bootstrap-editable.min.js',
        'js/bootbox.min.js'
    );
}
if (get_current_URL_name('portal')) {
    $page_js_res = array(
        'js/s2/select2.min.js',
        'js/jquery.autosize.min.js'
    );
}
if (get_current_URL_name('profile')) {
    $page_js_res = array(
        'js/s2/select2.min.js',
        'js/jquery.autosize.min.js'
    );
}
if (get_current_URL_name('helper')) {
    $page_js_res = array(
        'js/summernote.min.js',
        'js/summernote-lang.js',
        'js/dropzone.js',
        'js/fancybox/jquery.fancybox.js',
        'js/jquery.mjs.nestedSortable.js',
        'js/bootstrap3-editable/js/bootstrap-editable.min.js',
        'js/bootbox.min.js'
    );
}
if (get_current_URL_name('notes')) {
    $page_js_res = array(
        'js/summernote.min.js',
        'js/summernote-lang.js',
        'js/dropzone.js',
        'js/fancybox/jquery.fancybox.js'
    );
}
if (get_current_URL_name('mailers')) {
    $page_js_res = array(
        'js/summernote.min.js',
        'js/summernote-lang.js',
        'js/dropzone.js',
        'js/fancybox/jquery.fancybox.js',
        'js/daterangepicker.js',
        'js/s2/select2.min.js',
        'js/jqueryKnob/jquery.knob.js',
        'js/bootstrap-timepicker.min.js'
    );
}
if (get_current_URL_name('stats')) {
    $page_js_res = array(
        'js/highcharts.js'
    );
}
if (get_current_URL_name('view_user')) {
    $page_js_res = array(
        'js/jqueryKnob/jquery.knob.js'
    );
}
if (get_current_URL_name('user_stats')) {
    $page_js_res = array(
        'js/daterangepicker.js',
        'js/s2/select2.min.js',
        'js/jqueryKnob/jquery.knob.js',
        'js/bootstrap-timepicker.min.js'
    );
}
if (get_current_URL_name('main_stats')) {
    $page_js_res = array(
        'js/daterangepicker.js',
        'js/s2/select2.min.js',
        'js/jqueryKnob/jquery.knob.js',
        'js/bootstrap-timepicker.min.js'
    );
}
if (get_current_URL_name('sla_rep')) {
    $page_js_res = array(
        'js/daterangepicker.js',
        'js/s2/select2.min.js',
        'js/jqueryKnob/jquery.knob.js',
        'js/bootstrap-timepicker.min.js'
    );
}
if (get_current_URL_name('subj')) {
    $page_js_res = array(
        'js/jquery.mjs.nestedSortable.js',
        'js/bootstrap3-editable/js/bootstrap-editable.min.js',
        'js/bootbox.min.js'
    );
}
if (get_current_URL_name('calendar')) {
    $page_js_res = array(
        'js/fullcalendar.min.js',
        'js/lang-all.js',
        'js/daterangepicker.js'
    );
}
 
$JS_conf_arr = array(
    'MyHOSTNAME' => site_proto().$_SERVER['HTTP_HOST'].$CONF['hostname'],
    'MyLANG' => $lang,
    'USER_HASH' => get_user_val('uniq_id') ,
    'NODE_URL' => get_conf_param('node_port') ,
    'USER_noty_layot' => get_user_val_by_id($_SESSION['helpdesk_user_id'], 'noty_layot') ,
    'MOMENTJS_DAY' => lang('MOMENTJS_DAY') ,
    'MOMENTJS_HOUR' => lang('MOMENTJS_HOUR') ,
    'MOMENTJS_MINUTE' => lang('MOMENTJS_MINUTE') ,
    'MOMENTJS_SEC' => lang('MOMENTJS_SEC') ,
    'ZENLIX_session_id' => $_SESSION['zenlix.session_id'],
    'CAL_today' => lang('CALENDAR_today') ,
    'CAL_month' => lang('CALENDAR_month') ,
    'CAL_week' => lang('CALENDAR_week') ,
    'CAL_day' => lang('CALENDAR_day')
);

$main_js_start = array(
    'js/jquery-2.1.3.min.js',
    'js/bootstrap/js/bootstrap.min.js',
    'js/icheck.min.js',
    'js/app.js',
    'js/jquery.titlealert.js',
    'js/noty/packaged/jquery.noty.packaged.min.js',
    'js/ion.sound.min.js',
    'js/moment.min.js',
    'js/moment-timezone-with-data-2010-2020.min.js',
    'js/moment-with-langs.js',
    'js/jquery-ui-1.10.4.custom.min.js',
    'js/chosen.jquery.min.js',
    'js/bootbox.min.js'
);

$main_js_stop = array(
    'js/socket.io-1.1.0.js',
    'js/core.js'
);

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
    $template = $twig->loadTemplate('footer.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'JS_conf_arr' => $JS_conf_arr,
        'version' => get_conf_param('version') ,
        'main_js_start' => $main_js_start,
        'main_js_stop' => $main_js_stop,
        'page_js' => $page_js_res,
        'date' => date('Y') ,
        'style_hide' => $style_hide
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}
?>
