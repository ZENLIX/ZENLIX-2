                <section class="content-header">
                    <h1>
                    
                        <i class="fa fa-tachometer"></i> <?php echo lang('DASHBOARD_TITLE'); ?>
                        <small><?php echo lang('DASHBOARD_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('DASHBOARD_TITLE'); ?></li>
                    </ol>
                </section>



<section class="content">
    
    
    <div class="row">
    <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3 id="tool_1">
                                        <?php echo get_total_tickets_free(); ?>
                                    </h3>
                                    <p>
                                        <?php echo lang('DASHBOARD_ticket_in'); ?>  
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-download"></i>
                                </div>
                                <a href="list?in" class="small-box-footer">
                                   <?php echo lang('EXT_more_info'); ?> <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3 id="tool_2">
                                        <?php echo get_total_tickets_lock(); ?>
                                    </h3>
                                    <p>
                                        <?php echo lang('DASHBOARD_ticket_lock'); ?>
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-lock"></i>
                                </div>
                                <a href="list?in" class="small-box-footer">
                                    <?php echo lang('EXT_more_info'); ?> <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3 id="tool_3">
                                        <?php echo get_total_tickets_out_and_success(); ?>
                                    </h3>
                                    <p>
                                        <?php echo lang('DASHBOARD_ticket_out'); ?>
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-upload"></i>
                                </div>
                                <a href="list?out" class="small-box-footer">
                                    <?php echo lang('EXT_more_info'); ?> <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        
                                                <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3 id="tool_4">
                                        <?php echo get_total_tickets_ok(); ?>
                                    </h3>
                                    <p>
                                        <?php echo lang('LIST_ok_t'); ?>
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-check"></i>
                                </div>
                                <a href="list?in" class="small-box-footer">
                                    <?php echo lang('EXT_more_info'); ?> <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        
                        

    </div>
    
    
    
    <div class="row">
    <div class="col-md-6">
    <div class="box">
    <div class="box-header"><h3 class="box-title"><a href="helper"><i class="fa fa-globe"></i> <?php echo lang('DASHBOARD_last_help'); ?></a></h3></div>
    <div class="box-body">
    
    <?php
get_helper(); ?>
    
    </div>
    
    </div>
    </div>
    
    <div class="col-md-6">
    <div class="box">
    <div class="box-header"><h3 class="box-title"><i class="fa fa-exclamation-circle"></i> <?php echo lang('DASHBOARD_messages'); ?></h3></div>
    <div class="box-body">
    <?php
    if (get_user_val('messages_type') == "0") {$style_msg="info";}
    else if (get_user_val('messages_type') == "1") {$style_msg="warning";}
    else if (get_user_val('messages_type') == "2") {$style_msg="danger";}
    ?>
     <div class="callout callout-<?=$style_msg;?>">
                                        <h4><?php echo get_user_val('messages_title'); ?></h4>
                                        <p><?php echo get_user_val('messages'); ?></p>
                                    </div>
    
    </div>
    
    </div>
    </div>
    
   
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
    </div>
    

    
  <div class="row">
  
  
  <div class="col-md-12">
                            <div class="box">
                                
                                
                                
                                <div class="box-header">
                                    <h3 class="box-title"><a href="list?in"><i class="fa fa-list-alt"></i> <?php echo lang('DASHBOARD_last_in'); ?></a></h3>
                                    <div class="box-tools">
                                        <div class="btn-group btn-group-xs pull-right">
  <button id="dashboard_set_ticket" type="button" class="btn btn-default">5</button>
  <button id="dashboard_set_ticket" type="button" class="btn btn-default">10</button>
  <button id="dashboard_set_ticket" type="button" class="btn btn-default">15</button>
</div>

                                    </div>
                                </div>
                                
                                
                                
                                
                                
                                <div class="box-body">
                                    
                                    
                                    
                                    
                                    <div id="spinner" class="well well-large well-transparent lead">
                        <center><i class="fa fa-spinner fa-spin icon-2x"></i> <?php echo lang('LIST_loading'); ?> ...</center>
                    </div>

                    <div id="dashboard_t"></div>
                                    
                                    
                                    
                                    
                                                                    </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                        
                        
                        
                        
  
    </div>
    
    

       
</div>
</section>

