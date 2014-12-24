<?php
session_start();
include_once "../functions.inc.php";

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        if (isset($_GET['h'])) {
            
            $h = ($_GET['h']);


            if (isset($_GET['edit'])) {
                $hn=$h;
            $stmt = $dbConnection->prepare('select id, user_init_id, unit_to_id, dt, title, message, hashname,client_flag,cat_id from helper where hashname=:hn');
            $stmt->execute(array(':hn' => $hn));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $isclient_status = $fio['client_flag'];
            
            if ($isclient_status == "1") {
                $isclient_status = "checked";
            } else {
                $isclient_status = "";
            }
            $cat_id=$fio['cat_id'];
            $u = $fio['unit_to_id'];
            $u = explode(",", $u);
?>

        <section class="content-header">
                    <h1>
                        <i class="fa fa-globe"></i> <?php echo lang('HELPER_title'); ?>
                        
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                       <li><a href="<?php echo $CONF['hostname'] ?>helper "><?php echo lang('HELPER_title'); ?></a></li>
                       <li class="active"><a href="<?php echo $CONF['hostname'] ?>helper?cat=<?=$cat_id;?>"><?=get_helper_cat_name($cat_id);?></a></li>
                        
                    </ol>
                </section>
                
                
                
            <section class="content">


<div class="row">
   
    <div class="col-md-12">

            <div class="box box-solid">
            <div class="box-body">
            <form class="form-horizontal" role="form">


                <div class="form-group">
                    <label for="u" class="col-md-2 control-label"><small><?php echo lang('HELPER_cat'); ?>: </small></label>
                    <div class="col-md-10">
                        <select style="height: 34px;" data-placeholder="<?php echo lang('HELPER_cat'); ?>" class="chosen-select form-control" id="cat" name="cat_id">
                            
                            <?php
            $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM helper_cat order by sort_id ASC');
            $stmt->execute();
            $result = $stmt->fetchAll();
            foreach ($result as $row) {
                
                $row['label'] = $row['label'];
                $row['value'] = (int)$row['value'];


                //$cat_id

                $sel_cat="";

                if ($cat_id == $row['value']) {$sel_cat="selected";}
?>

                                <option value="<?php echo $row['value'] ?>" <?=$sel_cat;?>><?php echo $row['label'] ?></option>

                            <?php
            }
?>

                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="u" class="col-md-2 control-label"><small><?php echo lang('NEW_to'); ?>: </small></label>
                    <div class="col-md-10">
                        
                        <select data-placeholder="<?php echo lang('NEW_to_unit'); ?>" class="chosen-select form-control" id="u" name="unit_id" multiple>
                            <?php $ar_n=""; if (in_array('0', $u)) { $ar_n="selected"; } 
                             ?>
                        <option value="0" <?=$ar_n;?>><?php echo lang('HELP_all'); ?></option>
                            <?php
            
            $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps');
            $stmt->execute();
            $result = $stmt->fetchAll();
            
            foreach ($result as $row) {
                
                $row['label'] = $row['label'];
                $row['value'] = (int)$row['value'];
                
                $opt_sel = '';
                foreach ($u as $val) {
                    if ($val == $row['value']) {
                        $opt_sel = "selected";
                    }
                }
?>

                                <option <?php echo $opt_sel; ?> value="<?php echo $row['value'] ?>"><?php echo $row['label'] ?></option>

<?php
                
                //
                
            }
?>

                        </select>
                    </div>
                </div>
                <div class="">
                    <div class="">
                        <div class="form-group">

                            <label for="t" class="col-sm-2 control-label"><small><?php echo lang('HELP_desc'); ?>: </small></label>

                            <div class="col-sm-10">


                                <input  type="text" name="fio" class="form-control input-sm" id="t" placeholder="<?php echo lang('HELP_desc'); ?>" value="<?php echo $fio['title']; ?>">



                            </div>



                        </div></div>
                        
                        
                        <div class="form-group">
  <label for="is_client" class="col-sm-2 control-label"><small><?php echo lang('EXT_for_clients'); ?></small></label>
  <div class="col-sm-10">
  
  
  
      <div class="col-sm-10">
      <div class="checkbox">
    <label>
      <input type="checkbox" id="is_client" <?php echo $isclient_status; ?>> <?php echo lang('CONF_true'); ?>
      <p class="help-block"><small><?php echo lang('EXT_for_clients_ext'); ?></small></p>
    </label>
  </div>
      </div>
  </div>
    </div>
    
    
                        
                    <div class="form-group">

                        <label for="t2" class="col-sm-2 control-label"><small><?php echo lang('HELP_do'); ?>: </small></label>

                        <div class="col-sm-10">


                            <div id="summernote_help"><?php echo $fio['message']; ?></div>



                        </div>
                        <div class="col-md-12"><hr></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                    <button id="do_save_help" value="<?php echo $hn ?>" class="btn btn-success" type="submit"><i class="fa fa-check-circle-o"></i> <?php echo lang('HELP_save'); ?></button>
                                </div>
                                <div class="btn-group">
                                    <a href="helper" class="btn btn-default" type="submit"><i class="fa fa-reply"></i> <?php echo lang('HELP_back'); ?></a>
                                </div>
                            </div>


                        </div>
            </form>
            </div></div></form></div></div></div></div></section>
            <?php
            }
            else if (!isset($_GET['edit'])) {
                
            
            $stmt = $dbConnection->prepare('select id, user_init_id, unit_to_id, dt, title, message, hashname, cat_id, user_edit_id
                            from helper where hashname=:h');
            $stmt->execute(array(':h' => $h));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            $cat_id=$fio['cat_id'];
            $user_edit_id=$fio['user_edit_id'];
            $user_init_id=$fio['user_init_id'];
?>

        <section class="content-header">
                    <h1>
                        <i class="fa fa-globe"></i> <?php echo lang('HELPER_title'); ?>
                        
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li><a href="<?php echo $CONF['hostname'] ?>helper "><?php echo lang('HELPER_title'); ?></a></li>
                       <li class="active"><a href="<?php echo $CONF['hostname'] ?>helper?cat=<?=$cat_id;?>"><?=get_helper_cat_name($cat_id);?></a></li>
                        
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
    
    <p class="text-right"><small class="text-muted"><?php echo lang('HELPER_pub'); ?>: <?php echo nameshort(name_of_user_ret($fio['user_init_id'])); ?></small><br><small class="text-muted"> <time id="c" datetime="<?php echo $fio['dt']; ?>"></time>
<br>

<?php
//|| ($user_edit_id != $user_init_id)
if (($user_edit_id != "0") && ($user_edit_id != $user_init_id)) {

?>
<?=lang('TICKET_t_last_up');?>: <?php echo nameshort(name_of_user_ret($fio['user_edit_id'])); ?>
<?php }
?>


    </small>
    <br>
<?php 
                $user_id = id_of_user($_SESSION['helpdesk_user_login']);
                $unit_user = unit_of_user($user_id);
                $priv_val = priv_status($user_id);
                
                $units = explode(",", $unit_user);
                array_push($units, "0");

                        $unit2id = explode(",", $fio['unit_to_id']);
                        
                        $diff = array_intersect($units, $unit2id);
                        $priv_h = "no";
                        if ($priv_val == 1) {
                            if (($diff) || ($user_id == $fio['user_init_id'])) {
                                $ac = "ok";
                            }
                            
                            if ($user_id == $fio['user_init_id']) {
                                $priv_h = "yes";
                            }
                        } else if ($priv_val == 0) {
                            $ac = "ok";
                            if ($user_id == $fio['user_init_id']) {
                                $priv_h = "yes";
                            }
                        } else if ($priv_val == 2) {
                            $ac = "ok";
                            $priv_h = "yes";
                        }


if ($priv_h == "yes") {
                                echo " 
            <div class=\"btn-group pull-right\">
            <a id=\"print_t\" class=\"btn btn-default btn-xs\"> <i class=\"fa fa-print\"></i> " . lang('HELPER_print') . "</a>
            <a href=\"" . $CONF['hostname']."/helper?h=".$h . "&edit\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-pencil\"></i> " . lang('CONF_act_edit') . " </a>
            <button id=\"del_helper\" value=\"" . $h . "\"type=\"button\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-trash-o\"></i></button>
            </div>
            ";
                            } ?>
                            <br>
        </p>
            </div>
        </div>
    </div>
</div>
            </section>
    
    
    
    
    

    
    <?php
}
        } else if (!isset($_GET['h'])) {


            if (isset($_GET['edit_cats'])) {
?>
    <section class="content-header">
                    <h1>
                        <i class="fa fa-globe"></i> <?php echo lang('HELPER_title'); ?>
                        <small><?php echo lang('HELP_cats_title'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><a href="helper"><?php echo lang('HELPER_title'); ?></a></li>
                        <li class="active"><?php echo lang('HELP_cats_title'); ?></li>
                    </ol>
                </section>
                
                
                
            <section class="content">







                    <!-- row -->
                    <div class="row">
                    
                    
                    
                                        <div class="col-md-3">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <a href="<?php echo $CONF['hostname'] ?>helper?add" class="btn btn-success btn-sm btn-block"><i class="fa fa-file-o"></i> <?php echo lang('HELPER_create'); ?></a>
                    <?php if ( (priv_status($_SESSION['helpdesk_user_id']) == "2") || (priv_status($_SESSION['helpdesk_user_id']) == "0") ) { ?>
                    <a href="<?php echo $CONF['hostname'] ?>helper?edit_cats" class="btn btn-default btn-sm btn-block"><i class="fa fa-list"></i> <?=lang('HELP_cats_title');?> </a>
                    <?php } ?>
<br>
                    <div class="callout callout-info">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php echo lang('HELPER_cats_info'); ?>
         </small>
                                    </div>
                        </div>
                        <div class="col-md-12">
                            






                        </div>
                    </div>
                    
                                    
                                    
                                    
                    
                    
                    
                    </div>

                    
                    <div class="col-md-9">
                         <div class="box box-solid">
            <div class="">
            
<style type="text/css">



        

        pre, code {
            font-size: 12px;
        }

        pre {
            width: 100%;
            overflow: auto;
        }

        small {
            font-size: 90%;
        }

        small code {
            font-size: 11px;
        }

        .placeholder {
            outline: 1px dashed #4183C4;
            /*-webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            margin: -1px;*/
            height: 20px;
        }

        .mjs-nestedSortable-error {
            background: #fbe3e4;
            border-color: transparent;
        }

        ul {
            margin: 0;
            padding: 0;
            padding-left: 30px;
        }

        ul.sortable, ul.sortable ul {
            margin: 0 0 0 25px;
            padding: 0;
            list-style-type: none;
        }

        ul.sortable {
            margin: 4em 0;
        }

        .sortable li {
            margin: 5px 0 0 0;
            padding: 0;
        }

        .sortable li div  {
            /*
            border: 1px solid #d4d4d4;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            border-color: #D4D4D4 #D4D4D4 #BCBCBC;
            padding: 6px;
            margin: 0;
            cursor: move;
            background: #f6f6f6;
            background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #ededed 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(47%,#f6f6f6), color-stop(100%,#ededed));
            background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            background: -o-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            background: -ms-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            background: linear-gradient(to bottom,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ededed',GradientType=0 );
            */
        }

        .sortable li.mjs-nestedSortable-branch div {
           /* background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #f0ece9 100%);
            background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#f0ece9 100%);
            */
            list-style-type: none;

        }

        .sortable li.mjs-nestedSortable-leaf div {


        }

        li.mjs-nestedSortable-collapsed.mjs-nestedSortable-hovering div {
            border-color: #999;
            background: #fafafa;
        }

        .disclose {
            cursor: pointer;
            width: 10px;
            display: none;
        }

        .sortable li.mjs-nestedSortable-collapsed > ul {
            display: none;
        }

        .sortable li.mjs-nestedSortable-branch > div > .disclose {
            display: inline-block;
        }

        .sortable li.mjs-nestedSortable-collapsed > div > .disclose > span:before {
            content: '+ ';
        }

        .sortable li.mjs-nestedSortable-expanded > div > .disclose > span:before {
            content: '- ';
        }

        

        p, ol, ul, pre, form {
            margin-top: 0;
            margin-bottom: 1em;
        }

        dl {
            margin: 0;
        }

        dd {
            margin: 0;
            padding: 0 0 0 1.5em;
        }

        code {
            background: #e5e5e5;
        }

        input {
            vertical-align: text-bottom;
        }

        .notice {
            color: #c33;
        }

    </style>


<div class="">
                                <div class="box-header">
                                    
                                    <h3 class="box-title"><?=lang('HELP_cats_title');?></h3>

                                </div><!-- /.box-header -->
                                <div class="box-body">




<div id="content_items"> 


<?php

showMenu_helper();
?>


</div>

                                </div><!-- /.box-body -->
                                <div class="box-footer clearfix no-border">
                                    <button id="add_helper_item" class="btn btn-default pull-right"><i class="fa fa-plus"></i> <?=lang('NOTES_create');?></button>
                                </div>
                            </div>
            </div></div>
                    </div>
                    
                    
                    
                    </div>
            </section>    
<?php
            }
else if (isset($_GET['cat'])) {


$cat_id=$_GET['cat'];

    $stmt = $dbConnection->prepare('SELECT name from helper_cat where id=:p_id');
    $stmt->execute(array(':p_id' => $cat_id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

   // $row['name'];

?>
 <section class="content-header">
                    <h1>
                        <i class="fa fa-globe"></i> <?php echo lang('HELPER_title'); ?>
                        <small><?=$row['name'];?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><a href="helper"><?php echo lang('HELPER_title'); ?></a></li>
                        <li class="active"><?=$row['name'];?></li>
                    </ol>
</section>
                
                
                
            <section class="content">







                    <!-- row -->
                    <div class="row">
                    
                    
                    
                                        <div class="col-md-3">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <a href="<?php echo $CONF['hostname'] ?>helper?add" class="btn btn-success btn-sm btn-block"><i class="fa fa-file-o"></i> <?php echo lang('HELPER_create'); ?></a>
                    <?php if ( (priv_status($_SESSION['helpdesk_user_id']) == "2") || (priv_status($_SESSION['helpdesk_user_id']) == "0") ) { ?>
                    <a href="<?php echo $CONF['hostname'] ?>helper?edit_cats" class="btn btn-default btn-sm btn-block"><i class="fa fa-list"></i> <?=lang('HELP_cats_title');?> </a>
                    <?php } ?>
<br>

                        </div>
                        <div class="col-md-12">
                            
                            <div class="box box-solid">
                                                                <div class="box-header">
                                    
                                    <h3 class="box-title"><?=lang('HELPER_cats');?></h3>

                                </div><!-- /.box-header -->
                                <div class="box-body">
                                <?=show_items_helper();?>
                            </div>
                            </div>





                        </div>
                    </div>
                    
                                    
                                    
                                    
                    
                    
                    
                    </div>

                    
                    <div class="col-md-9">
                         <div class="box box-solid">


                                <div class="">
                                   <?=show_item_helper_cat($cat_id);?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>





<?php
}
            else if (isset($_GET['add'])) {

                ?>
                <section class="content-header">
                    <h1>
                        <i class="fa fa-globe"></i> <?php echo lang('HELPER_title'); ?>
                        <small><?php echo lang('HELPER_add'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><a href="helper"><?php echo lang('HELPER_title'); ?></a></li>
                        <li class="active"><?php echo lang('HELPER_add'); ?></li>
                    </ol>
                </section>
                
                
                
            <section class="content">







                    <!-- row -->
                    <div class="row">
                    
                    
                    
                                        <div class="col-md-3">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <a href="<?php echo $CONF['hostname'] ?>helper?add"  class="btn btn-success btn-sm btn-block"><i class="fa fa-file-o"></i> <?php echo lang('HELPER_create'); ?></a>
                    <?php if ( (priv_status($_SESSION['helpdesk_user_id']) == "2") || (priv_status($_SESSION['helpdesk_user_id']) == "0") ) { ?>
                    <a href="<?php echo $CONF['hostname'] ?>helper?edit_cats" class="btn btn-default btn-sm btn-block"><i class="fa fa-list"></i> <?=lang('HELP_cats_title');?> </a>
                    <?php } ?>
<br>
                    <div class="callout callout-info">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php echo lang('HELPER_add_info'); ?>
         </small>
                                    </div>
                        </div>
                        <div class="col-md-12">
                            






                        </div>
                    </div>
                    
                                    
                                    
                                    
                    
                    
                    
                    </div>
                    
                    <div class="col-md-9" id="">
            <div class="box box-solid">
            <div class="box-body">
            <form class="form-horizontal" role="form">

                <div class="form-group">
                    <label for="u" class="col-md-2 control-label"><small><?php echo lang('HELPER_cat'); ?>: </small></label>
                    <div class="col-md-10">
                        <select style="height: 34px;" data-placeholder="<?php echo lang('HELPER_cat'); ?>" class="chosen-select form-control" id="cat" name="cat_id">
                            
                            <?php
            $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM helper_cat order by sort_id ASC');
            $stmt->execute();
            $result = $stmt->fetchAll();
            foreach ($result as $row) {
                
                $row['label'] = $row['label'];
                $row['value'] = (int)$row['value'];
?>

                                <option value="<?php echo $row['value'] ?>"><?php echo $row['label'] ?></option>

                            <?php
            }
?>

                        </select>
                    </div>
                </div>



                <div class="form-group">
                    <label for="u" class="col-md-2 control-label"><small><?php echo lang('NEW_to'); ?>: </small></label>
                    <div class="col-md-10">
                        <select style="height: 34px;" data-placeholder="<?php echo lang('NEW_to_unit'); ?>" class="chosen-select form-control" id="u" name="unit_id" multiple>
                            <option value="0"><?php echo lang('HELP_all'); ?></option>
                            <?php
            $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
            $stmt->execute(array(':n' => '0', ':s' => '1'));
            $result = $stmt->fetchAll();
            foreach ($result as $row) {
                
                $row['label'] = $row['label'];
                $row['value'] = (int)$row['value'];
?>

                                <option value="<?php echo $row['value'] ?>"><?php echo $row['label'] ?></option>

                            <?php
            }
?>

                        </select>
                    </div>
                </div>
                <div class="">
                    <div class="">
                        <div class="form-group">

                            <label for="t" class="col-sm-2 control-label"><small><?php echo lang('HELP_desc'); ?>: </small></label>

                            <div class="col-sm-10">


                                <input  type="text" name="fio" class="form-control input-sm" id="t" placeholder="<?php echo lang('HELP_desc'); ?>">



                            </div>



                        </div>






                    </div>
                        
                        
                        
                        <div class="form-group">
  <label for="is_client" class="col-sm-2 control-label"><small><?php echo lang('EXT_for_clients'); ?></small></label>
  <div class="col-sm-10">
  
  
  
      <div class="col-sm-10">
      <div class="checkbox">
    <label>
      <input type="checkbox" id="is_client"> <?php echo lang('CONF_true'); ?>
      <p class="help-block"><small><?php echo lang('EXT_for_clients_ext'); ?></small></p>
    </label>
  </div>
      </div>
  </div>
    </div>
                        
                        
                    <div class="form-group">

                        <label for="t2" class="col-sm-2 control-label"><small><?php echo lang('HELP_do'); ?>: </small></label>

                        <div class="col-sm-10">


                            <div id="summernote_help"></div>



                        </div>
                        <div class="col-md-12"><hr></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                    <button id="do_create_help" class="btn btn-success" type="submit"><i class="fa fa-check-circle-o"></i> <?php echo lang('HELP_create'); ?></button>
                                </div>
                                <div class="btn-group">
                                    <a href="helper" class="btn btn-default" type="submit"><i class="fa fa-reply"></i> <?php echo lang('HELP_back'); ?></a>
                                </div>
                            </div>


                        </div>
            </form>
            </div></div> </div></div> 
                
            </section>
                <?php
            }
            else {

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
                    
                    <div class="row">
                        <div class="col-md-12">
                            <a href="<?php echo $CONF['hostname'] ?>helper?add"  class="btn btn-success btn-sm btn-block"><i class="fa fa-file-o"></i> <?php echo lang('HELPER_create'); ?></a>
                    <?php if ( (priv_status($_SESSION['helpdesk_user_id']) == "2") || (priv_status($_SESSION['helpdesk_user_id']) == "0") ) { ?>
                    <a href="<?php echo $CONF['hostname'] ?>helper?edit_cats" class="btn btn-default btn-sm btn-block"><i class="fa fa-list"></i> <?=lang('HELP_cats_title');?> </a>
                    <?php } ?>
<br>
                    <div class="callout callout-info">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php echo lang('HELPER_info'); ?>
         </small>
                                    </div>
                        </div>
                        <div class="col-md-12">
                            






                        </div>
                    </div>
                    
                                    
                                    
                                    
                    
                    
                    
                    </div>
                    
                    <div class="col-md-9" id="help_content">
                    
                    </div>
                    
                    
                    
                    
                    
                    </div>
            </section>    
                
                
                
                
                


        


<?php
}
        }
        include ("footer.inc.php");
?>


<?php
    }
} else {
    include 'auth.php';
}
?>
