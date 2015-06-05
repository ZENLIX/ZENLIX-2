<?php
session_start();
include_once ("../functions.inc.php");
include_once ("library/SimpleImage/SimpleImage.php");
if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    //if (validate_admin($_SESSION['helpdesk_user_id'])) {
    include ("head.inc.php");
    include ("client.navbar.inc.php");
    
    if ($_FILES["file"]) {
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
                
                $image = new abeautifulsite\SimpleImage($nf);
                $image->adaptive_resize(250, 250)->save($nf);
                
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
    
    $stmt = $dbConnection->prepare('SELECT pb,fio, pass, login, status, priv, unit,email, lang, tel, skype, adr from users where id=:usid');
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
        $langu = $row['lang'];
        $push = $row['pb'];
        
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
    
    $ad_fields = false;
    $ad_fields_arr = array();
    $stmt = $dbConnection->prepare('SELECT * FROM user_fields where status=:n and for_client=1');
    $stmt->execute(array(
        ':n' => '1'
    ));
    $res1 = $stmt->fetchAll();
    
    if (!empty($res1)) {
        $ad_fields = true;
        
        foreach ($res1 as $row) {
            
            if ($row['t_type'] == "date") {
                $vr = get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);
            }

            if ($row['t_type'] == "text") {
                $vr = get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);
            }
            
            if ($row['t_type'] == "textarea") {
                $vr = get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);
            }
            
            if ($row['t_type'] == "select") {
                $vs = get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);
                $vr = array();
                $v = explode(",", $row['value']);
                $vs = explode(",", $vs);
                foreach ($v as $value) {
                    // code...
                    $sc = "";
                    if (in_array($value, $vs)) {
                        $sc = "selected";
                    }
                    
                    array_push($vr, array(
                        
                        'value' => $value,
                        'sc' => $sc
                    ));
                }
            }
            
            if ($row['t_type'] == "multiselect") {
                $vs = get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);
                $vr = array();
                $v = explode(",", $row['value']);
                $vs = explode(",", $vs);
                foreach ($v as $value) {
                    // code...
                    $sc = "";
                    if (in_array($value, $vs)) {
                        $sc = "selected";
                    }
                    array_push($vr, array(
                        
                        'value' => $value,
                        'sc' => $sc
                    ));
                }
            }
            
            array_push($ad_fields_arr, array(
                
                't_type' => $row['t_type'],
                'hash' => $row['hash'],
                'name' => $row['name'],
                'placeholder' => $row['placeholder'],
                'vr' => $vr
            ));
        }
    }
    
    $mail_arr = array();
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
        
        array_push($mail_arr, array(
            
            'key' => $key,
            'value' => $value,
            'sc' => $sc
        ));
    }
    
    $canChangePw = false;
    $ul = get_userlogin_byid($_SESSION['helpdesk_user_id']);
    if (get_user_authtype($login) == false) {
        $canChangePw = true;
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
        $template = $twig->loadTemplate('client.profile.view.tmpl');
        
        // передаём в шаблон переменные и значения
        // выводим сформированное содержание
        echo $template->render(array(
            'hostname' => $CONF['hostname'],
            'name_of_firm' => $CONF['name_of_firm'],
            'NAVBAR_profile' => lang('NAVBAR_profile') ,
            'NAVBAR_profile_ext' => lang('NAVBAR_profile_ext') ,
            'get_last_ticket_new' => get_last_ticket_new($_SESSION['helpdesk_user_id']) ,
            'fio' => $fio,
            'posada' => get_user_val('posada') ,
            'get_user_img' => get_user_img() ,
            'PROFILE_select_image' => lang('PROFILE_select_image') ,
            'PROFILE_del_image' => lang('PROFILE_del_image') ,
            'P_main' => lang('P_main') ,
            'WORKER_fio' => lang('WORKER_fio') ,
            'P_mail' => lang('P_mail') ,
            'P_mail_desc' => lang('P_mail_desc') ,
            'email' => $email,
            'push' => $push,
            'WORKER_tel_full' => lang('WORKER_tel_full') ,
            'tel' => $tel,
            'skype' => $skype,
            'APPROVE_adr' => lang('APPROVE_adr') ,
            'adr' => $adr,
            'SYSTEM_lang' => lang('SYSTEM_lang') ,
            'status_lang_en' => $status_lang_en,
            'status_lang_ru' => $status_lang_ru,
            'status_lang_ua' => $status_lang_ua,
            'usid' => $usid,
            'P_edit' => lang('P_edit') ,
            'ad_fields' => $ad_fields,
            'FIELD_add_title' => lang('FIELD_add_title') ,
            'ad_fields_arr' => $ad_fields_arr,
            'PROFILE_perf_notify' => lang('PROFILE_perf_notify') ,
            'CONF_mail_status' => lang('CONF_mail_status') ,
            'mail_arr' => $mail_arr,
            'canChangePw' => $canChangePw,
            'P_passedit' => lang('P_passedit') ,
            'P_pass_old' => lang('P_pass_old') ,
            'P_pass_old2' => lang('P_pass_old2') ,
            'P_pass_new' => lang('P_pass_new') ,
            'P_pass_new2' => lang('P_pass_new2') ,
            'P_pass_new_re' => lang('P_pass_new_re') ,
            'P_pass_new_re2' => lang('P_pass_new_re2') ,
            'P_do_edit_pass' => lang('P_do_edit_pass')
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
