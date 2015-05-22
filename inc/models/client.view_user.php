<?php
session_start();
include_once ("../functions.inc.php");

if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    //if (validate_admin($_SESSION['helpdesk_user_id'])) {
    include ("head.inc.php");
    include ("client.navbar.inc.php");
    
    $rkeys = array_keys($_GET);
    $hn = $rkeys[0];
    $stmt = $dbConnection->prepare('SELECT 
                            id, fio, posada, unit_desc, usr_img, tel, skype, last_time, status,email, adr, is_client, uniq_id
                            from users
                            where uniq_id=:hn limit 1');
    
    $stmt->execute(array(
        ':hn' => $hn
    ));
    $res1 = $stmt->fetchAll();
    if (!empty($res1)) {
        foreach ($res1 as $row) {
            $user_id = $row['id'];
            $user_fio = $row['fio'];
            $user_posada = $row['posada'];
            $user_unit = $row['unit_desc'];
            $is_client = $row['is_client'];
            $user_tel = $row['tel'];
            $user_skype = $row['skype'];
            $user_last_time = $row['last_time'];
            $user_status = $row['last_status'];
            $user_mail = $row['email'];
            $user_adr = $row['adr'];
            $uniq_id = $row['uniq_id'];
            
            $user_status = $row['status'];
            
            if ($row['usr_img']) {
                $user_img = $CONF['hostname'] . 'upload_files/avatars/' . $row['usr_img'];
            } 
            else if (!$row['usr_img']) {
                $user_img = $CONF['hostname'] . 'img/avatar5.png';
            }
        }
        $finduser = true;
    } 
    else {
        $finduser = false;
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
        $template = $twig->loadTemplate('client.view_user.view.tmpl');
        
        // передаём в шаблон переменные и значения
        // выводим сформированное содержание
        echo $template->render(array(
            'hostname' => $CONF['hostname'],
            'name_of_firm' => $CONF['name_of_firm'],
            'finduser' => $finduser,
            'VIEWUSER_title' => lang('VIEWUSER_title') ,
            'VIEWUSER_title_ext' => lang('VIEWUSER_title_ext') ,
            'TICKET_t_no' => lang('TICKET_t_no') ,
            'user_status' => $user_status,
            'user_fio' => $user_fio,
            'user_img' => $user_img,
            'USER_DEL_main' => lang('USER_DEL_main') ,
            'USER_DEL_info' => lang('USER_DEL_info') ,
            'user_posada' => $user_posada,
            'P_main' => lang('P_main') ,
            'get_user_status' => get_user_status($user_id) ,
            'user_mail' => $user_mail,
            'APPROVE_mail' => lang('APPROVE_mail')
        ));
    }
    catch(Exception $e) {
        die('ERROR: ' . $e->getMessage());
    }
    
    include ("footer.inc.php");
?>

<?php
    
    //}
    
    
} 
else {
    include 'auth.php';
}
?>
