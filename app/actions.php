<?php
define("ZENLIX_DIR", dirname(dirname(__FILE__)));


if (isset($_GET['mode'])) {
    $mode = ($_GET['mode']);



if ($mode == "download_file") {
$hn=$_GET['file'];
//echo $hn;


    $stmt = $dbConnection->prepare('SELECT original_name,file_type,file_ext, file_size from files where file_hash=:file_hash LIMIT 1');
    $stmt->execute(array(':file_hash' => $hn));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $original_name=$row['original_name'];
    $file_type=$row['file_type'];
    $file_ext=$row['file_ext'];
    $file_size=$row['file_size'];
    //echo($original_name." ".$file_type);
    
    
    
    //echo $original_name;
    if (file_exists(ZENLIX_DIR."/upload_files/".$hn.".".$file_ext)) {
      header("Content-Type: ".$file_type);
      header("Content-Disposition:  attachment; filename=\"" . $original_name . "\";" );
      header("Content-Transfer-Encoding:  binary");

      header('Content-Length: ' . $file_size);
      ob_clean();
      flush();
      readfile(ZENLIX_DIR."/upload_files/".$hn.".".$file_ext);
      exit;
          }
}


if ($mode == "download_user_file") {
$hn=$_GET['file'];
//echo $hn;


    $stmt = $dbConnection->prepare('SELECT original_name,file_type,file_ext, file_size from user_files where file_hash=:file_hash LIMIT 1');
    $stmt->execute(array(':file_hash' => $hn));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $original_name=$row['original_name'];
    $file_type=$row['file_type'];
    $file_ext=$row['file_ext'];
    $file_size=$row['file_size'];
    //echo($original_name." ".$file_type);
    
    
    
    //echo $original_name;
    if (file_exists(ZENLIX_DIR."/upload_files/".$hn.".".$file_ext)) {
      header("Content-Type: ".$file_type);
      header("Content-Disposition:  attachment; filename=\"" . $original_name . "\";" );
      header("Content-Transfer-Encoding:  binary");

      header('Content-Length: ' . $file_size);
      ob_clean();
      flush();
      readfile(ZENLIX_DIR."/upload_files/".$hn.".".$file_ext);
      exit;
          }
}


if ($mode == "getJSON_posada") {
    $term = trim(strip_tags(($_GET['term'])));
    
    $stmt = $dbConnection->prepare('SELECT id, name FROM posada');
    $stmt->execute();
    $res1 = $stmt->fetchAll();
    foreach ($res1 as $row) {
        
        $data[] = array(
            'value' => (int)$row['id'],
            'text' => $row['name']
        );
    }
    
    echo json_encode($data);
    }


if ($mode == "getJSON_units") {
        $term = trim(strip_tags(($_GET['term'])));
    
    $stmt = $dbConnection->prepare('SELECT id, name FROM units');
    $stmt->execute();
    $res1 = $stmt->fetchAll();
    foreach ($res1 as $row) {
        
        $data[] = array(
            'value' => (int)$row['id'],
            'text' => $row['name']
        );
    }
    
    echo json_encode($data);
}    

if ($mode == "getJSON_fio")
{
    $term = trim(strip_tags(($_GET['term'])));
    
    $stmt = $dbConnection->prepare('SELECT fio as label, login as label2, tel as label3, unit_desc as label4, id as value FROM users WHERE ((fio LIKE :term) or (login LIKE :term2) or (tel LIKE :term3)) and id!=1 and status!=2 limit 10');
    $stmt->execute(array(
        ':term' => '%' . $term . '%',
        ':term2' => '%' . $term . '%',
        ':term3' => '%' . $term . '%'
    ));
    $res1 = $stmt->fetchAll();
    foreach ($res1 as $row) {
        
        $row['label'] = $row['label'];
        $row['value'] = (int)$row['value'];
        $row['label2'] = $row['label2'];
        $row['label3'] = $row['label3'];
        $row['label4'] = $row['label4'];
        $row_set[] = $row;
    }
    
    echo json_encode($row_set);
}


}


