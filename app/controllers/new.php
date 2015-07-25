<?php
$CONF['title_header'] = lang('NEW_title') . " - " . $CONF['name_of_firm'];

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        //check_unlinked_file();
        //echo get_userlogin_byid($_SESSION['helpdesk_user_id']);
        
        class new_ticket_form
        {
            
            public function get_to_unit_list() {
                global $dbConnection;
                
                $res = array();
                $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n');
                $stmt->execute(array(
                    ':n' => '0'
                ));
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $row) {
                    
                    //echo($row['label']);
                    $row['label'] = $row['label'];
                    $row['value'] = (int)$row['value'];
                    
                    $s1 = "";
                    if (get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_unit_id') == $row['value']) {
                        $s1 = "selected";
                    }
                    
                    array_push($res, array(
                        'label' => $row['label'],
                        'value' => $row['value'],
                        'sel' => $s1
                    ));
                }
                
                return $res;
            }
            
            public function get_to_user_list() {
                
                global $dbConnection;
                
                $res = array();
                
                $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=:n and id !=:system and is_client=0 order by fio ASC');
                $stmt->execute(array(
                    ':n' => '1',
                    ':system' => '1'
                ));
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $row) {
                    
                    //echo($row['label']);
                    $row['label'] = $row['label'];
                    $row['value'] = (int)$row['value'];
                    
                    $st_sel = "";
                    $mass = explode(",", get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_user_id'));
                    if (in_array($row['value'], $mass)) {
                        $st_sel = "selected";
                    }
                    
                    if (get_user_status_text($row['value']) == "online") {
                        $s = "online";
                    } 
                    else if (get_user_status_text($row['value']) == "offline") {
                        $s = "offline";
                    }
                    
                    array_push($res, array(
                        
                        'label' => nameshort($row['label']) ,
                        'value' => $row['value'],
                        'st_sel' => $st_sel,
                        'df' => $s
                    ));
                }
                return $res;
            }
            
            public function get_subj_list() {
                global $dbConnection;
                $res = array();
                $stmt = $dbConnection->prepare('SELECT name FROM subj order by sort_id ASC');
                $stmt->execute();
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $row) {
                    array_push($res, array(
                        'name' => $row['name']
                    ));
                }
                return $res;
            }
            
            public function get_fields_forms() {
                global $dbConnection;
                $res = array();
                $stmt = $dbConnection->prepare('SELECT * FROM ticket_fields where status=:n');
                $stmt->execute(array(
                    ':n' => '1'
                ));
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $row) {
                    
                    if ($row['t_type'] == "text") {
                        $v = $row['value'];
                        if ($row['value'] == "0") {
                            $v = "";
                        }
                        $vr = $v;
                    } 
                    else if ($row['t_type'] == "textarea") {
                        $v = $row['value'];
                        if ($row['value'] == "0") {
                            $v = "";
                        }
                        $vr = $v;
                    } 
                    else if ($row['t_type'] == "select") {
                        $vr = array();
                        $v = $row['value'];
                        if ($row['value'] == "0") {
                            $v = "";
                        }
                        $v = explode(",", $row['value']);
                        foreach ($v as $value) {
                            array_push($vr, $value);
                        }
                    } 
                    else if ($row['t_type'] == "multiselect") {
                        $vr = array();
                        $v = $row['value'];
                        if ($row['value'] == "0") {
                            $v = "";
                        }
                        $v = explode(",", $row['value']);
                        foreach ($v as $value) {
                            array_push($vr, $value);
                        }
                    }
                    
                    array_push($res, array(
                        'name' => $row['name'],
                        'hash' => $row['hash'],
                        't_type' => $row['t_type'],
                        'value' => $vr,
                        'placeholder' => $row['placeholder']
                    ));
                }
                return $res;
            }
        }
        
        $new_ticket_form = new new_ticket_form();
        
        $to_unit_list = $new_ticket_form->get_to_unit_list();
        $to_user_list = $new_ticket_form->get_to_user_list();
        $subj_list = $new_ticket_form->get_subj_list();
        
        $fields_forms = $new_ticket_form->get_fields_forms();
        
        $ok_msg = false;
        $h=NULL;
        if (isset($_GET['ok'])) {
            if (isset($_GET['h'])) {
                $h = $_GET['h'];
                $ok_msg = true;
            }
        }
        
        if ($CONF['fix_subj'] == "true") {
            $mut = "";
        }
        if ($CONF['fix_subj'] == "true_multiple") {
            $mut = "multiple";
        }
        
        ob_start();
        
        //Start output buffer
        get_sla_view_select_box();
        $get_sla_view_select_box = ob_get_contents();
        
        //Grab output
        ob_end_clean();
        
        try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/views');
            
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
            $template = $twig->loadTemplate('new.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'NEW_title' => lang('NEW_title') ,
                'hostname' => $CONF['hostname'],
                'name_of_firm' => $CONF['name_of_firm'],
                'ok_msg' => $ok_msg,
                'h' => $h,
                'NEW_to_unit_desc' => lang('NEW_to_unit_desc') ,
                'NEW_ok' => lang('NEW_ok') ,
                'NEW_ok_1' => lang('NEW_ok_1') ,
                'NEW_ok_2' => lang('NEW_ok_2') ,
                'NEW_ok_3' => lang('NEW_ok_3') ,
                'NEW_ok_4' => lang('NEW_ok_4') ,
                'NEW_from' => lang('NEW_from') ,
                'NEW_from_desc' => lang('NEW_from_desc') ,
                'NEW_fio' => lang('NEW_fio') ,
                'NEW_fio_desc' => lang('NEW_fio_desc') ,
                'uniq_id' => get_user_val('uniq_id') ,
                'CREATE_TICKET_ME' => lang('CREATE_TICKET_ME') ,
                'NEW_to_desc' => lang('NEW_to_desc') ,
                'def_unit_id' => get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_unit_id') ,
                'NEW_to' => lang('NEW_to') ,
                'NEW_to_unit' => lang('NEW_to_unit') ,
                'to_unit_list' => $to_unit_list,
                'NEW_to_user' => lang('NEW_to_user') ,
                'to_user_list' => $to_user_list,
                'NEW_prio' => lang('NEW_prio') ,
                'NEW_prio_low' => lang('NEW_prio_low') ,
                'NEW_prio_norm' => lang('NEW_prio_norm') ,
                'NEW_prio_high' => lang('NEW_prio_high') ,
                'NEW_prio_high_desc' => lang('NEW_prio_high_desc') ,
                'sla_system' => get_conf_param('sla_system') ,
                'fix_subj' => $CONF['fix_subj'],
                'mut' => $mut,
                'NEW_subj' => lang('NEW_subj') ,
                'NEW_subj_msg' => lang('NEW_subj_msg') ,
                'subj_list' => $subj_list,
                'get_sla_view_select_box' => $get_sla_view_select_box,
                'NEW_MSG' => lang('NEW_MSG') ,
                'NEW_MSG_msg' => lang('NEW_MSG_msg') ,
                'NEW_MSG_ph' => lang('NEW_MSG_ph') ,
                'EXT_fill_msg' => lang('EXT_fill_msg') ,
                'ticket_last_time' => get_conf_param('ticket_last_time') ,
                'TICKET_deadline_text' => lang('TICKET_deadline_text') ,
                'date_dl' => date("Y-m-d H:i:s") ,
                'file_uploads' => $CONF['file_uploads'],
                'TICKET_file_add' => lang('TICKET_file_add') ,
                'PORTAL_fileplace' => lang('PORTAL_fileplace') ,
                'add_fields_forms' => $fields_forms,
                'NEW_button_create' => lang('NEW_button_create') ,
                'NEW_button_reset' => lang('NEW_button_reset') ,
                'hashname' => md5(time()) ,
                'user_init_id' => $_SESSION['helpdesk_user_id'],
                'user_name_login' => get_user_val('login') ,
                'ftypes' => $CONF['file_types'],
                'file_size' => $CONF['file_size']
            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
        
        include ("footer.inc.php");
?>



<?php
    }
} 
else {
    include 'auth.php';
}
?>
