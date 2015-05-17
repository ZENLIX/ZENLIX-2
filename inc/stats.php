<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        $basedir = dirname(dirname(__FILE__));
        
        ////////////
        try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir . '/inc/views');
            
            // инициализируем Twig
            if (get_conf_param('twig_cache') == "true") {
                $twig = new Twig_Environment($loader, array(
                    'cache' => $basedir . '/inc/cache',
                ));
            } 
            else {
                $twig = new Twig_Environment($loader);
            }
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('stats.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            $main_arr = array(
                'hostname' => $CONF['hostname'],
                'name_of_firm' => $CONF['name_of_firm'],
                'STATS_TITLE' => lang('STATS_TITLE') ,
                'STATS_TITLE_ext' => lang('STATS_TITLE_ext') ,
                'STATS_in' => lang('STATS_in') ,
                'STATS_lock' => lang('STATS_lock') ,
                'STATS_out' => lang('STATS_out') ,
                'STATS_new' => lang('STATS_new') ,
                'STATS_ok' => lang('STATS_ok') ,
                'STATS_nook' => lang('STATS_nook') ,
                'STATS_create' => lang('STATS_create') ,
                'STATS_lock_o' => lang('STATS_lock_o') ,
                'STATS_ok_o' => lang('STATS_ok_o') ,
                'get_total_tickets_free' => get_total_tickets_free() ,
                'get_total_tickets_lock' => get_total_tickets_lock() ,
                'get_total_tickets_ok' => get_total_tickets_ok() ,
                'get_total_tickets_out_and_success' => get_total_tickets_out_and_success() ,
                'get_total_tickets_out' => get_total_tickets_out() ,
                'get_total_tickets_out_and_lock' => get_total_tickets_out_and_lock() ,
                'get_total_tickets_out_and_ok' => get_total_tickets_out_and_ok() ,
                'STATS_help1' => lang('STATS_help1') ,
                'STATS_help2' => lang('STATS_help2')
            );
            
            $main_arr = array_merge($main_arr);
            
            echo $template->render($main_arr);
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
        
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
            text: '<?php
        echo lang('STATS_in_now'); ?>'
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
            name: '<?php
        echo lang('STATS_t'); ?>',
            data: [
                {
                    name: '<?php
        echo lang('STATS_lock'); ?>',
                    y: <?php
        echo get_total_tickets_lock(); ?>,
                    color: '#F0AD4E'
                },
                {
                    name: '<?php
        echo lang('STATS_t_ok'); ?>',
                    y: <?php
        echo get_total_tickets_ok(); ?>,
                    color: '#aaff99'
                },
                {
                    name: '<?php
        echo lang('STATS_t_free'); ?>',
                    y: <?php
        echo get_total_tickets_free(); ?>,
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
            text: '<?php
        echo lang('STATS_out_all'); ?>'
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
            name: '<?php
        echo lang('STATS_t'); ?>',
            data: [
                {
                    name: '<?php
        echo lang('STATS_t_lock'); ?>',
                    y: <?php
        echo get_total_tickets_out_and_lock(); ?>,
                    color: '#F0AD4E'
                },
                {
                    name: '<?php
        echo lang('STATS_t_ok'); ?>',
                    y: <?php
        echo get_total_tickets_out_and_ok(); ?>,
                    color: '#aaff99'
                },
                {
                    name: '<?php
        echo lang('STATS_t_free'); ?>',
                    y: <?php
        echo get_total_tickets_out_and_success(); ?>,
                    color: '#FF2D46'
                }
            ]
        }]
    });

    });
</script>
<?php
    } 
    else {
        include 'auth.php';
    }
}
?>