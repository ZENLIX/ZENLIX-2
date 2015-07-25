<?php
//session_start();
include_once ("functions.inc.php");
include_once ("library/SimpleImage/SimpleImage.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    //if (validate_admin($_SESSION['helpdesk_user_id'])) {
    
    $CONF['title_header'] = lang('NAVBAR_profile') . " - " . $CONF['name_of_firm'];
    
    include ("head.inc.php");
    include ("navbar.inc.php");
    
    if (isset($_FILES["file"])) {
        $output_dir = "upload_files/avatars/";
        $allowedExts = array(
            "jpg",
            "jpeg",
            "gif",
            "png",
            "bmp"
        );
        $extension = end(explode(".", $_FILES["file"]["name"]));
        $fhash = randomhash();
        $fileName = $_FILES["file"]["name"];
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileName_norm = $fhash . "." . $ext;
        
        //echo $_FILES["file"]["size"];
        
        if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] < 2000000) && in_array($extension, $allowedExts)) {
            
            if ($_FILES["file"]["error"] > 0) {
                
                //echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
                
                
            } 
            else {
                 
                move_uploaded_file($_FILES["file"]["tmp_name"], $output_dir . $fileName_norm);
                $nf = $output_dir . $fileName_norm;
                
                /*
                $image = new SimpleImage();
                $image->load($nf);
                $image->resizeToHeight(200);
                
                $image->save($nf);
                */
                
                $image = new abeautifulsite\SimpleImage($nf);
                $image->adaptive_resize(250, 250)->save($nf);
                
                //$image->save($nf);
                
                $u = $_SESSION['helpdesk_user_id'];
                $stmt = $dbConnection->prepare('update users set usr_img = :uimg where id=:uid ');
                $stmt->execute(array(
                    ':uimg' => $fileName_norm,
                    ':uid' => $u
                ));
                
                //}
                
                //$_FILES["file"]["name"];
                
                
            }
        } 
        else {
            
            //echo $_FILES["file"]["type"]."<br />";
            //echo "Invalid file";
            
            
        }
    }
    
    $usid = $_SESSION['helpdesk_user_id'];
    
    //$query = "SELECT fio, pass, login, status, priv, unit,email, lang from users where id='$usid'; ";
    //    $sql = mysql_query($query) or die(mysql_error());
    
    $stmt = $dbConnection->prepare('SELECT pb,fio, pass, login, status, priv, unit,email, lang, tel, skype, adr, unit_desc, posada,mob from users where id=:usid');
    $stmt->execute(array(
        ':usid' => $usid
    ));
    $res1 = $stmt->fetchAll();
    
    //if (mysql_num_rows($sql) == 1) {
    //$row = mysql_fetch_assoc($sql);
    foreach ($res1 as $row) {
        
        $fio = $row['fio'];
        $login = $row['login'];
        $pass = $row['pass'];
        $email = $row['email'];
        $tel = $row['tel'];
        $skype = $row['skype'];
        $adr = $row['adr'];
        if ($row['mob'] == "0") {
            $row['mob'] = "";
        }
        $mob = $row['mob'];
        
        $unitss = $row['unit_desc'];
        $posada = $row['posada'];
        $push = $row['pb'];
        $langu = $row['lang'];
        
$status_lang_en=Null;
$status_lang_ru=Null;
$status_lang_ua=Null;


        if ($langu == "en") {
            $status_lang_en = "selected";
        } 
        else if ($langu == "ru") {
            $status_lang_ru = "selected";
        } 
        else if ($langu == "ua") {
            $status_lang_ua = "selected";
        }
    }
    $get_user_val_posada=Null;
    if (get_user_val('posada') != 0) {
        $get_user_val_posada = get_user_val('posada');
    }
    
    $pos_arr = array();
    $stmt = $dbConnection->prepare('SELECT name FROM posada order by name COLLATE utf8_unicode_ci ASC');
    $stmt->execute();
    $res1 = $stmt->fetchAll();
    foreach ($res1 as $row) {
        
        $se = "";
        if ($posada == $row['name']) {
            $se = "selected";
        }
        array_push($pos_arr, array(
            'se' => $se,
            'name' => $row['name']
        ));
    }
    
    $unit_arr = array();
    $stmt = $dbConnection->prepare('SELECT name FROM units order by name COLLATE utf8_unicode_ci ASC');
    $stmt->execute();
    $res1 = $stmt->fetchAll();
    foreach ($res1 as $row) {
        
        $se2 = "";
        if ($unitss == $row['name']) {
            $se2 = "selected";
        }
        array_push($unit_arr, array(
            'se' => $se2,
            'name' => $row['name']
        ));
    }
    
    $ufields = false;
    $fields_arr = array();
    $stmt = $dbConnection->prepare('SELECT * FROM user_fields where status=:n');
    $stmt->execute(array(
        ':n' => '1'
    ));
    $res1 = $stmt->fetchAll();
    
    if (!empty($res1)) {
        $ufields = true;
        foreach ($res1 as $row) {
            
            $v = get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);
            $vs = get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);
            
            if ($row['t_type'] == "text") {
                $v = get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);
            }
            if ($row['t_type'] == "textarea") {
                $v = get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);
            }
            if ($row['t_type'] == "select") {
                $mf_arr = array();
                $v = explode(",", $row['value']);
                $vs = explode(",", $vs);
                foreach ($v as $value) {
                    // code...
                    $sc = "";
                    if (in_array($value, $vs)) {
                        $sc = "selected";
                    }
                    array_push($mf_arr, array(
                        'sc' => $sc,
                        'value' => $value
                    ));
                }
                
                $v = $mf_arr;
            }
            if ($row['t_type'] == "multiselect") {
                
                $mmf_arr = array();
                $v = explode(",", $row['value']);
                $vs = explode(",", $vs);
                
                //print_r($vs)."<br>";
                foreach ($v as $value) {
                    // code...
                    //echo $value."<br>";
                    $sc = "";
                    if (in_array($value, $vs)) {
                        $sc = "selected";
                    }
                    
                    array_push($mmf_arr, array(
                        'sc' => $sc,
                        'value' => $value
                    ));
                }
                
                $v = $mmf_arr;
            }
            
            array_push($fields_arr, array(
                'hash' => $row['hash'],
                'name' => $row['name'],
                't_type' => $row['t_type'],
                'v' => $v,
                'placeholder' => $row['placeholder']
            ));
        }
    }
    
    ///////////////////////////////
    $mailnf_arr = array();
    $stmt2 = $dbConnection->prepare('SELECT mail from users_notify where user_id=:uto');
    $stmt2->execute(array(
        ':uto' => $_SESSION['helpdesk_user_id']
    ));
    $tt2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    
    $nl = get_notify_opt_list();
    
    foreach ($nl as $key => $value) {
        // code...
        
        $sc = "";
        
        if ($tt2['mail']) {
            
            $al = explode(",", $tt2['mail']);
            
            if (in_array($key, $al)) {
                $sc = "selected";
            }
        } 
        else if (!$tt2['mail']) {
            
            $sc = "selected";
        }
        array_push($mailnf_arr, array(
            
            'key' => $key,
            'sc' => $sc,
            'value' => $value
        ));
    }
    
    ///////////////////////////////
    
    ////////////////////////////////////
    $smsc_arr = array();
    $stmt2 = $dbConnection->prepare('SELECT sms from users_notify where user_id=:uto');
    $stmt2->execute(array(
        ':uto' => $_SESSION['helpdesk_user_id']
    ));
    $tt2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    
    $nl = get_notify_opt_list();
    
    $nla = explode(",", get_conf_param('smsc_list_action'));
    
    //$nl = array_intersect_key ($nl, $nla);
    
    foreach ($nl as $key => $value) {
        // code...
        
        $sc = "";
        
        if ($tt2['sms']) {
            
            $al = explode(",", $tt2['sms']);
            
            if (in_array($key, $al)) {
                $sc = "selected";
            }
        } 
        else if (!$tt2['sms']) {
            
            $sc = "";
        }
        
        if (in_array($key, $nla)) {
            
            array_push($smsc_arr, array(
                
                'key' => $key,
                'sc' => $sc,
                'value' => $value
            ));
        }
    }
    
    ////////////////////////////////////
    
    $check_ldap_user = false;
    $ul = get_userlogin_byid($_SESSION['helpdesk_user_id']);
    if (get_user_authtype($login) == false) {
        $check_ldap_user = true;
    }
    
    $api_status = false;
    if (get_conf_param('api_status') == "true") {
        
        $api_status = true;
    }







