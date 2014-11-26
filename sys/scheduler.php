<?php
	
	function get_user_hash_by_id($in) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('select uniq_id from users where id=:in');
    $stmt->execute(array(':in' => $in));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $tt = $total_ticket['uniq_id'];
    return $tt;
}

	
	function create_ticket($user_init_id,$user_to_id,$subj,$msg,$client_id,$unit_id,$status,$prio) {
		global $dbConnection,$CONF;
		$hn = md5(time());
		$status = '0';
		
		
                
                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM tickets");
                $stmt->execute();
                $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);
                
                $max_id_res_ticket = $max_id_ticket[0] + 1;
                
                $stmt = $dbConnection->prepare('INSERT INTO tickets
                                (id, user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio, last_update) VALUES (:max_id_res_ticket, :user_init_id, :user_to_id, :n,:subj, :msg,:max_id,:unit_id, :status, :hashname, :prio, :nz)');
                
                $stmt->execute(array(':max_id_res_ticket' => $max_id_res_ticket, ':user_init_id' => $user_init_id, ':user_to_id' => $user_to_id, ':subj' => $subj, ':msg' => $msg, ':max_id' => $client_id, ':unit_id' => $unit_id, ':status' => $status, ':hashname' => $hn, ':prio' => $prio, ':n' => $CONF['now_dt'], ':nz' => $CONF['now_dt']));
                
                $unow = '1';
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id, to_user_id, to_unit_id) values (:create, :n, :unow, :max_id_res_ticket, :user_to_id, :unit_id)');
                
                $stmt->execute(array(':create' => 'create', ':unow' => $unow, ':max_id_res_ticket' => $max_id_res_ticket, ':user_to_id' => $user_to_id, ':unit_id' => $unit_id, ':n' => $CONF['now_dt']));
                
                //??????????????????????????????????????????????????????????????
                
                //echo("dd");
                //if ($CONF_MAIL['active'] == "true") {
                //send_notification('ticket_create', $max_id_res_ticket);
                
                //////////////////////
                $ticket_id=$max_id_res_ticket;
    $stmt = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
    $stmt->execute(array(':tid' => $max_id_res_ticket));
    $res_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $user_to_id = $res_ticket['user_to_id'];
    $unit_to_id = $res_ticket['unit_id'];
    $user_init_id = $res_ticket['user_init_id'];
    if ($user_to_id == 0) {
            
            //отправка всему отделу
            /* выбрать всех пользователей у кого статус активен и отдел равен N и в БД записать: id пользователей,$type,$ticket_id */
            
            $stmt = $dbConnection->prepare('SELECT id FROM users where find_in_set(:id,unit) and status=:n and is_client=0');
            $stmt->execute(array(':n' => '1', ':id' => $unit_to_id));
            $res1 = $stmt->fetchAll();
            $delivers_ids = array();
            foreach ($res1 as $qrow) {
                array_push($delivers_ids, $qrow['id']);
            }
            
            if (($key = array_search($user_init_id, $delivers_ids)) !== false) {
                unset($delivers_ids[$key]);
            }
            
            /*  ADD TO notification_msg_pool: uniq_id  */
            
            foreach ($delivers_ids as $uniq_id_row) {
                
                $u_hash = get_user_hash_by_id($uniq_id_row);
                $stmt_n = $dbConnection->prepare('insert into notification_msg_pool (delivers_id, type_op, ticket_id, dt) VALUES (:delivers_id, :type_op, :tid, :n)');
                $stmt_n->execute(array(':delivers_id' => $u_hash, ':type_op' => 'ticket_create', ':tid' => $ticket_id, ':n' => $CONF['now_dt']));
            }
            
            $res_str = implode(",", $delivers_ids);
            
            $stmt = $dbConnection->prepare('insert into news (date_op, msg, init_user_id, target_user, ticket_id) 
				 										   VALUES (:n, :msg, :init_user_id, :target_user,:ticket_id)');
            $stmt->execute(array(':msg' => 'ticket_create', ':init_user_id' => $user_init_id, ':target_user' => $res_str, ':ticket_id' => $ticket_id, ':n' => $CONF['now_dt']));
            
            $stmt = $dbConnection->prepare('insert into notification_pool (delivers_id, type_op, ticket_id, dt) VALUES (:delivers_id, :type_op, :tid, :n)');
            $stmt->execute(array(':delivers_id' => $res_str, ':type_op' => 'ticket_create', ':tid' => $ticket_id, ':n' => $CONF['now_dt']));
        } 
        else if ($user_to_id <> 0) {
            
            $su = array();
            $users = explode(",", $user_to_id);
            foreach ($users as $val) {
                $stmt = $dbConnection->prepare('SELECT unit FROM users where id=:n');
                $stmt->execute(array(':n' => $val));
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $qrow) {
                    $user_units = $qrow['unit'];
                    $res_str = explode(",", $user_units);
                    foreach ($res_str as $vals) {
                        $stmt2 = $dbConnection->prepare('SELECT id FROM users where find_in_set(:id,unit) and (priv=2 OR priv=0) and is_client=0');
                        $stmt2->execute(array(':id' => $vals));
                        $res2 = $stmt2->fetchAll();
                        foreach ($res2 as $qrow2) {
                            array_push($su, $qrow2['id']);
                        }
                    }
                }
            }
            
            // array_merge($users,$su);
            $nr = array();
            $nr = array_unique(array_merge($users, $su));
            if (($key = array_search($user_init_id, $nr)) !== false) {
                unset($nr[$key]);
            }
            
            /*  ADD TO notification_msg_pool: uniq_id  */
            
            foreach ($nr as $uniq_id_row) {
                
                $u_hash = get_user_hash_by_id($uniq_id_row);
                $stmt_n = $dbConnection->prepare('insert into notification_msg_pool (delivers_id, type_op, ticket_id, dt) VALUES (:delivers_id, :type_op, :tid, :n)');
                $stmt_n->execute(array(':delivers_id' => $u_hash, ':type_op' => 'ticket_create', ':tid' => $ticket_id, ':n' => $CONF['now_dt']));
            }
            
            $su = implode(",", $nr);
            
            if ($su) {
                
                $stmt = $dbConnection->prepare('insert into news (date_op, msg, init_user_id, target_user, ticket_id) 
				 										   VALUES (:n, :msg, :init_user_id, :target_user,:ticket_id)');
                $stmt->execute(array(':msg' => 'ticket_create', ':init_user_id' => $user_init_id, ':target_user' => $su, ':ticket_id' => $ticket_id, ':n' => $CONF['now_dt']));
                
                $stmt = $dbConnection->prepare('insert into notification_pool (delivers_id, type_op, ticket_id, dt) VALUES (:delivers_id, :type_op, :tid, :n)');
                $stmt->execute(array(':delivers_id' => $su, ':type_op' => 'ticket_create', ':tid' => $ticket_id, ':n' => $CONF['now_dt']));
            }
        }
        
                //////////////////////
                
		
		
	}
	
	
	function up_cron($id) {
		global $dbConnection,$CONF;
		$stmt = $dbConnection->prepare('update scheduler_ticket set last_action_dt=:now where (id=:id)');
$stmt->execute(array(':now' => $CONF['now_dt'], ':id'=>$id));

	}
	
	
	
	$stmt = $dbConnection->prepare('SELECT id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, period, period_arr, action_time, dt_start, dt_stop, prio, last_action_dt from scheduler_ticket where (dt_start < :now) and (:now2 < dt_stop)');
