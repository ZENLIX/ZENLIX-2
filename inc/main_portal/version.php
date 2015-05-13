<?php







$rkeys = array_keys($_GET);


$hn = $rkeys[1];
$CONF['title_header']=get_conf_param('name_of_firm')." - ".lang('PORTAL_versions');
if (isset($hn)) {
$news_item=get_version_info($hn);
$CONF['title_header']=get_conf_param('name_of_firm')." - ".$news_item['subj'];
}
include "head.inc.php";


include "navbar.inc.php";


$validate_user=false;
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
$validate_user=true;
}



if ($hn) {
    $hnset=true;


if ($hn == "new_feed") {
    $hnparam="new_feed";
}
else if ($hn != "new_feed") {
    $hnparam="no_new_feed";
    $news_item=get_version_info($hn);

if (isset($_GET['edit_feed'])) {
    $hnget="edit_feed";
}
else if (!isset($_GET['edit_feed'])) {
$hnget="no_edit_feed";
    }
}


}


if (!$hn) {
    $hnset=false;

}



$logo_img=$CONF['hostname']."upload_files/avatars/".get_conf_param('logo_img');
if (strlen(get_conf_param('logo_img')) < 5) {
  $logo_img=$CONF['hostname'].'img/ZENLIX_small.png';
}


$getna=array();
$news_arr=get_version_array();
foreach ($news_arr as $n) {
    array_push($getna, array(

'uniq_id'=>$n['uniq_id'],
'subj'=>$n['subj'],
'title'=>$n['title'],
'dt'=>$n['dt']



        ));
    }




ob_start();
showMenu_todo();
$showMenu_todo = ob_get_contents();
ob_end_clean();


ob_start();
get_main_todo();
$get_main_todo = ob_get_contents();
ob_end_clean();





ob_start();
view_release_bar();
$view_release_bar = ob_get_contents();
ob_end_clean();



ob_start();
view_stat_cat();
$view_stat_cat = ob_get_contents();
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
            $template = $twig->loadTemplate('version.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
'hostname'=>$CONF['hostname'],
'validate_user'=>$validate_user,
'hnset'=>$hnset,
'hnparam'=>$hnparam,
'hnget'=>$hnget,
'PORTAL_new_msg'=>lang('PORTAL_new_msg'),
'PORTAL_subj'=>lang('PORTAL_subj'),
'PORTAL_t'=>lang('PORTAL_t'),
'PORTAL_news_create'=>lang('PORTAL_news_create'),
'time'=>md5(time()),
'subj'=>$news_item['subj'],
'title'=>$news_item['title'],
'msg'=>$news_item['msg'],
'PORTAL_news_save'=>lang('PORTAL_news_save'),
'uniq_id'=>$news_item['uniq_id'],
'logo_img'=>$logo_img,
'REQUEST_URI'=>urlencode( $CONF['hostname'].$_SERVER['REQUEST_URI']),
'dt'=>$news_item['dt'],
'nameshort'=>nameshort(name_of_user_ret_nolink($news_item['author_id'])),
'PORTAL_act_del'=>lang('PORTAL_act_del'),
'PORTAL_act_edit'=>lang('PORTAL_act_edit'),
'PORTAL_version_box_title'=>lang('PORTAL_version_box_title'),
'portal_box_version_n'=>get_conf_param('portal_box_version_n'),
'FIELD_type_text'=>lang('FIELD_type_text'),
'portal_box_version_text'=>get_conf_param('portal_box_version_text'),
'PORTAL_icon'=>lang('PORTAL_icon'),
'portal_box_version_icon'=>get_conf_param('portal_box_version_icon'),
'JS_save'=>lang('JS_save'),
'PORTAL_todo_1'=>lang('PORTAL_todo_1'),
'showMenu_todo'=>$showMenu_todo,
'NOTES_create'=>lang('NOTES_create'),
'PORTAL_todo_2'=>lang('PORTAL_todo_2'),
'get_main_todo'=>$get_main_todo,
'PORTAL_versions'=>lang('PORTAL_versions'),
'getna'=>$getna,
'PORTAL_admin_menu'=>lang('PORTAL_admin_menu'),
'PORTAL_news_create'=>lang('PORTAL_news_create'),
'view_stat_cat'=>$view_stat_cat,
'view_release_bar'=>$view_release_bar


            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }











/*
?>
<div class="content-wrapper">
<section class="content">







<section class="invoice">
          <!-- title row -->
          <div class="row">



<div class="col-md-9">
<?php
if ($hn) {


if ($hn == "new_feed") {
  ?>
<?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_new_msg');?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-leaf"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                <div class="">


 <form class="form-horizontal">
  <div class="form-group">
    
    <div class="col-sm-9">
      <input type="text" class="form-control" id="subj" placeholder="<?=lang('PORTAL_subj');?>" value="">
    </div>
        <div class="col-sm-3">
      
    </div>
        <div class="col-sm-12"><br>
      <input type="text" class="form-control" id="title" placeholder="<?=lang('PORTAL_t');?>" value="">
    </div>
  </div>

    <div class="form-group">
    
<div class="col-sm-12">
<div id="note"></div>
</div>
<div class="col-sm-12" >


</div>

<div class="col-sm-12" id="post_res">



</div>



<div class="col-sm-6 col-sm-offset-3">
<br>
<button class="btn btn-block btn-info" id="make_new_version" ><?=lang('PORTAL_news_create');?></button>
<input type="hidden" value="<?php echo md5(time()); ?>" id="news_hash">
</div>

  </div>


  </form>



                </div><!-- /.footer -->
                </div>
                </div>

  <?php
}
}
else if ($hn != "new_feed") {



$news_item=get_version_info($hn);

if (isset($_GET['edit_feed'])) {
  ?>
  <?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_new_msg');?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-leaf"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                <div class="">


 <form class="form-horizontal">
  <div class="form-group">
    
    <div class="col-sm-9">
      <input type="text" class="form-control" id="subj" placeholder="<?=lang('PORTAL_subj');?>" value="<?=$news_item['subj'];?>">
    </div>
        <div class="col-sm-3">
      
    </div>
        <div class="col-sm-12"><br>
      <input type="text" class="form-control" id="title" placeholder="<?=lang('PORTAL_t');?>" value="<?=$news_item['title'];?>">
    </div>
  </div>

    <div class="form-group">
    
<div class="col-sm-12">
<div id="note"><?=$news_item['msg'];?></div>
</div>
<div class="col-sm-12" >


</div>

<div class="col-sm-12" id="post_res">



</div>



<div class="col-sm-6 col-sm-offset-3">
<br>
<button class="btn btn-block btn-info" id="make_edit_version" ><?=lang('PORTAL_news_save');?></button>
<input type="hidden" value="<?=$news_item['uniq_id'];?>" id="news_hash">
</div>

  </div>


  </form>



                </div><!-- /.footer -->
                </div>
                </div>
  <?php
}
}

else if (!isset($_GET['edit_feed'])) {





?>
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=$news_item['subj'];?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-leaf"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
<p class="text-muted"><?=$news_item['title'];?></p>

                <?=$news_item['msg'];?>
                </div>
                <div class="box-footer">


<?php
$logo_img=$CONF['hostname']."upload_files/avatars/".get_conf_param('logo_img');
if (strlen(get_conf_param('logo_img')) < 5) {
  $logo_img=$CONF['hostname'].'img/ZENLIX_small.png';
}
?>

<a style="width:25px;" class="btn btn-xs btn-twitter" href="http://twitter.com/share?text=<?=$news_item['title'];?>&url=<?php echo urlencode( $CONF['hostname'].$_SERVER['REQUEST_URI']); ?>" title="Share on Twitter" target="_blank"><i class="fa fa-twitter"></i></a>

<a style="width:25px;" class="btn btn-xs btn-facebook" 
href="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=<?php echo urlencode( $CONF['hostname'].$_SERVER['REQUEST_URI']); ?>&p[title]=<?php echo $news_item['title']; ?>&p[summary]=<?php echo $news_item['title']; ?>&p[images][0]=<?php echo $logo_img ?>"


><i class="fa fa-facebook"></i></a>
<a style="width:25px;" class="btn btn-xs btn-google-plus" href="https://plus.google.com/share?url=<?php echo urlencode( $CONF['hostname'].$_SERVER['REQUEST_URI']); ?>"><i class="fa fa-google-plus"></i></a>

<a style="width:25px;" class="btn btn-xs btn-github" href="mailto:?subject=&body=<?=$news_item['title'];?> - <?=$CONF['hostname'].$_SERVER['REQUEST_URI'];?>"><i class="fa fa-envelope"></i></a>

                <br>
                <small class="text-muted">
                <time id="c" datetime="<?=$news_item['dt'];?>"></time>
                </small>
                <small class="text-muted pull-right">
                <?=nameshort(name_of_user_ret_nolink($news_item['author_id']));?>

<?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
                <div class="btn-group ">
<button class="btn btn-xs bg-maroon" id="delete_version" value="<?=$news_item['uniq_id'];?>"><?=lang('PORTAL_act_del');?></button> 
<a class="btn btn-xs bg-orange btn-flat" href="version&<?=$news_item['uniq_id'];?>&edit_feed"><?=lang('PORTAL_act_edit');?></a>
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



?><?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 


<div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_version_box_title');?></h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                
      <form class="form-horizontal" role="form">
    <div class="form-group">
    <label for="file_size" class="col-sm-4 control-label"><small><?php
        echo lang('PORTAL_t'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="portal_box_version_n" placeholder="<?php
        echo lang('PORTAL_t'); ?>" value="<?php
        echo get_conf_param('portal_box_version_n'); ?>">
    </div>
  </div>
      <div class="form-group">
    <label for="file_size" class="col-sm-4 control-label"><small><?php
        echo lang('FIELD_type_text'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="portal_box_version_text" placeholder="<?php
        echo lang('FIELD_type_text'); ?>" value="<?php
        echo get_conf_param('portal_box_version_text'); ?>">
    </div>
  </div>
    <div class="form-group">
    <label for="file_size" class="col-sm-4 control-label"><small><?php
        echo lang('PORTAL_icon'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="portal_box_version_icon" placeholder="<?php
        echo lang('PORTAL_icon');?>" value="<?php
        echo get_conf_param('portal_box_version_icon'); ?>">
    </div>
  </div>
  <center>
    <button type="submit" id="conf_edit_version_banner" class="btn btn-success"><i class="fa fa-pencil"></i> <?php
        echo lang('JS_save'); ?></button>
    
</center>
      </form>


                </div>
                <div id="conf_edit_version_banner_res"></div>
                </div>


<div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_todo_1');?></h3>

                </div><!-- /.box-header -->
                <div class="box-body">

                  
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



<div id="content_items"> 


<?php

showMenu_todo();
?>


</div>



                </div>
                <div class="box-footer clearfix no-border">
                                    <button id="add_todo_item" class="btn btn-default pull-right"><i class="fa fa-plus"></i> <?=lang('NOTES_create');?></button>
                         

 
</div>
</div>


 <?php

}
?>

<div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_todo_2');?></h3>

                </div><!-- /.box-header -->
                <div class="box-body">
                <div class="">
                <?=get_main_todo();?>
                </div>
                </div>
                </div>


<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_versions');?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-leaf"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                <div class="">


 <ul class="products-list product-list-in-box ">


<?php
$news_arr=get_version_array();

foreach ($news_arr as $n) {
  # code...

?>



                    <li class="item">
                      
                      <div class="product-info" style="margin-left:0px;">
                        <a href="version&<?=$n['uniq_id'];?>" class="product-title"><h4><?=$n['subj'];?></h4> </a>
                        <span class="product-description">
                          <?=$n['title'];?>
                        </span>
                        <small class="text-muted pull-right">

<i class="fa fa-clock-o"></i> <time id="c" datetime="<?=$n['dt'];?>"></time>
                        </small>
                      </div>
                    </li><!-- /.item -->


<?php 

}
?>





                                        
                    
                  </ul>



                </div><!-- /.footer -->
                </div>
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
                  <a href="version?new_feed" class="btn btn-default btn-block"><?=lang('PORTAL_news_create');?></a>
                </div>
              </div>
<?php } ?>


<?=view_release_bar();?>






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