<?php
$base = dirname(dirname(__FILE__)); 
include($base ."/conf.php");
date_default_timezone_set('Europe/Kiev');
function humanTiming_old ($time)
{

    $time = time() - $time;

    return floor($time/86400);
}
$dbConnection = new PDO(
    'mysql:host='.$CONF_DB['host'].';dbname='.$CONF_DB['db_name'],
    $CONF_DB['username'],
    $CONF_DB['password'],
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
);
$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function get_conf_param($in) {
 global $dbConnection;
 $stmt = $dbConnection->prepare('SELECT value FROM perf where param=:in');
 $stmt->execute(array(':in' => $in));
 $con = $stmt->fetch(PDO::FETCH_ASSOC);

return $con['value'];

}

$CONF = array (
'days2arch'   => get_conf_param('days2arch'),
'time_zone'   => get_conf_param('time_zone')
);

$time_zone=$CONF['time_zone'];



/*   
5 0 * * * /usr/bin/php5 -f /var/www/hd_prod/sys/4cron.php > /var/www/hd_prod/4cron.log 2>&1
*/

            $stmt = $dbConnection->prepare('SELECT id, ok_by, ok_date,date_create,user_to_id,unit_id,user_init_id
							from tickets
							where arch=:n1 and ok_by !=:n2');
			$stmt->execute(array(':n1' => '0',':n2' => '0'));
			$res1 = $stmt->fetchAll();
			foreach($res1 as $row) {

				        $user_to_id=$row['user_to_id'];
						$unit_to_id=$row['unit_id'];
						$user_init_id=$row['user_init_id'];


    $m=$row['id'];
    $td= humanTiming_old(strtotime($row['ok_date']))."<br>";

    if ($td > $CONF['days2arch'] ) {

                $stmt = $dbConnection->prepare('update tickets set arch=:n1, last_update=:n where id=:m');
		$stmt->execute(array(':n1' => '1',':m' => $m, ':n'=>$time_zone));
        
        
        
                
                
            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, ticket_id)
values (:arch, :n, :m)');
			$stmt->execute(array(':arch' => 'arch',':m' => $m,':n'=>$time_zone));
			
			



					$delivers_ids=array();
					array_push($delivers_ids,$user_init_id);
				
					
					
					///////////Исполнителям?///////////////////
					if ( $user_to_id == 0) {
						//выбрать всех с отдела
									        $stmt = $dbConnection->prepare('SELECT id FROM users where find_in_set(:id,unit) and status=:n and is_client=0');
											$stmt->execute(array(':n'=>'1', ':id'=>$unit_to_id));
											$res1 = $stmt->fetchAll();                 
											
											foreach($res1 as $qrow) { 
											array_push($delivers_ids,$qrow['id']);
											}
					
					
						
					}
					else if ($user_to_id <> 0) {
					$users=explode(",",$user_to_id);
					foreach ($users as $val) {
					//всем исполнителям
					array_push($delivers_ids,$val);
					}
					}
					
					

					///////////Исполнителям?///////////////////
					
					
					
					
					
					//кто прокомментировал - тому не слать
					//SELECT id,init_user_id FROM ticket_log where ticket_id=1 and msg='comment' order by id DESC limit 1
					$stmt = $dbConnection->prepare("SELECT init_user_id FROM ticket_log where ticket_id=:id and msg=:n order by id DESC limit 1");
					$stmt->execute(array(':n'=>'comment', ':id'=>$m));
					$who_last = $stmt->fetch(PDO::FETCH_NUM);
					$res=$who_last[0];
					
					$delivers_ids=array_unique($delivers_ids);
					if(($key = array_search($res, $delivers_ids)) !== false) {
					 				unset($delivers_ids[$key]);
					}
					
					

					
					
					$delivers_ids=implode(",", array_unique($delivers_ids));
					
									 $stmt = $dbConnection->prepare('insert into news (date_op, msg, init_user_id, target_user, ticket_id) 
				 										   VALUES (:n, :msg, :init_user_id, :target_user,:ticket_id)');
				 $stmt->execute(array(':msg'=>'ticket_arch', 
				 					  ':init_user_id'=>$user_init_id, 
				 					  ':target_user'=>$delivers_ids,
				 					  ':ticket_id'=>$m,
				 					  ':n'=>$time_zone));

			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
    }

}

?>
