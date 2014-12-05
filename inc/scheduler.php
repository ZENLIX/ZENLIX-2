<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        
        if (isset($_GET['plus'])) {
	        
	        if (isset($_GET['view'])) {
		        
		        
		    $stmt = $dbConnection->prepare('SELECT id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, period, period_arr, action_time, dt_start, dt_stop, prio, last_action_dt from scheduler_ticket where id=:tid');
            $stmt->execute(array(':tid' => $_GET['view']));
            $cron = $stmt->fetch(PDO::FETCH_ASSOC);
            
            
            $per_arr=explode(",", $cron['period_arr']);
		         ?>
		          <section class="content-header">
                    <h1>
                        <i class="fa fa-tag"></i> <?=lang('cron_title');?>
                        <small>
                            
                            <?=lang('cron_view');?>                          
                        </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $CONF['hostname'] ?>"><span class="icon-svg"></span>  <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?=lang('cron_title');?></li>
                    </ol>
                </section>
                
                
                <section class="content">

<div class="row">
            <div class="col-md-12">                      
                                
                            
                                   
                                   
                                                                   
                            
                            
                            
                            
<div class="" id="form_add">
<input type="hidden" id="main_last_new_ticket" value="<?php echo get_last_ticket_new($_SESSION['helpdesk_user_id']); ?>">


<div class="row" style="padding-bottom:20px;">


                            
                            
                            
                            
                            
                            
<div class="col-md-8" id="div_new">
<div class="box box-solid">
                                
                                <div class="box-body">



<div class="" style="padding:20px;">
<div class="">

<div class="form-horizontal" id="main_form" novalidate="" action="" method="post">


<div class="control-group">
    <div class="controls">
        <div class="form-group" id="for_fio">

            <label for="fio" class="col-sm-2 control-label"><small><?php echo lang('NEW_from'); ?>: </small></label>

            <div class="col-sm-10"data-toggle="tooltip" data-placement="right" title="<?php echo lang('NEW_from_desc'); ?>">

                <div class="input-group">
                <input value="<?=get_user_val_by_id($cron['client_id'], 'login');?>" type="text" name="fio" class="form-control input-sm" id="fio" placeholder="<?php echo lang('NEW_fio'); ?>" autofocus data-toggle="popover" data-trigger="manual" data-html="true" data-placement="right" data-content="<small><?php echo lang('NEW_fio_desc'); ?></small>">
                <a id="select_init_user" param-hash="<?php echo get_user_val('uniq_id'); ?>" href='#' class="input-group-addon">Я</a>
                </div>


            </div>



        </div></div>

    <hr>
<div data-toggle="tooltip" data-placement="right" title="<?php echo lang('NEW_to_desc'); ?>">
    <div class="form-group" id="for_to" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right">
        <label for="to" class="col-md-2 control-label" ><small><?php echo lang('NEW_to'); ?>: </small></label>
        <div class="col-md-6">
            <select data-placeholder="<?php echo lang('NEW_to_unit'); ?>" class="chosen-select form-control" id="to" name="unit_id">
                <option value="0"></option>
                <?php
        $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
        $stmt->execute(array(':n' => '0', ':s' => '1'));
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row) {
            $opt="";
            //echo($row['label']);
            $row['label'] = $row['label'];
            $row['value'] = (int)$row['value'];
            
            if ($cron['unit_id'] == $row['value']) { $opt="selected";}
?>

                            <option value="<?php echo $row['value'] ?>" <?=$opt;?>><?php echo $row['label'] ?></option>

                        <?php
        }
?>

            </select>
        </div>




        <div class="col-md-4" style="" id="dsd" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_to_unit_desc'); ?></small>">
    
    
    <select data-placeholder="<?php echo lang('NEW_to_user'); ?>" id="users_do" name="unit_id" class="form-control input-sm" multiple>
        <option></option>


<?php
        
        /* $qstring = "SELECT fio as label, id as value FROM users where status='1' and login !='system' order by fio ASC;";
                $result = mysql_query($qstring);//query the database for entries containing the term
        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
        */
        
        $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=:n and login !=:system and is_client=0 order by fio ASC');
        $stmt->execute(array(':n' => '1', ':system' => 'system'));
        $res1 = $stmt->fetchAll();
        
        $opts=explode(",", $cron['user_to_id']);
        
        foreach ($res1 as $row) {
            $optu="";
            //echo($row['label']);
            $row['label'] = $row['label'];
            $row['value'] = (int)$row['value'];
            
            if (get_user_status_text($row['value']) == "online") {
                $s = "online";
            } else if (get_user_status_text($row['value']) == "offline") {
                $s = "offline";
            }
            if (in_array($row['value'], $opts)) { $optu="selected";}
            
            
?>
                    <option data-foo="<?php echo $s; ?>" value="<?php echo $row['value'] ?>" <?=$optu;?>><?php echo nameshort($row['label']) ?> </option>

                <?php
        }
?>
    </select>
            

        </div>

    </div>
</div>



</div>

<?php
	
	switch ($cron['prio']) {
		
		case "0": $p_0="active";$p_0s="fa fa-check";
		break;
		case "1": $p_1="active";$p_1s="fa fa-check";
		break;
		case "2": $p_2="active";$p_2s="fa fa-check";
		break;
	}
	
	
		switch ($cron['period']) {
		
		case "day": 	$per_0="active"; $day_field=$cron['period_arr'];
		break;
		case "week": 	$per_1="active"; $week_field=$cron['period_arr'];
		break;
		case "month": 	$per_2="active"; $month_field=$cron['period_arr'];
		break;
	}
	
	
	
	?>


<div class="control-group" id="for_prio">
    <div class="controls">
        <div class="form-group">
            <label for="" class="col-sm-2 control-label"><small><?php echo lang('NEW_prio'); ?>: </small></label>
            <div class="col-sm-10" style=" padding-top: 5px; ">

                <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-xs <?=$p_0;?>" id="prio_low"><i id="lprio_low" class="<?=$p_0s;?>"></i><?php echo lang('NEW_prio_low'); ?></button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info btn-xs <?=$p_1;?>" id="prio_normal"><i id="lprio_norm" class="<?=$p_1s;?>"></i> <?php echo lang('NEW_prio_norm'); ?></button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger btn-xs <?=$p_2;?>" data-toggle="tooltip" data-placement="right" title="<?php echo lang('NEW_prio_high_desc'); ?>" id="prio_high"><i id="lprio_high" class="<?=$p_2s;?>"></i><?php echo lang('NEW_prio_high'); ?></button>
                    </div>
                </div>
            </div></div></div></div>
<?php
        
        /*
        
        
        
        
        
        */
        
        
?>

<div class="control-group" id="for_subj">
        <div class="controls">
          <div class="form-group">
    <label for="subj" class="col-sm-2 control-label"><small><?php echo lang('NEW_subj'); ?>: </small></label>
    <div class="col-sm-10">
      <input type="text" class="form-control input-sm" name="subj" id="subj" placeholder="<?php echo lang('NEW_subj'); ?>" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_subj_msg'); ?></small>" value="<?=$cron['subj'];?>">
    </div>
  </div></div></div>












<div class="control-group">
    <div class="controls">
        <div class="form-group" id="for_msg">
            <label for="msg" class="col-sm-2 control-label"><small><?php echo lang('NEW_MSG'); ?>:</small></label>
            <div class="col-sm-10">
                <textarea data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_MSG_msg'); ?></small>" placeholder="<?php echo lang('NEW_MSG_ph'); ?>" class="form-control input-sm animated" name="msg" id="msg" rows="3" required="" data-validation-required-message="<?php echo lang('EXT_fill_msg'); ?>" aria-invalid="false"><?=$cron['msg'];?></textarea>
            </div>
        </div>
        <div class="help-block"></div></div></div>



<div class="control-group" id="for_period">
        <div class="controls">
          <div class="form-group">
    <label for="period" class="col-sm-2 control-label"><small><?=lang('cron_period');?>: </small></label>
    <div class="col-sm-10">
     <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs <?=$per_0;?>" id="btn_period_day"><?=lang('cron_day');?></button>
                    </div>
                                        <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs <?=$per_1;?>" id="btn_period_week"><?=lang('cron_week');?></button>
                    </div>
                                        <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs <?=$per_2;?>" id="btn_period_month"><?=lang('cron_month');?></button>
                    </div>
                </div>
    </div>
  </div>
          
          
          </div>
          
          </div>
  
  
  
  
  
  
  <div class="control-group" id="for_subj">
        <div class="controls">
          <div class="form-group">
    <label for="subj" class="col-sm-2 control-label"><small><?=lang('cron_tab');?>: </small></label>
    <div class="col-sm-10">
     <div id="period_day">
	     
	     <div class="input-group">
                                        <span class="input-group-addon"><small><?=lang('cron_do_every');?> </small></span>
                                        <input type="text" class="form-control input-sm" id="day_field" value="<?=$day_field;?>">
                                        <span class="input-group-addon"><small><?=lang('cron_do_day');?> </small></span>
                                    </div>
	    
                                    
                                    
	     
	     </div>
     <div id="period_week"><div class="input-group">
                                            <?php
	                                            
	                                            
	                                            
	                                            ?>
                                         
                                        <span class="input-group-addon"><small><?=lang('cron_do_every');?> </small></span>
                                            <select multiple="" class="chosen-select form-control input-sm " id="week_select">
                                                <option value="1"><?=lang('cron_1');?></option>
                                                <option value="2"><?=lang('cron_2');?></option>
                                                <option value="3"><?=lang('cron_3');?></option>
                                                <option value="4"><?=lang('cron_4');?></option>
                                                <option value="5"><?=lang('cron_5');?></option>
                                                <option value="6"><?=lang('cron_6');?></option>
                                                <option value="7"><?=lang('cron_7');?></option>
                                            </select>
                                            <span class="input-group-addon"><small><?=lang('cron_do_week');?></small></span>
	                                            </div>
                                            </div>
                                       
                                        
                                        
                                        
     <div id="period_month"> <div class="input-group">
                                        <span class="input-group-addon"><small><?=lang('cron_do_every');?> </small></span>
                                        
                                            
                                            <select multiple="" class="chosen-select form-control input-sm " id="month_select">
                                                <?php 
	                                                
	                                                for ($i = 1; $i <= 30; $i++) { 
	                                                $optm="";
	                                                if (in_array($i, $per_arr)) { $optm="selected";}
                                                ?>
    <option value="<?=$i;?>" <?=$optm;?>><?=$i;?></option>
<?php } ?>
                                               
                                            </select>
                                            <span class="input-group-addon"><small><?=lang('cron_do_month');?></small></span>
                                            </div>
                                            </div>
                                            
                                            
                                            
                                            
                                            
                                        </div></div>
    </div>
  </div></div>
  
  
  
  
  <div class="control-group" id="for_period">
        <div class="controls">
          <div class="form-group">
    <label for="period" class="col-sm-2 control-label"><small><?=lang('cron_ta');?>: </small></label>
    <div class="col-sm-10">
     
     <div class="bootstrap-timepicker">
                                        <div class="form-group">
          
                                            <div class="input-group">
                                                <input type="text" class="form-control timepicker input-sm" id="time_action" value="<?=$cron['action_time'];?>"/>
                                                <div class="input-group-addon">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
                                            </div><!-- /.input group -->
                                        </div><!-- /.form group -->
                                    </div>
                                    
                                    
     
     
     
    </div>
  </div>
          
          
          </div>
          
          </div>
          
          
          
  
  
	  
	  <div class="control-group" id="for_period">
        <div class="controls">
          <div class="form-group">
    <label for="period" class="col-sm-2 control-label"><small><?=lang('cron_active');?>: </small></label>
    <div class="col-sm-10">
     <div class="form-group">

    <div class="input-group ">
      <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" name="reservation" id="reservation" class="form-control input-sm active" value="<?=$cron['dt_start'];?> - <?=$cron['dt_stop'];?>">
    </div>
  </div>
    </div>
  </div>
          
          
          </div>
          
          </div>
          
          
          
          
          
          
         
	  
 
  

<div class="row">
	<div class="col-md-12"><hr></div>
<div class="col-md-2"></div>
<div class="col-md-10" id="processing">
    <div class="btn-group btn-group-justified">

        <div class="btn-group">
            <button id="cron_delete" value="<?=$cron['id']; ?>" class="btn btn-danger" type="submit"><i class="fa fa-eraser"></i> <?=lang('cron_del');?></button>
        </div>
    </div>
    <input type="hidden" id="file_array" value="">
    <input type="hidden" id="client_id_param" value="<?=$cron['client_id'];?>">
    <input type="hidden" id="hashname" value="<?php echo md5(time()); ?>">
    <input type="hidden" id="status_action" value="">
    <input type="hidden" id="prio" value="<?=$cron['prio'];?>">
    <input type="hidden" id="period" value="<?=$cron['period'];?>">
    <input type="hidden" id="action_start" value="<?=$cron['dt_start'];?>">
    <input type="hidden" id="action_stop" value="<?=$cron['dt_stop'];?>">
    <input type="hidden" value="<?php
        echo $_SESSION['helpdesk_user_id']; ?>" id="user_init_id">
    <input type="hidden" id="user_name_login" value="<?php echo get_user_val('login'); ?>">







</div>
</div>

</div>
</div>


    <br>


                                </div><!-- /.box-body -->
                            </div>


</div>
<div id="alert_add">
    </div>
<div class="col-md-4" id="user_info">

                            
                            
                            
                            





</div>
    


</div>




</div><!-- /.box-body -->
                            
                            
                            
                            
                            
                            
                            
                            </div>
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
</div>


</section>
<?php
		         }
	        else if (!isset($_GET['view'])) {
	        
	        ?>
	        
	                        <section class="content-header">
                    <h1>
                        <i class="fa fa-tag"></i> <?=lang('cron_title');?>
                        <small><?=lang('cron_t_create');?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $CONF['hostname'] ?>"><span class="icon-svg"></span>  <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?=lang('cron_title');?></li>
                    </ol>
                </section>
                
                
                <section class="content">

<div class="row">
            <div class="col-md-12">                      
                                
                            
                                   
                                   
                                                                   
                            
                            
                            
                            
<div class="" id="form_add">
<input type="hidden" id="main_last_new_ticket" value="<?php echo get_last_ticket_new($_SESSION['helpdesk_user_id']); ?>">


<div class="row" style="padding-bottom:20px;">


                            
                            
                            
                            
                            
                            
<div class="col-md-8" id="div_new">
<div class="box box-solid">
                                
                                <div class="box-body">



<div class="" style="padding:20px;">
<div class="">

<div class="form-horizontal" id="main_form" novalidate="" action="" method="post">


<div class="control-group">
    <div class="controls">
        <div class="form-group" id="for_fio">

            <label for="fio" class="col-sm-2 control-label"><small><?php echo lang('NEW_from'); ?>: </small></label>

            <div class="col-sm-10"data-toggle="tooltip" data-placement="right" title="<?php echo lang('NEW_from_desc'); ?>">

                <div class="input-group">
                <input  type="text" name="fio" class="form-control input-sm" id="fio" placeholder="<?php echo lang('NEW_fio'); ?>" autofocus data-toggle="popover" data-trigger="manual" data-html="true" data-placement="right" data-content="<small><?php echo lang('NEW_fio_desc'); ?></small>">
                <a id="select_init_user" param-hash="<?php echo get_user_val('uniq_id'); ?>" href='#' class="input-group-addon">Я</a>
                </div>


            </div>



        </div></div>

    <hr>
<div data-toggle="tooltip" data-placement="right" title="<?php echo lang('NEW_to_desc'); ?>">
    <div class="form-group" id="for_to" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right">
        <label for="to" class="col-md-2 control-label" ><small><?php echo lang('NEW_to'); ?>: </small></label>
        <div class="col-md-6">
            <select data-placeholder="<?php echo lang('NEW_to_unit'); ?>" class="chosen-select form-control" id="to" name="unit_id">
                <option value="0"></option>
                <?php
        $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
        $stmt->execute(array(':n' => '0', ':s' => '1'));
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row) {
            
            //echo($row['label']);
            $row['label'] = $row['label'];
            $row['value'] = (int)$row['value'];
?>

                            <option value="<?php echo $row['value'] ?>"><?php echo $row['label'] ?></option>

                        <?php
        }
