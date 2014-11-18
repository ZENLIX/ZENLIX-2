<?php
session_start();
include("../functions.inc.php");
$rkeys=array_keys($_GET);

$CONF['title_header']=lang('TICKET_name')." #".get_ticket_id_by_hash($rkeys[1])." - ".$CONF['name_of_firm'];

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    include("head.inc.php");
    
    $hn=$rkeys[1];
    $stmt = $dbConnection->prepare('SELECT
                           id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, comment, last_edit, is_read, lock_by, ok_by, arch, ok_date, prio, last_update
                            from tickets
                            where hash_name=:hn');
    $stmt->execute(array(':hn'=>$hn));
    $res1 = $stmt->fetchAll();
    if (!empty($res1)) {
    foreach($res1 as $row) {
            
            
            
                if ($row['user_to_id'] <> 0 ) {
                $to_text="<div class=''>".name_of_user_ret_nolink($row['user_to_id'])."</div>";
            }
            if ($row['user_to_id'] == 0 ) {
                $to_text="<strong>".lang('t_list_a_all')."</strong> ".lang('T_from')." ".view_array(get_unit_name_return($row['unit_id']));
            }        
            
            
            
            $date_today = date("d.m.Y");
            
            $cid=$row['client_id'];
            $tid=$row['id'];
            
			$subj=$row['subj'];
            $status_ok=$row['status'];
			$ms=$row['msg'];
			$pr=$row['prio'];
            ?>

<section class="content invoice">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-xs-12">
                            <h2 class="page-header">
                                 <?=$CONF['name_of_firm'];?>
                                <small class="pull-right"><?=$date_today;?></small>
                            </h2>
                        </div><!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
	                     <center><h3><?=lang('TICKET_name');?> #<?=$tid;?></h3></center>
                        <div class="col-sm-4 invoice-col">
                            <?=lang('TICKET_t_from');?>
                            <address>
                                <strong><?=name_of_user_ret_nolink($row['user_init_id'])?></strong><br>
        <?php if (get_user_val_by_id($row['user_init_id'],'adr')) {?><i class="fa fa-building-o"></i> <?=get_user_val_by_id($row['user_init_id'],'adr');?><br> <?php } ?>
        
                                <?php if (get_user_val_by_id($row['user_init_id'],'tel')) { ?> <i class="fa fa-phone-square"></i> <?=get_user_val_by_id($row['user_init_id'],'tel');?><br><?php }?>
                                
                                
                              <?php if (get_user_val_by_id($row['user_init_id'],'skype')) { ?><i class="fa fa-skype"></i> <?=get_user_val_by_id($row['user_init_id'],'skype');?><br> <?php } ?>
                                <?php if (get_user_val_by_id($row['user_init_id'],'email')) { ?><i class="fa fa-envelope-o"></i> <?=get_user_val_by_id($row['user_init_id'],'email');?><?php } ?>
                            </address>
                        </div><!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            
                            <address>
                            <?=lang('TICKET_t_to');?><br>
                                <strong><?=$to_text;?></strong><br>
                                
                            </address>
                        </div><!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <?=lang('USERS_p_4');?><br>
                            
                           <address>
                                <strong><?=name_of_user_ret_nolink($cid)?></strong><br>
        <?php if (get_user_val_by_id($cid,'adr')) {?><i class="fa fa-building-o"></i> <?=get_user_val_by_id($cid,'adr');?><br> <?php } ?>
        
                                <?php if (get_user_val_by_id($cid,'tel')) { ?> <i class="fa fa-phone-square"></i> <?=get_user_val_by_id($cid,'tel');?><br><?php }?>
                                
                                
                              <?php if (get_user_val_by_id($cid,'skype')) { ?><i class="fa fa-skype"></i> <?=get_user_val_by_id($cid,'skype');?><br> <?php } ?>
                                <?php if (get_user_val_by_id($cid,'email')) { ?><i class="fa fa-envelope-o"></i> <?=get_user_val_by_id($cid,'email');?><?php } ?>
                            </address>
                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    <!-- Table row -->


                    <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-xs-12">
                            <div class="lead"><?=make_html($row['subj'])?></div>
                           
                            <div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                <?=make_html($row['msg'])?>
                            </div>
                        </div><!-- /.col -->
                        <div class="col-xs-12">
	                        <p class="pull-left"><?=$date_today;?></p>
                            <p class="pull-right">_______________ <?=name_of_user_ret_nolink($_SESSION['helpdesk_user_id'])?>
	                           
                            </p>
                        
                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    <!-- this row will not appear when printing -->
                    <div class="row no-print">
                        <div class="col-xs-12">
                            <button class="btn btn-default" onclick="window.print();"><i class="fa fa-print"></i> Print</button>
                                                    </div>
                    </div>
                </section>
                
                
                
                
                
                
                














        <?php
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

<?php
}
else {
    include 'auth.php';
}
?>
