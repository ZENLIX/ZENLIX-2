<?php

header('Content-Type: application/json');
include ("functions.inc.php");

$data_json = json_decode( file_get_contents('php://input') );


/*
API ZENLIX script

Авторизация:  
request params: { mode:«auth», login: [login], pass: [pass], device_token:[token] } 
answer params: { status:«ok», device_hash:[hash]} или { status:«error»}

Получитьсписокзаявок: 
request params: { mode:«ticket_list», uniq_id:[hash] } 
answer params: { array_of_tickets(id, subj, text)}

Просмотретьзаявку: 
request params: { mode:«ticket_view», uniq_id:[hash], ticket_hash:[t_hash] } 
answer params: { array_of_tickets(id, subj, text)}

Заблокироватьзаявку:  
request params: { mode:«ticket_lock», uniq_id:[hash], ticket_hash:[t_hash] } 
answer params: { array_of_tickets(id, subj, text)}

Выполнитьзаявку: 
request params: { mode:«ticket_ok», uniq_id:[hash], ticket_hash:[t_hash] } 
answer params: { array_of_tickets(id, subj, text)}

code на status (ok, error)
msg на error_description
id на ticket_id
date_create на date_created

*/

if (isset($data_json->mode)) {

    $mode = $data_json->mode;
    
    if ($mode == "auth") {
        
        if (isset($data_json->login, $data_json->pass, $data_json->device_token)) {
            $login = ($data_json->login);
            $password = md5($data_json->pass);
            
            if (get_user_authtype($login)) {
                if (ldap_auth($login, $data_json->pass)) {
                    
                    $stmt = $dbConnection->prepare('SELECT id,login,fio from users where login=:login AND status=1');
                    $stmt->execute(array(
                        ':login' => $login
                    ));
                    if ($stmt->rowCount() == 1) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $code = "ok";
                    }
                } else {
                    $code = "error";
                    $error_msg = "ldap auth error";
                }
            }
            
            //SYSTEM auth
            else if (get_user_authtype($login) == false) {
                
                $stmt = $dbConnection->prepare('SELECT id,login,fio from users where login=:login AND pass=:pass AND status=1');
                $stmt->execute(array(
                    ':login' => $login,
                    ':pass' => $password
                ));
                
                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $user_id = $row['id'];
                    $code = "ok";
                } else {
                    $code = "error";
                    $error_msg = "system auth error";
                }
            }
            
            //check_user()  if ok => device_hash=...
            
            
        } else {
            $code = "error";
            $error_msg = "input values not alls";
        }
        
        $status = "ok";
        $r['uniq_id'] = get_user_val_by_id($user_id, 'uniq_id');
        $r['status'] = $code;
        $r['error_description'] = $error_msg;
        $row_set[] = $r;
        print json_encode($row_set);
    }

    else if ($mode == "ticket_list") {
        if (isset($data_json->uniq_id)) {

            if (validate_user_by_api($data_json->uniq_id)) {


        $user_id 	= get_user_val_by_hash($data_json->uniq_id, 'id');
        $unit_user 	= unit_of_user($user_id);
        $priv_val 	= priv_status($user_id);
        
        $units 		= explode(",", $unit_user);
        $units 		= implode("', '", $units);
        $ee 		= explode(",", $unit_user);

        foreach ($ee as $key => $value) {
            $in_query = $in_query . ' :val_' . $key . ', ';
        }
        $in_query = substr($in_query, 0, -2);
        foreach ($ee as $key => $value) {
            $vv[":val_" . $key] = $value;
        }

        if ($priv_val == 0) { 
        		$stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, prio, last_update
                            from tickets
                            where unit_id IN (' . $in_query . ')  and arch=:n order by ok_by asc, prio desc, id desc limit 10');
                
                $paramss = array(':n' => '0');
                $stmt->execute(array_merge($vv, $paramss));
            }
        else if ($priv_val == 1) { 
        		$stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, prio, last_update
                            from tickets
                            where ((find_in_set(:user_id,user_to_id) and arch=:n) or
                            (find_in_set(:n1,user_to_id) and unit_id IN (' . $in_query . ') and arch=:n2)) order by ok_by asc, prio desc, id desc limit 10');
                $paramss = array(':user_id' => $user_id, ':n' => '0', ':n1' => '0', ':n2' => '0');
                $stmt->execute(array_merge($vv, $paramss));
            }
        else if ($priv_val == 2) {
                $stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, prio, last_update
                            from tickets
                            where arch=:n
                            order by ok_by asc, prio desc, id desc limit 10');
                $stmt->execute(array(':n' => '0'));
                 }
                
                $res1 = $stmt->fetchAll();
				
				$r['tickets'] = array();
                foreach ($res1 as $row) {
                	array_push($r['tickets'], array(
                    'id_ticket' 			=> $row['id'],
                    'ticket_hash' 	=> $row['hash_name'],
                    'subj' 			=> $row['subj'],
                    'text' 			=> $row['msg'],
                    'date_created' 	=> $row['date_create']
                	));
                }

                
                $code = "ok";
                
            }
            else {
            $code = "error";
            $error_msg = "auth error";
            }
            
            //validate_auth() {}
            
            
        } else {
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
            	$user_id 	= get_user_val_by_hash($data_json->uniq_id, 'id');
            	$stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, comment, last_edit, is_read, lock_by, ok_by, arch, ok_date, prio, last_update
                            from tickets
                            where hash_name=:hn');
    						$stmt->execute(array(':hn' => $data_json->ticket_hash));
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
        							if ($row['status'] == 1) {
                    $st = 'ok';
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

        			array_push($r['ticket'], array(
                    'id_ticket' 	=> $row['id'],
                    'ticket_hash' 	=> $row['hash_name'],
                    'subj' 			=> $row['subj'],
                    'text' 			=> $row['msg'],
                    'date_created' 	=> $row['date_create'], 
                    'user_init_id'	=> nameshort(name_of_user_ret_nolink($row['user_init_id'])),
                    'client_id'		=> nameshort(name_of_user_ret_nolink($row['client_id'])),
                    'unit_id'		=> view_array(get_unit_name_return($row['unit_id'])),
                    'user_to_id'	=> nameshort(name_of_user_ret_nolink($row['user_to_id'])),
                    'status'		=> $st,
                    'lock_by'		=> nameshort(name_of_user_ret_nolink($row['lock_by'])),
                    'ok_by'			=> nameshort(name_of_user_ret_nolink($row['ok_by']))
                	));
            }
                $code = "ok";
        }
    
    }
    else { 
                $code = "error";
                $error_msg = "validate error";
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
            	$user_id 	= get_user_val_by_hash($data_json->uniq_id, 'id');

            	//check
            $stmt = $dbConnection->prepare('SELECT lock_by, id FROM tickets where hash_name=:tid');
            $stmt->execute(array(':tid' => $data_json->ticket_hash));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            $lb = $fio['lock_by'];
            $t_id = $fio['id'];
             if ($lb == "0") {
                
                $stmt = $dbConnection->prepare('update tickets set lock_by=:user, last_update=:n where hash_name=:tid');
                $stmt->execute(array(':tid' => $data_json->ticket_hash, ':user' => $user_id, ':n' => $CONF['now_dt']));
                
                $unow = $user_id;
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:lock, :n, :unow, :tid)');
                $stmt->execute(array(':tid' => $t_id, ':unow' => $unow, ':lock' => 'lock', ':n' => $CONF['now_dt']));
                
                send_notification('ticket_lock', $t_id);
            }

            	$stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, comment, last_edit, is_read, lock_by, ok_by, arch, ok_date, prio, last_update
                            from tickets
                            where hash_name=:hn');
    						$stmt->execute(array(':hn' => $data_json->ticket_hash));
    						$res1 = $stmt->fetchAll();
    			if (!empty($res1)) {
    				$r['ticket'] = array();







        		foreach ($res1 as $row) {
        							if ($row['status'] == 1) {
                    $st = 'ok';
                }
                if ($row['status'] == 0) {
                    if ($row['lock_by'] <> 0) {
                        
                        if ($row['lock_by'] == $user_id) {
                            $st = "lock_by_me";
                        }
                        
                        if ($row['lock_by'] <> $user_id) {
                            $st = $st = "lock_by_other";
                        }
                    }
                    if ($row['lock_by'] == 0) {
                        $st = "free";
                    }
                }

        			array_push($r['ticket'], array(
                    'id_ticket' 	=> $row['id'],
                    'ticket_hash' 	=> $row['hash_name'],
                    'subj' 			=> $row['subj'],
                    'text' 			=> $row['msg'],
                    'date_created' 	=> $row['date_create'], 
                    'user_init_id'	=> nameshort(name_of_user_ret_nolink($row['user_init_id'])),
                    'client_id'		=> nameshort(name_of_user_ret_nolink($row['client_id'])),
                    'unit_id'		=> view_array(get_unit_name_return($row['unit_id'])),
                    'user_to_id'	=> nameshort(name_of_user_ret_nolink($row['user_to_id'])),
                    'status'		=> $st,
                    'lock_by'		=> nameshort(name_of_user_ret_nolink($row['lock_by'])),
                    'ok_by'			=> nameshort(name_of_user_ret_nolink($row['ok_by']))
                	));
            }
                $code = "ok";
        }
    }
    else { 
                $code = "error";
                $error_msg = "validate error";
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



else if ($mode == "ticket_ok") {
    	if (isset($data_json->uniq_id, $data_json->ticket_hash)) {

            if (validate_user_by_api($data_json->uniq_id)) {
            	$user_id 	= get_user_val_by_hash($data_json->uniq_id, 'id');

            	//check
            $stmt = $dbConnection->prepare('SELECT ok_by, status, id FROM tickets where hash_name=:tid');
            $stmt->execute(array(':tid' => $data_json->ticket_hash));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            $ob = $fio['ok_by'];
            $t_id = $fio['id'];
            $status = $fio['status'];
             if ($status == "0") {
                $stmt = $dbConnection->prepare('update tickets set ok_by=:user, status=:s, ok_date=:n, last_update=:nz where id=:tid');
                $stmt->execute(array(':s' => '1', ':tid' => $t_id, ':user' => $user_id, ':n' => $CONF['now_dt'], ':nz' => $CONF['now_dt']));
                
                $unow = $user_id;
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log 
            (msg, date_op, init_user_id, ticket_id)
            values (:ok, :n, :unow, :tid)');
                $stmt->execute(array(':ok' => 'ok', ':tid' => $t_id, ':unow' => $user_id, ':n' => $CONF['now_dt']));
                send_notification('ticket_ok', $t_id);
            }

            	$stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, comment, last_edit, is_read, lock_by, ok_by, arch, ok_date, prio, last_update
                            from tickets
                            where hash_name=:hn');
    						$stmt->execute(array(':hn' => $data_json->ticket_hash));
    						$res1 = $stmt->fetchAll();
    			if (!empty($res1)) {
    				$r['ticket'] = array();







        		foreach ($res1 as $row) {
        							if ($row['status'] == 1) {
                    $st = 'ok';
                }
                if ($row['status'] == 0) {
                    if ($row['lock_by'] <> 0) {
                        
                        if ($row['lock_by'] == $user_id) {
                            $st = "lock_by_me";
                        }
                        
                        if ($row['lock_by'] <> $user_id) {
                            $st = $st = "lock_by_other";
                        }
                    }
                    if ($row['lock_by'] == 0) {
                        $st = "free";
                    }
                }

        			array_push($r['ticket'], array(
                    'id_ticket' 	=> $row['id'],
                    'ticket_hash' 	=> $row['hash_name'],
                    'subj' 			=> $row['subj'],
                    'text' 			=> $row['msg'],
                    'date_created' 	=> $row['date_create'], 
                    'user_init_id'	=> nameshort(name_of_user_ret_nolink($row['user_init_id'])),
                    'client_id'		=> nameshort(name_of_user_ret_nolink($row['client_id'])),
                    'unit_id'		=> view_array(get_unit_name_return($row['unit_id'])),
                    'user_to_id'	=> nameshort(name_of_user_ret_nolink($row['user_to_id'])),
                    'status'		=> $st,
                    'lock_by'		=> nameshort(name_of_user_ret_nolink($row['lock_by'])),
                    'ok_by'			=> nameshort(name_of_user_ret_nolink($row['ok_by']))
                	));
            }
                $code = "ok";
                
        }
    }
    else { 
                $code = "error";
                $error_msg = "validate error";
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
?>
    