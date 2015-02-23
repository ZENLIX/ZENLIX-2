<?php
session_start();
include_once ("../functions.inc.php");
$CONF['title_header'] = lang('NEW_title') . " - " . $CONF['name_of_firm'];

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        //check_unlinked_file();
        //echo get_userlogin_byid($_SESSION['helpdesk_user_id']);
        
?>

                <section class="content-header">
                    <h1>
                        <i class="fa fa-tag"></i> <?php echo lang('NEW_title'); ?>
                        
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $CONF['hostname'] ?>"><span class="icon-svg"></span>  <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('NEW_title'); ?></li>
                    </ol>
                </section>
                
                
                <section class="content">

<div class="row">
            <div class="col-md-12">                      
                                
                            
                                   
                                   
                                                                   
                            
                            
                            
                            
<div class="" id="form_add">
<input type="hidden" id="main_last_new_ticket" value="<?php echo get_last_ticket_new($_SESSION['helpdesk_user_id']); ?>">


<div class="row" style="padding-bottom:20px;">


                            
                            
                            
                            
                            
                            
<div class="col-md-8" id="div_new">
<div class="box box-solid">
                                
                                <div class="box-body">


<?php
        if (isset($_GET['ok'])) {
            if (isset($_GET['h'])) {
                $h = $_GET['h'];
            }
?>
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong><i class="fa fa-check"></i> <?php echo lang('NEW_ok'); ?></strong> <?php echo lang('NEW_ok_1'); ?> <a class="alert-link" href="<?php echo $CONF['hostname'] ?>ticket?<?php echo $h; ?>"><?php echo lang('NEW_ok_2'); ?></a> <?php echo lang('NEW_ok_3'); ?>
        <a class="alert-link" href="<?php echo $CONF['hostname'] ?>print_ticket?<?php echo $h; ?>"target="_blank"> <?php echo lang('NEW_ok_4'); ?></a>.
    </div>
<?php
        }
?>
<div class="" style="padding:20px;">
<div class="">

<div class="form-horizontal" id="main_form" novalidate="" action="" method="post">


<div class="control-group">
    <div class="controls">
        <div class="form-group" id="for_fio">

            <label for="fio" class="col-sm-2 control-label"><small><?php echo lang('NEW_from'); ?>: </small></label>

            <div class="col-sm-10"data-toggle="tooltip" data-placement="right" title="<?php echo lang('NEW_from_desc'); ?>">

                <div class="input-group">
                <input  type="text" name="fio" class="form-control input-sm" id="fio" placeholder="<?php echo lang('NEW_fio'); ?>" autofocus data-toggle="popover" data-trigger="manual" data-html="true" data-placement="right" data-content="<small><?php echo lang('NEW_fio_desc'); ?></small>">
                <a id="select_init_user" param-hash="<?php echo get_user_val('uniq_id'); ?>" href='#' class="input-group-addon">Я</a>
                </div>


            </div>



        </div></div>

    <hr>
<div data-toggle="tooltip" data-placement="right" title="<?php echo lang('NEW_to_desc'); ?>">
    <div class="form-group" id="for_to" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right">
        <label for="to" class="col-md-2 control-label" ><small><?php echo lang('NEW_to'); ?>: </small></label>
        <div class="col-md-6">
            <select data-placeholder="<?php echo lang('NEW_to_unit'); ?>" class="chosen-select form-control" id="to" name="unit_id">
                <option value="0"></option>
                <?php
        $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
        $stmt->execute(array(':n' => '0', ':s' => '1'));
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row) {
            
            //echo($row['label']);
            $row['label'] = $row['label'];
            $row['value'] = (int)$row['value'];
?>

                            <option value="<?php echo $row['value'] ?>"><?php echo $row['label'] ?></option>

                        <?php
        }
?>

            </select>
        </div>




        <div class="col-md-4" style="" id="dsd" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_to_unit_desc'); ?></small>">
    
    
    <select data-placeholder="<?php echo lang('NEW_to_user'); ?>" id="users_do" name="unit_id" class="form-control input-sm" multiple>
        <option></option>


