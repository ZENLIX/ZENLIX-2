<?php
session_start();

$validate = false;
if ((validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) || (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code']))) {
    $validate = true;
}

if ($validate == true) {
    
    include "head.inc.php";
    
    include "navbar.inc.php";
    
    if ($_GET['session_key']) {
        
        if ($_SESSION['zenlix_portal_post']) {
            $subj = $_SESSION['zenlix_portal_post'];
        }
    }
    
    switch ($_GET['p']) {
        case '1':
            $type['1'] = "selected";
            break;

        case '2':
            $type['2'] = "selected";
            break;

        case '3':
            $type['3'] = "selected";
            break;

        case '4':
            $type['4'] = "selected";
            break;

        default:
            $type['1'] = "selected";
            break;
    }
    
    ob_start();
    view_stat_cat();
    $view_stat_cat = ob_get_contents();
    ob_end_clean();
    
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
        $template = $twig->loadTemplate('new_post.view.tmpl');
        
        // передаём в шаблон переменные и значения
        // выводим сформированное содержание
        echo $template->render(array(
            'hostname' => $CONF['hostname'],
            'PORTAL_new_msg' => lang('PORTAL_new_msg') ,
            'PORTAL_subj' => lang('PORTAL_subj') ,
            'subj' => $subj,
            'type1' => $type['1'],
            'type2' => $type['2'],
            'type3' => $type['3'],
            'type4' => $type['4'],
            'PORTAL_idea_one' => lang('PORTAL_idea_one') ,
            'PORTAL_trouble_one' => lang('PORTAL_trouble_one') ,
            'PORTAL_question_one' => lang('PORTAL_question_one') ,
            'PORTAL_thank_one' => lang('PORTAL_thank_one') ,
            'PORTAL_fileplace' => lang('PORTAL_fileplace') ,
            'PORTAL_news_create' => lang('PORTAL_news_create') ,
            'time' => md5(time()) ,
            'view_stat_cat' => $view_stat_cat
        ));
    }
    catch(Exception $e) {
        die('ERROR: ' . $e->getMessage());
    }
    
    include "footer.inc.php";
} 
else if ($validate == false) {
    
    header("Location: " . site_proto() . $_SERVER['HTTP_HOST'] . $CONF['hostname'] . "auth");
}
?>