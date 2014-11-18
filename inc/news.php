<?php
session_start();
include("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
//if (validate_admin($_SESSION['helpdesk_user_id'])) {
   include("head.inc.php");
   include("navbar.inc.php");
   
  
/*

from ticket_log: date_op, msg, init_user_id, to_user_id, ticket_id, to_unit_id



*/

?>
                <section class="content-header">
                    <h1>
                        <i class="fa fa-bullhorn"></i> <?=lang('NAVBAR_news');?>
                        <small><?=lang('DASHBOARD_last_news');?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?=$CONF['hostname']?>index.php"><span class="icon-svg"></span> <?=$CONF['name_of_firm']?></a></li>
                        <li class="active"><?=lang('NAVBAR_news');?></li>
                    </ol>
                </section>
                
                
                
<section class="content">

                    <!-- row -->
                    <div class="row">
                        <div class="col-md-12">
                        
                        

                        
                            <!-- The time line -->
                            <ul class="timeline">



<?php
$uid=$_SESSION['helpdesk_user_id'];
$unit_user=unit_of_user($uid);
$priv_val=priv_status($uid);
$units = explode(",", $unit_user);
$units = implode("', '", $units);
$ee=explode(",", $unit_user);

foreach($ee as $key=>$value) {$in_query = $in_query . ' :val_' . $key . ', '; }
$in_query = substr($in_query, 0, -2);

foreach ($ee as $key=>$value) { $vv[":val_" . $key]=$value;}

if ($priv_val == "0") { 

//Начальник
//print_r($vv);



//SELECT date_op, msg, init_user_id, target_user, ticket_id from news where find_in_set(:n,target_user) order by id DESC limit :c
//$paramss=array(':n'=>$uid, ':uid2'=>$uid, ':c'=>'30');
$stmt = $dbConnection->prepare('SELECT date_op, msg, init_user_id, target_user, ticket_id from news where find_in_set(:n,target_user) order by id DESC limit :c');
$stmt->execute(array(':n'=>$uid, ':c'=>'30'));
$res = $stmt->fetchAll();


	
}
else if ($priv_val == "1") {


$stmt = $dbConnection->prepare('SELECT date_op, msg, init_user_id, target_user, ticket_id from news where find_in_set(:n,target_user) order by id DESC limit :c');
$stmt->execute(array(':n'=>$uid, ':c'=>'30'));
$res = $stmt->fetchAll();


                
 }
else if ($priv_val == "2") { 


				$stmt = $dbConnection->prepare('SELECT date_op, msg, init_user_id, target_user, ticket_id from news order by id DESC limit :c');
                $stmt->execute(array(':c'=>'30'));
                $res = $stmt->fetchAll();
                
                
                }
    
                           
                
                foreach($res as $rows) {
                	$nd=date('d-m-Y', strtotime($rows['date_op']) );
					if ($z != $nd) { ?> 
					
					            <li class="time-label" style=" font-size: 10px; ">
                                    <span class="bg-blue">
                                    <?php 
	                                    
	                                    $today=date('d-m-Y');
	                                    $re_date=date('d-m-Y', strtotime($rows['date_op']) );
	                                    
	                                    if ($today == $re_date) {
		                                    echo lang('NEWS_today')." (<time id=\"d\" datetime=\"".$rows['date_op']."\"></time>)";
	                                    }
	                                    else {
	                                    echo "<time id=\"d\" datetime=\"".$rows['date_op']."\"></time>";
		                                    
	                                    }
                                    ?>
                                    
                                    </span>
                                </li>
                                
					<?php }
					
					
				
					$z=date('d-m-Y', strtotime($rows['date_op']));
						
						
					$init_user=$rows['init_user_id'];
					$t_id=$rows['ticket_id'];
					$t_dc=$rows['date_op'];
			
			
			
			
			
			$stmt_t = $dbConnection->prepare('select id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, prio, last_update from tickets where id=:hn');
            $stmt_t->execute(array(':hn' => $t_id));
            $ticket = $stmt_t->fetch(PDO::FETCH_ASSOC);


            $t_msg=$ticket['msg'];
            
            $stmt_comment = $dbConnection->prepare('select comment_text from comments where t_id=:hn and dt=:do');
            $stmt_comment->execute(array(':hn' => $t_id, ':do'=>$t_dc));
            $ticket_comment = $stmt_comment->fetch(PDO::FETCH_ASSOC);
            
            $tc=$ticket_comment['comment_text'];
            
            
                
                //echo $rows['date_op'].' '.$rows['msg'].'<br> ';
                
                if ($rows['msg'] == 'ticket_lock') {
	                ?>
	                <li>
                                    <i class="fa fa-lock bg-yellow"></i>
                                    <div class="timeline-item">
                                        <span class="time"><small><i class="fa fa-clock-o"></i> 
                                        <time id="c" datetime="<?=$t_dc?>"></time></small></span>
                                        <h3 class="timeline-header no-border"><a href="view_user?<?=get_user_hash_by_id($init_user);?>"><?=name_of_user_ret($init_user)?></a> 
                                        <?=lang('NEWS_action_lock');?> <a href="ticket?<?=get_ticket_hash_by_id($t_id)?>">#<?=$t_id;?></a></h3>
<div class="timeline-body">

<small class="" style="margin-top: 0px; margin-bottom: 0px;">
                                <?=make_html(get_ticket_val_by_hash('msg',get_ticket_hash_by_id($t_id)), 'no');?>...
                            </small>
                                           
                                        </div>
                                    </div>
                                </li>
	                <?
                }
                
               else if ($rows['msg'] == 'ticket_unlock') {
	                ?>
	                <li>
                                    <i class="fa fa-unlock bg-maroon"></i>
                                    <div class="timeline-item">
                                        <span class="time"><small><i class="fa fa-clock-o"></i> <time id="c" datetime="<?=$t_dc?>"></time></small></span>
                                        <h3 class="timeline-header no-border"><a href="view_user?<?=get_user_hash_by_id($init_user);?>"><?=name_of_user_ret($init_user)?></a>
                                        
                                        
                                         <?=lang('NEWS_action_unlock');?> <a href="ticket?<?=get_ticket_hash_by_id($t_id)?>">#<?=$t_id;?></a></h3>
<div class="timeline-body">

<small class="" style="margin-top: 0px; margin-bottom: 0px;">
                                <?=make_html(get_ticket_val_by_hash('msg',get_ticket_hash_by_id($t_id)), 'no');?>...
                            </small>
                                           
                                        </div>
<div class="timeline-footer">
                                            <a href="ticket?<?=get_ticket_hash_by_id($t_id)?>" class="btn btn-xs bg-maroon"><?=lang('EXT_news_view_t');?></a>
                                        </div>
                                    </div>
                                </li>
                                
                                
                                
                                
                                
                                
                                
	                <?
                }
                else if ($rows['msg'] == 'ticket_ok') {
	                ?>
	                <li>
                                    <i class="fa fa-check-circle-o bg-green"></i>
                                    <div class="timeline-item">
                                        <span class="time"><small><i class="fa fa-clock-o"></i> <time id="c" datetime="<?=$t_dc?>"></time></small></span>
                                        <h3 class="timeline-header no-border"><a href="view_user?<?=get_user_hash_by_id($init_user);?>"><?=name_of_user_ret($init_user)?></a>
                                        
                                        
                                         <?=lang('NEWS_action_ok');?> <a href="ticket?<?=get_ticket_hash_by_id($t_id)?>">#<?=$t_id;?></a></h3>
                                         
                                         <div class="timeline-body">

<small class="" style="margin-top: 0px; margin-bottom: 0px;">
                                <?=make_html(get_ticket_val_by_hash('msg',get_ticket_hash_by_id($t_id)), 'no');?>...
                            </small>
                                           
                                        </div>
                                        
                                    </div>
                                </li>
                                
                                
                                
                                
                                
                                
                                
	                <?
                }
else if ($rows['msg'] == 'ticket_no_ok') {
	                ?>
	                <li>
                                    <i class="fa fa-circle-o bg-red"></i>
                                    <div class="timeline-item">
                                        <span class="time"><small><i class="fa fa-clock-o"></i> <time id="c" datetime="<?=$t_dc?>"></time></small></span>
                                        <h3 class="timeline-header no-border"><a href="view_user?<?=get_user_hash_by_id($init_user);?>"><?=name_of_user_ret($init_user)?></a>
                                        
                                        
                                         <?=lang('NEWS_action_no_ok');?> <a href="ticket?<?=get_ticket_hash_by_id($t_id)?>">#<?=$t_id;?></a>, <?=lang('NEWS_action_no_ok2');?></h3>
                                         <div class="timeline-body">

<small class="" style="margin-top: 0px; margin-bottom: 0px;">
                                <?=make_html(get_ticket_val_by_hash('msg',get_ticket_hash_by_id($t_id)), 'no');?>...
                            </small>
                                           
                                        </div>
                                         
                                         
                                         <div class="timeline-footer">
                                            <a href="ticket?<?=get_ticket_hash_by_id($t_id)?>" class="btn btn-xs bg-red"><?=lang('EXT_news_view_t');?></a>
                                        </div>
                                         
                                    </div>
                                </li>
                                
                                
                                
                                
                                
                                
                                
	                <?
                }
else if ($rows['msg'] == 'ticket_refer') {



$user2id=get_ticket_val_by_hash('user_to_id', get_ticket_hash_by_id($t_id));
$unit2id=get_ticket_val_by_hash('unit_id', get_ticket_hash_by_id($t_id));

            if ($user2id <> 0 ) {
                $to_text=name_of_user_ret($user2id);
            }
            if ($user2id == 0 ) {
                $to_text=view_array(get_unit_name_return($unit2id));
            }
            
            
	                ?>
	                <li>
                                    <i class="fa fa fa-share bg-blue"></i>
                                    <div class="timeline-item">
                                        <span class="time"><small><i class="fa fa-clock-o"></i> <time id="c" datetime="<?=$t_dc?>"></time></small></span>
                                        <h3 class="timeline-header no-border"><a href="view_user?<?=get_user_hash_by_id($init_user);?>"><?=name_of_user_ret($init_user)?></a>
                                        
                                        
                                         <?=lang('NEWS_action_refer');?> <a href="ticket?<?=get_ticket_hash_by_id($t_id)?>">#<?=$t_id;?></a>  <?=lang('mail_msg_ticket_to_ext');?> <?=$to_text;?> </h3>
                                        <div class="timeline-body">

<small class="" style="margin-top: 0px; margin-bottom: 0px;">
                                <?=make_html(get_ticket_val_by_hash('msg',get_ticket_hash_by_id($t_id)), 'no');?>...
                            </small>
                                           
                                        </div>
                                        
                                    </div>
                     </li>
                                
                                
                                
                                
                                
                                
                                
	                <?
                }
                else if ($rows['msg'] == 'ticket_comment') {
	                ?>
	                <li>
                                    <i class="fa fa-comments bg-purple"></i>
                                    <div class="timeline-item">
                                        <span class="time"><small><i class="fa fa-clock-o"></i> <time id="c" datetime="<?=$t_dc?>"></time></small></span>
<h3 class="timeline-header no-border"><a href="view_user?<?=get_user_hash_by_id($init_user);?>"><?=name_of_user_ret($init_user)?></a>
                                        
                                        
                                         <?=lang('NEWS_action_comment');?> <a href="ticket?<?=get_ticket_hash_by_id($t_id)?>">#<?=$t_id;?></a></h3>
                                         <div class="timeline-body">

<small >
                                <?=make_html(get_ticket_val_by_hash('msg',get_ticket_hash_by_id($t_id)), 'no');?>...
                            </small><br><br>
                            
                            


                                           <div class="callout callout-info" style=" margin: 0px; padding-bottom: 5px; padding-top: 5px;">
                                        <small class="text-muted"><em><?=lang('NEWS_text_comment');?>:</em></small>
                                        <small><p>
	                                        <?php 
		                                        	       if (substr( $tc, 0, 6 ) === "[file:") {
		       
		       $arr_hash=explode(":", $tc);
		       $f_hash=substr($arr_hash[1], 0, -1);
		       //$hn=get_ticket_id_by_hash($f_hash);
		        $stmt2 = $dbConnection->prepare('SELECT original_name, file_size,file_type,file_ext FROM files where file_hash=:tid');
                $stmt2->execute(array(':tid'=>$f_hash));
				$file_arr = $stmt2->fetch(PDO::FETCH_ASSOC);
				

				
				
				$ct='<div class=\'text-muted well well-sm no-shadow\' style=\'margin-bottom: 5px;\'><em><small>'.lang('EXT_attach_file').'</small> <br></em>';
				
				$fts=array('image/jpeg','image/gif','image/png');
				
					if (in_array($file_arr['file_type'], $fts)) {
						
						$ct.=' <small><a href=\''.$CONF['hostname'].'sys/download.php?'.$f_hash.'\'><img style=\'max-height:100px;\' src=\''.$CONF['hostname'].'upload_files/'.$f_hash.'.'.$file_arr['file_ext'].'\'></a>  </small>';
						
					}
					else {
				$ct.=get_file_icon($f_hash).' <small><a href=\''.$CONF['hostname'].'sys/download.php?'.$f_hash.'\'>'.$file_arr['original_name'].'</a> '.round(($file_arr['file_size']/(1024*1024)),2).' Mb </small>';
				}
				$ct.='</div>';
		       }
	       else {$ct=make_html($tc,'no');
		       
		       
	       }
	       
	        ?>
	                                        <?=$ct;?>
	                                        
	                                        
	                                        
	                                        </p></small>
                                    </div>
                                        
                                           
                                        </div>
                                        <div class="timeline-footer">
                                            <a href="ticket?<?=get_ticket_hash_by_id($t_id)?>" class="btn btn-xs bg-purple"><?=lang('EXT_news_view_t');?></a>
                                        </div>
                                    </div>
                                </li>
                                
                                
                                
                                
                                
                                
                                
	                <?
                }
                
                else if ($rows['msg'] == 'ticket_create') {
	                ?>
	                <li>
                                    <i class="fa fa-tag bg-aqua"></i>
                                    <div class="timeline-item">
                                        <span class="time"><small><i class="fa fa-clock-o"></i> 
                                        <time id="c" datetime="<?=$t_dc?>"></time></small>
                                        </span>
                                        <h3 class="timeline-header no-border"><a href="view_user?<?=get_user_hash_by_id($init_user);?>"><?=name_of_user_ret($init_user)?></a>
                                        
                                        
                                         <?=lang('NEWS_action_create');?> <a href="ticket?<?=get_ticket_hash_by_id($t_id)?>">#<?=$t_id;?></a></h3>
                                        <div class="timeline-body">

<small class="" style="margin-top: 0px; margin-bottom: 0px;">
                                <?=make_html(get_ticket_val_by_hash('msg',get_ticket_hash_by_id($t_id)), 'no');?>...
                            </small>
                                           
                                        </div>
<div class="timeline-footer">
                                            <a href="ticket?<?=get_ticket_hash_by_id($t_id)?>" class="btn btn-xs bg-aqua"><?=lang('EXT_news_view_t');?></a>
                                        </div>
                                    </div>
                                </li>
                                
                                
                                
                                
                                
                                
                                
	                <?
                }
                else if ($rows['msg'] == 'ticket_arch') {
	                ?>
	                <li>
                                    <i class="fa fa-archive bg-gray"></i>
                                    <div class="timeline-item">
                                        <span class="time"><small><i class="fa fa-clock-o"></i> 
                                        <time id="c" datetime="<?=$t_dc?>"></time></small>
                                        </span>
                                        <h3 class="timeline-header no-border">
                                        
                                        
                                         <?=lang('FILES_ticket');?> <a href="ticket?<?=get_ticket_hash_by_id($t_id)?>">#<?=$t_id;?></a> <?=lang('NEWS_action_ticket_arch');?></h3>
                                        <div class="timeline-body">

<small class="" style="margin-top: 0px; margin-bottom: 0px;">
                                <?=make_html(get_ticket_val_by_hash('msg',get_ticket_hash_by_id($t_id)), 'no');?>...
                            </small>
                                           
                                        </div>

                                    </div>
                                </li>
                                
                                
                                
                                
                                
                                
                                
	                <?
                }
                
                }
                
                
                

?>









                                <li>
                                    <i class="fa fa-clock-o"></i>
                                </li>
                            </ul>
                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    

                </section>
<?php
 include("footer.inc.php");
?>

<?php
	
	}
else {
    include '../auth.php';
}
?>