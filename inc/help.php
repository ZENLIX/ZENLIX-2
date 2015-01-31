<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    //if (validate_admin($_SESSION['helpdesk_user_id'])) {
    include ("head.inc.php");
    include ("navbar.inc.php");
?>
    <section class="content-header">
                    <h1>
                        <i class="fa fa-life-ring"></i> <?php echo lang('HELP_title'); ?>
                        <small><?php echo lang('HELP_title'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('HELP_title'); ?></li>
                    </ol>
                </section>

    
    
    
    <section class="content">


<div class="row">
    
    
    
    
    
    <div class="">



        <div class="">
            <div class="col-md-offset-1 col-md-10">
                <div class="panel ">
                    <div class="panel-body">
                        <center>
                            <img src="img/rus.001.png" class="img-responsive img-thumbnail">
                        </center>
                    </div>
                </div>
            </div>
            <div class="col-md-offset-1 col-md-10">













                <ul class="nav nav-tabs">
                    <li class="active"><a href="#home" data-toggle="tab">1. <?php echo lang('HELP_new'); ?></a></li>
                    <li><a href="#profile" data-toggle="tab">2. <?php echo lang('HELP_review'); ?></a></li>
                    <li><a href="#messages" data-toggle="tab">3. <?php echo lang('HELP_edit_user'); ?></a></li>

                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="home"><div class="panel panel-default">
                            
                            <div class="panel-body">

                                <img src="img/75e07fbdbf9d19760d4f365b9a2fe2b6.gif" class="img-responsive img-thumbnail"><br>
                                <?php echo lang('HELP_new_text'); ?>

                            </div>

                        </div></div>
                    <div class="tab-pane" id="profile"><div class="panel panel-default">
                            
                            <div class="panel-body">








                                <img src="img/t.png" class="img-responsive img-thumbnail">
                                <br>
                                <?php echo lang('HELP_review_text'); ?>

                            </div>
                        </div></div>
                    <div class="tab-pane" id="messages"><div class="panel panel-default">
                            <div class="panel-heading"><?php echo lang('HELP_edit_user'); ?></div>
                            <div class="panel-body">
                                <?php echo lang('HELP_edit_user_text'); ?>
                            </div></div></div>
                </div>
            </div>




        </div>




        <br>



    </div>
</div>
    </section>
    <?php
    include ("inc/footer.inc.php");
?>

    <?php
    
    //}
    
} else {
    include 'auth.php';
}
?>