?>

            </select>
        </div>




        <div class="col-md-4" style="" id="dsd" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_to_unit_desc'); ?></small>">
    
    
    <select data-placeholder="<?php echo lang('NEW_to_user'); ?>" id="users_do" name="unit_id" class="form-control input-sm" multiple>
        <option></option>


<?php
        
        /* $qstring = "SELECT fio as label, id as value FROM users where status='1' and login !='system' order by fio ASC;";
                $result = mysql_query($qstring);//query the database for entries containing the term
        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
        */
        
        $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=:n and login !=:system and is_client=0 order by fio ASC');
        $stmt->execute(array(':n' => '1', ':system' => 'system'));
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row) {
            
            //echo($row['label']);
            $row['label'] = $row['label'];
            $row['value'] = (int)$row['value'];
            
            if (get_user_status_text($row['value']) == "online") {
                $s = "online";
            } else if (get_user_status_text($row['value']) == "offline") {
                $s = "offline";
            }
?>
                    <option data-foo="<?php echo $s; ?>" value="<?php echo $row['value'] ?>"><?php echo nameshort($row['label']) ?> </option>

                <?php
        }
?>
    </select>
            

        </div>

    </div>
</div>



</div>




<div class="control-group" id="for_prio">
    <div class="controls">
        <div class="form-group">
            <label for="" class="col-sm-2 control-label"><small><?php echo lang('NEW_prio'); ?>: </small></label>
            <div class="col-sm-10" style=" padding-top: 5px; ">

                <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-xs" id="prio_low"><i id="lprio_low" class=""></i><?php echo lang('NEW_prio_low'); ?></button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info btn-xs active" id="prio_normal"><i id="lprio_norm" class="fa fa-check"></i> <?php echo lang('NEW_prio_norm'); ?></button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="right" title="<?php echo lang('NEW_prio_high_desc'); ?>" id="prio_high"><i id="lprio_high" class=""></i><?php echo lang('NEW_prio_high'); ?></button>
                    </div>
                </div>
            </div></div></div></div>
