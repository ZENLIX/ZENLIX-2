<?php
session_start();
include_once "../functions.inc.php";

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        
        $CONF['title_header'] = lang('HELPER_title') . " - " . $CONF['name_of_firm'];
        
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        if (isset($_GET['h'])) {
            $h_check = true;
            $h = ($_GET['h']);
            
            if (isset($_GET['edit'])) {
                $h_check_type = "edit";
                $hn = $h;
                $stmt = $dbConnection->prepare('select id, user_init_id, unit_to_id, dt, title, message, hashname,client_flag,cat_id from helper where hashname=:hn');
                $stmt->execute(array(
                    ':hn' => $hn
                ));
                $fio = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $isclient_status = $fio['client_flag'];
                
                if ($isclient_status == "1") {
                    $isclient_status = "checked";
                } 
                else {
                    $isclient_status = "";
                }
                $cat_id = $fio['cat_id'];
                $u = $fio['unit_to_id'];
                $u = explode(",", $u);
                
                //////////
                $cat_id_arr = array();
                $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM helper_cat order by sort_id ASC');
                $stmt->execute();
                $result = $stmt->fetchAll();
                foreach ($result as $row) {
                    
                    $row['label'] = $row['label'];
                    $row['value'] = (int)$row['value'];
                    
                    //$cat_id
                    
                    $sel_cat = "";
                    
                    if ($cat_id == $row['value']) {
                        $sel_cat = "selected";
                    }
                    array_push($cat_id_arr, array(
                        'value' => $row['value'],
                        'label' => $row['label'],
                        'sel_cat' => $sel_cat
                    ));
                }
                
                //////////
                
                $ar_n = "";
                if (in_array('0', $u)) {
                    $ar_n = "selected";
                }
                
                ////////////////
                $u_arr = array();
                $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps');
                $stmt->execute();
                $result = $stmt->fetchAll();
                
                foreach ($result as $row) {
                    
                    $row['label'] = $row['label'];
                    $row['value'] = (int)$row['value'];
                    
                    $opt_sel = '';
                    foreach ($u as $val) {
                        if ($val == $row['value']) {
                            $opt_sel = "selected";
                        }
                    }
                    array_push($u_arr, array(
                        'value' => $row['value'],
                        'label' => $row['label'],
                        'opt_sel' => $opt_sel
                    ));
                }
                
                ////////////////
                
                ///////////////////////
                $files_res = false;
                $files_arr = array();
                $stmt = $dbConnection->prepare('SELECT * FROM files where ticket_hash=:tid and obj_type=1');
                $stmt->execute(array(
                    ':tid' => $hn
                ));
                $res1 = $stmt->fetchAll();
                if (!empty($res1)) {
                    $files_res = true;
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
                            $ct = ' <a href=\'' . $CONF['hostname'] . 'sys/download.php?' . $r['file_hash'] . '\'>' . $r['original_name'] . '</a>';
                            $ic = get_file_icon($r['file_hash']);
                        }
                    }
                    
                    array_push($files_arr, array(
                        
                        'ic' => $ic,
                        'ct' => $ct,
                        'fm' => round(($r['file_size'] / (1024 * 1024)) , 2) ,
                        'fhash' => $r['file_hash']
                    ));
                }
                
                ///////////////////////
                
                $ar_res = array(
                    'cat_id' => $cat_id,
                    'get_helper_cat_name' => get_helper_cat_name($cat_id) ,
                    'cat_id_arr' => $cat_id_arr,
                    'ar_n' => $ar_n,
                    'u_arr' => $u_arr,
                    'fio_title' => $fio['title'],
                    'isclient_status' => $isclient_status,
                    'fio_message' => $fio['message'],
                    'files_arr' => $files_arr,
                    'files_res' => $files_res,
                    'hn' => $hn
                );
            } 
            else if (!isset($_GET['edit'])) {
                $h_check_type = "no_edit";
                $stmt = $dbConnection->prepare('select id, user_init_id, unit_to_id, dt, title, message, hashname, cat_id, user_edit_id
                            from helper where hashname=:h');
                $stmt->execute(array(
                    ':h' => $h
                ));
                $fio = $stmt->fetch(PDO::FETCH_ASSOC);
                $cat_id = $fio['cat_id'];
                $user_edit_id = $fio['user_edit_id'];
                $user_init_id = $fio['user_init_id'];
                
                ////////////////
                $files_res = false;
                $files_arr = array();
                $stmt = $dbConnection->prepare('SELECT * FROM files where ticket_hash=:tid and obj_type=1');
                $stmt->execute(array(
                    ':tid' => $h
                ));
                $res1 = $stmt->fetchAll();
                if (!empty($res1)) {
                    $files_res = true;
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
                            $ct = ' <a href=\'' . $CONF['hostname'] . 'sys/download.php?' . $r['file_hash'] . '\'>' . $r['original_name'] . '</a>';
                            $ic = get_file_icon($r['file_hash']);
                        }
                        
                        array_push($files_arr, array(
                            
                            'ic' => $ic,
                            'ct' => $ct,
                            'fm' => round(($r['file_size'] / (1024 * 1024)) , 2)
                        ));
                    }
                }
                
                ////////////////
                
                $some_check = false;
                if (($user_edit_id != "0") && ($user_edit_id != $user_init_id)) {
                    $some_check = true;
                }
                
                ////////////////////
                
                $user_id = id_of_user($_SESSION['helpdesk_user_login']);
                $unit_user = unit_of_user($user_id);
                $priv_val = priv_status($user_id);
                
                $units = explode(",", $unit_user);
                array_push($units, "0");
                
                $unit2id = explode(",", $fio['unit_to_id']);
                
                $diff = array_intersect($units, $unit2id);
                $priv_h = "no";
                if ($priv_val == 1) {
                    if (($diff) || ($user_id == $fio['user_init_id'])) {
                        $ac = "ok";
                    }
                    
                    if ($user_id == $fio['user_init_id']) {
                        $priv_h = "yes";
                    }
                } 
                else if ($priv_val == 0) {
                    $ac = "ok";
                    if ($user_id == $fio['user_init_id']) {
                        $priv_h = "yes";
                    }
                } 
                else if ($priv_val == 2) {
                    $ac = "ok";
                    $priv_h = "yes";
                }
                
                $ar_res = array(
                    
                    'cat_id' => $cat_id,
                    'get_helper_cat_name' => get_helper_cat_name($cat_id) ,
                    'fio_t' => make_html($fio['title']) ,
                    'fio_m' => $fio['message'],
                    'files_arr' => $files_arr,
                    'files_res' => $files_res,
                    'fio_init' => nameshort(name_of_user_ret($fio['user_init_id'])) ,
                    'fio_dt' => $fio['dt'],
                    'some_check' => $some_check,
                    'fio_editu' => nameshort(name_of_user_ret($fio['user_edit_id'])) ,
                    'priv_h' => $priv_h,
                    'h' => $h
                );
            }
        } 
        else if (!isset($_GET['h'])) {
            $h_check = false;
            
            if (isset($_GET['edit_cats'])) {
                $h_check_type = "edit_cats";
                
                $sc = false;
                if ((priv_status($_SESSION['helpdesk_user_id']) == "2") || (priv_status($_SESSION['helpdesk_user_id']) == "0")) {
                    $sc = true;
                }
                ob_start();
                showMenu_helper();
                $showMenu_helper = ob_get_contents();
                ob_end_clean();
                
                $ar_res = array(
                    
                    'sc' => $sc,
                    'showMenu_helper' => $showMenu_helper
                );
            } 
            else if (isset($_GET['cat'])) {
                $h_check_type = "cat";
                
                $cat_id = $_GET['cat'];
                
                $stmt = $dbConnection->prepare('SELECT name from helper_cat where id=:p_id');
                $stmt->execute(array(
                    ':p_id' => $cat_id
                ));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $sc2 = false;
                if ((priv_status($_SESSION['helpdesk_user_id']) == "2") || (priv_status($_SESSION['helpdesk_user_id']) == "0")) {
                    $sc2 = true;
                }
                
                ob_start();
                show_item_helper_cat($cat_id);
                $show_item_helper_cat = ob_get_contents();
                ob_end_clean();
                
                ob_start();
                show_items_helper();
                $show_items_helper = ob_get_contents();
                ob_end_clean();
                
                $ar_res = array(
                    
                    'rname' => $row['name'],
                    'sc2' => $sc2,
                    'show_items_helper' => $show_items_helper,
                    'show_item_helper_cat' => $show_item_helper_cat
                );
            } 
            else if (isset($_GET['add'])) {
                $h_check_type = "add";
                
                $sc3 = false;
                if ((priv_status($_SESSION['helpdesk_user_id']) == "2") || (priv_status($_SESSION['helpdesk_user_id']) == "0")) {
                    $sc3 = true;
                }
                
                $catlist = array();
                $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM helper_cat order by sort_id ASC');
                $stmt->execute();
                $result = $stmt->fetchAll();
                foreach ($result as $row) {
                    array_push($catlist, array(
                        'value' => $row['value'],
                        'label' => $row['label']
                    ));
                }
                
                $tolist = array();
                $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
                $stmt->execute(array(
                    ':n' => '0',
                    ':s' => '1'
                ));
                $result = $stmt->fetchAll();
                foreach ($result as $row) {
                    
                    $row['label'] = $row['label'];
                    $row['value'] = (int)$row['value'];
                    
                    array_push($tolist, array(
                        'value' => $row['value'],
                        'label' => $row['label']
                    ));
                }
                
                $ar_res = array(
                    
                    'sc3' => $sc3,
                    'catlist' => $catlist,
                    'tolist' => $tolist,
                    'mdtime' => md5(time())
                );
            } 
            else {
                $h_check_type = "else";
                
                $sc4 = false;
                if ((priv_status($_SESSION['helpdesk_user_id']) == "2") || (priv_status($_SESSION['helpdesk_user_id']) == "0")) {
                    $sc4 = true;
                }
                
                $ar_res = array(
                    'sc4' => $sc4
                );
            }
        }
        
        $basedir = dirname(dirname(__FILE__));
        
        ////////////
        try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir . '/inc/views');
            
            // инициализируем Twig
            if (get_conf_param('twig_cache') == "true") {
                $twig = new Twig_Environment($loader, array(
                    'cache' => $basedir . '/inc/cache',
                ));
            } 
            else {
                $twig = new Twig_Environment($loader);
            }
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('helper.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            $main_arr = array(
                'hostname' => $CONF['hostname'],
                'name_of_firm' => $CONF['name_of_firm'],
                'HELPER_title' => lang('HELPER_title') ,
                'HELPER_cat' => lang('HELPER_cat') ,
                'NEW_to' => lang('NEW_to') ,
                'NEW_to_unit' => lang('NEW_to_unit') ,
                'HELP_all' => lang('HELP_all') ,
                'HELP_desc' => lang('HELP_desc') ,
                'EXT_for_clients' => lang('EXT_for_clients') ,
                'EXT_for_clients_ext' => lang('EXT_for_clients_ext') ,
                'HELP_do' => lang('HELP_do') ,
                'PORTAL_fileplace' => lang('PORTAL_fileplace') ,
                'TICKET_file_list' => lang('TICKET_file_list') ,
                'HELP_save' => lang('HELP_save') ,
                'HELP_back' => lang('HELP_back') ,
                'HELPER_back' => lang('HELPER_back') ,
                'HELPER_pub' => lang('HELPER_pub') ,
                'TICKET_t_last_up' => lang('TICKET_t_last_up') ,
                'HELPER_print' => lang('HELPER_print') ,
                'CONF_act_edit' => lang('CONF_act_edit') ,
                'HELPER_create' => lang('HELPER_create') ,
                'HELP_cats_title' => lang('HELP_cats_title') ,
                'HELPER_cats_info' => lang('HELPER_cats_info') ,
                'NOTES_create' => lang('NOTES_create') ,
                'HELPER_add' => lang('HELPER_add') ,
                'HELPER_add_info' => lang('HELPER_add_info') ,
                'CONF_true' => lang('CONF_true') ,
                'HELP_create' => lang('HELP_create') ,
                'HELPER_find' => lang('HELPER_find') ,
                'HELPER_info' => lang('HELPER_info') ,
                'h_check' => $h_check,
                'h_check_type' => $h_check_type
            );
            
            $main_arr = array_merge($ar_res, $main_arr);
            
            echo $template->render($main_arr);
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
        
        include ("footer.inc.php");
    }
} 
else {
    include 'auth.php';
}
?>
