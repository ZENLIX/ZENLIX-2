<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
        include ("head.inc.php");
        include ("navbar.inc.php");
?>
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

<section class="content-header">
                    <h1>
                        <i class="fa fa-tags"></i> <?php echo lang('SUBJ_title'); ?>
                        <small><?php echo lang('STATS_TITLE_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('SUBJ_title'); ?></li>
                    </ol>
                </section>
                
                
                <section class="content">

                    <!-- row -->
                    <div class="row">
                    <div class="col-md-3">
                    
                    <!--input type="text" class="form-control input-sm ui-autocomplete-input" id="subj_text" placeholder="<?php echo lang('SUBJ_name'); ?>" autocomplete="off"-->
      
        <button id="subj_add" class="btn btn-default btn-sm btn-block" type="submit"><?php echo lang('SUBJ_add'); ?></button>
     <br>
                    
                    
                    
                     <?php
        if ($CONF['fix_subj'] == "false") { ?>
                   <div class="callout callout-danger">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php echo lang('DEPS_off'); ?>
       </small>
                                    </div><br>
                   <?php
        } ?>

                    <div class="callout callout-info">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php echo lang('SUBJ_info'); ?>
       </small>
                                    </div></div>
                    <div class="col-md-9">
                    
                    
                    
                    
                    <div class="box box-solid">
                                
                                <div class="box-body">
                                <div class="" id="content_subj">
      

<?=showMenu_sla();?>










      
      
<!--table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
        <thead>
          <tr>
            <th><center>ID</center></th>
            <th><center><?php echo lang('SUBJ_n'); ?></center></th>
            <th><center><?php echo lang('SUBJ_action'); ?></center></th>
          </tr>
        </thead>
    <tbody>   
    <?php
        
        //while ($row = mysql_fetch_assoc($results)) {
        foreach ($res1 as $row) {
?>
    <tr id="tr_<?php echo $row['id']; ?>">
    
    
    <td><small><center><?php echo $row['id']; ?></center></small></td>
    <td><small id="small_<?php echo $row['id']; ?>"><?php echo $row['name']; ?></small></td>
<td><small><center><button id="subj_del" type="button" class="btn btn-danger btn-xs" value="<?php echo $row['id']; ?>">del</button></center></small></td>
    </tr>
        <?php
        } ?>
    
    
      
    </tbody>
</table-->


      <br>
      </div>
                                </div>
                    </div>
                    </div>
                    </div>
                </section>


<?php
        include ("footer.inc.php");
?>

<?php
    }
} else {
    include '../auth.php';
}
?>