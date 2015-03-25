<?php
session_start();
include ("../functions.inc.php");

if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    //if (validate_admin($_SESSION['helpdesk_user_id'])) {
    include ("head.inc.php");
    include ("client.navbar.inc.php");
?>
   <section class="content-header">
                    <h1>
                        <i class="fa fa-bullhorn"></i> <?php echo lang('VIEWUSER_title'); ?>
                        <small><?php echo lang('VIEWUSER_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('VIEWUSER_title'); ?></li>
                    </ol>
                </section>
   
   
   <?php
    
    $rkeys = array_keys($_GET);
    $hn = $rkeys[1];
    $stmt = $dbConnection->prepare('SELECT 
                            id, fio, posada, unit_desc, usr_img, tel, skype, last_time, status,email, adr, is_client, uniq_id
                            from users
                            where uniq_id=:hn limit 1');
    
    $stmt->execute(array(':hn' => $hn));
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

            $user_status=$row['status'];
            
            if ($row['usr_img']) {
                $user_img = $CONF['hostname'] . '/upload_files/avatars/' . $row['usr_img'];
            } else if (!$row['usr_img']) {
                $user_img = $CONF['hostname'] . '/img/avatar5.png';
            }
        }
    } else {
?>
        <div class="well well-large well-transparent lead">
            <center><?php echo lang('TICKET_t_no'); ?></center>
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
                                <h4 style="text-align:center;"><?php echo $user_fio; ?></h4>
                                </div>
                                <div class="box-body">
                                  
                        <center>
                            <img  src="<?php echo $user_img; ?>" class="img-rounded" alt="User Image">
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
              <h3><i class="fa fa-warning text-red"></i> <?=lang('USER_DEL_main');?></h3>
              <p>
                <?=lang('USER_DEL_info');?>
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
                                <h4 style="text-align:center;"><?php echo $user_fio; ?><br><small><?php echo $user_posada; ?></small></h4>
                                </div>
                                <div class="box-body">
                                  
                        <center>
                            <img  src="<?php echo $user_img; ?>" class="img-rounded" alt="User Image">
                        </center>
                        


        
                           
                                    
                                    
                                </div><!-- /.box-body -->
                            </div>
                            
                            
                            
                            
                            
                            
                            </div>
                            
                            
                            
                            
                            
                            <div class="col-md-9">
                                
                                <div class="row">
                                    
                                    <div class="col-md-12"><div class="box box-solid">
                                <div class="box-header">
                                    <h3 class="box-title"><i class="fa fa-user"></i> <?php echo lang('P_main'); ?></h3>
                                    <div class="box-tools pull-right">
                                    <?php echo get_user_status($user_id); ?>
                                        
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    
                                    
     
      <div class="panel-body">
      
      
      <div class="row">
      
      
      <div class="col-md-5">
          
          
          <div class="row">
              

              
              <?php
    if ($user_mail) { ?>
                          <div class="col-md-3"><small class="text-muted"><?=lang('APPROVE_mail');?>:</small></div>
              <div class="col-md-9"><small><?php echo $user_mail; ?></small></div><?php
    } ?> 
              
              
          </div>
          
         
                         
                                
          
                            
                            
                            
          
      </div>
      <div class="col-md-7">
          
          
          
          
      </div>
      
      
      
      </div>
 
      
      </div>
      
      
                                </div><!-- /.box-body -->
                                
                                
                            </div></div>
                            
                            
                            
                                    
                                    
                                </div>
                                
                                
                            
                            
                            
                            
                            
                            
                            </div>
                    </div>
                    
                    
                     <?php 

}
                     ?>
                    
                    
                    
                    
                    </div>





<?php
    include ("footer.inc.php");
?>

<?php
    
    //}
    
} else {
    include 'auth.php';
}
?>