<?php
        
        /*
        
        
        
        
        
        */
        
        
?>

<div class="control-group" id="for_subj">
        <div class="controls">
          <div class="form-group">
    <label for="subj" class="col-sm-2 control-label"><small><?php echo lang('NEW_subj'); ?>: </small></label>
    <div class="col-sm-10">
      <input type="text" class="form-control input-sm" name="subj" id="subj" placeholder="<?php echo lang('NEW_subj'); ?>" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_subj_msg'); ?></small>" >
    </div>
  </div></div></div>












<div class="control-group">
    <div class="controls">
        <div class="form-group" id="for_msg">
            <label for="msg" class="col-sm-2 control-label"><small><?php echo lang('NEW_MSG'); ?>:</small></label>
            <div class="col-sm-10">
                <textarea data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_MSG_msg'); ?></small>" placeholder="<?php echo lang('NEW_MSG_ph'); ?>" class="form-control input-sm animated" name="msg" id="msg" rows="3" required="" data-validation-required-message="<?php echo lang('EXT_fill_msg'); ?>" aria-invalid="false"></textarea>
            </div>
        </div>
        <div class="help-block"></div></div></div>


<div class="control-group" id="for_period">
        <div class="controls">
          <div class="form-group">
    <label for="period" class="col-sm-2 control-label"><small><?=lang('cron_period');?>: </small></label>
    <div class="col-sm-10">
     <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs active" id="btn_period_day"><?=lang('cron_day');?></button>
                    </div>
                                        <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs" id="btn_period_week"><?=lang('cron_week');?></button>
                    </div>
                                        <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs" id="btn_period_month"><?=lang('cron_month');?></button>
                    </div>
                </div>
    </div>
  </div>
          
          
          </div>
          
          </div>
  
  
  
  
  
  
  <div class="control-group" id="for_subj">
        <div class="controls">
          <div class="form-group">
    <label for="subj" class="col-sm-2 control-label"><small><?=lang('cron_tab');?>: </small></label>
    <div class="col-sm-10">
     <div id="period_day">
	     
	     <div class="input-group">
                                        <span class="input-group-addon"><small><?=lang('cron_do_every');?> </small></span>
                                        <input type="text" class="form-control input-sm" id="day_field">
                                        <span class="input-group-addon"><small><?=lang('cron_do_day');?> </small></span>
                                    </div>
	    
                                    
                                    
	     
	     </div>
     <div id="period_week"><div class="input-group">
                                            
                                         
                                        <span class="input-group-addon"><small><?=lang('cron_do_every');?> </small></span>
                                            <select multiple="" class="chosen-select form-control input-sm " id="week_select" data-placeholder=" ">
                                                <option value="1"><?=lang('cron_1');?></option>
                                                <option value="2"><?=lang('cron_2');?></option>
                                                <option value="3"><?=lang('cron_3');?></option>
                                                <option value="4"><?=lang('cron_4');?></option>
                                                <option value="5"><?=lang('cron_5');?></option>
                                                <option value="6"><?=lang('cron_6');?></option>
                                                <option value="7"><?=lang('cron_7');?></option>
                                            </select>
                                            <span class="input-group-addon"><small><?=lang('cron_do_week');?></small></span>
	                                            </div>
                                            </div>
                                       
                                        
                                        
                                        
     <div id="period_month"> <div class="input-group">
                                        <span class="input-group-addon"><small><?=lang('cron_do_every');?> </small></span>
                                        
                                            
                                            <select multiple="" class="chosen-select form-control input-sm " id="month_select" data-placeholder=" ">
                                                <?php for ($i = 1; $i <= 30; $i++) { ?>
    <option value="<?=$i;?>"><?=$i;?></option>
<?php } ?>
                                               
                                            </select>
                                            <span class="input-group-addon"><small><?=lang('cron_do_month');?></small></span>
                                            </div>
                                            </div>
                                            
                                            
                                            
                                            
                                            
                                        </div></div>
    </div>
  </div>
  
  
  
  
  <div class="control-group" id="for_period">
        <div class="controls">
          <div class="form-group">
    <label for="period" class="col-sm-2 control-label"><small><?=lang('cron_ta');?>: </small></label>
    <div class="col-sm-10">
     
     <div class="bootstrap-timepicker">
                                        <div class="">
          
                                            <div class="input-group">
                                                
                                                <div class="input-group-addon">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
                                                <input type="text" class="form-control timepicker input-sm" id="time_action"/>
                                            </div><!-- /.input group -->
                                        </div><!-- /.form group -->
                                    </div>
                                    
                                    
     
     
     
    </div>
  </div>
          
          
          </div>
          
          </div>
          
          
          
  
  
	  
	  <div class="control-group" id="for_period">
        <div class="controls">
          <div class="form-group">
    <label for="period" class="col-sm-2 control-label"><small><?=lang('cron_active');?>: </small></label>
    <div class="col-sm-10">
     <div class="">

    <div class="input-group ">
      <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" name="reservation" id="reservation" class="form-control input-sm active">
    </div>
  </div>
    </div>
  </div>
          
          
          </div>
          
          </div>
          
          
          
          
          
          
         
	  
 
  

