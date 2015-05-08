<?php
session_start();
include ("../functions.inc.php");
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    //if (validate_admin($_SESSION['helpdesk_user_id'])) {
    
    $CONF['title_header'] = lang('VIEWUSER_title') . " - " . $CONF['name_of_firm'];
    
    include ("head.inc.php");
    include ("navbar.inc.php");



$rkeys = array_keys($_GET);
    $hn = $rkeys[1];
    $stmt = $dbConnection->prepare('SELECT
                            id, fio, posada, unit_desc, usr_img, tel, skype, last_time, status,email, adr, is_client, uniq_id
                            from users
                            where uniq_id=:hn limit 1');
    
    $stmt->execute(array(
        ':hn' => $hn
    ));
    $res1 = $stmt->fetchAll();
    if (!empty($res1)) {
        foreach ($res1 as $row) {
            $user_id = $row['id'];
            $user_fio = $row['fio'];
            $user_posada = $row['posada'];
            $user_unit = $row['unit_desc'];
            $is_client = $row['is_client'];
            $user_tel = $row['tel'];
            $user_skype = $row['skype'];
            $user_last_time = $row['last_time'];
            $user_status = $row['last_status'];
            $user_mail = $row['email'];
            $user_adr = $row['adr'];
            $uniq_id = $row['uniq_id'];
            
            $user_status = $row['status'];
            
            if ($row['usr_img']) {
                $user_img = $CONF['hostname'] . '/upload_files/avatars/' . $row['usr_img'];
            } 
            else if (!$row['usr_img']) {
                $user_img = $CONF['hostname'] . '/img/avatar5.png';
            }
        }
        $find_user=true;
    } 
    else {

$find_user=false;

 }



$user_last_time_status=false;
if ($user_last_time) {
$user_last_time_status=true;
}


$canWriteMessage=false;
if ($user_id != $_SESSION['helpdesk_user_id']) {
$canWriteMessage=true;
}



$uViewAdr=false;
if (($user_adr) && ($user_unit)) { 
$uViewAdr=true;
}


$canUserSkype=false;
if ($user_skype) {
$canUserSkype=true;
}






////////////////////////////////////////////

$ufieldsStatus=false;
$ufields=array();
$stmtf = $dbConnection->prepare('SELECT user_data.field_val as udf, user_data.field_name as udfn from user_data,user_fields where user_data.field_id=user_fields.id and user_data.user_id=:uid and user_fields.for_client=1 and user_fields.status=1');
        $stmtf->execute(array(
            ':uid' => $user_id
        ));
        $resf = $stmtf->fetchAll();
        
        if (!empty($resf)) {
$ufieldsStatus=true;

foreach ($resf as $fv) {


array_push($ufields, array(
    'udfn'=>$fv['udfn'],
    'udf'=>$fv['udf']
    ));


}




        }


////////////////////////////////////////////



$ufieldsStatus2=false;
$ufields2=array();
if (get_user_val_by_id($_SESSION['helpdesk_user_id'], 'priv') <> "1") {
            $stmtf = $dbConnection->prepare('SELECT user_data.field_val as udf, user_data.field_name as udfn from user_data,user_fields where user_data.field_id=user_fields.id and user_data.user_id=:uid and user_fields.for_client=0 and user_fields.status=1');
            $stmtf->execute(array(
                ':uid' => $user_id
            ));
            $resf = $stmtf->fetchAll();
            
            if (!empty($resf)) {
$ufieldsStatus2=true;

foreach ($resf as $fv) {


array_push($ufields2, array(
    'udfn'=>$fv['udfn'],
    'udf'=>$fv['udf']
    ));


}

            }

        }



$check_admin_user_priv=false;
if (check_admin_user_priv($user_id)) {
$check_admin_user_priv=true;
}





$someStatStatus_one=false;
$someStatStatus_arr=array();
            $stmt = $dbConnection->prepare('select id, subj, date_create, hash_name from tickets where status=0 and lock_by=:u order by id desc');
            $stmt->execute(array(
                ':u' => $user_id
            ));
            $result = $stmt->fetchAll();
            
            if (empty($result)) {

$someStatStatus_one=false;

            }
            else if (!empty($result)) {
$someStatStatus_one=true;
                foreach ($result as $row) {
array_push($someStatStatus_arr, array(
'hash_name'=>$row['hash_name'],
'id'=>$row['id'],
'subj'=>$row['subj'],
'date_create'=>$row['date_create']


    ));
                }

            }












$someStatStatus_two=false;
$someStatStatus_arr_two=array();
                        $stmt = $dbConnection->prepare('select id, subj, date_create, hash_name from tickets where status=0 and lock_by=0 and (find_in_set(:u,user_to_id)) order by id desc');
            $stmt->execute(array(
                ':u' => $user_id
            ));
            $result = $stmt->fetchAll();
            
            if (empty($result)) {

$someStatStatus_two=false;

            }
            else if (!empty($result)) {
$someStatStatus_two=true;
                foreach ($result as $row) {
array_push($someStatStatus_arr_two, array(
'hash_name'=>$row['hash_name'],
'id'=>$row['id'],
'subj'=>$row['subj'],
'date_create'=>$row['date_create']


    ));
                }

            }




/////////////////////////////////////////









$basedir = dirname(dirname(__FILE__)); 
            ////////////
    try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/inc/views');
            
            // инициализируем Twig
            $twig = new Twig_Environment($loader);
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('view_user.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'hostname'=>$CONF['hostname'],
                'name_of_firm'=>$CONF['name_of_firm'],
                'VIEWUSER_title'=>lang('VIEWUSER_title'),
                'VIEWUSER_title_ext'=>lang('VIEWUSER_title_ext'),
                'find_user'=>$find_user,
                'TICKET_t_no'=>lang('TICKET_t_no'),
                'user_status'=>$user_status,
                'user_fio'=>$user_fio,
                'user_img'=>$user_img,
                'USER_DEL_main'=>lang('USER_DEL_main'),
                'USER_DEL_info'=>lang('USER_DEL_info'),
                'user_posada'=>$user_posada,
                'user_last_time_status'=>$user_last_time_status,
                'uniq_id'=>$uniq_id,
                'stats_last_time'=>lang('stats_last_time'),
                'user_last_time'=>$user_last_time,
                'canWriteMessage'=>$canWriteMessage,
                'EXT_do_write_message'=>lang('EXT_do_write_message'),
                'P_main'=>lang('P_main'),
                'get_user_status'=>get_user_status($user_id),
                'uViewAdr'=>$uViewAdr,
                'APPROVE_adr'=>lang('APPROVE_adr'),
                'user_adr'=>$user_adr,
                'user_unit'=>$user_unit,
                'canUserSkype'=>$canUserSkype,
                'user_skype'=>$user_skype,
                'user_tel'=>$user_tel,
                'APPROVE_tel'=>lang('APPROVE_tel'),
                'user_mail'=>$user_mail,
                'APPROVE_mail'=>lang('APPROVE_mail'),
                'FIELD_add_title'=>lang('FIELD_add_title'),
                'ufields'=>$ufields,
                'ufieldsStatus'=>$ufieldsStatus,
                'ufields2'=>$ufields2,
                'ufieldsStatus2'=>$ufieldsStatus2,
                'is_client'=>$is_client,
                'get_total_tickets_out'=>get_total_tickets_out($user_id),
                'get_total_tickets_count'=>get_total_tickets_count(),
                'EXT_t_created'=>lang('EXT_t_created'),
                'get_total_tickets_lock'=>get_total_tickets_lock($user_id),
                'EXT_t_locked'=>lang('EXT_t_locked'),
                'get_total_tickets_ok'=>get_total_tickets_ok($user_id),
                'EXT_t_oked'=>lang('EXT_t_oked'),
                'check_admin_user_priv'=>$check_admin_user_priv,
                'PROFILE_tickets_lock'=>lang('PROFILE_tickets_lock'),
                'someStatStatus_one'=>$someStatStatus_one,
                'someStatStatus_arr'=>$someStatStatus_arr,
                'MSG_no_records'=>lang('MSG_no_records'),
                'NEW_subj'=>lang('NEW_subj'),
                'TICKET_t_date'=>lang('TICKET_t_date'),
                'PROFILE_tickets_free'=>lang('PROFILE_tickets_free'),
                'someStatStatus_two'=>$someStatStatus_two,
                'someStatStatus_arr_two'=>$someStatStatus_arr_two



            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }








/*

?>
<section class="content-header">
    <h1>
    <i class="fa fa-bullhorn"></i> <?php
    echo lang('VIEWUSER_title'); ?>
    <small><?php
    echo lang('VIEWUSER_title_ext'); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php
    echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php
    echo $CONF['name_of_firm'] ?></a></li>
        <li class="active"><?php
    echo lang('VIEWUSER_title'); ?></li>
    </ol>
</section>


<?php
    $rkeys = array_keys($_GET);
    $hn = $rkeys[1];
    $stmt = $dbConnection->prepare('SELECT
                            id, fio, posada, unit_desc, usr_img, tel, skype, last_time, status,email, adr, is_client, uniq_id
                            from users
                            where uniq_id=:hn limit 1');
    
    $stmt->execute(array(
        ':hn' => $hn
    ));
    $res1 = $stmt->fetchAll();
    if (!empty($res1)) {
        foreach ($res1 as $row) {
            $user_id = $row['id'];
            $user_fio = $row['fio'];
            $user_posada = $row['posada'];
            $user_unit = $row['unit_desc'];
            $is_client = $row['is_client'];
            $user_tel = $row['tel'];
            $user_skype = $row['skype'];
            $user_last_time = $row['last_time'];
            $user_status = $row['last_status'];
            $user_mail = $row['email'];
            $user_adr = $row['adr'];
            $uniq_id = $row['uniq_id'];
            
            $user_status = $row['status'];
            
            if ($row['usr_img']) {
                $user_img = $CONF['hostname'] . '/upload_files/avatars/' . $row['usr_img'];
            } 
            else if (!$row['usr_img']) {
                $user_img = $CONF['hostname'] . '/img/avatar5.png';
            }
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
<section class="content">
    <?php
    if ($user_status == "2") {
?>
    <div class="row">
        <div class="col-md-3">
            <div class="box box-warning">
                <div class="box-header">
                    <h4 style="text-align:center;"><?php
        echo $user_fio; ?></h4>
                </div>
                <div class="box-body">

                    <center>
                    <img  src="<?php
        echo $user_img; ?>" class="img-rounded img-responsive" alt="User Image">
                    </center>





                    </div><!-- /.box-body -->
                </div>






            </div>
            <div class="col-md-9">

                <div class="row">

                    <div class="col-md-12"><div class="box box-solid">

                        <div class="box-body">



                            <div class="panel-body">
                                <section class="content">
                                    <div class="error-page">

                                        <div class="">
                                            <h3><i class="fa fa-warning text-red"></i> <?php
        echo lang('USER_DEL_main'); ?></h3>
                                            <p>
                                            <?php
        echo lang('USER_DEL_info'); ?>
                                            </p>

                                        </div>
                                        </div><!-- /.error-page -->
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    if ($user_status != "2") {
?>
        <!-- row -->
        <div class="row">


            <div class="col-md-3">
                <div class="box box-warning">
                    <div class="box-header">
                        <h4 style="text-align:center;"><?php
        echo $user_fio; ?><br><small><?php
        echo $user_posada; ?></small></h4>
                    </div>
                    <div class="box-body">

                        <center>
                        <img  src="<?php
        echo $user_img; ?>" class="img-rounded img-responsive" alt="User Image">
                        </center>

                        <?php
        if ($user_last_time) { ?>
                        <center>
                        <small><?php
            echo lang('stats_last_time'); ?> <br><i class="fa fa-clock-o"></i> <time id="c" datetime="<?php
            echo $user_last_time
?>"></time></small>
                        </center>
                        <?php
        }
        
        if ($user_id != $_SESSION['helpdesk_user_id']) {
?>

                        <br> <a href="messages?to=<?php
            echo $uniq_id; ?>" class="btn btn-primary btn-block btn-xs"><i class="fa fa-comments"></i> <?php
            echo lang('EXT_do_write_message'); ?></a>
                        <?php
        } ?>




                        </div><!-- /.box-body -->
                    </div>






                </div>





                <div class="col-md-9">

                    <div class="row">

                        <div class="col-md-12"><div class="box box-solid">
                            <div class="box-header">
                                <h3 class="box-title"><i class="fa fa-user"></i> <?php
        echo lang('P_main'); ?></h3>
                                <div class="box-tools pull-right">
                                    <?php
        echo get_user_status($user_id); ?>

                                </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">



                                    <div class="panel-body">


                                        <div class="row">


                                            <div class="col-md-5">


                                                <div class="row">

                                                    <?php
        if (($user_adr) && ($user_unit)) { ?>
                                                    <div class="col-md-3"><small class="text-muted"><?php
            echo lang('APPROVE_adr'); ?>:</small></div>
                                                    <div class="col-md-9"> <small><?php
            echo $user_adr; ?></small>
                                                        <small><?php
            echo $user_unit; ?></small>
                                                    </div>
                                                    <?php
        } ?>
                                                    <?php
        if ($user_skype) { ?><div class="col-md-3"><small class="text-muted">Skype:</small></div>
                                                    <div class="col-md-9"><small><?php
            echo $user_skype; ?></small></div> <?php
        } ?>

                                                    <?php
        if ($user_tel) { ?><div class="col-md-3"><small class="text-muted"><?php
            echo lang('APPROVE_tel'); ?>:</small></div>
                                                    <div class="col-md-9"><small><?php
            echo $user_tel; ?></small></div><?php
        } ?>

                                                    <?php
        if ($user_mail) { ?>
                                                    <div class="col-md-3"><small class="text-muted"><?php
            echo lang('APPROVE_mail'); ?>:</small></div>
                                                    <div class="col-md-9"><small><?php
            echo $user_mail; ?></small></div><?php
        } ?>

                                                    <?php
        $stmtf = $dbConnection->prepare('SELECT user_data.field_val as udf, user_data.field_name as udfn from user_data,user_fields where user_data.field_id=user_fields.id and user_data.user_id=:uid and user_fields.for_client=1 and user_fields.status=1');
        $stmtf->execute(array(
            ':uid' => $user_id
        ));
        $resf = $stmtf->fetchAll();
        
        if (!empty($resf)) {
?>
                                                    <br><br><br>
                                                    <center>
                                                    <?php
            echo lang('FIELD_add_title'); ?></center>
                                                    <table class="table  ">
                                                        <tbody>
                                                            <?php
            foreach ($resf as $fv) {
?>
                                                            <tr>
                                                                <td style=" width: 30px; "><small><?php
                echo $fv['udfn']; ?>:</small></td>
                                                                <td><small><?php
                echo $fv['udf']
?></small></td>
                                                            </tr>
                                                            <?php
            }
?>
                                                        </tbody>
                                                    </table>
                                                    <?php
        }
?>
                                                    <?php
        if (get_user_val_by_id($_SESSION['helpdesk_user_id'], 'priv') <> "1") {
            $stmtf = $dbConnection->prepare('SELECT user_data.field_val as udf, user_data.field_name as udfn from user_data,user_fields where user_data.field_id=user_fields.id and user_data.user_id=:uid and user_fields.for_client=0 and user_fields.status=1');
            $stmtf->execute(array(
                ':uid' => $user_id
            ));
            $resf = $stmtf->fetchAll();
            
            if (!empty($resf)) {
?>
                                                    <br>
                                                    <table class="table  ">
                                                        <tbody>
                                                            <?php
                foreach ($resf as $fv) {
?>
                                                            <tr>
                                                                <td style=" width: 30px; "><small><?php
                    echo $fv['udfn']; ?>:</small></td>
                                                                <td><small><?php
                    echo $fv['udf']
?></small></td>
                                                            </tr>
                                                            <?php
                }
?>
                                                        </tbody>
                                                    </table>
                                                    <?php
            }
        }
?>

                                                </div>









                                            </div>
                                            <div class="col-md-7">


                                                <div class="row">
                                                    <div class="col-xs-4 text-center"
                                                        <?php
        if ($is_client == "0") { ?>
                                                        style="border-right: 1px solid #f4f4f4"
                                                        <?php
        } ?>
                                                        >
                                                        <input type="text" class="knob" data-readonly="true" value="<?php
        echo get_total_tickets_out($user_id); ?>" data-width="100" data-height="100" data-max="<?php
        echo (get_total_tickets_count()); ?>" data-fgColor="#39CCCC"/>
                                                        <div class="knob-label"><?php
        echo lang('EXT_t_created'); ?></div>
                                                        </div><!-- ./col -->



                                                        <?php
        if ($is_client == "0") { ?>

                                                        <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                                            <input type="text" class="knob" data-readonly="true" value="<?php
            echo get_total_tickets_lock($user_id); ?>" data-width="100" data-height="100" data-max="50" data-max="<?php
            echo (get_total_tickets_count()); ?>" data-fgColor="#F4C01B"/>
                                                            <div class="knob-label"><?php
            echo lang('EXT_t_locked'); ?></div>
                                                            </div><!-- ./col -->
                                                            <div class="col-xs-4 text-center">
                                                                <input type="text" class="knob" data-readonly="true" value="<?php
            echo get_total_tickets_ok($user_id); ?>" data-width="100" data-height="100" data-max="50" data-max="<?php
            echo (get_total_tickets_count()); ?>" data-fgColor="#39CC57"/>
                                                                <div class="knob-label"><?php
            echo lang('EXT_t_oked'); ?></div>
                                                                </div><!-- ./col -->

                                                                <?php
        } ?>



                                                            </div>

                                                        </div>



                                                    </div>


                                                </div>


                                                </div><!-- /.box-body -->















                                                <div class="col-md-12">
                                                    <?php
        if (check_admin_user_priv($user_id)) { ?>
                                                    <div class="row">
<br>
                                                        <div class="col-md-6">


                                                            <div class="box" style="min-height: 10px; max-height: 400px; scroll-behavior: initial; overflow-y: scroll;">
                                                                <div class="box-header">
                                                                    <h3 class="box-title"><i class="fa fa-lock"></i> <?php
            echo lang('PROFILE_tickets_lock'); ?></h3>
                                                                    </div><!-- /.box-header -->

                                                                    <?php
            $stmt = $dbConnection->prepare('select id, subj, date_create, hash_name from tickets where status=0 and lock_by=:u order by id desc');
            $stmt->execute(array(
                ':u' => $user_id
            ));
            $result = $stmt->fetchAll();
            
            if (empty($result)) {
?>
                                                                    <div class="box-body ">
                                                                        <div id="" class="well well-large well-transparent lead">
                                                                            <center>
                                                                            <?php
                echo lang('MSG_no_records'); ?>
                                                                            </center>
                                                                        </div>
                                                                    </div>
                                                                    <?php
            } 
            else if (!empty($result)) {
?>
                                                                    <div class="box-body ">
                                                                        <table class="table table-condensed">
                                                                            <tbody><tr>
                                                                                <th style="width: 50px">#</th>
                                                                                <th><?php
                echo lang('NEW_subj'); ?></th>
                                                                                <th><?php
                echo lang('TICKET_t_date'); ?></th>
                                                                            </tr>
                                                                            <?php
                foreach ($result as $row) {
?>
                                                                            <tr>
                                                                                <td style="width: 50px"><small><a href="ticket?<?php
                    echo $row['hash_name'] ?>"><?php
                    echo $row['id']; ?></a></small></td>
                                                                                <td><small><?php
                    echo $row['subj']; ?></small></td>
                                                                                <td><small><time id="c" datetime="<?php
                    echo $row['date_create']; ?>"></time></small></td>
                                                                            </tr>
                                                                            <?php
                }
?>

                                                                        </tbody></table>
                                                                        </div><!-- /.box-body -->
                                                                        <?php
            }
?>

                                                                    </div>





                                                                </div>

                                                                <div class="col-md-6">


                                                                    <div class="box" style="min-height: 10px; max-height: 400px; scroll-behavior: initial; overflow-y: scroll;">
                                                                        <div class="box-header">
                                                                            <h3 class="box-title"><i class="fa fa-circle-o"></i> <?php
            echo lang('PROFILE_tickets_free'); ?></h3>
                                                                            </div><!-- /.box-header -->

                                                                            <?php
            $stmt = $dbConnection->prepare('select id, subj, date_create, hash_name from tickets where status=0 and lock_by=0 and (find_in_set(:u,user_to_id)) order by id desc');
            $stmt->execute(array(
                ':u' => $user_id
            ));
            $result = $stmt->fetchAll();
            
            if (empty($result)) {
?>
                                                                            <div class="box-body">
                                                                                <div id="" class="well well-large well-transparent lead">
                                                                                    <center>
                                                                                    <?php
                echo lang('MSG_no_records'); ?>
                                                                                    </center>
                                                                                </div>
                                                                            </div>
                                                                            <?php
            } 
            else if (!empty($result)) { ?>

                                                                            <div class="box-body">
                                                                                <table class="table table-condensed">
                                                                                    <tbody><tr>
                                                                                        <th style="width: 50px">#</th>
                                                                                        <th><?php
                echo lang('NEW_subj'); ?></th>
                                                                                        <th><?php
                echo lang('TICKET_t_date'); ?></th>
                                                                                    </tr>
                                                                                    <?php
                foreach ($result as $row) {
?>
                                                                                    <tr>
                                                                                        <td style="width: 50px"><small><a href="ticket?<?php
                    echo $row['hash_name'] ?>"><?php
                    echo $row['id']; ?></a></small></td>
                                                                                        <td><small><?php
                    echo $row['subj']; ?></small></td>
                                                                                        <td><small><time id="c" datetime="<?php
                    echo $row['date_create']; ?>"></time></small></td>
                                                                                    </tr>
                                                                                    <?php
                }
            }
?>

                                                                                </tbody></table>
                                                                                </div><!-- /.box-body -->
                                                                            </div>





                                                                        </div>
                                                                    </div>
                                                                    <?php
        } ?></div>




</div>





                                                            </div>








                                                        </div>
                                                    </div>


                                                    <?php
    }
?>


                                                </section>
                                                <?php


*/


    include ("footer.inc.php");
?>
                                                <?php
    
    //}
    
    
} 
else {
    include 'auth.php';
}
?>