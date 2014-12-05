<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        $timezones = array('Pacific/Midway' => "(GMT-11:00) Midway Island", 'US/Samoa' => "(GMT-11:00) Samoa", 'US/Hawaii' => "(GMT-10:00) Hawaii", 'US/Alaska' => "(GMT-09:00) Alaska", 'US/Pacific' => "(GMT-08:00) Pacific Time (US &amp; Canada)", 'America/Tijuana' => "(GMT-08:00) Tijuana", 'US/Arizona' => "(GMT-07:00) Arizona", 'US/Mountain' => "(GMT-07:00) Mountain Time (US &amp; Canada)", 'America/Chihuahua' => "(GMT-07:00) Chihuahua", 'America/Mazatlan' => "(GMT-07:00) Mazatlan", 'America/Mexico_City' => "(GMT-06:00) Mexico City", 'America/Monterrey' => "(GMT-06:00) Monterrey", 'Canada/Saskatchewan' => "(GMT-06:00) Saskatchewan", 'US/Central' => "(GMT-06:00) Central Time (US &amp; Canada)", 'US/Eastern' => "(GMT-05:00) Eastern Time (US &amp; Canada)", 'US/East-Indiana' => "(GMT-05:00) Indiana (East)", 'America/Bogota' => "(GMT-05:00) Bogota", 'America/Lima' => "(GMT-05:00) Lima", 'America/Caracas' => "(GMT-04:30) Caracas", 'Canada/Atlantic' => "(GMT-04:00) Atlantic Time (Canada)", 'America/La_Paz' => "(GMT-04:00) La Paz", 'America/Santiago' => "(GMT-04:00) Santiago", 'Canada/Newfoundland' => "(GMT-03:30) Newfoundland", 'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires", 'Greenland' => "(GMT-03:00) Greenland", 'Atlantic/Stanley' => "(GMT-02:00) Stanley", 'Atlantic/Azores' => "(GMT-01:00) Azores", 'Atlantic/Cape_Verde' => "(GMT-01:00) Cape Verde Is.", 'Africa/Casablanca' => "(GMT) Casablanca", 'Europe/Dublin' => "(GMT) Dublin", 'Europe/Lisbon' => "(GMT) Lisbon", 'Europe/London' => "(GMT) London", 'Africa/Monrovia' => "(GMT) Monrovia", 'Europe/Amsterdam' => "(GMT+01:00) Amsterdam", 'Europe/Belgrade' => "(GMT+01:00) Belgrade", 'Europe/Berlin' => "(GMT+01:00) Berlin", 'Europe/Bratislava' => "(GMT+01:00) Bratislava", 'Europe/Brussels' => "(GMT+01:00) Brussels", 'Europe/Budapest' => "(GMT+01:00) Budapest", 'Europe/Copenhagen' => "(GMT+01:00) Copenhagen", 'Europe/Ljubljana' => "(GMT+01:00) Ljubljana", 'Europe/Madrid' => "(GMT+01:00) Madrid", 'Europe/Paris' => "(GMT+01:00) Paris", 'Europe/Prague' => "(GMT+01:00) Prague", 'Europe/Rome' => "(GMT+01:00) Rome", 'Europe/Sarajevo' => "(GMT+01:00) Sarajevo", 'Europe/Skopje' => "(GMT+01:00) Skopje", 'Europe/Stockholm' => "(GMT+01:00) Stockholm", 'Europe/Vienna' => "(GMT+01:00) Vienna", 'Europe/Warsaw' => "(GMT+01:00) Warsaw", 'Europe/Zagreb' => "(GMT+01:00) Zagreb", 'Europe/Athens' => "(GMT+02:00) Athens", 'Europe/Bucharest' => "(GMT+02:00) Bucharest", 'Africa/Cairo' => "(GMT+02:00) Cairo", 'Africa/Harare' => "(GMT+02:00) Harare", 'Europe/Helsinki' => "(GMT+02:00) Helsinki", 'Europe/Istanbul' => "(GMT+02:00) Istanbul", 'Asia/Jerusalem' => "(GMT+02:00) Jerusalem", 'Europe/Kiev' => "(GMT+02:00) Kyiv", 'Europe/Minsk' => "(GMT+02:00) Minsk", 'Europe/Riga' => "(GMT+02:00) Riga", 'Europe/Sofia' => "(GMT+02:00) Sofia", 'Europe/Tallinn' => "(GMT+02:00) Tallinn", 'Europe/Vilnius' => "(GMT+02:00) Vilnius", 'Asia/Baghdad' => "(GMT+03:00) Baghdad", 'Asia/Kuwait' => "(GMT+03:00) Kuwait", 'Africa/Nairobi' => "(GMT+03:00) Nairobi", 'Asia/Riyadh' => "(GMT+03:00) Riyadh", 'Asia/Tehran' => "(GMT+03:30) Tehran", 'Europe/Moscow' => "(GMT+04:00) Moscow", 'Asia/Baku' => "(GMT+04:00) Baku", 'Europe/Volgograd' => "(GMT+04:00) Volgograd", 'Asia/Muscat' => "(GMT+04:00) Muscat", 'Asia/Tbilisi' => "(GMT+04:00) Tbilisi", 'Asia/Yerevan' => "(GMT+04:00) Yerevan", 'Asia/Kabul' => "(GMT+04:30) Kabul", 'Asia/Karachi' => "(GMT+05:00) Karachi", 'Asia/Tashkent' => "(GMT+05:00) Tashkent", 'Asia/Kolkata' => "(GMT+05:30) Kolkata", 'Asia/Kathmandu' => "(GMT+05:45) Kathmandu", 'Asia/Yekaterinburg' => "(GMT+06:00) Ekaterinburg", 'Asia/Almaty' => "(GMT+06:00) Almaty", 'Asia/Dhaka' => "(GMT+06:00) Dhaka", 'Asia/Novosibirsk' => "(GMT+07:00) Novosibirsk", 'Asia/Bangkok' => "(GMT+07:00) Bangkok", 'Asia/Jakarta' => "(GMT+07:00) Jakarta", 'Asia/Krasnoyarsk' => "(GMT+08:00) Krasnoyarsk", 'Asia/Chongqing' => "(GMT+08:00) Chongqing", 'Asia/Hong_Kong' => "(GMT+08:00) Hong Kong", 'Asia/Kuala_Lumpur' => "(GMT+08:00) Kuala Lumpur", 'Australia/Perth' => "(GMT+08:00) Perth", 'Asia/Singapore' => "(GMT+08:00) Singapore", 'Asia/Taipei' => "(GMT+08:00) Taipei", 'Asia/Ulaanbaatar' => "(GMT+08:00) Ulaan Bataar", 'Asia/Urumqi' => "(GMT+08:00) Urumqi", 'Asia/Irkutsk' => "(GMT+09:00) Irkutsk", 'Asia/Seoul' => "(GMT+09:00) Seoul", 'Asia/Tokyo' => "(GMT+09:00) Tokyo", 'Australia/Adelaide' => "(GMT+09:30) Adelaide", 'Australia/Darwin' => "(GMT+09:30) Darwin", 'Asia/Yakutsk' => "(GMT+10:00) Yakutsk", 'Australia/Brisbane' => "(GMT+10:00) Brisbane", 'Australia/Canberra' => "(GMT+10:00) Canberra", 'Pacific/Guam' => "(GMT+10:00) Guam", 'Australia/Hobart' => "(GMT+10:00) Hobart", 'Australia/Melbourne' => "(GMT+10:00) Melbourne", 'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby", 'Australia/Sydney' => "(GMT+10:00) Sydney", 'Asia/Vladivostok' => "(GMT+11:00) Vladivostok", 'Asia/Magadan' => "(GMT+12:00) Magadan", 'Pacific/Auckland' => "(GMT+12:00) Auckland", 'Pacific/Fiji' => "(GMT+12:00) Fiji",);
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-cog"></i>  <?php echo lang('CONF_title'); ?>
                        <small><?php echo lang('CONF_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('CONF_title'); ?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">


<div class="col-md-3">

<div class="callout callout-info">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php echo lang('CONF_info'); ?>
       </small>
                                    </div>





<div class="box box-solid bg-blue">
                                <div class="box-header">
                                    <h3 class="box-title">ZENLIX v.<?php echo get_conf_param('version'); ?></h3>
                                </div>
                                <div class="box-body">
                                Coding by ZENLIX (c) 2014
                                   <p>
      <i class="fa fa-envelope"></i> support@zenlix.com
      </p>
      <hr>
      <button id="check_update" class="btn btn-default btn-block btn-sm">Check updates</button>
      <div id="result_update"></div>
                                </div><!-- /.box-body -->
                            </div>

<div class="box box-solid">
                                <div class="box-header">
                                    <h3 class="box-title">CRON</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
 <p class="help-block"><small><?php echo lang('CONF_2arch_info'); ?> <br>
<pre>5 0 * * * /usr/bin/php5 -f <?php echo realpath(dirname(dirname(__FILE__))) . "/sys/4cron.php" ?> > <?php echo realpath(dirname(dirname(__FILE__))) . "/4cron.log" ?> 2>&1</pre></small></p>

<p class="help-block"><small><?php echo lang('CONF_2noty_info'); ?> <br>
      <pre>* * * * * /usr/bin/php5 -f <?php echo realpath(dirname(dirname(__FILE__))) . "/sys/4cron_notify.php" ?> > <?php echo realpath(dirname(dirname(__FILE__))) . "/4cron.log" ?> 2>&1</pre></small></p> 


                                </div><!-- /.box-body -->
                            </div>



                                    
                                    
                                    
                                    
                                    
</div>
<div class="col-md-9">
<div class="row">
<div class="col-md-12">

<div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-cog"></i> <?php echo lang('CONF_mains'); ?></h3>
</div>
      <div class="box-body">
          <form class="form-horizontal" role="form">
    <div class="form-group">
    <label for="name_of_firm" class="col-sm-4 control-label"><small><?php echo lang('CONF_name'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="name_of_firm" placeholder="<?php echo lang('CONF_name'); ?>" value="<?php echo get_conf_param('name_of_firm'); ?>">
    </div>
  </div>  
  
    <div class="form-group">
    <label for="mail" class="col-sm-4 control-label"><small><?php echo lang('CONF_mail'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="mail" placeholder="<?php echo lang('CONF_mail'); ?>" value="<?php echo get_conf_param('mail'); ?>">
    </div>
  </div>
  
  <div class="form-group">
    <label for="ldap_ip" class="col-sm-4 control-label"><small><?php echo lang('EXT_ldap_ip'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="ldap_ip" placeholder="<?php echo lang('EXT_ldap_ip'); ?>" value="<?php echo get_conf_param('ldap_ip') ?>">
    </div>
  </div>
    <div class="form-group">
    <label for="ldap_domain" class="col-sm-4 control-label"><small><?php echo lang('EXT_ldap_domain'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="ldap_domain" placeholder="<?php echo lang('EXT_ldap_domain'); ?>" value="<?php echo get_conf_param('ldap_domain') ?>">
    </div>
  </div>
  
      <div class="form-group">
    <label for="node_port" class="col-sm-4 control-label"><small>NodeJS port</small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="node_port" placeholder="8080" value="<?php echo get_conf_param('node_port') ?>">
    </div>
  </div>
  
  
  
  <div class="form-group">
    <label for="title_header" class="col-sm-4 control-label"><small><?php echo lang('CONF_title_org'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="title_header" placeholder="<?php echo lang('CONF_title_org'); ?>" value="<?php echo get_conf_param('title_header'); ?>">
    </div>
  </div>
  <div class="form-group">
    <label for="hostname" class="col-sm-4 control-label"><small><?php echo lang('CONF_url'); ?></small></label>
    <div class="col-sm-8">
    <div class="input-group">
    <span class="input-group-addon"><small><?php echo site_proto(); ?></small></span>
      <input type="text" class="form-control input-sm" id="hostname" placeholder="<?php
        $pos = strrpos($_SERVER['REQUEST_URI'], '/');
        echo "http://" . $_SERVER['HTTP_HOST'] . substr($_SERVER['REQUEST_URI'], 0, $pos + 1); ?>" value="<?php echo get_conf_param('hostname'); ?>">
    </div>
    </div>
  </div>



<div class="form-group">
    <label for="time_zone" class="col-sm-4 control-label"><small><?php echo lang('CONF_timezone'); ?></small></label>
    <div class="col-sm-8">
     


<select class="form-control input-sm" id="time_zone">
  <?php
        foreach ($timezones as $key => $value) {
?>
    <option value="<?php echo $key; ?>" <?php
            if (get_conf_param('time_zone') == $key) {
                echo "selected";
            } ?> ><?php echo $value; ?></option>
    <?php
        } ?>
  
</select> 





    </div>
  </div>


  
    <div class="form-group">
    <label for="days2arch" class="col-sm-4 control-label"><small><?php echo lang('CONF_2arch'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="days2arch" placeholder="<?php echo lang('CONF_2arch'); ?>" value="<?php echo get_conf_param('days2arch'); ?>">
     
    </div>
  </div>
  

  

  
  
        <div class="form-group">
    <label for="fix_subj" class="col-sm-4 control-label"><small><?php echo lang('CONF_subj'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="fix_subj">
  <option value="true" <?php
        if (get_conf_param('fix_subj') == "true") {
            echo "selected";
        } ?>><?php echo lang('CONF_fix_list'); ?></option>
  <option value="false" <?php
        if (get_conf_param('fix_subj') == "false") {
            echo "selected";
        } ?>><?php echo lang('CONF_subj_text'); ?></option>
</select>    
<p class="help-block"><small>
<?php echo lang('CONF_subj_info'); ?>
</small></p>
</div>
  </div>
  
  
  
  
  <div class="form-group">
    <label for="allow_register" class="col-sm-4 control-label"><small><?=lang('REG_new');?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="allow_register">
  <option value="true" <?php
        if (get_conf_param('allow_register') == "true") {
            echo "selected";
        } ?>><?php echo lang('CONF_true'); ?></option>
  <option value="false" <?php
        if (get_conf_param('allow_register') == "false") {
            echo "selected";
        } ?>><?php echo lang('CONF_false'); ?></option>
</select>    
</div>
  </div>





  
  
  
  
          <div class="form-group">
    <label for="file_uploads" class="col-sm-4 control-label"><small><?php echo lang('CONF_fup'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="file_uploads">
  <option value="true" <?php
        if (get_conf_param('file_uploads') == "true") {
            echo "selected";
        } ?>><?php echo lang('CONF_true'); ?></option>
  <option value="false" <?php
        if (get_conf_param('file_uploads') == "false") {
            echo "selected";
        } ?>><?php echo lang('CONF_false'); ?></option>
</select>    
<p class="help-block"><small>
<?php echo lang('CONF_fup_info'); ?>
</small></p>
</div>
  </div>
  
  
  
  <div class="form-group">
    <label for="file_types" class="col-sm-4 control-label"><small><?php echo lang('CONF_file_types'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="file_types" placeholder="gif,jpe?g,png,doc,xls,rtf,pdf,zip,rar,bmp,docx,xlsx" value="<?php
        $bodytag = str_replace("|", ",", get_conf_param('file_types'));
        echo $bodytag;
?>">

    </div>
  </div>
  
    <div class="form-group">
    <label for="file_size" class="col-sm-4 control-label"><small><?php echo lang('CONF_file_size'); ?></small></label>
    <div class="col-sm-8">
    <div class="input-group">
      <input type="text" class="form-control input-sm" id="file_size" placeholder="5" value="<?php echo round(get_conf_param('file_size') / 1024 / 1024); ?>">
<span class="input-group-addon">Mb</span>
    </div>
    </div>
  </div>
  
  
  
<center>
    <button type="submit" id="conf_edit_main" class="btn btn-success"><i class="fa fa-pencil"></i> <?php echo lang('CONF_act_edit'); ?></button>
    
</center>


  
    </form>
  
  <div id="conf_edit_main_res"></div>
      
      </div>
</div>





</div>



<div class="col-md-12">
<div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-bell"></i> <?php echo lang('CONF_mail_name'); ?></h3>
</div>
      <div class="box-body">
      <form class="form-horizontal" role="form">
    
    <div class="form-group">
    <label for="mail_active" class="col-sm-4 control-label"><small><?php echo lang('CONF_mail_status'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="mail_active">
  <option value="true" <?php
        if (get_conf_param('mail_active') == "true") {
            echo "selected";
        } ?>><?php echo lang('CONF_true'); ?></option>
  <option value="false" <?php
        if (get_conf_param('mail_active') == "false") {
            echo "selected";
        } ?>><?php echo lang('CONF_false'); ?></option>
</select>   </div>
  </div>
  
  <div class="form-group">
    <label for="from" class="col-sm-4 control-label"><small><?php echo lang('CONF_mail_from'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="from" placeholder="<?php echo lang('CONF_mail_from'); ?>" value="<?php echo get_conf_param('mail_from') ?>">
    </div>
  </div>
      <div class="form-group">
    <label for="mail_type" class="col-sm-4 control-label"><small><?php echo lang('CONF_mail_type'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="mail_type">
  <option value="sendmail" <?php
        if (get_conf_param('mail_type') == "sendmail") {
            echo "selected";
        } ?>>sendmail</option>
  <option value="SMTP" <?php
        if (get_conf_param('mail_type') == "SMTP") {
            echo "selected";
        } ?>>SMTP</option>
</select>    </div>
  </div>
  
  <div id="smtp_div">

    <div class="form-group">
    <label for="host" class="col-sm-4 control-label"><small><?php echo lang('CONF_mail_host'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="host" placeholder="<?php echo lang('CONF_mail_host'); ?>" value="<?php echo get_conf_param('mail_host') ?>">
    </div>
  </div>

    <div class="form-group">
    <label for="port" class="col-sm-4 control-label"><small><?php echo lang('CONF_mail_port'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="port" placeholder="<?php echo lang('CONF_mail_port'); ?>" value="<?php echo get_conf_param('mail_port') ?>">
    </div>
  </div>
  
  <div class="form-group">
    <label for="auth" class="col-sm-4 control-label"><small><?php echo lang('CONF_mail_auth'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="auth">
  <option value="true" <?php
        if (get_conf_param('mail_auth') == "true") {
            echo "selected";
        } ?>><?php echo lang('CONF_true'); ?></option>
  <option value="false" <?php
        if (get_conf_param('mail_auth') == "false") {
            echo "selected";
        } ?>><?php echo lang('CONF_false'); ?></option>
</select>    </div>
  </div>
  
  <div class="form-group">
    <label for="auth_type" class="col-sm-4 control-label"><small><?php echo lang('CONF_mail_type'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="auth_type">
  <option value="none" <?php
        if (get_conf_param('mail_auth_type') == "none") {
            echo "selected";
        } ?>>no</option>
  <option value="ssl" <?php
        if (get_conf_param('mail_auth_type') == "ssl") {
            echo "selected";
        } ?>>SSL</option>
  <option value="tls" <?php
        if (get_conf_param('mail_auth_type') == "tls") {
            echo "selected";
        } ?>>TLS</option>
</select>    </div>
  </div>
  
      <div class="form-group">
    <label for="username" class="col-sm-4 control-label"><small><?php echo lang('CONF_mail_login'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="username" placeholder="<?php echo lang('CONF_mail_login'); ?>" value="<?php echo get_conf_param('mail_username') ?>">
    </div>
  </div>
  
      <div class="form-group">
    <label for="password" class="col-sm-4 control-label"><small><?php echo lang('CONF_mail_pass'); ?></small></label>
    <div class="col-sm-8">
      <input type="password" class="form-control input-sm" id="password" placeholder="<?php echo lang('CONF_mail_pass'); ?>" value="<?php echo get_conf_param('mail_password') ?>">
    </div>
  </div>
  
  </div>
  

  <button type="submit" id="conf_test_mail" class="btn btn-default btn-sm pull-right"> test</button>
<center>
    <button type="submit" id="conf_edit_mail" class="btn btn-success"><i class="fa fa-pencil"></i> <?php echo lang('CONF_act_edit'); ?></button>
</center>


      <div class="" id="conf_edit_mail_res"></div>
      <div class="" id="conf_test_mail_res"></div>
    </form>
    
      </div>
</div>
</div>



<div class="col-md-12">
  <div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-bell"></i> <?php echo lang('EXT_pb_noti'); ?></h3>
</div>
      <div class="box-body">
      <form class="form-horizontal" role="form">
   
  
  <div class="form-group">
    <label for="from" class="col-sm-4 control-label"><small><?php echo lang('EXT_pb_api_key'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="pb_api" placeholder="<?php echo lang('EXT_pb_api_key'); ?>" value="<?php echo get_conf_param('pb_api') ?>">
    </div>
  </div>
<div class="">
<center>
    <button type="submit" id="conf_edit_pb" class="btn btn-success"><i class="fa fa-pencil"></i> <?php echo lang('CONF_act_edit'); ?></button>
<div class="" id="conf_edit_pb_res"></div>
</center>
</div>
  </form>
      
      </div>
  </div>
  
  
</div>


</div></div>
</div></section>


<?php
        include ("footer.inc.php");
?>

<?php
    }
} else {
    include '../auth.php';
}
?>