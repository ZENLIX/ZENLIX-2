<?php
session_start();
include_once "../functions.inc.php";

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        if (isset($_GET['h'])) {
            
            $h = ($_GET['h']);
            
            $stmt = $dbConnection->prepare('select id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where hashname=:h');
            $stmt->execute(array(':h' => $h));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
?>

        <section class="content-header">
                    <h1>
                        <i class="fa fa-globe"></i> <?php echo lang('HELPER_title'); ?>
                        
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('HELPER_title'); ?></li>
                    </ol>
                </section>
                
                
                
            <section class="content">


<div class="row">
    <div class="col-md-1">
        <a id="go_back" class="btn btn-primary btn-sm btn-block"><i class="fa fa-reply"></i> <?php echo lang('HELPER_back'); ?></a>
    </div>
    
    
    <div class="col-md-11">
        <div class="box box-solid">
            <div class="box-body">
            <h3 style=" margin-top: 0px; "><?php echo make_html($fio['title']) ?></h3>
    <p><?php echo ($fio['message']) ?></p>
    <hr>
    
    <p class="text-right"><small class="text-muted"><?php echo lang('HELPER_pub'); ?>: <?php echo nameshort(name_of_user_ret($fio['user_init_id'])); ?></small><br><small class="text-muted"> <time id="c" datetime="<?php echo $fio['dt']; ?>"></time></small>
    <br><a id="print_t" class="btn btn-default btn-xs"> <i class="fa fa-print"></i> <?php echo lang('HELPER_print'); ?></a>
        </p>
            </div>
        </div>
    </div>
</div>
            </section>
    
    
    
    
    

    
    <?php
        } else if (!isset($_GET['h'])) {
?>

    <section class="content-header">
                    <h1>
                        <i class="fa fa-globe"></i> <?php echo lang('HELPER_title'); ?>
                        <small><?php echo lang('HELPER_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('HELPER_title'); ?></li>
                    </ol>
                </section>
                
                
                
            <section class="content">


<div class="row">
    
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-body"><div class="input-group">
                        <input type="text" class="form-control input-sm" id="find_helper" autofocus placeholder="<?php echo lang('HELPER_desc'); ?>">
      <span class="input-group-btn">
        <button id="" class="btn btn-default btn-sm" type="submit"><i class="fa fa-search"></i> <?php echo lang('HELPER_find'); ?></button>
      </span>
                    </div>
            </div>
        </div>
    </div>
</div>



                    <!-- row -->
                    <div class="row">
                    
                    
                    
                                        <div class="col-md-3">
                    
                    <button id="create_new_help" type="submit" class="btn btn-success btn-sm btn-block"><i class="fa fa-file-o"></i> <?php echo lang('HELPER_create'); ?></button>
                    <br>
                    <div class="callout callout-info">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php echo lang('HELPER_info'); ?>
         </small>
                                    </div>
                                    
                                    
                                    
                    
                    
                    
                    </div>
                    
                    <div class="col-md-9" id="help_content">
                    
                    </div>
                    
                    
                    
                    
                    
                    </div>
            </section>    
                
                
                
                
                


        


<?php
        }
        include ("footer.inc.php");
?>


<?php
    }
} else {
    include 'auth.php';
}
?>