<?php
        
        /* $qstring = "SELECT fio as label, id as value FROM users where status='1' and login !='system' order by fio ASC;";
                $result = mysql_query($qstring);//query the database for entries containing the term
        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
        */
        
        $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=:n and login !=:system and is_client=0 order by fio ASC');
        $stmt->execute(array(':n' => '1', ':system' => 'system'));
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row) {
            
            //echo($row['label']);
            $row['label'] = $row['label'];
            $row['value'] = (int)$row['value'];
            
            if (get_user_status_text($row['value']) == "online") {
                $s = "online";
            } else if (get_user_status_text($row['value']) == "offline") {
                $s = "offline";
            }
?>
                    <option data-foo="<?php echo $s; ?>" value="<?php echo $row['value'] ?>"><?php echo nameshort($row['label']) ?> </option>

                <?php
        }
?>
    </select>
            

        </div>

    </div>
</div>



</div>




<div class="control-group" id="for_prio">
    <div class="controls">
        <div class="form-group">
            <label for="" class="col-sm-2 control-label"><small><?php echo lang('NEW_prio'); ?>: </small></label>
            <div class="col-sm-10" style=" padding-top: 5px; ">

                <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-xs" id="prio_low"><i id="lprio_low" class=""></i><?php echo lang('NEW_prio_low'); ?></button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info btn-xs active" id="prio_normal"><i id="lprio_norm" class="fa fa-check"></i> <?php echo lang('NEW_prio_norm'); ?></button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="right" title="<?php echo lang('NEW_prio_high_desc'); ?>" id="prio_high"><i id="lprio_high" class=""></i><?php echo lang('NEW_prio_high'); ?></button>
                    </div>
                </div>
            </div></div></div></div>
<?php
        
        /*
        
        
        
        
        
        */
        
        if ($CONF['fix_subj'] == "false") {
?>

<div class="control-group" id="for_subj">
        <div class="controls">
          <div class="form-group">
    <label for="subj" class="col-sm-2 control-label"><small><?php echo lang('NEW_subj'); ?>: </small></label>
    <div class="col-sm-10">
      <input type="text" class="form-control input-sm" name="subj" id="subj" placeholder="<?php echo lang('NEW_subj'); ?>" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_subj_msg'); ?></small>" >
    </div>
  </div></div></div>
<?php
        } else if ($CONF['fix_subj'] == "true") {
?>



<div class="control-group " >
    <div class="controls">
        <div class="form-group " id="for_subj" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_subj_msg'); ?></small>">
            <label for="subj" class="col-sm-2 control-label"><small><?php echo lang('NEW_subj'); ?>: </small></label>
            <div class="col-sm-10 " style="">
                <select data-placeholder="<?php echo lang('NEW_subj_det'); ?>" class="chosen-select form-control input-sm " id="subj" name="subj" multiple>
                    <option value="0"></option>
                    <?php
            
            /*$qstring = "SELECT name FROM subj order by name COLLATE utf8_unicode_ci ASC";
                    $result = mysql_query($qstring);//query the database for entries containing the term
            while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
            */
            
            $stmt = $dbConnection->prepare('SELECT name FROM subj order by name COLLATE utf8_unicode_ci ASC');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $row) {
?>

                        <option value="<?php echo $row['name'] ?>"><?php echo $row['name'] ?></option>

                    <?php
            }
?>

                </select>
            </div>
        </div>

    </div>
</div>


<?php
        } ?>







<div class="control-group">
    <div class="controls">
        <div class="form-group" id="for_msg">
            <label for="msg" class="col-sm-2 control-label"><small><?php echo lang('NEW_MSG'); ?>:</small></label>
            <div class="col-sm-10">
                <textarea data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_MSG_msg'); ?></small>" placeholder="<?php echo lang('NEW_MSG_ph'); ?>" class="form-control input-sm animated" name="msg" id="msg" rows="3" required="" data-validation-required-message="<?php echo lang('EXT_fill_msg'); ?>" aria-invalid="false"></textarea>
            </div>
        </div>
        <div class="help-block"></div></div></div>




            <!--######### INPUT FOR DATE-FINISH ############## -->

    <?php
        if (get_conf_param('ticket_last_time') == "true") { ?>

            
                      <div class="control-group" id="for_prio">
    <div class="controls">
        <div class="form-group">
            <label for="d_finish" class="col-sm-2 control-label"><small><?=lang('TICKET_deadline_text');?>: </small></label>

            <div class="col-sm-10" style=" padding-top: 5px; ">

<div class='input-group date' id='date_finish'>

                    <input id="d_finish" type='text' class="form-control input-sm" data-date-format="YYYY-MM-DD HH:mm:ss" value="<?php echo date("Y-m-d H:i:s"); ?>" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                
            </div>
            
        </div>
    </div>
    
    </div>  
            
            <?php }?>
     
            
            <!--######### INPUT FOR DATE-FINISH ############## -->