<div class="row">
	<div class="col-md-12"><hr></div>
<div class="col-md-2"></div>
<div class="col-md-10" id="processing">
    <div class="btn-group btn-group-justified">
        <div class="btn-group">
            <button id="add_scheduler" class="btn btn-success" type="button"><i class="fa fa-check-circle-o"></i> <?=lang('cron_add');?></button>
        </div>
        <div class="btn-group">
            <button id="reset_cron" class="btn btn-default" type="submit"><i class="fa fa-eraser"></i> <?php echo lang('NEW_button_reset'); ?></button>
        </div>
    </div>
    <input type="hidden" id="file_array" value="">
    <input type="hidden" id="client_id_param" value="">
    <input type="hidden" id="hashname" value="<?php echo md5(time()); ?>">
    <input type="hidden" id="status_action" value="">
    <input type="hidden" id="prio" value="1">
    <input type="hidden" id="period" value="day">
    <input type="hidden" id="action_start" value="">
    <input type="hidden" id="action_stop" value="">
    <input type="hidden" value="<?php
        echo $_SESSION['helpdesk_user_id']; ?>" id="user_init_id">
    <input type="hidden" id="user_name_login" value="<?php echo get_user_val('login'); ?>">







</div><div id="error_content"></div>
</div>

</div>
</div>


    <br>


                                </div><!-- /.box-body -->
                            </div>


