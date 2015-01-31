<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
?>
    


<section class="content-header">
                    <h1>
                        <i class="fa fa-bar-chart-o"></i>  <?php echo lang('STATS_TITLE'); ?>
                        <small><?php echo lang('STATS_TITLE_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('STATS_TITLE'); ?></li>
                    </ol>
                </section>
                
                
                <section class="content">

                    <!-- row -->
                    <div class="row">
                    
                    <div class="col-md-12">
                    
                    
                    
                    
                    <div class="box box-solid">
                                
                                <div class="box-body">
                                    <table class="table table-bordered">
<tr>
<td colspan="3" style="width:50%"><strong><center><?php echo lang('STATS_in'); ?></center></strong></td>
<td colspan="4"><strong><center><?php echo lang('STATS_out'); ?></center></strong></td>
</tr>
<tr>
<td><center><?php echo lang('STATS_new'); ?>            </center></td>
<td><center><?php echo lang('STATS_lock'); ?>  </center></td>
<td><center><?php echo lang('STATS_ok'); ?> </center></td>
<td><center><?php echo lang('STATS_nook'); ?>           </center></td>  
<td><center><?php echo lang('STATS_create'); ?> </center></td>
<td><center><?php echo lang('STATS_lock_o'); ?>     </center></td>
<td><center><?php echo lang('STATS_ok_o'); ?>       </center></td>
</tr>
<tr>
<td><center><span class="text-danger"> <h4><?php echo get_total_tickets_free(); ?>          </h4></span>    </center></td>
<td><center><span class="text-warning"><h4><?php echo get_total_tickets_lock(); ?>          </h4></span>    </center></td>
<td><center><span class="text-success"><h4><?php echo get_total_tickets_ok(); ?>                </h4></span></center></td>  
<td><center><span class="text-danger"> <h4><?php echo get_total_tickets_out_and_success(); ?></h4></span>   </center></td>
<td><center><span class="">            <h4><?php echo get_total_tickets_out(); ?>           </h4></span>    </center></td>
<td><center><span class="text-warning"><h4><?php echo get_total_tickets_out_and_lock(); ?>  </h4></span>    </center></td>
<td><center><span class="text-success"><h4><?php echo get_total_tickets_out_and_ok(); ?>        </h4></span></center></td>
</tr>
<tr>
<td colspan="3"><center><div class="col-md-12" id="chart_in">
</div></center></td>
<td colspan="4"><div class="col-md-12" id="chart_out">
</div></td></tr>
<tr>
<td colspan="3"><small>
<ul>
    <?php echo lang('STATS_help1'); ?>
</ul>
</small></td>
<td colspan="4">
    
    <small>
<ul>
    <?php echo lang('STATS_help2'); ?>
</ul>
</small>
    
</td>
</tr>
</table>
                                    
                                    
                                    
                                    
                                    
                                                                    </div><!-- /.box-body -->
                            </div>
                            
                            
                            
                            
                            
                            
                    
                    
                    
                    
                        
                    </div>
                    
                    </div>
                </section>
                
                
                
                




<?php
        include ("footer.inc.php");
?>

<script>
    $(function () {
    $('#chart_in').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: '<?php echo lang('STATS_in_now'); ?>'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y}</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.y}',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: '<?php echo lang('STATS_t'); ?>',
            data: [
                {
                    name: '<?php echo lang('STATS_lock'); ?>',
                    y: <?php echo get_total_tickets_lock(); ?>,
                    color: '#F0AD4E'
                },
                {
                    name: '<?php echo lang('STATS_t_ok'); ?>',
                    y: <?php echo get_total_tickets_ok(); ?>,
                    color: '#aaff99'
                },
                {
                    name: '<?php echo lang('STATS_t_free'); ?>',
                    y: <?php echo get_total_tickets_free(); ?>,
                    color: '#FF2D46'
                }
            ]
        }]
    });





$('#chart_out').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: '<?php echo lang('STATS_out_all'); ?>'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y}</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.y}',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: '<?php echo lang('STATS_t'); ?>',
            data: [
                {
                    name: '<?php echo lang('STATS_t_lock'); ?>',
                    y: <?php echo get_total_tickets_out_and_lock(); ?>,
                    color: '#F0AD4E'
                },
                {
                    name: '<?php echo lang('STATS_t_ok'); ?>',
                    y: <?php echo get_total_tickets_out_and_ok(); ?>,
                    color: '#aaff99'
                },
                {
                    name: '<?php echo lang('STATS_t_free'); ?>',
                    y: <?php echo get_total_tickets_out_and_success(); ?>,
                    color: '#FF2D46'
                }
            ]
        }]
    });

    });
</script>
<?php
    } else {
        include 'auth.php';
    }
}
?>