<?php
        if ($CONF['file_uploads'] == "true") { ?>

<div class="control-group">
    <div class="controls">
    <div class="form-group">
    
    <label for="" class="col-sm-2 control-label"><small><?php echo lang('TICKET_file_add'); ?>:</small></label>

    <div class="col-sm-10">

 <form id="fileupload" action="" method="POST" enctype="multipart/form-data">
        <div class="fileupload-buttonbar">
            <div class="">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button btn-xs">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span><?php echo lang('TICKET_file_upload') ?></span>
                    <input id="filer" type="file" name="files[]" multiple>
                </span>
                
                <!--button data-toggle="popover" data-html="true" data-trigger="manual" data-placement="top" data-content="<small><?php echo lang('upload_not_u') ?></small>" type="submit" class="btn btn-primary start btn-xs" id="start_upload">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span><?php echo lang('TICKET_file_startupload'); ?></span>
                </button>
                <button type="reset" class="btn btn-warning cancel btn-xs">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span><?php echo lang('TICKET_file_notupload') ?></span>
                </button--><br>
               <small class="text-muted"><?php echo lang('TICKET_file_upload_msg'); ?></small>
                <!-- The global file processing state -->
                
                
                
                <span class="fileupload-process"></span>
            </div>

        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
    </form>

</div>
    </div>
    </div>
</div>

<?php
        } ?>

<div class="col-md-2"></div>
<div class="col-md-10" id="processing">
    <div class="btn-group btn-group-justified">
        <div class="btn-group">
            <button id="enter_ticket" class="btn btn-success" type="button"><i class="fa fa-check-circle-o"></i> <?php echo lang('NEW_button_create'); ?></button>
        </div>
        <div class="btn-group">
            <button id="reset_ticket" class="btn btn-default" type="submit"><i class="fa fa-eraser"></i> <?php echo lang('NEW_button_reset'); ?></button>
        </div>
    </div>
    <div id="res"></div>
    <input type="hidden" id="file_array" value="">
    <input type="hidden" id="client_id_param" value="">
    <input type="hidden" id="hashname" value="<?php echo md5(time()); ?>">
    <input type="hidden" id="status_action" value="">
    <input type="hidden" id="prio" value="1">
    <input type="hidden" id="d_finish_val" value="NULL">
    <input type="hidden" value="<?php
        echo $_SESSION['helpdesk_user_id']; ?>" id="user_init_id">
    <input type="hidden" id="user_name_login" value="<?php echo get_user_val('login'); ?>">

<input type="hidden" id="file_types" value="<?php echo $CONF['file_types'] ?>">
<input type="hidden" id="file_size" value="<?php echo $CONF['file_size'] ?>">





</div>


</div>
</div>
</div>

    <br>


                                </div><!-- /.box-body -->
                            </div>


</div>
<div id="alert_add">
    </div>
<div class="col-md-4" id="user_info">

                            
                            
                            
                            





</div>
    


</div>




</div><!-- /.box-body -->
                            
                            
                            
                            
                            
                            
                            
                            </div>
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
</div>


</section>
<?php
        include ("footer.inc.php");
?>
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade" id="up_entry">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">
{% if (file.name.length>20) { %}
{%=file.name.substr(0,10) %}...{%=file.name.substr(-5) %}
{% } %}
{% if (file.name.length<20) { %}
{%=file.name%}
{% } %}

            </p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button id="s_start" class="btn btn-primary start btn-xs" disabled><i class="glyphicon glyphicon-upload"></i> <?php echo lang('TICKET_file_startupload'); ?>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel btn-xs">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span><?php echo lang('TICKET_file_notupload_one'); ?></span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->

<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
{% if (file.name2.length>30) { %}
    <?php echo lang('file_info'); ?>: {%=file.name2.substr(0,30) %}...{%=file.name2.substr(-5) %} - <?php echo lang('file_info2'); ?>
{% } %}
{% if (file.name2.length<30) { %}
    <?php echo lang('file_info'); ?>: {%=file.name2%} - <?php echo lang('file_info2'); ?>
{% } %}

            </p>

            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            <p class="name">
    <span class="label label-success"><i class="fa fa-check"></i> ok</span>
        </p>
    </td>
            </tr>
{% } %}
</script>



<?php
    }
} else {
    include 'auth.php';
}
?>
