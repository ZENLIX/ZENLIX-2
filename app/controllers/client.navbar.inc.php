 <?php
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

$current_file_name = $file[0];

$current_file_name = explode('&', $current_file_name);
$cfn = $current_file_name[0];

$main_portal = $CONF['main_portal'];

$newt = get_total_client_tickets_out();
$newt2 = get_total_client_tickets_ok();
$newt = $newt - $newt2;

if ($newt != 0) {
    $newtickets = " <small class=\"badge pull-right bg-red\">" . $newt . "</small>";
}
if ($newt <= 0) {
    $newtickets = "";
}

$ap = get_approve();
if ($ap != 0) {
    $apr = "
    <small class=\"badge pull-right bg-yellow\">" . $ap . "</small>";
}
if ($ap == 0) {
    $apr = "";
}

if ($main_portal == "true") {
    $index_page = "dashboard";
} 
else if ($main_portal == "false") {
    $index_page = "index.php";
}

$style_hide = "display:none;";
if (get_current_URL_name('print_ticket')) {
    $style_hide = "";
}


$tm = get_total_unread_messages();
if ($tm != 0) {
    $atm = "
    <small id=\"label_msg\"> <small class=\"badge pull-right bg-yellow\">" . $tm . "</small></small>";
} 
else if ($tm == 0) {
    $atm = "<small id=\"label_msg\"></small>";
}


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
    $template = $twig->loadTemplate('client.navbar.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'index_page' => $index_page,
        'img_logo_small' => get_logo_img('small') ,
        'name_of_firm' => $CONF['name_of_firm'],
        'EXT_toggle_nav' => lang('EXT_toggle_nav') ,
        'main_portal_active' => $main_portal,
        'PORTAL_title' => lang('PORTAL_title') ,
        'atm'=>$atm,
        'namemy' => nameshort(get_user_val('fio')) ,
        'usr_img' => get_user_img() ,
        'usr_fio' => get_user_val('fio') ,
        'usr_posada' => get_user_val('posada') ,
        
        'NAVBAR_help' => lang('NAVBAR_help') ,
        'NAVBAR_profile' => lang('NAVBAR_profile') ,
        'NAVBAR_logout' => lang('NAVBAR_logout') ,
        'EXT_hello' => lang('EXT_hello') ,
        'MESSAGES_navbar' => lang('MESSAGES_navbar') ,
        'usr_name' => get_user_name(get_user_val('fio')) ,
        'LIST_find_button' => lang('LIST_find_button') ,
        'LIST_find_ph' => lang('LIST_find_ph') ,
        'style_hide' => $style_hide,
        'val_admin' => $val_admin,
        'some_priv' => $some_priv,
        'DASHBOARD_TITLE' => lang('DASHBOARD_TITLE') ,
        'NAVBAR_create_ticket' => lang('NAVBAR_create_ticket') ,
        'NAVBAR_list_ticket' => lang('NAVBAR_list_ticket') ,
        'NAVBAR_helper' => lang('NAVBAR_helper') ,
        'cur_file_name' => cur_file_name() ,
        'newtickets' => $newtickets,
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}
?>
        