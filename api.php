<?php


header('Content-Type: application/json');
include_once ("functions.inc.php");

$data_json = json_decode(file_get_contents('php://input'));

if (get_conf_param('api_status') == "true") {








    
    if (isset($data_json->mode)) {
$mode = $data_json->mode;

        
        
        
     if ($mode == "auth") {
            
            if (isset($data_json->login, $data_json->pass)) {
                $login = ($data_json->login);
                $password = md5($data_json->pass);
                
                if (get_user_authtype($login)) {
                    if (ldap_auth($login, $data_json->pass)) {
                        
                        $stmt = $dbConnection->prepare('SELECT * from users where login=:login AND status=1');
                        $stmt->execute(array(
                            ':login' => $login
                        ));
                        if ($stmt->rowCount() == 1) {
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            $user_id = $row['id'];
                            $code = "ok";
                            $fio = $row['fio'];
                            $ui = $row['usr_img'];
                        }
                    } 
                    else {
                        $code = "error";
                        $error_msg = "system auth error";
                    }
                }
                
                //SYSTEM auth
                else if (get_user_authtype($login) == false) {
                    
                    $stmt = $dbConnection->prepare('SELECT * from users where login=:login AND pass=:pass AND status=1');
                    $stmt->execute(array(
                        ':login' => $login,
                        ':pass' => $password
                    ));
                    
                    if ($stmt->rowCount() == 1) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $user_id = $row['id'];
                        $code = "ok";
                        $fio = $row['fio'];
                        $ui = $row['usr_img'];
                    } 
                    else {
                        $code = "error";
                        $error_msg = "system auth error";
                    }
                }
                
                //check_user()  if ok => device_hash=...
                
                
            } 
            else {
                $code = "error";
                $error_msg = "input values not alls";
            }
            
            $status = "ok";
            
            if (isset($data_json->device_token)) {
                
                $dtoken = $data_json->device_token;
                
                $stmt = $dbConnection->prepare('delete from user_devices where device_token=:dt');
                $stmt->execute(array(
                    ':dt' => $dtoken
                ));
                
                $stmt_n = $dbConnection->prepare('insert into user_devices (user_id, device_token, dt) VALUES (:user_id, :device_token, :dt)');
                $stmt_n->execute(array(
                    ':user_id' => $user_id,
                    ':device_token' => $dtoken,
                    ':dt' => $CONF['now_dt']
                ));
            }
            
            if (get_user_val_by_id($user_id, 'api_key')) {
                $ap_key = get_user_val_by_id($user_id, 'api_key');
            }
            if (!get_user_val_by_id($user_id, 'api_key')) {
                $ap_key = gen_new_api($user_id);
            }
            
            if (strlen($ui) < 5) {
                $ui = "img/avatar5.png";
            } 
            else {
                $ui = "upload_files/avatars/" . $ui;
            }
            
            $r = array(
                'uniq_id' => $ap_key,
                'status' => $code,
                'error_description' => $error_msg,
                'fio' => $fio,
                'name_only' => get_user_name($fio) ,
                'usr_img' => $ui
            );
            
            print json_encode($r);
        } 
        else if ($mode == "reset_token") {
            
            if (isset($data_json->device_token)) {
                
                $device_token = $data_json->device_token;
                
                reset_device_token($device_token);
            } 
            else {
                $code = "error";
                $error_msg = "input values not alls";
            }
        } 
        else if ($mode == "ticket_list") {
            if (isset($data_json->uniq_id, $data_json->type)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    
                    $is_client = get_user_val_by_api($data_json->uniq_id, 'is_client');
                    
                    if ($is_client == "1") {
                        $data_json->type = "client";
                    }
                    
                    if ($data_json->type == "client") {
                        $user_id = get_user_val_by_api($data_json->uniq_id, 'id');
                        $stmt = $dbConnection->prepare('SELECT * from tickets where user_init_id=:user_id and arch=:n and client_id=:cid
        order by id desc');
                        $stmt->execute(array(
                            ':user_id' => $user_id,
                            ':cid' => $user_id,
                            ':n' => '0'
                        ));
                    }
                    
                    if ($data_json->type == "in") {
                        $user_id = get_user_val_by_api($data_json->uniq_id, 'id');
                        $unit_user = unit_of_user($user_id);
                        $priv_val = priv_status($user_id);
                        
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
                        
                        if ($priv_val == 0) {
                            $stmt = $dbConnection->prepare('SELECT *
                            from tickets
                            where unit_id IN (' . $in_query . ') and arch=:n order by ok_by asc, prio desc, id desc');
                            
                            $paramss = array(
                                ':n' => '0'
                            );
                            $stmt->execute(array_merge($vv, $paramss));
                        } 
                        else if ($priv_val == 1) {
                            $stmt = $dbConnection->prepare('SELECT * from tickets
                            where ((find_in_set(:user_id,user_to_id) and arch=:n) or
                            (find_in_set(:n1,user_to_id) and unit_id IN (' . $in_query . ') and arch=:n2)) order by ok_by asc, prio desc, id desc');
                            $paramss = array(
                                ':user_id' => $user_id,
                                ':n' => '0',
                                ':n1' => '0',
                                ':n2' => '0'
                            );
                            $stmt->execute(array_merge($vv, $paramss));
                        } 
                        else if ($priv_val == 2) {
                            $stmt = $dbConnection->prepare('SELECT *
                            from tickets
                            where arch=:n
                            order by ok_by asc, prio desc, id desc');
                            $stmt->execute(array(
                                ':n' => '0'
                            ));
                        }
                    } 
                    else if ($data_json->type == "out") {
                        $user_id = get_user_val_by_api($data_json->uniq_id, 'id');
                        $unit_user = unit_of_user($user_id);
                        $priv_val = priv_status($user_id);
                        
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
                        
                        if ($priv_val == 0) {
                            
                            $p = get_users_from_units_by_user();
                            
                            //print_r($p);
                            
                            //$ee = explode(",", $unit_user);
                            
                            foreach ($p as $key => $value) {
                                $in_query = $in_query . ' :val_' . $key . ', ';
                            }
                            
                            $in_query = substr($in_query, 0, -2);
                            foreach ($p as $key => $value) {
                                $vv[":val_" . $key] = $value;
                            }
                            
                            ////
                            $stmt = $dbConnection->prepare('SELECT * from tickets 
        where user_init_id IN (' . $in_query . ') and arch=:n 
        order by id desc');
                            $paramss = array(
                                ':n' => '0'
                            );
                            $stmt->execute(array_merge($vv, $paramss));
                        } 
                        else if ($priv_val == 1) {
                            
                            $stmt = $dbConnection->prepare('SELECT * from tickets 
        where user_init_id=:user_id and arch=:n 
        order by id desc ');
                            $stmt->execute(array(
                                ':user_id' => $user_id,
                                ':n' => '0'
                            ));
                        } 
                        else if ($priv_val == 2) {
                            
                            $stmt = $dbConnection->prepare('SELECT * from tickets 
        where arch=:n 
        order by id desc ');
                            $stmt->execute(array(
                                ':n' => '0'
                            ));
                        }
                    } 
                    else if ($data_json->type == "arch") {
                        
                        $user_id = get_user_val_by_api($data_json->uniq_id, 'id');
                        

                        $unit_user = unit_of_user($user_id);
                        $units = explode(",", $unit_user);
                        $units = implode("', '", $units);
                        $priv_val = priv_status($user_id);
                        
                        $ee = explode(",", $unit_user);
                        $s = 1;
                        foreach ($ee as $key => $value) {
                            $in_query = $in_query . ' :val_' . $key . ', ';
                            $s++;
                        }
                        $c = ($s - 1);
                        foreach ($ee as $key => $value) {
                            $in_query2 = $in_query2 . ' :val_' . ($c + $key) . ', ';
                        }
                        $in_query = substr($in_query, 0, -2);
                        $in_query2 = substr($in_query2, 0, -2);
                        foreach ($ee as $key => $value) {
                            $vv[":val_" . $key] = $value;
                        }
                        foreach ($ee as $key => $value) {
                            $vv2[":val_" . ($c + $key) ] = $value;
                        }
                        
                        //$pp2=array_merge($vv,$vv2);
                        
                        if ($priv_val == 0) {
                            
                            $stmt = $dbConnection->prepare('SELECT * from tickets
                            where (unit_id IN (' . $in_query . ') or user_init_id=:user_id) and arch=:n
                            order by id DESC');
                            
                            $paramss = array(
                                ':n' => '1',
                                ':user_id' => $user_id
                            );
                            $stmt->execute(array_merge($vv, $paramss));
                        } 
                        else if ($priv_val == 1) {
                            
                            $stmt = $dbConnection->prepare('
            SELECT * from tickets
                            where (
                            (find_in_set(:user_id,user_to_id) and unit_id IN (' . $in_query . ') and arch=:n) or
                            (find_in_set(:n1,user_to_id) and unit_id IN (' . $in_query2 . ') and arch=:n2)
                            ) or (user_init_id=:user_id2 and arch=:n3)
                            order by id DESC');
                            
                            $paramss = array(
                                ':n' => '1',
                                ':n1' => '0',
                                ':n2' => '1',
                                ':n3' => '1',
                                ':user_id' => $user_id,
                                ':user_id2' => $user_id
                            );
                            
                            $stmt->execute(array_merge($vv, $vv2, $paramss));
                        } 
                        else if ($priv_val == 2) {
                            
                            $stmt = $dbConnection->prepare('SELECT * from tickets
                            where arch=:n
                            order by id DESC');
                            
                            $stmt->execute(array(
                                ':n' => '1'
                            ));
                        }
                    }
                    
                    /*
                    lock_priv=lock/unlock
                    ok_priv=ok/unok
                    */
                    
                    $res1 = $stmt->fetchAll();
                    
                    $r['tickets'] = array();
                    foreach ($res1 as $row) {
                        
                        $lock_by_other_fio = "0";
                        $ok_by_fio = "0";
                        
                        if ($row['arch'] == 1) {
                            $st = 'arch';
                        } 
                        else if ($row['arch'] == 0) {
                            if ($row['status'] == 1) {
                                
                                if ($row['ok_by'] == $user_id) {
                                    $st = "ok_by_me";
                                }
                                
                                if ($row['ok_by'] <> $user_id) {
                                    $st = "ok_by_other";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                                
                                // $st = 'ok';
                                
                                
                            }
                            if ($row['status'] == 0) {
                                if ($row['lock_by'] <> 0) {
                                    
                                    if ($row['lock_by'] == $user_id) {
                                        $st = "lock_by_me";
                                    }
                                    
                                    if ($row['lock_by'] <> $user_id) {
                                        $st = "lock_by_other";
                                        $lock_by_other_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                } 
                                else if ($row['lock_by'] == 0) {
                                    $st = "free";
                                }
                            }
                        }
                        
                        //nameshort(name_of_user_ret_nolink())
                        
                        array_push($r['tickets'], array(
                            'id_ticket' => $row['id'],
                            'ticket_hash' => $row['hash_name'],
                            'subj' => cutstr_api($row['subj'], 150) ,
                            'text' => cutstr_api($row['msg'], 150) ,
                            'date_created' => $row['date_create'],
                            'status' => $st,
                            'prio' => $row['prio'],
                            'lock_by_other_fio' => $lock_by_other_fio,
                            'ok_by_fio' => $ok_by_fio,
                            'user_init_hash' => get_user_hash_by_id($row['user_init_id']) ,
                            'client_hash' => get_user_hash_by_id($row['client_id']) ,
                            'to_user_hash' => get_user_hash_by_id($row['user_to_id']) ,
                            'user_init_fio' => nameshort(name_of_user_ret_nolink($row['user_init_id'])) ,
                            'client_fio' => nameshort(name_of_user_ret_nolink($row['client_id'])) ,
                            'to_user_fio' => nameshort(name_of_user_ret_nolink($row['user_to_id'])) ,
                            'to_unit_id' => $row['unit_id'],
                            'access_priv' => get_ticket_action_priv_api_arr($row['id'], $user_id)
                        ));
                    }
                    
                    $code = "ok";
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
                
                //validate_auth() {}
                
                
            } 
            else {
                $code = "error";
                $error_msg = "input values not alls";
            }
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "ticket_view") {
            
            if (isset($data_json->uniq_id, $data_json->ticket_hash)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    $user_id = get_user_val_by_api($data_json->uniq_id, 'id');
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where hash_name=:hn');
                    $stmt->execute(array(
                        ':hn' => $data_json->ticket_hash
                    ));
                    $res1 = $stmt->fetchAll();
                    if (!empty($res1)) {
                        $r['ticket'] = array();
                        
                        /*
                        status_states:
                        -free
                        -lock
                        -lock by me
                        -lock by other
                        -ok
                        -ok by me
                        -ok by other
                        -arch
                        
                        */
                        
                        foreach ($res1 as $row) {
                            
                            if ($row['arch'] == 1) {
                                $st = 'arch';
                            } 
                            else if ($row['arch'] == 0) {
                                if ($row['status'] == 1) {
                                    
                                    if ($row['ok_by'] == $user_id) {
                                        $st = "ok_by_me";
                                    }
                                    
                                    if ($row['ok_by'] <> $user_id) {
                                        $st = "ok_by_other";
                                        $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                    }
                                }
                                if ($row['status'] == 0) {
                                    if ($row['lock_by'] <> 0) {
                                        
                                        if ($row['lock_by'] == $user_id) {
                                            $st = "lock_by_me";
                                        }
                                        
                                        if ($row['lock_by'] <> $user_id) {
                                            $st = "lock_by_other";
                                        }
                                    } 
                                    else if ($row['lock_by'] == 0) {
                                        $st = "free";
                                    }
                                }
                            }
                            
                            if ($row['user_to_id'] == "0") {
                                $utid_str = get_unit_name_return4news($row['unit_id']);
                            } 
                            else if ($row['user_to_id'] != "0") {
                                $utid_str = nameshort(name_of_user_ret_nolink($row['user_to_id']));
                            }
                            $msg_text=$row['msg'];

                            $breaks = array("<br />","<br>","<br/>");  
                            $msg_text = str_ireplace($breaks, "\r\n", $msg_text);

                            //nameshort(name_of_user_ret_nolink())
                            array_push($r['ticket'], array(
                                'id_ticket' => $row['id'],
                                'ticket_hash' => $row['hash_name'],
                                'subj' => $row['subj'],
                                'text' => strip_tags($msg_text),
                                'date_created' => $row['date_create'],
                                'user_init_id' => get_user_hash_by_id($row['user_init_id']) ,
                                'client_id' => get_user_hash_by_id($row['client_id']) ,
                                'user_init_fio' => nameshort(name_of_user_ret_nolink($row['user_init_id'])) ,
                                'client_fio' => nameshort(name_of_user_ret_nolink($row['client_id'])) ,
                                
                                'unit_id' => $row['unit_id'],
                                'user_to_id' => get_user_hash_by_id($row['user_to_id']) ,
                                'user_to_fio' => $utid_str,
                                'status' => $st,
                                'lock_by' => get_user_hash_by_id($row['lock_by']) ,
                                'ok_by' => get_user_hash_by_id($row['ok_by']) ,
                                'can_refer' => get_ticket_action_priv_api_arr_ref($row['id'], $user_id),
                                'lock_by_fio' => nameshort(name_of_user_ret_nolink($row['lock_by'])) ,
                                'ok_by_fio' => nameshort(name_of_user_ret_nolink($row['ok_by'])) ,
                                'access_priv' => get_ticket_action_priv_api_arr($row['id'], $user_id) ,
                                'prio' => $row['prio'],
                                'ok_date' => $row['ok_date'],
                                'deadline_time' => $row['deadline_time']
                            ));
                        }
                        $code = "ok";
                    }
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "ticket_lock") {
            if (isset($data_json->uniq_id, $data_json->ticket_hash)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    
                    $user_id = get_user_val_by_api($data_json->uniq_id, 'id');
                    $priv_val = priv_status($user_id);
                    
                    //check
                    $stmt = $dbConnection->prepare('SELECT * FROM tickets where hash_name=:tid');
                    $stmt->execute(array(
                        ':tid' => $data_json->ticket_hash
                    ));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $t_id = $row['id'];
                    
                    $hs = explode(",", get_ticket_action_priv_api($t_id, $user_id));
                    if (in_array("lock", $hs)) {
                        
                        if ($row['arch'] == 1) {
                            $st = 'arch';
                        } 
                        else if ($row['arch'] == 0) {
                            if ($row['status'] == 1) {
                                
                                if ($row['ok_by'] == $user_id) {
                                    $st = "ok_by_me";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                                
                                if ($row['ok_by'] <> $user_id) {
                                    $st = "ok_by_other";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                            }
                            if ($row['status'] == 0) {
                                if ($row['lock_by'] <> 0) {
                                    
                                    if ($row['lock_by'] == $user_id) {
                                        $st = "lock_by_me";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                    
                                    if ($row['lock_by'] <> $user_id) {
                                        $st = "lock_by_other";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                } 
                                else if ($row['lock_by'] == 0) {
                                    $st = "free";
                                }
                            }
                        }
                        
                        if (in_array($st, array(
                            'free'
                        ))) {
                            
                            $stmt = $dbConnection->prepare('update tickets set lock_by=:user, last_update=:n where hash_name=:tid');
                            $stmt->execute(array(
                                ':tid' => $data_json->ticket_hash,
                                ':user' => $user_id,
                                ':n' => $CONF['now_dt']
                            ));
                            
                            $unow = $user_id;
                            
                            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:lock, :n, :unow, :tid)');
                            $stmt->execute(array(
                                ':tid' => $t_id,
                                ':unow' => $unow,
                                ':lock' => 'lock',
                                ':n' => $CONF['now_dt']
                            ));
                            
                            send_notification('ticket_lock', $t_id);
                        }
                        
                        $code = "ok";
                    } 
                    else if (!in_array("lock", $hs)) {
                        $code = "error";
                        $error_msg = "you have no priviliges";
                        
                        if ($row['arch'] == 1) {
                            $st = 'arch';
                        } 
                        else if ($row['arch'] == 0) {
                            if ($row['status'] == 1) {
                                
                                if ($row['ok_by'] == $user_id) {
                                    $st = "ok_by_me";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                                
                                if ($row['ok_by'] <> $user_id) {
                                    $st = "ok_by_other";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                            }
                            if ($row['status'] == 0) {
                                if ($row['lock_by'] <> 0) {
                                    
                                    if ($row['lock_by'] == $user_id) {
                                        $st = "lock_by_me";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                    
                                    if ($row['lock_by'] <> $user_id) {
                                        $st = "lock_by_other";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                } 
                                else if ($row['lock_by'] == 0) {
                                    $st = "free";
                                }
                            }
                        }
                    }
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $r['status_ticket'] = $st;
            $r['lock_by_fio'] = $lock_by_fio;
            $r['ok_by_fio'] = $ok_by_fio;
            
            //array_push($r['access_priv'], get_ticket_action_priv_api_arr($t_id, $user_id));
            $r['access_priv'] = get_ticket_action_priv_api_arr($t_id, $user_id);
            
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "ticket_unlock") {
            if (isset($data_json->uniq_id, $data_json->ticket_hash)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    $user_id = get_user_val_by_api($data_json->uniq_id, 'id');
                    $priv_val = priv_status($user_id);
                    
                    //check
                    $stmt = $dbConnection->prepare('SELECT * FROM tickets where hash_name=:tid');
                    $stmt->execute(array(
                        ':tid' => $data_json->ticket_hash
                    ));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $t_id = $row['id'];
                    
                    $hs = explode(",", get_ticket_action_priv_api($t_id, $user_id));
                    if (in_array("unlock", $hs)) {
                        
                        $p = "";
                        if (in_array($priv_val, array(
                            '2',
                            '0'
                        ))) {
                            $p = "lock_by_other";
                        }
                        
                        if ($row['arch'] == 1) {
                            $st = 'arch';
                        } 
                        else if ($row['arch'] == 0) {
                            if ($row['status'] == 1) {
                                
                                if ($row['ok_by'] == $user_id) {
                                    $st = "ok_by_me";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                                
                                if ($row['ok_by'] <> $user_id) {
                                    $st = "ok_by_other";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                            }
                            if ($row['status'] == 0) {
                                if ($row['lock_by'] <> 0) {
                                    
                                    if ($row['lock_by'] == $user_id) {
                                        $st = "lock_by_me";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                    
                                    if ($row['lock_by'] <> $user_id) {
                                        $st = "lock_by_other";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                } 
                                else if ($row['lock_by'] == 0) {
                                    $st = "free";
                                }
                            }
                        }
                        
                        if (in_array($st, array(
                            'lock_by_me',
                            $p
                        ))) {
                            
                            $stmt = $dbConnection->prepare('update tickets set lock_by=:user, last_update=:n where hash_name=:tid');
                            $stmt->execute(array(
                                ':tid' => $data_json->ticket_hash,
                                ':user' => '0',
                                ':n' => $CONF['now_dt']
                            ));
                            
                            $unow = $user_id;
                            
                            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:lock, :n, :unow, :tid)');
                            $stmt->execute(array(
                                ':tid' => $t_id,
                                ':unow' => $unow,
                                ':lock' => 'unlock',
                                ':n' => $CONF['now_dt']
                            ));
                            
                            send_notification('ticket_unlock', $t_id);
                        }
                        
                        $code = "ok";
                    } 
                    else if (!in_array("unlock", $hs)) {
                        $code = "error";
                        $error_msg = "you have no priviliges";
                        
                        if ($row['arch'] == 1) {
                            $st = 'arch';
                        } 
                        else if ($row['arch'] == 0) {
                            if ($row['status'] == 1) {
                                
                                if ($row['ok_by'] == $user_id) {
                                    $st = "ok_by_me";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                                
                                if ($row['ok_by'] <> $user_id) {
                                    $st = "ok_by_other";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                            }
                            if ($row['status'] == 0) {
                                if ($row['lock_by'] <> 0) {
                                    
                                    if ($row['lock_by'] == $user_id) {
                                        $st = "lock_by_me";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                    
                                    if ($row['lock_by'] <> $user_id) {
                                        $st = "lock_by_other";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                } 
                                else if ($row['lock_by'] == 0) {
                                    $st = "free";
                                }
                            }
                        }
                    }
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $r['status_ticket'] = $st;
            $r['lock_by_fio'] = $lock_by_fio;
            $r['ok_by_fio'] = $ok_by_fio;
            $r['access_priv'] = get_ticket_action_priv_api_arr($t_id, $user_id);
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "ticket_ok") {
            if (isset($data_json->uniq_id, $data_json->ticket_hash)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    $user_id = get_user_val_by_api($data_json->uniq_id, 'id');
                    $priv_val = priv_status($user_id);
                    
                    //check
                    $stmt = $dbConnection->prepare('SELECT * FROM tickets where hash_name=:tid');
                    $stmt->execute(array(
                        ':tid' => $data_json->ticket_hash
                    ));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $t_id = $row['id'];
                    
                    $hs = explode(",", get_ticket_action_priv_api($t_id, $user_id));
                    if (in_array("ok", $hs)) {
                        
                        if ($row['arch'] == 1) {
                            $st = 'arch';
                        } 
                        else if ($row['arch'] == 0) {
                            if ($row['status'] == 1) {
                                
                                if ($row['ok_by'] == $user_id) {
                                    $st = "ok_by_me";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                                
                                if ($row['ok_by'] <> $user_id) {
                                    $st = "ok_by_other";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                            }
                            if ($row['status'] == 0) {
                                if ($row['lock_by'] <> 0) {
                                    
                                    if ($row['lock_by'] == $user_id) {
                                        $st = "lock_by_me";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                    
                                    if ($row['lock_by'] <> $user_id) {
                                        $st = "lock_by_other";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                } 
                                else if ($row['lock_by'] == 0) {
                                    $st = "free";
                                }
                            }
                        }
                        
                        $p = "";
                        if (in_array($priv_val, array(
                            '2',
                            '0'
                        ))) {
                            $p = "lock_by_other";
                        }
                        
                        if (in_array($st, array(
                            'lock_by_me',
                            'free',
                            $p
                        ))) {
                            $stmt = $dbConnection->prepare('update tickets set ok_by=:user, status=:s, ok_date=:n, last_update=:nz where id=:tid');
                            $stmt->execute(array(
                                ':s' => '1',
                                ':tid' => $t_id,
                                ':user' => $user_id,
                                ':n' => $CONF['now_dt'],
                                ':nz' => $CONF['now_dt']
                            ));
                            
                            $unow = $user_id;
                            
                            $stmt = $dbConnection->prepare('INSERT INTO ticket_log 
            (msg, date_op, init_user_id, ticket_id)
            values (:ok, :n, :unow, :tid)');
                            $stmt->execute(array(
                                ':ok' => 'ok',
                                ':tid' => $t_id,
                                ':unow' => $user_id,
                                ':n' => $CONF['now_dt']
                            ));
                            send_notification('ticket_ok', $t_id);
                        }
                        
                        $code = "ok";
                    } 
                    else if (!in_array("ok", $hs)) {
                        $code = "error";
                        $error_msg = "you have no priviliges";
                        
                        if ($row['arch'] == 1) {
                            $st = 'arch';
                        } 
                        else if ($row['arch'] == 0) {
                            if ($row['status'] == 1) {
                                
                                if ($row['ok_by'] == $user_id) {
                                    $st = "ok_by_me";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                                
                                if ($row['ok_by'] <> $user_id) {
                                    $st = "ok_by_other";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                            }
                            if ($row['status'] == 0) {
                                if ($row['lock_by'] <> 0) {
                                    
                                    if ($row['lock_by'] == $user_id) {
                                        $st = "lock_by_me";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                    
                                    if ($row['lock_by'] <> $user_id) {
                                        $st = "lock_by_other";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                } 
                                else if ($row['lock_by'] == 0) {
                                    $st = "free";
                                }
                            }
                        }
                    }
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $r['status_ticket'] = $st;
            $r['lock_by_fio'] = $lock_by_fio;
            $r['ok_by_fio'] = $ok_by_fio;
            $r['access_priv'] = get_ticket_action_priv_api_arr($t_id, $user_id);
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "ticket_no_ok") {
            if (isset($data_json->uniq_id, $data_json->ticket_hash)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    $user_id = get_user_val_by_api($data_json->uniq_id, 'id');
                    $priv_val = priv_status($user_id);
                    
                    //check
                    $stmt = $dbConnection->prepare('SELECT * FROM tickets where hash_name=:tid');
                    $stmt->execute(array(
                        ':tid' => $data_json->ticket_hash
                    ));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $t_id = $row['id'];
                    
                    $hs = explode(",", get_ticket_action_priv_api($t_id, $user_id));
                    if (in_array("un_ok", $hs)) {
                        
                        if ($row['arch'] == 1) {
                            $st = 'arch';
                        } 
                        else if ($row['arch'] == 0) {
                            if ($row['status'] == 1) {
                                
                                if ($row['ok_by'] == $user_id) {
                                    $st = "ok_by_me";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                                
                                if ($row['ok_by'] <> $user_id) {
                                    $st = "ok_by_other";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                            }
                            if ($row['status'] == 0) {
                                if ($row['lock_by'] <> 0) {
                                    
                                    if ($row['lock_by'] == $user_id) {
                                        $st = "lock_by_me";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                    
                                    if ($row['lock_by'] <> $user_id) {
                                        $st = "lock_by_other";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                } 
                                else if ($row['lock_by'] == 0) {
                                    $st = "free";
                                }
                            }
                        }
                        
                        $p = "";
                        if (in_array($priv_val, array(
                            '2',
                            '0'
                        ))) {
                            $p = "ok_by_other";
                        }
                        
                        if (in_array($st, array(
                            'ok_by_me',
                            $p
                        ))) {
                            $stmt = $dbConnection->prepare('update tickets set ok_by=:user, status=:s, ok_date=:n, last_update=:nz where id=:tid');
                            $stmt->execute(array(
                                ':s' => '0',
                                ':tid' => $t_id,
                                ':user' => $user_id,
                                ':n' => $CONF['now_dt'],
                                ':nz' => $CONF['now_dt']
                            ));
                            
                            $unow = $user_id;
                            
                            $stmt = $dbConnection->prepare('INSERT INTO ticket_log 
            (msg, date_op, init_user_id, ticket_id)
            values (:ok, :n, :unow, :tid)');
                            $stmt->execute(array(
                                ':ok' => 'no_ok',
                                ':tid' => $t_id,
                                ':unow' => $user_id,
                                ':n' => $CONF['now_dt']
                            ));
                            send_notification('ticket_no_ok', $t_id);
                        }
                        
                        $code = "ok";
                    } 
                    else if (!in_array("un_ok", $hs)) {
                        $code = "error";
                        $error_msg = "you have no priviliges";
                        
                        if ($row['arch'] == 1) {
                            $st = 'arch';
                        } 
                        else if ($row['arch'] == 0) {
                            if ($row['status'] == 1) {
                                
                                if ($row['ok_by'] == $user_id) {
                                    $st = "ok_by_me";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                                
                                if ($row['ok_by'] <> $user_id) {
                                    $st = "ok_by_other";
                                    $ok_by_fio = nameshort(name_of_user_ret_nolink($row['ok_by']));
                                }
                            }
                            if ($row['status'] == 0) {
                                if ($row['lock_by'] <> 0) {
                                    
                                    if ($row['lock_by'] == $user_id) {
                                        $st = "lock_by_me";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                    
                                    if ($row['lock_by'] <> $user_id) {
                                        $st = "lock_by_other";
                                        $lock_by_fio = nameshort(name_of_user_ret_nolink($row['lock_by']));
                                    }
                                } 
                                else if ($row['lock_by'] == 0) {
                                    $st = "free";
                                }
                            }
                        }
                    }
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $r['status_ticket'] = $st;
            $r['lock_by_fio'] = $lock_by_fio;
            $r['ok_by_fio'] = $ok_by_fio;
            $r['access_priv'] = get_ticket_action_priv_api_arr($t_id, $user_id);
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "get_user_info") {
            if (isset($data_json->uniq_id, $data_json->user_hash)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    $user_id = get_user_val_by_api($data_json->user_hash, 'id');
                    $r['info'] = array();
                    
                    $def_unit = 0;
                    if (get_user_val_by_hash_api($data_json->uniq_id, 'def_unit_id') != "0") {
                        
                        $def_unit = get_user_val_by_hash_api($data_json->uniq_id, 'def_unit_id');
                    }
                    
                    $def_user = 0;
                    if (get_user_val_by_hash_api($data_json->uniq_id, 'def_user_id') != "0") {
                        
                        $def_user = get_user_hash_by_id(get_user_val_by_hash($data_json->uniq_id, 'def_user_id'));
                    }
                    
                    $ui = get_user_val_by_hash_api($data_json->user_hash, 'usr_img');
                    if (strlen($ui) < 5) {
                        $ui = "img/avatar5.png";
                    }
                    
                    array_push($r['info'], array(
                        'fio' => get_user_val_by_hash_api($data_json->user_hash, 'fio') ,
                        'user_login' => get_user_val_by_hash_api($data_json->user_hash, 'login') ,
                        'status' => get_user_val_by_hash_api($data_json->user_hash, 'status') ,
                        'priv' => get_user_val_by_hash_api($data_json->user_hash, 'priv') ,
                        'unit' => get_user_val_by_hash_api($data_json->user_hash, 'unit') ,
                        'is_client' => get_user_val_by_hash_api($data_json->user_hash, 'is_client') ,
                        'email' => get_user_val_by_hash_api($data_json->user_hash, 'email') ,
                        'lang' => get_user_val_by_hash_api($data_json->user_hash, 'lang') ,
                        'last_time' => get_user_val_by_hash_api($data_json->user_hash, 'last_time') ,
                        'usr_img' => $ui,
                        'posada' => get_user_val_by_hash_api($data_json->user_hash, 'posada') ,
                        'tel' => get_user_val_by_hash_api($data_json->user_hash, 'tel') ,
                        'skype' => get_user_val_by_hash_api($data_json->user_hash, 'skype') ,
                        'unit_desc' => get_user_val_by_hash_api($data_json->user_hash, 'unit_desc') ,
                        'adr' => get_user_val_by_hash_api($data_json->user_hash, 'adr') ,
                        'def_unit_id' => $def_unit,
                        'def_user_id' => $def_user
                    ));
                    $code = "ok";
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "get_unit_info") {
            if (isset($data_json->uniq_id, $data_json->unit_code)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    
                    //$user_id    = get_user_val_by_api($data_json->user_hash, 'id');
                    $r['info'] = array();
                    
                    $stmt = $dbConnection->prepare('SELECT id, name FROM deps where id=:val');
                    $stmt->execute(array(
                        ':val' => $data_json->unit_code
                    ));
                    $dep = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    array_push($r['info'], array(
                        'id' => $dep['id'],
                        'name' => $dep['name']
                    ));
                    $code = "ok";
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        } 
















        else if ($mode == "get_unit_list") {
            if (isset($data_json->uniq_id)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    
                    //$user_id    = get_user_val_by_api($data_json->user_hash, 'id');
                    
                    $stmt = $dbConnection->prepare('SELECT id, name FROM deps where status=:val');
                    $stmt->execute(array(
                        ':val' => '1'
                    ));
                    $dep = $stmt->fetchAll();
                    
                    $r['list'] = array();
                    foreach ($dep as $value) {
                        array_push($r['list'], array(
                            'id' => $value['id'],
                            'name' => $value['name']
                        ));
                        
                        // code...
                        
                        
                    }
                    
                    $code = "ok";
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "get_subj_list") {
            if (isset($data_json->uniq_id)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    
                    //$user_id    = get_user_val_by_api($data_json->user_hash, 'id');
                    
                    if (get_conf_param('sla_system') == "true") {
                        $stmt = $dbConnection->prepare('SELECT id, name FROM sla_plans');
                        $stmt->execute();
                        $dep = $stmt->fetchAll();
                        
                        $r['list'] = array();
                        foreach ($dep as $value) {
                            array_push($r['list'], array(
                                'id' => $value['id'],
                                'name' => $value['name']
                            ));
                            
                            // code...
                            
                            
                        }
                    } 
                    else if (get_conf_param('sla_system') == "false") {
                        $stmt = $dbConnection->prepare('SELECT id, name FROM subj');
                        $stmt->execute();
                        $dep = $stmt->fetchAll();
                        
                        $r['list'] = array();
                        foreach ($dep as $value) {
                            array_push($r['list'], array(
                                'id' => $value['id'],
                                'name' => $value['name']
                            ));
                            
                            // code...
                            
                            
                        }
                    }
                    
                    $code = "ok";
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "get_posada_list") {
            if (isset($data_json->uniq_id)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    
                    //$user_id    = get_user_val_by_api($data_json->user_hash, 'id');
                    
                    $stmt = $dbConnection->prepare('SELECT id, name FROM posada');
                    $stmt->execute();
                    $dep = $stmt->fetchAll();
                    
                    $r['list'] = array();
                    foreach ($dep as $value) {
                        array_push($r['list'], array(
                            'id' => $value['id'],
                            'name' => $value['name']
                        ));
                        
                        // code...
                        
                        
                    }
                    
                    $code = "ok";
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "get_users_list") {
            if (isset($data_json->uniq_id, $data_json->find_param)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    
                    //$user_id    = get_user_val_by_api($data_json->user_hash, 'id');
                    $r['users'] = array();
                    
                    $term = trim(strip_tags($data_json->find_param));
                    
                    $stmt = $dbConnection->prepare('SELECT * FROM users WHERE ((fio LIKE :term) or (login LIKE :term2) or (tel LIKE :term3)) and id!=1 and status!=2 limit 10');
                    $stmt->execute(array(
                        ':term' => '%' . $term . '%',
                        ':term2' => '%' . $term . '%',
                        ':term3' => '%' . $term . '%'
                    ));
                    
                    $u = $stmt->fetchAll();
                    
                    foreach ($u as $dep) {
                        
                        array_push($r['users'], array(
                            
                            'uniq_id' => $dep['uniq_id'],
                            'fio' => $dep['fio'],
                            'status_user' => $dep['status'],
                            'priv' => $dep['priv'],
                            'unit' => $dep['unit'],
                            'is_client' => $dep['is_client'],
                            'email' => $dep['email']
                        ));
                    }
                    $code = "ok";
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "get_user_by_dep") {
            if (isset($data_json->uniq_id, $data_json->dep_code)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    
                    //$user_id    = get_user_val_by_api($data_json->user_hash, 'id');
                    $r['users'] = array();
                    
                    $stmt = $dbConnection->prepare('SELECT * from users where find_in_set(:uid,unit) and is_client=0 and status!=2 and id!=1');
                    $stmt->execute(array(
                        ':uid' => $data_json->dep_code
                    ));
                    
                    $u = $stmt->fetchAll();
                    
                    foreach ($u as $dep) {
                        
                        array_push($r['users'], array(
                            
                            'uniq_id' => $dep['uniq_id'],
                            'fio' => $dep['fio'],
                            'status' => $dep['status'],
                            'priv' => $dep['priv'],
                            'unit' => $dep['unit'],
                            'is_client' => $dep['is_client'],
                            'email' => $dep['email']
                        ));
                    }
                    $code = "ok";
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "get_ticket_comments") {
            if (isset($data_json->uniq_id, $data_json->ticket_hash)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    
                    //$user_id    = get_user_val_by_api($data_json->user_hash, 'id');
                    $r['comments'] = array();
                    
                    $stmt = $dbConnection->prepare('SELECT * FROM comments where t_id=:val order by id asc');
                    $stmt->execute(array(
                        ':val' => get_ticket_id_by_hash($data_json->ticket_hash)
                    ));
                    $dep_r = $stmt->fetchAll();
                    
                    foreach ($dep_r as $dep) {
                        
                        $ui = get_user_val_by_id($dep['user_id'], 'usr_img');
                        
                        if (strlen($ui) < 5) {
                            $ui = "img/avatar5.png";
                        } 
                        else {
                            $ui = "upload_files/avatars/" . $ui;
                        }
                        
                        $fio = get_user_val_by_id($dep['user_id'], 'fio');
                        

$fl=strpos(make_html($dep['comment_text'], true),'[file:');

if ($fl !== false) {
    $ct=explode("[file:", $dep['comment_text']);
    $dep['comment_text']=$ct[0];
}
$dep['comment_text']=strip_tags($dep['comment_text']);


                        array_push($r['comments'], array(
                            'author' => get_user_hash_by_id($dep['user_id']) ,
                            'text' => $dep['comment_text'],
                            'dt' => $dep['dt'],
                            'usr_img' => $ui,
                            'usr_fio' => $fio
                        ));
                    }
                    $code = "ok";
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['ticket_code'] = get_ticket_id_by_hash($data_json->ticket_hash);
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "add_ticket_comment") {
            if (isset($data_json->uniq_id, $data_json->ticket_hash, $data_json->msg)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    
                    //$user_id    = get_user_val_by_api($data_json->user_hash, 'id');
                    
                    $tid_comment = get_ticket_id_by_hash($data_json->ticket_hash);
                    $user_comment = get_user_val_by_api($data_json->uniq_id, 'id');
                    $text_comment = $data_json->msg;
                    
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
                    
                    $code = "ok";
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "refer_ticket") {
            if (isset($data_json->uniq_id, $data_json->ticket_hash, $data_json->user_to_id, $data_json->unit_id, $data_json->msg)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    
                    //$user_id    = get_user_val_by_api($data_json->user_hash, 'id');
                    
                    $author = get_user_val_by_api($data_json->uniq_id, 'id');
                    
                    $tid = get_ticket_id_by_hash($data_json->ticket_hash);
                    $to = $data_json->unit_id;
                    
                    // $tou = get_user_val_by_api($data_json->user_to_id, 'id');
                    $tou = get_user_id_by_hash($data_json->user_to_id);
                    $tom = $data_json->msg;
                    
                    $hs = explode(",", get_ticket_action_priv_api($tid, $author));
                    if (in_array("ref", $hs)) {
                        
                        if (strlen($tom) > 2) {
                            
                            $x_refer_comment = '<strong><small class=\'text-danger\'>' . nameshort(name_of_user_ret($author)) . ' ' . lang('REFER_comment_add') . ' (' . date(' d.m.Y h:i:s') . '):</small> </strong>' . strip_tags(xss_clean(($tom)));
                            
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
                        
                        $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, to_user_id, ticket_id, to_unit_id) values (:refer, :n, :unow, :tou, :tid, :to)');
                        $stmt->execute(array(
                            ':to' => $to,
                            ':tou' => $tou,
                            ':refer' => 'refer',
                            ':tid' => $tid,
                            ':unow' => $author,
                            ':n' => $CONF['now_dt']
                        ));
                        
                        send_notification('ticket_refer', $tid);
                        
                        $code = "ok";
                    } 
                    else if (!in_array("ref", $hs)) {
                        
                        $code = "error";
                        $error_msg = "you have no priviliges";
                    }
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else if ($mode == "create_ticket") {
            if (isset($data_json->uniq_id, $data_json->user_to_id, $data_json->subj, $data_json->msg, $data_json->client_id, $data_json->unit_id, $data_json->prio)) {
                
                if (validate_user_by_api($data_json->uniq_id)) {
                    
                    //$user_id    = get_user_val_by_api($data_json->user_hash, 'id');
                    
                    //$user_id    = get_user_val_by_api($data_json->user_hash, 'id');
                    
                    //???///////////////////////////////////////////////////////////
                    /*
                    user_to_id (0 def)
                    !subj
                    !msg
                    !client_id
                    !unit_id
                    prio (1 def)
                    
                    */
                    $error_code = true;
                    
                    //проверить длину
                    
                    //msg$msg=$data_json->msg;
                    //if (iconv_strlen($data_json->msg, 'UTF-8') > 2 ) {
                    $msg = $data_json->msg;
                    
                    //}
                    //if (iconv_strlen($data_json->msg, 'UTF-8') <= 2 ) {
                    //   $error_code=false;
                    //}
                    //проверить длину
                    
                    //$client_id_param=$data_json->client_id;
                    //есть ли вообще такой?
                    
                    if (get_user_val_by_hash($data_json->client_id, 'id')) {
                        $client_id_param = get_user_val_by_hash($data_json->client_id, 'id');
                    }
                    if (!get_user_val_by_hash($data_json->client_id, 'id')) {
                        $error_code = false;
                    }
                    
                    $unit_id = $data_json->unit_id;
                    
                    $prio = 1;
                    if (in_array($data_json->prio, array(
                        '0',
                        '1',
                        '2'
                    ))) {
                        $prio = $data_json->prio;
                    }
                    
                    if ($data_json->user_to_id == "0") {
                        
                        $user_to_id = "0";
                    }
                    if ($data_json->user_to_id != "0") {
                        
                        $user_to_id = get_user_id_by_hash($data_json->user_to_id);
                    }
                    
                    $hashname = md5(time());
                    $now_date_time = $CONF['now_dt'];
                    
                    if ($error_code == false) {
                        $code = "error";
                        $error_msg = "error data in fields";
                    } 
                    else if ($error_code == true) {
                        
                        if (get_conf_param('sla_system') == "true") {
                            $subj_id = $data_json->subj;
                            $stmt_subj = $dbConnection->prepare('SELECT name from sla_plans where id=:p_id');
                            $stmt_subj->execute(array(
                                ':p_id' => $subj_id
                            ));
                            $row_subj = $stmt_subj->fetch(PDO::FETCH_ASSOC);
                            
                            $subj = $row_subj['name'];
                            $sla_plan_id = $data_json->subj;
                        }
                        
                        if (get_conf_param('sla_system') == "false") {
                            
                            $subj_id = $data_json->subj;
                            
                            $stmt_subj = $dbConnection->prepare('SELECT name from subj where id=:p_id');
                            $stmt_subj->execute(array(
                                ':p_id' => $subj_id
                            ));
                            $row_subj = $stmt_subj->fetch(PDO::FETCH_ASSOC);
                            
                            $subj = $row_subj['name'];
                            $sla_plan_id = 0;
                        }
                        
                        $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM tickets");
                        $stmt->execute();
                        $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);
                        $max_id_res_ticket = $max_id_ticket[0] + 1;
                        
                        $user_init_id = get_user_val_by_api($data_json->uniq_id, 'id');
                        $dl = Null;
                        $stmt = $dbConnection->prepare('INSERT INTO tickets
                                (id,
                                 user_init_id,
                                 user_to_id,
                                 date_create,
                                 subj,
                                 msg,
                                 client_id,
                                 unit_id,
                                 status,
                                 hash_name,
                                 prio,
                                 last_update,
                                 deadline_time,
                                 sla_plan_id
                                 ) VALUES (
                                  :max_id_res_ticket,
                                  :user_init_id,
                                  :user_to_id,
                                  :n,
                                  :subj,
                                  :msg,
                                  :max_id,
                                  :unit_id,
                                  :status,
                                  :hashname,
                                  :prio,
                                  :nz,
                                  :deadline_time,
                                  :sla_plan_id)');
                        $stmt->execute(array(
                            ':max_id_res_ticket' => $max_id_res_ticket,
                            ':user_init_id' => $user_init_id,
                            ':user_to_id' => $user_to_id,
                            ':subj' => $subj,
                            ':msg' => $msg,
                            ':max_id' => $client_id_param,
                            ':unit_id' => $unit_id,
                            ':status' => '0',
                            ':hashname' => $hashname,
                            ':prio' => $prio,
                            ':n' => $now_date_time,
                            ':nz' => $now_date_time,
                            ':deadline_time' => $dl,
                            ':sla_plan_id' => $sla_plan_id
                        ));
                        
                        $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id, to_user_id, to_unit_id) values (:create, :n, :unow, :max_id_res_ticket, :user_to_id, :unit_id)');
                        
                        $stmt->execute(array(
                            ':create' => 'create',
                            ':unow' => $user_init_id,
                            ':max_id_res_ticket' => $max_id_res_ticket,
                            ':user_to_id' => $user_to_id,
                            ':unit_id' => $unit_id,
                            ':n' => $now_date_time
                        ));
                        
                        send_notification('ticket_create', $max_id_res_ticket);
                        insert_ticket_info($max_id_res_ticket, 'api');
                        
                        //}
                        
                        /////////////////////////////////////////////////////////////
                        
                        $code = "ok";
                        $r['ticket_hash'] = $hashname;
                        $r['ticket_id'] = $max_id_res_ticket;
                    }
                } 
                else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            } 
            else {
                $code = "error";
                $error_msg = "not all params";
            }
            
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        } 
        else {
            $code = "error";
            $error_msg = "mode-param is not valid";
            $r['status'] = $code;
            $r['error_description'] = $error_msg;
            $row_set[] = $r;
            print json_encode($row_set);
        }
    }
}
?>
    