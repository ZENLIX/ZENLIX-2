<?php
session_start();
//include ("../functions.inc.php");
$rkeys = array_keys($_GET);

$CONF['title_header'] = lang('TICKET_name') . " #" . get_ticket_id_by_hash($rkeys[0]) . " - " . $CONF['name_of_firm'];

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    include ("head.inc.php");
    
    $hn = $rkeys[0];
    $stmt = $dbConnection->prepare('SELECT
                           id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, comment, last_edit, is_read, lock_by, ok_by, arch, ok_date, prio, last_update
                            from tickets
                            where hash_name=:hn');
    $stmt->execute(array(
        ':hn' => $hn
    ));
    $res1 = $stmt->fetchAll();
    if (!empty($res1)) {
        foreach ($res1 as $row) {
            
            if ($row['user_to_id'] <> 0) {
                $to_text = "<div class=''>" . name_of_user_ret_nolink($row['user_to_id']) . "</div>";
            }
            if ($row['user_to_id'] == 0) {
                $to_text = "<strong>" . lang('t_list_a_all') . "</strong> " . lang('T_from') . " " . view_array(get_unit_name_return($row['unit_id']));
            }
            
            $date_today = date("d.m.Y");
            
            $cid = $row['client_id'];
            $tid = $row['id'];
            
            $subj = $row['subj'];
            $status_ok = $row['status'];
            $ms = $row['msg'];
            $pr = $row['prio'];
?>

<section class="content invoice">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-xs-12">
                            <h2 class="page-header">
                                 <?php
            echo $CONF['name_of_firm']; ?>
                                <small class="pull-right"><?php
            echo $date_today; ?></small>
                            </h2>
                        </div><!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                         <center><h3><?php
            echo lang('TICKET_name'); ?> #<?php
            echo $tid; ?></h3></center>
                        <div class="col-sm-4 invoice-col">
                            <?php
            echo lang('TICKET_t_from'); ?>
                            <address>
                                <strong><?php
            echo name_of_user_ret_nolink($row['user_init_id']) ?></strong><br>
        <?php
            if (get_user_val_by_id($row['user_init_id'], 'adr')) { ?><i class="fa fa-building-o"></i> <?php
                echo get_user_val_by_id($row['user_init_id'], 'adr'); ?><br> <?php
            } ?>
        
                                <?php
            if (get_user_val_by_id($row['user_init_id'], 'tel')) { ?> <i class="fa fa-phone-square"></i> <?php
                echo get_user_val_by_id($row['user_init_id'], 'tel'); ?><br><?php
            } ?>
                                
                                
                              <?php
            if (get_user_val_by_id($row['user_init_id'], 'skype')) { ?><i class="fa fa-skype"></i> <?php
                echo get_user_val_by_id($row['user_init_id'], 'skype'); ?><br> <?php
            } ?>
                                <?php
            if (get_user_val_by_id($row['user_init_id'], 'email')) { ?><i class="fa fa-envelope-o"></i> <?php
                echo get_user_val_by_id($row['user_init_id'], 'email'); ?><?php
            } ?>
                            </address>
                        </div><!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            
                            <address>
                            <?php
            echo lang('TICKET_t_to'); ?><br>
                                <strong><?php
            echo $to_text; ?></strong><br>
                                
                            </address>
                        </div><!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <?php
            echo lang('USERS_p_4'); ?><br>
                            
                           <address>
                                <strong><?php
            echo name_of_user_ret_nolink($cid) ?></strong><br>
        <?php
            if (get_user_val_by_id($cid, 'adr')) { ?><i class="fa fa-building-o"></i> <?php
                echo get_user_val_by_id($cid, 'adr'); ?><br> <?php
            } ?>
        
                                <?php
            if (get_user_val_by_id($cid, 'tel')) { ?> <i class="fa fa-phone-square"></i> <?php
                echo get_user_val_by_id($cid, 'tel'); ?><br><?php
            } ?>
                                
                                
                              <?php
            if (get_user_val_by_id($cid, 'skype')) { ?><i class="fa fa-skype"></i> <?php
                echo get_user_val_by_id($cid, 'skype'); ?><br> <?php
            } ?>
                                <?php
            if (get_user_val_by_id($cid, 'email')) { ?><i class="fa fa-envelope-o"></i> <?php
                echo get_user_val_by_id($cid, 'email'); ?><?php
            } ?>
                            </address>
                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    <!-- Table row -->


                    <div class="row">
                        <!-- accepted payments column -->
<div class="col-xs-12">
<?php
            $stmts = $dbConnection->prepare('SELECT * FROM ticket_data where ticket_hash=:n');
            $stmts->execute(array(
                ':n' => $hn
            ));
            $res11 = $stmts->fetchAll();
            
            if (!empty($res11)) {
?><br>
<small class="text-muted"><?php echo lang('FIELD_add_title'); ?>: </small>
<table class="table table-bordered">
                <tbody>
<?php
                foreach ($res11 as $rown) {
                    
                    $stmt2 = $dbConnection->prepare('SELECT name from ticket_fields where id=:tm and status=:s');
                    $stmt2->execute(array(
                        ':tm' => $rown['field_id'],
                        ':s' => '1'
                    ));
                    
                    $tt = $stmt2->fetch(PDO::FETCH_ASSOC);
?>

        <tr>
                    <td style="width:150px"><small class="text-muted"><?php
                    echo $rown['field_name']; ?>: </small></td>
                    <td><small><?php
                    echo $rown['field_val']; ?> </small></td>
                    
                    
                </tr>


<?php
                }
?>
 </tbody>
            </table>
<?php
            }
?>
</div>
<div class="col-xs-12">
<hr>
</div>

                        <div class="col-xs-12">
                            <div class="lead"><?php
            echo make_html($row['subj']) ?></div>
                           
                            <div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                <?php
            echo make_html($row['msg']) ?>
                            </div>
                        </div><!-- /.col -->
                        <div class="col-xs-12">
                            <p class="pull-left"><?php
            echo $date_today; ?></p>
                            <p class="pull-right">_______________ <?php
            echo name_of_user_ret_nolink($_SESSION['helpdesk_user_id']) ?>
                               
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
            <center><?php
        echo lang('TICKET_t_no'); ?></center>
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
