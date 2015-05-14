<?php





$rkeys = array_keys($_GET);


$hn = $rkeys[0];


$CONF['title_header']=get_conf_param('name_of_firm')." - ".lang('PORTAL_help_center');
if (isset($hn)) {


$news_item=get_manual_info($hn);
$CONF['title_header']=get_conf_param('name_of_firm')." - ".$news_item['name'];
}
include "head.inc.php";


include "navbar.inc.php";
if ($hn == "qa") {
    $news_item=get_qa_obj($_GET['qa']);
    $CONF['title_header']=get_conf_param('name_of_firm')." - ".$news_item['question'];
}










$val_user=false;
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
$val_user=true;
}





if ($hn) {
    $hn_set=true;




if ($hn == "edit_some_qa") {
    $hn_param="edit_some_qa";
$news_item=get_qa_obj($_GET['edit_some_qa']);
}
else if ($hn == "edit_qa") {
    $hn_param="edit_qa";

}

else if ($hn == "edit_cat") {
    $hn_param="edit_cat";

    }

else if ($hn == "qa") {
    $hn_param="qa";
$news_item=get_qa_obj($_GET['qa']);
    }

    else if ($hn == "find") {
        $hn_param="find";
        $t=$_GET['find'];

$ex=explode(" ", $t);

foreach ($ex as $value) {
$stmt = $dbConnection->prepare("SELECT * from portal_posts where (portal_posts.subj like :t) limit 10");
                $stmt->execute(array(
                    ':t' => '%' . $value . '%'
                ));
                $result = $stmt->fetchAll();
$find_res="<ul>";
foreach ($result as $row) {

  $find_res .= "<li style='list-style:none;'>".get_cat_icon($row['type'])." <a href=\"".$CONF['hostname']."thread&".$row['uniq_id']."\">".$row['subj']."</a></li>";
  # code...
}
$find_res .= "</ul>";
}


foreach ($ex as $value) {
$stmt = $dbConnection->prepare("SELECT * from portal_manual_cat where (name like :t) limit 10");
                $stmt->execute(array(
                    ':t' => '%' . $value . '%'
                ));
                $result = $stmt->fetchAll();
$find_res .= "<ul>";
foreach ($result as $row) {

  $find_res .= "<li style='list-style:none;'><i class=\"fa fa-graduation-cap\"></i> <a href=\"".$CONF['hostname']."manual&".$row['uniq_id']."\">".$row['name']."</a></li>";
  # code...
}
$find_res .= "</ul>";
}




    }

else if ($hn != "new_manual") {
    $hn_param="no_new_manual";
$news_item=get_manual_info($hn);


    if (isset($_GET['edit_manual'])) {
        $get_param="edit_manual";
    }
    else if (!isset($_GET['edit_manual'])) {
        $get_param="no_edit_manual";
    }

}    

}
if (!$hn) {
$hn_set=false;
}


ob_start();
view_stat_cat();
$view_stat_cat = ob_get_contents();
ob_end_clean();


ob_start();
show_all_manual();
$show_all_manual = ob_get_contents();
ob_end_clean();



ob_start();
showMenu_qa();
$showMenu_qa = ob_get_contents();
ob_end_clean();




ob_start();
showMenu_manual();
$showMenu_manual = ob_get_contents();
ob_end_clean();


ob_start();
view_attach_files($news_item['uniq_id'], 'comment');
$view_attach_files = ob_get_contents();
ob_end_clean();





ob_start();
get_main_manual();
$get_main_manual = ob_get_contents();
ob_end_clean();



ob_start();
show_qa_manual();
$show_qa_manual = ob_get_contents();
ob_end_clean();

$basedir = dirname(dirname(dirname(__FILE__))); 

 try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/inc/main_portal/views');
            
            // инициализируем Twig
