<?php
session_start();
include("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
//if (validate_admin($_SESSION['helpdesk_user_id'])) {
   include("head.inc.php");
   include("navbar.inc.php");
   ?>
   <section class="content-header">
                    <h1>
                        <i class="fa fa-bullhorn"></i> <?=lang('VIEWUSER_title');?>
                        <small><?=lang('VIEWUSER_title_ext');?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?=$CONF['hostname']?>index.php"><span class="icon-svg"></span> <?=$CONF['name_of_firm']?></a></li>
                        <li class="active"><?=lang('VIEWUSER_title');?></li>
                    </ol>
                </section>
   
   
   <?php
   

   $rkeys=array_keys($_GET);
   $hn=$rkeys[1];
   $stmt = $dbConnection->prepare('SELECT 
							id, fio, posada, unit_desc, usr_img, tel, skype, last_time, status,email, adr, is_client, uniq_id
							from users
							where uniq_id=:hn limit 1');
							
    $stmt->execute(array(':hn'=>$hn));
    $res1 = $stmt->fetchAll();
    if (!empty($res1)) {
    foreach($res1 as $row) {
    $user_id=$row['id'];
    $user_fio=$row['fio'];
    $user_posada=$row['posada'];
    $user_unit=$row['unit_desc'];
    $is_client=$row['is_client'];
    $user_tel=$row['tel'];
    $user_skype=$row['skype'];
    $user_last_time=$row['last_time'];
    $user_status=$row['last_status'];
    $user_mail=$row['email'];
    $user_adr=$row['adr'];
    $uniq_id=$row['uniq_id'];
    
    if ($row['usr_img']) {
	    $user_img=$CONF['hostname'].'/upload_files/avatars/'.$row['usr_img'];
    }
    else if (!$row['usr_img']) {$user_img=$CONF['hostname'].'/img/avatar5.png';}
    
    }
    
   }
else {
        ?>
        <div class="well well-large well-transparent lead">
            <center><?=lang('TICKET_t_no');?></center>
        </div>
    <?php
    }



?>






<section class="content">

                    <!-- row -->
                    <div class="row">
                    
                    
                      		<div class="col-md-3">
	                  		    <div class="box box-warning">
                                <div class="box-header">
                                <h4 style="text-align:center;"><?=$user_fio;?><br><small><?=$user_posada;?></small></h4>
                                </div>
                                <div class="box-body">
                                  
                        <center>
                            <img height="120" src="<?=$user_img;?>" class="img-rounded" alt="User Image">
                        </center>
                        
                        <?php if ($user_last_time) { ?>
                        <center>
                        <small><?=lang('stats_last_time');?> <br><i class="fa fa-clock-o"></i> <time id="c" datetime="<?=$user_last_time?>"></time></small>
                        </center>
                                        <?php } 
	                                        
	                                        if ($user_id != $_SESSION['helpdesk_user_id']) {
                                        ?>
                                        
       <br> <a href="messages?to=<?=$uniq_id;?>" class="btn btn-warning btn-block btn-xs"><i class="fa fa-comments"></i> <?=lang('EXT_do_write_message');?></a>
       <?php } ?>
        
                           
                                    
                                    
                                </div><!-- /.box-body -->
                            </div>
                            
                            
                            
                            
                            
                            
                      		</div>
                      		
                      		
                      		
                      		
                      		
                      		<div class="col-md-9">
	                      		
	                      		<div class="row">
		                      		
		                      		<div class="col-md-12"><div class="box box-solid">
                                <div class="box-header">
                                    <h3 class="box-title"><i class="fa fa-user"></i> <?=lang('P_main');?></h3>
                                    <div class="box-tools pull-right">
                                    <?=get_user_status($user_id);?>
                                        
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    
                                    
     
      <div class="panel-body">
      
      
      <div class="row">
      
      
      <div class="col-md-5">
	      
	      
	      <div class="row">
		      <div class="col-md-2"><i class="fa fa-building-o"></i></div>
		      <div class="col-md-10"><?php if ($user_adr) {?> <dd><strong><?=$user_adr;?></strong></dd><?php } ?>
	      <?php if ($user_unit) {?><dd><?=$user_unit;?></dd> <br> <?php } ?> </div>
		      
		      		      <?php if ($user_skype) {?><div class="col-md-2"><i class="fa fa-skype"></i></div>
		      <div class="col-md-10"><?=$user_skype;?></div> <?php } ?> 
		      
		      		      <?php if ($user_tel) {?><div class="col-md-2"><i class="fa fa-phone-square"></i></div>
		      <div class="col-md-10"><?=$user_tel;?></div><?php } ?>
		      
		      <?php if ($user_mail) {?>
		      		      <div class="col-md-2"><i class="fa fa-envelope-o"></i></div>
		      <div class="col-md-10"><?=$user_mail;?></div><?php } ?> 
		      
		      
	      </div>
	      
	     
                         
                                
          
                            
                            
                            
	      
      </div>
      <div class="col-md-7">
	      
	      
	      <div class="row">
                                        <div class="col-xs-4 text-center" 
                                        <?php if ($is_client == "0") {?>
                                        style="border-right: 1px solid #f4f4f4"
                                        <?php } ?>
                                        >
                                            <input type="text" class="knob" data-readonly="true" value="<?=get_total_tickets_out($user_id);?>" data-width="100" data-height="100" data-max="<?=(get_total_tickets_count());?>" data-fgColor="#39CCCC"/>
                                            <div class="knob-label"><?=lang('EXT_t_created');?></div>
                                        </div><!-- ./col -->
                                        
                                        
                                        
                                        <?php if ($is_client == "0") {?>
                                        
                                        <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="<?=get_total_tickets_lock($user_id);?>" data-width="100" data-height="100" data-max="50" data-max="<?=(get_total_tickets_count());?>" data-fgColor="#F4C01B"/>
                                            <div class="knob-label"><?=lang('EXT_t_locked');?></div>
                                        </div><!-- ./col -->
                                        <div class="col-xs-4 text-center">
                                            <input type="text" class="knob" data-readonly="true" value="<?=get_total_tickets_ok($user_id);?>" data-width="100" data-height="100" data-max="50" data-max="<?=(get_total_tickets_count());?>" data-fgColor="#39CC57"/>
                                            <div class="knob-label"><?=lang('EXT_t_oked');?></div>
                                        </div><!-- ./col -->
                                        
                                        <?php } ?>
                                        
                                        
                                        
                                    </div>
	      
      </div>
      
      
      
      </div>
 
      
      </div>
      
      
                                </div><!-- /.box-body -->
                                
                                
                            </div></div>
                            
                            
                            
		                      		<div class="col-md-12"><?php if (check_admin_user_priv($user_id)) {?>
<div class="row">

	
	<div class="col-md-6">
		
		
		<div class="box" style="min-height: 10px; max-height: 400px; scroll-behavior: initial; overflow-y: scroll;">
                                <div class="box-header">
                                    <h3 class="box-title"><i class="fa fa-lock"></i> <?=lang('STATS_lock_o');?></h3>
                                </div><!-- /.box-header -->
                                
                                        <?php
	                                        
	$stmt = $dbConnection->prepare('select id, subj, date_create, hash_name from tickets where status=0 and lock_by=:u order by id desc');
    $stmt->execute(array(':u' => $user_id));
    $result = $stmt->fetchAll();


    if (empty($result)) {
            ?>
            <div class="box-body no-padding">
            <div id="" class="well well-large well-transparent lead">
                <center>
                    <?= lang('MSG_no_records'); ?>
                </center>
            </div>
            </div>
        <?php
        } else if (!empty($result)) {
	        ?>
	        <div class="box-body no-padding">
                                    <table class="table table-condensed">
                                        <tbody><tr>
                                            <th style="width: 50px">#</th>
                                            <th><?=lang('NEW_subj');?></th>
                                            <th><?=lang('TICKET_t_date');?></th>
                                        </tr>
	        <?php
            foreach ($result as $row) {
	                                        
	                                         ?>
	                                         <tr>
                                            <td style="width: 50px"><small><a href="ticket?<?=$row['hash_name']?>"><?=$row['id'];?></a></small></td>
                                            <td><small><?=$row['subj'];?></small></td>
                                            <td><small><time id="c" datetime="<?=$row['date_create']; ?>"></time></small></td>
                                        </tr>
	                                         <?php
		                                         } 
		                                         
		                                         
		                                         ?>
		                                         
		                                         </tbody></table>
                                </div><!-- /.box-body -->

		                                          <?php }
		                                          ?>
                                        
                                                                                                        </div>
                            
                            
                            
		
		
	</div>
	
	<div class="col-md-6">
		
		
		<div class="box" style="min-height: 10px; max-height: 400px; scroll-behavior: initial; overflow-y: scroll;">
                                <div class="box-header">
                                    <h3 class="box-title"><i class="fa fa-lock"></i> <?=lang('STATS_t_free');?></h3>
                                </div><!-- /.box-header -->
                                
                                        <?php
	                                        
	$stmt = $dbConnection->prepare('select id, subj, date_create, hash_name from tickets where status=0 and lock_by=0 and (find_in_set(:u,user_to_id)) order by id desc');
    $stmt->execute(array(':u' => $user_id));
    $result = $stmt->fetchAll();


    if (empty($result)) {
            ?>
            <div id="" class="well well-large well-transparent lead">
                <center>
                    <?= lang('MSG_no_records'); ?>
                </center>
            </div>
        <?php
        } else if (!empty($result)) { ?>
        
        <div class="box-body no-padding">
                                    <table class="table table-condensed">
                                        <tbody><tr>
                                            <th style="width: 50px">#</th>
                                            <th><?=lang('NEW_subj');?></th>
                                            <th><?=lang('TICKET_t_date');?></th>
                                        </tr>
        <?php
            foreach ($result as $row) {
	                                        
	                                         ?>
	                                         <tr>
                                            <td style="width: 50px"><small><a href="ticket?<?=$row['hash_name']?>"><?=$row['id'];?></a></small></td>
                                            <td><small><?=$row['subj'];?></small></td>
                                            <td><small><time id="c" datetime="<?=$row['date_create']; ?>"></time></small></td>
                                        </tr>
	                                         <?php
		                                         } }
		                                          ?>
                                        
                                                                            </tbody></table>
                                </div><!-- /.box-body -->
                            </div>
                            
                            
                            
		
		
	</div>
</div>
      <?php } ?></div>
		                      		
	                      		</div>
	                      		
	                  		    
                            
                            
                            
                            
                            
                            
                      		</div>
                    </div>
                    
                    
                     
                    
                    
                    
                    
                    </div>





<?php
 include("footer.inc.php");
?>

<?php
	//}
	}
else {
    include 'auth.php';
}
?>
