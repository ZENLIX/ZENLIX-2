<?php
session_start();
include_once ("../functions.inc.php");
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    //if (validate_admin($_SESSION['helpdesk_user_id'])) {
    
    $CONF['title_header'] = lang('VIEWUSER_title') . " - " . $CONF['name_of_firm'];
    
    include ("head.inc.php");
    include ("navbar.inc.php");
    
    $rkeys = array_keys($_GET);
    
    //print_r($rkeys);
    
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
        $find_user = true;
    } 
    else {
        
        $find_user = false;
    }
    
    $user_last_time_status = false;
    if ($user_last_time) {
        $user_last_time_status = true;
    }
    
    $canWriteMessage = false;
    if ($user_id != $_SESSION['helpdesk_user_id']) {
        $canWriteMessage = true;
    }
    
    $uViewAdr = false;
    if (($user_adr) && ($user_unit)) {
        $uViewAdr = true;
    }
    
    $canUserSkype = false;
    if ($user_skype) {
        $canUserSkype = true;
    }
    
    ////////////////////////////////////////////
    
    $ufieldsStatus = false;
    $ufields = array();
    $stmtf = $dbConnection->prepare('SELECT user_data.field_val as udf, user_data.field_name as udfn from user_data,user_fields where user_data.field_id=user_fields.id and user_data.user_id=:uid and user_fields.for_client=1 and user_fields.status=1');
    $stmtf->execute(array(
        ':uid' => $user_id
    ));
    $resf = $stmtf->fetchAll();
    
    if (!empty($resf)) {
        $ufieldsStatus = true;
        
        foreach ($resf as $fv) {
            
            array_push($ufields, array(
                'udfn' => $fv['udfn'],
                'udf' => $fv['udf']
            ));
        }
    }
    
    ////////////////////////////////////////////
    
    $ufieldsStatus2 = false;
    $ufields2 = array();
    if (get_user_val_by_id($_SESSION['helpdesk_user_id'], 'priv') <> "1") {
        $stmtf = $dbConnection->prepare('SELECT user_data.field_val as udf, user_data.field_name as udfn from user_data,user_fields where user_data.field_id=user_fields.id and user_data.user_id=:uid and user_fields.for_client=0 and user_fields.status=1');
        $stmtf->execute(array(
            ':uid' => $user_id
        ));
        $resf = $stmtf->fetchAll();
        
        if (!empty($resf)) {
            $ufieldsStatus2 = true;
            
            foreach ($resf as $fv) {
                
                array_push($ufields2, array(
                    'udfn' => $fv['udfn'],
                    'udf' => $fv['udf']
                ));
            }
        }
    }
    
    $check_admin_user_priv = false;
    if (check_admin_user_priv($user_id)) {
        $check_admin_user_priv = true;
    }
    
    $someStatStatus_one = false;
    $someStatStatus_arr = array();
    $stmt = $dbConnection->prepare('select id, subj, date_create, hash_name from tickets where status=0 and lock_by=:u order by id desc');
    $stmt->execute(array(
        ':u' => $user_id
    ));
    $result = $stmt->fetchAll();
    
    if (empty($result)) {
        
        $someStatStatus_one = false;
    } 
    else if (!empty($result)) {
        $someStatStatus_one = true;
        foreach ($result as $row) {
            array_push($someStatStatus_arr, array(
                'hash_name' => $row['hash_name'],
                'id' => $row['id'],
                'subj' => $row['subj'],
                'date_create' => $row['date_create']
            ));
        }
    }
    
    $someStatStatus_two = false;
    $someStatStatus_arr_two = array();
    $stmt = $dbConnection->prepare('select id, subj, date_create, hash_name from tickets where status=0 and lock_by=0 and (find_in_set(:u,user_to_id)) order by id desc');
    $stmt->execute(array(
        ':u' => $user_id
    ));
    $result = $stmt->fetchAll();
    
    if (empty($result)) {
        
        $someStatStatus_two = false;
    } 
    else if (!empty($result)) {
        $someStatStatus_two = true;
        foreach ($result as $row) {
            array_push($someStatStatus_arr_two, array(
                'hash_name' => $row['hash_name'],
                'id' => $row['id'],
                'subj' => $row['subj'],
                'date_create' => $row['date_create']
            ));
        }
    }
    
    /////////////////////////////////////////








