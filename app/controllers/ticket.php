<?php
//session_start();

//include ("../functions.inc.php");
$rkeys = array_keys($_GET);

$CONF['title_header'] = lang('TICKET_name') . " #" . get_ticket_id_by_hash($rkeys[0]) . " (" . get_ticket_val_by_hash('subj', $rkeys[0]) . ")" . " - " . $CONF['name_of_firm'];

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    include ("head.inc.php");
    include ("navbar.inc.php");
    
    //echo $rkeys[1];
    //$hn=($_GET['hash']);
    $hn = $rkeys[0];
    $stmt = $dbConnection->prepare('SELECT 
                            * from tickets
                            where hash_name=:hn');
    $stmt->execute(array(
        ':hn' => $hn
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $t_true = false;
    
    if (!empty($row)) {
        $t_true = true;
        
        //echo "ok";
        $status_lock=NULL;
        //foreach ($res1 as $row) {}
        
        $lock_by = $row['lock_by'];
        $ok_by = $row['ok_by'];
        $ok_date = $row['ok_date'];
        $cid = $row['client_id'];
        $tid = $row['id'];
        $arch = $row['arch'];
        $subj = $row['subj'];
        $status_ok = $row['status'];
        $ms = $row['msg'];
        $pr = $row['prio'];
        $dcr = $row['date_create'];
        
        $sla_plan = $row['sla_plan_id'];
        
        if ($arch == 1) {
            $st = "<span class=\"label label-default\"><i class=\"fa fa-archive\"></i> " . lang('TICKET_status_arch') . "</span>";
        }
        if ($arch == 0) {
            if ($status_ok == 1) {
                $st = "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> " . lang('TICKET_status_ok') . " " . nameshort(name_of_user_ret_nolink($ok_by)) . "</span>";
            }
            if ($status_ok == 0) {
                if ($lock_by <> 0) {
                    $st = "<span class=\"label label-warning\"><i class=\"fa fa-gavel\"></i> " . lang('TICKET_status_lock') . " " . name_of_user_ret_nolink($lock_by) . "</span>";
                }
                if ($lock_by == 0) {
                    $st = "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> " . lang('TICKET_status_new') . "</span>";
                }
            }
        }
        
        if ($row['user_to_id'] <> 0) {
            $to_text = "<div class=''>" . name_of_user_ret($row['user_to_id']) . "</div>";
        }
        if ($row['user_to_id'] == 0) {
            $to_text = "<strong>" . lang('t_list_a_all') . "</strong> " . lang('T_from') . " " . view_array(get_unit_name_return($row['unit_id']));
        }
        
        if ($row['is_read'] == "0") {
            
            $res = $dbConnection->prepare("update tickets set is_read=:n where id=:tid");
            $res->execute(array(
                ':n' => '1',
                ':tid' => $tid
            ));
        }
        
        if ($lock_by <> "0") {
            if ($lock_by == $_SESSION['helpdesk_user_id']) {
                $status_lock = "me";
                
                //$lock_disabled="";
                $lock_text = "<i class=\"fa fa-unlock\"></i> " . lang('TICKET_action_unlock') . "";
                $lock_status = "unlock";
            } 
            else {
                
                $status_lock = "you";
                
                $lock_status = "unlock";
                $lock_text = "<i class=\"fa fa-unlock\"></i> " . lang('TICKET_action_unlock') . "";
            }
        }
        if ($lock_by == "0") {
            
            $lock_text = "<i class=\"fa fa-lock\"></i> " . lang('TICKET_action_lock') . "";
            $lock_status = "lock";
        }
        
        if ($status_ok == "1") {
            $status_ok_text = lang('TICKET_action_nook');
            $status_ok_status = "ok";
        }
        
        if ($status_ok == "0") {
            $status_ok_text = "<i class=\"fa fa-check\"></i> " . lang('TICKET_action_ok') . "";
            $status_ok_status = "no_ok";
        }
        
        $inituserid_flag = 0;
        if ($row['user_init_id'] == $_SESSION['helpdesk_user_id']) {
            $inituserid_flag = 1;
        }
        $prio_style=array(
            'normal'=>NULL,
            'low'=>NULL,
            'high'=>NULL
            );
        $prio = "<span class=\"label label-info\"><i class=\"fa fa-minus\"></i> " . lang('t_list_a_p_norm') . "</span>";
        if ($row['prio'] == "1") {
            $prio_style['normal'] = "active";
        } 
        else if ($row['prio'] == "0") {
            $prio = "<span class=\"label label-primary\"><i class=\"fa fa-arrow-down\"></i> " . lang('t_list_a_p_low') . "</span>";
            $prio_style['low'] = "active";
        } 
        else if ($row['prio'] == "2") {
            $prio = "<span class=\"label label-danger\"><i class=\"fa fa-arrow-up\"></i> " . lang('t_list_a_p_high') . "</span>";
            $prio_style['high'] = "active";
        }
        
        $refresh = false;
        if (isset($_GET['refresh'])) {
            $refresh = true;
        }
        
        $tdata_arr = array();
        $tdata = false;
        $stmts = $dbConnection->prepare('SELECT * FROM ticket_data where ticket_hash=:n');
        $stmts->execute(array(
            ':n' => $hn
        ));
        $res11 = $stmts->fetchAll();
        
        if (!empty($res11)) {
            $tdata = true;
            foreach ($res11 as $rown) {
                
                $stmt2 = $dbConnection->prepare('SELECT name from ticket_fields where id=:tm and status=:s');
                $stmt2->execute(array(
                    ':tm' => $rown['field_id'],
                    ':s' => '1'
                ));
                
                $tt = $stmt2->fetch(PDO::FETCH_ASSOC);
                
                array_push($tdata_arr, array(
                    
                    'field_name' => $rown['field_name'],
                    'field_val' => $rown['field_val']
                ));
            }
        }
        
        $can_edit = false;
        if ((($inituserid_flag == 1) && ($arch == 0)) || (priv_status($_SESSION['helpdesk_user_id']) == "2") || (priv_status($_SESSION['helpdesk_user_id']) == "0")) {
            $can_edit = true;
        }
        
        $tfiles_arr = array();
        $tfiles = false;
        $stmt = $dbConnection->prepare('SELECT * FROM files where ticket_hash=:tid');
        $stmt->execute(array(
            ':tid' => $hn
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
                    $ct = ' <a href=\'' . $CONF['hostname'] . 'action?mode=download_file&file=' . $r['file_hash'] . '\'>' . $r['original_name'] . '</a>';
                    $ic = get_file_icon($r['file_hash']);
                }
                
                array_push($tfiles_arr, array(
                    
                    'ic' => $ic,
                    'ct' => $ct,
                    'size' => round(($r['file_size'] / (1024 * 1024)) , 2)
                ));
            }
        }
        
        $user_id = $_SESSION['helpdesk_user_id'];
        $unit_user = unit_of_user($user_id);
        $ps = priv_status($user_id);
        
        $lo = "no";
        
        /////////если пользователь///////////////////////////////////////////////////////////////////////////////////////////
        if ($ps == 1) {
            
            //ЗАявка не выполнена ИЛИ выполнена мной
            //ЗАявка не заблокирована ИЛИ заблокирована мной
            if ($row['user_init_id'] == $user_id) {
                
                $lo = "yes";
            }
            
            if ($row['user_init_id'] <> $user_id) {
                
                if (($status_ok == 0) || (($status_ok == 1) && ($ok_by == $user_id))) {
                    
                    if (($lock_by == 0) || ($lock_by == $user_id)) {
                        $lo = "yes";
                    }
                }
            }
        }
        
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        /////////если нач отдела/////////////////////////////////////////////////////////////////////////////////////////////
        if ($ps == 0) {
            $lo = "yes";
        }
        
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        //////////главный админ//////////////////////////////////////////////////////////////////////////////////////////////
        if ($ps == 2) {
            $lo = "yes";
        }
        
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        if ($lo == "no") {
            $lock_disabled = "disabled=\"disabled\"";
        } 
        else if ($lo == "yes") {
            $lock_disabled = "";
        }
        
        $unit_arr = array();
        
        $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
        $stmt->execute(array(
            ':n' => '0',
            ':s' => '1'
        ));
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row3) {
            
            array_push($unit_arr, array(
                'value' => $row3['value'],
                'label' => $row3['label']
            ));
        }
        
        $user_arr = array();
        
        $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=:n and id !=:system and is_client=0 order by fio ASC');
        $stmt->execute(array(
            ':n' => '1',
            ':system' => '1'
        ));
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row2) {
            
            //echo($row['label']);
            $row2['label'] = $row2['label'];
            $row2['value'] = (int)$row2['value'];
            
            if (get_user_status_text($row2['value']) == "online") {
                $s = "online";
            } 
            else if (get_user_status_text($row2['value']) == "offline") {
                $s = "offline";
            }
            
            array_push($user_arr, array(
                's' => $s,
                'value' => $row2['value'],
                'label' => nameshort($row2['label'])
            ));
        }
        
        $slshow = false;
        if (get_conf_param('sla_system') == "true") {
            
            if ($sla_plan != "0") {
                
                $stmt_sla = $dbConnection->prepare('SELECT * from sla_plans where id=:uid');
                $stmt_sla->execute(array(
                    ':uid' => $sla_plan
                ));
                $row_sla = $stmt_sla->fetch(PDO::FETCH_ASSOC);
                
                if ($pr == "0") {
                    $sla_react = $row_sla['reaction_time_low_prio'];
                    $sla_work = $row_sla['work_time_low_prio'];
                    $sla_deadline = $row_sla['deadline_time_low_prio'];
                } 
                else if ($pr == "1") {
                    $sla_react = $row_sla['reaction_time_def'];
                    $sla_work = $row_sla['work_time_def'];
                    $sla_deadline = $row_sla['deadline_time_def'];
                } 
                else if ($pr == "2") {
                    $sla_react = $row_sla['reaction_time_high_prio'];
                    $sla_work = $row_sla['work_time_high_prio'];
                    $sla_deadline = $row_sla['deadline_time_high_prio'];
                }
                
                if (get_ticket_time_reaction_sec($tid) == 0) {
                    $per = floor((get_ticket_time_reaction_sec_no_lock($tid) * 100) / $sla_react);
                } 
                else if (get_ticket_time_reaction_sec($tid) != 0) {
                    $per = floor((get_ticket_time_reaction_sec($tid) * 100) / $sla_react);
                }
                if ($per > 100) {
                    $per = 100;
                }
                
                if (get_ticket_time_lock_sec($tid) == 0) {
                    $perw = 0;
                } 
                else if (get_ticket_time_lock_sec($tid) != 0) {
                    $perw = floor((get_ticket_time_lock_sec($tid) * 100) / $sla_work);
                }
                if ($perw > 100) {
                    $perw = 100;
                }
                
                if ($sla_react == "0") {
                    $sla_react = lang('SLA_not_sel');
                } 
                else if ($sla_react != "0") {
                    
                    $sla_react = "<time id=\"f\" datetime=\"" . $sla_react . "\"></time>";
                }
                
                if ($sla_work == "0") {
                    $sla_work = lang('SLA_not_sel');
                } 
                else if ($sla_work != "0") {
                     
                    $sla_work = "<time id=\"f\" datetime=\"" . $sla_work . "\"></time>";
                }
                
                if ($sla_deadline == "0") {
                    $left_secr = lang('SLA_not_sel');
                    $ls = "false";
                } 
                else if ($sla_deadline != "0") {
                    
                    //$left_sec=(strtotime($dcr)+$sla_deadline)-time();
                    
                    if ($status_ok == "0") {
                        
                        $left_sec = (strtotime($dcr) + $sla_deadline) - time();
                        if ($left_sec < 0) {
                            $left_secr = lang('SLA_time_old');
                            $ls = "false";
                        }
                        if ($left_sec >= 0) {
                            $left_secr = lang('SLA_deadline_t') . ": " . "<time id=\"f\" datetime=\"" . $left_sec . "\"></time>";
                            $ls = "true";
                        }
                        
                        $perd = floor(((time() - strtotime($dcr)) * 100) / $sla_deadline);
                    }
                    
                    if ($status_ok == "1") {
                        
                        $stmt_dl = $dbConnection->prepare('SELECT date_op from ticket_log where ticket_id=:tid and msg=:m order by date_op DESC limit 1');
                        $stmt_dl->execute(array(
                            ':tid' => $tid,
                            ':m' => 'ok'
                        ));
                        $tts_dl = $stmt_dl->fetch(PDO::FETCH_ASSOC);
                        
                        $left_sec = (strtotime($dcr) + $sla_deadline) - strtotime($tts_dl['date_op']);
                        
                        $ok_by_time = (strtotime($tts_dl['date_op']) - strtotime($dcr));
                        
                        $perd = floor(((strtotime($tts_dl['date_op']) - strtotime($dcr)) * 100) / $sla_deadline);
                        
                        if ($left_sec < 0) {
                            $left_secr = lang('SLA_time_old');
                        }
                        
                        if ($left_sec > 0) {
                            $left_secr = lang('SLA_deadline_ok_by') . " " . "<time id=\"f\" datetime=\"" . $ok_by_time . "\"></time>";
                        }
                        $ls = "false";
                    }
                    
                    /*
                    Если выполнено
                    
                    время выполнения - время создания = время всей заявки
                    
                    время создания + время деадлайн = допустимое время заявки
                    
                    если время всей заявки > допустимое время заявки то просрочено
                    иначе: то: время все заявки показать
                    
                    ---------------
                    
                    если не выполнена
                    */
                    
                    //$left_sec=(strtotime($dcr)+$sla_deadline)-time();
                    
                    
                }
                
                if ($perd > 100) {
                    $perd = 100;
                }
                
                if ($status_ok == 1) {
                    $sl = "false";
                }
                if ($status_ok == 0) {
                    if ($lock_status == "lock") {
                        $sl = "false";
                    } 
                    else if ($lock_status == "unlock") {
                        $sl = "true";
                    }
                }
                
                $slshow = true;
            }
        }
        
        $val_admin = false;
        if (validate_admin($_SESSION['helpdesk_user_id'])) {
            $val_admin = true;
        }
        
        ob_start();
        
        //Start output buffer
        view_comment($tid);
        $view_comment = ob_get_contents();
        
        //Grab output
        ob_end_clean();
        
        ob_start();
        
        //Start output buffer
        view_log($tid);
        $view_log = ob_get_contents();
        
        //Grab output
        ob_end_clean();
        
        ob_start();
        get_client_info_ticket($cid);
        $get_client_info_ticket = ob_get_contents();
        
        //Grab output
        ob_end_clean();
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
        $template = $twig->loadTemplate('ticket.view.tmpl');
        
        // передаём в шаблон переменные и значения
        // выводим сформированное содержание
        echo $template->render(array(
            'TICKET_name' => lang('TICKET_name') ,
            'id' => $row['id'],
            'subj' => make_html($row['subj'], 'no') ,
            'hostname' => $CONF['hostname'],
            'name_of_firm' => $CONF['name_of_firm'],
            'TICKET_msg_updated' => lang('TICKET_msg_updated') ,
            'refresh' => $refresh,
            'gt_ir' => get_ticket_info_source($row['id']) ,
            'date_create' => $row['date_create'],
            'get_deadline_label' => get_deadline_label($row['id']) ,
            'TICKET_t_from' => lang('TICKET_t_from') ,
            'user_init' => name_of_user_ret($row['user_init_id']) ,
            'TICKET_t_prio' => lang('TICKET_t_prio') ,
            'prio' => $prio,
            'TICKET_t_to' => lang('TICKET_t_to') ,
            'to_text' => $to_text,
            'TICKET_t_status' => lang('TICKET_t_status') ,
            'st' => $st,
            'tdata' => $tdata,
            'tdata_arr' => $tdata_arr,
            'msg' => make_html($row['msg']) ,
            'get_ticket_info' => get_ticket_info($row['id']) ,
            'hn' => $hn,
            'TICKET_print' => lang('TICKET_print') ,
            'CONF_act_edit' => lang('CONF_act_edit') ,
            'can_edit' => $can_edit,
            'tfiles' => $tfiles,
            'tfiles_arr' => $tfiles_arr,
            'get_button_act_status_refer' => get_button_act_status(get_ticket_action_priv($row['id']) , 'refer') ,
            'TICKET_t_refer' => lang('TICKET_t_refer') ,
            'get_button_act_status_lock' => get_button_act_status(get_ticket_action_priv($row['id']) , $lock_status) ,
            'lock_status' => $lock_status,
            'helpdesk_user_id' => $_SESSION['helpdesk_user_id'],
            'tid' => $tid,
            'lock_text' => $lock_text,
            'get_button_act_status_ok' => get_button_act_status(get_ticket_action_priv($row['id']) , $status_ok_status) ,
            'status_ok_text' => $status_ok_text,
            'NEW_to_unit_desc' => lang('NEW_to_unit_desc') ,
            'TICKET_t_refer_to' => lang('TICKET_t_refer_to') ,
            'lock_disabled' => $lock_disabled,
            'NEW_to_unit' => lang('NEW_to_unit') ,
            'unit_arr' => $unit_arr,
            'NEW_to_user' => lang('NEW_to_user') ,
            'user_arr' => $user_arr,
            'TICKET_t_opt' => lang('TICKET_t_opt') ,
            'NEW_MSG_ph_1' => lang('NEW_MSG_ph_1') ,
            'TICKET_t_comment' => lang('TICKET_t_comment') ,
            'TICKET_t_history' => lang('TICKET_t_history') ,
            'view_comment' => $view_comment,
            'TICKET_t_det_ticket' => lang('TICKET_t_det_ticket') ,
            'TICKET_t_comm_ph' => lang('TICKET_t_comm_ph') ,
            'view_log' => $view_log,
            'get_client_info_ticket' => $get_client_info_ticket,
            'slshow' => $slshow,
            'SLA_perf_reaction' => lang('SLA_perf_reaction') ,
            'get_ticket_time_reaction' => get_ticket_time_reaction($tid) ,
            'per' => $per,
            'SLA_REGLAMENT' => lang('SLA_REGLAMENT') ,
            'sla_react' => $sla_react,
            'SLA_perf_work_a' => lang('SLA_perf_work_a') ,
            'sl' => $sl,
            'get_ticket_time_lock' => get_ticket_time_lock($tid) ,
            'perw' => $perw,
            'sla_work' => $sla_work,
            'SLA_perf_deadline_short' => lang('SLA_perf_deadline_short') ,
            'left_secr' => $left_secr,
            'ls' => $ls,
            'perd' => $perd,
            'sla_deadline' => $sla_deadline,
            'val_admin' => $val_admin,
            'TICKET_action_delete_info' => lang('TICKET_action_delete_info') ,
            'TICKET_action_delete' => lang('TICKET_action_delete') ,
            'arch' => $arch,
            'MAIN_attention' => lang('MAIN_attention') ,
            'TICKET_t_in_arch' => lang('TICKET_t_in_arch') ,
            'status_ok' => $status_ok,
            'status_ok_status' => $status_ok_status,
            'TICKET_t_ok' => lang('TICKET_t_ok') ,
            'ok_by_fio' => name_of_user_ret($ok_by) ,
            'ok_date' => $ok_date,
            'TICKET_t_ok_1' => lang('TICKET_t_ok_1') ,
            'lock_by' => $lock_by,
            'status_lock' => $status_lock,
            'TICKET_t_lock' => lang('TICKET_t_lock') ,
            'lock_by_fio' => name_of_user_ret($lock_by) ,
            'TICKET_t_lock_1' => lang('TICKET_t_lock_1') ,
            'TICKET_t_lock_i' => lang('TICKET_t_lock_i') ,
            'pr' => $pr,
            'P_title' => lang('P_title') ,
            'NEW_prio' => lang('NEW_prio') ,
              'NEW_subj'=>lang('NEW_subj'),
            'prio_style_low' => $prio_style['low'],
            'prio_style_normal' => $prio_style['normal'],
            'prio_style_high' => $prio_style['high'],
            'NEW_prio_low' => lang('NEW_prio_low') ,
            'NEW_prio_norm' => lang('NEW_prio_norm') ,
            'NEW_prio_high' => lang('NEW_prio_high') ,
            'NEW_MSG' => lang('NEW_MSG') ,
            'NEW_MSG_msg' => lang('NEW_MSG_msg') ,
            'NEW_MSG_ph' => lang('NEW_MSG_ph') ,
            'EXT_fill_msg' => lang('EXT_fill_msg') ,
            'ms' => $ms,
            'TICKET_file_notupload_one' => lang('TICKET_file_notupload_one') ,
            'JS_save' => lang('JS_save') ,
            't_true' => $t_true,
            'TICKET_t_no' => lang('TICKET_t_no') ,
            'TICKET_file_list' => lang('TICKET_file_list') ,
            'FIELD_add_title' => lang('FIELD_add_title'),
            'PORTAL_fileplace'=>lang('PORTAL_fileplace')
        ));
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