if (isset($_POST['mode'])) {
    
    $mode = ($_POST['mode']);
    
    if ($mode == "get_host_conf") {
        
        print ($CONF['hostname']);
    }
    
    if ($mode == "get_lang_param") {
        $p = ($_POST['param']);
        $r = lang($p);
        print ($r);
    }
    
    //forgot_pass_change
    if ($mode == "forgot_pass_change") {
        $msg=NULL;
        $ct = false;
        $uniq_code = $_POST['uc'];
        $pass_md = $_POST['ph'];
        
        $stmt = $dbConnection->prepare('select id, pass from users where uniq_id=:uniq_id limit 1');
        $stmt->execute(array(
            ':uniq_id' => $uniq_code
        ));
        $r = $stmt->fetchAll();
        
        if (!empty($r)) {
            foreach ($r as $v) {
                
                //echo md5($v['pass'])." == ".$pass_md;
                if (md5($v['pass']) == $pass_md) {
                    $ct = true;
                }
            }
        }
        
        if ($ct == true) {
            
            $ec = 0;
            
            $validator = new GUMP();
            $_POST = $validator->sanitize($_POST);
            
            $rules = array(
                'p1' => 'required|max_len,100|min_len,6',
                'p2' => 'required|max_len,100|min_len,6'
            );
            $filters = array(
                'p1' => 'sanitize_string|trim',
                'p2' => 'sanitize_string|trim',
            );
            
            GUMP::set_field_name("p1", lang('P_pass_new'));
            GUMP::set_field_name("p2", lang('P_pass_new_re'));
            
            $_POST = $validator->filter($_POST, $filters);
            
            $validated = $validator->validate($_POST, $rules);
            
            if ($validated === true) {
                
                $p_new = md5(($_POST['p1']));
                $p_new2 = md5(($_POST['p2']));
                
                if ($p_new <> $p_new2) {
                    $ec = 1;
                    $text.= lang('PROFILE_msg_pass_err2');
                }
                
                if (strlen($p_new) < 3) {
                    $ec = 1;
                    $text.= lang('PROFILE_msg_pass_err3');
                }
                
                if ($ec == 0) {
                    
                    $check_error = "true";
                    $msg = " <div class=\"body bg-blues\"> <div class=\"alert alert-success\">";
                    $msg.= lang('PROFILE_msg_pass_ok');
                    $msg.= "</div></div>";
                    
                    $stmt = $dbConnection->prepare('update users set pass=:p_new where uniq_id=:id');
                    $stmt->execute(array(
                        ':id' => $uniq_code,
                        ':p_new' => $p_new
                    ));
                    
                    $loginname = get_user_val_by_hash($uniq_code, 'login');
                    $mail = get_user_val_by_hash($uniq_code, 'email');
                    
                    $subject = $CONF['name_of_firm'] . " - password changed successfull";
                    

                    


try {
            //$base = dirname(__FILE__);
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem(ZENLIX_DIR.'/app/mail_tmpl');
            
            // инициализируем Twig
            if (get_conf_param('twig_cache') == "true") {
                $twig = new Twig_Environment($loader, array(
                    'cache' => ZENLIX_DIR . '/app/cache',
                ));
            } 
            else {
                $twig = new Twig_Environment($loader);
            }
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('forgot_mail_success.tpl');

$message=$template->render(array(
'real_hostname'=>$CONF['real_hostname'],
'name_of_firm'=>get_conf_param('name_of_firm'),


'MAIL_forgot_success'=>lang('MAIL_forgot'),
'MAIL_forgot_success_ext'=>lang('MAIL_forgot_ext'),
'MAIL_info'=>lang('MAIL_REG_title_data'),
'MAIL_login'=>lang('PORTAL_login_name'),
'login'=>$loginname,
'MAIL_pass'=>lang('CONF_mail_pass'),
'pass'=>$_POST['p2'],
'link'=>$link4mail,
));

        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }





                    
                    send_mail_reg($mail, $subject, $message);
                }
                if ($ec == 1) {
                    $check_error = "false";
                    
                    $msg = " <div class=\"alert alert-danger\"><strong>";
                    
                    $msg.= lang('PROFILE_msg_te') . "!</strong><br>";
                    $msg.= $text;
                    $msg.= "</div>";
                }
            } 
            else {
                $check_error = "false";
                $msg = "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($validator->get_readable_errors(false) as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
                
                //echo $msg;
                
                
            }
        }
        
        //true - норм
        //false - ошибка
        
        $results[] = array(
            'check_error' => $check_error,
            'msg' => "<br>" . $msg
        );
        print json_encode($results);
    }
    
    if ($mode == "forgot_pass") {
        $msg=NULL;
        $login = $_POST['login'];
        $mail = $_POST['mail'];
        $hn = md5(time());
        $check_error = "true";
        
        if (!empty($login) && !empty($mail)) {
            
            $stmt = $dbConnection->prepare('select id, uniq_id,pass from users where email=:mail and login=:login and ldap_key=0 limit 1');
            $stmt->execute(array(
                ':mail' => $mail,
                ':login' => $login
            ));
            $r = $stmt->fetchAll();
            
            if (empty($r)) {
                $check_error = "false";
                $msge = lang('CREATE_ACC_error');
            } 
            else if (!empty($r)) {
                $check_error = "true";
                $msge = lang('FORGOT_instr');
                
                foreach ($r as $k) {
                    
                    // code...
                    $uc = $k['uniq_id'];
                    $ph = md5($k['pass']);
                }
                
                //$pass = generatepassword();
                
                $link4mail = $CONF['real_hostname'] . 'forgot?uc=' . $uc . '&ph=' . $ph . '&m=true';
                
                $subject = $CONF['name_of_firm'] . " - password recovery";
                







 try {
            //$base = dirname(__FILE__);
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem(ZENLIX_DIR.'/app/mail_tmpl');
            
            // инициализируем Twig
            if (get_conf_param('twig_cache') == "true") {
                $twig = new Twig_Environment($loader, array(
                    'cache' => ZENLIX_DIR . '/app/cache',
                ));
            } 
            else {
                $twig = new Twig_Environment($loader);
            }
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('forgot_mail.tpl');

$message=$template->render(array(
'real_hostname'=>$CONF['real_hostname'],
'name_of_firm'=>get_conf_param('name_of_firm'),


'MAIL_forgot'=>lang('MAIL_forgot'),
'MAIL_forgot_ext'=>lang('MAIL_forgot_ext'),
'MAIL_info'=>lang('MAIL_REG_title_data'),
'MAIL_forgot_link'=>lang('MAIL_forgot_link'),
'link'=>$link4mail,
));

        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }





                
                send_mail_reg($mail, $subject, $message);
                
                //$msge.=$message;
                
                //отправить ссылку на смену пароля
                //открываешь страницу где указываешь новые логин/пароль
                
                //forgot?{uniq_code}&q={md5(pass)}&m=true
                
                //если есть такая строка то предоставить форму смены пароля
                
                /*
                
                $stmts = $dbConnection->prepare('update users set pass=:pass where email=:mail and login=:login');
                $stmts->execute(array(
                ':mail' => $mail,
                ':login'=> $login,
                ':pass'=>md5($pass)
                ));
                */
            }
        } 
        else if (empty($login) || empty($mail)) {
            $check_error = "true";
            $msge = "empty login & mail ";
        }
        
        $msg = "<div class=\"col-md-12\">";
        $msg.= "<div class=\"alert alert-warning alert-dismissable\"> <h4> ";
        $msg.= " </h4>";
        $msg.= $msge;
        $msg.= "</div>";
        $msg.= "</div>";
        
        $results[] = array(
            'check_error' => $check_error,
            'msg' => "<br>" . $msg
        );
        print json_encode($results);
    }
    
    if ($mode == "register_new") {
        $msg=NULL;
        $fio = $_POST['fio'];
        $login = $_POST['login'];
        $mail = $_POST['mail'];
        $hn = md5(time());
        $errors = false;
        
        /*
        - Проверка есть ли такой логин уже в системе? - дубликат?
        - Проверка правильный ли вообще email.
        - Проверка есть ли такой email - проверка дубликатов?
        */


            
        if (validate_exist_login($login) == false) {
            $errors = true;
            $el = lang('ticket_login_error') . "<br>";
        }
        
        if (!validate_email($mail)) {
            $errors = true;
            $el.= lang('PROFILE_msg_error') . "<br>";
        }

        if (validate_exist_mail_not_auth($mail) == false) {
            $errors = true;
            $el.= lang('PROFILE_msg_error') . "(already exist)<br>";
        }
        /* */


        if ($errors == true) {
            $check_error = "false";
            $msg = "<div class=\"body bg-blues\">";
            $msg.= "<div class=\"alert alert-danger\">
                                        <i class=\"fa fa-ban\"></i>
                                        ";
            $msg.= $el;
            $msg.= "</div>";
            $msg.= "</div>";
        } 
        else if ($errors == false) {
            $check_error = "true";
            
            $msg = "<div class=\"col-md-12\">";
            $msg.= "<div class=\"alert alert-success alert-dismissable\"> <h4>    <i class=\"icon fa fa-check\"></i> " . lang('REG_msg') . "</h4><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>";
            
            //$msg.= lang('REG_msg');
            $msg.= "</div>";
            $msg.= "</div>";
        }
        
        $results[] = array(
            'check_error' => $check_error,
            'msg' => "<br>" . $msg
        );
        print json_encode($results);
        
        //
        
        if ($errors == true) {
        } 
        else if ($errors == false) {
            
            $pass = generatepassword();
            
            $stmt = $dbConnection->prepare('insert into users 
             (fio, 
             login, 
             email, 
             priv,
             is_client,
             uniq_id,
             status,
             pass,
             api_key) 
             VALUES         
             (
             :client_fio, 
             :client_login,   
             :client_mail, 
             :priv,
             :is_client,
             :uniq_id,
             :status,
             :pass,
             :api_key)');
            
            $stmt->execute(array(
                ':client_fio' => $fio,
                ':client_login' => $login,
                ':client_mail' => $mail,
                ':priv' => '1',
                ':is_client' => '1',
                ':uniq_id' => $hn,
                ':status' => '1',
                ':pass' => md5($pass) ,
                ':api_key' => md5($pass . "zen")
            ));
            
 


try {
             //$base = dirname(__FILE__);
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem(ZENLIX_DIR.'/app/mail_tmpl');
            
            // инициализируем Twig
            if (get_conf_param('twig_cache') == "true") {
                $twig = new Twig_Environment($loader, array(
                    'cache' => ZENLIX_DIR . '/app/cache',
                ));
            } 
            else {
                $twig = new Twig_Environment($loader);
            }
            
            // подгружаем шаблон
$template = $twig->loadTemplate('register_mail.tpl');
$subject = $CONF['name_of_firm'] . " - registration successfull";
$message=$template->render(array(
'real_hostname'=>$CONF['real_hostname'],
'name_of_firm'=>get_conf_param('name_of_firm'),
'MAIL_new_reg'=>lang('MAIL_REG_title'),
'MAIL_new_reg_ext'=>lang('MAIL_REG_title_ext'),
'MAIL_info'=>lang('MAIL_REG_title_data'),
'MAIL_new_reg_login'=>lang('CONF_mail_login'),
'login'=>$login,
'MAIL_new_reg_pass'=>lang('CONF_mail_pass'),
'pass'=>$pass
   ));

        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }






            send_mail_reg($mail, $subject, $message);
        }
    }
    
    if ((validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) || (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code']))) {
        



            if ($mode == "summernote_file_add") {
if ($_FILES['file']['name']) {
            if (!$_FILES['file']['error']) {
                $name = md5(time());
                $ext = explode('.', $_FILES['file']['name']);
                $filename = $name . '.' . $ext[1];
                $destination = ZENLIX_DIR."/upload_files/user_content/" . $filename; //change this directory
                $location = $_FILES["file"]["tmp_name"];
                move_uploaded_file($location, $destination);
                echo 'upload_files/user_content/' . $filename;//change this URL
            }
            else
            {
              echo  $message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['file']['error'];
            }
        }
            }






        
        if (validate_admin($_SESSION['helpdesk_user_id'])) {
            if ($mode == "sort_sla") {
                $list = $_POST['list'];
                
                echo $list;
                 
                $orderlist = explode('&', $list);
                
                $n = 0;
                foreach ($orderlist as $order) {
                    
                    $a = explode("=", $order);
                    
                    //echo $a[0];
                    
                    $b = explode("[", $a['0']);
                    
                    $с = substr($b[1], 0, -1);
                    
                    //?
                    $rest = substr($b[1], 0, -1);
                    
                    //echo $a[1];
                    //echo "ID:".$rest."  Parent:".$a[1]."  Pos:".$n."                              ////";
                    if ($a[1] == "null") {
                        $a[1] = get_max_helper_parent();
                    }
                    echo "parent_id=" . $a[1] . " where id=" . $rest . ";\r\n";
                    
                    $stmt = $dbConnection->prepare('UPDATE subj set sort_id=:s_id,parent_id=:p_id where id=:el_id');
                    $stmt->execute(array(
                        ':s_id' => $n,
                        ':p_id' => $a[1],
                        ':el_id' => $rest
                    ));
                    
                    $n++;
                }
            }
            if ($mode == "sort_sla_plans") {
                $list = $_POST['list'];
                
                echo $list;
                
                $orderlist = explode('&', $list);
                
                $n = 0;
                foreach ($orderlist as $order) {
                    
                    $a = explode("=", $order);
                    
                    //echo $a[0];
                    
                    $b = explode("[", $a['0']);
                    
                    $с = substr($b[1], 0, -1);
                    
                    //?
                    $rest = substr($b[1], 0, -1);
                    
                    //echo $a[1];
                    //echo "ID:".$rest."  Parent:".$a[1]."  Pos:".$n."                              ////";
                    if ($a[1] == "null") {
                        $a[1] = get_max_helper_parent();
                    }
                    echo "parent_id=" . $a[1] . " where id=" . $rest . ";\r\n";
                    
                    $stmt = $dbConnection->prepare('UPDATE sla_plans set sort_id=:s_id,parent_id=:p_id where id=:el_id');
                    $stmt->execute(array(
                        ':s_id' => $n,
                        ':p_id' => $a[1],
                        ':el_id' => $rest
                    ));
                    
                    $n++;
                }
            }
            
            if ($mode == "save_subj_item") {
                
                $stmt = $dbConnection->prepare('UPDATE subj set name=:t where id=:el_id');
                $stmt->execute(array(
                    ':t' => $_POST['value'],
                    ':el_id' => $_POST['pk']
                ));
            }
            
            if ($mode == "save_sla") {
                $msg=NULL;
                if (!$_POST['react_low_1']) {
                    $_POST['react_low_1'] = 0;
                }
                if (!$_POST['react_low_2']) {
                    $_POST['react_low_2'] = 0;
                }
                if (!$_POST['react_low_3']) {
                    $_POST['react_low_3'] = 0;
                }
                if (!$_POST['react_low_4']) {
                    $_POST['react_low_4'] = 0;
                }
                
                $react_low_sec = (($_POST['react_low_1'] * 24 + $_POST['react_low_2']) * 60 + $_POST['react_low_3']) * 60;
                $react_low_sec = $react_low_sec + $_POST['react_low_4'];
                
                if (!$_POST['react_def_1']) {
                    $_POST['react_def_1'] = 0;
                }
                if (!$_POST['react_def_2']) {
                    $_POST['react_def_2'] = 0;
                }
                if (!$_POST['react_def_3']) {
                    $_POST['react_def_3'] = 0;
                }
                if (!$_POST['react_def_4']) {
                    $_POST['react_def_4'] = 0;
                }
                $react_def_sec = (($_POST['react_def_1'] * 24 + $_POST['react_def_2']) * 60 + $_POST['react_def_3']) * 60;
                $react_def_sec = $react_def_sec + $_POST['react_def_4'];
                
                if (!$_POST['react_high_1']) {
                    $_POST['react_high_1'] = 0;
                }
                if (!$_POST['react_high_2']) {
                    $_POST['react_high_2'] = 0;
                }
                if (!$_POST['react_high_3']) {
                    $_POST['react_high_3'] = 0;
                }
                if (!$_POST['react_high_4']) {
                    $_POST['react_high_4'] = 0;
                }
                $react_high_sec = (($_POST['react_high_1'] * 24 + $_POST['react_high_2']) * 60 + $_POST['react_high_3']) * 60;
                $react_high_sec = $react_high_sec + $_POST['react_high_4'];
                
                //$second += (($_POST['react_low_1'] * 24 + $_POST['react_low_2']) * 60 + $_POST['react_low_3']) * 60;
                
                if (!$_POST['work_low_1']) {
                    $_POST['work_low_1'] = 0;
                }
                if (!$_POST['work_low_2']) {
                    $_POST['work_low_2'] = 0;
                }
                if (!$_POST['work_low_3']) {
                    $_POST['work_low_3'] = 0;
                }
                if (!$_POST['work_low_4']) {
                    $_POST['work_low_4'] = 0;
                }
                
                $work_low_sec = (($_POST['work_low_1'] * 24 + $_POST['work_low_2']) * 60 + $_POST['work_low_3']) * 60;
                $work_low_sec = $work_low_sec + $_POST['work_low_4'];
                
                if (!$_POST['work_def_1']) {
                    $_POST['work_def_1'] = 0;
                }
                if (!$_POST['work_def_2']) {
                    $_POST['work_def_2'] = 0;
                }
                if (!$_POST['work_def_3']) {
                    $_POST['work_def_3'] = 0;
                }
                if (!$_POST['work_def_4']) {
                    $_POST['work_def_4'] = 0;
                }
                $work_def_sec = (($_POST['work_def_1'] * 24 + $_POST['work_def_2']) * 60 + $_POST['work_def_3']) * 60;
                $work_def_sec = $work_def_sec + $_POST['work_def_4'];
                
                if (!$_POST['work_high_1']) {
                    $_POST['work_high_1'] = 0;
                }
                if (!$_POST['work_high_2']) {
                    $_POST['work_high_2'] = 0;
                }
                if (!$_POST['work_high_3']) {
                    $_POST['work_high_3'] = 0;
                }
                if (!$_POST['work_high_4']) {
                    $_POST['work_high_4'] = 0;
                }
                $work_high_sec = (($_POST['work_high_1'] * 24 + $_POST['work_high_2']) * 60 + $_POST['work_high_3']) * 60;
                $work_high_sec = $work_high_sec + $_POST['work_high_4'];
                
                if (!$_POST['deadline_low_1']) {
                    $_POST['deadline_low_1'] = 0;
                }
                if (!$_POST['deadline_low_2']) {
                    $_POST['deadline_low_2'] = 0;
                }
                if (!$_POST['deadline_low_3']) {
                    $_POST['deadline_low_3'] = 0;
                }
                if (!$_POST['deadline_low_4']) {
                    $_POST['deadline_low_4'] = 0;
                }
                
                $deadline_low_sec = (($_POST['deadline_low_1'] * 24 + $_POST['deadline_low_2']) * 60 + $_POST['deadline_low_3']) * 60;
                $deadline_low_sec = $deadline_low_sec + $_POST['deadline_low_4'];
                
                if (!$_POST['deadline_def_1']) {
                    $_POST['deadline_def_1'] = 0;
                }
                if (!$_POST['deadline_def_2']) {
                    $_POST['deadline_def_2'] = 0;
                }
                if (!$_POST['deadline_def_3']) {
                    $_POST['deadline_def_3'] = 0;
                }
                if (!$_POST['deadline_def_4']) {
                    $_POST['deadline_def_4'] = 0;
                }
                $deadline_def_sec = (($_POST['deadline_def_1'] * 24 + $_POST['deadline_def_2']) * 60 + $_POST['deadline_def_3']) * 60;
                $deadline_def_sec = $deadline_def_sec + $_POST['deadline_def_4'];
                
                if (!$_POST['deadline_high_1']) {
                    $_POST['deadline_high_1'] = 0;
                }
                if (!$_POST['deadline_high_2']) {
                    $_POST['deadline_high_2'] = 0;
                }
                if (!$_POST['deadline_high_3']) {
                    $_POST['deadline_high_3'] = 0;
                }
                if (!$_POST['deadline_high_4']) {
                    $_POST['deadline_high_4'] = 0;
                }
                $deadline_high_sec = (($_POST['deadline_high_1'] * 24 + $_POST['deadline_high_2']) * 60 + $_POST['deadline_high_3']) * 60;
                $deadline_high_sec = $deadline_high_sec + $_POST['deadline_high_4'];
                
                $stmt = $dbConnection->prepare('UPDATE sla_plans set 
reaction_time_def=:reaction_time_def,
reaction_time_low_prio=:reaction_time_low_prio,
reaction_time_high_prio=:reaction_time_high_prio,
work_time_def=:work_time_def,
work_time_low_prio=:work_time_low_prio,
work_time_high_prio=:work_time_high_prio,
deadline_time_def=:deadline_time_def,
deadline_time_low_prio=:deadline_time_low_prio,
deadline_time_high_prio=:deadline_time_high_prio
  where uniq_id=:el_id');
                $stmt->execute(array(
                    ':el_id' => $_POST['uniq_id'],
                    ':reaction_time_def' => $react_def_sec,
                    ':reaction_time_low_prio' => $react_low_sec,
                    ':reaction_time_high_prio' => $react_high_sec,
                    ':work_time_def' => $work_def_sec,
                    ':work_time_low_prio' => $work_low_sec,
                    ':work_time_high_prio' => $work_high_sec,
                    ':deadline_time_def' => $deadline_def_sec,
                    ':deadline_time_low_prio' => $deadline_low_sec,
                    ':deadline_time_high_prio' => $deadline_high_sec
                ));
                
                $msg = "<div class=\"alert alert-success\">" . lang('PROFILE_msg_ok') . "</div>";
                echo $msg;
            }
            
            if ($mode == "make_sla_active") {
                
                if ($_POST['name'] == "true") {
                    $h = 1;
                } 
                else if ($_POST['name'] == "false") {
                    $h = 0;
                }
                
                update_val_by_key('sla_system', $_POST['name']);
            }
            
            if ($mode == "save_sla_item") {
                
                $stmt = $dbConnection->prepare('UPDATE sla_plans set name=:t where id=:el_id');
                $stmt->execute(array(
                    ':t' => $_POST['value'],
                    ':el_id' => $_POST['pk']
                ));
            }
            
            if ($mode == "add_cron") {
                $msg=NULL;
                $validator = new GUMP();
                $_POST = $validator->sanitize($_POST);
                
                $rules = array(
                    'subj' => 'required',
                    'msg' => 'required',
                    'client_id_param' => 'required|numeric',
                    'to' => 'required|numeric',
                    'period' => 'required',
                    'time_action' => 'required',
                    'action_start' => 'required'
                );
                $filters = array(
                    'subj' => 'trim|sanitize_string',
                    'msg' => 'trim'
                );
                
                $validator->set_field_name(array(
                    "subj" => lang('NEW_subj')
                ));
                
                GUMP::set_field_name("subj", lang('NEW_subj'));
                GUMP::set_field_name("msg", lang('NEW_MSG'));
                GUMP::set_field_name("client_id_param", lang('NEW_from'));
                GUMP::set_field_name("to", lang('NEW_to'));
                GUMP::set_field_name("period", lang('cron_tab'));
                GUMP::set_field_name("time_action", lang('cron_ta'));
                GUMP::set_field_name("action_start", lang('cron_active'));
                
                $_POST = $validator->filter($_POST, $filters);
                
                $validated = $validator->validate($_POST, $rules);
                
                $status_action = $_POST['status_action'];
                
                if ($_POST['period'] == "day") {
                    $p_arr = $_POST['day_field'];
                } 
                else if ($_POST['period'] == "week") {
                    $p_arr = $_POST['week_select'];
                } 
                else if ($_POST['period'] == "month") {
                    $p_arr = $_POST['month_select'];
                }
                
                if ($validated === true) {
                    $check_error = true;
                    $stmt = $dbConnection->prepare('insert into scheduler_ticket
        (user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, period, period_arr, action_time, dt_start, dt_stop, prio) values (
        :user_init_id, 
        :user_to_id, 
        :date_create, 
        :subj, 
        :msg, 
        :client_id, 
        :unit_id, 
        :period, 
        :period_arr, 
        :action_time, 
        :dt_start, 
        :dt_stop,  
        :prio)');
                    
                    $stmt->execute(array(
                        ':user_init_id' => '1',
                        ':user_to_id' => $_POST['s2id_users_do'],
                        ':date_create' => $CONF['now_dt'],
                        ':subj' => $_POST['subj'],
                        ':msg' => $_POST['msg'],
                        ':client_id' => $_POST['client_id_param'],
                        ':unit_id' => $_POST['to'],
                        ':period' => $_POST['period'],
                        ':period_arr' => $p_arr,
                        ':action_time' => $_POST['time_action'],
                        ':dt_start' => $_POST['action_start'],
                        ':dt_stop' => $_POST['action_stop'],
                        ':prio' => $_POST['prio']
                    ));
                } 
                else {
                    
                    //print_r($is_valid);
                    $check_error = false;
                    $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                    foreach ($validator->get_readable_errors(false) as $key => $value) {
                        $msg.= "<li>" . $value . "</li>";
                    }
                    $msg.= "</ul></p></div>";
                }
                $results[] = array(
                    'check_error' => $check_error,
                    'msg' => $msg
                );
                print json_encode($results);
            }
            
            if ($mode == "check_version") {
                
                $myversion = get_conf_param('version');
                
                //echo $myversion;
                $content = file_get_contents($CONF['update_server'] . "/up.php");
                $data = json_decode($content, true);
                $getver = $data['version'];
                
                $myversion = str_replace('.', '', $myversion);
                $getver = str_replace('.', '', $getver);
                
                //print_r($data);
                //echo $getver;
                if ($myversion >= $getver) {
                    echo "<br><center>" . "You have latest version." . "</center>";
                } 
                else if ($myversion < $getver) {
                    echo "<br><center>" . $data['msg'] . "</center><br>";
                    echo "<a href=\"update.php\" class=\"btn btn-success btn-block btn-sm\">update now</a>";
                }
            }





            if ($mode == "make_ldap_import") {
                include_once ZENLIX_DIR."/library/ldap_import/ldap_import.class.php";
                echo "<br>";
                
                $users_do = $_POST['users_do'];
                $ldap_step3_obj = $_POST['ldap_step3_obj'];
                
                $ldap = new LDAP($_SESSION['zenlix_def_ldap_ip'], $_SESSION['zenlix_def_ldap_domain'], $_SESSION['zenlix_def_ldap_admin_user'], $_SESSION['zenlix_def_ldap_admin_pass']);
                $users = $ldap->get_users();
                
                //$users = array_slice($users, 0, 5);
                ///////////////////////////////////////////////////
                
                $login = $_SESSION['zenlix_def_ldap_login'];
                $fio = $_SESSION['zenlix_def_ldap_fio'];
                $mail = $_SESSION['zenlix_def_ldap_mail'];
                $tel = $_SESSION['zenlix_def_ldap_tel'];
                $adr = $_SESSION['zenlix_def_ldap_adr'];
                $skype = $_SESSION['zenlix_def_ldap_skype'];
                $unit = $_SESSION['zenlix_def_ldap_unit'];
                
                if ($_SESSION['zenlix_def_ldap_priv'] == "4") {
                    $is_client = "1";
                    $privs = "1";
                } 
                else if ($priv != "4") {
                    $is_client = "0";
                    $privs = $_SESSION['zenlix_def_ldap_priv'];
                }
                
                if ($_SESSION['zenlix_def_ldap_priv_add_client'] == "true") {
                    $priv_add_client = 1;
                } 
                else {
                    $priv_add_client = 0;
                }
                if ($_SESSION['zenlix_def_ldap_priv_edit_client'] == "true") {
                    $priv_edit_client = 1;
                } 
                else {
                    $priv_edit_client = 0;
                }
                
                $res_good = array();
                $res_good['num'] = 0;
                $res_good['logins'] = 0;
                $res_good['fios'] = 0;
                
                ///////////////////////////////////////////////////
                if ($ldap_step3_obj == "all") {
                    
                    //echo "all";
                    
                    $i = 0;
                    
                    foreach ($users as $value) {
                        
                        $user_login = "";
                        $user_fio = "";
                        $user_mail = "";
                        $user_tel = "";
                        $user_adr = "";
                        $user_skype = "";
                        $user_unit = "";
                        
                        if ($login != "empty") {
                            $user_login = $value[$login];
                        }
                        if ($fio != "empty") {
                            $user_fio = $value[$fio];
                        }
                        if ($mail != "empty") {
                            $user_mail = $value[$mail];
                        }
                        if ($tel != "empty") {
                            foreach ($value[$tel] as $value1) {
                                $user_tel.= $value1 . " ";
                            }
                        }
                        if ($adr != "empty") {
                            $user_adr = $value[$adr];
                        }
                        if ($skype != "empty") {
                            $user_skype = $value[$skype];
                        }
                        if ($unit != "empty") {
                            $user_unit = $value[$unit];
                        }
                        
                        //$value[$login] //поле логина пользователя
                        
                        if (validate_exist_login($user_login) == true) {
                            
                            $hn = md5(time()) . $i;
                            
                            $res_good['num']++;
                            
                            $stmt = $dbConnection->prepare('INSERT INTO users 
            (fio, 
            login, 
            pass, 
            status, 
            priv, 
            unit, 
            email, 
            messages, 
            lang, 
            priv_add_client, 
            priv_edit_client, 
            ldap_key,
            messages_title,
            uniq_id,
            tel,
            skype,
            unit_desc,
            adr,
            is_client,
            messages_type,
            api_key
            )
values 
            (:fio, 
            :login, 
            :pass, 
            :one, 
            :priv, 
            :unit, 
            :mail, 
            :mess, 
            :lang, 
            :priv_add_client, 
            :priv_edit_client, 
            :lk,
            :messages_title,
            :uniq_id,
            :tel,
            :skype,
            :unit_desc,
            :adr,
            :is_client,
            :msg_type,
            :api_key
            )');
                            $stmt->execute(array(
                                ':fio' => $user_fio,
                                ':login' => $user_login,
                                ':pass' => $hn,
                                ':one' => $_SESSION['zenlix_def_ldap_status'],
                                ':priv' => $privs,
                                ':unit' => $_SESSION['zenlix_def_ldap_unit'],
                                ':mail' => $user_mail,
                                ':mess' => $_SESSION['zenlix_def_ldap_mess'],
                                ':lang' => $_SESSION['zenlix_def_ldap_lang'],
                                ':priv_add_client' => $priv_add_client,
                                ':priv_edit_client' => $priv_edit_client,
                                ':lk' => '1',
                                ':messages_title' => $_SESSION['zenlix_def_ldap_mess_t'],
                                ':uniq_id' => $hn,
                                ':api_key' => md5($hn) ,
                                ':tel' => $user_tel,
                                ':skype' => $user_skype,
                                ':unit_desc' => $user_unit,
                                ':adr' => $user_adr,
                                ':is_client' => $is_client,
                                ':msg_type' => $_SESSION['zenlix_def_ldap_msg_type']
                            ));
                        }
                        $i++;
                    }
                } 
                else if ($ldap_step3_obj == "selected") {
                    
                    //echo "selected";
                    
                    $i = 0;
                    
                    $arrlogins = explode(",", $users_do);
                    
                    foreach ($users as $value) {
                        
                        if (in_array($value[$login], $arrlogins)) {
                            $user_login = "";
                            $user_fio = "";
                            $user_mail = "";
                            $user_tel = "";
                            $user_adr = "";
                            $user_skype = "";
                            $user_unit = "";
                            
                            if ($login != "empty") {
                                $user_login = $value[$login];
                            }
                            if ($fio != "empty") {
                                $user_fio = $value[$fio];
                            }
                            if ($mail != "empty") {
                                $user_mail = $value[$mail];
                            }
                            if ($tel != "empty") {
                                
                                // $user_tel=$value[$tel];
                                
                                foreach ($value[$tel] as $value1) {
                                    $user_tel.= $value1 . " ";
                                }
                            }
                            if ($adr != "empty") {
                                $user_adr = $value[$adr];
                            }
                            if ($skype != "empty") {
                                $user_skype = $value[$skype];
                            }
                            if ($unit != "empty") {
                                $user_unit = $value[$unit];
                            }
                            
                            //$value[$login] //поле логина пользователя
                            
                            if (validate_exist_login($user_login) == true) {
                                
                                $hn = md5(time()) . $i;
                                
                                $res_good['num']++;
                                
                                $stmt = $dbConnection->prepare('INSERT INTO users 
            (fio, 
            login, 
            pass, 
            status, 
            priv, 
            unit, 
            email, 
            messages, 
            lang, 
            priv_add_client, 
            priv_edit_client, 
            ldap_key,
            messages_title,
            uniq_id,
            tel,
            skype,
            unit_desc,
            adr,
            is_client,
            messages_type,
            api_key
            )
values 
            (:fio, 
            :login, 
            :pass, 
            :one, 
            :priv, 
            :unit, 
            :mail, 
            :mess, 
            :lang, 
            :priv_add_client, 
            :priv_edit_client, 
            :lk,
            :messages_title,
            :uniq_id,
            :tel,
            :skype,
            :unit_desc,
            :adr,
            :is_client,
            :msg_type,
            :api_key
            )');
                                $stmt->execute(array(
                                    ':fio' => $user_fio,
                                    ':login' => $user_login,
                                    ':pass' => $hn,
                                    ':one' => $_SESSION['zenlix_def_ldap_status'],
                                    ':priv' => $privs,
                                    ':unit' => $_SESSION['zenlix_def_ldap_unit'],
                                    ':mail' => $user_mail,
                                    ':mess' => $_SESSION['zenlix_def_ldap_mess'],
                                    ':lang' => $_SESSION['zenlix_def_ldap_lang'],
                                    ':priv_add_client' => $priv_add_client,
                                    ':priv_edit_client' => $priv_edit_client,
                                    ':lk' => '1',
                                    ':messages_title' => $_SESSION['zenlix_def_ldap_mess_t'],
                                    ':uniq_id' => $hn,
                                    ':api_key' => md5($hn) ,
                                    ':tel' => $user_tel,
                                    ':skype' => $user_skype,
                                    ':unit_desc' => $user_unit,
                                    ':adr' => $user_adr,
                                    ':is_client' => $is_client,
                                    ':msg_type' => $_SESSION['zenlix_def_ldap_msg_type']
                                ));
                            }
                            $i++;
                        }
                    }
                }
                
                //Импортировано: столько-то, такие-то логины:
                
                
?>
<?php
                echo lang('LDAP_IMPORT_already'); ?>: <?php
                echo $res_good['num']; ?>
<?php
            }
            
            if ($mode == "change_userfield_client") {
                
                if ($_POST['name'] == "false") {
                    $s = 0;
                }
                if ($_POST['name'] == "true") {
                    $s = 1;
                }
                
                $stmt = $dbConnection->prepare('update user_fields set for_client=:name where hash=:h');
                $stmt->execute(array(
                    ':h' => $_POST['hash'],
                    ':name' => $s
                ));
            }
            
            if ($mode == "change_field_client") {
                
                if ($_POST['name'] == "false") {
                    $s = 0;
                }
                if ($_POST['name'] == "true") {
                    $s = 1;
                }
                
                $stmt = $dbConnection->prepare('update ticket_fields set for_client=:name where hash=:h');
                $stmt->execute(array(
                    ':h' => $_POST['hash'],
                    ':name' => $s
                ));
            }
            
            if ($mode == "change_userfield_check") {
                
                if ($_POST['name'] == "false") {
                    $s = 0;
                }
                if ($_POST['name'] == "true") {
                    $s = 1;
                }
                
                $stmt = $dbConnection->prepare('update user_fields set status=:name where hash=:h');
                $stmt->execute(array(
                    ':h' => $_POST['hash'],
                    ':name' => $s
                ));
            }
            
            if ($mode == "change_field_check") {
                
                if ($_POST['name'] == "false") {
                    $s = 0;
                }
                if ($_POST['name'] == "true") {
                    $s = 1;
                }
                
                $stmt = $dbConnection->prepare('update ticket_fields set status=:name where hash=:h');
                $stmt->execute(array(
                    ':h' => $_POST['hash'],
                    ':name' => $s
                ));
            }
            
            if ($mode == "change_userfield_select") {
                
                $stmt = $dbConnection->prepare('update user_fields set t_type=:name where hash=:h');
                $stmt->execute(array(
                    ':h' => $_POST['hash'],
                    ':name' => $_POST['name']
                ));
            }
            
            if ($mode == "change_field_select") {
                
                $stmt = $dbConnection->prepare('update ticket_fields set t_type=:name where hash=:h');
                $stmt->execute(array(
                    ':h' => $_POST['hash'],
                    ':name' => $_POST['name']
                ));
            }
            
            if ($mode == "change_userfield_value") {
                
                $stmt = $dbConnection->prepare('update user_fields set value=:name where hash=:h');
                $stmt->execute(array(
                    ':h' => $_POST['hash'],
                    ':name' => $_POST['name']
                ));
            }
            
            if ($mode == "change_field_value") {
                
                $stmt = $dbConnection->prepare('update ticket_fields set value=:name where hash=:h');
                $stmt->execute(array(
                    ':h' => $_POST['hash'],
                    ':name' => $_POST['name']
                ));
            }
            if ($mode == "change_userfield_name") {
                
                $stmt = $dbConnection->prepare('update user_fields set name=:name where hash=:h');
                $stmt->execute(array(
                    ':h' => $_POST['hash'],
                    ':name' => $_POST['name']
                ));
            }
            if ($mode == "change_field_name") {
                
                $stmt = $dbConnection->prepare('update ticket_fields set name=:name where hash=:h');
                $stmt->execute(array(
                    ':h' => $_POST['hash'],
                    ':name' => $_POST['name']
                ));
            }
            if ($mode == "change_userfield_placeholder") {
                
                $stmt = $dbConnection->prepare('update user_fields set placeholder=:name where hash=:h');
                $stmt->execute(array(
                    ':h' => $_POST['hash'],
                    ':name' => $_POST['name']
                ));
            }
            if ($mode == "change_field_placeholder") {
                
                $stmt = $dbConnection->prepare('update ticket_fields set placeholder=:name where hash=:h');
                $stmt->execute(array(
                    ':h' => $_POST['hash'],
                    ':name' => $_POST['name']
                ));
            }
            
            //ldap_import_next
            if ($mode == "ldap_import_next") {
                
                $_SESSION['zenlix_def_ldap_ip'] = $_POST['ldap_ip'];
                $_SESSION['zenlix_def_ldap_domain'] = $_POST['ldap_domain'];
                $_SESSION['zenlix_def_ldap_admin_user'] = $_POST['ldap_admin_user'];
                $_SESSION['zenlix_def_ldap_admin_pass'] = $_POST['ldap_admin_pass'];
                
                $_SESSION['zenlix_def_ldap_fio'] = $_POST['users_fio'];
                $_SESSION['zenlix_def_ldap_login'] = $_POST['users_login'];
                $_SESSION['zenlix_def_ldap_mail'] = $_POST['users_mail'];
                $_SESSION['zenlix_def_ldap_tel'] = $_POST['users_tel'];
                $_SESSION['zenlix_def_ldap_adr'] = $_POST['users_adr'];
                $_SESSION['zenlix_def_ldap_skype'] = $_POST['users_skype'];
                $_SESSION['zenlix_def_ldap_unit'] = $_POST['users_unit'];
            }
            
            //ldap_import_next
            if ($mode == "ldap_import_next_2") {
                
                $_SESSION['zenlix_def_ldap_lang'] = $_POST['lang'];
                $_SESSION['zenlix_def_ldap_priv'] = $_POST['priv'];
                $_SESSION['zenlix_def_ldap_unit'] = $_POST['unit'];
                $_SESSION['zenlix_def_ldap_priv_add_client'] = $_POST['priv_add_client'];
                $_SESSION['zenlix_def_ldap_priv_edit_client'] = $_POST['priv_edit_client'];
                $_SESSION['zenlix_def_ldap_mess'] = $_POST['mess'];
                $_SESSION['zenlix_def_ldap_mess_t'] = $_POST['mess_t'];
                $_SESSION['zenlix_def_ldap_msg_type'] = $_POST['msg_type'];
                $_SESSION['zenlix_def_ldap_status'] = $_POST['status'];
            }
            
            //ldap_import_check
            if ($mode == "ldap_import_check") {
                
                $_SESSION['zenlix_def_ldap_ip'] = $_POST['ldap_ip'];
                $_SESSION['zenlix_def_ldap_domain'] = $_POST['ldap_domain'];
                $_SESSION['zenlix_def_ldap_admin_user'] = $_POST['ldap_admin_user'];
                $_SESSION['zenlix_def_ldap_admin_pass'] = $_POST['ldap_admin_pass'];
                
                include_once "library/ldap_import/ldap_import.class.php";
                
                $ldap = new LDAP($_POST['ldap_ip'], $_POST['ldap_domain'], $_POST['ldap_admin_user'], $_POST['ldap_admin_pass']);
                $users = $ldap->get_users();
                
                $output = array_slice($users, 0, 5);
?>
<br><hr>
<h4>TOP 5 test result</h4>
                                        <table class="table table-hover table-bordered">
                                            <thead>
                                            <tr>
                                                <th><center><small>name</small></center>    </th>
                                                <th><center><small>mail </small></center></th>
                                                <th><center><small>mobile   </small></center></th>
                                                <th><center><small>skype </small></center></th>
                                                <th><center><small>telephone </small></center></th>
                                                <th><center><small>department </small></center></th>
                                                <th><center><small>title </small></center></th>
                                                <th><center><small>userprincipalname </small></center></th>
                                                <th><center><small>samaccountname </small></center></th>
                                                <th><center><small>othertelephone </small></center></th>

                                            </tr>
                                            </thead>

                                            <tbody>

<?php
                foreach ($output as $value) {
?>
<tr>
    <td style="vertical-align: inherit;"><small><center><?php
                    echo $value['name']; ?></center></small></td>
    <td style="vertical-align: inherit;"><small><center><?php
                    echo $value['mail']; ?></center></small></td>
    <td style="vertical-align: inherit;"><small><center><?php
                    echo $value['mobile']; ?></center></small></td>
    <td style="vertical-align: inherit;"><small><center><?php
                    echo $value['skype']; ?></center></small></td>
    <td style="vertical-align: inherit;"><small><center><?php
                    echo $value['telephone']; ?></center></small></td>
    <td style="vertical-align: inherit;"><small><center><?php
                    echo $value['department']; ?></center></small></td>
    <td style="vertical-align: inherit;"><small><center><?php
                    echo $value['title']; ?></center></small></td>
    <td style="vertical-align: inherit;"><small><center><?php
                    echo $value['userprincipalname']; ?></center></small></td>
    <td style="vertical-align: inherit;"><small><center><?php
                    echo $value['samaccountname']; ?></center></small></td>
    <td style="vertical-align: inherit;"><small><center><?php
                    foreach ($value['othertelephone'] as $value) {
                        
                        // code...
                        echo $value . " ";
                    }
?></center></small></td>
</tr>
    <?php
                }
?>

</tbody>
</table>

<?php
            }
            
            if ($mode == "aprove_yes") {
                $id = ($_POST['id']);
                
                $stmt = $dbConnection->prepare('SELECT 
            id,fio,tel,unit_desc,adr ,email,login, posada, email,client_id,type_op,skype FROM approved_info where id=:id');
                $stmt->execute(array(
                    ':id' => $id
                ));
                $fio = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $q_fio = ($fio['fio']);
                $q_login = ($fio['login']);
                $q_tel = ($fio['tel']);
                $q_pod = ($fio['unit_desc']);
                $q_adr = ($fio['adr']);
                $q_type_op = $fio['type_op'];
                $q_mail = ($fio['email']);
                $q_posada = ($fio['posada']);
                $q_skype = ($fio['skype']);
                $q_cid = ($fio['client_id']);
                
                if ($q_type_op == "edit") {
                    
                    $stmt = $dbConnection->prepare('update users set 
    fio=:qfio, 
    tel=:qtel, 
    login=:qlogin, 
    unit_desc=:qpod,
    adr=:qadr, 
    email=:qemail,
    skype=:qskype, 
    posada=:qposada 
    where id=:cid');
                    
                    $stmt->execute(array(
                        ':qfio' => $q_fio,
                        ':qtel' => $q_tel,
                        ':qlogin' => $q_login,
                        ':qpod' => $q_pod,
                        ':qadr' => $q_adr,
                        ':qemail' => $q_mail,
                        ':qposada' => $q_posada,
                        ':qskype' => $q_skype,
                        ':cid' => $q_cid
                    ));
                } 
                else if ($q_type_op == "add") {
                    
                    $hn = md5(time());
                    $stmt = $dbConnection->prepare('INSERT INTO users 
            (fio, 
            login, 
            status, 
            priv, 
            email, 
            uniq_id,
            posada,
            tel,
            skype,
            unit_desc,
            adr,
            is_client,
            api_key
            )
values 
            (
            :fio, 
            :login, 
            :status, 
            :priv, 
            :email, 
            :uniq_id,
            :posada,
            :tel,
            :skype,
            :unit_desc,
            :adr,
            :is_client,
            :api_key
            )');
                    $stmt->execute(array(
                        ':fio' => $q_fio,
                        ':login' => $q_login,
                        ':status' => '0',
                        ':priv' => '1',
                        ':email' => $q_mail,
                        ':uniq_id' => $hn,
                        ':posada' => $q_posada,
                        ':tel' => $q_tel,
                        ':skype' => $q_skype,
                        ':unit_desc' => $q_pod,
                        ':adr' => $q_adr,
                        ':is_client' => '1',
                        ':api_key' => md5($hn)
                    ));
                }
                
                $stmt = $dbConnection->prepare('delete from approved_info where id=:id');
                $stmt->execute(array(
                    ':id' => $id
                ));
            }
            if ($mode == "aprove_no") {
                $id = ($_POST['id']);
                
                $stmt = $dbConnection->prepare('delete from approved_info where id=:id');
                $stmt->execute(array(
                    ':id' => $id
                ));
            }
            
            if ($mode == "conf_edit_portal") {
                update_val_by_key("portal_status", $_POST['status']);
                update_val_by_key("portal_msg_type", $_POST['msg_type']);
                update_val_by_key("portal_msg_title", $_POST['msg_title']);
                update_val_by_key("portal_msg_text", $_POST['msg_text']);
                
                //portal_msg_status
                update_val_by_key("portal_msg_status", $_POST['portal_msg_status']);
                
                $ntu = $_POST['ntu'];
                if ($_POST['ntu'] == "null") {
                    $ntu = "";
                }
                
                update_val_by_key("portal_posts_mail_users", $ntu);
?>
                <div class="alert alert-success">
                    <?php
                echo lang('PROFILE_msg_ok'); ?>
                </div>
        <?php
            }
            
            if ($mode == "conf_edit_sms") {
                update_val_by_key("smsc_active", $_POST['smsc_active']);
                update_val_by_key("smsc_login", $_POST['smsc_login']);
                update_val_by_key("smsc_pass", $_POST['smsc_pass']);
                update_val_by_key("smsc_list_action", $_POST['sms_nf']);
?>
                <div class="alert alert-success">
                    <?php
                echo lang('PROFILE_msg_ok'); ?>
                </div>
        <?php
            }
            
            if ($mode == "conf_edit_pb") {
                update_val_by_key("pb_api", $_POST['api']);
                update_val_by_key("pb_active", $_POST['pb_active']);
?>
                <div class="alert alert-success">
                    <?php
                echo lang('PROFILE_msg_ok'); ?>
                </div>
        <?php
            }
            
            if ($mode == "conf_edit_email_gate") {
                
                /*
                "&email_gate_status="+$("#email_gate_status").val()+
                "&email_gate_all="+$("#email_gate_all").val()+
                "&to="+$("#to").val()+
                "&users_do="+$("#users_do").val()+
                "&email_gate_mailbox="+$("#email_gate_mailbox").val()+
                "&email_gate_filter="+$("#email_gate_filter").val()+
                "&email_gate_host="+$("#email_gate_host").val()+
                "&email_gate_cat="+$("#email_gate_cat").val()+
                "&email_gate_port="+$("#email_gate_port").val()+
                "&email_gate_login="+$("#email_gate_login").val()+
                "&email_gate_pass="+$("#email_gate_pass").val(),
                */
                
                //echo $_POST['email_gate_filter'];
                update_val_by_key("email_gate_status", $_POST['email_gate_status']);
                update_val_by_key("email_gate_all", $_POST['email_gate_all']);
                update_val_by_key("email_gate_unit_id", $_POST['to']);
                update_val_by_key("email_gate_user_id", $_POST['users_do']);
                update_val_by_key("email_gate_mailbox", $_POST['email_gate_mailbox']);
                update_val_by_key("email_gate_host", $_POST['email_gate_host']);
                update_val_by_key("email_gate_port", $_POST['email_gate_port']);
                update_val_by_key("email_gate_login", $_POST['email_gate_login']);
                update_val_by_key("email_gate_pass", $_POST['email_gate_pass']);
                update_val_by_key("email_gate_filter", stripslashes($_POST['email_gate_filter']));
                update_val_by_key("email_gate_cat", $_POST['email_gate_cat']);
                update_val_by_key("email_gate_connect_param", $_POST['email_gate_cp']);
                
                //update_val_by_key("mail_debug", $_POST['debug']);
                
                
?>
                <div class="alert alert-success">
                    <?php
                echo lang('PROFILE_msg_ok'); ?>
                </div>
        <?php
            }
            
            if ($mode == "re_user") {
                
                $uhash = $_POST['id'];
                
                $stmt = $dbConnection->prepare('update users set status=:s where uniq_id=:id');
                $stmt->execute(array(
                    ':id' => $uhash,
                    ':s' => '1'
                ));
            }
            
            if ($mode == "del_user") {
                
                $uhash = $_POST['id'];
                
                $stmt = $dbConnection->prepare('update users set status=:s where uniq_id=:id');
                $stmt->execute(array(
                    ':id' => $uhash,
                    ':s' => '2'
                ));
            }
            
            if ($mode == "conf_edit_mail") {
                update_val_by_key("mail_type", $_POST['type']);
                update_val_by_key("mail_active", $_POST['mail_active']);
                update_val_by_key("mail_host", $_POST['host']);
                update_val_by_key("mail_port", $_POST['port']);
                update_val_by_key("mail_auth", $_POST['auth']);
                update_val_by_key("mail_auth_type", $_POST['auth_type']);
                update_val_by_key("mail_username", $_POST['username']);
                update_val_by_key("mail_password", $_POST['password']);
                update_val_by_key("mail_from", stripslashes($_POST['from']));
                
                //update_val_by_key("mail_debug", $_POST['debug']);
                
                
?>
                <div class="alert alert-success">
                    <?php
                echo lang('PROFILE_msg_ok'); ?>
                </div>
        <?php
            }
            
            if ($mode == "conf_edit_ticket") {
                $msg=NULL;
                GUMP::set_field_name("days2arch", lang('CONF_2arch'));
                GUMP::set_field_name("file_size", lang('CONF_file_size'));
                
                $is_valid = GUMP::is_valid($_POST, array(
                    'days2arch' => 'required|numeric',
                    'file_size' => 'required|numeric'
                ));
                
                if ($is_valid === true) {
                    $r = true;
                    $bodytag = str_replace(",", "|", $_POST['file_types']);
                    
                    update_val_by_key("days2arch", $_POST['days2arch']);
                    update_val_by_key("fix_subj", $_POST['fix_subj']);
                    update_val_by_key("file_uploads", $_POST['file_uploads']);
                    update_val_by_key("file_types", $bodytag);
                    update_val_by_key("file_size", $_POST['file_size']);
                    update_val_by_key("ticket_last_time", $_POST['ticket_last_time']);
                    
                    //update_val_by_key("mail", $_POST['mail']);
                    $msg.= "<div class=\"alert alert-success\">" . lang('PROFILE_msg_ok') . "</div>";
                } 
                else {
                    
                    //print_r($is_valid);
                    $r = false;
                    
                    //$msg=$is_valid;
                    
                    $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                    foreach ($is_valid as $key => $value) {
                        $msg.= "<li>" . $value . "</li>";
                    }
                    $msg.= "</ul></p></div>";
                }
                $results[] = array(
                    'res' => $r,
                    'msg' => $msg
                );
                print json_encode($results);
            }
            





            //conf_edit_gm
            if ($mode == "conf_edit_gm") {
                $msg=NULL;
                //print_r($_POST);
                
                if ($_POST['to_msg'] == "0") {
                    update_val_by_key("global_msg_to", $_POST['usr_list']);
                } 
                else if ($_POST['to_msg'] == "1") {
                    update_val_by_key("global_msg_to", "all");
                }
                
                update_val_by_key("global_msg_status", $_POST['status']);
                update_val_by_key("global_msg_data", stripslashes($_POST['gm_text']));
                
                if ($_POST['msg_type'] == "0") {
                    update_val_by_key("global_msg_type", 'info');
                }
                if ($_POST['msg_type'] == "1") {
                    update_val_by_key("global_msg_type", 'warning');
                }
                if ($_POST['msg_type'] == "2") {
                    update_val_by_key("global_msg_type", 'danger');
                }
                
                $msg = "<div class=\"alert alert-success\">" . lang('PROFILE_msg_ok') . "</div>";
                echo $msg;
            }
            
            if ($mode == "conf_edit_main") {
                $msg=NULL;
                GUMP::set_field_name("ldap", lang('EXT_ldap_ip'));
                GUMP::set_field_name("name_of_firm", lang('CONF_name'));
                
                //GUMP::set_field_name("node_port", 'NodeJS port');
                
                GUMP::set_field_name("mail", lang('CONF_mail'));
                GUMP::set_field_name("title_header", lang('CONF_title_org'));
                
                $is_valid = GUMP::is_valid($_POST, array(
                    'ldap' => 'valid_ip',
                    'name_of_firm' => 'required|max_len,100',
                    'title_header' => 'required|max_len,100',
                    
                    //'node_port' => 'required|numeric',
                    'mail' => 'required|valid_email'
                ));
                
                if ($is_valid === true) {
                    $r = true;
                    
                    if (substr($_POST['hostname'], -1) == "/") {
                        $_POST['hostname'] = rtrim($_POST['hostname'], "/");
                        
                        // $_POST['hostname']=$CONF['hostname']."/";
                        
                        
                    }
                    
                    update_val_by_key("ldap_ip", $_POST['ldap']);
                    update_val_by_key("ldap_domain", $_POST['ldapd']);
                    update_val_by_key("name_of_firm", stripslashes($_POST['name_of_firm']));
                    update_val_by_key("title_header", stripslashes($_POST['title_header']));
                    update_val_by_key("hostname", $_POST['hostname']);
                    
                    update_val_by_key("node_port", $_POST['node_port']);
                    update_val_by_key("time_zone", stripslashes($_POST['time_zone']));
                    update_val_by_key("allow_register", $_POST['allow_register']);
                    update_val_by_key("lang_def", $_POST['lang']);
                    
                    //$bodytag = str_replace(",", "|", $_POST['file_types']);
                    update_val_by_key("allow_forgot", $_POST['allow_forgot']);
                    update_val_by_key("api_status", $_POST['api_status']);
                    update_val_by_key("twig_cache", $_POST['twig_cache']);
                    update_val_by_key("mail", $_POST['mail']);
                    $msg.= "<div class=\"alert alert-success\">" . lang('PROFILE_msg_ok') . "</div>";
                } 
                else {
                    
                    //print_r($is_valid);
                    $r = false;
                    
                    //$msg=$is_valid;
                    
                    $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                    foreach ($is_valid as $key => $value) {
                        $msg.= "<li>" . $value . "</li>";
                    }
                    $msg.= "</ul></p></div>";
                }
                $results[] = array(
                    'res' => $r,
                    'msg' => $msg
                );
                print json_encode($results);
            }
            
            if ($mode == "sla_del") {
                $id = ($_POST['id']);
                
                $stmt = $dbConnection->prepare('UPDATE sla_plans set parent_id=:t where parent_id=:el_id');
                $stmt->execute(array(
                    ':t' => '0',
                    ':el_id' => $_POST['id']
                ));
                
                $stmt = $dbConnection->prepare('delete from sla_plans where id=:id');
                $stmt->execute(array(
                    ':id' => $id
                ));
                get_sla_view();
            }
            
            if ($mode == "subj_del") {
                $id = ($_POST['id']);
                $stmt = $dbConnection->prepare('UPDATE subj set parent_id=:t where parent_id=:el_id');
                $stmt->execute(array(
                    ':t' => '0',
                    ':el_id' => $_POST['id']
                ));
                
                $stmt = $dbConnection->prepare('delete from subj where id=:id');
                $stmt->execute(array(
                    ':id' => $id
                ));
                showMenu_sla();
            }
            if ($mode == "deps_add") {
                $t = ($_POST['text']);
                
                $stmt = $dbConnection->prepare('insert into deps (name) values (:t)');
                $stmt->execute(array(
                    ':t' => $t
                ));
                
                $stmt = $dbConnection->prepare('select id, name, status from deps where id!=:n');
                $stmt->execute(array(
                    ':n' => '0'
                ));
                $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    
                    <th><center><?php
                echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php
                echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                
                //while ($row = mysql_fetch_assoc($results)) {
                foreach ($res1 as $row) {
                    $cl = "";
                    if ($row['status'] == "0") {
                        $id_action = "deps_show";
                        $icon = "<i class=\"fa fa-eye-slash\"></i>";
                        $cl = "active";
                    }
                    if ($row['status'] == "1") {
                        $id_action = "deps_hide";
                        $icon = "<i class=\"fa fa-eye\"></i>";
                        $cl = "";
                    }
?>
                    <tr id="tr_<?php
                    echo $row['id']; ?>" class="<?php
                    echo $cl; ?>">


                        
                        <td><small><a href="#" data-pk="<?php
                    echo $row['id'] ?>" data-url="action" id="edit_deps" data-type="text"><?php
                    echo $row['name']; ?></a></small></td>
                        <td><small><center>
                        <button id="deps_del" type="button" class="btn btn-danger btn-xs" value="<?php
                    echo $row['id']; ?>"><i class="fa fa fa-trash"></i></button>
                        <button id="<?php
                    echo $id_action; ?>" type="button" class="btn btn-default btn-xs" value="<?php
                    echo $row['id']; ?>"><?php
                    echo $icon; ?></button>
                        
                        </center></small></td>
                    </tr>
                <?php
                } ?>



                </tbody>
            </table>
            <br>
        <?php
            }
            
            if ($mode == "files_del") {
                $id = ($_POST['id']);
                
                $stmt2 = $dbConnection->prepare('SELECT file_ext from files where file_hash=:id');
                $stmt2->execute(array(
                    ':id' => $id
                ));
                $max = $stmt2->fetch(PDO::FETCH_NUM);
                $ext = $max[0];
                
                unlink(ZENLIX_DIR . "/upload_files/" . $id . "." . $ext);
                $stmt = $dbConnection->prepare('delete from files where file_hash=:id');
                $stmt->execute(array(
                    ':id' => $id
                ));
            }
            
            if ($mode == "deps_del") {
                $id = ($_POST['id']);
                
                $stmt = $dbConnection->prepare('delete from deps where id=:id');
                $stmt->execute(array(
                    ':id' => $id
                ));
                
                /*
                найти всех пользователей у которых есть этот отдел
                обновить пользователя
                */
                
                $stmt = $dbConnection->prepare('select id, name, status from deps where id!=:n');
                $stmt->execute(array(
                    ':n' => '0'
                ));
                $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    
                    <th><center><?php
                echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php
                echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($res1 as $row) {
                    $cl = "";
                    if ($row['status'] == "0") {
                        $id_action = "deps_show";
                        $icon = "<i class=\"fa fa-eye-slash\"></i>";
                        $cl = "active";
                    }
                    if ($row['status'] == "1") {
                        $id_action = "deps_hide";
                        $icon = "<i class=\"fa fa-eye\"></i>";
                        $cl = "";
                    }
?>
                    <tr id="tr_<?php
                    echo $row['id']; ?>" class="<?php
                    echo $cl; ?>">


                        
                        <td><small><a href="#" data-pk="<?php
                    echo $row['id'] ?>" data-url="action" id="edit_deps" data-type="text"><?php
                    echo $row['name']; ?></a></small></td>
                        <td><small><center><button id="deps_del" type="button" class="btn btn-danger btn-xs" value="<?php
                    echo $row['id']; ?>"><i class="fa fa fa-trash"></i></button> <button id="<?php
                    echo $id_action; ?>" type="button" class="btn btn-default btn-xs" value="<?php
                    echo $row['id']; ?>"><?php
                    echo $icon; ?></button></center></small></center></small></td>
                    </tr>
                <?php
                } ?>



                </tbody>
            </table>
            <br>
        <?php
            }
            
            if ($mode == "subj_edit") {
                $v = ($_POST['v']);
                $sid = ($_POST['id']);
                
                $stmt = $dbConnection->prepare('update subj set name=:v where id=:sid');
                $stmt->execute(array(
                    ':sid' => $sid,
                    ':v' => $v
                ));
                
                $stmt = $dbConnection->prepare('select id, name from subj');
                $stmt->execute();
                $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?php
                echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php
                echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($res1 as $row) {
?>
                    <tr id="tr_<?php
                    echo $row['id']; ?>">


                        <td><small><center><?php
                    echo $row['id']; ?></center></small></td>
                        <td><small><?php
                    echo $row['name']; ?></small></td>
                        <td><small><center><button id="subj_del" type="button" class="btn btn-danger btn-xs" value="<?php
                    echo $row['id']; ?>">del</button></center></small></td>
                    </tr>
                <?php
                } ?>



                </tbody>
            </table>
            <br>
        <?php
            }
            
            //add_slaplan_item
            
            if ($mode == "add_slaplan_item") {
                $t = ($_POST['text']);
                
                $stmt = $dbConnection->prepare('insert into sla_plans (name, parent_id, uniq_id) values (:t, 0, :hn)');
                $stmt->execute(array(
                    ':t' => $t,
                    ':hn' => md5(time())
                ));
                
                get_sla_view();
            }
            if ($mode == "subj_add") {
                $t = ($_POST['text']);
                
                $stmt = $dbConnection->prepare('insert into subj (name, parent_id) values (:t, 0)');
                $stmt->execute(array(
                    ':t' => $t
                ));
                
                showMenu_sla();
            }
            
            if ($mode == "posada_add") {
                $t = ($_POST['text']);
                
                $stmt = $dbConnection->prepare('insert into posada (name) values (:t)');
                $stmt->execute(array(
                    ':t' => $t
                ));
                
                $stmt = $dbConnection->prepare('select id, name from posada');
                $stmt->execute();
                $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?php
                echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php
                echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($res1 as $row) {
?>
                    <tr id="tr_<?php
                    echo $row['id']; ?>">


                        <td><small><center><?php
                    echo $row['id']; ?></center></small></td>
                        <td><small><?php
                    echo $row['name']; ?></small></td>
                        <td><small><center><button id="posada_del" type="button" class="btn btn-danger btn-xs" value="<?php
                    echo $row['id']; ?>">del</button></center></small></td>
                    </tr>
                <?php
                } ?>



                </tbody>
            </table>
            <br>
        <?php
            }
            
            if ($mode == "cron_del") {
                $id = ($_POST['id']);
                
                $stmt = $dbConnection->prepare('delete from scheduler_ticket where id=:id');
                $stmt->execute(array(
                    ':id' => $id
                ));
            }
            
            if ($mode == "posada_del") {
                $id = ($_POST['id']);
                
                $stmt = $dbConnection->prepare('delete from posada where id=:id');
                $stmt->execute(array(
                    ':id' => $id
                ));
                
                $stmt = $dbConnection->prepare('select id, name from posada');
                $stmt->execute();
                $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?php
                echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php
                echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($res1 as $row) {
?>
                    <tr id="tr_<?php
                    echo $row['id']; ?>">


                        <td><small><center><?php
                    echo $row['id']; ?></center></small></td>
                        <td><small><?php
                    echo $row['name']; ?></small></td>
                        <td><small><center><button id="posada_del" type="button" class="btn btn-danger btn-xs" value="<?php
                    echo $row['id']; ?>">del</button></center></small></td>
                    </tr>
                <?php
                } ?>



                </tbody>
            </table>
            <br>
        <?php
            }
            
            if ($mode == "units_add") {
                $t = ($_POST['text']);
                
                $stmt = $dbConnection->prepare('insert into units (name) values (:t)');
                $stmt->execute(array(
                    ':t' => $t
                ));
                
                $stmt = $dbConnection->prepare('select id, name,status from units');
                $stmt->execute();
                $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    
                    <th><center><?php
                echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php
                echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($res1 as $row) {
if ($row['status'] == 1) {
    $l_a='units_lock';
    $l_i='unlock';
}
if ($row['status'] == 0) {
    $l_a='units_unlock';
    $l_i='lock';
}
?>
    <tr id="tr_<?php
            echo $row['id']; ?>">
    
    
    
    <td><small><a href="#" data-pk="<?php
            echo $row['id'] ?>" data-url="action" id="edit_units" data-type="text"><?php
            echo $row['name']; ?></a></small></td>
<td><small><center><button id="units_del" type="button" class="btn btn-danger btn-xs" value="<?php
            echo $row['id']; ?>"><i class="fa fa fa-trash"></i></button>


<button id="<?php echo $l_a; ?>" type="button" class="btn btn-default btn-xs" value="<?php
            echo $row['id']; ?>"><i class="fa fa fa-<?php echo $l_i; ?>"></i></button>

            </center></small></td>
    </tr>
                <?php
                } ?>



                </tbody>
            </table>
            <br>
        <?php
            }
            if ($mode == "units_del") {
                $id = ($_POST['id']);
                
                $stmt = $dbConnection->prepare('delete from units where id=:id');
                $stmt->execute(array(
                    ':id' => $id
                ));
                
                $stmt = $dbConnection->prepare('select id, name,status from units');
                $stmt->execute();
                $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    
                    <th><center><?php
                echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php
                echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($res1 as $row) {
if ($row['status'] == 1) {
    $l_a='units_lock';
    $l_i='unlock';
}
if ($row['status'] == 0) {
    $l_a='units_unlock';
    $l_i='lock';
}
?>
    <tr id="tr_<?php
            echo $row['id']; ?>">
    
    
    
    <td><small><a href="#" data-pk="<?php
            echo $row['id'] ?>" data-url="action" id="edit_units" data-type="text"><?php
            echo $row['name']; ?></a></small></td>
<td><small><center><button id="units_del" type="button" class="btn btn-danger btn-xs" value="<?php
            echo $row['id']; ?>"><i class="fa fa fa-trash"></i></button>


<button id="<?php echo $l_a; ?>" type="button" class="btn btn-default btn-xs" value="<?php
            echo $row['id']; ?>"><i class="fa fa fa-<?php echo $l_i; ?>"></i></button>

            </center></small></td>
    </tr>
                <?php
                } ?>



                </tbody>
            </table>
            <br>
        <?php
            }
            
            if ($mode == "mailers_send") {
                
                $s = $_POST['subj_mailers'];
                $m = $_POST['msg'];
                $ulist = $_POST['users_list'];
                $upriv_arr = $_POST['users_priv'];
                $u_units = $_POST['users_units'];
                
                //print_r($_POST);
                if ($_POST['type_to_mail'] == "1") {
                    
                    if (!$ulist) {
                        
                        //не указал получаталей
                        
                        
                    } 
                    else if ($ulist) {
                        
                        //список всех получателей
                        //$ulist=implode(',', $ulist);
                        
                        
                    }
                } 
                else if ($_POST['type_to_mail'] == "2") {
                    
                    $ulist_arr = array();
                    
                    //$result = array_intersect($ee, $ec);
                    
                    $stmt = $dbConnection->prepare('SELECT id,unit,is_client,priv FROM users where email REGEXP :r');
                    $stmt->execute(array(
                        ':r' => '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
                    ));
                    $res1 = $stmt->fetchAll();
                    foreach ($res1 as $v) {
                        
                        $ec = explode(",", $v['unit']);
                        
                        if ($upriv_arr) {
                            if (in_array("client", $upriv_arr)) {
                                if ($v['is_client'] == "1") {
                                    array_push($ulist_arr, $v['id']);
                                }
                            }
                            
                            if (in_array($v['priv'], $upriv_arr)) {
                                
                                //array_push($ulist_arr, $v['id']);
                                
                                if ($u_units) {
                                    $r = array_intersect($u_units, $ec);
                                    if ($r) {
                                        array_push($ulist_arr, $v['id']);
                                    }
                                } 
                                else if (!$u_units) {
                                    array_push($ulist_arr, $v['id']);
                                }
                            }
                        } 
                        else if (!$upriv_arr) {
                            
                            if ($u_units) {
                                $r = array_intersect($u_units, $ec);
                                if ($r) {
                                    array_push($ulist_arr, $v['id']);
                                }
                            } 
                            else if (!$u_units) {
                                
                                //echo "ok";
                                array_push($ulist_arr, $v['id']);
                            }
                        }
                    }
                    
                    $ulist = $ulist_arr;
                    
                    //$ulist=implode(",", $ulist_arr);
                    
                    
                }
                
                $ulist = array_unique($ulist);
                
                if ($_POST['check'] != "true") {
                    echo "<ul>";
                    foreach ($ulist as $k) {
                        
                        // code...
                        echo "<li>" . nameshort(name_of_user_ret_nolink($k)) . " (" . get_user_val_by_id($k, 'email') . ")</li>";
                    }
                    echo "</ul>";
                }
                
                if ($_POST['check'] == "true") {
                    
                    //echo "ok";
                    
                    if ($ulist) {
                        $su = implode(",", $ulist);
                        
                        update_val_by_key('mailers_subj', $s);
                        update_val_by_key('mailers_text', $m);
                        
                        $stmt = $dbConnection->prepare('insert into notification_pool (delivers_id, type_op, ticket_id, dt) VALUES (:delivers_id, :type_op, :tid, :n)');
                        $stmt->execute(array(
                            ':delivers_id' => $su,
                            ':type_op' => 'mailers',
                            ':tid' => '0',
                            ':n' => $CONF['now_dt']
                        ));
?>
<div class="alert alert-success"><i class="fa fa-check"></i> 
<?php
                        echo lang('MAILERS_OK'); ?>
</div>
    <?php
                    } 
                    else if (!$ulist) {
?>
<div class="alert alert-danger"><?php
                        echo lang('MAILERS_ERROR'); ?>
</div>
<?php
                    }
                }
                
                //echo "ПОКАЗАТЬ КОМУ БУДЕТ РАССЫЛКА И ТОЛЬКО ПОТОМ ПОДТВЕРЖИТЬ";
                
                //print_r($_POST['users_list']);
                //Список получателей
                //$ulist
                
                //Тема
                //$s
                
                //Сообщение
                //$m
                
                
            }
            
            if ($mode == "edit_user") {
                $usid = ($_POST['idu']);
                $status = ($_POST['status']);
                
                $fio = ($_POST['fio']);
                $login = ($_POST['login']);
                $pass = md5($_POST['pass']);
                $priv = ($_POST['priv']);
                $mail = strtolower($_POST['mail']);
                $mess = ($_POST['mess']);
                $mess_title = ($_POST['mess_t']);
                $tel = $_POST['tel'];
                $skype = $_POST['skype'];
                $adr = $_POST['adr'];
                $push = $_POST['push'];
                $lang = ($_POST['lang']);
                $pidrozdil = $_POST['pidrozdil'];
                $posada = $_POST['posada'];
                $msg_type = $_POST['msg_type'];
                
               // $main_unit_user=$_POST['main_unit_user'];

                $def_unit_id = $_POST['def_unit_id'];
                $def_user_id = $_POST['def_user_id'];
                $user_to_def = $_POST['user_to_def'];
                
                $unit = ($_POST['unit']);
                $mail_nf = $_POST['mail_nf'];
                
                //$_POST['mail'];
                $stmt2r = $dbConnection->prepare('SELECT id from users_notify where user_id=:uto');
                $stmt2r->execute(array(
                    ':uto' => get_user_val_by_hash($usid, 'id')
                ));
                $tt2r = $stmt2r->fetch(PDO::FETCH_ASSOC);
                
                if ($tt2r['id']) {
                    $stmt2 = $dbConnection->prepare('update users_notify set mail=:mail, pb=:pb, sms=:sms where user_id=:user_id');
                    $stmt2->execute(array(
                        ':user_id' => get_user_val_by_hash($usid, 'id') ,
                        ':mail' => $mail_nf,
                        ':pb' => '',
                        ':sms' => ''
                    ));
                } 
                else if (!$tt2r['id']) {
                    
                    $stmt2 = $dbConnection->prepare('insert into users_notify (user_id,mail,pb,sms) values (:user_id,:mail,:pb,:sms)');
                    $stmt2->execute(array(
                        ':user_id' => get_user_val_by_hash($usid, 'id') ,
                        ':mail' => $mail_nf,
                        ':pb' => '',
                        ':sms' => ''
                    ));
                }
                
                //########################## ADDITIONAL FIELDS ###############################
                
                $stmt = $dbConnection->prepare('SELECT * FROM user_fields where status=:n');
                $stmt->execute(array(
                    ':n' => '1'
                ));
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $row) {
                    
                    $cur_hash = $row['hash'];
                    
                    if ($_POST[$cur_hash]) {
                        
                        //insert
                        
                        $v_field = $_POST[$cur_hash];
                        if ($row['t_type'] == "multiselect") {
                            
                            // code...
                            $v_field = implode(",", $_POST[$cur_hash]);
                        }
                        
                        $stmtf = $dbConnection->prepare('SELECT id FROM user_data where user_id=:val and field_id=:fid');
                        $stmtf->execute(array(
                            ':val' => get_user_val_by_hash($usid, 'id') ,
                            ':fid' => $row['id']
                        ));
                        $ifex = $stmtf->fetch(PDO::FETCH_ASSOC);
                        
                        if ($ifex['id']) {
                            $stmts = $dbConnection->prepare('update user_data set field_val=:field_val, field_name=:field_name where field_id=:field_id and user_id=:user_id');
                            $stmts->execute(array(
                                ':user_id' => get_user_val_by_hash($usid, 'id') ,
                                ':field_id' => $row['id'],
                                ':field_val' => $v_field,
                                ':field_name' => $row['name']
                            ));
                        } 
                        else if (!$ifex['id']) {
                            
                            $stmts = $dbConnection->prepare('insert into user_data (user_id,field_id,field_val, field_name) VALUES (:user_id,:field_id,:field_val,:field_name)');
                            $stmts->execute(array(
                                ':user_id' => get_user_val_by_hash($usid, 'id') ,
                                ':field_id' => $row['id'],
                                ':field_val' => $v_field,
                                ':field_name' => $row['name']
                            ));
                        }
                    }
                }
                
/*
//get_user_val_by_hash($hn,'id')
if ($pidrozdil != "NULL") {

if ($main_unit_user == "true") {

    $stmt = $dbConnection->prepare('update units set main_user=:user_id where id=:pidrozdil');

            $stmt->execute(array(
                ':user_id' => get_user_val_by_hash($usid, 'id'),
                ':pidrozdil' => $pidrozdil
            ));
        }

else if ($main_unit_user == "false") {
                    $stmt2r1 = $dbConnection->prepare('SELECT main_user from units where id=:uto');
                $stmt2r1->execute(array(
                    ':uto' => $pidrozdil
                ));
                $tt2r = $stmt2r1->fetch(PDO::FETCH_ASSOC);
                if ($tt2r['main_user'] == get_user_val_by_hash($usid, 'id')) {
                        $stmt = $dbConnection->prepare('update units set main_user=Null where id=:pidrozdil');

            $stmt->execute(array(
                ':pidrozdil' => $pidrozdil
            ));
                }
}


}

*/
                //########################## ADDITIONAL FIELDS ###############################
                
                if ($user_to_def == "true") {
                    
                    if ($def_unit_id != "0") {
                        
                        $user_2_unit = $def_unit_id;
                        
                        if ($def_user_id != "null") {
                            $user_2_user = $def_user_id;
                        } 
                        else if ($def_user_id == "null") {
                            $user_2_user = "0";
                        }
                    } 
                    else if ($def_unit_id == "0") {
                        
                        $user_2_unit = "0";
                        $user_2_user = "0";
                    }
                } 
                else {
                    
                    $user_2_unit = "0";
                    $user_2_user = "0";
                }
                
                if ($priv == "4") {
                    $is_client = "1";
                    $privs = "1";
                } 
                else if ($priv != "4") {
                    $is_client = "0";
                    $privs = $priv;
                }
                
                $priv_add_client = $_POST['priv_add_client'];
                $priv_edit_client = $_POST['priv_edit_client'];
                $ldap_key = $_POST['ldap_auth_key'];
                if ($ldap_key == "true") {
                    $ldap_key = 1;
                } 
                else {
                    $ldap_key = 0;
                }
                if ($priv_add_client == "true") {
                    $priv_add_client = 1;
                } 
                else {
                    $priv_add_client = 0;
                }
                if ($priv_edit_client == "true") {
                    $priv_edit_client = 1;
                } 
                else {
                    $priv_edit_client = 0;
                }
                
                if (strlen($_POST['pass']) > 1) {
                    
                    $stmt = $dbConnection->prepare('update users set
                fio=:fio, 
                login=:login,
                pass=:pass,
                status=:status, 
                priv=:priv, 
                unit=:unit, 
                email=:mail, 
                messages=:mess, 
                lang=:lang, 
                ldap_key=:lk,
                priv_add_client=:priv_add_client,
                priv_edit_client=:priv_edit_client,
                pb=:pb,
                messages_title=:messages_title,
                uniq_id=:uniq_id,
                posada=:posada,
                tel=:tel,
                skype=:skype,
                unit_desc=:unit_desc,
                adr=:adr,
                is_client=:is_client,
                messages_type=:msg_type,
                def_unit_id=:def_unit_id,
                def_user_id=:def_user_id,
                api_key=:ak
                where uniq_id=:usid
                ');
                    $stmt->execute(array(
                        ':fio' => $fio,
                        ':login' => $login,
                        ':status' => $status,
                        ':priv' => $privs,
                        ':unit' => $unit,
                        ':mail' => $mail,
                        ':mess' => $mess,
                        ':lang' => $lang,
                        ':usid' => $usid,
                        ':lk' => $ldap_key,
                        ':pass' => $pass,
                        ':priv_add_client' => $priv_add_client,
                        ':priv_edit_client' => $priv_edit_client,
                        ':pb' => $push,
                        ':messages_title' => $mess_title,
                        ':uniq_id' => $usid,
                        ':posada' => $posada,
                        ':tel' => $tel,
                        ':skype' => $skype,
                        ':unit_desc' => $pidrozdil,
                        ':adr' => $adr,
                        ':is_client' => $is_client,
                        ':msg_type' => $msg_type,
                        ':def_unit_id' => $user_2_unit,
                        ':def_user_id' => $user_2_user,
                        ':ak' => md5(time())
                    ));
                } 
                else {
                    $stmt = $dbConnection->prepare('update users set
                fio=:fio, 
                login=:login,
                status=:status, 
                priv=:priv, 
                unit=:unit, 
                email=:mail, 
                messages=:mess, 
                lang=:lang, 
                ldap_key=:lk,
                priv_add_client=:priv_add_client,
                priv_edit_client=:priv_edit_client,
                pb=:pb,
                messages_title=:messages_title,
                uniq_id=:uniq_id,
                posada=:posada,
                tel=:tel,
                skype=:skype,
                unit_desc=:unit_desc,
                adr=:adr,
                is_client=:is_client,
                messages_type=:msg_type,
                def_unit_id=:def_unit_id,
                def_user_id=:def_user_id
                where uniq_id=:usid
                ');
                    $stmt->execute(array(
                        ':fio' => $fio,
                        ':login' => $login,
                        ':status' => $status,
                        ':priv' => $privs,
                        ':unit' => $unit,
                        ':mail' => $mail,
                        ':mess' => $mess,
                        ':lang' => $lang,
                        ':usid' => $usid,
                        ':lk' => $ldap_key,
                        ':priv_add_client' => $priv_add_client,
                        ':priv_edit_client' => $priv_edit_client,
                        ':pb' => $push,
                        ':messages_title' => $mess_title,
                        ':uniq_id' => $usid,
                        ':posada' => $posada,
                        ':tel' => $tel,
                        ':skype' => $skype,
                        ':unit_desc' => $pidrozdil,
                        ':adr' => $adr,
                        ':is_client' => $is_client,
                        ':msg_type' => $msg_type,
                        ':def_unit_id' => $user_2_unit,
                        ':def_user_id' => $user_2_user
                    ));
                }
                
                /*
                $fio=($_POST['fio']);
                $login=($_POST['login']);
                
                $unit=($_POST['unit']);
                $priv=($_POST['priv']);
                $status=($_POST['status']);
                $usid=($_POST['idu']);
                $mail=($_POST['mail']);
                $mess=($_POST['mess']);
                $lang=($_POST['lang']);
                $priv_add_client=$_POST['priv_add_client'];
                $priv_edit_client=$_POST['priv_edit_client'];
                $ldap_key=$_POST['ldap_auth_key'];
                if ($ldap_key == "true") {$ldap_key=1;} else {$ldap_key=0;}
                if ($priv_add_client == "true") {$priv_add_client=1;} else {$priv_add_client=0;}
                if ($priv_edit_client == "true") {$priv_edit_client=1;} else {$priv_edit_client=0;}
                
                if (strlen($_POST['pass'])>1) {
                $p=md5($_POST['pass']);
                
                $stmt = $dbConnection->prepare('update users set 
                fio=:fio, 
                login=:login,
                pass=:pass,
                status=:status, 
                priv=:priv, 
                unit=:unit, 
                email=:mail, 
                messages=:mess, 
                lang=:lang, 
                ldap_key=:lk,
                priv_add_client=:priv_add_client,
                priv_edit_client=:priv_edit_client  
                where id=:usid');
                $stmt->execute(array(
                ':fio'=>$fio, 
                ':login'=>$login, 
                ':status'=>$status, 
                ':priv'=>$priv, 
                ':unit'=>$unit, 
                ':mail'=>$mail, 
                ':mess'=>$mess, 
                ':lang'=>$lang, 
                ':usid'=>$usid, 
                ':lk'=>$ldap_key,
                ':pass'=>$p,
                ':priv_add_client'=>$priv_add_client,
                ':priv_edit_client'=>$priv_edit_client));
                
                }
                else { $p="";
                $stmt = $dbConnection->prepare('update users set fio=:fio, login=:login, status=:status, priv=:priv, unit=:unit, email=:mail, messages=:mess, lang=:lang, ldap_key=:lk,priv_add_client=:priv_add_client,priv_edit_client=:priv_edit_client where id=:usid');
                $stmt->execute(array(':fio'=>$fio, ':login'=>$login, ':status'=>$status, ':priv'=>$priv, ':unit'=>$unit, ':mail'=>$mail, ':mess'=>$mess, ':lang'=>$lang, ':usid'=>$usid,':lk'=>$ldap_key,':priv_add_client'=>$priv_add_client,':priv_edit_client'=>$priv_edit_client));
                
                }
                
                */
            }
            


            if ($mode == "unit_save") {

$stmt = $dbConnection->prepare('update units set name=:name, main_user=:main_user where id=:id');

                $stmt->execute(array(
                    ':name'=>$_POST['name'],
                    ':main_user'=>$_POST['main_user'],
                    ':id'=>$_POST['id']));

            }




            if ($mode == "add_user") {
                $fio = ($_POST['fio']);
                $login = ($_POST['login']);
                $pass = md5($_POST['pass']);
                $priv = ($_POST['priv']);
                $mail = strtolower($_POST['mail']);
                $mess = ($_POST['mess']);
                $mess_title = ($_POST['mess_t']);
                $tel = $_POST['tel'];
                $skype = $_POST['skype'];
                $adr = $_POST['adr'];
                $push = $_POST['push'];
                $lang = ($_POST['lang']);
                $pidrozdil = $_POST['pidrozdil'];
                $posada = $_POST['posada'];
                $msg_type = $_POST['msg_type'];
               // $main_unit_user= $_POST['main_unit_user'];
                $def_unit_id = $_POST['def_unit_id'];
                $def_user_id = $_POST['def_user_id'];
                $user_to_def = $_POST['user_to_def'];
                
                $mail_nf = $_POST['mail_nf'];
                
                if ($user_to_def == "true") {
                    
                    if ($def_unit_id != "0") {
                        
                        $user_2_unit = $def_unit_id;
                        
                        if ($def_user_id != "null") {
                            $user_2_user = $def_user_id;
                        } 
                        else if ($def_user_id == "null") {
                            $user_2_user = "0";
                        }
                    } 
                    else if ($def_unit_id == "0") {
                        
                        $user_2_unit = "0";
                        $user_2_user = "0";
                    }
                } 
                else {
                    
                    $user_2_unit = "0";
                    $user_2_user = "0";
                }
                
                //$hidden=array();
                //$hidden = ($_POST['unit']);
                //print_r($hidden);
                $unit = ($_POST['unit']);
                
                if ($priv == "4") {
                    $is_client = "1";
                    $privs = "1";
                } 
                else if ($priv != "4") {
                    $is_client = "0";
                    $privs = $priv;
                }
                
                $priv_add_client = $_POST['priv_add_client'];
                $priv_edit_client = $_POST['priv_edit_client'];
                $ldap_key = $_POST['ldap_auth_key'];
                if ($ldap_key == "true") {
                    $ldap_key = 1;
                } 
                else {
                    $ldap_key = 0;
                }
                if ($priv_add_client == "true") {
                    $priv_add_client = 1;
                } 
                else {
                    $priv_add_client = 0;
                }
                if ($priv_edit_client == "true") {
                    $priv_edit_client = 1;
                } 
                else {
                    $priv_edit_client = 0;
                }
                
                $hn = md5(time());
                
                $stmt = $dbConnection->prepare('INSERT INTO users 
            (fio, 
            login, 
            pass, 
            status, 
            priv, 
            unit, 
            email, 
            messages, 
            lang, 
            priv_add_client, 
            priv_edit_client, 
            ldap_key,
            pb,
            messages_title,
            uniq_id,
            api_key,
            posada,
            tel,
            skype,
            unit_desc,
            adr,
            is_client,
            messages_type,
            def_unit_id,
            def_user_id
            )
values 
            (:fio, 
            :login, 
            :pass, 
            :one, 
            :priv, 
            :unit, 
            :mail, 
            :mess, 
            :lang, 
            :priv_add_client, 
            :priv_edit_client, 
            :lk,
            :pb,
            :messages_title,
            :uniq_id,
            :api_key,
            :posada,
            :tel,
            :skype,
            :unit_desc,
            :adr,
            :is_client,
            :msg_type,
            :def_unit_id,
            :def_user_id
            )');
                $stmt->execute(array(
                    ':fio' => $fio,
                    ':login' => $login,
                    ':pass' => $pass,
                    ':one' => '1',
                    ':priv' => $privs,
                    ':unit' => $unit,
                    ':mail' => $mail,
                    ':mess' => $mess,
                    ':lang' => $lang,
                    ':priv_add_client' => $priv_add_client,
                    ':priv_edit_client' => $priv_edit_client,
                    ':lk' => $ldap_key,
                    ':pb' => $push,
                    ':messages_title' => $mess_title,
                    ':uniq_id' => $hn,
                    ':api_key' => md5($hn) ,
                    ':posada' => $posada,
                    ':tel' => $tel,
                    ':skype' => $skype,
                    ':unit_desc' => $pidrozdil,
                    ':adr' => $adr,
                    ':is_client' => $is_client,
                    ':msg_type' => $msg_type,
                    ':def_unit_id' => $user_2_unit,
                    ':def_user_id' => $user_2_user
                ));



$flist=$_POST['files'];
$flist=explode(",", $flist);

//print_r($flist);

if (!empty($flist)) {
    foreach ($flist as $value) {
        # code...
                    $stmt = $dbConnection->prepare('update user_files set user_id=:user_id, obj_type=1 where file_hash=:file_hash');

            $stmt->execute(array(
                ':user_id' => get_user_val_by_hash($hn,'id'),
                ':file_hash' => $value
            ));
    }
}


/*

//get_user_val_by_hash($hn,'id')
//get_user_val_by_hash($hn,'id')
if ($pidrozdil != "NULL") {

if ($main_unit_user == "true") {

    $stmt = $dbConnection->prepare('update units set main_user=:user_id where id=:pidrozdil');

            $stmt->execute(array(
                ':user_id' => get_user_val_by_hash($hn, 'id'),
                ':pidrozdil' => $pidrozdil
            ));
        }



else if ($main_unit_user == "false") {
                    $stmt2r1 = $dbConnection->prepare('SELECT main_user from units where id=:uto');
                $stmt2r1->execute(array(
                    ':uto' => $pidrozdil
                ));
                $tt2r = $stmt2r1->fetch(PDO::FETCH_ASSOC);
                if ($tt2r['main_user'] == get_user_val_by_hash($hn, 'id')) {
                        $stmt = $dbConnection->prepare('update units set main_user=Null where id=:pidrozdil');

            $stmt->execute(array(
                ':pidrozdil' => $pidrozdil
            ));
                }
}


}
*/


                
                //########################## ADDITIONAL FIELDS ###############################
                
                $stmt = $dbConnection->prepare('SELECT * FROM user_fields where status=:n');
                $stmt->execute(array(
                    ':n' => '1'
                ));
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $row) {
                    
                    $cur_hash = $row['hash'];
                    
                    if ($_POST[$cur_hash]) {
                        
                        //insert
                        
                        $v_field = $_POST[$cur_hash];
                        if ($row['t_type'] == "multiselect") {
                            
                            // code...
                            $v_field = implode(",", $_POST[$cur_hash]);
                        }
                        
                        $stmt = $dbConnection->prepare('insert into user_data (user_id,field_id,field_val, field_name) VALUES (:user_id,:field_id,:field_val,:field_name)');
                        $stmt->execute(array(
                            ':user_id' => get_user_val_by_hash($hn, 'id') ,
                            ':field_id' => $row['id'],
                            ':field_val' => $v_field,
                            ':field_name' => $row['name']
                        ));
                    }
                }
                
                //########################## ADDITIONAL FIELDS ###############################
                
                $stmt2 = $dbConnection->prepare('insert into users_notify (user_id,mail,pb,sms) values (:user_id,:mail,:pb,:sms)');
                $stmt2->execute(array(
                    ':user_id' => get_user_val_by_hash($hn, 'id') ,
                    ':mail' => $mail_nf,
                    ':pb' => '',
                    ':sms' => ''
                ));
            }
            
            if ($mode == "del_ticket") {
                
                $t_hash = $_POST['t_hash'];
                $t_id = get_ticket_id_by_hash($t_hash);
                
                if (validate_admin($_SESSION['helpdesk_user_id'])) {
                    
                    //tickets,comments,files,news,ticket_info,ticket_log
                    /*
                    $stmt = $dbConnection->prepare('delete from notes where hashname=:noteid');
                    $stmt->execute(array(
                    ':noteid' => $noteid
                    ));
                    */
                    
                    $stmt = $dbConnection->prepare('delete from tickets where hash_name=:id');
                    $stmt->execute(array(
                        ':id' => $t_hash
                    ));
                    
                    $stmt = $dbConnection->prepare('delete from comments where t_id=:id');
                    $stmt->execute(array(
                        ':id' => $t_id
                    ));
                    
                    $stmt = $dbConnection->prepare('delete from ticket_data where ticket_hash=:id');
                    $stmt->execute(array(
                        ':id' => $t_hash
                    ));
                    
                    //delete files
                    $stmt = $dbConnection->prepare("SELECT *
                            from files where ticket_hash=:id");
                    $stmt->execute(array(
                        ':id' => $t_hash
                    ));
                    $result = $stmt->fetchAll();
                    
                    if (!empty($result)) {
                        foreach ($result as $row) {
                            
                            unlink(ZENLIX_DIR . "/upload_files/" . $row['file_hash'] . "." . $row['file_ext']);
                        }
                    }
                    $stmt = $dbConnection->prepare('delete from files where ticket_hash=:id');
                    $stmt->execute(array(
                        ':id' => $t_hash
                    ));
                    
                    $stmt = $dbConnection->prepare('delete from news where ticket_id=:id');
                    $stmt->execute(array(
                        ':id' => $t_id
                    ));
                    
                    $stmt = $dbConnection->prepare('delete from ticket_info where ticket_id=:id');
                    $stmt->execute(array(
                        ':id' => $t_id
                    ));
                    
                    $stmt = $dbConnection->prepare('delete from ticket_log where ticket_id=:id');
                    $stmt->execute(array(
                        ':id' => $t_id
                    ));
                }
            }
            
            //end admin priv
            
            
        }
        
        if ($mode == "get_list_notes") {
            $userid = $_SESSION['helpdesk_user_id'];
            
            $stmt = $dbConnection->prepare('SELECT id, hashname, message from notes where user_id=:userid order by dt DESC');
            $stmt->execute(array(
                ':userid' => $userid
            ));
            $res = $stmt->fetchAll();
?>
            
            
            
            
            
            
            <div class="box">

                                <div class="box-body no-padding">
                                    
                                    

            
            <ul class="nav nav-pills nav-stacked" id="table_list">
                
           
                                               
            
            
            
            
            
            
            <!--table class="table table-hover" style="margin-bottom: 0px; margin-bottom: 0px;" id="table_list"-->


            <?php
            if (empty($res)) {
                echo lang('empty');
            } 
            else if (!empty($res)) {
                
                foreach ($res as $row) {
                    
                    $t_msg = cutstr_ret(strip_tags($row['message']));
                    
                    if (strlen($t_msg) < 2) {
                        $t_msg = "<em>" . lang('NOTES_single') . "</em>";
                    }
?>
                    
                    <li class="tr_<?php
                    echo $row['id']; ?>">
<a style=" cursor: pointer; " id="to_notes" value="<?php
                    echo $row['hashname']; ?>"><?php
                    echo $t_msg; ?>

<span class="badge pull-right bg-red" id="del_notes" value="<?php
                    echo $row['hashname']; ?>">
<i class="glyphicon glyphicon-trash"></i></span>

</a>


                    </li>
                    
                    
                    
                    

                    
                    
                    
                    
                    
                <?php
                }
?><!--/table-->
            </ul>                                </div><!-- /.box-body -->
                            </div><?php
            }
        }
        
        if ($mode == "check_login") {
            
            $l = $_POST['login'];
            
            if ($_POST['exclude']) {
                
                $t = $_POST['exclude'];
                if (validate_exist_login_ex($l, $t) == true) {
                    $r['check_login_status'] = true;
                } 
                else if (validate_exist_login_ex($l, $t) == false) {
                    $r['check_login_status'] = false;
                }
            } 
            else if (!$_POST['exclude']) {
                if (validate_exist_login($l) == true) {
                    $r['check_login_status'] = true;
                } 
                else if (validate_exist_login($l) == false) {
                    $r['check_login_status'] = false;
                }
            }
            
            $row_set[] = $r;
            echo json_encode($row_set);
        }
        
        if ($mode == "save_notes") {
            $noteid = ($_POST['hn']);
            $message = ($_POST['msg']);
            $message = str_replace("\r\n", "\n", $message);
            $message = str_replace("\r", "\n", $message);
            $message = str_replace("&nbsp;", " ", $message);
            
            $stmt = $dbConnection->prepare('update notes set message=:message, dt=:n where hashname=:noteid');
            $stmt->execute(array(
                ':message' => $message,
                ':noteid' => $noteid,
                ':n' => $CONF['now_dt']
            ));
            
            print_r($_POST['msg']);
        }
        
        if ($mode == "get_first_note") {
            $noteid = ($_POST['hn']);
            $uid = $_SESSION['helpdesk_user_id'];
            
            $stmt = $dbConnection->prepare('select hashname, message from notes where user_id=:uid order by dt DESC limit 1');
            $stmt->execute(array(
                ':uid' => $uid
            ));
            
            $res = $stmt->fetchAll();
            
            if (empty($res)) {
                echo "no";
            } 
            else if (!empty($res)) {
                
                foreach ($res as $row) {
                    echo $row['message'];
                }
            }
        }
        
        if ($mode == "attach_file_comment") {
            
            $flag = false;
            $output_dir = ZENLIX_DIR."/upload_files/";
            $fhash = randomhash();
            $user_comment = $_SESSION['helpdesk_user_id'];
            $th = $_POST['tid'];
            $tid_comment = get_ticket_id_by_hash($_POST['tid']);
            $ms = 30097152;
            
            $fileName = $_FILES["file"]["name"];
            $filetype = $_FILES["file"]["type"];
            $filesize = $_FILES["file"]["size"];
            
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileName_norm = $fhash . "." . $ext;
            
            if ($_FILES["file"]["size"] > $ms) {
                $flag = true;
            }
            
            if ($flag == false) {
                
                move_uploaded_file($_FILES["file"]["tmp_name"], $output_dir . $fileName_norm);
                
                $stmt = $dbConnection->prepare('insert into files 
        (ticket_hash, original_name, file_hash, file_type, file_size, file_ext) values 
        (:ticket_hash, :original_name, :file_hash, :file_type, :file_size, :file_ext)');
                $stmt->execute(array(
                    ':ticket_hash' => $th,
                    ':original_name' => $fileName,
                    ':file_hash' => $fhash,
                    ':file_type' => $filetype,
                    ':file_size' => $filesize,
                    ':file_ext' => $ext
                ));
                
                ///comment
                $stmt = $dbConnection->prepare('INSERT INTO comments (t_id, user_id, comment_text, dt)
                                            values (:tid_comment, :user_comment, :text_comment, :n)');
                $stmt->execute(array(
                    ':tid_comment' => $tid_comment,
                    ':user_comment' => $user_comment,
                    ':text_comment' => '[file:' . $fhash . ']',
                    ':n' => $CONF['now_dt']
                ));
                
                ///comment end
                
                ///add log////
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:comment, :n, :user_comment, :tid_comment)');
                $stmt->execute(array(
                    ':tid_comment' => $tid_comment,
                    ':user_comment' => $user_comment,
                    ':comment' => 'comment',
                    ':n' => $CONF['now_dt']
                ));
                
                ////add log end///
                
                send_notification('ticket_comment', $tid_comment);
                
                $stmt = $dbConnection->prepare('update tickets set last_update=:n where id=:tid_comment');
                $stmt->execute(array(
                    ':tid_comment' => $tid_comment,
                    ':n' => $CONF['now_dt']
                ));
                view_comment($tid_comment);
            } 
            else if ($flag == true) {
                view_comment($tid_comment);
?>
            <div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <?php
                echo lang('upload_errorsize'); ?>
            </div>
                                    
                                    
           
            <?php
            }
        }
        
        if ($mode == "get_sla_period_stat") {
            $start = $_POST['start'] . " 00:00:00";
            $end = $_POST['end'] . " 23:59:00";
            $unit = $_POST['unit'];
            
            if ($unit != "0") {
?>

<div class="box box-solid">
 <div class="box-header">

<h4 class="">
<center>
<?php
                echo get_unit_name($unit); ?></center></h4>
<h5><center>
    <?php
                echo lang('STATS_by_unit'); ?> <br> <time id="c" datetime="<?php
                echo $start
?>"></time> - <time id="c" datetime="<?php
                echo $end
?>"></time>
</center>

</h5><hr>
</div>
<div class="box-body">








<center><h4><?php
                echo lang('STAT_MAIN_t1'); ?></h4></center>
<table class="table table-bordered">
<tbody>

                                <tr>
                    <td style=""><strong><small><center><?php
                echo lang('STAT_MAIN_num'); ?></center></small></strong></td>
                    <td style=""><strong><small><center>#</center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('NEW_subj'); ?></center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_create'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_init'); ?>       </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_to'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('USERS_p_4'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_status'); ?>  </center></small></strong></td>
                    
                </tr>


<?php
                $stmt = $dbConnection->prepare('SELECT fio as label, id as value, unit FROM users where id !=:system and is_client=0 order by fio ASC');
                $stmt->execute(array(
                    ':system' => '1'
                ));
                $res1 = $stmt->fetchAll();
                $i = 1;
                foreach ($res1 as $row) {
                    
                    $ec = explode(",", $row['unit']);
                    
                    if (in_array($unit, $ec)) {
                        
                        //$row['value']; //ID
                        //
                        $usr_id = $row['value'];
                        $stmt = $dbConnection->prepare('SELECT date_op, msg, init_user_id, to_user_id, to_unit_id, ticket_id from ticket_log where init_user_id=:iud and date_op between :start AND :end AND msg=:msg order by date_op ASC');
                        $stmt->execute(array(
                            ':iud' => $usr_id,
                            ':start' => $start,
                            ':end' => $end,
                            ':msg' => 'create'
                        ));
                        $re = $stmt->fetchAll();
                        
                        if (!empty($re)) {
                            
                            foreach ($re as $row) {
                                
                                //$row['id'];
                                ////////////////////////////Показывает кому/////////////////////////////////////////////////////////////////
                                if ($row['to_user_id'] <> 0) {
                                    $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['to_user_id'])) . "</div>";
                                }
                                if ($row['to_user_id'] == 0) {
                                    $to_text = view_array(get_unit_name_return($row['to_unit_id']));
                                }
                                
                                ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                
                                ////////////////////////////Показывает labels//////////////////////////////////////////////////////////////
                                $t_status = get_ticket_val_by_hash('status', get_ticket_hash_by_id($row['ticket_id']));
                                $t_ob = get_ticket_val_by_hash('ok_by', get_ticket_hash_by_id($row['ticket_id']));
                                $t_dc = get_ticket_val_by_hash('date_create', get_ticket_hash_by_id($row['ticket_id']));
                                $t_lb = get_ticket_val_by_hash('lock_by', get_ticket_hash_by_id($row['ticket_id']));
                                if ($t_status == 1) {
                                    $st = "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> " . lang('t_list_a_oko') . " " . nameshort(name_of_user_ret_nolink($t_ob)) . "</span>";
                                    $t_ago = get_date_ok($t_dc, $row['ticket_id']);
                                }
                                if ($t_status == 0) {
                                    $t_ago = $t_dc;
                                    if ($t_lb <> 0) {
                                        
                                        $st = "<span class=\"label label-default\"><i class=\"fa fa-gavel\"></i> " . lang('t_list_a_lock_u') . " " . nameshort(name_of_user_ret_nolink($t_lb)) . "</span>";
                                    }
                                    if ($t_lb == 0) {
                                        $st = "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> " . lang('t_list_a_hold') . "</span>";
                                    }
                                }
                                
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                
                                
?>

                                <tr>
                    <td style=""><small><center><?php
                                echo $i; ?></center></small></td>
                    <td style=""><small><center><?php
                                echo $row['ticket_id']; ?> </center>  </small></td>
                    <td style=""><small><a href="ticket?<?php
                                echo get_ticket_hash_by_id($row['ticket_id']); ?>">
                        <?php
                                echo str_replace('"', "", make_html(strip_tags(get_ticket_val_by_hash('subj', get_ticket_hash_by_id($row['ticket_id']))) , 'no')); ?>
                    </a>
                    </small></td>
                    <td style=""><small><time id="c" datetime="<?php
                                echo $row['date_op'] ?>"></time>   </small></td>
                    <td style=""><small><?php
                                echo name_of_user_ret($row['init_user_id']); ?>       </small></td>
                    <td style=""><small><?php
                                echo $to_text; ?> </small></td>
                    <td style=""><small><?php
                                echo nameshort(name_of_user_ret(get_ticket_val_by_hash('client_id', get_ticket_hash_by_id($row['ticket_id'])))); ?> </small></td>
                    <td style=""><small><?php
                                echo $st; ?>  </small></td>
                    
                </tr>


<?php
                                
                                //echo $row['date_op'] . "<br>";
                                $i++;
                            }
                        }
                    }
                }
?>
</tbody>
</table>
<br>
<br>




<center><h4><?php
                echo lang('STAT_MAIN_t4'); ?></h4></center>
<table class="table table-bordered">
<tbody>

                <tr>
                    <td style=""><strong><small><center><?php
                echo lang('STAT_MAIN_num'); ?></center></small></strong></td>
                    <td style=""><strong><small><center>#</center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('NEW_subj'); ?></center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_create'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_init'); ?>       </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_to'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('USERS_p_4'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_status'); ?>  </center></small></strong></td>
                    
                </tr>


<?php
                $ar_u = array();
                $stmt = $dbConnection->prepare('SELECT fio as label, id as value, unit FROM users where id !=:system and is_client=0 order by fio ASC');
                $stmt->execute(array(
                    ':system' => '1'
                ));
                $res1 = $stmt->fetchAll();
                $i = 1;
                foreach ($res1 as $row) {
                    
                    $ec = explode(",", $row['unit']);
                    
                    if (in_array($unit, $ec)) {
                        
                        //$row['value']; //ID
                        //
                        $usr_id = $row['value'];
                        
                        $stmt = $dbConnection->prepare('SELECT date_op, msg, init_user_id, to_user_id, to_unit_id, ticket_id from ticket_log where ((find_in_set(:user_id,to_user_id))
                            ) and date_op between :start AND :end AND msg=:msg order by date_op ASC');
                        $stmt->execute(array(
                            ':user_id' => $usr_id,
                            ':start' => $start,
                            ':end' => $end,
                            ':msg' => 'create'
                        ));
                        $re = $stmt->fetchAll();
                        $ar_u = array_merge($ar_u, $re);
                    }
                }
                
                $stmt2 = $dbConnection->prepare('SELECT date_op, msg, init_user_id, to_user_id, to_unit_id, ticket_id from ticket_log where to_unit_id=:unit_id AND to_user_id=:u
                             and date_op between :start AND :end AND msg=:msg order by date_op ASC');
                $stmt2->execute(array(
                    ':unit_id' => $unit,
                    ':u' => '0',
                    ':start' => $start,
                    ':end' => $end,
                    ':msg' => 'create'
                ));
                $re2 = $stmt2->fetchAll();
                
                $re_s = array_merge($re2, $ar_u);
                
                if (!empty($re_s)) {
                    
                    foreach ($re_s as $row) {
                        
                        //$row['id'];
                        ////////////////////////////Показывает кому/////////////////////////////////////////////////////////////////
                        if ($row['to_user_id'] <> 0) {
                            $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['to_user_id'])) . "</div>";
                        }
                        if ($row['to_user_id'] == 0) {
                            $to_text = view_array(get_unit_name_return($row['to_unit_id']));
                        }
                        
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        
                        ////////////////////////////Показывает labels//////////////////////////////////////////////////////////////
                        $t_status = get_ticket_val_by_hash('status', get_ticket_hash_by_id($row['ticket_id']));
                        $t_ob = get_ticket_val_by_hash('ok_by', get_ticket_hash_by_id($row['ticket_id']));
                        $t_dc = get_ticket_val_by_hash('date_create', get_ticket_hash_by_id($row['ticket_id']));
                        $t_lb = get_ticket_val_by_hash('lock_by', get_ticket_hash_by_id($row['ticket_id']));
                        if ($t_status == 1) {
                            $st = "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> " . lang('t_list_a_oko') . " " . nameshort(name_of_user_ret_nolink($t_ob)) . "</span>";
                            $t_ago = get_date_ok($t_dc, $row['ticket_id']);
                        }
                        if ($t_status == 0) {
                            $t_ago = $t_dc;
                            if ($t_lb <> 0) {
                                
                                $st = "<span class=\"label label-default\"><i class=\"fa fa-gavel\"></i> " . lang('t_list_a_lock_u') . " " . nameshort(name_of_user_ret_nolink($t_lb)) . "</span>";
                            }
                            if ($t_lb == 0) {
                                $st = "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> " . lang('t_list_a_hold') . "</span>";
                            }
                        }
                        
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        
                        
?>

                                <tr>
                    <td style=""><small><center><?php
                        echo $i; ?></center></small></td>
                    <td style=""><small><center><?php
                        echo $row['ticket_id']; ?> </center>  </small></td>
                    <td style=""><small><a href="ticket?<?php
                        echo get_ticket_hash_by_id($row['ticket_id']); ?>">
                        <?php
                        echo str_replace('"', "", make_html(strip_tags(get_ticket_val_by_hash('subj', get_ticket_hash_by_id($row['ticket_id']))) , 'no')); ?>
                    </a>
                    </small></td>
                    <td style=""><small><time id="c" datetime="<?php
                        echo $row['date_op'] ?>"></time>   </small></td>
                    <td style=""><small><?php
                        echo name_of_user_ret($row['init_user_id']); ?>       </small></td>
                    <td style=""><small><?php
                        echo $to_text; ?> </small></td>
                    <td style=""><small><?php
                        echo nameshort(name_of_user_ret(get_ticket_val_by_hash('client_id', get_ticket_hash_by_id($row['ticket_id'])))); ?> </small></td>
                    <td style=""><small><?php
                        echo $st; ?>  </small></td>
                    
                </tr>


<?php
                        
                        //echo $row['date_op'] . "<br>";
                        $i++;
                    }
                }
?>
</tbody>
</table>
<br>
<br>








<center><h4><?php
                echo lang('STAT_MAIN_t2'); ?></h4></center>
<table class="table table-bordered">
<tbody>
                                <tr>
                    <td style=""><strong><small><center><?php
                echo lang('STAT_MAIN_num'); ?></center></small></strong></td>
                    <td style=""><strong><small><center>#</center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('NEW_subj'); ?></center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_create'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_init'); ?>       </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_to'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('USERS_p_4'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_status'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('PERF_menu_sla'); ?>  </center></small></strong></td>
                    
                </tr>



<?php
                $res_react_ok = 0;
                $res_react_no = 0;
                $res_work_ok = 0;
                $res_work_no = 0;
                $res_dl_ok = 0;
                $res_dl_no = 0;
                
                $stmt = $dbConnection->prepare('SELECT fio as label, id as value, unit FROM users where id !=:system and is_client=0 order by fio ASC');
                $stmt->execute(array(
                    ':system' => '1'
                ));
                $res1 = $stmt->fetchAll();
                $i = 1;
                foreach ($res1 as $row) {
                    
                    $ec = explode(",", $row['unit']);
                    
                    if (in_array($unit, $ec)) {
                        
                        //$row['value']; //ID
                        //
                        $usr_id = $row['value'];
                        $stmt = $dbConnection->prepare('SELECT * from tickets where ok_by=:iud and ok_date between :start AND :end  order by ok_date ASC');
                        $stmt->execute(array(
                            ':iud' => $usr_id,
                            ':start' => $start,
                            ':end' => $end
                        ));
                        $re = $stmt->fetchAll();
                        
                        if (!empty($re)) {
                            
                            foreach ($re as $row) {
                                
                                //$row['id'];
                                ////////////////////////////Показывает кому/////////////////////////////////////////////////////////////////
                                if ($row['user_to_id'] <> 0) {
                                    $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['user_to_id'])) . "</div>";
                                }
                                if ($row['user_to_id'] == 0) {
                                    $to_text = view_array(get_unit_name_return($row['unit_id']));
                                }
                                
                                ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                
                                ////////////////////////////Показывает labels//////////////////////////////////////////////////////////////
                                $t_status = get_ticket_val_by_hash('status', get_ticket_hash_by_id($row['id']));
                                $t_ob = get_ticket_val_by_hash('ok_by', get_ticket_hash_by_id($row['id']));
                                $t_dc = get_ticket_val_by_hash('date_create', get_ticket_hash_by_id($row['id']));
                                $t_lb = get_ticket_val_by_hash('lock_by', get_ticket_hash_by_id($row['id']));
                                if ($t_status == 1) {
                                    $st = "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> " . lang('t_list_a_oko') . " " . nameshort(name_of_user_ret_nolink($t_ob)) . "</span>";
                                    $t_ago = get_date_ok($t_dc, $row['id']);
                                }
                                if ($t_status == 0) {
                                    $t_ago = $t_dc;
                                    if ($t_lb <> 0) {
                                        
                                        $st = "<span class=\"label label-default\"><i class=\"fa fa-gavel\"></i> " . lang('t_list_a_lock_u') . " " . nameshort(name_of_user_ret_nolink($t_lb)) . "</span>";
                                    }
                                    if ($t_lb == 0) {
                                        $st = "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> " . lang('t_list_a_hold') . "</span>";
                                    }
                                }
                                
                                $sla = get_ticket_sla_status($row['id']);
                                
                                if ($sla['status'] == "true") {
                                    
                                    if ($sla['react'] == 0) {
                                        $sla['react'] = "<span class=\"label label-danger\">" . lang('SLA_time_old') . "</span>";
                                        $res_react_no++;
                                    } 
                                    else if (($sla['react'] > 0) && ($sla['react'] < 50)) {
                                        $sla['react'] = "<span class=\"label label-warning\">" . $sla['react'] . "% </span>";
                                        $res_react_ok++;
                                    } 
                                    else if (($sla['react'] >= 50)) {
                                        $sla['react'] = "<span class=\"label label-success\">" . $sla['react'] . "% </span>";
                                        $res_react_ok++;
                                    }
                                    
                                    if ($sla['work'] == 0) {
                                        $sla['work'] = "<span class=\"label label-danger\">" . lang('SLA_time_old') . "</span>";
                                        $res_work_no++;
                                    } 
                                    else if (($sla['work'] > 0) && ($sla['work'] < 50)) {
                                        $sla['work'] = "<span class=\"label label-warning\">" . $sla['work'] . "% </span>";
                                        $res_work_ok++;
                                    } 
                                    else if (($sla['work'] >= 50)) {
                                        $sla['work'] = "<span class=\"label label-success\">" . $sla['work'] . "% </span>";
                                        $res_work_ok++;
                                    }
                                    
                                    if ($sla['dl'] == 0) {
                                        $sla['dl'] = "<span class=\"label label-danger\">" . lang('SLA_time_old') . "</span>";
                                        $res_dl_no++;
                                    } 
                                    else if (($sla['dl'] > 0) && ($sla['dl'] < 50)) {
                                        $sla['dl'] = "<span class=\"label label-warning\">" . $sla['dl'] . "% </span>";
                                        $res_dl_ok++;
                                    } 
                                    else if (($sla['dl'] >= 50)) {
                                        $sla['dl'] = "<span class=\"label label-success\">" . $sla['dl'] . "% </span>";
                                        $res_dl_ok++;
                                    }
                                    
                                    $sla_str = lang('SLA_perf_reaction') . ": " . $sla['react'] . "<br>";
                                    $sla_str.= lang('SLA_perf_work_a') . ": " . $sla['work'] . "<br>";
                                    $sla_str.= lang('SLA_perf_deadline_short') . ": " . $sla['dl'] . "<br>";
                                } 
                                else if ($sla['status'] == "false") {
                                    $sla_str = lang('SLA_not_sel');
                                }
                                
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                
                                
?>

                                <tr>
                    <td style=""><small><center><?php
                                echo $i; ?></center></small></td>
                    <td style=""><small><center><?php
                                echo $row['id']; ?> </center>  </small></td>
                    <td style=""><small><a href="ticket?<?php
                                echo get_ticket_hash_by_id($row['id']); ?>">
                        <?php
                                echo str_replace('"', "", make_html(strip_tags(get_ticket_val_by_hash('subj', get_ticket_hash_by_id($row['id']))) , 'no')); ?>
                    </a>
                    </small></td>
                    <td style=""><small><time id="c" datetime="<?php
                                echo $t_dc
?>"></time>   </small></td>
                    <td style=""><small><?php
                                echo name_of_user_ret($row['user_init_id']); ?>       </small></td>
                    <td style=""><small><?php
                                echo $to_text; ?> </small></td>
                    <td style=""><small><?php
                                echo nameshort(name_of_user_ret(get_ticket_val_by_hash('client_id', get_ticket_hash_by_id($row['id'])))); ?> </small></td>
                    <td style=""><small><?php
                                echo $st; ?>  </small></td>
                    <td style=""><small><?php
                                echo $sla_str; ?> </small></td>
                </tr>


<?php
                                
                                //echo $row['date_op'] . "<br>";
                                $i++;
                            }
                        }
                    }
                }
?>
</tbody>
</table>

 
<br>
<center><h4><?php
                echo lang('SLA_stat_res_by_ok'); ?></h4></center>

<table class="table table-bordered table-hover">
<thead>
 <tr>
 <td><strong><small><center> <?php
                echo lang('SLA_stat_name'); ?></center></small></strong></td> 
 <td><strong><small><center> <?php
                echo lang('SLA_stat_count'); ?> </center></small></strong></td>
 </tr>
 </thead>
 <tbody>
  <tr class="text-success">
 <td><strong><small><?php
                echo lang('SLA_stat_react_ok'); ?></small></strong></td> <td><strong><small><center><?php
                echo $res_react_ok; ?></center></small></strong> </td>
 </tr>
   <tr class="text-danger">
 <td><strong><small><?php
                echo lang('SLA_stat_react_no'); ?></small></strong></td> <td><strong><small><center><?php
                echo $res_react_no; ?></center></small></strong> </td>
 </tr>

    <tr class="text-success">
 <td><strong><small><?php
                echo lang('SLA_stat_work_ok'); ?></small></strong></td> <td><strong><small><center><?php
                echo $res_work_ok; ?></center></small></strong> </td>
 </tr>

    <tr class="text-danger">
 <td><strong><small><?php
                echo lang('SLA_stat_work_no'); ?></small></strong></td> <td><strong><small><center><?php
                echo $res_work_no; ?></center></small></strong> </td>
 </tr>

     <tr class="text-success">
 <td><strong><small><?php
                echo lang('SLA_stat_dl_ok'); ?></small></strong></td> <td><strong><small><center><?php
                echo $res_dl_ok; ?></center></small></strong> </td>
 </tr>

    <tr class="text-danger">
 <td><strong><small><?php
                echo lang('SLA_stat_dl_no'); ?></small></strong></td> <td><strong><small><center><?php
                echo $res_dl_no; ?></center></small></strong> </td>
 </tr>
 </tbody>
</table>












<br><br>
<center><h4><?php
                echo lang('STAT_MAIN_t3'); ?></h4></center>
<table class="table table-bordered">

                                <tr>
                    <td style=""><strong><small><center><?php
                echo lang('STAT_MAIN_num'); ?></center></small></strong></td>
                    <td style=""><strong><small><center>#</center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('NEW_subj'); ?></center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_create'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_init'); ?>       </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_to'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('USERS_p_4'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_status'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('PERF_menu_sla'); ?>  </center></small></strong></td>
                    
                </tr>




<?php
                $res_react_ok1 = 0;
                $res_react_no1 = 0;
                $res_work_ok1 = 0;
                $res_work_no1 = 0;
                $res_dl_ok1 = 0;
                $res_dl_no1 = 0;
                
                $stmt = $dbConnection->prepare('SELECT id, ok_by, ok_date, user_to_id, user_init_id, unit_id,last_update from tickets where status=:st and unit_id=:iud and last_update between :start AND :end  order by last_update ASC');
                $stmt->execute(array(
                    ':iud' => $unit,
                    ':start' => $start,
                    ':end' => $end,
                    ':st' => '0'
                ));
                
                $res1 = $stmt->fetchAll();
                
                $i = 1;
                
                if (!empty($res1)) {
                    
                    foreach ($res1 as $row) {
                        
                        //$row['id'];
                        ////////////////////////////Показывает кому/////////////////////////////////////////////////////////////////
                        if ($row['user_to_id'] <> 0) {
                            $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['user_to_id'])) . "</div>";
                        }
                        if ($row['user_to_id'] == 0) {
                            $to_text = view_array(get_unit_name_return($row['unit_id']));
                        }
                        
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        
                        ////////////////////////////Показывает labels//////////////////////////////////////////////////////////////
                        $t_status = get_ticket_val_by_hash('status', get_ticket_hash_by_id($row['id']));
                        $t_ob = get_ticket_val_by_hash('last_update', get_ticket_hash_by_id($row['id']));
                        $t_dc = get_ticket_val_by_hash('date_create', get_ticket_hash_by_id($row['id']));
                        $t_lb = get_ticket_val_by_hash('lock_by', get_ticket_hash_by_id($row['id']));
                        if ($t_status == 1) {
                            $st = "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> " . lang('t_list_a_oko') . " " . nameshort(name_of_user_ret_nolink($t_ob)) . "</span>";
                            $t_ago = get_date_ok($t_dc, $row['id']);
                        }
                        if ($t_status == 0) {
                            $t_ago = $t_dc;
                            if ($t_lb <> 0) {
                                
                                $st = "<span class=\"label label-default\"><i class=\"fa fa-gavel\"></i> " . lang('t_list_a_lock_u') . " " . nameshort(name_of_user_ret_nolink($t_lb)) . "</span>";
                            }
                            if ($t_lb == 0) {
                                $st = "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> " . lang('t_list_a_hold') . "</span>";
                            }
                        }
                        
                        $sla = get_ticket_sla_status_nook($row['id']);
                        
                        if ($sla['status'] == "true") {
                            
                            if ($sla['react'] == 0) {
                                $sla['react'] = "<span class=\"label label-danger\">" . lang('SLA_time_old') . "</span>";
                                $res_react_no1++;
                            } 
                            else if (($sla['react'] > 0) && ($sla['react'] < 50)) {
                                $sla['react'] = "<span class=\"label label-warning\">" . $sla['react'] . "% </span>";
                                $res_react_ok1++;
                            } 
                            else if (($sla['react'] >= 50)) {
                                $sla['react'] = "<span class=\"label label-success\">" . $sla['react'] . "% </span>";
                                $res_react_ok1++;
                            }
                            
                            if ($sla['work'] == 0) {
                                $sla['work'] = "<span class=\"label label-danger\">" . lang('SLA_time_old') . "</span>";
                                $res_work_no1++;
                            } 
                            else if (($sla['work'] > 0) && ($sla['work'] < 50)) {
                                $sla['work'] = "<span class=\"label label-warning\">" . $sla['work'] . "% </span>";
                                $res_work_ok1++;
                            } 
                            else if (($sla['work'] >= 50)) {
                                $sla['work'] = "<span class=\"label label-success\">" . $sla['work'] . "% </span>";
                                $res_work_ok1++;
                            }
                            
                            if ($sla['dl'] == 0) {
                                $sla['dl'] = "<span class=\"label label-danger\">" . lang('SLA_time_old') . "</span>";
                                $res_dl_no1++;
                            } 
                            else if (($sla['dl'] > 0) && ($sla['dl'] < 50)) {
                                $sla['dl'] = "<span class=\"label label-warning\">" . $sla['dl'] . "% </span>";
                                $res_dl_ok1++;
                            } 
                            else if (($sla['dl'] >= 50)) {
                                $sla['dl'] = "<span class=\"label label-success\">" . $sla['dl'] . "% </span>";
                                $res_dl_ok1++;
                            }
                            
                            $sla_str = lang('SLA_perf_reaction') . ": " . $sla['react'] . "<br>";
                            $sla_str.= lang('SLA_perf_work_a') . ": " . $sla['work'] . "<br>";
                            $sla_str.= lang('SLA_perf_deadline_short') . ": " . $sla['dl'] . "<br>";
                        } 
                        else if ($sla['status'] == "false") {
                            $sla_str = lang('SLA_not_sel');
                        }
                        
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        
                        
?>

                                <tr>
                    <td style=""><small><center><?php
                        echo $i; ?></center></small></td>
                    <td style=""><small><center><?php
                        echo $row['id']; ?> </center>  </small></td>
                    <td style=""><small><a href="ticket?<?php
                        echo get_ticket_hash_by_id($row['id']); ?>">
                        <?php
                        echo str_replace('"', "", make_html(strip_tags(get_ticket_val_by_hash('subj', get_ticket_hash_by_id($row['id']))) , 'no')); ?>
                    </a>
                    </small></td>
                    <td style=""><small><time id="c" datetime="<?php
                        echo $row['last_update'] ?>"></time>   </small></td>
                    <td style=""><small><?php
                        echo name_of_user_ret($row['user_init_id']); ?>       </small></td>
                    <td style=""><small><?php
                        echo $to_text; ?> </small></td>
                    <td style=""><small><?php
                        echo nameshort(name_of_user_ret(get_ticket_val_by_hash('client_id', get_ticket_hash_by_id($row['id'])))); ?> </small></td>
                    <td style=""><small><?php
                        echo $st; ?>  </small></td>
                    <td style=""><small><?php
                        echo $sla_str; ?> </small></td>
                    
                </tr>


<?php
                        
                        //echo $row['date_op'] . "<br>";
                        $i++;
                    }
                }
?>
</tbody>
</table>

<br>
<center><h4><?php
                echo lang('SLA_stat_res_by_nook'); ?></h4></center>

<table class="table table-bordered table-hover">
<thead>
 <tr>
 <td><strong><small><center> <?php
                echo lang('SLA_stat_name'); ?></center></small></strong></td> 
 <td><strong><small><center> <?php
                echo lang('SLA_stat_count'); ?> </center></small></strong></td>
 </tr>
 </thead>
 <tbody>
  <tr class="text-success">
 <td><strong><small><?php
                echo lang('SLA_stat_react_ok'); ?></small></strong></td> <td><strong><small><center><?php
                echo $res_react_ok1; ?></center></small></strong> </td>
 </tr>
   <tr class="text-danger">
 <td><strong><small><?php
                echo lang('SLA_stat_react_no'); ?></small></strong></td> <td><strong><small><center><?php
                echo $res_react_no1; ?></center></small></strong> </td>
 </tr>

    <tr class="text-success">
 <td><strong><small><?php
                echo lang('SLA_stat_work_ok'); ?></small></strong></td> <td><strong><small><center><?php
                echo $res_work_ok1; ?></center></small></strong> </td>
 </tr>

    <tr class="text-danger">
 <td><strong><small><?php
                echo lang('SLA_stat_work_no'); ?></small></strong></td> <td><strong><small><center><?php
                echo $res_work_no1; ?></center></small></strong> </td>
 </tr>

     <tr class="text-success">
 <td><strong><small><?php
                echo lang('SLA_stat_dl_ok'); ?></small></strong></td> <td><strong><small><center><?php
                echo $res_dl_ok1; ?></center></small></strong> </td>
 </tr>

    <tr class="text-danger">
 <td><strong><small><?php
                echo lang('SLA_stat_dl_no'); ?></small></strong></td> <td><strong><small><center><?php
                echo $res_dl_no1; ?></center></small></strong> </td>
 </tr>
 </tbody>
</table>


</div>

</div>

    <?php
            } 
            else if ($unit == "0") {
            }
        }
        
        if ($mode == "get_total_period_stat") {
            $start = $_POST['start'] . " 00:00:00";
            $end = $_POST['end'] . " 23:59:00";
            $unit = $_POST['unit'];
            
            if ($unit != "0") {
?>
<div class="box box-solid">
 <div class="box-header">
<h4 class="box-title">
<center>
    <?php
                echo lang('STATS_by_unit'); ?> <time id="c" datetime="<?php
                echo $start
?>"></time> - <time id="c" datetime="<?php
                echo $end
?>"></time><br>
<?php
                echo get_unit_name($unit); ?>
</center>

</h4><br>
</div>
<div class="box-body">
<center><h4><?php
                echo lang('STAT_MAIN_t1'); ?></h4></center>
<table class="table table-bordered">
<tbody>

                                <tr>
                    <td style=""><strong><small><center><?php
                echo lang('STAT_MAIN_num'); ?></center></small></strong></td>
                    <td style=""><strong><small><center>#</center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('NEW_subj'); ?></center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_create'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_init'); ?>       </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_to'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('USERS_p_4'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_status'); ?>  </center></small></strong></td>
                    
                </tr>




<?php
                $stmt = $dbConnection->prepare('SELECT fio as label, id as value, unit FROM users where id !=:system and is_client=0 order by fio ASC');
                $stmt->execute(array(
                    ':system' => '1'
                ));
                $res1 = $stmt->fetchAll();
                $i = 1;
                foreach ($res1 as $row) {
                    
                    $ec = explode(",", $row['unit']);
                    
                    if (in_array($unit, $ec)) {
                        
                        //$row['value']; //ID
                        //
                        $usr_id = $row['value'];
                        $stmt = $dbConnection->prepare('SELECT date_op, msg, init_user_id, to_user_id, to_unit_id, ticket_id from ticket_log where init_user_id=:iud and date_op between :start AND :end AND msg=:msg order by date_op ASC');
                        $stmt->execute(array(
                            ':iud' => $usr_id,
                            ':start' => $start,
                            ':end' => $end,
                            ':msg' => 'create'
                        ));
                        $re = $stmt->fetchAll();
                        
                        if (!empty($re)) {
                            
                            foreach ($re as $row) {
                                
                                //$row['id'];
                                ////////////////////////////Показывает кому/////////////////////////////////////////////////////////////////
                                if ($row['to_user_id'] <> 0) {
                                    $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['to_user_id'])) . "</div>";
                                }
                                if ($row['to_user_id'] == 0) {
                                    $to_text = view_array(get_unit_name_return($row['to_unit_id']));
                                }
                                
                                ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                
                                ////////////////////////////Показывает labels//////////////////////////////////////////////////////////////
                                $t_status = get_ticket_val_by_hash('status', get_ticket_hash_by_id($row['ticket_id']));
                                $t_ob = get_ticket_val_by_hash('ok_by', get_ticket_hash_by_id($row['ticket_id']));
                                $t_dc = get_ticket_val_by_hash('date_create', get_ticket_hash_by_id($row['ticket_id']));
                                $t_lb = get_ticket_val_by_hash('lock_by', get_ticket_hash_by_id($row['ticket_id']));
                                if ($t_status == 1) {
                                    $st = "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> " . lang('t_list_a_oko') . " " . nameshort(name_of_user_ret_nolink($t_ob)) . "</span>";
                                    $t_ago = get_date_ok($t_dc, $row['ticket_id']);
                                }
                                if ($t_status == 0) {
                                    $t_ago = $t_dc;
                                    if ($t_lb <> 0) {
                                        
                                        $st = "<span class=\"label label-default\"><i class=\"fa fa-gavel\"></i> " . lang('t_list_a_lock_u') . " " . nameshort(name_of_user_ret_nolink($t_lb)) . "</span>";
                                    }
                                    if ($t_lb == 0) {
                                        $st = "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> " . lang('t_list_a_hold') . "</span>";
                                    }
                                }
                                
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                
                                
?>

                                <tr>
                    <td style=""><small><center><?php
                                echo $i; ?></center></small></td>
                    <td style=""><small><center><?php
                                echo $row['ticket_id']; ?> </center>  </small></td>
                    <td style=""><small><a href="ticket?<?php
                                echo get_ticket_hash_by_id($row['ticket_id']); ?>">
                        <?php
                                echo str_replace('"', "", make_html(strip_tags(get_ticket_val_by_hash('subj', get_ticket_hash_by_id($row['ticket_id']))) , 'no')); ?>
                    </a>
                    </small></td>
                    <td style=""><small><time id="c" datetime="<?php
                                echo $row['date_op'] ?>"></time>   </small></td>
                    <td style=""><small><?php
                                echo name_of_user_ret($row['init_user_id']); ?>       </small></td>
                    <td style=""><small><?php
                                echo $to_text; ?> </small></td>
                    <td style=""><small><?php
                                echo nameshort(name_of_user_ret(get_ticket_val_by_hash('client_id', get_ticket_hash_by_id($row['ticket_id'])))); ?> </small></td>
                    <td style=""><small><?php
                                echo $st; ?>  </small></td>
                    
                </tr>


<?php
                                
                                //echo $row['date_op'] . "<br>";
                                $i++;
                            }
                        }
                    }
                }
?>
</tbody>
</table>
<br>
<br>
<center><h4><?php
                echo lang('STAT_MAIN_t2'); ?></h4></center>
<table class="table table-bordered">
<tbody>
                                <tr>
                    <td style=""><strong><small><center><?php
                echo lang('STAT_MAIN_num'); ?></center></small></strong></td>
                    <td style=""><strong><small><center>#</center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('NEW_subj'); ?></center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_create'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_init'); ?>       </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_to'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('USERS_p_4'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_status'); ?>  </center></small></strong></td>
                    
                </tr>



<?php
                $stmt = $dbConnection->prepare('SELECT fio as label, id as value, unit FROM users where id !=:system and is_client=0 order by fio ASC');
                $stmt->execute(array(
                    ':system' => '1'
                ));
                $res1 = $stmt->fetchAll();
                $i = 1;
                foreach ($res1 as $row) {
                    
                    $ec = explode(",", $row['unit']);
                    
                    if (in_array($unit, $ec)) {
                        
                        //$row['value']; //ID
                        //
                        $usr_id = $row['value'];
                        $stmt = $dbConnection->prepare('SELECT * from tickets where ok_by=:iud and ok_date between :start AND :end  order by ok_date ASC');
                        $stmt->execute(array(
                            ':iud' => $usr_id,
                            ':start' => $start,
                            ':end' => $end
                        ));
                        $re = $stmt->fetchAll();
                        
                        if (!empty($re)) {
                            
                            foreach ($re as $row) {
                                
                                //$row['id'];
                                ////////////////////////////Показывает кому/////////////////////////////////////////////////////////////////
                                if ($row['user_to_id'] <> 0) {
                                    $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['user_to_id'])) . "</div>";
                                }
                                if ($row['user_to_id'] == 0) {
                                    $to_text = view_array(get_unit_name_return($row['unit_id']));
                                }
                                
                                ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                
                                ////////////////////////////Показывает labels//////////////////////////////////////////////////////////////
                                $t_status = get_ticket_val_by_hash('status', get_ticket_hash_by_id($row['id']));
                                $t_ob = get_ticket_val_by_hash('ok_by', get_ticket_hash_by_id($row['id']));
                                $t_dc = get_ticket_val_by_hash('date_create', get_ticket_hash_by_id($row['id']));
                                $t_lb = get_ticket_val_by_hash('lock_by', get_ticket_hash_by_id($row['id']));
                                if ($t_status == 1) {
                                    $st = "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> " . lang('t_list_a_oko') . " " . nameshort(name_of_user_ret_nolink($t_ob)) . "</span>";
                                    $t_ago = get_date_ok($t_dc, $row['id']);
                                }
                                if ($t_status == 0) {
                                    $t_ago = $t_dc;
                                    if ($t_lb <> 0) {
                                        
                                        $st = "<span class=\"label label-default\"><i class=\"fa fa-gavel\"></i> " . lang('t_list_a_lock_u') . " " . nameshort(name_of_user_ret_nolink($t_lb)) . "</span>";
                                    }
                                    if ($t_lb == 0) {
                                        $st = "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> " . lang('t_list_a_hold') . "</span>";
                                    }
                                }
                                
                                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                
                                
?>

                                <tr>
                    <td style=""><small><center><?php
                                echo $i; ?></center></small></td>
                    <td style=""><small><center><?php
                                echo $row['id']; ?> </center>  </small></td>
                    <td style=""><small><a href="ticket?<?php
                                echo get_ticket_hash_by_id($row['id']); ?>">
                        <?php
                                echo str_replace('"', "", make_html(strip_tags(get_ticket_val_by_hash('subj', get_ticket_hash_by_id($row['id']))) , 'no')); ?>
                    </a>
                    </small></td>
                    <td style=""><small><time id="c" datetime="<?php
                                echo $row['ok_date'] ?>"></time>   </small></td>
                    <td style=""><small><?php
                                echo name_of_user_ret($row['user_init_id']); ?>       </small></td>
                    <td style=""><small><?php
                                echo $to_text; ?> </small></td>
                    <td style=""><small><?php
                                echo nameshort(name_of_user_ret(get_ticket_val_by_hash('client_id', get_ticket_hash_by_id($row['id'])))); ?> </small></td>
                    <td style=""><small><?php
                                echo $st; ?>  </small></td>
                    
                </tr>


<?php
                                
                                //echo $row['date_op'] . "<br>";
                                $i++;
                            }
                        }
                    }
                }
?>
</tbody>
</table>

<br><br>
<center><h4><?php
                echo lang('STAT_MAIN_t3'); ?></h4></center>
<table class="table table-bordered">

                                <tr>
                    <td style=""><strong><small><center><?php
                echo lang('STAT_MAIN_num'); ?></center></small></strong></td>
                    <td style=""><strong><small><center>#</center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('NEW_subj'); ?></center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_create'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_init'); ?>       </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_to'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('USERS_p_4'); ?>  </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('t_LIST_status'); ?>  </center></small></strong></td>
                    
                </tr>




<?php
                $stmt = $dbConnection->prepare('SELECT id, ok_by, ok_date, user_to_id, user_init_id, unit_id,last_update from tickets where status=:st and unit_id=:iud and last_update between :start AND :end  order by last_update ASC');
                $stmt->execute(array(
                    ':iud' => $unit,
                    ':start' => $start,
                    ':end' => $end,
                    ':st' => '0'
                ));
                
                $res1 = $stmt->fetchAll();
                
                $i = 1;
                
                if (!empty($res1)) {
                    
                    foreach ($res1 as $row) {
                        
                        //$row['id'];
                        ////////////////////////////Показывает кому/////////////////////////////////////////////////////////////////
                        if ($row['user_to_id'] <> 0) {
                            $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['user_to_id'])) . "</div>";
                        }
                        if ($row['user_to_id'] == 0) {
                            $to_text = view_array(get_unit_name_return($row['unit_id']));
                        }
                        
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        
                        ////////////////////////////Показывает labels//////////////////////////////////////////////////////////////
                        $t_status = get_ticket_val_by_hash('status', get_ticket_hash_by_id($row['id']));
                        $t_ob = get_ticket_val_by_hash('last_update', get_ticket_hash_by_id($row['id']));
                        $t_dc = get_ticket_val_by_hash('date_create', get_ticket_hash_by_id($row['id']));
                        $t_lb = get_ticket_val_by_hash('lock_by', get_ticket_hash_by_id($row['id']));
                        if ($t_status == 1) {
                            $st = "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> " . lang('t_list_a_oko') . " " . nameshort(name_of_user_ret_nolink($t_ob)) . "</span>";
                            $t_ago = get_date_ok($t_dc, $row['id']);
                        }
                        if ($t_status == 0) {
                            $t_ago = $t_dc;
                            if ($t_lb <> 0) {
                                
                                $st = "<span class=\"label label-default\"><i class=\"fa fa-gavel\"></i> " . lang('t_list_a_lock_u') . " " . nameshort(name_of_user_ret_nolink($t_lb)) . "</span>";
                            }
                            if ($t_lb == 0) {
                                $st = "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> " . lang('t_list_a_hold') . "</span>";
                            }
                        }
                        
                        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        
                        
?>

                                <tr>
                    <td style=""><small><center><?php
                        echo $i; ?></center></small></td>
                    <td style=""><small><center><?php
                        echo $row['id']; ?> </center>  </small></td>
                    <td style=""><small><a href="ticket?<?php
                        echo get_ticket_hash_by_id($row['id']); ?>">
                        <?php
                        echo str_replace('"', "", make_html(strip_tags(get_ticket_val_by_hash('subj', get_ticket_hash_by_id($row['id']))) , 'no')); ?>
                    </a>
                    </small></td>
                    <td style=""><small><time id="c" datetime="<?php
                        echo $row['last_update'] ?>"></time>   </small></td>
                    <td style=""><small><?php
                        echo name_of_user_ret($row['user_init_id']); ?>       </small></td>
                    <td style=""><small><?php
                        echo $to_text; ?> </small></td>
                    <td style=""><small><?php
                        echo nameshort(name_of_user_ret(get_ticket_val_by_hash('client_id', get_ticket_hash_by_id($row['id'])))); ?> </small></td>
                    <td style=""><small><?php
                        echo $st; ?>  </small></td>
                    
                </tr>


<?php
                        
                        //echo $row['date_op'] . "<br>";
                        $i++;
                    }
                }
?>
</tbody>
</table>
<br>


</div>

</div>


<?php
            } 
            else if ($unit == "0") {
?>
    <div class="box box-solid">
        <div class="box-header">
<h4 class="box-title"><?php
                echo lang('ALLSTATS_main'); ?> <time id="c" datetime="<?php
                echo $start
?>"></time> - <time id="c" datetime="<?php
                echo $end
?>"></time></h4>
</div>
            <div class="box-body">
            <h4><center><?php
                echo lang('ALLSTATS_unit'); ?></center></h4>
            <table class="table table-bordered">
<tbody>
                                <tr>
                    <td style="width: 300px;"></td>
                    <td style=""><strong><small><center><?php
                echo lang('ALLSTATS_unit_out'); ?>   </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('ALLSTATS_unit_free'); ?>   </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('ALLSTATS_unit_lock'); ?>       </center></small></strong></td>
                    <td style=""><strong><small><center><?php
                echo lang('ALLSTATS_unit_ok'); ?> </center></small></strong></td>
                    
                </tr>
<?php
                $unit_user = unit_of_user($_SESSION['helpdesk_user_id']);
                $ee = explode(",", $unit_user);
                foreach ($ee as $key => $value) {
?>



                <tr>
                    <td style=""><small><?php
                    echo get_unit_name_return4news($value); ?>    </small></td>
                    <td style=""><small><center><?php
                    echo get_unit_stat_create($value, $start, $end); ?>   </center></small></td>
                    <td style=""><small><center><?php
                    echo get_unit_stat_free($value, $start, $end); ?>   </center></small></td>
                    <td style=""><small><center><?php
                    echo get_unit_stat_lock($value, $start, $end); ?>   </center></small></td>
                    <td style=""><small><center><?php
                    echo get_unit_stat_ok($value, $start, $end); ?>     </center></small></td>
                </tr>

                
                
    <?php
                } ?>
</tbody>
</table>

<h4><center><?php
                echo lang('ALLSTATS_user'); ?></center></h4>
<table class="table table-bordered table-hover">
                <tbody>
                <tr>
                    <td style="width: 200px;">  <strong><small><center><?php
                echo lang('ALLSTATS_user_fio'); ?>                 </center></small></strong></td>
                    <td style="">               <strong><small><center><?php
                echo lang('t_LIST_status'); ?>         </center></small></strong></td>
                    <td style="">               <strong><small><center><?php
                echo lang('EXT_t_created'); ?>            </center></small></strong></td>
                    <td style="">               <strong><small><center><?php
                echo lang('EXT_stats_refer'); ?>            </center></small></strong></td>
                    <td style="">               <strong><small><center><?php
                echo lang('EXT_t_oked'); ?>          </center></small></strong></td>
                    <td style="">               <strong><small><center><?php
                echo lang('EXT_stats_lock'); ?>     </center></small></strong></td>
                    <td style="">               <strong><small><center><?php
                echo lang('EXT_stats_unlock'); ?> </center></small></strong></td>
                    <td style="">               <strong><small><center><?php
                echo lang('EXT_stats_no_ok'); ?> </center></small></strong></td>
                </tr>
<?php
                
                //$ee - массив id отделов, на которые у меня есть права
                //$ec - массив id отделов пользователей
                //если какой-то отдел совпадает вывести
                $stmt = $dbConnection->prepare('SELECT id, unit from users where is_client=0');
                $stmt->execute();
                $result = $stmt->fetchAll();
                if (!empty($result)) {
                    
                    foreach ($result as $row) {
                        $ec = explode(",", $row['unit']);
                        
                        $res = $dbConnection->prepare('SELECT count(*) from tickets where user_init_id=:uid and date_create between :start AND :end');
                        $res->execute(array(
                            ':uid' => $row['id'],
                            ':start' => $start,
                            ':end' => $end
                        ));
                        $count = $res->fetch(PDO::FETCH_NUM);
                        $get_total_tickets_create = $count[0];
                        
                        $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                        $res->execute(array(
                            ':uid' => $row['id'],
                            ':start' => $start,
                            ':end' => $end,
                            ':refer' => 'refer'
                        ));
                        $count = $res->fetch(PDO::FETCH_NUM);
                        $get_total_tickets_refer = $count[0];
                        
                        $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                        $res->execute(array(
                            ':uid' => $row['id'],
                            ':start' => $start,
                            ':end' => $end,
                            ':refer' => 'ok'
                        ));
                        $count = $res->fetch(PDO::FETCH_NUM);
                        $get_total_tickets_ok = $count[0];
                        
                        $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                        $res->execute(array(
                            ':uid' => $row['id'],
                            ':start' => $start,
                            ':end' => $end,
                            ':refer' => 'lock'
                        ));
                        $count = $res->fetch(PDO::FETCH_NUM);
                        $get_total_tickets_lock = $count[0];
                        
                        $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                        $res->execute(array(
                            ':uid' => $row['id'],
                            ':start' => $start,
                            ':end' => $end,
                            ':refer' => 'unlock'
                        ));
                        $count = $res->fetch(PDO::FETCH_NUM);
                        $get_total_tickets_unlock = $count[0];
                        
                        $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                        $res->execute(array(
                            ':uid' => $row['id'],
                            ':start' => $start,
                            ':end' => $end,
                            ':refer' => 'no_ok'
                        ));
                        $count = $res->fetch(PDO::FETCH_NUM);
                        $get_total_tickets_no_ok = $count[0];
                        
                        $result = array_intersect($ee, $ec);
                        if ($result) {
?>

<tr>
                    <td style="width: 200px;"><small><?php
                            echo name_of_user_ret($row['id']); ?></small></td>
                    <td style=""><small class="text-danger"><center><?php
                            echo get_user_status($row['id']); ?></center></small></td>

                    <td style=""><small class="text-danger"><center><?php
                            echo $get_total_tickets_create; ?></center></small></td>
                    <td style=""><small class="text-warning"><center><?php
                            echo $get_total_tickets_refer; ?></center></small></td>
                    <td style=""><small class="text-success"><center><?php
                            echo $get_total_tickets_ok; ?></center></small></td>
                    <td style=""><small class=""><center><?php
                            echo $get_total_tickets_lock; ?></center></small></td>
                    <td style=""><small class=""><center><?php
                            echo $get_total_tickets_unlock; ?></center></small></td>
                    <td style=""><small class=""><center><?php
                            echo $get_total_tickets_no_ok; ?></center></small></td>
</tr>






<?php
                        }
                    }
                }
?>

                </tbody>
</table>

            </div>
    </div>
<?php
            }
        }
        
        if ($mode == "get_new_ticket_log") {
            
            echo view_log(get_ticket_val_by_hash('id', $_POST['ticket_hash']));
            
            //echo $_POST['ticket_hash'];
            
            
        }
        
        if ($mode == "get_user_stat") {
            
            //print_r($_POST);
            
            if ($_POST['uid']) {
                $start = $_POST['start'] . " 00:00:00";
                $end = $_POST['end'] . " 23:59:00";
                $uid = $_POST['uid'];
                
                /*
                вывести весь лог всех действий пользователя
                
                */
                
                $stmt = $dbConnection->prepare('SELECT date_op, msg, init_user_id, to_user_id, to_unit_id, ticket_id from ticket_log where init_user_id=:iud and date_op between :start AND :end order by id DESC');
                $stmt->execute(array(
                    ':iud' => $uid,
                    ':start' => $start,
                    ':end' => $end
                ));
                $re = $stmt->fetchAll();
                
                $res = $dbConnection->prepare('SELECT count(*) from tickets where user_init_id=:uid and date_create between :start AND :end');
                $res->execute(array(
                    ':uid' => $uid,
                    ':start' => $start,
                    ':end' => $end
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $get_total_tickets_create = $count[0];
                
                $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                $res->execute(array(
                    ':uid' => $uid,
                    ':start' => $start,
                    ':end' => $end,
                    ':refer' => 'refer'
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $get_total_tickets_refer = $count[0];
                
                $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                $res->execute(array(
                    ':uid' => $uid,
                    ':start' => $start,
                    ':end' => $end,
                    ':refer' => 'ok'
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $get_total_tickets_ok = $count[0];
                
                $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                $res->execute(array(
                    ':uid' => $uid,
                    ':start' => $start,
                    ':end' => $end,
                    ':refer' => 'lock'
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $get_total_tickets_lock = $count[0];
                
                $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                $res->execute(array(
                    ':uid' => $uid,
                    ':start' => $start,
                    ':end' => $end,
                    ':refer' => 'unlock'
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $get_total_tickets_unlock = $count[0];
                
                $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                $res->execute(array(
                    ':uid' => $uid,
                    ':start' => $start,
                    ':end' => $end,
                    ':refer' => 'no_ok'
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $get_total_tickets_no_ok = $count[0];
                
                if (!empty($re)) { ?>
                        
                        <div class="box box-info">
                            <div class="box-header">
                                    <h4 class="box-title"><?php
                    echo lang('EXT_stat_title'); ?> <time id="c" datetime="<?php
                    echo $start
?>"></time> - <time id="c" datetime="<?php
                    echo $end
?>"></time></h4>
                                </div>
                            <div class="panel-body" style="max-height: 400px; scroll-behavior: initial; overflow-y: scroll;">

                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th><center><small><?php
                    echo lang('TICKET_t_date'); ?></small></center>    </th>
                                                <th><center><small><?php
                    echo lang('TICKET_name'); ?>   </small></center></th>
                                                <th><center><small><?php
                    echo lang('TICKET_t_action'); ?>   </small></center></th>
                                                <th><center><small><?php
                    echo lang('TICKET_t_desc'); ?> </small></center></th>
                                                

                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php
                    foreach ($re as $row) {
                        
                        $t_action = $row['msg'];
                        
                        if ($t_action == 'refer') {
                            $icon_action = "fa fa-long-arrow-right";
                            $text_action = "" . lang('TICKET_t_a_refer') . " " . view_array(get_unit_name_return($row['to_unit_id'])) . "<br>" . name_of_user_ret($row['to_user_id']);
                        }
                        
                        if ($t_action == 'ok') {
                            $icon_action = "fa fa-check-circle-o";
                            $text_action = lang('TICKET_t_a_ok');
                        }
                        if ($t_action == 'no_ok') {
                            $icon_action = "fa fa-circle-o";
                            $text_action = lang('TICKET_t_a_nook');
                        }
                        if ($t_action == 'lock') {
                            $icon_action = "fa fa-lock";
                            $text_action = lang('TICKET_t_a_lock');
                        }
                        if ($t_action == 'unlock') {
                            $icon_action = "fa fa-unlock";
                            $text_action = lang('TICKET_t_a_unlock');
                        }
                        if ($t_action == 'create') {
                            $icon_action = "fa fa-star-o";
                            $text_action = lang('TICKET_t_a_create');
                        }
                        
                        if ($t_action == 'comment') {
                            $icon_action = "fa fa-comment";
                            $text_action = lang('TICKET_t_a_com');
                        }
                        
                        $ru = name_of_user_ret($row['init_user_id']);
?>
                                                <tr>
                                                    <td style="width: 100px; vertical-align: inherit;"><small><center>
                                                    
                                                    <time id="c" datetime="<?php
                        echo $row['date_op'] ?>"></time>
                                                    
                                                    </center></small></td>
                                                    <td style=" width: 70px; vertical-align: inherit;"><center><small>
                                                       <a href="ticket?<?php
                        echo get_ticket_hash_by_id($row['ticket_id']) ?>"> #<?php
                        echo $row['ticket_id'] ?></a>
                                                        </small></center></td>
                                                    <td style=" width: 50px; vertical-align: inherit;"><small><center><i class="<?php
                        echo $icon_action; ?>"></i>  </center></small></td>
                                                    <td style=" width: 200px; vertical-align: inherit;"><small><?php
                        echo $text_action
?></small></td>

                                                    
                                                </tr>
                                            <?php
                    } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                
                        
                        <div class="box-body"></div></div>
                            
                            
                            
                            
                            <div class="box box-info">
                                <div class="box-header">
                                    <h4 class="box-title"><?php
                    echo lang('EXT_stats_main'); ?></h4>
                                </div>
                                
                                <div class="row">
                            <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="<?php
                    echo $get_total_tickets_create; ?>" data-width="100" data-height="100" data-max="50" data-max="<?php
                    echo (get_total_tickets_count()); ?>" data-fgColor="#39CCCC"/>
                                            <div class="knob-label"><?php
                    echo lang('EXT_t_created'); ?></div>
                                        </div><!-- ./col -->
                            <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="<?php
                    echo $get_total_tickets_refer; ?>" data-width="100" data-height="100" data-max="50" data-max="<?php
                    echo (get_total_tickets_count()); ?>" data-fgColor="#932AB6"/>
                                            <div class="knob-label"><?php
                    echo lang('EXT_stats_refer'); ?></div>
                                        </div><!-- ./col -->
                            <div class="col-xs-4 text-center">
                                            <input type="text" class="knob" data-readonly="true" value="<?php
                    echo $get_total_tickets_ok; ?>" data-width="100" data-height="100" data-max="50" data-max="<?php
                    echo (get_total_tickets_count()); ?>" data-fgColor="#39CC57"/>
                                            <div class="knob-label"><?php
                    echo lang('EXT_t_oked'); ?></div>
                                        </div><!-- ./col -->
                                </div>
                            <div class="row">
                               <div class="col-xs-12"> <hr></div>
                            <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="<?php
                    echo $get_total_tickets_lock; ?>" data-width="100" data-height="100" data-max="50" data-max="<?php
                    echo (get_total_tickets_count()); ?>" data-fgColor="#F39C12"/>
                                            <div class="knob-label"><?php
                    echo lang('EXT_stats_lock'); ?></div>
                                        </div><!-- ./col -->
                            <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="<?php
                    echo $get_total_tickets_unlock; ?>" data-width="100" data-height="100" data-max="50" data-max="<?php
                    echo (get_total_tickets_count()); ?>" data-fgColor="#001F3F"/>
                                            <div class="knob-label"><?php
                    echo lang('EXT_stats_unlock'); ?></div>
                                        </div><!-- ./col -->
                            <div class="col-xs-4 text-center">
                                            <input type="text" class="knob" data-readonly="true" value="<?php
                    echo $get_total_tickets_no_ok; ?>" data-width="100" data-height="100" data-max="50" data-max="<?php
                    echo (get_total_tickets_count()); ?>" data-fgColor="#F56954"/>
                                            <div class="knob-label"><?php
                    echo lang('EXT_stats_no_ok'); ?></div>
                                        </div><!-- ./col -->
                                </div>
                            </div>
<?php
                } 
                else {
?>
                <div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-warning"></i>
                                        
                                        <?php
                    echo lang('E_no_info'); ?>
                                    </div>

                <?php
                }
            } 
            else {
?>
                <div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-warning"></i>
                                        
                                        <?php
                echo lang('E_no_selected_user'); ?>
                                    </div>

                <?php
            }
        }
        
        if ($mode == "get_notes") {
            $noteid = ($_POST['hn']);
            
            $stmt = $dbConnection->prepare('select hashname, message from notes where hashname=:noteid');
            $stmt->execute(array(
                ':noteid' => $noteid
            ));
            $res = $stmt->fetchAll();
            
            foreach ($res as $row) {
                echo $row['message'];
            }
        }
        
        if ($mode == "del_notes") {
            $noteid = ($_POST['nid']);
            $stmt = $dbConnection->prepare('delete from notes where hashname=:noteid');
            $stmt->execute(array(
                ':noteid' => $noteid
            ));
        }
        
        if ($mode == "create_notes") {
            $uid = $_SESSION['helpdesk_user_id'];
            $hn = md5(time());
            $stmt = $dbConnection->prepare('insert into notes (message, hashname, user_id, dt) values (:nr, :hn, :uid, :n)');
            $stmt->execute(array(
                ':nr' => 'new record',
                ':hn' => $hn,
                ':uid' => $uid,
                ':n' => $CONF['now_dt']
            ));
            
            echo $hn;
        }
        
        if ($mode == "find_client") {
            
            $term = trim(strip_tags(($_POST['name'])));
            
            $stmt = $dbConnection->prepare('SELECT id FROM users WHERE ((fio = :term) or (login = :term2) or (tel = :term3)) and id!=1 and is_client=1 and status!=2 limit 1');
            $stmt->execute(array(
                ':term' => $term,
                ':term2' => $term,
                ':term3' => $term
            ));
            
            $res1 = $stmt->fetchAll();
            
            if (!empty($res1)) {
                foreach ($res1 as $row) {
                    $r['res'] = true;
                    $r['p'] = $row['id'];
                }
            }
            
            if (empty($res1)) {
                $r['res'] = false;
                
                //user priv to add client in new ticket
                $pa = get_user_val('priv_add_client');
                
                if (isset($_POST['cron'])) {
                    $r['priv'] = false;
                    $r['msg_error'] = "<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">
  <button type=\"button\" class=\"close\" data-dismiss=\"alert\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">Close</span></button>
  User must be created.
</div>";
                } 
                else if (!isset($_POST['cron'])) {
                    if ($pa == 1) {
                        $r['priv'] = true;
                        $r['msg_error'] = "";
                    }
                    if ($pa == 0) {
                        $r['priv'] = false;
                        $r['msg_error'] = "<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">
  <button type=\"button\" class=\"close\" data-dismiss=\"alert\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">Close</span></button>
  " . lang('TICKET_error_msg') . "
</div>";
                    }
                }
            }
            
            $row_set[] = $r;
            echo json_encode($row_set);
        }
        
        if ($mode == "add_additional_tickets_perf") {
            
            $stmt = $dbConnection->prepare('INSERT into ticket_fields (name, placeholder, hash) values (:n,:p, :h)');
            $stmt->execute(array(
                ':n' => '',
                ':p' => '',
                ':h' => randomhash()
            ));
            
            get_ticket_form_view();
        }
        
        if ($mode == "add_additional_user_perf") {
            
            $stmt = $dbConnection->prepare('INSERT into user_fields (name, placeholder, hash) values (:n,:p, :h)');
            $stmt->execute(array(
                ':n' => '',
                ':p' => '',
                ':h' => randomhash()
            ));
            
            get_user_form_view();
        }
        if ($mode == "del_userfield_item") {
            
            $stmt = $dbConnection->prepare('delete from user_fields where hash=:h');
            $stmt->execute(array(
                ':h' => $_POST['hash']
            ));
            
            get_user_form_view();
        }
        if ($mode == "del_field_item") {
            
            $stmt = $dbConnection->prepare('delete from ticket_fields where hash=:h');
            $stmt->execute(array(
                ':h' => $_POST['hash']
            ));
            
            get_ticket_form_view();
        }
        
        if ($mode == "reset_sort") {
            
            $pt = $_POST['pt'];
            
            if ($pt == "in") {
                unset($_SESSION['zenlix_list_in_sort']);
                unset($_SESSION['zenlix_list_in_sort_var']);
            }
            if ($pt == "out") {
                unset($_SESSION['zenlix_list_out_sort']);
                unset($_SESSION['zenlix_list_out_sort_var']);
            }
        }
        
        if ($mode == "make_sort") {
            
            $pt = $_POST['pt'];
            $st = $_POST['st'];
            
            if ($pt == "in") {
                
                if ($_SESSION['zenlix_list_in_sort'] == $st) {
                    
                    if ($_SESSION['zenlix_list_in_sort_var'] == "asc") {
                        $sort_val = "desc";
                    } 
                    else if ($_SESSION['zenlix_list_in_sort_var'] == "desc") {
                        $sort_val = "asc";
                    } 
                    else {
                        $sort_val = "asc";
                    }
                } 
                else {
                    $sort_val = "asc";
                }
                
                //<mark>
                
                $_SESSION['zenlix_list_in_sort'] = $st;
                $_SESSION['zenlix_list_in_sort_var'] = $sort_val;
            } 
            else if ($pt == "out") {
                
                if ($_SESSION['zenlix_list_out_sort'] == $st) {
                    
                    if ($_SESSION['zenlix_list_out_sort_var'] == "asc") {
                        $sort_val = "desc";
                    } 
                    else if ($_SESSION['zenlix_list_out_sort_var'] == "desc") {
                        $sort_val = "asc";
                    } 
                    else {
                        $sort_val = "asc";
                    }
                } 
                else {
                    $sort_val = "asc";
                }
                
                //<mark>
                
                $_SESSION['zenlix_list_out_sort'] = $st;
                $_SESSION['zenlix_list_out_sort_var'] = $sort_val;
            }
        }
        
        if ($mode == "get_client_from_new_t") {
            if (isset($_POST['get_client_info'])) {
                
                $client_id = ($_POST['get_client_info']);
                
                $tc = get_user_val_by_id($client_id, 'is_client');
                
                if ($tc == "1") {
                    
                    get_client_info($client_id);
                } 
                else {
?>
                    <?php
                    echo get_client_info_ticket($client_id) ?>
                    <?php
                }
            } 
            else if (isset($_POST['get_my_info'])) {
                
                get_my_info();
            } 
            else if (isset($_POST['new_client_info'])) {
                $fio = ($_POST['new_client_info']);
                $u_l = ($_POST['new_client_login']);
?>


<div class="box box-info">
                                <div class="box-header">
                                    <i class="fa fa-user"></i>
                                    <h3 class="box-title"> <?php
                echo lang('WORKER_TITLE'); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body" >

                        <div class="">


<div class="callout callout-warning">
                                        
                                        <p><?php
                echo lang('msg_created_new_user'); ?></p>
                                    </div>

                            <table class="table  ">
                                <tbody>
                                <tr>
                                    <td style=" width: 30px; "><small><?php
                echo lang('WORKER_fio'); ?>:</small></td>
                                    <td><small>
                                            <a href="#" id="username" data-type="text" data-pk="1" data-title="Enter username"><?php
                echo $fio
?></a>
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?php
                echo lang('WORKER_login'); ?>:</small></td>
                                    <td><small><a href="#" id="new_login" data-type="text"  data-pk="1" data-title="Enter username"><?php
                echo $u_l
?></a></small></td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?php
                echo lang('WORKER_posada'); ?>:</small></td>
                                    <td><small><a href="#" id="new_posada"  data-type="select" data-source="<?php
                echo $CONF['hostname']; ?>action?mode=getJSON_posada" data-pk="1" data-title="<?php
                echo lang('WORKER_posada'); ?>"></a></small></td>
                                </tr>
                                

                                <tr>
                                    <td style=" width: 30px; "><small><?php
                echo lang('WORKER_tel'); ?>:</small></td>
                                    <td><small><a href="#" id="new_tel" data-type="text" data-pk="1" data-title="Enter username"></a></small></td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?php
                echo lang('WORKER_room'); ?>:</small></td>
                                    <td><small><a href="#" id="new_adr" data-type="text" data-pk="1" data-title="Enter username"></a></small></td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?php
                echo lang('WORKER_mail'); ?>:</small></td>
                                    <td><small><a href="#" id="new_mail" data-type="text" data-pk="1" data-title="Enter username"></a></small></td>
                                </tr>

                                </tbody>
                            </table>

                        </div>
                    
                                
                                </div>
</div>



                                    
                                    
                                    


            <?php
            }
        }
        
        if ($mode == "verify_login_nt") {
            
            $l = $_POST['value'];
            
            if (validate_exist_login($l) == true) {
                echo "";
            } 
            else if (validate_exist_login($l) == false) {
                header('HTTP 400 Bad Request', true, 400);
                echo lang('ticket_login_error');
            }
            
            //header('HTTP 400 Bad Request', true, 400);
            //echo lang('ticket_login_error');
            
            
        }
        
        if ($mode == "get_unit_id") {
            $uid = ($_POST['uid']);
            
            $u = unit_of_user($uid);
            $units = explode(",", $u);
            echo $units[0];
        }
        
        if ($mode == "view_unread_msgs_labels") {
            $r = get_total_unread_messages();
            
            if ($r != 0) {
                echo $r;
            } 
            else if ($r == 0) {
                echo "";
            }
        }
        
        if ($mode == "view_unread_msgs_total") {
            
            $tm = get_total_unread_messages();
            if ($tm != 0) {
                $title = lang('EXT_unread_msg1') . " <strong class=\"label_unread_msg\">" . $tm . "</strong> " . lang('EXT_unread_msg2');
            } 
            else if ($tm == 0) {
                $title = lang('EXT_no_unread_msg');
            }
            
            echo $title;
        }
        
        if ($mode == "view_unread_msgs") {
            $stmt = $dbConnection->prepare('SELECT user_from, msg, date_op from messages where user_to=:uto and is_read=0');
            $stmt->execute(array(
                ':uto' => $_SESSION['helpdesk_user_id']
            ));
            
            $re = $stmt->fetchAll();
            
            foreach ($re as $rews) {
?>
                                    
                                    
                                        <li><!-- start message -->
                                            <a href="messages?to=<?php
                echo get_user_val_by_id($rews['user_from'], 'uniq_id'); ?>">
                                                <div class="pull-left">
                                                    <img src="<?php
                echo get_user_img_by_id($uniq_id); ?>" class="img-circle" alt="User Image"/>
                                                </div>
                                                <h4>
                                                    <?php
                echo nameshort(name_of_user_ret_nolink($rews['user_from'])); ?>
                                                    
                                                    <small><i class="fa fa-clock-o"></i> <time id="b" datetime="<?php
                echo $rews['date_op']; ?>"></time> </time></small>
                                                </h4>
                                                <p><?php
                echo make_html($rews['msg'], 'no'); ?></p>
                                            </a>
                                        </li><!-- end message -->
                                        <?php
            }
        }
        
        if ($mode == "count_online_users") {
            
            echo get_total_users_online();
        }
        
        if ($mode == "show_online_users") {
            
            $stmt = $dbConnection->prepare('select fio,id,uniq_id from users where last_time >= DATE_SUB(:n,INTERVAL 2 MINUTE)');
            $stmt->execute(array(
                ':n' => $CONF['now_dt']
            ));
            $re = $stmt->fetchAll();
            
            foreach ($re as $rews) {
?>
<li><!-- start message -->
                                            <a href="view_user?<?php
                echo $rews['uniq_id']; ?>">
                                                <div class="pull-left">
                                                    <img src="<?php
                echo get_user_img_by_id($rews['id']); ?>" class="img-circle" alt="User Image"/>
                                                </div>
                                                <h4>
                                                    <?php
                echo nameshort(name_of_user_ret_nolink($rews['id'])); ?>
                                                    
                                                    
                                                </h4>
                                                <p><?php
                echo get_user_val_by_id($rews['id'], 'posada'); ?></p>
                                            </a>
                                        </li><!-- end message -->
                                       <?php
            }
        }
        
        if ($mode == "get_chat_message") {
            $msgid = $_POST['msg_id'];
            
            $stmt = $dbConnection->prepare('select user_from, date_op, msg from messages where id=:msgid');
            $stmt->execute(array(
                ':msgid' => $msgid
            ));
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $uuniq_id = get_user_val_by_id($r['user_from'], 'uniq_id');
            $user_from = nameshort(name_of_user_ret_nolink($r['user_from']));
            $msgtext = $r['msg'];
            
            $results[] = array(
                'uniq_id' => $uuniq_id,
                'new_msg_text' => lang('EXT_new_message') ,
                'time_op' => "<time id=\"b\" datetime=\"" . date("Y-m-d H:i:s") . "\"></time>",
                'user_from' => $user_from,
                'user_chat' => $msgtext
            );
            print json_encode($results);
        }
        
        if ($mode == "update_dashboard_labels") {
            $results[] = array(
                'tool1' => get_total_tickets_free() ,
                'tool2' => get_total_tickets_lock() ,
                'tool3' => get_total_tickets_out_and_success() ,
                'tool4' => get_total_tickets_ok()
            );
            print json_encode($results);
        }
        
        if ($mode == "update_list_labels") {
            $newt = get_total_tickets_free();
            
            if ($newt != 0) {
                $newtickets = "(" . $newt . ")";
            }
            if ($newt == 0) {
                $newtickets = "";
            }
            $outt = get_total_tickets_out_and_success();
            if ($outt != 0) {
                $out_tickets = "(" . $outt . ")";
            }
            if ($outt == 0) {
                $out_tickets = "";
            }
            
            $results[] = array(
                'in' => $newtickets,
                'out' => $out_tickets
            );
            print json_encode($results);
        }
        if ($mode == "check_update_one") {
            $lu = ($_POST['last_update']);
            $ticket_id = ($_POST['id']);
            
            $stmt = $dbConnection->prepare('SELECT last_update,hash_name FROM tickets where id=:ticket_id');
            $stmt->execute(array(
                ':ticket_id' => $ticket_id
            ));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $db_lu = $fio['last_update'];
            $db_hn = $fio['hash_name'];
            $at = get_last_action_type($ticket_id);
            
            if (strtotime($db_lu) > strtotime($lu)) {
                if ($at == 'comment') {
                    $todo = "comment";
                } 
                else {
                    $todo = "update";
                }
            }
            if (strtotime($db_lu) <= strtotime($lu)) {
                $todo = "no";
            }
            
            $results[] = array(
                'type' => $todo,
                'time' => $db_lu,
                'hash' => $db_hn
            );
            
            print json_encode($results);
        }
        
        if ($mode == "get_users_list") {
            $idzz = ($_POST['unit']);
            
            $stmt = $dbConnection->prepare('SELECT fio, id, unit FROM users where id != 1 and status =1 and is_client=0');
            $stmt->execute();
            $result = $stmt->fetchAll();
            
            foreach ($result as $row) {
                
                if ($idzz == "0") {
                    $un = $row['fio'];
                    $ud = (int)$row['id'];
                    if (get_user_status_text($row['value']) == "online") {
                        $s = "online";
                    } 
                    else if (get_user_status_text($row['value']) == "offline") {
                        $s = "offline";
                    }
                    
                    $results[] = array(
                        'name' => nameshort($un) ,
                        'stat' => $s,
                        'co' => $ud
                    );
                } 
                else if ($idzz <> "0") {
                    $un = $row['fio'];
                    $ud = (int)$row['id'];
                    $u = explode(",", $row['unit']);
                    
                    if (in_array($idzz, $u)) {
                        
                        if (get_user_status_text($row['value']) == "online") {
                            $s = "online";
                        } 
                        else if (get_user_status_text($row['value']) == "offline") {
                            $s = "offline";
                        }
                        
                        $results[] = array(
                            'name' => nameshort($un) ,
                            'stat' => $s,
                            'co' => $ud
                        );
                    }
                }
            }
            
            print json_encode($results);
        }
        
        if ($mode == "sort_units_helper") {
            $list = $_POST['list'];
            
            echo $list;
            
            $orderlist = explode('&', $list);
            
            $n = 0;
            foreach ($orderlist as $order) {
                
                $a = explode("=", $order);
                
                //echo $a[0];
                
                $b = explode("[", $a['0']);
                
                $с = substr($b[1], 0, -1);
                
                //?
                $rest = substr($b[1], 0, -1);
                
                //echo $a[1];
                //echo "ID:".$rest."  Parent:".$a[1]."  Pos:".$n."                              ////";
                if ($a[1] == "null") {
                    $a[1] = get_max_helper_parent();
                }
                echo "parent_id=" . $a[1] . " where id=" . $rest . ";\r\n";
                
                $stmt = $dbConnection->prepare('UPDATE helper_cat set sort_id=:s_id,parent_id=:p_id where id=:el_id');
                $stmt->execute(array(
                    ':s_id' => $n,
                    ':p_id' => $a[1],
                    ':el_id' => $rest
                ));
                
                $n++;
            }
        }
        
        if ($mode == "save_helper_item") {
            
            $stmt = $dbConnection->prepare('UPDATE helper_cat set name=:t where id=:el_id');
            $stmt->execute(array(
                ':t' => $_POST['value'],
                ':el_id' => $_POST['pk']
            ));
        }
        
        //helper_item_del
        if ($mode == "helper_item_del") {
            
            $stmt = $dbConnection->prepare('UPDATE helper_cat set parent_id=:t where parent_id=:el_id');
            $stmt->execute(array(
                ':t' => '0',
                ':el_id' => $_POST['id']
            ));
            
            $stmt = $dbConnection->prepare('delete from helper_cat where id=:n');
            $stmt->execute(array(
                ':n' => $_POST['id']
            ));
            
            showMenu_helper();
        }
        
        if ($mode == "items_view") {
            $stmt = $dbConnection->prepare('INSERT into helper_cat (name, parent_id, sort_id) values (:n,:p,:s)');
            $stmt->execute(array(
                ':n' => 'new item',
                ':p' => '0',
                ':s' => '100'
            ));
            showMenu_helper();
        }
        
        if ($mode == "units_helper") {
?>
            <div class="box box-solid">
            <div class="">
            
<style type="text/css">

a, a:visited {
            color: #4183C4;
            text-decoration: none;
        }

        

        pre, code {
            font-size: 12px;
        }

        pre {
            width: 100%;
            overflow: auto;
        }

        small {
            font-size: 90%;
        }

        small code {
            font-size: 11px;
        }

        .placeholder {
            outline: 1px dashed #4183C4;
            /*-webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            margin: -1px;*/
        }

        .mjs-nestedSortable-error {
            background: #fbe3e4;
            border-color: transparent;
        }

        ul {
            margin: 0;
            padding: 0;
            padding-left: 30px;
        }

        ul.sortable, ul.sortable ul {
            margin: 0 0 0 25px;
            padding: 0;
            list-style-type: none;
        }

        ul.sortable {
            margin: 4em 0;
        }

        .sortable li {
            margin: 5px 0 0 0;
            padding: 0;
        }

        .sortable li div  {
            /*
            border: 1px solid #d4d4d4;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            border-color: #D4D4D4 #D4D4D4 #BCBCBC;
            padding: 6px;
            margin: 0;
            cursor: move;
            background: #f6f6f6;
            background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #ededed 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(47%,#f6f6f6), color-stop(100%,#ededed));
            background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            background: -o-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            background: -ms-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            background: linear-gradient(to bottom,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ededed',GradientType=0 );
            */
        }

        .sortable li.mjs-nestedSortable-branch div {
           /* background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #f0ece9 100%);
            background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#f0ece9 100%);
            */
            list-style-type: none;

        }

        .sortable li.mjs-nestedSortable-leaf div {


        }

        li.mjs-nestedSortable-collapsed.mjs-nestedSortable-hovering div {
            border-color: #999;
            background: #fafafa;
        }

        .disclose {
            cursor: pointer;
            width: 10px;
            display: none;
        }

        .sortable li.mjs-nestedSortable-collapsed > ul {
            display: none;
        }

        .sortable li.mjs-nestedSortable-branch > div > .disclose {
            display: inline-block;
        }

        .sortable li.mjs-nestedSortable-collapsed > div > .disclose > span:before {
            content: '+ ';
        }

        .sortable li.mjs-nestedSortable-expanded > div > .disclose > span:before {
            content: '- ';
        }

        

        p, ol, ul, pre, form {
            margin-top: 0;
            margin-bottom: 1em;
        }

        dl {
            margin: 0;
        }

        dd {
            margin: 0;
            padding: 0 0 0 1.5em;
        }

        code {
            background: #e5e5e5;
        }

        input {
            vertical-align: text-bottom;
        }

        .notice {
            color: #c33;
        }

    </style>


<div class="">
                                <div class="box-header">
                                    
                                    <h3 class="box-title">To Do List</h3>

                                </div><!-- /.box-header -->
                                <div class="box-body">




<div id="content_items"> 


<?php
            showMenu_helper();
?>


</div>

                                </div><!-- /.box-body -->
                                <div class="box-footer clearfix no-border">
                                    <button id="add_helper_item" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add item</button>
                                </div>
                            </div>
            </div></div>
        <?php
        }
        
        if ($mode == "find_help") {
            $t = ($_POST['t']);
            $user_id = $_SESSION['helpdesk_user_id'];
            $unit_user = unit_of_user($user_id);
            $priv_val = priv_status($user_id);
            
            $units = explode(",", $unit_user);
            array_push($units, "0");
            
            $is_client = get_user_val('is_client');
            
            if ($is_client == "1") {
                
                $stmt = $dbConnection->prepare("SELECT 
                            id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where (title like :t or message like :t2) and client_flag=:cf
                            order by dt desc");
                $stmt->execute(array(
                    ':t' => '%' . $t . '%',
                    ':t2' => '%' . $t . '%',
                    ':cf' => '1'
                ));
                $result = $stmt->fetchAll();
?>
            <div class="box box-solid">
            <div class="box-body">
            <?php
                foreach ($result as $row) {
                    
                    $unit2id = explode(",", $row['unit_to_id']);
                    
                    $diff = array_intersect($units, $unit2id);
                    
                    $priv_h = "no";
                    if ($priv_val == 1) {
                        if (($diff) || ($user_id == $row['user_init_id'])) {
                            $ac = "ok";
                        }
                        
                        if ($user_id == $row['user_init_id']) {
                            $priv_h = "yes";
                        }
                    } 
                    else if ($priv_val == 0) {
                        $ac = "ok";
                        if ($user_id == $row['user_init_id']) {
                            $priv_h = "yes";
                        }
                    } 
                    else if ($priv_val == 2) {
                        $ac = "ok";
                        $priv_h = "yes";
                    }
                    
                    if ($ac == "ok") {
?>

                    <div class="box box-solid">
                                <div class="box-header">
                                    <h5 class="box-title"><small><i class="fa fa-file-text-o"></i></small> <a style="font-size: 18px;" class="text-light-blue" href="helper?h=<?php
                        echo $row['hashname']; ?>"><?php
                        echo $row['title']; ?></a></h5>
                                    <div class="box-tools pull-right">

                                    </div>
                                </div>
                                <div class="box-body">
                                    <small><?php
                        echo cutstr_help_ret(strip_tags($row['message'])); ?>
                            </small>                                </div><!-- /.box-body -->
                            </div>
                <?php
                    }
                }
?></div></div> <?php
            } 
            else if ($is_client == "0") {
                
                $stmt = $dbConnection->prepare("SELECT 
                            id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where title like :t or message like :t2
                            order by dt desc");
                $stmt->execute(array(
                    ':t' => '%' . $t . '%',
                    ':t2' => '%' . $t . '%'
                ));
                $result = $stmt->fetchAll();
?>
            <div class="box box-solid">
            <div class="box-body">
            <?php
                foreach ($result as $row) {
                    
                    $unit2id = explode(",", $row['unit_to_id']);
                    
                    $diff = array_intersect($units, $unit2id);
                    
                    $priv_h = "no";
                    if ($priv_val == 1) {
                        if (($diff) || ($user_id == $row['user_init_id'])) {
                            $ac = "ok";
                        }
                        
                        if ($user_id == $row['user_init_id']) {
                            $priv_h = "yes";
                        }
                    } 
                    else if ($priv_val == 0) {
                        $ac = "ok";
                        if ($user_id == $row['user_init_id']) {
                            $priv_h = "yes";
                        }
                    } 
                    else if ($priv_val == 2) {
                        $ac = "ok";
                        $priv_h = "yes";
                    }
                    
                    if ($ac == "ok") {
?>

                    <div class="box box-solid">
                                <div class="box-header">
                                    <h5 class="box-title"><small><i class="fa fa-file-text-o"></i></small> <a style="font-size: 18px;" class="text-light-blue" href="helper?h=<?php
                        echo $row['hashname']; ?>"><?php
                        echo $row['title']; ?></a></h5>
                                    <div class="box-tools pull-right">
<small>(<?php
                        echo lang('DASHBOARD_author'); ?>: <?php
                        echo nameshort(name_of_user_ret($row['user_init_id'])); ?>)<?php
                        if ($priv_h == "yes") {
                            echo " 
            <div class=\"btn-group\">
            <a href=\"" . $CONF['hostname'] . "helper?h=" . $row['hashname'] . "&edit\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-pencil\"></i></a>
            <button id=\"del_helper\" value=\"" . $row['hashname'] . "\"type=\"button\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-trash-o\"></i></button>
            </div>
            ";
                        } ?></small>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <small><?php
                        echo cutstr_help_ret(strip_tags($row['message'])); ?>
                            </small>                                </div><!-- /.box-body -->
                            </div>                <?php
                    }
                }
?></div></div><?php
            }
        }
        
        if ($mode == "del_help") {
            $hn = ($_POST['hn']);
            
            $stmt = $dbConnection->prepare('delete from helper where hashname=:hn');
            $stmt->execute(array(
                ':hn' => $hn
            ));
        }
        
        if ($mode == "list_help") {
            
            $is_client = get_user_val('is_client');
            
            if ($is_client == "1") {
                $stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where client_flag=:cf
                            order by dt desc');
                $stmt->execute(array(
                    ':cf' => '1'
                ));
                $result = $stmt->fetchAll();
?>
            <div class="box box-solid">
            <div class="box-body">
            <?php
                if (empty($result)) {
?>
                 <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">

                    <center><?php
                    echo lang('MSG_no_records'); ?></center></p>



            <?php
                } 
                else if (!empty($result)) {
                    
                    foreach ($result as $row) {
?>
<div class="box box-solid">
                                <div class="box-header">
                                    <h5 class="box-title"><small><i class="fa fa-file-text-o"></i> </small><a style="font-size: 18px;" class="text-light-blue" href="helper?h=<?php
                        echo $row['hashname']; ?>"><?php
                        echo $row['title']; ?></a></h5>
                                </div>
                                <div class="box-body" id="help_content">
                                    <div id="summernote_help">
                                    <small><?php
                        echo cutstr_help_ret(strip_tags($row['message'])); ?>
                            </small>                               
                                    </div>
                             </div><!-- /.box-body -->
                            </div>
                        <?php
                    }
                }
?>
                
            </div></div>
                
                <?php
            } 
            else if ($is_client == "0") {
                
                $user_id = $_SESSION['helpdesk_user_id'];
                $unit_user = unit_of_user($user_id);
                $priv_val = priv_status($user_id);
                
                $units = explode(",", $unit_user);
                array_push($units, "0");
                
                $stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper 
                            order by dt desc');
                $stmt->execute();
                $result = $stmt->fetchAll();
?>
            <div class="box box-solid">
            <div class="box-body">
            <?php
                if (empty($result)) {
?>
                <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">

                    <center><?php
                    echo lang('MSG_no_records'); ?></center></p>

                



            <?php
                } 
                else if (!empty($result)) {
                    
                    foreach ($result as $row) {
                        $unit2id = explode(",", $row['unit_to_id']);
                        
                        $diff = array_intersect($units, $unit2id);
                        $priv_h = "no";
                        if ($priv_val == 1) {
                            if (($diff) || ($user_id == $row['user_init_id'])) {
                                $ac = "ok";
                            }
                            
                            if ($user_id == $row['user_init_id']) {
                                $priv_h = "yes";
                            }
                        } 
                        else if ($priv_val == 0) {
                            $ac = "ok";
                            if ($user_id == $row['user_init_id']) {
                                $priv_h = "yes";
                            }
                        } 
                        else if ($priv_val == 2) {
                            $ac = "ok";
                            $priv_h = "yes";
                        }
                        
                        if ($ac == "ok") {
?>


<div class="box box-solid">
                                <div class="box-header">
                                    <h5 class="box-title"><small><i class="fa fa-file-text-o"></i></small> <a style="font-size: 18px;" class="text-light-blue" href="helper?h=<?php
                            echo $row['hashname']; ?>"><?php
                            echo $row['title']; ?></a></h5>
                                    <div class="box-tools pull-right">
<small>(<?php
                            echo lang('DASHBOARD_author'); ?>: <?php
                            echo nameshort(name_of_user_ret($row['user_init_id'])); ?>)<?php
                            if ($priv_h == "yes") {
                                echo " 
            <div class=\"btn-group\">
<a href=\"" . $CONF['hostname'] . "helper?h=" . $row['hashname'] . "&edit\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-pencil\"></i></a>
            <button id=\"del_helper\" value=\"" . $row['hashname'] . "\"type=\"button\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-trash-o\"></i></button>
            </div>
            ";
                            } ?></small>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <small><?php
                            echo cutstr_help_ret(strip_tags($row['message'])); ?>
                            </small>                                </div><!-- /.box-body -->
                            </div>



                        
                        
                        
                        
                        
                        
                        
                    <?php
                        }
                    }
?>
            </div></div>
                 <?php
                }
            }
        }
        
        if ($mode == "view_cats") {
?>
<div class="box box-solid">
    <div class="box-header">
                                    
                                    <h3 class="box-title"><?php
            echo lang('HELPER_cats'); ?></h3>

                                </div><!-- /.box-header -->
                                <div class="box-body" style=" font-size: 15px; line-height: 20px; ">
                                    <?php
            echo show_items_helper(); ?>
                                </div>
                            </div>
    <?php
        }
        
        ///////
        if ($mode == "do_save_help") {
            $u = $_POST['u'];
            $beats = implode(',', $u);
            $hn = ($_POST['hn']);
            
            $t = ($_POST['t']);
            $user_id_z = $_SESSION['helpdesk_user_id'];
            
            $is_client = $_POST['is_client'];
            
            if ($is_client == "true") {
                $is_client = 1;
            } 
            else {
                $is_client = 0;
            }
            $cat_id = $_POST['cat_id'];
            $message = ($_POST['msg']);
            $message = str_replace("\r\n", "\n", $message);
            $message = str_replace("\r", "\n", $message);
            $message = str_replace("&nbsp;", " ", $message);
            
            $stmt = $dbConnection->prepare('update helper set user_edit_id=:user_id_z, unit_to_id=:beats, dt=:n, title=:t, message=:message, client_flag=:cf, cat_id=:cat_id where hashname=:hn');
            $stmt->execute(array(
                ':hn' => $hn,
                ':user_id_z' => $user_id_z,
                ':beats' => $beats,
                ':t' => $t,
                ':message' => $message,
                ':cf' => $is_client,
                ':n' => $CONF['now_dt'],
                ':cat_id' => $cat_id
            ));
        }
        
        if ($mode == "do_create_help") {
            $u = $_POST['u'];
            $beats = implode(',', $u);
            $cat_id = $_POST['cat'];
            $is_client = $_POST['is_client'];
            $mh = $_POST['mh'];
            if ($is_client == "true") {
                $is_client = 1;
            } 
            else {
                $is_client = 0;
            }
            $t = ($_POST['t']);
            $user_id_z = $_SESSION['helpdesk_user_id'];
            
            $hn = $mh;
            $message = ($_POST['msg']);
            $message = str_replace("\r\n", "\n", $message);
            $message = str_replace("\r", "\n", $message);
            $message = str_replace("&nbsp;", " ", $message);
            
            $stmt = $dbConnection->prepare('insert into helper (hashname, user_init_id,unit_to_id, dt, title,message,client_flag, cat_id) values 
        (:hn,:user_id_z,:beats, :n, :t,:message, :cf, :cat_id)');
            $stmt->execute(array(
                ':hn' => $hn,
                ':user_id_z' => $user_id_z,
                ':beats' => $beats,
                ':t' => $t,
                ':message' => $message,
                ':cf' => $is_client,
                ':n' => $CONF['now_dt'],
                ':cat_id' => $cat_id
            ));
        }
        
        if ($mode == "dashboard_t") {
            
            $page = 1;
            $perpage = '5';
            
            if (isset($_POST['p'])) {
                $perpage = $_POST['p'];
            }
            
            $start_pos = ($page - 1) * $perpage;
            
            $user_id = $_SESSION['helpdesk_user_id'];
            $unit_user = unit_of_user($user_id);
            $priv_val = priv_status($user_id);
            

            $units = explode(",", $unit_user);
            $units = implode("', '", $units);
            $in_query=NULL;
            $ee = explode(",", $unit_user);
            foreach ($ee as $key => $value) {
                $in_query = $in_query . ' :val_' . $key . ', ';
            }
            $in_query = substr($in_query, 0, -2);
            foreach ($ee as $key => $value) {
                $vv[":val_" . $key] = $value;
            }
            
            // find_in_set('44',unit_to_id) <> 0
            
            if ($priv_val == 0) {
                
                $stmt = $dbConnection->prepare('SELECT * from tickets
                            where unit_id IN (' . $in_query . ')  and arch=:n
                            order by ok_by asc, prio desc, id desc
                            limit :start_pos, :perpage');
                
                $paramss = array(
                    ':n' => '0',
                    ':start_pos' => $start_pos,
                    ':perpage' => $perpage
                );
                $stmt->execute(array_merge($vv, $paramss));
                $results = $stmt->fetchAll();
            } 
            else if ($priv_val == 1) {
                
                //find_in_set(:user_id,user_to_id) <> 0
                /*
                $arr = array(
                'p1' => $user_id
                );
                ('.implode(' OR ', array_map(fis('user_to_id'), array_keys($arr))).')
                */
                
                $stmt = $dbConnection->prepare('SELECT * from tickets
                            where ((find_in_set(:user_id,user_to_id) and arch=:n) or
                            (find_in_set(:n1,user_to_id) and unit_id IN (' . $in_query . ') and arch=:n2))
                            order by ok_by asc, prio desc, id desc
                            limit :start_pos, :perpage');
                
                $paramss = array(
                    ':n' => '0',
                    ':start_pos' => $start_pos,
                    ':perpage' => $perpage,
                    ':user_id' => $user_id,
                    ':n1' => '0',
                    ':n2' => '0'
                );
                
                $stmt->execute(array_merge($vv, $paramss));
                
                $results = $stmt->fetchAll();
            } 
            else if ($priv_val == 2) {
                
                $stmt = $dbConnection->prepare('SELECT * from tickets
                            where arch=:n
                            order by ok_by asc, prio desc, id desc
                            limit :start_pos, :perpage');
                $stmt->execute(array(
                    ':n' => '0',
                    ':start_pos' => $start_pos,
                    ':perpage' => $perpage
                ));
                $results = $stmt->fetchAll();
            }
            
            $aha = get_total_pages('dashboard', $user_id);
            if ($aha == "0") {
?>
                <div id="spinner" class="well well-large well-transparent lead">
                    <center>
                        <?php
                echo lang('MSG_no_records'); ?>
                    </center>
                </div>
            <?php
            }
            if ($aha <> "0") {
$id_icon=NULL;
$prio_icon=NULL;
$subj_icon=NULL;
$cli_icon=NULL;
$init_icon=NULL;
?>

                <input type="hidden" value="<?php
                echo get_total_pages('in', $user_id); ?>" id="val_menu">
                <input type="hidden" value="<?php
                echo $user_id; ?>" id="user_id">
                <input type="hidden" value="" id="total_tickets">
                <input type="hidden" value="" id="last_total_tickets">








                <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-bordered " style=" font-size: 14px; ">
                <thead>
                <tr>
                    <th><center><div id="sort_id" >#<?php
                echo $id_icon; ?></div></center></th>
                    <th><center><div id="sort_prio"><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="<?php
                echo lang('t_LIST_prio'); ?>"></i><?php
                echo $prio_icon; ?></div></center></th>
                    <th><center><div id="sort_subj"><?php
                echo lang('t_LIST_subj'); ?><?php
                echo $subj_icon; ?></div></center></th>
                    <th><center><div id="sort_cli"><?php
                echo lang('t_LIST_worker'); ?><?php
                echo $cli_icon; ?></div></center></th>
                    <th><center><?php
                echo lang('t_LIST_create'); ?></center></th>
                    <th><center><?php
                echo lang('t_LIST_ago'); ?></center></th>
                    <th><center><div id="sort_init"><?php
                echo lang('t_LIST_init'); ?><?php
                echo $init_icon; ?></div></center></th>
                    <th><center><?php
                echo lang('t_LIST_to'); ?></center></th>
                    <th><center><?php
                echo lang('t_LIST_status'); ?></center></th>

                </tr>
                </thead>
                <tbody>

                <?php
                foreach ($results as $row) {
                    
                    $lb = $row['lock_by'];
                    $ob = $row['ok_by'];
                    
                    $user_id_z = $_SESSION['helpdesk_user_id'];
                    $unit_user_z = unit_of_user($user_id_z);
                    $status_ok_z = $row['status'];
                    $ok_by_z = $row['ok_by'];
                    $lock_by_z = $row['lock_by'];
                    
                    ////////////////////////////Раскрашивает и подписывает кнопки/////////////////////////////////////////////////////////////////
                    if ($row['is_read'] == "0") {
                        $style = "bold_for_new";
                    }
                    if ($row['is_read'] <> "0") {
                        $style = "";
                    }
                    if ($row['status'] == "1") {
                        $ob_text = "<i class=\"fa fa-check-circle-o\"></i>";
                        $ob_status = "unok";
                        $ob_tooltip = lang('t_list_a_nook');
                        $style = "success";
                        
                        if ($lb <> "0") {
                            $lb_text = "<i class=\"fa fa-lock\"></i>";
                            $lb_status = "unlock";
                            $lb_tooltip = lang('t_list_a_unlock');
                        }
                        if ($lb == "0") {
                            $lb_text = "<i class=\"fa fa-unlock\"></i>";
                            $lb_status = "lock";
                            $lb_tooltip = lang('t_list_a_lock');
                        }
                    }
                    
                    if ($row['status'] == "0") {
                        $ob_text = "<i class=\"fa fa-circle-o\"></i>";
                        $ob_status = "ok";
                        $ob_tooltip = lang('t_list_a_ok');
                        if ($lb <> "0") {
                            $lb_text = "<i class=\"fa fa-lock\"></i>";
                            $lb_status = "unlock";
                            $lb_tooltip = lang('t_list_a_unlock');
                            if ($lb == $user_id) {
                                $style = "warning";
                            }
                            if ($lb <> $user_id) {
                                $style = "active";
                            }
                        }
                        
                        if ($lb == "0") {
                            $lb_text = "<i class=\"fa fa-unlock\"></i>";
                            $lb_status = "lock";
                            $lb_tooltip = lang('t_list_a_lock');
                        }
                    }
                    
                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
                    ////////////////////////////Показывает кому/////////////////////////////////////////////////////////////////
                    if ($row['user_to_id'] <> 0) {
                        $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['user_to_id'])) . "</div>";
                    }
                    if ($row['user_to_id'] == 0) {
                        $to_text = "<strong data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . view_array(get_unit_name_return($row['unit_id'])) . "\">" . lang('t_list_a_all') . "</strong>";
                    }
                    
                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
                    ////////////////////////////Показывает приоритет//////////////////////////////////////////////////////////////
                    $prio = "<span class=\"label label-info\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . lang('t_list_a_p_norm') . "\"><i class=\"fa fa-minus\"></i></span>";
                    
                    if ($row['prio'] == "0") {
                        $prio = "<span class=\"label label-primary\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . lang('t_list_a_p_low') . "\"><i class=\"fa fa-arrow-down\"></i></span>";
                    }
                    
                    if ($row['prio'] == "2") {
                        $prio = "<span class=\"label label-danger\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . lang('t_list_a_p_high') . "\"><i class=\"fa fa-arrow-up\"></i></span>";
                    }
                    
                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
                    ////////////////////////////Показывает labels//////////////////////////////////////////////////////////////
                    if ($row['status'] == 1) {
                        $st = "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> " . lang('t_list_a_oko') . " " . nameshort(name_of_user_ret_nolink($ob)) . "</span>";
                        $t_ago = get_date_ok($row['date_create'], $row['id']);
                    }
                    if ($row['status'] == 0) {
                        $t_ago = $row['date_create'];
                        if ($lb <> 0) {
                            
                            if ($lb == $user_id) {
                                $st = "<span class=\"label label-warning\"><i class=\"fa fa-gavel\"></i> " . lang('t_list_a_lock_i') . "</span>";
                            }
                            
                            if ($lb <> $user_id) {
                                $st = "<span class=\"label label-default\"><i class=\"fa fa-gavel\"></i> " . lang('t_list_a_lock_u') . " " . nameshort(name_of_user_ret_nolink($lb)) . "</span>";
                            }
                        }
                        if ($lb == 0) {
                            $st = "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> " . lang('t_list_a_hold') . "</span>";
                        }
                    }
                    
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
                    /////////если пользователь///////////////////////////////////////////////////////////////////////////////////////////
                    if ($priv_val == 1) {
                        
                        //ЗАявка не выполнена ИЛИ выполнена мной
                        //ЗАявка не заблокирована ИЛИ заблокирована мной
                        $lo == "no";
                        if (($status_ok_z == 0) || (($status_ok_z == 1) && ($ok_by_z == $user_id_z))) {
                            if (($lock_by_z == 0) || ($lock_by_z == $user_id_z)) {
                                $lo == "yes";
                            }
                        }
                        if ($lo == "yes") {
                            $lock_st = "";
                            $muclass = "";
                        } 
                        else if ($lo == "no") {
                            $lock_st = "disabled=\"disabled\"";
                            $muclass = "text-muted";
                        }
                    }
                    
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
                    /////////если нач отдела/////////////////////////////////////////////////////////////////////////////////////////////
                    else if ($priv_val == 0) {
                        $lock_st = "";
                        $muclass = "";
                    }
                    
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
                    //////////главный админ//////////////////////////////////////////////////////////////////////////////////////////////
                    else if ($priv_val == 2) {
                        $lock_st = "";
                        $muclass = "";
                    }
                    
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
                    
?>




                    <tr id="tr_<?php
                    echo $row['id']; ?>" class="<?php
                    echo $style
?>">
                        <td style=" vertical-align: middle; "><small class="<?php
                    echo $muclass; ?>"><center><?php
                    echo $row['id']; ?></center></small></td>
                        <td style=" vertical-align: middle; "><small class="<?php
                    echo $muclass; ?>"><center><?php
                    echo $prio
?></center></small></td>
                        
                        <td style=" vertical-align: middle; "><a class="<?php
                    echo $muclass; ?> pops"  
                    title="<?php
                    echo make_html($row['subj'], 'no'); ?>"
                    data-content="<small><?php
                    echo str_replace('"', "", cutstr_help_ret(make_html(strip_tags($row['msg']) , 'no'))); ?></small>" 
                    
                    
                    href="ticket?<?php
                    echo $row['hash_name']; ?>"><?php
                    cutstr(make_html($row['subj'], 'no')); ?></a></td>
                        
                        
                        <td style=" vertical-align: middle; "><small class="<?php
                    echo $muclass; ?>">
                        <a href="view_user?<?php
                    echo get_user_hash_by_id($row['client_id']); ?>">
                        <?php
                    echo get_user_val_by_id($row['client_id'], 'fio'); ?>
                        </a>
                        </small></td>
                        <td style=" vertical-align: middle; "><small class="<?php
                    echo $muclass; ?>"><center><time id="c" datetime="<?php
                    echo $row['date_create']; ?>"></time></center></small></td>
                        <td style=" vertical-align: middle; "><small class="<?php
                    echo $muclass; ?>"><center><time id="a" datetime="<?php
                    echo $t_ago; ?>"></time>
<?php
                    echo get_deadline_label($row['id']); ?>
                    </center></small></td>

                        <td style=" vertical-align: middle; "><small class="<?php
                    echo $muclass; ?>">
                        <a href="view_user?<?php
                    echo get_user_hash_by_id($row['user_init_id']); ?>">
                        <?php
                    echo nameshort(name_of_user_ret($row['user_init_id'])); ?>
                        </a>
                        </small></td>

                        <td style=" vertical-align: middle; "><small class="<?php
                    echo $muclass; ?>">
                                <?php
                    echo $to_text; ?>
                            </small></td>
                        <td style=" vertical-align: middle; "><small><center>
                                    <?php
                    echo $st; ?> </center>
                            </small></td>

                    </tr>
                <?php
                }
?>
                </tbody>
                </table>

                </div>



            <?php
            }
        }
        if ($mode == "set_list_count") {
            $pt = $_POST['pt'];
            $v = $_POST['v'];
            if ($pt == "in") {
                $_SESSION['hd.rustem_list_in'] = $v;
            } 
            else if ($pt == "out") {
                $_SESSION['hd.rustem_list_out'] = $v;
            } 
            else if ($pt == "arch") {
                $_SESSION['hd.rustem_list_arch'] = $v;
            }
        }
        
        if ($mode == "sort_list") {
            $pt = $_POST['pt'];
            $sort_type = $_POST['st'];
            
            if ($pt == "in") {
                
                switch ($sort_type) {
                    case 'main':
                        unset($_SESSION['hd.rustem_sort_in']);
                        break;

                    case 'free':
                        $_SESSION['hd.rustem_sort_in'] = "free";
                        break;

                    case 'ok':
                        $_SESSION['hd.rustem_sort_in'] = "ok";
                        break;

                    case 'ilock':
                        $_SESSION['hd.rustem_sort_in'] = "ilock";
                        break;

                    case 'lock':
                        $_SESSION['hd.rustem_sort_in'] = "lock";
                        break;

                    default:
                        unset($_SESSION['hd.rustem_sort_in']);
                }
            } 
            else if ($pt == "out") {
                switch ($sort_type) {
                    case 'main':
                        unset($_SESSION['hd.rustem_sort_out']);
                        break;

                    case 'free':
                        $_SESSION['hd.rustem_sort_out'] = "free";
                        break;

                    case 'ok':
                        $_SESSION['hd.rustem_sort_out'] = "ok";
                        break;

                    case 'ilock':
                        $_SESSION['hd.rustem_sort_out'] = "ilock";
                        break;

                    case 'lock':
                        $_SESSION['hd.rustem_sort_out'] = "lock";
                        break;

                    default:
                        unset($_SESSION['hd.rustem_sort_out']);
                }
            }
        }
        
        if ($mode == "last_news") {
            
            $uid = $_SESSION['helpdesk_user_id'];
            $unit_user = unit_of_user($uid);
            $priv_val = priv_status($uid);
            $c = 4;
            $start = 10;
            
            if (isset($_POST['v'])) {
                $c = $_POST['v'];
                $start = ($_POST['v'] + 5);
            }
            
            //$_POST['v']
            
            $units = explode(",", $unit_user);
            $units = implode("', '", $units);
            $ee = explode(",", $unit_user);
            foreach ($ee as $key => $value) {
                $in_query = $in_query . ' :val_' . $key . ', ';
            }
            $in_query = substr($in_query, 0, -2);
            foreach ($ee as $key => $value) {
                $vv[":val_" . $key] = $value;
            }
            
            $u_type = get_user_val('is_client');
            
            if ($u_type == "0") {
                
                /*
                
                if ($priv_val == "0") {
                
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (unit_id IN ('.$in_query.') or user_init_id=:uid) order by last_update DESC limit :c');
                $paramss=array(':uid'=>$uid, ':c'=>$c);
                $stmt->execute(array_merge($vv,$paramss));
                $res1 = $stmt->fetchAll();
                
                
                
                foreach($res1 as $rews) {
                    $at=get_last_action_ticket($rews['id']);
                
                    $who_action=get_who_last_action_ticket($rews['id']);
                    $results[] = array(
                        'name' => $rews['id'],
                        'at' => $at,
                        'hash' => $rews['hash_name'],
                        'time' => $rews['last_update']
                    );
                
                
                }
                }
                else if ($priv_val == "1") {
                
                
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (
                ((find_in_set(:uid,user_to_id)) or (find_in_set(:n,user_to_id) and unit_id IN ('.$in_query.')))
                or user_init_id=:uid2) order by last_update DESC limit :c');
                $paramss=array(':uid'=>$uid, ':n'=>'0', ':uid2'=>$uid, ':c'=>$c);
                $stmt->execute(array_merge($vv,$paramss));
                
                
                
                $stmt->execute(array_merge($paramss));
                
                
                
                
                
                
                
                $res1 = $stmt->fetchAll();
                
                
                
                
                foreach($res1 as $rews) {
                
                
                    $at=get_last_action_ticket($rews['id']);
                    $who_action=get_who_last_action_ticket($rews['id']);
                
                
                    $results[] = array(
                        'name' => $rews['id'],
                        'at' => $at,
                        'hash' => $rews['hash_name'],
                        'time' => $rews['last_update']
                    );
                
                }
                
                
                
                }
                else if ($priv_val == "2") {
                
                
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets order by last_update DESC limit :c');
                $stmt->execute(array(':c'=>$c));
                $res1 = $stmt->fetchAll();
                
                
                
                
                
                foreach($res1 as $rews) {
                    $at=get_last_action_ticket($rews['id']);
                    $who_action=get_who_last_action_ticket($rews['id']);
                
                
                    $results[] = array(
                        'name' => $rews['id'],
                        'at' => $at,
                        'hash' => $rews['hash_name'],
                        'time' => $rews['last_update']
                    );
                
                }
                
                
                
                }
                */
            } 
            else if ($u_type == "1") {
                
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where user_init_id=:cid and client_id=:cid2 order by last_update DESC limit :c');
                
                $stmt->execute(array(
                    ':cid' => $uid,
                    ':cid2' => $uid,
                    ':c' => $c
                ));
                $res1 = $stmt->fetchAll();
                
                foreach ($res1 as $rews) {
                    $at = get_last_action_ticket($rews['id']);
                    
                    $who_action = get_who_last_action_ticket($rews['id']);
                    $results[] = array(
                        'name' => $rews['id'],
                        'at' => $at,
                        'hash' => $rews['hash_name'],
                        'time' => $rews['last_update']
                    );
                }
            }
            if (empty($results)) {
?>
                <div id="" class="well well-large well-transparent lead">
                    <center>
                        <?php
                echo lang('MSG_no_records'); ?>
                    </center>
                </div>
            <?php
            } 
            else {
?><table class="table table-hover" style="margin-bottom: 0px;" id=""> <?php
                foreach ($results as $arr) {
?>

                    <tr><td style=" width: 100px; vertical-align: inherit;"><small><i class="fa fa-tag"></i> </small><a href="ticket?<?php
                    echo $arr['hash']; ?>"><small><?php
                    echo lang('TICKET_name'); ?> #<?php
                    echo $arr['name']; ?></small></a></td><td><small><?php
                    echo $arr['at']; ?></small></td>
                    <td style=" width: 110px; vertical-align: inherit;"><small style="float:right;" class="text-muted "> <time id="b" datetime="<?php
                    echo $arr['time']; ?>"></time></small></td></tr>

                <?php
                }
?></table><small><center><a id="more_news" value="<?php
                echo $start
?>" class="btn btn-default btn-xs"><?php
                echo lang('last_more'); ?></a></center></small><?php
            }
        }
        
        if ($mode == "update_status_time") {
            $uid = $_SESSION['helpdesk_user_id'];
            $stmt = $dbConnection->prepare('update users set last_time=:n where id=:cid');
            $stmt->execute(array(
                ':cid' => $uid,
                ':n' => $CONF['now_dt']
            ));
        }
        
        if ($mode == "check_update") {
            $pm = ($_POST['type']);
            $uid = $_SESSION['helpdesk_user_id'];
            $lu = ($_POST['last_update']);
            
            $current_ticket_update = get_last_ticket($pm, $uid);
            
            if (strtotime($current_ticket_update) > strtotime($lu)) {
                echo $current_ticket_update;
            }
            if (strtotime($current_ticket_update) <= strtotime($lu)) {
                echo "no";
            }
            
            //update
            $stmt = $dbConnection->prepare('update users set last_time=:n where id=:cid');
            $stmt->execute(array(
                ':cid' => $uid,
                ':n' => $CONF['now_dt']
            ));
        }
        
        if ($mode == "get_noty_actions") {
            $type_op = ($_POST['type']);
            $uid = $_SESSION['helpdesk_user_id'];
            $ticket_id = $_POST['ticket_id'];
            
            $priv_val = priv_status($uid);
            
            switch ($type_op) {
                case 'ticket_create':
                    $at = get_last_msg_ticket($ticket_id, 'create');
                    break;

                case 'ticket_refer':
                    $at = get_last_msg_ticket($ticket_id, 'refer');
                    break;

                case 'ticket_ok':
                    $at = get_last_msg_ticket($ticket_id, 'ok');
                    break;

                case 'ticket_no_ok':
                    $at = get_last_msg_ticket($ticket_id, 'no_ok');
                    break;

                case 'ticket_lock':
                    $at = get_last_msg_ticket($ticket_id, 'lock');
                    break;

                case 'ticket_unlock':
                    $at = get_last_msg_ticket($ticket_id, 'unlock');
                    break;

                case 'ticket_comment':
                    $at = get_last_msg_ticket($ticket_id, 'comment');
                    break;
            }
            
            $results[] = array(
                'url' => $CONF['hostname'],
                'up' => lang('JS_up') ,
                
                //обновлено
                'ticket' => lang('JS_ticket') ,
                
                //Заявка
                'name' => $ticket_id,
                'at' => $at,
                
                //слова
                'hash' => get_ticket_hash_by_id($ticket_id) ,
                'time' => "<time id=\"b\" datetime=\"" . date("Y-m-d H:i:s") . "\"></time>"
                
                //время
                
                
            );
            
            /*
            if ($priv_val == "0") {
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where id=:tid');
                
                $paramss=array(':uid'=>$uid, ':lu'=>$lu);
                $stmt->execute(array_merge($vv,$paramss));
                $res1 = $stmt->fetchAll();
                foreach($res1 as $rews) {
            
                    $at=get_last_action_ticket($rews['id']);
            
                    $who_action=get_who_last_action_ticket($rews['id']);
                    if ($who_action <> $uid) {
                        $results[] = array(
                            'url' => $CONF['hostname'],
                            'up' => lang('JS_up'),
                            'ticket' => lang('JS_ticket'),
                            'name' => $rews['id'],
                            'at' => $at,
                            'hash' => $rews['hash_name'],
                            'time' => "<time id=\"b\" datetime=\"".$rews['last_update']."\"></time>"
                        );
                    }
            
                }
            }
            
            
            else if ($priv_val == "1") {
            //find_in_set(:uid,user_to_id)
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (
            ((find_in_set(:uid,user_to_id)) or (find_in_set(:n,user_to_id) and unit_id IN ('.$in_query.')))
            or user_init_id=:uid2) and last_update > :lu');
                $paramss=array(':uid'=>$uid, ':lu'=>$lu, ':uid2'=>$uid, ':n'=>'0');
                $stmt->execute(array_merge($vv,$paramss));
                $res1 = $stmt->fetchAll();
                foreach($res1 as $rews) {
            
            
                    $at=get_last_action_ticket($rews['id']);
                    $who_action=get_who_last_action_ticket($rews['id']);
                    if ($who_action <> $uid) {
            
                        $results[] = array(
                            'url' => $CONF['hostname'],
                            'up' => lang('JS_up'),
                            'ticket' => lang('JS_ticket'),
                            'name' => $rews['id'],
                            'at' => $at,
                            'hash' => $rews['hash_name'],
                            'time' => "<time id=\"b\" datetime=\"".$rews['last_update']."\"></time>"
                        );
                    }
                }
            
            
            
            }
            else if ($priv_val == "2") {
            
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where last_update > :lu');
                $stmt->execute(array(':lu'=>$lu));
                $res1 = $stmt->fetchAll();
                foreach($res1 as $rews) {
            
            
                    $at=get_last_action_ticket($rews['id']);
                    $who_action=get_who_last_action_ticket($rews['id']);
                    if ($who_action <> $uid) {
            
                        $results[] = array(
                            'url' => $CONF['hostname'],
                            'up' => lang('JS_up'),
                            'ticket' => lang('JS_ticket'),
                            'name' => $rews['id'],
                            'at' => $at,
                            'hash' => $rews['hash_name'],
                            
                            'time' => "<time id=\"b\" datetime=\"".$rews['last_update']."\"></time>"
                        );
                    }
                }
            
            
            
            }
            */
            
            print json_encode($results);
        }
        if ($mode == "push_msg_action2user") {
            
            push_msg_action2user($_POST['user'], $_POST['op']);
        }
        

        if ($mode == "delete_user_file") {
            $uniq_code = $_POST['uniq_code'];
            
            $stmt = $dbConnection->prepare("SELECT *
                            from user_files where file_hash=:id");
            $stmt->execute(array(
                ':id' => $uniq_code
            ));
            $result = $stmt->fetchAll();
            
            if (!empty($result)) {
                foreach ($result as $row) {
                    
                    unlink(ZENLIX_DIR . "/upload_files/" . $row['file_hash'] . "." . $row['file_ext']);
                }
            }
            $stmt = $dbConnection->prepare('delete from user_files where file_hash=:id');
            $stmt->execute(array(
                ':id' => $uniq_code
            ));
        }

        if ($mode == "delete_post_file") {
            $uniq_code = $_POST['uniq_code'];
            
            $stmt = $dbConnection->prepare("SELECT *
                            from files where file_hash=:id");
            $stmt->execute(array(
                ':id' => $uniq_code
            ));
            $result = $stmt->fetchAll();
            
            if (!empty($result)) {
                foreach ($result as $row) {
                    
                    unlink(ZENLIX_DIR . "/upload_files/" . $row['file_hash'] . "." . $row['file_ext']);
                }
            }
            $stmt = $dbConnection->prepare('delete from files where file_hash=:id');
            $stmt->execute(array(
                ':id' => $uniq_code
            ));
        }
        



        if ($mode == "set_zenlix_logo") {
            class SimpleImage
            {
                
                var $image;
                var $image_type;
                
                function load($filename) {
                    $image_info = getimagesize($filename);
                    $this->image_type = $image_info[2];
                    if ($this->image_type == IMAGETYPE_JPEG) {
                        $this->image = imagecreatefromjpeg($filename);
                    } 
                    elseif ($this->image_type == IMAGETYPE_GIF) {
                        $this->image = imagecreatefromgif($filename);
                    } 
                    elseif ($this->image_type == IMAGETYPE_PNG) {
                        $this->image = imagecreatefrompng($filename);
                    }
                }
                function save($filename, $image_type = IMAGETYPE_PNG, $compression = 100, $permissions = null) {
                    if ($image_type == IMAGETYPE_JPEG) {
                        imagejpeg($this->image, $filename, $compression);
                    } 
                    elseif ($image_type == IMAGETYPE_GIF) {
                        imagegif($this->image, $filename);
                    } 
                    elseif ($image_type == IMAGETYPE_PNG) {
                        imagepng($this->image, $filename);
                    }
                    if ($permissions != null) {
                        chmod($filename, $permissions);
                    }
                }
                function output($image_type = IMAGETYPE_JPEG) {
                    if ($image_type == IMAGETYPE_JPEG) {
                        imagejpeg($this->image);
                    } 
                    elseif ($image_type == IMAGETYPE_GIF) {
                        imagegif($this->image);
                    } 
                    elseif ($image_type == IMAGETYPE_PNG) {
                        imagepng($this->image);
                    }
                }
                function getWidth() {
                    return imagesx($this->image);
                }
                function getHeight() {
                    return imagesy($this->image);
                }
                function resizeToHeight($height) {
                    $ratio = $height / $this->getHeight();
                    $width = $this->getWidth() * $ratio;
                    $this->resize($width, $height);
                }
                function resizeToWidth($width) {
                    $ratio = $width / $this->getWidth();
                    $height = $this->getheight() * $ratio;
                    $this->resize($width, $height);
                }
                function scale($scale) {
                    $width = $this->getWidth() * $scale / 100;
                    $height = $this->getheight() * $scale / 100;
                    $this->resize($width, $height);
                }
                function resize($width, $height) {
                    $new_image = imagecreatetruecolor($width, $height);
                    imagealphablending($new_image, false);
                    imagesavealpha($new_image, true);
                    imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth() , $this->getHeight());
                    $this->image = $new_image;
                }
            }
            
            if ($_FILES["file"]) {
                $output_dir = ZENLIX_DIR. "/upload_files/avatars/";
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
                $fileName_norm_logo = $fhash . "_logo." . $ext;
                
                //echo $_FILES["file"]["size"];
                
                if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] < 2000000) && in_array($extension, $allowedExts)) {
                    
                    if ($_FILES["file"]["error"] > 0) {
                        
                        //echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
                        
                        
                    } 
                    else {
                        
                        move_uploaded_file($_FILES["file"]["tmp_name"], $output_dir . $fileName_norm);
                        $nf = $output_dir . $fileName_norm;
                        $nf_logo = $output_dir . $fileName_norm_logo;
                        $image = new SimpleImage();
                        $image->load($nf);
                        $image->resizeToHeight(128);
                        $image->save($nf);
                        
                        $image_logo = new SimpleImage();
                        $image_logo->load($nf);
                        $image_logo->resizeToHeight(40);
                        $image_logo->save($nf_logo);
                        
                        //$u = $_SESSION['helpdesk_user_id'];
                        //$stmt = $dbConnection->prepare('update users set usr_img = :uimg where id=:uid ');
                        //$stmt->execute(array(':uimg' => $fileName_norm, ':uid' => $u));
                        update_val_by_key("logo_img", $fileName_norm);
                        
                        //}
                        
                        //$_FILES["file"]["name"];
                        
                        
                    }
                } 
                else {
                    
                    //echo $_FILES["file"]["type"]."<br />";
                    //echo "Invalid file";
                    
                    
                }
            }
            header("Location: " . site_proto() . $_SERVER['HTTP_HOST'] . $CONF['hostname'] . "config");
        }
        
        if ($mode == "set_user_avatar") {
            include_once ("library/SimpleImage/SimpleImage.php");
            if ($_FILES["file"]) {
                
                //echo "ok";
                $output_dir = ZENLIX_DIR."/upload_files/avatars/";
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
            header("Location: " . site_proto() . $_SERVER['HTTP_HOST'] . $CONF['hostname'] . "profile");
        }
        
        if ($mode == "delete_manual_file") {
            $uniq_code = $_POST['uniq_code'];
            
            $stmt = $dbConnection->prepare("SELECT *
                            from files where file_hash=:id and obj_type=1");
            $stmt->execute(array(
                ':id' => $uniq_code
            ));
            $result = $stmt->fetchAll();
            
            if (!empty($result)) {
                foreach ($result as $row) {
                    
                    unlink(ZENLIX_DIR . "/upload_files/" . $row['file_hash'] . "." . $row['file_ext']);
                }
            }
            $stmt = $dbConnection->prepare('delete from files where file_hash=:id');
            $stmt->execute(array(
                ':id' => $uniq_code
            ));
        }



        if ($mode == "upload_user_file") {
            $msg=NULL;
            $output_dir = ZENLIX_DIR."/upload_files/";
            $hn = $_POST['post_hash'];
            $type="1";
            if ($_POST['type']) {
                $type=$_POST['type'];
            }
            
            $maxsize = get_conf_param('file_size');
            
            $good_files = explode("|", get_conf_param('file_types'));
            
            $acceptable = $good_files;
            
            if (isset($_FILES["myfile"])) {
                $ret = array();
                
                $error = $_FILES["myfile"]["error"];
                $flag = false;
                
                //You need to handle  both cases
                //If Any browser does not support serializing of multiple files using FormData()
                if (!is_array($_FILES["myfile"]["name"]))
                
                //single file
                {
                    $fileName = $_FILES["myfile"]["name"];
                    $filetype = $_FILES["myfile"]["type"];
                    $filesize = $_FILES["myfile"]["size"];
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    if ($_FILES["myfile"]["size"] > $maxsize) {
                        $flag = true;
                        $msg = lang('PORTAL_file_big');
                    }
                    if ((!in_array($ext, $acceptable)) && (!empty($_FILES["myfile"]["type"]))) {
                        $flag = true;
                        $msg = lang('PORTAL_file_ext');
                    }
                    
                    if ($flag == false) {
                        
                        $fhash = randomhash();
                        
                        //$ext = pathinfo($fileName, PATHINFO_EXTENSION);
                        $fileName_norm = $fhash . "." . $ext;
                        
                        move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir . $fileName_norm);
                        
                        $stmt = $dbConnection->prepare('insert into user_files 
        (user_id, original_name, file_hash, file_type, file_size, file_ext, obj_type) values 
        (:user_id, :original_name, :file_hash, :file_type, :file_size, :file_ext, :obj_type)');
                        $stmt->execute(array(
                            ':user_id' => get_user_val_by_hash($hn, 'id'),
                            ':original_name' => $fileName,
                            ':file_hash' => $fhash,
                            ':file_type' => $filetype,
                            ':file_size' => $filesize,
                            ':file_ext' => $ext,
                            ':obj_type' => $type
                        ));
                    }
                    
                    //{msg: "Upload limit reached", status: "error", code: "403"}
                    
                    if ($flag == false) {
                        $status = "ok";
                    } 
                    else if ($flag == true) {
                        $status = "error";
                    }
                    
                    $results[] = array(
                        'uniq_code' => $fhash,
                        'code' => 501,
                        'status' => $status,
                        'msg' => $msg
                    );
                    
                    print json_encode($results);
                }
            }
        }




        if ($mode == "upload_manual_file") {
            $msg=NULL;
            $output_dir = ZENLIX_DIR."/upload_files/";
            $hn = $_POST['post_hash'];
            $maxsize = get_conf_param('file_size');
            
            $good_files = explode("|", get_conf_param('file_types'));
            
            $acceptable = $good_files;
            
            if (isset($_FILES["myfile"])) {
                $ret = array();
                
                $error = $_FILES["myfile"]["error"];
                $flag = false;
                
                //You need to handle  both cases
                //If Any browser does not support serializing of multiple files using FormData()
                if (!is_array($_FILES["myfile"]["name"]))
                
                //single file
                {
                    $fileName = $_FILES["myfile"]["name"];
                    $filetype = $_FILES["myfile"]["type"];
                    $filesize = $_FILES["myfile"]["size"];
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    if ($_FILES["myfile"]["size"] > $maxsize) {
                        $flag = true;
                        $msg = lang('PORTAL_file_big');
                    }
                    if ((!in_array($ext, $acceptable)) && (!empty($_FILES["myfile"]["type"]))) {
                        $flag = true;
                        $msg = lang('PORTAL_file_ext');
                    }
                    
                    if ($flag == false) {
                        
                        $fhash = randomhash();
                        
                        //$ext = pathinfo($fileName, PATHINFO_EXTENSION);
                        $fileName_norm = $fhash . "." . $ext;
                        
                        move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir . $fileName_norm);
                        
                        $stmt = $dbConnection->prepare('insert into files 
        (ticket_hash, original_name, file_hash, file_type, file_size, file_ext, obj_type) values 
        (:ticket_hash, :original_name, :file_hash, :file_type, :file_size, :file_ext, :obj_type)');
                        $stmt->execute(array(
                            ':ticket_hash' => $hn,
                            ':original_name' => $fileName,
                            ':file_hash' => $fhash,
                            ':file_type' => $filetype,
                            ':file_size' => $filesize,
                            ':file_ext' => $ext,
                            ':obj_type' => '1'
                        ));
                    }
                    
                    //{msg: "Upload limit reached", status: "error", code: "403"}
                    
                    if ($flag == false) {
                        $status = "ok";
                    } 
                    else if ($flag == true) {
                        $status = "error";
                    }
                    
                    $results[] = array(
                        'uniq_code' => $fhash,
                        'code' => 501,
                        'status' => $status,
                        'msg' => $msg
                    );
                    
                    print json_encode($results);
                }
            }
        }
        
        if ($mode == "upload_post_file") {

if (!isset($_POST['type'])) {
    $_POST['type']="1";
}

            $type=$_POST['type'];
            $msg=NULL;
            $output_dir = ZENLIX_DIR."/upload_files/";
            $hn = $_POST['post_hash'];
            $maxsize = get_conf_param('file_size');
            
            $good_files = explode("|", get_conf_param('file_types'));
            
            $acceptable = $good_files;
            
            if (isset($_FILES["myfile"])) {
                $ret = array();
                
                $error = $_FILES["myfile"]["error"];
                $flag = false;
                
                //You need to handle  both cases
                //If Any browser does not support serializing of multiple files using FormData()
                if (!is_array($_FILES["myfile"]["name"]))
                
                //single file
                {
                    $fileName = $_FILES["myfile"]["name"];
                    $filetype = $_FILES["myfile"]["type"];
                    $filesize = $_FILES["myfile"]["size"];
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    if ($_FILES["myfile"]["size"] > $maxsize) {
                        $flag = true;
                        $msg = lang('PORTAL_file_big');
                    }
                    if ((!in_array($ext, $acceptable)) && (!empty($_FILES["myfile"]["type"]))) {
                        $flag = true;
                        $msg = lang('PORTAL_file_ext');
                    }
                    
                    if ($flag == false) {
                        
                        $fhash = randomhash();
                        
                        //$ext = pathinfo($fileName, PATHINFO_EXTENSION);
                        $fileName_norm = $fhash . "." . $ext;
                        
                        move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir . $fileName_norm);
                        
                        $stmt = $dbConnection->prepare('insert into files 
        (ticket_hash, original_name, file_hash, file_type, file_size, file_ext, obj_type) values 
        (:ticket_hash, :original_name, :file_hash, :file_type, :file_size, :file_ext, :obj_type)');
                        $stmt->execute(array(
                            ':ticket_hash' => $hn,
                            ':original_name' => $fileName,
                            ':file_hash' => $fhash,
                            ':file_type' => $filetype,
                            ':file_size' => $filesize,
                            ':file_ext' => $ext,
                            ':obj_type' => $type
                        ));
                    }
                    
                    //{msg: "Upload limit reached", status: "error", code: "403"}
                    
                    if ($flag == false) {
                        $status = "ok";
                    } 
                    else if ($flag == true) {
                        $status = "error";
                    }
                    
                    $results[] = array(
                        'uniq_code' => $fhash,
                        'code' => 501,
                        'status' => $status,
                        'msg' => $msg
                    );
                    
                    print json_encode($results);
                }
            }
        }
        
        //del_profile_img
        if ($mode == "del_profile_img") {
            
            $id = $_SESSION['helpdesk_user_id'];
            $stmt = $dbConnection->prepare('update users set usr_img=:s where id=:id');
            $stmt->execute(array(
                ':id' => $id,
                ':s' => ''
            ));
        }
        
        //del_profile_img
        if ($mode == "del_logo_img") {
            update_val_by_key("logo_img", '');
        }
        
        if ($mode == "edit_profile_main_client") {
            $fio = ($_POST['fio']);
            $m = strtolower($_POST['mail']);
            $id = $_SESSION['helpdesk_user_id'];
            $langu = ($_POST['lang']);
            $skype = ($_POST['skype']);
            $tel = ($_POST['tel']);
            $adr = ($_POST['adr']);
            
            $ec = 0;
            
            if (!validate_email($m)) {
                $ec = 1;
            }
            if (!validate_exist_mail($m)) {
                $ec = 1;
            }
            
            if ($ec == 0) {
                $stmt = $dbConnection->prepare('update users set 
                    fio=:fio, 
                    skype=:s, 
                    tel=:t, 
                    email=:m, 
                    lang=:langu,
                    adr=:adr,
                    pb=:pb
                    where id=:id');
                $stmt->execute(array(
                    ':id' => $id,
                    ':m' => $m,
                    ':langu' => $langu,
                    ':s' => $skype,
                    ':t' => $tel,
                    ':adr' => $adr,
                    ':pb' => $_POST['pb'],
                    ':fio' => $fio
                ));
?>
                <div class="alert alert-success">
                    <?php
                echo lang('PROFILE_msg_ok'); ?>
                </div>
            <?php
            }
            if ($ec == 1) {
?>
                <div class="alert alert-danger">
                    <?php
                echo lang('PROFILE_msg_error'); ?>
                </div>
            <?php
            }
        }
        
        if ($mode == "edit_profile_main") {
            $msg=NULL;
            $validator = new GUMP();
            $_POST = $validator->sanitize($_POST);
            
            $rules = array(
                'fio' => 'required|max_len,100|min_len,6',
                'mail' => 'required|valid_email'
            );
            $filters = array(
                'fio' => 'sanitize_string|trim'
            );
            
            GUMP::set_field_name("fio", lang('WORKER_fio'));
            GUMP::set_field_name("mail", lang('P_mail'));
            
            $_POST = $validator->filter($_POST, $filters);
            
            $validated = $validator->validate($_POST, $rules);
            
            if ($validated === true) {
                $m = strtolower($_POST['mail']);
                $id = $_SESSION['helpdesk_user_id'];
                $langu = ($_POST['lang']);
                $skype = ($_POST['skype']);
                $tel = ($_POST['tel']);
                $adr = ($_POST['adr']);
                $fio = ($_POST['fio']);
                $posada = ($_POST['posada']);
                //$unitss = ($_POST['unit']);
                $noty = $_POST['user_layot'];
                
                $ec = 0;
                if (!validate_email($m)) {
                    $ec = 1;
                }
                if (!validate_exist_mail($m)) {
                    $ec = 1;
                }
                if ($ec == 0) {
                    $stmt = $dbConnection->prepare('update users set fio=:fio, skype=:s, tel=:t, email=:m, lang=:langu,
                adr=:adr,posada=:posada,noty_layot=:noty,pb=:pb where id=:id');
                    $stmt->execute(array(
                        ':id' => $id,
                        ':m' => $m,
                        ':langu' => $langu,
                        ':s' => $skype,
                        ':t' => $tel,
                        ':adr' => $adr,
                        ':posada' => $posada,
                        ':fio' => $fio,
                        ':noty' => $noty,
                        ':pb' => $_POST['pb']
                    ));
?>
                <div class="alert alert-success">
                    <?php
                    echo lang('PROFILE_msg_ok'); ?>
                </div>
            <?php
                }
                if ($ec == 1) {
?>
                <div class="alert alert-danger">
                    <?php
                    echo lang('PROFILE_msg_error'); ?>
                </div>
            <?php
                }
            } 
            else {
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($validator->get_readable_errors(false) as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
                echo $msg;
            }
        }
        
        if ($mode == "gen_new_api") {
            $nc = md5(time());
            
            $stmt = $dbConnection->prepare('update users set api_key=:ak where id=:id');
            $stmt->execute(array(
                ':id' => $_SESSION['helpdesk_user_id'],
                ':ak' => $nc
            ));
            
            echo $nc;
        }
        
        if ($mode == "edit_profile_pass") {
            $msg=NULL;
            $validator = new GUMP();
            $_POST = $validator->sanitize($_POST);
            
            $rules = array(
                'old_pass' => 'required|max_len,100',
                'new_pass' => 'required|max_len,100|min_len,6',
                'new_pass2' => 'required|max_len,100|min_len,6'
            );
            $filters = array(
                'old_pass' => 'sanitize_string|trim',
                'new_pass' => 'sanitize_string|trim',
                'new_pass2' => 'sanitize_string|trim',
            );
            
            GUMP::set_field_name("old_pass", lang('P_pass_old'));
            GUMP::set_field_name("new_pass", lang('P_pass_new'));
            GUMP::set_field_name("new_pass2", lang('P_pass_new_re'));
            
            $_POST = $validator->filter($_POST, $filters);
            
            $validated = $validator->validate($_POST, $rules);
            
            if ($validated === true) {
                
                $p_old = md5(($_POST['old_pass']));
                $p_new = md5(($_POST['new_pass']));
                $p_new2 = md5(($_POST['new_pass2']));
                $id = ($_SESSION['helpdesk_user_id']);
                
                $stmt = $dbConnection->prepare('select pass from users where id=:id');
                $stmt->execute(array(
                    ':id' => $id
                ));
                $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $pass_orig = $total_ticket['pass'];
                
                $ec = 0;
                
                if ($pass_orig <> $p_old) {
                    $ec = 1;
                    $text = lang('PROFILE_msg_pass_err');
                }
                
                if ($p_new <> $p_new2) {
                    $ec = 1;
                    $text = lang('PROFILE_msg_pass_err2');
                }
                
                if (strlen($p_new) < 3) {
                    $ec = 1;
                    $text = lang('PROFILE_msg_pass_err3');
                }
                
                if ($ec == 0) {
                    
                    $stmt = $dbConnection->prepare('update users set pass=:p_new, api_key=:ak where id=:id');
                    $stmt->execute(array(
                        ':id' => $id,
                        ':p_new' => $p_new,
                        ':ak' => md5(time())
                    ));
                    
                    session_destroy();
                    unset($_SESSION);
                    session_unset();
                    setcookie('authhash_uid', "");
                    setcookie('authhash_code', "");
                    unset($_COOKIE['authhash_uid']);
                    unset($_COOKIE['authhash_code']);
?>
                <div class="alert alert-success">
                    <?php
                    echo lang('PROFILE_msg_pass_ok'); ?>
                </div>
            <?php
                }
                if ($ec == 1) {
?>
                <div class="alert alert-danger">
                    <?php
                    echo lang('PROFILE_msg_te'); ?> <?php
                    echo $text; ?>
                </div>
            <?php
                }
            } 
            else {
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($validator->get_readable_errors(false) as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
                echo $msg;
            }
        }
        
        if ($mode == "add_user_approve") {
            $msg=NULL;
            $fio = ($_POST['fio']);
            $login = ($_POST['login']);
            $posada = ($_POST['posada']);
            $pid = ($_POST['pidrozdil']);
            $tel = ($_POST['tel']);
            $adr = ($_POST['adr']);
            $mail = strtolower($_POST['mail']);
            $skype = ($_POST['skype']);
            $uf = $_SESSION['helpdesk_user_id'];
            GUMP::set_field_name("fio", lang('USERS_fio'));
            GUMP::set_field_name("login", lang('USERS_login'));
            $is_valid = GUMP::is_valid($_POST, array(
                'fio' => 'required|max_len,100|min_len,1',
                'login' => 'required|max_len,50|min_len,1'
            ));
            
            if ($is_valid === true) {
                $r = true;
                $stmt = $dbConnection->prepare('insert into approved_info
(fio,login,tel, unit_desc, adr, email, posada,skype,type_op, user_from, date_app)
VALUES (:fio, :login, :tel, :unit_desc, :adr, :email, :posada,:skype,:type_op, :user_from,  :n)');
                
                $stmt->execute(array(
                    ':fio' => $fio,
                    ':tel' => $tel,
                    ':login' => $login,
                    ':unit_desc' => $pid,
                    ':adr' => $adr,
                    ':email' => $mail,
                    ':posada' => $posada,
                    ':skype' => $skype,
                    ':type_op' => 'add',
                    ':user_from' => $uf,
                    ':n' => $CONF['now_dt']
                ));
            } 
            else {
                
                //print_r($is_valid);
                $r = false;
                
                //$msg=$is_valid;
                
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($is_valid as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
            }
            $results[] = array(
                'res' => $r,
                'msg' => $msg
            );
            print json_encode($results);
            
            /*
            $stmt = $dbConnection->prepare('insert into approved_info
            (fio,login,tel, unit_desc, adr, email, posada,skype,type_op, user_from, date_app)
            VALUES (:fio, :login, :tel, :unit_desc, :adr, :email, :posada,:skype,:type_op, :user_from,  :n)');
            
            $stmt->execute(array(':fio' => $fio, ':tel' => $tel, ':login' => $login, ':unit_desc' => $pid, ':adr' => $adr, ':email' => $mail, ':posada' => $posada, ':skype' => $skype, ':type_op' => 'add', ':user_from' => $uf, ':n' => $CONF['now_dt']));
            */
        }
        
        if ($mode == "edit_user_approve") {
            $msg=NULL;
            $fio = ($_POST['fio']);
            $login = ($_POST['login']);
            $posada = ($_POST['posada']);
            //$pid = ($_POST['pidrozdil']);
            $tel = ($_POST['tel']);
            $adr = ($_POST['adr']);
            $mail = strtolower($_POST['mail']);
            $skype = ($_POST['skype']);
            $uf = $_SESSION['helpdesk_user_id'];
            $cid = get_user_val_by_hash($_POST['cid'], 'id');
            
            GUMP::set_field_name("fio", lang('USERS_fio'));
            GUMP::set_field_name("login", lang('USERS_login'));
            
            $is_valid = GUMP::is_valid($_POST, array(
                'fio' => 'required|max_len,100|min_len,1',
                'login' => 'required|max_len,50|min_len,1|alpha_numeric'
            ));
            
            if ($is_valid === true) {
                $r = true;
                
                $stmt = $dbConnection->prepare('insert into approved_info
(fio,login,tel, adr, email, posada,skype,type_op, user_from, client_id, date_app)
VALUES (:fio, :login, :tel, :adr, :email, :posada,:skype,:type_op, :user_from, :cid,  :n)');
                
                $stmt->execute(array(
                    ':fio' => $fio,
                    ':tel' => $tel,
                    ':login' => $login,
                    ':adr' => $adr,
                    ':email' => $mail,
                    ':posada' => $posada,
                    ':skype' => $skype,
                    ':type_op' => 'edit',
                    ':user_from' => $uf,
                    ':cid' => $cid,
                    ':n' => $CONF['now_dt']
                ));
            } 
            else {
                
                //print_r($is_valid);
                $r = false;
                
                //$msg=$is_valid;
                
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($is_valid as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
            }
            $results[] = array(
                'res' => $r,
                'msg' => $msg
            );
            print json_encode($results);
        }
        
        if ($mode == "arch_now") {
            $user = ($_POST['user']);
            $tid = ($_POST['tid']);
            
            $stmt = $dbConnection->prepare('SELECT arch FROM tickets where id=:tid');
            $stmt->execute(array(
                ':tid' => $tid
            ));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $s = $fio['arch'];
            
            if ($s == "0") {
                
                $stmt = $dbConnection->prepare('update tickets set arch=:n1, last_update=:n where id=:tid');
                $stmt->execute(array(
                    ':tid' => $tid,
                    ':n1' => '1',
                    ':n' => $CONF['now_dt']
                ));
            }
            if ($s == "1") {
                $stmt = $dbConnection->prepare('update tickets set arch=:n1, last_update=:n where id=:tid');
                $stmt->execute(array(
                    ':tid' => $tid,
                    ':n1' => '0',
                    ':n' => $CONF['now_dt']
                ));
            }
            
            $unow = $_SESSION['helpdesk_user_id'];
            
            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:ar, :n, :unow, :tid)');
            $stmt->execute(array(
                ':tid' => $tid,
                ':unow' => $unow,
                ':ar' => 'arch',
                ':n' => $CONF['now_dt']
            ));
        }
        
        if ($mode == "status_no_ok") {
            $user = ($_POST['user']);
            $tid = ($_POST['tid']);
            $hs = explode(",", get_ticket_action_priv($tid));
            if (in_array("ok", $hs)) {
                $stmt = $dbConnection->prepare('SELECT status, ok_by,lock_by FROM tickets where id=:tid');
                $stmt->execute(array(
                    ':tid' => $tid
                ));
                $fio = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $st = $fio['status'];
                $ob = $fio['ok_by'];
                
                $lb = $fio['lock_by'];
                
                $ps = priv_status($ob);
                
                if ($st == "0") {
                    
                    if ($lb != "0") {
                        $stmt = $dbConnection->prepare('update tickets set ok_by=:user, status=:s, ok_date=:n, last_update=:nz where id=:tid');
                        $stmt->execute(array(
                            ':s' => '1',
                            ':tid' => $tid,
                            ':user' => $user,
                            ':n' => $CONF['now_dt'],
                            ':nz' => $CONF['now_dt']
                        ));
                        
                        $unow = $_SESSION['helpdesk_user_id'];
                        
                        $stmt = $dbConnection->prepare('INSERT INTO ticket_log 
            (msg, date_op, init_user_id, ticket_id)
            values (:ok, :n, :unow, :tid)');
                        $stmt->execute(array(
                            ':ok' => 'ok',
                            ':tid' => $tid,
                            ':unow' => $unow,
                            ':n' => $CONF['now_dt']
                        ));
                        send_notification('ticket_ok', $tid);
?>

                <div class="alert alert-success"><i class="fa fa-check"></i> <?php
                        echo lang('TICKET_msg_OK'); ?></div>

            <?php
                    } 
                    else if ($lb == "0") {
?>
<div class="alert alert-danger"><?php
                        echo lang('TICKET_msg_OK_error'); ?> <?php
                        echo name_of_user($ob); ?></div>
<?php
                    }
                } 
                else if ($st == "1") {
?>

                <div class="alert alert-danger"><?php
                    echo lang('TICKET_msg_OK_error'); ?> <?php
                    echo name_of_user($ob); ?></div>

            <?php
                }
            }
        }
        
        /*
                'mode': 'mailers_send',
                'subj_mailers': encodeURIComponent($('#subj_mailers').val()),
                'msg': sHTML,
                'type_to_mail':encodeURIComponent($("input[type=radio][name=optionsRadios]:checked").val()),
                'users_priv':encodeURIComponent($("#users_priv").val()),
                'users_units':encodeURIComponent($("#users_units").val()),
                'users_list':encodeURIComponent($("#users_list").val())
        */
        
        if ($mode == "status_ok") {
            
            $user = ($_POST['user']);
            $tid = ($_POST['tid']);
            
            $hs = explode(",", get_ticket_action_priv($tid));
            if (in_array("un_ok", $hs)) {
                $stmt = $dbConnection->prepare('SELECT status, ok_by, user_init_id FROM tickets where id=:tid');
                $stmt->execute(array(
                    ':tid' => $tid
                ));
                $fio = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $st = $fio['status'];
                $ob = $fio['ok_by'];
                $uinitd = $fio['user_init_id'];
                
                $ps = priv_status($user);
                
                if ($st == "1") {
                    
                    if (($ob == $user) || ($ps == "0") || ($ps == "2") || ($uinitd == $user)) {
                        
                        $stmt = $dbConnection->prepare('update tickets set ok_by=:n, status=:n1, last_update=:nz where id=:tid');
                        $stmt->execute(array(
                            ':tid' => $tid,
                            ':n' => '0',
                            ':n1' => '0',
                            ':nz' => $CONF['now_dt']
                        ));
                        
                        $unow = $_SESSION['helpdesk_user_id'];
                        
                        $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:no_ok, :n, :unow, :tid)');
                        $stmt->execute(array(
                            ':tid' => $tid,
                            ':unow' => $unow,
                            ':no_ok' => 'no_ok',
                            ':n' => $CONF['now_dt']
                        ));
                        
                        send_notification('ticket_no_ok', $tid);
?>

                    <div class="alert alert-success"><i class="fa fa-check"></i> <?php
                        echo lang('TICKET_msg_unOK'); ?></div>

                <?php
                    }
                }
                if ($st == "0") {
?>
                <div class="alert alert-danger"><?php
                    echo lang('TICKET_msg_unOK_error'); ?></div>
            <?php
                }
            }
        }
        
        if ($mode == "lock") {
            $user = ($_POST['user']);
            $tid = ($_POST['tid']);
            
            $hs = explode(",", get_ticket_action_priv($tid));
            if (in_array("lock", $hs)) {
                
                $stmt = $dbConnection->prepare('SELECT lock_by FROM tickets where id=:tid');
                $stmt->execute(array(
                    ':tid' => $tid
                ));
                $fio = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $lb = $fio['lock_by'];
                
                $ps = priv_status($lb);
                
                if ($lb == "0") {
                    
                    $stmt = $dbConnection->prepare('update tickets set lock_by=:user, last_update=:n where id=:tid');
                    $stmt->execute(array(
                        ':tid' => $tid,
                        ':user' => $user,
                        ':n' => $CONF['now_dt']
                    ));
                    
                    $unow = $_SESSION['helpdesk_user_id'];
                    
                    $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:lock, :n, :unow, :tid)');
                    $stmt->execute(array(
                        ':tid' => $tid,
                        ':unow' => $unow,
                        ':lock' => 'lock',
                        ':n' => $CONF['now_dt']
                    ));
                    
                    send_notification('ticket_lock', $tid);
?>

                <div class="alert alert-success"><i class="fa fa-check"></i> <?php
                    echo lang('TICKET_msg_lock'); ?></div>

            <?php
                }
                if ($lb <> "0") {
?>
                <div class="alert alert-danger"><?php
                    echo lang('TICKET_msg_lock_error'); ?> <?php
                    echo name_of_user($lb); ?></div>
            <?php
                }
            }
        }
        if ($mode == "unlock") {
            $tid = ($_POST['tid']);
            $hs = explode(",", get_ticket_action_priv($tid));
            if (in_array("unlock", $hs)) {
                $stmt = $dbConnection->prepare('update tickets set lock_by=:n, last_update=:nz where id=:tid');
                $stmt->execute(array(
                    ':tid' => $tid,
                    ':n' => '0',
                    ':nz' => $CONF['now_dt']
                ));
                
                $unow = $_SESSION['helpdesk_user_id'];
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:unlock, :n, :unow, :tid)');
                $stmt->execute(array(
                    ':tid' => $tid,
                    ':unow' => $unow,
                    ':unlock' => 'unlock',
                    ':n' => $CONF['now_dt']
                ));
                send_notification('ticket_unlock', $tid);
?>

            <div class="alert alert-success"><i class="fa fa-check"></i> <?php
                echo lang('TICKET_msg_unlock'); ?></div>

        <?php
            }
        }
         
        if ($mode == "update_to") {
            

            $hs = explode(",", get_ticket_action_priv($_POST['ticket_id']));
            if (in_array("ref", $hs)) {
                
                $tid = ($_POST['ticket_id']);
                $to = ($_POST['to']);
                $tou = ($_POST['tou']);
                $tom = ($_POST['tom']);
                
                if (strlen($tom) > 2) {
                    
                    $x_refer_comment = '<strong><small class=\'text-danger\'>' . nameshort(name_of_user_ret($_SESSION['helpdesk_user_id'])) . ' ' . lang('REFER_comment_add') . ' (' . date(' d.m.Y h:i:s') . '):</small> </strong>' . strip_tags((($_POST['tom'])));
                    
                    $stmt = $dbConnection->prepare('update tickets set 
            unit_id=:to, 
            user_to_id=:tou, 
            msg=concat(msg,:br,:x_refer_comment), 
            lock_by=:n, 
            last_update=:nz where id=:tid');
                    $stmt->execute(array(
                        ':to' => $to,
                        ':tou' => $tou,
                        ':br' => '<br>',
                        ':x_refer_comment' => $x_refer_comment,
                        ':tid' => $tid,
                        ':n' => '0',
                        ':nz' => $CONF['now_dt']
                    ));
                } 
                else if (strlen($tom) <= 2) {
                    
                    $stmt = $dbConnection->prepare('update tickets set 
            unit_id=:to, 
            user_to_id=:tou, 
            lock_by=:n, 
            last_update=:nz where id=:tid');
                    $stmt->execute(array(
                        ':to' => $to,
                        ':tou' => $tou,
                        ':tid' => $tid,
                        ':n' => '0',
                        ':nz' => $CONF['now_dt']
                    ));
                }
                
                $unow = $_SESSION['helpdesk_user_id'];
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:unlock, :n, :unow, :tid)');
                $stmt->execute(array(
                    ':tid' => $tid,
                    ':unow' => $unow,
                    ':unlock' => 'unlock',
                    ':n' => $CONF['now_dt']
                ));
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, to_user_id, ticket_id, to_unit_id) values (:refer, :n, :unow, :tou, :tid, :to)');
                $stmt->execute(array(
                    ':to' => $to,
                    ':tou' => $tou,
                    ':refer' => 'refer',
                    ':tid' => $tid,
                    ':unow' => $unow,
                    ':n' => $CONF['now_dt']
                ));
                
                send_notification('ticket_refer', $tid);
?>
            <div class="alert alert-success"><?php
                echo lang('TICKET_msg_refer'); ?></div>
        <?php
            }
        }
        
        if ($mode == "edit_profile_ad_f") {
            $msg=NULL;
            $uid = $_SESSION['helpdesk_user_id'];
            
            //########################## ADDITIONAL FIELDS ###############################
            
            $stmt = $dbConnection->prepare('SELECT * FROM user_fields where status=:n');
            $stmt->execute(array(
                ':n' => '1'
            ));
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $row) {
                
                $cur_hash = $row['hash'];
                
                if ($_POST[$cur_hash]) {
                    
                    //insert
                    
                    $v_field = $_POST[$cur_hash];
                    if ($row['t_type'] == "multiselect") {
                        
                        // code...
                        $v_field = implode(",", $_POST[$cur_hash]);
                    }
                    
                    $stmtf = $dbConnection->prepare('SELECT id FROM user_data where user_id=:val and field_id=:fid');
                    $stmtf->execute(array(
                        ':val' => $uid,
                        ':fid' => $row['id']
                    ));
                    $ifex = $stmtf->fetch(PDO::FETCH_ASSOC);
                    
                    if ($ifex['id']) {
                        $stmts = $dbConnection->prepare('update user_data set field_val=:field_val, field_name=:field_name where field_id=:field_id and user_id=:user_id');
                        $stmts->execute(array(
                            ':user_id' => $uid,
                            ':field_id' => $row['id'],
                            ':field_val' => $v_field,
                            ':field_name' => $row['name']
                        ));
                    } 
                    else if (!$ifex['id']) {
                        
                        $stmts = $dbConnection->prepare('insert into user_data (user_id,field_id,field_val, field_name) VALUES (:user_id,:field_id,:field_val,:field_name)');
                        $stmts->execute(array(
                            ':user_id' => $uid,
                            ':field_id' => $row['id'],
                            ':field_val' => $v_field,
                            ':field_name' => $row['name']
                        ));
                    }
                }
            }
            
            //########################## ADDITIONAL FIELDS ###############################
            $msg = " <div class=\"alert alert-success\">";
            $msg.= lang('PROFILE_msg_ok');
            $msg.= "</div>";
            
            echo $msg;
        }
        
        if ($mode == "save_edit_ticket") {
            
            $t_hash = $_POST['t_hash'];
            
            $subj = $_POST['subj'];
            $msg = $_POST['msg'];
            $prio = $_POST['prio'];
            
            $stmt = $dbConnection->prepare('SELECT id, subj, msg, prio FROM tickets where hash_name=:hn');
            $stmt->execute(array(
                ':hn' => $t_hash
            ));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            $pk = $fio['id'];
            
            if ($prio != $fio['prio']) {
                $stmt = $dbConnection->prepare('update tickets set prio=:v, last_edit=:n, last_update=:nz where hash_name=:pk');
                $stmt->execute(array(
                    ':v' => $prio,
                    ':pk' => $t_hash,
                    ':n' => $CONF['now_dt'],
                    ':nz' => $CONF['now_dt']
                ));
                $unow = $_SESSION['helpdesk_user_id'];
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:edit_subj, :n, :unow, :pk)');
                $stmt->execute(array(
                    ':edit_subj' => 'edit_prio',
                    ':pk' => $pk,
                    ':unow' => $unow,
                    ':n' => $CONF['now_dt']
                ));
            }
             
            if ($subj != $fio['subj']) {
                $stmt = $dbConnection->prepare('update tickets set subj=:subj, last_edit=:n, last_update=:nz where hash_name=:pk');
                $stmt->execute(array(
                    
                    ':subj' => $subj,
                    ':pk' => $t_hash,
                    ':n' => $CONF['now_dt'],
                    ':nz' => $CONF['now_dt']
                ));
                
                $unow = $_SESSION['helpdesk_user_id'];
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:edit_subj, :n, :unow, :pk)');
                $stmt->execute(array(
                    ':edit_subj' => 'edit_subj',
                    ':pk' => $pk,
                    ':unow' => $unow,
                    ':n' => $CONF['now_dt']
                ));
            }
            
            if ($msg != $fio['msg']) {
                
                $stmt = $dbConnection->prepare('update tickets set msg=:v, last_edit=:n, last_update=:nz where hash_name=:pk');
                $stmt->execute(array(
                    ':v' => $msg,
                    ':pk' => $t_hash,
                    ':n' => $CONF['now_dt'],
                    ':nz' => $CONF['now_dt']
                ));
                
                $unow = $_SESSION['helpdesk_user_id'];
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:edit_msg, :n, :unow, :pk)');
                $stmt->execute(array(
                    ':edit_msg' => 'edit_msg',
                    ':pk' => $pk,
                    ':unow' => $unow,
                    ':n' => $CONF['now_dt']
                ));
            }
        }


        if ($mode == "units_lock") {
            $id = ($_POST['id']);
            $stmt = $dbConnection->prepare('update units set status=:v where id=:id');
            $stmt->execute(array(
                ':v' => '0',
                ':id' => $id
            ));


            $stmt = $dbConnection->prepare('update users set status=0 where is_client=1 and unit_desc=:id');
            $stmt->execute(array(
                ':id' => $id
            ));
//Найти всех клиентов данного подразделения и заблокировать
            //update users set status = 0 where is_client = 1 and unit_desc = 


        }
        if ($mode == "units_unlock") {
            $id = ($_POST['id']);
            $stmt = $dbConnection->prepare('update units set status=:v where id=:id');
            $stmt->execute(array(
                ':v' => '1',
                ':id' => $id
            ));

            $stmt = $dbConnection->prepare('update users set status=1 where is_client=1 and unit_desc=:id');
            $stmt->execute(array(
                ':id' => $id
            ));
        }




        
        if ($mode == "deps_hide") {
            $id = ($_POST['id']);
            $stmt = $dbConnection->prepare('update deps set status=:v where id=:id');
            $stmt->execute(array(
                ':v' => '0',
                ':id' => $id
            ));
        }
        if ($mode == "deps_show") {
            $id = ($_POST['id']);
            $stmt = $dbConnection->prepare('update deps set status=:v where id=:id');
            $stmt->execute(array(
                ':v' => '1',
                ':id' => $id
            ));
        }
        

        if ($mode == "edit_units") {
            $v = ($_POST['value']);
            $pk = ($_POST['pk']);
            
            $stmt = $dbConnection->prepare('update units set name=:v where id=:pk');
            $stmt->execute(array(
                ':v' => $v,
                ':pk' => $pk
            ));
        }


        if ($mode == "edit_deps") {
            $v = ($_POST['value']);
            $pk = ($_POST['pk']);
            
            $stmt = $dbConnection->prepare('update deps set name=:v where id=:pk');
            $stmt->execute(array(
                ':v' => $v,
                ':pk' => $pk
            ));
        }
        
        if ($mode == "recalculate_messages") {
            $tm = get_total_unread_messages();
            if ($tm != 0) {
                $atm = "
    <small class=\"badge pull-right bg-yellow\">" . $tm . "</small>";
            } 
            else if ($tm == 0) {
                $atm = "";
            }
            
            echo $atm;
        }
        
        if ($mode == "messages_title_username") {
            $uid = $_POST['uid'];
            
            echo "Переписка с " . get_user_val_by_id($uid, 'fio');
        }
        
        if ($mode == "recalculate_messages_ul") {
            $uniq_id = $_POST['uid'];
            
            $stmt = $dbConnection->prepare('SELECT count(id) as cou from messages where
        ((user_from=:ufrom and user_to=:uto)) and is_read=0
         ');
            $stmt->execute(array(
                ':ufrom' => $uniq_id,
                ':uto' => $_SESSION['helpdesk_user_id']
            ));
            
            $tt = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($tt['cou'] != 0) {
                $tt = "<small class=\"badge pull-right\">" . $tt['cou'] . "</small>";
            } 
            else {
                $tt = "";
            }
            
            echo $tt;
        }
        
        if ($mode == "get_tt_label") {
            
            $newt = get_total_tickets_free();
            
            if ($newt != 0) {
                $newtickets = " <small class=\"badge pull-right bg-red\">" . $newt . "</small>";
            } 
            else if ($newt == 0) {
                $newtickets = "";
            }
            echo $newtickets;
        }
        


//messages_view_client
if ($mode == "messages_view_client") {
    $stmt = $dbConnection->prepare('SELECT user_to from messages where type_msg=:type_msg and user_from=:ufrom');
    $stmt->execute(array(
        ':type_msg'=>'request',
        ':ufrom' => $_SESSION['helpdesk_user_id']
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    view_messages($tt['user_to']);

}




if ($mode == "clientCloseStatus"){
?>
<div class="col-md-6 col-md-offset-3">
    <br><br><br>
            <a id="ClientChatRequest_action" class="btn btn-block btn-primary" ><?php echo lang('chat_request');?></a>
            <br>
            <center><small class="text-muted"><?php echo lang('chat_q');?></small></center>
        </div>
    <?php
}
if ($mode == "clientWaitStatus"){
echo lang('chat_wait');
}

        if ($mode == "total_msgs_user_client") {
            




    $stmt = $dbConnection->prepare('SELECT user_to from messages where type_msg=:type_msg and user_from=:ufrom');
    $stmt->execute(array(
        ':type_msg'=>'request',
        ':ufrom' => $_SESSION['helpdesk_user_id']
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo get_total_msgs_user($tt['user_to']);
        }


        if ($mode == "total_msgs_user") {
            
            echo get_total_msgs_user($_POST['in']);
        }

        if ($mode == "total_msgs_main") {
            
            echo get_total_msgs_main();
        }
        
        if ($mode == "message_user_list") {
            $t = $_POST['t'];
            if ($_POST['t']) {
                $stmt = $dbConnection->prepare('SELECT id, fio from users where fio like :t and id!=:uid and status!=2  order by fio ASC limit 10');
                $stmt->execute(array(
                    ':t' => '%' . $t . '%',
                    ':uid' => $_SESSION['helpdesk_user_id']
                ));
                
                $re = $stmt->fetchAll();
?>
                                    
                                            
                                                <ul class="nav nav-pills nav-stacked">
                                                    <?php
                if (empty($re)) {
                    
                    echo "no ";
                } 
                else if (!empty($re)) {
                    
                    foreach ($re as $rews) {
                        $uniq_id = $rews['id'];
                        
                        $stmt = $dbConnection->prepare('SELECT count(id) as cou from messages where
        ((user_from=:ufrom and user_to=:uto)) and is_read=0
         ');
                        $stmt->execute(array(
                            ':ufrom' => $uniq_id,
                            ':uto' => $_SESSION['helpdesk_user_id']
                        ));
                        
                        $tt = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($tt['cou'] != 0) {
                            $tt = "<small id=\"ul_label_" . $uniq_id . "\"><small class=\"badge pull-right\">" . $tt['cou'] . "</small></small>";
                        } 
                        else {
                            $tt = "<small id=\"ul_label_" . $uniq_id . "\"></small>";
                        }
?>
                                                    <li class="user_li" user-id="<?php
                        echo $uniq_id; ?>">
                                                    <a href="#">
                                                        <img style="width: 25px;height: 25px;" src="<?php
                        echo get_user_img_by_id($uniq_id); ?>" class="img-circle" alt="User Image">
                                                     <?php
                        echo nameshort(name_of_user_ret_nolink($uniq_id)); ?>
                                                     
                                                     <?php
                        echo $tt; ?>
                                                     </a>
                                                     </li>
                                                    <?php
                    }
                }
?>
                                                </ul>
                                                
<?php
            } 
            else if (!$_POST['t']) {
                $stmt = $dbConnection->prepare('SELECT id, user_from,user_to from messages where
                        (user_to=:u_to)
                        order by is_read, date_op ASC');
                $stmt->execute(array(
                    ':u_to' => $_SESSION['helpdesk_user_id']
                ));
                
                $re = $stmt->fetchAll();
                if (!empty($re)) {
                    $user_arr = array();
                    foreach ($re as $rews) {
                        
                        array_push($user_arr, $rews['user_from']);
                        array_push($user_arr, $rews['user_to']);
                    }
                }
                
                $user_arr = array_unique($user_arr);
                if (($key = array_search($_SESSION['helpdesk_user_id'], $user_arr)) !== false) {
                    unset($user_arr[$key]);
                }
                if (($key = array_search('0', $user_arr)) !== false) {
                    unset($user_arr[$key]);
                }
?>
                                        
                                        <ul class="nav nav-pills nav-stacked">
                                                    <?php
                foreach ($user_arr as $uniq_id) {
                    
                    $stmt = $dbConnection->prepare('SELECT count(id) as cou from messages where
        ((user_from=:ufrom and user_to=:uto)) and is_read=0
         ');
                    $stmt->execute(array(
                        ':ufrom' => $uniq_id,
                        ':uto' => $_SESSION['helpdesk_user_id']
                    ));
                    
                    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($tt['cou'] != 0) {
                        $tt = "<small id=\"ul_label_" . $uniq_id . "\"><small class=\"badge pull-right\">" . $tt['cou'] . "</small></small>";
                    } 
                    else {
                        $tt = "<small id=\"ul_label_" . $uniq_id . "\"></small>";
                    }
?>
                                                    <li class="user_li" user-id="<?php
                    echo $uniq_id; ?>">
                                                    <a href="#">
                                                        <img style="width: 25px;height: 25px;" src="<?php
                    echo get_user_img_by_id($uniq_id); ?>" class="img-circle" alt="User Image">
                                                     <?php
                    echo nameshort(name_of_user_ret_nolink($uniq_id)); ?>
                                                     
                                                     <?php
                    echo $tt; ?>
                                                     </a>
                                                     </li>
                                                    <?php
                }
?>
                                                    
                                                    
                                                </ul>
<?php
            }
        }
        

//check_request_status
if ($mode == "check_request_status") {
    $stmt_spec = $dbConnection->prepare('SELECT client_request_status from messages where type_msg=:str and user_from=:uf');
    $stmt_spec->execute(array(
        ':str' => 'request',
        ':uf' => $_SESSION['helpdesk_user_id']
    ));
    $row = $stmt_spec->fetch(PDO::FETCH_ASSOC);

if (empty($row)) {
    $res="empty";
}
else if (!empty($row)) {
    
    if ($row['client_request_status'] == "0") {
        $res="wait";
    }
    else if ($row['client_request_status'] == "1") {
        $res="active";
    }

}

echo $res;
}

if ($mode == "startChatWithClient") {

            $stmt = $dbConnection->prepare('update messages set client_request_status=1, user_to=:uto where type_msg=:type_msg and user_from=:user_from');
            $stmt->execute(array(
                ':type_msg' => 'request',
                ':user_from' => $_POST['target'],
                ':uto'=>$_SESSION['helpdesk_user_id']
            ));

            view_messages($_POST['target']);

}

if ($mode == "stopChatWithClient") {

            $stmt = $dbConnection->prepare('delete from messages  where type_msg=:type_msg and user_from=:user_from');
            $stmt->execute(array(
                ':type_msg' => 'request',
                ':user_from' => $_POST['target']
            ));

            view_messages($_POST['target']);

}


        if ($mode == "messages_sendClient") {
            
            $user_comment = $_SESSION['helpdesk_user_id'];
            $text_comment = $_POST['textmsg'];
            //$target = $_POST['target'];
            
            //chat_msg_id
            
    $stmt_spec = $dbConnection->prepare('SELECT client_request_status,user_to  from messages where type_msg=:str and user_from=:uf');
    $stmt_spec->execute(array(
        ':str' => 'request',
        ':uf' => $_SESSION['helpdesk_user_id']
    ));
    $row = $stmt_spec->fetch(PDO::FETCH_ASSOC);


if (!empty($row)){
    $client_request_status=$row['client_request_status'];

if ($client_request_status == "0") {
echo lang('chat_wait');
}
else if ($client_request_status == "1") {





            $stmt_m = $dbConnection->prepare("SELECT MAX(id) max_id FROM messages");
            $stmt_m->execute();
            $max_id_msgs = $stmt_m->fetch(PDO::FETCH_NUM);
                $a = $row['user_to'];//target_system
                $b = "0";
                
                $unid = get_user_val_by_id($target, 'uniq_id');
                
                $stmt = $dbConnection->prepare('INSERT INTO notification_msg_pool (delivers_id,type_op,ticket_id,dt,chat_msg_id)
                    values (:delivers_id,:type_op,:ticket_id,:n,:chat_msg_id)');
                $stmt->execute(array(
                    ':delivers_id' => $unid,
                    ':type_op' => 'message_send',
                    ':ticket_id' => $user_comment,
                    ':chat_msg_id' => $max_id_res_msgs,
                    ':n' => $CONF['now_dt']
                ));
            
            
            $stmt = $dbConnection->prepare('INSERT INTO messages (id, user_from,user_to,date_op,msg,type_msg,is_read)
                    values (:ida, :user_from, :user_to, :n, :msg, :type_msg, :is_read)');
            $stmt->execute(array(
                ':ida' => $max_id_res_msgs,
                ':user_from' => $user_comment,
                ':user_to' => $a,
                ':msg' => $text_comment,
                ':type_msg' => $b,
                ':is_read' => '0',
                ':n' => $CONF['now_dt']
            ));


    view_messages($row['user_to']);
}


}
else if (empty($row)){

            $stmt = $dbConnection->prepare('insert into messages (
                    user_from,
                    user_to,
                    date_op,
                    msg,
                    type_msg,
                    is_read,
                    client_request_status
                ) VALUES (
                    :user_from,
                    :user_to,
                    :date_op,
                    :msg,
                    :type_msg,
                    :is_read,
                    :client_request_status
                )');

            $stmt->execute(array(
                ':user_from'=>$_SESSION['helpdesk_user_id'],
                ':user_to'=>'0',
                ':date_op'=>$CONF['now_dt'],
                ':msg'=>$text_comment,
                ':type_msg'=>'request',
                ':is_read'=>'0',
                ':client_request_status'=>'0'

            ));
}

            



            //echo "ok";
            
            //echo $target.'=='.$_SESSION['helpdesk_user_id'];
            
            
        }


        if ($mode == "messages_send") {
            
            $user_comment = $_SESSION['helpdesk_user_id'];
            $text_comment = $_POST['textmsg'];
            $target = $_POST['target'];
            
            //chat_msg_id
            
            $stmt_m = $dbConnection->prepare("SELECT MAX(id) max_id FROM messages");
            $stmt_m->execute();
            $max_id_msgs = $stmt_m->fetch(PDO::FETCH_NUM);
            
            $max_id_res_msgs = $max_id_msgs[0] + 1;
            
            if ($target == "main") {
                $a = "0";
                $b = "main";
            } 
            else if ($target != "main") {
                $a = $target;
                $b = "0";
                
                $unid = get_user_val_by_id($target, 'uniq_id');
                
                $stmt = $dbConnection->prepare('INSERT INTO notification_msg_pool (delivers_id,type_op,ticket_id,dt,chat_msg_id)
                    values (:delivers_id,:type_op,:ticket_id,:n,:chat_msg_id)');
                $stmt->execute(array(
                    ':delivers_id' => $unid,
                    ':type_op' => 'message_send',
                    ':ticket_id' => $user_comment,
                    ':chat_msg_id' => $max_id_res_msgs,
                    ':n' => $CONF['now_dt']
                ));
            }
            
            $stmt = $dbConnection->prepare('INSERT INTO messages (id, user_from,user_to,date_op,msg,type_msg,is_read)
                    values (:ida, :user_from, :user_to, :n, :msg, :type_msg, :is_read)');
            $stmt->execute(array(
                ':ida' => $max_id_res_msgs,
                ':user_from' => $user_comment,
                ':user_to' => $a,
                ':msg' => $text_comment,
                ':type_msg' => $b,
                ':is_read' => '0',
                ':n' => $CONF['now_dt']
            ));
            
            view_messages($target);
            
            //echo $target.'=='.$_SESSION['helpdesk_user_id'];
            
            
        }
        
        if ($mode == "messages_view") {
            $target = $_POST['target'];
            
            view_messages($target);
        }
        
        if ($mode == "view_comment") {
            
            $tid_comment = ($_POST['tid']);
            view_comment($tid_comment);
        }
        
        if ($mode == "add_comment") {
            
            $user_comment = $_SESSION['helpdesk_user_id'];
            $tid_comment = get_ticket_id_by_hash($_POST['tid']);
            $ru = nameshort(name_of_user_ret($user_comment));
            //$text_comment=strip_tags(xss_clean(($_POST['textmsg'])),"<b><a><br>");


if (!empty($_POST['files'])) {

$f_list.="[file:";
$f_list.=$_POST['files'];
$f_list.="]";

}


            $text_comment = $_POST['textmsg'];
            $text_comment=$text_comment."<br>".$f_list;



            //if ($_SESSION['helpdesk_user_type'] == "user") {
            
            $stmt = $dbConnection->prepare('INSERT INTO comments (t_id, user_id, comment_text, dt)
values (:tid_comment, :user_comment, :text_comment, :n)');
            $stmt->execute(array(
                ':tid_comment' => $tid_comment,
                ':user_comment' => $user_comment,
                ':text_comment' => $text_comment,
                ':n' => $CONF['now_dt']
            ));
            
            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:comment, :n, :user_comment, :tid_comment)');
            $stmt->execute(array(
                ':tid_comment' => $tid_comment,
                ':user_comment' => $user_comment,
                ':comment' => 'comment',
                ':n' => $CONF['now_dt']
            ));
            
            send_notification('ticket_comment', $tid_comment);
            
            //}
            
            $stmt = $dbConnection->prepare('update tickets set last_update=:n where id=:tid_comment');
            $stmt->execute(array(
                ':tid_comment' => $tid_comment,
                ':n' => $CONF['now_dt']
            ));
            
            //view_comment($tid_comment);
$fl=strpos(make_html($text_comment, true),'[file:');

if ($fl !== false) {
    


$cline=substr(make_html($text_comment, true), strpos(make_html($text_comment, true),'[file:'));

$cline=rtrim($cline, "]");



$cline_res=explode(":", $cline);

$some_arr=explode(",", $cline_res[1]);

$ct = substr(make_html($text_comment, true), 0, strpos(make_html($text_comment, true),'[file:'));
$ct .= '<div class=\'text-muted\' style=\'margin-bottom: 5px;\'><em><small>' . lang('EXT_attach_file') . '</small> <br></em>';

foreach ($some_arr as $f_hash) {

$stmt2 = $dbConnection->prepare('SELECT original_name, file_size,file_type,file_ext FROM files where file_hash=:tid');
            $stmt2->execute(array(
                ':tid' => $f_hash
            ));
$file_arr = $stmt2->fetch(PDO::FETCH_ASSOC);

$fts = array(
                'image/jpeg',
                'image/gif',
                'image/png'
            );
            
            if (in_array($file_arr['file_type'], $fts)) {
                
                $ct.= ' <small><a class=\'fancybox\' href=\'' . $CONF['hostname'] . 'upload_files/' . $f_hash . '.' . $file_arr['file_ext'] . '\'><img style=\'max-height:100px;\' src=\'' . $CONF['hostname'] . 'upload_files/' . $f_hash . '.' . $file_arr['file_ext'] . '\'></a>  </small> ';
            } 
            else {
                $ct.= get_file_icon($f_hash) . ' <small><a href=\'' . $CONF['hostname'] . 'action?mode=download_file&file=' . $f_hash . '\'>' . $file_arr['original_name'] . '</a> ' . round(($file_arr['file_size'] / (1024 * 1024)) , 2) . ' Mb </small><br>';
            }

    # code...
}
$ct.= '</div>';



}





        else {
            $ct = make_html($text_comment, true);
        }
?>
 <!-- Message. Default to the left -->
                    <div class="direct-chat-msg">
                      <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name pull-left"><a href="view_user?<?php
        echo get_user_hash_by_id($user_comment); ?>" class="name">
                                                
                                                <?php
        echo $ru; ?>
                                            </a></span>
                        <span class="direct-chat-timestamp pull-right"><small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 
                                                <time id="b" datetime="<?php
        echo $CONF['now_dt']; ?>"></time> <time id="c" datetime="<?php
        echo $CONF['now_dt']; ?>"></time>
                                                </small></span>
                      </div><!-- /.direct-chat-info -->
                      <img class="direct-chat-img <?php
        echo get_user_status_text($user_comment); ?>"  src="<?php
        echo get_user_img_by_id($user_comment); ?>" alt=""><!-- /.direct-chat-img -->
                      <div class="direct-chat-text">
                     <?php
        echo $ct; ?>
                      </div><!-- /.direct-chat-text -->
                    </div><!-- /.direct-chat-msg -->
<?php


        }
        


        if ($mode == "ClientChatRequest") {

            $stmt = $dbConnection->prepare('insert into messages (
                    user_from,
                    user_to,
                    date_op,
                    msg,
                    type_msg,
                    is_read,
                    client_request_status
                ) VALUES (
                    :user_from,
                    :user_to,
                    :date_op,
                    :msg,
                    :type_msg,
                    :is_read,
                    :client_request_status
                )');

            $stmt->execute(array(
                ':user_from'=>$_SESSION['helpdesk_user_id'],
                ':user_to'=>'0',
                ':date_op'=>$CONF['now_dt'],
                ':msg'=>'',
                ':type_msg'=>'request',
                ':is_read'=>'0',
                ':client_request_status'=>'0'

            ));

            ?>
Запрос успешно послан
            <?php


        }




        if ($mode == "upload_file") {
            $name = $_POST['name'];
            $hn = $_POST['hn'];
            
            $stmt = $dbConnection->prepare('insert into files (name, h_name) VALUES (:name, :hn)');
            $stmt->execute(array(
                ':name' => $name,
                ':hn' => $hn
            ));
        }

        if ($mode == "conf_clear_cache") {

function recursiveDelete($str) {
    if (is_file($str)) {
        return @unlink($str);
    }
    elseif (is_dir($str)) {
        $scan = glob(rtrim($str,'/').'/*');
        foreach($scan as $index=>$path) {
            recursiveDelete($path);
        }
        return @rmdir($str);
    }
}
recursiveDelete(ZENLIX_DIR."/app/cache/");
//array_map('unlink', glob(ZENLIX_DIR."/app/cache/*"));
//removeDirectory(ZENLIX_DIR."/app/cache/");
//unlink(ZENLIX_DIR . "/upload_files/" . $id . "." . $ext);


            ?>
<div class="alert alert-success">
Successfully cleared!
</div>
            <?php
        }

        if ($mode == "conf_test_mail") {
            $res_msg=NULL;
            echo "<pre>";
            /*
            
            if (get_conf_param('mail_auth_type') != "none")
            {
            $mail->SMTPSecure = $CONF_MAIL['auth_type'];
            }
            
            
            sendmail?
            SMTP?
            
            */
            if (get_conf_param('mail_type') == "sendmail") {
                $mail = new PHPMailer(true);
                $mail->IsSendmail();
                
                // telling the class to use SendMail transport
                
                try {
                    $mail->AddReplyTo($CONF_MAIL['from'], $CONF['name_of_firm']);
                    $mail->AddAddress($CONF['mail'], 'admin helpdesk');
                    $mail->SetFrom($CONF_MAIL['from'], $CONF['name_of_firm']);
                    $mail->Subject = 'test message';
                    $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
                    
                    // optional - MsgHTML will create an alternate automatically
                    $mail->MsgHTML('Test message via sendmail');
                    $mail->Send();
                    echo "Message Sent OK<p></p>\n";
                }
                catch(phpmailerException $e) {
                    $e->errorMessage();
                    
                    //Pretty error messages from PHPMailer
                    
                    
                }
                catch(Exception $e) {
                     $e->getMessage();
                    
                    //Boring error messages from anything else!
                    
                    
                }
            } 
            else if (get_conf_param('mail_type') == "SMTP") {
                
                $mail = new PHPMailer(true);
                
                // the true param means it will throw exceptions on errors, which we need to catch
                
                $mail->IsSMTP();
                
                // telling the class to use SMTP
                
                try {
                    $mail->SMTPDebug = 2;
                    
                    // enables SMTP debug information (for testing)
                    $mail->SMTPAuth = $CONF_MAIL['auth'];
                    
                    // enable SMTP authentication
                    if (get_conf_param('mail_auth_type') != "none") {
                        $mail->SMTPSecure = $CONF_MAIL['auth_type'];
                    }
                    $mail->Host = $CONF_MAIL['host'];
                    $mail->Port = $CONF_MAIL['port'];
                    $mail->Username = $CONF_MAIL['username'];
                    $mail->Password = $CONF_MAIL['password'];
                    
                    $mail->AddReplyTo($CONF_MAIL['from'], $CONF['name_of_firm']);
                    $mail->AddAddress($CONF['mail'], 'admin helpdesk');
                    $mail->SetFrom($CONF_MAIL['from'], $CONF['name_of_firm']);
                    $mail->Subject = 'test message via smtp';
                    $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
                    
                    // optional - MsgHTML will create an alternate automatically
                    $mail->MsgHTML("test message");
                    $mail->Send();
                    echo "Message Sent OK<p></p>\n";
                }
                catch(phpmailerException $e) {
                     $e->errorMessage();
                    
                    //Pretty error messages from PHPMailer
                    
                    
                }
                catch(Exception $e) {
                     $e->getMessage();
                    
                    //Boring error messages from anything else!
                    
                    
                }
            }

            echo "</pre>";
        }
        
        if ($mode == "profile_edit_nf") {
            
            //$_POST['mail'];
            $stmt2 = $dbConnection->prepare('SELECT id from users_notify where user_id=:uto');
            $stmt2->execute(array(
                ':uto' => $_SESSION['helpdesk_user_id']
            ));
            $tt2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if ($tt2['id']) {
                $stmt2 = $dbConnection->prepare('update users_notify set mail=:mail, pb=:pb, sms=:sms where user_id=:user_id');
                $stmt2->execute(array(
                    ':user_id' => $_SESSION['helpdesk_user_id'],
                    ':mail' => $_POST['mail'],
                    ':pb' => '',
                    ':sms' => $_POST['sms']
                ));
            } 
            else if (!$tt2['id']) {
                
                $stmt2 = $dbConnection->prepare('insert into users_notify (user_id,mail,pb,sms) values (:user_id,:mail,:pb,:sms)');
                $stmt2->execute(array(
                    ':user_id' => $_SESSION['helpdesk_user_id'],
                    ':mail' => $_POST['mail'],
                    ':pb' => '',
                    ':sms' => $_POST['sms']
                ));
            }
            $stmtm = $dbConnection->prepare('update users set mob=:mob where id=:user_id');
            $stmtm->execute(array(
                ':user_id' => $_SESSION['helpdesk_user_id'],
                ':mob' => $_POST['mob']
            ));
?>
                <div class="alert alert-success">
                    <?php
            echo lang('PROFILE_msg_ok'); ?>
                </div>
            <?php
        }
        
        if ($mode == "get_cal_event") {
            $stmt = $dbConnection->prepare('SELECT * from calendar where uniq_hash=:uh');
            $stmt->execute(array(
                ':uh' => $_POST['uniq_code']
            ));
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $row) {
                $period = $row['dtStart'] . " - " . $row['dtStop'];
                $ad = false;
                if ($row['allday'] == "true") {
                    $ad = true;
                    $period = "all day";
                }
                
                $d = $row['description'];
                if ($row['description'] == "0") {
                    $d = "";
                }
                
                $author_tag = "<a href=\"view_user?" . get_user_hash_by_id($row['user_id']) . "\">" . nameshort(name_of_user_ret($row['user_id'])) . "</a>";
                
                $data[] = array(
                    'id' => $row['uniq_hash'],
                    'title' => $row['title'],
                    'description' => $d,
                    'visibility' => $row['visibility'],
                    
                    //'url' => '/ticket?'.$row['hash_name'],
                    'start' => $row['dtStart'],
                    'end' => $row['dtStop'],
                    'allDay' => $ad,
                    'backgroundColor' => $row['backgroundColor'],
                    'borderColor' => $row['borderColor'],
                    'editable' => true,
                    'period' => $period,
                    'author' => $author_tag
                );
            }
            
            echo json_encode($data);
        }
        
        if ($mode == "get_cal_events") {
            
            $fe = $_POST['filter'];
            
            if ($fe == "") {
                $fe = "5";
            }
            
            $fe = explode(",", $fe);
            
            //0 - только USER_ID=me
            //1 - узнать id всех пользователей моего отдела
            //2 - all
            
            $res_main = array();
            foreach ($fe as $value) {
                
                // code...
                
                if ($value == "0") {
                    $stmt = $dbConnection->prepare('SELECT * from calendar where visibility=0 and user_id=:uid and dtStart between :start AND :end');
                    $stmt->execute(array(
                        ':uid' => $_SESSION['helpdesk_user_id'],
                        ':start' => $_POST['start'],
                        ':end' => $_POST['end']
                    ));
                    $res_1 = $stmt->fetchAll();
                    $res_main = array_merge($res_main, $res_1);
                } 
                else if ($value == "1") {
                    
                    $stmt = $dbConnection->prepare('SELECT * from calendar where visibility=1 and dtStart between :start AND :end');
                    $stmt->execute(array(
                        ':start' => $_POST['start'],
                        ':end' => $_POST['end']
                    ));
                    $res_1 = $stmt->fetchAll();
                    
                    foreach ($res_1 as $key => $value) {
                        
                        // code...
                        if (!check_admin_user_priv($value['user_id'])) {
                            unset($res_1[$key]);
                        }
                    }
                    
                    $res_main = array_merge($res_main, $res_1);
                } 
                else if ($value == "2") {
                    $stmt = $dbConnection->prepare('SELECT * from calendar where visibility=2 and dtStart between :start AND :end');
                    $stmt->execute(array(
                        ':start' => $_POST['start'],
                        ':end' => $_POST['end']
                    ));
                    $res_2 = $stmt->fetchAll();
                    $res_main = array_merge($res_main, $res_2);
                }
            }
            
            foreach ($res_main as $row) {
                $period = $row['dtStart'] . " - " . $row['dtStop'];
                $ad = false;
                if ($row['allday'] == "true") {
                    $ad = true;
                    $period = lang('CALENDAR_allday');
                }
                
                $d = $row['description'];
                if ($row['description'] == "0") {
                    $d = "";
                }
                
                $priv_val = priv_status($_SESSION['helpdesk_user_id']);
                $editable = false;
                
                if ($row['user_id'] == $_SESSION['helpdesk_user_id']) {
                    $editable = true;
                }
                if ($row['user_id'] != $_SESSION['helpdesk_user_id']) {
                    if ($priv_val == 2) {
                        $editable = true;
                    }
                    if ($priv_val == 0) {
                        $editable = true;
                    }
                    if ($priv_val == 1) {
                        $editable = false;
                    }
                }
                
                $data[] = array(
                    'id' => $row['uniq_hash'],
                    'title' => $row['title'],
                    'description' => $d,
                    
                    //'url' => '/ticket?'.$row['hash_name'],
                    'start' => $row['dtStart'],
                    'end' => $row['dtStop'],
                    'allDay' => $ad,
                    'backgroundColor' => $row['backgroundColor'],
                    'borderColor' => $row['borderColor'],
                    'editable' => $editable,
                    'period' => $period
                );
            }
            
            echo json_encode($data);
        }
        
        if ($mode == "cal_drop_events") {
            
            $stmt = $dbConnection->prepare('update calendar set title=:title, dtStart=:dtStart, dtStop=:dtStop, allday=:ad where uniq_hash=:id');
            $stmt->execute(array(
                ':title' => $_POST['title'],
                ':dtStart' => $_POST['start'],
                ':dtStop' => $_POST['end'],
                ':id' => $_POST['id'],
                ':ad' => $_POST['allday']
            ));
        }
        
        if ($mode == "cal_resize_events") {
            
            $stmt = $dbConnection->prepare('update calendar set title=:title, dtStart=:dtStart, dtStop=:dtStop, allday=:ad where uniq_hash=:id');
            $stmt->execute(array(
                ':title' => $_POST['title'],
                ':dtStart' => $_POST['start'],
                ':dtStop' => $_POST['end'],
                ':id' => $_POST['id'],
                ':ad' => $_POST['allday']
            ));
        }
        
        if ($mode == "cal_del_event") {
            $stmt = $dbConnection->prepare('delete from calendar where uniq_hash=:u');
            $stmt->execute(array(
                ':u' => $_POST['uniq_code']
            ));
        }
        
        if ($mode == "cal_edit_event") {
            
            $stmt = $dbConnection->prepare('update calendar set title=:title, description=:description, visibility=:visibility, backgroundColor=:backgroundColor, borderColor=:borderColor, allday=:allday,
        dtStart=:dtStart, dtStop=:dtStop
      where uniq_hash=:id');
            $stmt->execute(array(
                ':title' => $_POST['name'],
                ':description' => $_POST['desc'],
                ':visibility' => $_POST['priv'],
                ':id' => $_POST['uniq_code'],
                ':backgroundColor' => $_POST['color'],
                ':borderColor' => $_POST['color_b'],
                ':allday' => $_POST['allday'],
                ':dtStart' => $_POST['start'],
                ':dtStop' => $_POST['end']
            ));
        }
        
        if ($mode == "cal_insert_events") {
            
            $stmt = $dbConnection->prepare('insert into calendar 
        (title, dtStart, dtStop, allday, backgroundColor, borderColor, uniq_hash, user_id) 
        values (:title, :dtStart, :dtStop, :ad, :backgroundColor, :borderColor, :uniq_hash, :user_id)');
            $stmt->execute(array(
                ':title' => $_POST['title'],
                ':dtStart' => $_POST['start'],
                ':dtStop' => $_POST['end'],
                ':ad' => 'true',
                ':backgroundColor' => $_POST['backgroundColor'],
                ':borderColor' => $_POST['borderColor'],
                ':uniq_hash' => md5(time()) ,
                ':user_id' => $_SESSION['helpdesk_user_id']
            ));
        }
        
        if ($mode == "add_ticket") {
            $type = ($_POST['type_add']);
            
            //########################## ADDITIONAL FIELDS ###############################
            
            $stmt = $dbConnection->prepare('SELECT * FROM ticket_fields where status=:n');
            $stmt->execute(array(
                ':n' => '1'
            ));
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $row) {
                
                $cur_hash = $row['hash'];
                
                if ($_POST[$cur_hash]) {
                    
                    //insert
                    
                    $v_field = $_POST[$cur_hash];
                    if ($row['t_type'] == "multiselect") {
                        
                        // code...
                        $v_field = implode(",", $_POST[$cur_hash]);
                    }
                    
                    $stmt = $dbConnection->prepare('insert into ticket_data (ticket_hash,field_id,field_val, field_name) VALUES (:ticket_hash,:field_id,:field_val,:field_name)');
                    $stmt->execute(array(
                        ':ticket_hash' => $_POST['hashname'],
                        ':field_id' => $row['id'],
                        ':field_val' => $v_field,
                        ':field_name' => $row['name']
                    ));
                }
            }
            
            //########################## ADDITIONAL FIELDS ###############################
            
            $deadline_time = strip_tags(xss_clean($_POST['deadline_time']));
            if ($deadline_time == "NULL") {
                $deadline_time = NULL;
            }
            $user_init_id = ($_POST['user_init_id']);
            $user_to_id = ($_POST['user_do']);
            
            if (get_conf_param('sla_system') == "false") {
                $subj = strip_tags(xss_clean(($_POST['subj'])));
                $sla_plan_id = "0";
            } 
            else if (get_conf_param('sla_system') == "true") {
                $sla_plan_id = strip_tags(xss_clean(($_POST['subj'])));
                $stmt_sla = $dbConnection->prepare('SELECT * from sla_plans where id=:uid');
                $stmt_sla->execute(array(
                    ':uid' => $sla_plan_id
                ));
                $row_sla = $stmt_sla->fetch(PDO::FETCH_ASSOC);
                $subj = $row_sla['name'];
            }
            
            $msg = strip_tags(xss_clean(($_POST['msg'])));
            $status = '0';
            $unit_id = ($_POST['unit_id']);
            $prio = ($_POST['prio']);
            
            $client_fio = strip_tags(xss_clean(($_POST['fio'])));
            $client_tel = strip_tags(xss_clean(($_POST['tel'])));
            $client_login = strip_tags(xss_clean(($_POST['login'])));
            $unit_desc = strip_tags(xss_clean(($_POST['pod'])));
            
            $client_adr = strip_tags(xss_clean(($_POST['adr'])));
            $client_mail = strip_tags(xss_clean(($_POST['mail'])));
            $client_posada = strip_tags(xss_clean(($_POST['posada'])));
            
            $client_id_param = ($_POST['client_id_param']);
            
            if ($client_fio == "пусто") {
                $client_fio = "";
            }
            if ($client_tel == "пусто") {
                $client_tel = "";
            }
            if ($client_login == "пусто") {
                $client_login = "";
            }
            if ($unit_desc == "пусто") {
                $unit_desc = "";
            }
            if ($client_adr == "пусто") {
                $client_adr = "";
            }
            if ($client_mail == "пусто") {
                $client_mail = "";
            }
            if ($client_posada == "пусто") {
                $client_posada = "";
            }
            
            if (get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_unit_id') != "0") {
                $user_to_id = get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_user_id');
                $unit_id = get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_unit_id');
            }
            
            /*
            На этом месте можно дописывать код, для обработки создания заявки.
            Например SMS-информирование, подключать API и тд и тп
            Доступны переменные:
            $user_init_id   ID-пользователя, который создал заявку
            $user_to_id     ID-пользователя, которому назначена заявку
            $subj           Тема заявки
            $msg            Сообщение
            $unit_id        ID-подразделения, на которое назначена заявка
            $prio           Приоритет заявки
            $client_fio     ФИО клиента
            $client_tel     Тел клиента
            $client_login   Логин клиента
            $unit_desc      Подразделение клиента
            $client_adr     Адрес клиента
            $client_mail    Почта клиента
            $client_posada  Должность клиента
            */
            
            if ($type == "add") {
                
                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM users");
                $stmt->execute();
                $max = $stmt->fetch(PDO::FETCH_NUM);
                
                $max_id = $max[0] + 1;
                $hashname = ($_POST['hashname']);
                
                $hn = md5(time());
                
                $stmt = $dbConnection->prepare('insert into users 
             (id, 
             fio, 
             tel, 
             login, 
             unit_desc, 
             adr, 
             email, 
             posada,
             priv,
             is_client,
             uniq_id,
             api_key) 
             VALUES         
             (:max_id, 
             :client_fio, 
             :client_tel, 
             :client_login, 
             :unit_desc, 
             :client_adr,  
             :client_mail, 
             :client_posada,
             :priv,
             :is_client,
             :uniq_id,
             :api_key)');
                
                $stmt->execute(array(
                    ':max_id' => $max_id,
                    ':client_fio' => $client_fio,
                    ':client_tel' => $client_tel,
                    ':client_login' => $client_login,
                    ':unit_desc' => $unit_desc,
                    ':client_adr' => $client_adr,
                    ':client_mail' => $client_mail,
                    ':client_posada' => $client_posada,
                    ':priv' => '1',
                    ':is_client' => '1',
                    ':uniq_id' => $hn,
                    ':api_key' => md5($hn)
                ));
                
                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM tickets");
                $stmt->execute();
                $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);
                
                $max_id_res_ticket = $max_id_ticket[0] + 1;
                
                $stmt = $dbConnection->prepare('INSERT INTO tickets
                                (id, user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio, last_update, deadline_time, sla_plan_id) VALUES (:max_id_res_ticket, :user_init_id, :user_to_id, :n,:subj, :msg,:max_id,:unit_id, :status, :hashname, :prio, :nz, :deadline_time, :sla_plan_id)');
                $stmt->execute(array(
                    ':max_id_res_ticket' => $max_id_res_ticket,
                    ':user_init_id' => $user_init_id,
                    ':user_to_id' => $user_to_id,
                    ':subj' => $subj,
                    ':msg' => $msg,
                    ':max_id' => $max_id,
                    ':unit_id' => $unit_id,
                    ':status' => $status,
                    ':hashname' => $hashname,
                    ':prio' => $prio,
                    ':n' => $CONF['now_dt'],
                    ':nz' => $CONF['now_dt'],
                    ':deadline_time' => $deadline_time,
                    ':sla_plan_id' => $sla_plan_id
                ));
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id, to_user_id, to_unit_id) values (:create, :n, :unow, :max_id_res_ticket, :user_to_id, :unit_id)');
                $unow = $_SESSION['helpdesk_user_id'];
                $stmt->execute(array(
                    ':create' => 'create',
                    ':unow' => $unow,
                    ':max_id_res_ticket' => $max_id_res_ticket,
                    ':user_to_id' => $user_to_id,
                    ':unit_id' => $unit_id,
                    ':n' => $CONF['now_dt']
                ));
                
                //if ($CONF_MAIL['active'] == "true") {
                send_notification('ticket_create', $max_id_res_ticket);
                insert_ticket_info($max_id_res_ticket, 'web');
                
                //              }
                
                echo ($hashname);
            }
            if ($type == "edit") {
                
                $hashname = ($_POST['hashname']);
                $if_cl = get_user_val_by_id($client_id_param, 'is_client');
                
                if ($if_cl == "1") {
                    
                    $stmt = $dbConnection->prepare('update users set tel=:client_tel, login=:client_login, unit_desc=:unit_desc, adr=:client_adr, email=:client_mail, posada=:client_posada where id=:client_id_param');
                    
                    $stmt->execute(array(
                        ':client_tel' => $client_tel,
                        ':client_login' => $client_login,
                        ':unit_desc' => $unit_desc,
                        ':client_adr' => $client_adr,
                        ':client_mail' => $client_mail,
                        ':client_posada' => $client_posada,
                        ':client_id_param' => $client_id_param
                    ));
                }
                
                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM tickets");
                $stmt->execute();
                $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);
                
                $max_id_res_ticket = $max_id_ticket[0] + 1;
                
                $stmt = $dbConnection->prepare('INSERT INTO tickets
                                (id, user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio, last_update, deadline_time, sla_plan_id) VALUES (:max_id_res_ticket, :user_init_id, :user_to_id, :n,:subj, :msg,:max_id,:unit_id, :status, :hashname, :prio, :nz, :deadline_time, :sla_plan_id)');
                $stmt->execute(array(
                    ':max_id_res_ticket' => $max_id_res_ticket,
                    ':user_init_id' => $user_init_id,
                    ':user_to_id' => $user_to_id,
                    ':subj' => $subj,
                    ':msg' => $msg,
                    ':max_id' => $client_id_param,
                    ':unit_id' => $unit_id,
                    ':status' => $status,
                    ':hashname' => $hashname,
                    ':prio' => $prio,
                    ':n' => $CONF['now_dt'],
                    ':nz' => $CONF['now_dt'],
                    ':deadline_time' => $deadline_time,
                    ':sla_plan_id' => $sla_plan_id
                ));
                
                $unow = $_SESSION['helpdesk_user_id'];
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id, to_user_id, to_unit_id) values (:create, :n, :unow, :max_id_res_ticket, :user_to_id, :unit_id)');
                
                $stmt->execute(array(
                    ':create' => 'create',
                    ':unow' => $unow,
                    ':max_id_res_ticket' => $max_id_res_ticket,
                    ':user_to_id' => $user_to_id,
                    ':unit_id' => $unit_id,
                    ':n' => $CONF['now_dt']
                ));
                
                //echo("dd");
                //if ($CONF_MAIL['active'] == "true") {
                send_notification('ticket_create', $max_id_res_ticket);
                insert_ticket_info($max_id_res_ticket, 'web');
                
                //                }
                echo ($hashname);
            }
            
            if ($type == "client") {
                $deadline_time = strip_tags(xss_clean($_POST['deadline_time']));
                if ($deadline_time == "NULL") {
                    $deadline_time = NULL;
                }
                $hashname = ($_POST['hashname']);
                $user_init_id = $_SESSION['helpdesk_user_id'];
                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM tickets");
                $stmt->execute();
                $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);
                
                $max_id_res_ticket = $max_id_ticket[0] + 1;
                
                $stmt = $dbConnection->prepare('INSERT INTO tickets
                                (id, user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio, last_update,deadline_time, sla_plan_id) VALUES (:max_id_res_ticket, :user_init_id, :user_to_id, :n,:subj, :msg,:max_id,:unit_id, :status, :hashname, :prio, :nz, :deadline_time, :sla_plan_id)');
                
                $stmt->execute(array(
                    ':max_id_res_ticket' => $max_id_res_ticket,
                    ':user_init_id' => $user_init_id,
                    ':user_to_id' => $user_to_id,
                    ':subj' => $subj,
                    ':msg' => $msg,
                    ':max_id' => $_SESSION['helpdesk_user_id'],
                    ':unit_id' => $unit_id,
                    ':status' => $status,
                    ':hashname' => $hashname,
                    ':prio' => $prio,
                    ':n' => $CONF['now_dt'],
                    ':nz' => $CONF['now_dt'],
                    ':deadline_time' => $deadline_time,
                    ':sla_plan_id' => $sla_plan_id
                ));
                
                $unow = $_SESSION['helpdesk_user_id'];
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id, to_user_id, to_unit_id) values (:create, :n, :unow, :max_id_res_ticket, :user_to_id, :unit_id)');
                
                $stmt->execute(array(
                    ':create' => 'create',
                    ':unow' => $unow,
                    ':max_id_res_ticket' => $max_id_res_ticket,
                    ':user_to_id' => $user_to_id,
                    ':unit_id' => $unit_id,
                    ':n' => $CONF['now_dt']
                ));
                
                //??????????????????????????????????????????????????????????????
                
                //echo("dd");
                //if ($CONF_MAIL['active'] == "true") {
                send_notification('ticket_create', $max_id_res_ticket);
                insert_ticket_info($max_id_res_ticket, 'web');
                
                //  }
                echo ($hashname);
            }
            validate_file_by_ticket($hashname);
            //check_unlinked_file();
        }
    }
}
?>