$stmt->execute(array(':now' => $CONF['now_dt'], ':now2' => $CONF['now_dt']));
$res1 = $stmt->fetchAll();                 


foreach($res1 as $qrow) {
	
	
	//echo $qrow['id'];
	
	
		$period=$qrow['period'];
		$period_arr=$qrow['period_arr'];
		$last_action_dt=$qrow['last_action_dt'];
		$action_time=$qrow['action_time'];
		
		//$CONF['now_dt'];
		$dater = new DateTime($CONF['now_dt']);
		$now_time=$dater->format('H:i:s');
		
		
		$now_time_stamp=strtotime("1970-01-01 $now_time UTC");
		
		//echo time()."=".strtotime($action_time);
		
		



if ($period == "day") {
		
		if ($last_action_dt != null){
		$action_time_stamp=strtotime("1970-01-01 $action_time UTC");
		$future = strtotime($CONF['now_dt']); //Future date.
		$timefromdb = strtotime($last_action_dt);//source time
		$timeleft = $future-$timefromdb;
		$daysleft = round((($timeleft/24)/60)/60); 
		$minutesleft = round($timeleft/60);
		//echo $minutesleft;
		//дата последнего запуска больше 15 мин		
			//сколько дней прошло?
			//если 
			
				if (($daysleft >= $period_arr) && ($minutesleft > 10)) {
					
					if ( (($now_time_stamp-120)<$action_time_stamp) && (($now_time_stamp+120)>$action_time_stamp) ) {
						//create_task & update last_action_dt
						//echo "ok";
						create_ticket($qrow['user_init_id'],$qrow['user_to_id'],$qrow['subj'],$qrow['msg'],$qrow['client_id'],$qrow['unit_id'],'0',$qrow['prio']);
						up_cron($qrow['id']);
					}
				}
			
		}
		else if ($last_action_dt == null){
			$action_time_stamp=strtotime("1970-01-01 $action_time UTC");
			
			
			if ( (($now_time_stamp-120)<$action_time_stamp) && (($now_time_stamp+120)>$action_time_stamp) ) {
						//create_task & update last_action_dt
						create_ticket($qrow['user_init_id'],$qrow['user_to_id'],$qrow['subj'],$qrow['msg'],$qrow['client_id'],$qrow['unit_id'],'0',$qrow['prio']);
						up_cron($qrow['id']);
						//echo "ok";
			}
		}
		
		
		
			
		}




		else if ($period == "week") {
			
			if ($last_action_dt != null){
		$action_time_stamp=strtotime("1970-01-01 $action_time UTC");
		$future = strtotime($CONF['now_dt']); //Future date.
		$timefromdb = strtotime($last_action_dt);//source time
		$timeleft = $future-$timefromdb;
		$daysleft = round((($timeleft/24)/60)/60); 
		$minutesleft = round($timeleft/60);
		$dayweek_arr=explode(",",$period_arr);
		$day_of_week = date('N', strtotime($CONF['now_dt']));
		//echo $day_of_week;//current
		
		foreach ($dayweek_arr as $val) {
			if ($val == $day_of_week) {
				if (($minutesleft > 10)) {
					if ( (($now_time_stamp-120)<$action_time_stamp) && (($now_time_stamp+120)>$action_time_stamp) ) {
						//create_task & update last_action_dt
						
						create_ticket($qrow['user_init_id'],$qrow['user_to_id'],$qrow['subj'],$qrow['msg'],$qrow['client_id'],$qrow['unit_id'],'0',$qrow['prio']);
						up_cron($qrow['id']);
					}
				}
			}
			}
		//echo $minutesleft;
		//дата последнего запуска больше 15 мин		
			//сколько дней прошло?
			//если 
				
			
		}
		else if ($last_action_dt == null){
			$dayweek_arr=explode(",",$period_arr);
			$action_time_stamp=strtotime("1970-01-01 $action_time UTC");
			$day_of_week = date('N', strtotime($CONF['now_dt']));
			foreach ($dayweek_arr as $val) {
			if ($val == $day_of_week) {
				if ( (($now_time_stamp-120)<$action_time_stamp) && (($now_time_stamp+120)>$action_time_stamp) ) {
						//create_task & update last_action_dt
						create_ticket($qrow['user_init_id'],$qrow['user_to_id'],$qrow['subj'],$qrow['msg'],$qrow['client_id'],$qrow['unit_id'],'0',$qrow['prio']);
						up_cron($qrow['id']);
			}
			}
			}


			
			
		}			
			
			
		}
		
		
		
		
		else if ($period == "month") {
			
						if ($last_action_dt != null){
		$action_time_stamp=strtotime("1970-01-01 $action_time UTC");
		$future = strtotime($CONF['now_dt']); //Future date.
		$timefromdb = strtotime($last_action_dt);//source time
		$timeleft = $future-$timefromdb;
		$daysleft = round((($timeleft/24)/60)/60); 
		$minutesleft = round($timeleft/60);
		$month_arr=explode(",",$period_arr);
		$day_of_month = date('d', strtotime($CONF['now_dt']));
		//echo $day_of_month;//current
		
		foreach ($month_arr as $val) {
			if ($val == $day_of_month) {
				if (($minutesleft > 10)) {
					if ( (($now_time_stamp-120)<$action_time_stamp) && (($now_time_stamp+120)>$action_time_stamp) ) {
						//create_task & update last_action_dt
						create_ticket($qrow['user_init_id'],$qrow['user_to_id'],$qrow['subj'],$qrow['msg'],$qrow['client_id'],$qrow['unit_id'],'0',$qrow['prio']);
						up_cron($qrow['id']);
					}
				}
			}
			}
		//echo $minutesleft;
		//дата последнего запуска больше 15 мин		
			//сколько дней прошло?
			//если 
				
			
		}
		else if ($last_action_dt == null){
			$month_arr=explode(",",$period_arr);
			$day_of_month = date('d', strtotime($CONF['now_dt']));
			$action_time_stamp=strtotime("1970-01-01 $action_time UTC");
			foreach ($month_arr as $val) {
			if ($val == $day_of_month) {
				if ( (($now_time_stamp-120)<$action_time_stamp) && (($now_time_stamp+120)>$action_time_stamp) ) {
						//create_task & update last_action_dt
						create_ticket($qrow['user_init_id'],$qrow['user_to_id'],$qrow['subj'],$qrow['msg'],$qrow['client_id'],$qrow['unit_id'],'0',$qrow['prio']);
						up_cron($qrow['id']);
						
			}
			}
			}


			
			
		}
		
		
		
		
		
			
		}
	
	}
	
	
	
	
	
	
	
	
	
	?>