</div> </div>
<div id="alert_add">
    </div>
<div class="col-md-4" id="user_info">

                            
                            
                            
                            





</div>
    


</div>




</div><!-- /.box-body -->
                            
                            
                            
                            
                            
                            
                            
                            </div>
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
</div>


</section>
	        
	        <?php
        } }
        else if (!isset($_GET['plus'])) {
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-clock-o"></i> <?=lang('cron_title');?>
                        <small><?=lang('cron_title_list');?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?=lang('cron_title');?></li>
                    </ol>
                </section>
                
                
                <section class="content">

                    <!-- row -->
                    <div class="row">
                    <div class="col-md-3">
                                
        <a href="scheduler?plus" class="btn btn-default btn-sm btn-block"><?php echo lang('UNITS_add'); ?></a>
      <br>
      
      
                    <div class="callout callout-info">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?=lang('cron_info');?>
       </small>
                                    </div></div>
                    <div class="col-md-9">
                    
                    
                    
                    
                    <div class="box box-solid">
                                
                                <div class="box-body">
                                <div class="" id="content_cron">
      
<?php
        
        $stmt = $dbConnection->prepare('select id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, period, period_arr, action_time, dt_start, dt_stop, prio from scheduler_ticket 
         ');
        $stmt->execute();
        $res1 = $stmt->fetchAll();
        // start NOW stop
?>      
      
      
      
<table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
        <thead>
          <tr>
            
            
            <th><center><?=lang('NEW_subj');?></center></th>
            <th><center><?=lang('t_LIST_worker');?></center></th>
            
            <th><center><?=lang('USERS_p_4');?></center></th>
            
            <th><center><?=lang('DEPS_action');?></center></th>
          </tr>
        </thead>
        <tbody>     
        <?php
        
        foreach ($res1 as $row) {
	        if ($row['user_to_id'] <> 0) {
                    $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['user_to_id'])) . "</div>";
                }
                if ($row['user_to_id'] == 0) {
                    $to_text = "<strong data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . view_array(get_unit_name_return($row['unit_id'])) . "\">" . lang('t_list_a_all') . "</strong>";
                }


?>
        <tr id="tr_<?php echo $row['id']; ?>">
        
        
        
        
        
        <td><small><?=cutstr(make_html($row['subj'], 'no')); ?></small></td>
        <td><small><?=$to_text; ?></small></td>
        
        <td><small><a href="view_user?<?php echo get_user_hash_by_id($row['client_id']); ?>">
                    <?php echo get_user_val_by_id($row['client_id'], 'fio'); ?>
                    </a></small></td>
        
        <td><small><center>
                            <div class="btn-group btn-group-xs actions">
                                <a href="scheduler?plus&view=<?=$row['id'];?>" class="btn btn-primary">view</a>
                                <button id="cron_delete" value="<?=$row['id']; ?>" data-toggle="tooltip" data-placement="bottom" title="del" type="button" class="btn btn-danger">del</button>

                                
                            </div>
                        </center></small></td>
        


        </tr>
                <?php
        } ?>
        
        
            
        </tbody>
</table>

      <br>
      </div>
                                </div>
                    </div>
                    </div>
                    </div>
                </section>
                

<?php
	} 
        include ("footer.inc.php");
    }
} else {
    include '../auth.php';
}
?>