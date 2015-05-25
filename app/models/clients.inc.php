<?php
session_start();

//include("../functions.inc.php");
include_once ("../../functions.inc.php");

if (isset($_POST['menu'])) {
    
    if ($_POST['menu'] == 'new') {
        if (get_user_val('priv_add_client') == "1") {
            $menu = "new";
            if (isset($_GET['ok'])) {
                $oks = true;
            }
        }
    } 
    if ($_POST['menu'] == 'list') {
        $menu = "list";
        $page = ($_POST['page']);
        $perpage = '15';
        
        $start_pos = ($page - 1) * $perpage;
        
        if (isset($_POST['t'])) {
            $t = ($_POST['t']);
            
            //$results = mysql_query("SELECT id, fio, login, tel, unit_desc, adr, email, posada from clients where ((fio like '%" . $t . "%') or (login like '%" . $t . "%')) limit $start_pos, $perpage;");
            
            $stmt = $dbConnection->prepare('SELECT id, fio, login, tel, unit, adr, email, posada, uniq_id, is_client,skype,status from users where ((fio like :t) or (login like :t2) or (tel like :t3)) and status!=2 and id!=1 limit :start_pos, :perpage');
            $stmt->execute(array(
                ':t' => '%' . $t . '%',
                ':t2' => '%' . $t . '%',
                ':t3' => '%' . $t . '%',
                ':start_pos' => $start_pos,
                ':perpage' => $perpage
            ));
            $res1 = $stmt->fetchAll();
            
            //foreach($res1 as $row) {
            
            
        }
        if (!isset($_POST['t'])) {
            
            //$results = mysql_query("SELECT id, fio, login, tel, unit_desc, adr, email, posada from clients limit $start_pos, $perpage;");
            
            $stmt = $dbConnection->prepare('SELECT id, fio, login, priv, unit, status, uniq_id,is_client,email,tel, adr, skype from users where status!=2 and id!=1 limit :start_pos, :perpage');
            $stmt->execute(array(
                ':start_pos' => $start_pos,
                ':perpage' => $perpage
            ));
            $res1 = $stmt->fetchAll();
        }
        
        $list_arr = array();
        
        //while ($row = mysql_fetch_assoc($results)) {
        foreach ($res1 as $row) {
            
            $unit = view_array(get_unit_name_return($row['unit']));
            $statuss = $row['status'];
            
            if ($row['is_client'] == "1") {
                $priv = lang('USERS_p_4');
            } 
            else if ($row['is_client'] == "0") {
                
                if ($row['priv'] == "0") {
                    $priv = lang('USERS_p_1');
                } 
                else if ($row['priv'] == "1") {
                    $priv = lang('USERS_p_2');
                } 
                else if ($row['priv'] == "2") {
                    $priv = lang('USERS_p_3');
                }
            }
            if ($statuss != "1") {
                $r = "<span class=\"label label-danger\">disable</span>";
            } 
            else if ($statuss == "1") {
                $r = "<span class=\"label label-success\">enable</span>";
            }
            
            $adr = $row['adr'];
            $tel = $row['tel'];
            $skype = $row['skype'];
            $email = $row['email'];
            $uniq_id = $row['uniq_id'];
            
            if (!empty($adr)) {
                $adr_t = true;
            }
            if (!empty($skype)) {
                $skype_t = true;
            }
            if (!empty($tel)) {
                $tel_t = true;
            }
            if (!empty($email)) {
                $mail_t = true;
            }
            
            $NAVBAR_all_tickets = false;
            if (get_clients_total_ticket($row['id']) != 0) {
                if (($row['priv'] == 2) || ($row['priv'] == 0)) {
                    $NAVBAR_all_tickets = true;
                }
            }
            
            $priv_edit_client = false;
            if (get_user_val('priv_edit_client') == "1") {
                $priv_edit_client = true;
            }
            
            array_push($list_arr, array(
                
                'uniq_id' => $row['uniq_id'],
                'fio_r' => $row['fio'],
                'r' => $r,
                'get_user_status' => get_user_status($row['id']) ,
                'get_user_img_by_id' => get_user_img_by_id($row['id']) ,
                'NAVBAR_all_t' => $NAVBAR_all_tickets,
                'NAVBAR_all_tickets' => lang('NAVBAR_all_tickets') ,
                'get_clients_total_ticket' => get_clients_total_ticket($row['id']) ,
                'priv_edit_client' => $priv_edit_client,
                'CONF_act_edit' => lang('CONF_act_edit') ,
                'adr_t' => $adr_t,
                'skype_t' => $skype_t,
                'tel_t' => $tel_t,
                'mail_t' => $mail_t,
                'login_r' => $login,
                
                //'fio_r'=>$fio,
                'email_r' => $email,
                'tel_r' => $tel,
                'skype_r' => $skype,
                'adr_r' => $adr
            ));
        }
    }
    if ($_POST['menu'] == 'edit') {
        $menu = "edit";
        $usid = ($_POST['id']);
        
        /* $query = "SELECT fio, pass, login, status, priv, unit,email,messages,lang from users where id='$usid'; ";
        $sql = mysql_query($query) or die(mysql_error());
        if (mysql_num_rows($sql) == 1) {
        $row = mysql_fetch_assoc($sql);
        */
        
        $stmt = $dbConnection->prepare('SELECT id, fio, pass, login, status, priv, unit,email,messages,lang,priv_add_client,priv_edit_client,ldap_key,pb,tel,skype,adr, unit_desc, posada, is_client,messages_title from users where uniq_id=:usid');
        $stmt->execute(array(
            ':usid' => $usid
        ));
        $res1 = $stmt->fetchAll();
        
        foreach ($res1 as $row) {
            
            $priv_add_client = $row['priv_add_client'];
            $priv_edit_client = $row['priv_edit_client'];
            $fio = $row['fio'];
            $login = $row['login'];
            $pass = $row['pass'];
            $status = $row['status'];
            $priv = $row['priv'];
            $unit = $row['unit'];
            $email = $row['email'];
            $messages = $row['messages'];
            $langu = $row['lang'];
            $lk = $row['ldap_key'];
            $push = $row['pb'];
            $tel = $row['tel'];
            $skype = $row['skype'];
            $adr = $row['adr'];
            $msg_t = $row['messages_title'];
            
            $unitss = $row['unit_desc'];
            $posada = $row['posada'];
            
            $is_client = $row['is_client'];
            
            if ($priv_add_client == "1") {
                $priv_add_client = "checked";
            } 
            else {
                $priv_add_client = "";
            }
            if ($priv_edit_client == "1") {
                $priv_edit_client = "checked";
            } 
            else {
                $priv_edit_client = "";
            }
            
            if ($lk == "1") {
                $lk_status = "checked";
                $pd = "disabled";
            } 
            else {
                $lk_status = "";
                $pd = "";
            }
            
            if ($langu == "en") {
                $status_lang_en = "selected";
            } 
            else if ($langu == "ru") {
                $status_lang_ru = "selected";
            } 
            else if ($langu == "ua") {
                $status_lang_ua = "selected";
            }
            
            if ($status == "0") {
                $status_lock = "selected";
            }
            if ($status == "1") {
                $status_unlock = "selected";
            }
            
            if ($is_client == "1") {
                $status_client = "checked";
            } 
            else if ($is_client == "0") {
                
                if ($priv == "0") {
                    $status_admin = "checked";
                } 
                else if ($priv == "1") {
                    $status_user = "checked";
                } 
                else if ($priv == "2") {
                    $status_superadmin = "checked";
                }
            }
        }
        if (isset($_GET['ok'])) {
            $oks2 = true;
        }
    }
    
    $pos_arr11 = array();
    $stmt11 = $dbConnection->prepare('SELECT name FROM posada order by name COLLATE utf8_unicode_ci ASC');
    $stmt11->execute();
    $res1 = $stmt11->fetchAll();
    foreach ($res1 as $row) {
        
        $se = "";
        if ($posada == $row['name']) {
            $se = "selected";
        }
        array_push($pos_arr11, array(
            
            'name' => $row['name'],
            'se' => $se
        ));
    }
    
    $pos_arr22 = array();
    $stmt22 = $dbConnection->prepare('SELECT name FROM units order by name COLLATE utf8_unicode_ci ASC');
    $stmt22->execute();
    $res1 = $stmt22->fetchAll();
    foreach ($res1 as $row) {
        $se2 = "";
        if ($unitss == $row['name']) {
            $se2 = "selected";
        }
        array_push($pos_arr22, array(
            
            'name' => $row['name'],
            'se' => $se
        ));
    }
    
    $pos_arr = array();
    $stmt1 = $dbConnection->prepare('SELECT name FROM posada order by name COLLATE utf8_unicode_ci ASC');
    $stmt1->execute();
    $res1 = $stmt1->fetchAll();
    foreach ($res1 as $row) {
        array_push($pos_arr, array(
            
            'name' => $row['name']
        ));
    }
    
    $unit_arr = array();
    $stmt2 = $dbConnection->prepare('SELECT name FROM units order by name COLLATE utf8_unicode_ci ASC');
    $stmt2->execute();
    $res1 = $stmt2->fetchAll();
    foreach ($res1 as $row) {
        array_push($unit_arr, array(
            
            'name' => $row['name']
        ));
    }
    $priv_edit_client = false;
    if (get_user_val('priv_edit_client') == "1") {
        $priv_edit_client = true;
    }
    
    $basedir = dirname(dirname(__FILE__));
    
    ////////////
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
        $template = $twig->loadTemplate('clients_inc.view.tmpl');
        
        // передаём в шаблон переменные и значения
        // выводим сформированное содержание
        echo $template->render(array(
            'hostname' => $CONF['hostname'],
            'name_of_firm' => $CONF['name_of_firm'],
            'menu' => $menu,
            'oks' => $oks,
            'USERS_new_add' => lang('USERS_new_add') ,
            'USERS_login' => lang('USERS_login') ,
            'USERS_fio' => lang('USERS_fio') ,
            'USERS_fio_full' => lang('USERS_fio_full') ,
            'USERS_mail' => lang('USERS_mail') ,
            'WORKER_tel' => lang('WORKER_tel') ,
            'APPROVE_adr' => lang('APPROVE_adr') ,
            'WORKER_posada' => lang('WORKER_posada') ,
            'pos_arr' => $pos_arr,
            'WORKER_unit' => lang('WORKER_unit') ,
            'unit_arr' => $unit_arr,
            'USERS_make_create' => lang('USERS_make_create') ,
            'list_arr' => $list_arr,
            'oks2' => $oks2,
            'EXT_client_add_after' => lang('EXT_client_add_after') ,
            
            'pos_arr11' => $pos_arr11,
            'pos_arr22' => $pos_arr22,
            'priv_edit_client' => $priv_edit_client,
            'usid' => $usid,
            'USERS_make_edit_user' => lang('USERS_make_edit_user') ,
            'login' => $login,
            'fio' => $fio,
            'email' => $email,
            'tel' => $tel,
            'skype' => $skype,
            'adr' => $adr
        ));
    }
    catch(Exception $e) {
        die('ERROR: ' . $e->getMessage());
    }
}
?>
