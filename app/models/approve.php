<?php
session_start();
include_once ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        if (isset($_GET['create'])) {
            $status_create = "active";
        }
        if (isset($_GET['list'])) {
            $status_list = "active";
        }
?>





<section class="content-header">
                    <h1>
                        <i class="fa fa-check-square-o"></i> <?php
        echo lang('NAVBAR_approve'); ?>
                        <small><?php
        echo lang('APPROVE_title'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php
        echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php
        echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php
        echo lang('NAVBAR_approve'); ?></li>
                    </ol>
                </section>
                
                
                <section class="content">

                    <!-- row -->
                    <div class="row">
                    <div class="col-md-3">
                    
                    <div class="callout">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php
        echo lang('APPROVED_info'); ?>
         </small>
                                    </div>
                                    </div>
                    <div class="col-md-9">
                    
                    
                    
                    
                    <div class="box box-solid">
                                
                                <div class="box-body">
                                

                <?php
        $stmt = $dbConnection->prepare('select 
                id, fio, tel, login, unit_desc, adr, email, posada, user_from, date_app, client_id, skype, type_op 
                from approved_info');
        $stmt->execute();
        $res1 = $stmt->fetchAll();
        
        if (empty($res1)) {
?>
                            <div id="" class="well well-large well-transparent lead">
                <center><?php
            echo lang('MSG_no_records'); ?></center>
            </div>
                <?php
        } 
        else if (!empty($res1)) {
?>
                
                <table class="table table-bordered table-hover" style=" font-size: 14px; ">
                        <thead>
                        <tr>
                            <th><center><?php
            echo lang('APPROVE_info'); ?></center></th>

                            <th><center><?php
            echo lang('APPROVE_fio'); ?></center></th>
                            <th><center><?php
            echo lang('APPROVE_login'); ?></center></th>
                            <th><center><?php
            echo lang('APPROVE_posada'); ?></center></th>
                            <th><center><?php
            echo lang('APPROVE_unit'); ?></center></th>
                            <th><center><?php
            echo lang('APPROVE_tel'); ?></center></th>
                            <th><center><?php
            echo lang('APPROVE_adr'); ?></center></th>
                            <th><center><?php
            echo lang('APPROVE_mail'); ?></center></th>
                            <th><center>skype</center></th>
                            <th><center><?php
            echo lang('APPROVE_app'); ?></center></th>
                        </tr>
                        </thead>
                        <tbody>
                
                <?php
            foreach ($res1 as $row) {
                
                $tmp_id = $row['client_id'];
                
                $stmt = $dbConnection->prepare('select fio, tel, login, unit_desc, adr, email, posada,skype from users where id=:tmp_id and status!=2');
                $stmt->execute(array(
                    ':tmp_id' => $tmp_id
                ));
                $info = $stmt->fetch(PDO::FETCH_ASSOC);
                
                /*
                
                EDIT\ADD?   WHO?
                
                */
                if ($row['type_op'] == "edit") {
                    $top = lang('APPROVE_want_edit');
                } 
                else if ($row['type_op'] == "add") {
                    $top = lang('APPROVE_want_add');
                }
?>



                    
 
                        <tr class="table_<?php
                echo $row['id']; ?>">
                            <td><small><?php
                echo lang('APPROVE_orig'); ?></small></td>

                            <td><small><?php
                echo $info['fio']; ?></small></td>
                            <td><small><?php
                echo $info['login']; ?></small></td>
                            <td><small><?php
                echo $info['posada']; ?></small></td>
                            <td><small><?php
                echo $info['unit_desc']; ?></small></td>
                            <td><small><?php
                echo $info['tel'] . " " . $info['tel_ext']; ?></small></td>
                            <td><small><?php
                echo $info['adr']; ?></small></td>
                            <td><small><?php
                echo $info['email']; ?></small></td>
                            <td><small><?php
                echo $info['skype']; ?></small></td>
                            <td rowspan="2">
                                <center>
                                    <div class="btn-group-vertical">
                                        <button id="action_aprove_yes" type="button" class="btn btn-success btn-xs" value="<?php
                echo $row['id']; ?>"><?php
                echo lang('APPROVE_yes'); ?></button>
                                        <button id="action_aprove_no" type="button" class="btn btn-danger btn-xs" value="<?php
                echo $row['id']; ?>"><?php
                echo lang('APPROVE_no'); ?></button>
                                    </div>
                                </center>

                            </td>
                        </tr>
                        <tr class="table_<?php
                echo $row['id']; ?>">
                            <td><small><em><?php
                echo name_of_user($row['user_from']); ?></em> <?php
                echo $top; ?></small></td>

                            <td><small><?php
                echo $row['fio']; ?></small></td>
                            <td><small><?php
                echo $row['login']; ?></small></td>
                            <td><small><?php
                echo $row['posada']; ?></small></td>
                            <td><small><?php
                echo $row['unit_desc']; ?></small></td>
                            <td><small><?php
                echo $row['tel']; ?></small></td>
                            <td><small><?php
                echo $row['adr']; ?></small></td>
                            <td><small><?php
                echo $row['email']; ?></small></td>
                            <td><small><?php
                echo $row['skype']; ?></small></td>
                        </tr>
<tr class="table_<?php
                echo $row['id']; ?>">
<td colspan="10" class="active">
<br>
</td>
</tr>


                        
                <?php
            }
?>
                </tbody>
                    </table>
                    <br>
                <?php
        }
?>



            
                                </div>
                    </div>
                    </div>
                    </div>
                </section>





        <?php
        include ("footer.inc.php");
?>

    <?php
    }
} 
else {
    include 'auth.php';
}
?>