if (get_conf_param('twig_cache') == "true") {
$twig = new Twig_Environment($loader,array(
    'cache' => $basedir.'/inc/cache',
));
            }
            else {
$twig = new Twig_Environment($loader);
            }
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('manual.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
'hostname'=>$CONF['hostname'],
'hn_set'=>$hn_set,
'hn_param'=>$hn_param,
'get_param'=>$get_param,
'val_user'=>$val_user,
'PORTAL_edit_qa'=>lang('PORTAL_edit_qa'),
'PORTAL_q'=>lang('PORTAL_q'),
'question'=>$news_item['question'],
'answer'=>$news_item['answer'],
'PORTAL_news_save'=>lang('PORTAL_news_save'),
'uniq_id'=>$news_item['uniq_id'],
'PORTAL_q_manag'=>lang('PORTAL_q_manag'),
'showMenu_qa'=>$showMenu_qa,
'NOTES_create'=>lang('NOTES_create'),
'PORTAL_cat_manag'=>lang('PORTAL_cat_manag'),
'showMenu_manual'=>$showMenu_manual,
'NOTES_create'=>lang('NOTES_create'),
'dt'=>$news_item['dt'],
'author_id'=>nameshort(name_of_user_ret_nolink($news_item['author_id'])),
'PORTAL_s_res'=>lang('PORTAL_s_res'),
'find_res'=>$find_res,
'PORTAL_edit_n'=>lang('PORTAL_edit_n'),
'PORTAL_subj'=>lang('PORTAL_subj'),
'name'=>$news_item['name'],
'msg'=>$news_item['msg'],
'PORTAL_fileplace'=>lang('PORTAL_fileplace'),
'title'=>$news_item['title'],
'view_attach_files'=>$view_attach_files,
'id'=>$news_item['id'],
'PORTAL_act_del'=>lang('PORTAL_act_del'),
'PORTAL_act_edit'=>lang('PORTAL_act_edit'),
'PORTAL_findby_h'=>lang('PORTAL_findby_h'),
'PORTAL_sel_text'=>lang('PORTAL_sel_text'),
'PORTAL_find_act'=>lang('PORTAL_find_act'),
'PORTAL_help_center'=>lang('PORTAL_help_center'),
'get_main_manual'=>$get_main_manual,
'PORTAL_qa'=>lang('PORTAL_qa'),
'show_qa_manual'=>$show_qa_manual,
'PORTAL_admin_menu'=>lang('PORTAL_admin_menu'),
'PORTAL_cat_n_manag'=>lang('PORTAL_cat_n_manag'),
'PORTAL_cat_list'=>lang('PORTAL_cat_list'),
'show_all_manual'=>$show_all_manual,
'view_stat_cat'=>$view_stat_cat



            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }





/*
}
include "head.inc.php";


include "navbar.inc.php";
?>
<div class="content-wrapper">
<section class="content">







<section class="invoice">
          <!-- title row -->
          <div class="row">



<div class="col-md-9">
<?php
if ($hn) {
if ($hn == "edit_some_qa") {



$news_item=get_qa_obj($_GET['edit_some_qa']);


?>
<?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 

<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_edit_qa');?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-file-text-o"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                <div class="">


 <form class="form-horizontal">
  <div class="form-group">
    
    <div class="col-sm-12">
      <input type="text" class="form-control" id="subj" placeholder="<?=lang('PORTAL_q');?>" value="<?=$news_item['question'];?>">
    </div>


  </div>

    <div class="form-group">
    
<div class="col-sm-12">
<div id="note"><?=$news_item['answer'];?></div>
</div>

<div class="col-sm-12" >


</div>

<div class="col-sm-12" id="post_res">



</div>



<div class="col-sm-6 col-sm-offset-3">
<br>
<button class="btn btn-block btn-info" id="make_edit_manual_qa" ><?=lang('PORTAL_news_save');?></button>
<input type="hidden" value="<?=$news_item['uniq_id'];?>" id="manual_hash">
</div>

  </div>


  </form>



                </div><!-- /.footer -->
                </div>
                </div>


<?php









}
}
else if ($hn == "edit_qa") {
  ?>
  <?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
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

        }

        .sortable li.mjs-nestedSortable-branch div {

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
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_q_manag');?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-file-text-o"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">


<div id="content_items"> 


<?php

showMenu_qa();
?>


</div>



                </div>
                <div class="box-footer clearfix no-border">
                                    <button id="add_qa_item" class="btn btn-default pull-right"><i class="fa fa-plus"></i> <?=lang('NOTES_create');?></button>
                                </div>
                </div>

  <?php

}
}

else if ($hn == "edit_cat") {
  ?>
  <?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
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

        }

        .sortable li.mjs-nestedSortable-branch div {

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
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_cat_manag');?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-file-text-o"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">


<div id="content_items"> 


<?php

showMenu_manual();
?>


</div>



                </div>
                <div class="box-footer clearfix no-border">
                                    <button id="add_manual_item" class="btn btn-default pull-right"><i class="fa fa-plus"></i> <?=lang('NOTES_create');?></button>
                                </div>
                </div>

  <?php
}
}




else if ($hn == "qa") {

  $news_item=get_qa_obj($_GET['qa']);
  ?>
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=$news_item['question'];?></h3>
<div class="box-tools pull-right">
                    <h4> </h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">


                <?=$news_item['answer'];?>
                </div>
                <div class="box-footer">
                <small class="text-muted">
                <time id="c" datetime="<?=$news_item['dt'];?>"></time>
                </small>
                <small class="text-muted pull-right">
                <?=nameshort(name_of_user_ret_nolink($news_item['author_id']));?>


                </small>
                </div>
                </div>
  <?php

}




else if ($hn == "find") {
?>
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_s_res');?></h3>
<div class="box-tools pull-right">
                    <h4> </h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
<?php
$t=$_GET['find'];
$ex=explode(" ", $t);

foreach ($ex as $value) {
$stmt = $dbConnection->prepare("SELECT * from portal_posts where (portal_posts.subj like :t) limit 10");
                $stmt->execute(array(
                    ':t' => '%' . $value . '%'
                ));
                $result = $stmt->fetchAll();
echo "<ul>";
foreach ($result as $row) {

  echo "<li style='list-style:none;'>".get_cat_icon($row['type'])." <a href=\"".$CONF['hostname']."thread&".$row['uniq_id']."\">".$row['subj']."</a></li>";
  # code...
}
echo "</ul>";
}


foreach ($ex as $value) {
$stmt = $dbConnection->prepare("SELECT * from portal_manual_cat where (name like :t) limit 10");
                $stmt->execute(array(
                    ':t' => '%' . $value . '%'
                ));
                $result = $stmt->fetchAll();
echo "<ul>";
foreach ($result as $row) {

  echo "<li style='list-style:none;'><i class=\"fa fa-graduation-cap\"></i> <a href=\"".$CONF['hostname']."manual&".$row['uniq_id']."\">".$row['name']."</a></li>";
  # code...
}
echo "</ul>";
}
  
  ?>



                </div>

                </div>
  <?php

}





else if ($hn != "new_manual") {



$news_item=get_manual_info($hn);

if (isset($_GET['edit_manual'])) {
  ?>
  <?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_edit_n');?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-file-text-o"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                <div class="">


 <form class="form-horizontal">
  <div class="form-group">
    
    <div class="col-sm-9">
      <input type="text" class="form-control" id="subj" placeholder="<?=lang('PORTAL_subj');?>" value="<?=$news_item['name'];?>">
    </div>
        <div class="col-sm-3">
        
    </div>

  </div>

    <div class="form-group">
    
<div class="col-sm-12">
<div id="note"><?=$news_item['msg'];?></div>
</div>
<div class="col-sm-12" >

<div class="text-muted well well-sm no-shadow" id="myid" >
  <div class="dz-message" data-dz-message>
<center class="text-muted"><?=lang('PORTAL_fileplace');?></center>
  </div>



<form action="upload.php" class="dropzone"></form>

<div class="table table-striped" class="files" id="previews">
 
  <div id="template" class="file-row">
    <!-- This is used as the file preview template -->



<table class="table" style="margin-bottom: 0px;">
                  <tbody><tr>
                    <th style="width:50%"><p class="name" data-dz-name></p> </th>
                    <td><small class="text-muted"><p class="size" data-dz-size></p></small></td>
                    <td style="width:30%"><div class="progress progress-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
          <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
        </div></td>
                    <td class="pull-right"><button data-dz-remove class="btn btn-sm btn-danger delete">
        <i class="glyphicon glyphicon-trash"></i>
        <span>Delete</span>
      </button></td>
                  </tr>

                </tbody></table>










<!--table class="table">
  <tr>
    <td><p class="name" data-dz-name></p>
<br><p class="size" data-dz-size></p>
    </td>
    <td>        
        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
          <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
        </div></td>
        <td>
              <button data-dz-remove class="btn btn-danger delete">
        <i class="glyphicon glyphicon-trash"></i>
        <span>Delete</span>
      </button>
        </td>
  </tr>
</table-->

</div>
  </div>
 
</div>
</div>
<div class="col-sm-12" >


</div>

<div class="col-sm-12" id="post_res">



</div>



<div class="col-sm-6 col-sm-offset-3">
<br>
<button class="btn btn-block btn-info" id="make_edit_manual" ><?=lang('PORTAL_news_save');?></button>
<input type="hidden" value="<?=$news_item['uniq_id'];?>" id="manual_hash">
</div>

  </div>


  </form>



                </div><!-- /.footer -->
                </div>
                </div>
  <?php
}
}

else if (!isset($_GET['edit_manual'])) {





?>
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=$news_item['name'];?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-file-text-o"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
<p class="text-muted"><?=$news_item['title'];?></p>

                <?=$news_item['msg'];?>

                <?=view_attach_files($news_item['uniq_id'], 'comment');?></div>

                <div class="box-footer">
                <small class="text-muted">
                <time id="c" datetime="<?=$news_item['dt'];?>"></time>
                </small>
                <small class="text-muted pull-right">
                <?=nameshort(name_of_user_ret_nolink($news_item['author_id']));?>

<?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
                <div class="btn-group ">
<button class="btn btn-xs bg-maroon" id="delete_manual" value="<?=$news_item['id'];?>"><?=lang('PORTAL_act_del');?></button> 
<a class="btn btn-xs bg-orange btn-flat" href="manual&<?=$news_item['uniq_id'];?>&edit_manual"><?=lang('PORTAL_act_edit');?></a>
 </div>
<?php } ?>

                </small>
                </div>
                </div>










<?php
}
}
}
if (!$hn) {



?>


<div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_findby_h');?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-search"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                


<form method="get" action="manual">
<div class="input-group input-group-lg">
                    <input type="text" class="form-control" name="find" placeholder="<?=lang('PORTAL_sel_text');?>">
                    <span class="input-group-btn">
                      <button class="btn btn-info btn-flat" type="submit"><?=lang('PORTAL_find_act');?></button>
                    </span>
                  </div>
</form>

                </div>
                </div>






<div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_help_center');?></h3>
                  <div class="box-tools pull-right">
                    <h4> <i class="fa fa-graduation-cap"></i></h4>
                  </div>
                </div><!-- /.box-header -->


                <div class="box-body">
<div class="row">
<?=get_main_manual();?>

</div>

                </div><!-- /.box-body -->
              </div>

              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_qa');?></h3>
                  <div class="box-tools pull-right">
                    <h4> <i class="fa fa-graduation-cap"></i></h4>
                  </div>
                </div><!-- /.box-header -->


                <div class="box-body"><?=show_qa_manual();?>


                </div><!-- /.box-body -->
              </div>
<?php
}
?>



</div>



<div class="col-md-3">




<?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
<div class="box">
                <div class="box-header">
                  <h3 class="box-title"><?=lang('PORTAL_admin_menu');?></h3>
                </div>
                <div class="box-body">
                <a href="manual&edit_cat" class="btn btn-default btn-block"><?=lang('PORTAL_cat_n_manag');?></a>
                  <!--a href="manual?new_manual" class="btn btn-default btn-block">Создать статью</a-->
                  <a href="manual&edit_qa" class="btn btn-default btn-block"><?=lang('PORTAL_q_manag');?></a>
                  
                </div>
              </div>
<?php } ?>




<div class="box">
                <div class="box-header">
                  <h3 class="box-title"><?=lang('PORTAL_cat_list');?></h3>
                </div>
                <div class="box-body">
                <?=show_all_manual();?>
                </div>
              </div>











<?=view_stat_cat();?>







</div>


          </div>
          <!-- info row -->
          
        </section>




</section>
</div>


<?php
*/
include "footer.inc.php";
?>