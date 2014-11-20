<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if (($_SESSION['helpdesk_user_id'])) {
?>
 
<section class="content-header">
                    <h1>
                        <i class="fa fa-comments"></i> <?php echo lang('MESSAGES_us'); ?>
                        <small><?php echo lang('MESSAGES_us_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('MESSAGES_us'); ?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">







<div class="col-md-3">
	<div class="row">
		
		<div class="col-md-12">
			<a id="select_main_chat" class="btn btn-block btn-default" ><?php echo lang('MESSAGES_main'); ?></a>
			<br>
		</div>
		
		<div class="col-md-12">
		
		<div class="box box-info">

                                <div class="box-body">
                                <?php
            if ($_GET['to']) {
                $t = $_GET['to'];
                $ufio = get_user_val_by_hash($t, 'fio');
            } else {
                $ufio = "";
            } ?>
                                
	                                <input class="form-control input-sm" id="find_user" type="text" placeholder="<?php echo lang('MESSAGES_fio'); ?>" value="<?php echo $ufio; ?>">
                                    <?php
            
            //список всех пользователей с которыми когда-либо общался
            $stmt = $dbConnection->prepare('SELECT id, user_from,user_to from messages where
	                    (user_to=:u_to)
	                    order by is_read , date_op ASC');
            $stmt->execute(array(':u_to' => $_SESSION['helpdesk_user_id']));
            
            $re = $stmt->fetchAll();
            if (!empty($re)) {
                $user_arr = array();
                foreach ($re as $rews) {
                    
                    array_push($user_arr, $rews['user_from']);
                    array_push($user_arr, $rews['user_to']);
                }
            }
            
            $user_arr = array_unique($user_arr);
            if (($key = array_search($_SESSION['helpdesk_user_id'], $user_arr)) !== false) {
                unset($user_arr[$key]);
            }
            if (($key = array_search('0', $user_arr)) !== false) {
                unset($user_arr[$key]);
            }
?>
                                    
                                            <div id="user_list" style="margin-top: 15px;">
                                                <ul class="nav nav-pills nav-stacked">
	                                                <?php
            
            foreach ($user_arr as $uniq_id) {
                
                $stmt = $dbConnection->prepare('SELECT count(id) as cou from messages where
        ((user_from=:ufrom and user_to=:uto)) and is_read=0
         ');
                $stmt->execute(array(':ufrom' => $uniq_id, ':uto' => $_SESSION['helpdesk_user_id']));
                
                $tt = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($tt['cou'] != 0) {
                    $tt = "<small id=\"ul_label_" . $uniq_id . "\"><small class=\"badge pull-right\">" . $tt['cou'] . "</small></small>";
                } else {
                    $tt = "<small id=\"ul_label_" . $uniq_id . "\"></small>";
                }
                
                if ($_GET['to']) {
                    $t = $_GET['to'];
                    $tuid = get_user_val_by_hash($t, 'id');
                    
                    if ($tuid == $uniq_id) {
?>
                                                    <li class="user_li active" user-id="<?php echo $uniq_id; ?>">
                                                    <a href="#">
	                                                    <img style="width: 25px;
height: 25px;" src="<?php echo get_user_img_by_id($uniq_id); ?>" class="img-circle" alt="User Image">
                                                     <?php echo nameshort(name_of_user_ret_nolink($uniq_id)); ?>
                                                     
                                                     <?php echo $tt; ?>
                                                     </a>
                                                     </li>
                                                    <?php
                    }
                } else {
?>
                                                    <li class="user_li" user-id="<?php echo $uniq_id; ?>">
                                                    <a href="#">
	                                                    <img style="width: 25px;height: 25px;" src="<?php echo get_user_img_by_id($uniq_id); ?>" class="img-circle" alt="User Image">
                                                     <?php echo nameshort(name_of_user_ret_nolink($uniq_id)); ?>
                                                     
                                                     <?php echo $tt; ?>
                                                     </a>
                                                     </li>
                                                    <?php
                }
            }
?>
                                                    
                                                    
                                                </ul>
                                            </div>    
                                    
                                    
                                                                    </div><!-- /.box-body -->
                            </div>
		
	</div>
		
	</div>
	
</div>
<div class="col-md-9">
		
		
		<div class="row">
			<div class="col-md-12"><div class="box box-solid box-default" style="margin-bottom: 2px; height:100%;">
                                <div class="box-header">
                                    <h3 id="title_chat" class="box-title">
                                    
                                    <?php
            if ($_GET['to']) {
                $t = $_GET['to'];
?>
                                   <?php echo lang('MESSAGES_with'); ?> <?php echo get_user_val_by_hash($t, 'fio'); ?>
									   
<?php
            } else { ?><?php echo lang('MESSAGES_main'); ?><?php
            } ?>
                                    
                                    </h3>

                                </div>
                                <div class="box-body" >
	                                

                                    <div class="box-body chat" id="content_chat" style=" min-height: 350px; max-height: 350px; scroll-behavior: initial; overflow-y: scroll;">
                                   <?php
            if ($_GET['to']) {
                $t = $_GET['to'];
                view_messages(get_user_val_by_hash($t, 'id'));
            } else {
                view_messages('main');
            } ?>
                                    </div>
                                    
                                                                   </div><!-- /.box-body -->	                                
                                    <div class="loading1 "></div>
	                                <div class="loading2 "></div>
                            </div></div>
			<div class="col-md-12"><div class="box box-solid">
                                
                                <div class="box-body">
	                                
	                                
                                   <div class="input-group" id="for_msg">
                                        <input name="msg" id="msg" class="form-control" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="top" data-content="<?php echo lang('MESSAGES_sel_text'); ?>" placeholder="<?php echo lang('MESSAGES_sel_text'); ?>">
                                        <div class="input-group-btn">
                                            <button value="" id="do_comment" class="btn btn-success"><i class="fa fa-comment"></i></button>
                                            
                                            <?php
            if ($_GET['to']) {
                $t = $_GET['to'];
?>
                                   <input type="hidden" id="target_user" value="<?php echo get_user_val_by_hash($t, 'id'); ?>">
                                   <input type="hidden" id="total_msgs_main" value="<?php echo get_total_msgs_main(); ?>">
									   
<?php
            } else { ?><input type="hidden" id="target_user" value="main">
<input type="hidden" id="total_msgs_main" value="<?php echo get_total_msgs_main(); ?>"><?php
            } ?>
                                            
                                            
                                            

                                            
                                            
                                            
                                        </div>
                                    </div>
                                    
                                                                   </div><!-- /.box-body -->
                            </div></div>
			
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
} else {
    include '../auth.php';
}
?>
