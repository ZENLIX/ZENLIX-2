<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {

                   $CONF['title_header'] = lang('CALENDAR_title') . " - " . $CONF['name_of_firm'];



        include ("head.inc.php");
        include ("navbar.inc.php");


$basedir = dirname(dirname(__FILE__)); 
            ////////////
    try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/inc/views');
            
            // инициализируем Twig
            $twig = new Twig_Environment($loader);
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('calendar.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'hostname'=>$CONF['hostname'],
                'name_of_firm'=>$CONF['name_of_firm'],
                'CALENDAR_title'=>lang('CALENDAR_title'),
                'CALENDAR_title_desc'=>lang('CALENDAR_title_desc'),
                'CALENDAR_dr_ev'=>lang('CALENDAR_dr_ev'),
                'CALENDAR_ex_ev_3'=>lang('CALENDAR_ex_ev_3'),
                'CALENDAR_ex_ev_2'=>lang('CALENDAR_ex_ev_2'),
                'CALENDAR_ex_ev_4'=>lang('CALENDAR_ex_ev_4'),
                'CALENDAR_ex_ev_5'=>lang('CALENDAR_ex_ev_5'),
                'CALENDAR_ex_ev_1'=>lang('CALENDAR_ex_ev_1'),
                'CALENDAR_del_after_drag'=>lang('CALENDAR_del_after_drag'),
                'CALENDAR_create_event'=>lang('CALENDAR_create_event'),
                'CALENDAR_name'=>lang('CALENDAR_name'),
                'CALENDAR_add'=>lang('CALENDAR_add'),
                'CALENDAR_filter'=>lang('CALENDAR_filter'),
                'CALENDAR_private'=>lang('CALENDAR_private'),
                'CALENDAR_dep'=>lang('CALENDAR_dep'),
                'CALENDAR_corp'=>lang('CALENDAR_corp'),
                'CALENDAR_event'=>lang('CALENDAR_event'),
                'CALENDAR_author'=>lang('CALENDAR_author'),
                'CALENDAR_close'=>lang('CALENDAR_close'),
                'CALENDAR_edit_event'=>lang('CALENDAR_edit_event'),

                'CALENDAR_description'=>lang('CALENDAR_description'),
                'CALENDAR_allday'=>lang('CALENDAR_allday'),
                'CALENDAR_allday_desc'=>lang('CALENDAR_allday_desc'),
                'CALENDAR_period'=>lang('CALENDAR_period'),
                'CALENDAR_visibility'=>lang('CALENDAR_visibility'),
                'CALENDAR_e_1'=>lang('CALENDAR_e_1'),
                'CALENDAR_e_2'=>lang('CALENDAR_e_2'),
                'CALENDAR_e_3'=>lang('CALENDAR_e_3'),
                'CALENDAR_color'=>lang('CALENDAR_color'),
                'CALENDAR_cur_color'=>lang('CALENDAR_cur_color'),
                'CALENDAR_del'=>lang('CALENDAR_del'),
                'CALENDAR_save'=>lang('CALENDAR_save')






            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }



        include ("footer.inc.php");

    }
} else {
    include 'auth.php';
}
?>