$tfiles_arr = array();
        $tfiles = false;
        $stmt = $dbConnection->prepare('SELECT * FROM user_files where user_id=:tid');
        $stmt->execute(array(
            ':tid' => $user_id
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
                    'filehash'=>$r['file_hash'],
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
        $template = $twig->loadTemplate('view_user.view.tmpl');
        
        // передаём в шаблон переменные и значения
        // выводим сформированное содержание
        echo $template->render(array(
            'hostname' => $CONF['hostname'],
            'name_of_firm' => $CONF['name_of_firm'],
            'VIEWUSER_title' => lang('VIEWUSER_title') ,
            'VIEWUSER_title_ext' => lang('VIEWUSER_title_ext') ,
            'find_user' => $find_user,
            'TICKET_t_no' => lang('TICKET_t_no') ,
            'user_status' => $user_status,
            'user_fio' => $user_fio,
            'user_img' => $user_img,
            'USER_DEL_main' => lang('USER_DEL_main') ,
            'USER_DEL_info' => lang('USER_DEL_info') ,
            'user_posada' => $user_posada,
            'user_last_time_status' => $user_last_time_status,
            'uniq_id' => $uniq_id,
            'stats_last_time' => lang('stats_last_time') ,
            'user_last_time' => $user_last_time,
            'canWriteMessage' => $canWriteMessage,
            'EXT_do_write_message' => lang('EXT_do_write_message') ,
            'P_main' => lang('P_main') ,
            'get_user_status' => get_user_status($user_id) ,
            'uViewAdr' => $uViewAdr,
            'APPROVE_adr' => lang('APPROVE_adr') ,
            'user_adr' => $user_adr,
            'user_unit' => $user_unit,
            'canUserSkype' => $canUserSkype,
            'user_skype' => $user_skype,
            'user_tel' => $user_tel,
            'APPROVE_tel' => lang('APPROVE_tel') ,
            'user_mail' => $user_mail,
            'APPROVE_mail' => lang('APPROVE_mail') ,
            'FIELD_add_title' => lang('FIELD_add_title') ,
            'ufields' => $ufields,
            'ufieldsStatus' => $ufieldsStatus,
            'ufields2' => $ufields2,
            'ufieldsStatus2' => $ufieldsStatus2,
            'is_client' => $is_client,
            'get_total_tickets_out' => get_total_tickets_out($user_id) ,
            'get_total_tickets_count' => get_total_tickets_count() ,
            'EXT_t_created' => lang('EXT_t_created') ,
            'get_total_tickets_lock' => get_total_tickets_lock($user_id) ,
            'EXT_t_locked' => lang('EXT_t_locked') ,
            'get_total_tickets_ok' => get_total_tickets_ok($user_id) ,
            'EXT_t_oked' => lang('EXT_t_oked') ,
            'check_admin_user_priv' => $check_admin_user_priv,
            'PROFILE_tickets_lock' => lang('PROFILE_tickets_lock') ,
            'someStatStatus_one' => $someStatStatus_one,
            'someStatStatus_arr' => $someStatStatus_arr,
            'MSG_no_records' => lang('MSG_no_records') ,
            'NEW_subj' => lang('NEW_subj') ,
            'TICKET_t_date' => lang('TICKET_t_date') ,
            'PROFILE_tickets_free' => lang('PROFILE_tickets_free') ,
            'someStatStatus_two' => $someStatStatus_two,
            'someStatStatus_arr_two' => $someStatStatus_arr_two,
            'tfiles_arr'=>$tfiles_arr,
            'tfiles'=>$tfiles,
            'TICKET_file_list'=>lang('TICKET_file_list'),
            'user_id'=>$user_id,
            'PORTAL_fileplace'=>lang('PORTAL_fileplace')
        ));
    }
    catch(Exception $e) {
        die('ERROR: ' . $e->getMessage());
    }
    
    include ("footer.inc.php");

    
    //}
    
    
} 
else {
    include 'auth.php';
}
?>