$tfiles_arr = array();
        $tfiles = false;
        $stmt = $dbConnection->prepare('SELECT * FROM user_files where user_id=:tid');
        $stmt->execute(array(
            ':tid' => $_SESSION['helpdesk_user_id']
        ));
        $res1 = $stmt->fetchAll();
        if (!empty($res1)) {
            $tfiles = true;
            foreach ($res1 as $r) {
                
                $fts = array(
                    'image/jpeg',
                    'image/gif',
                    'image/png'
                );
                
                if (in_array($r['file_type'], $fts)) {
                    
                    $ct = ' <a class=\'fancybox\' href=\'' . $CONF['hostname'] . 'upload_files/' . $r['file_hash'] . '.' . $r['file_ext'] . '\'><img style=\'max-height:50px;\' src=\'' . $CONF['hostname'] . 'upload_files/' . $r['file_hash'] . '.' . $r['file_ext'] . '\'></a> ';
                    $ic = '';
                } 
                else {
                    $ct = ' <a href=\'' . $CONF['hostname'] . 'action?mode=download_user_file&file=' . $r['file_hash'] . '\'>' . $r['original_name'] . '</a>';
                    $ic = get_file_icon($r['file_hash']);
                }
                
                array_push($tfiles_arr, array(
                    
                    'ic' => $ic,
                    'ct' => $ct,
                    'size' => round(($r['file_size'] / (1024 * 1024)) , 2)
                ));
            }
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
        $template = $twig->loadTemplate('profile.view.tmpl');
        
        // передаём в шаблон переменные и значения
        // выводим сформированное содержание
        $main_arr = array(
            'hostname' => $CONF['hostname'],
            'name_of_firm' => $CONF['name_of_firm'],
            'NAVBAR_profile' => lang('NAVBAR_profile') ,
            'NAVBAR_profile_ext' => lang('NAVBAR_profile_ext') ,
            'get_last_ticket_new' => get_last_ticket_new($_SESSION['helpdesk_user_id']) ,
            'fio' => $fio,
            'get_user_val_posada' => $get_user_val_posada,
            'get_user_img' => get_user_img() ,
            'PROFILE_select_image' => lang('PROFILE_select_image') ,
            'PROFILE_del_image' => lang('PROFILE_del_image') ,
            'PROFILE_priv' => lang('PROFILE_priv') ,
            'priv_status_name' => priv_status_name($usid) ,
            'PROFILE_priv_unit' => lang('PROFILE_priv_unit') ,
            'units_u' => view_array(get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']))) ,
            'P_main' => lang('P_main') ,
            'WORKER_fio' => lang('WORKER_fio') ,
            'P_mail' => lang('P_mail') ,
            'email' => $email,
            'P_mail_desc' => lang('P_mail_desc') ,
            'push' => $push,
            'WORKER_tel_full' => lang('WORKER_tel_full') ,
            'tel' => $tel,
            'skype' => $skype,
            'APPROVE_adr' => lang('APPROVE_adr') ,
            'adr' => $adr,
            'WORKER_posada' => lang('WORKER_posada') ,
            'pos_arr' => $pos_arr,
            'WORKER_unit' => lang('WORKER_unit') ,
            'unit_arr' => $unit_arr,
            'SYSTEM_lang' => lang('SYSTEM_lang') ,
            'status_lang_en' => $status_lang_en,
            'status_lang_ru' => $status_lang_ru,
            'status_lang_ua' => $status_lang_ua,
            'NOTY_layot' => lang('NOTY_layot') ,
            'check_user_noty_layot_top' => check_user_noty_layot('top') ,
            'check_user_noty_layot_topLeft' => check_user_noty_layot('topLeft') ,
            'check_user_noty_layot_topCenter' => check_user_noty_layot('topCenter') ,
            'check_user_noty_layot_topRight' => check_user_noty_layot('topRight') ,
            'check_user_noty_layot_centerLeft' => check_user_noty_layot('centerLeft') ,
            'check_user_noty_layot_center' => check_user_noty_layot('center') ,
            'check_user_noty_layot_centerRight' => check_user_noty_layot('centerRight') ,
            'check_user_noty_layot_bottomLeft' => check_user_noty_layot('bottomLeft') ,
            'check_user_noty_layot_bottomCenter' => check_user_noty_layot('bottomCenter') ,
            'check_user_noty_layot_bottomRight' => check_user_noty_layot('bottomRight') ,
            'check_user_noty_layot_bottom' => check_user_noty_layot('bottom') ,
            'P_edit' => lang('P_edit') ,
            'usid' => $usid,
            'ufields' => $ufields,
            'fields_arr' => $fields_arr,
            
            'FIELD_add_title' => lang('FIELD_add_title') ,
            'PROFILE_perf_notify' => lang('PROFILE_perf_notify') ,
            'CONF_mail_status' => lang('CONF_mail_status') ,
            'mailnf_arr' => $mailnf_arr,
            'EXT_sms_noti' => lang('EXT_sms_noti') ,
            'smsc_arr' => $smsc_arr,
            'EXT_SMS_noti_mob' => lang('EXT_SMS_noti_mob') ,
            'mob' => $mob,
            'check_ldap_user' => $check_ldap_user,
            'P_passedit' => lang('P_passedit') ,
            'P_pass_old' => lang('P_pass_old') ,
            'P_pass_old2' => lang('P_pass_old2') ,
            'P_pass_new' => lang('P_pass_new') ,
            'P_pass_new2' => lang('P_pass_new2') ,
            'P_pass_new_re' => lang('P_pass_new_re') ,
            'P_pass_new_re2' => lang('P_pass_new_re2') ,
            'P_do_edit_pass' => lang('P_do_edit_pass') ,
            'api_status' => $api_status,
            'api_key' => get_user_val_by_id($_SESSION['helpdesk_user_id'], 'api_key'),
            'tfiles_arr'=>$tfiles_arr,
            'tfiles'=>$tfiles,
            'TICKET_file_list'=>lang('TICKET_file_list')
        );
        
        $main_arr = array_merge($main_arr);
        
        echo $template->render($main_arr);
    }
    catch(Exception $e) {
        die('ERROR: ' . $e->getMessage());
    }
    
    include ("footer.inc.php");
} 
else {
    include 'auth.php';
}
?>
