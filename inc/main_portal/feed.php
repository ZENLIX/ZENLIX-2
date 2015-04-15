<?php
$rkeys = array_keys($_GET);


$hn = $rkeys[1];




$CONF['title_header']=get_conf_param('name_of_firm')." - ".lang('PORTAL_news');
if (isset($hn)) {
$news_item=get_news_info($hn);
$CONF['title_header']=get_conf_param('name_of_firm')." - ".$news_item['subj'];
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


if ($hn == "new_feed") {

  ?>
<?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_new_msg');?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-comment"></i></h4>
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
      <input type="text" class="form-control" id="title" placeholder="<?=lang('PORTAL_ann');?>" value="">
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
<button class="btn btn-block btn-info" id="make_new_feed" ><?=lang('PORTAL_news_create');?></button>
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



$news_item=get_news_info($hn);

if (isset($_GET['edit_feed'])) {

  ?>
  <?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_new_msg');?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-comment"></i></h4>
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
      <input type="text" class="form-control" id="title" placeholder="<?=lang('PORTAL_ann');?>" value="<?=$news_item['title'];?>">
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
<button class="btn btn-block btn-info" id="make_edit_feed" ><?=lang('PORTAL_news_save');?></button>
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
                    <h4> <i class="fa fa-newspaper-o"></i></h4>
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
<button class="btn btn-xs bg-maroon" id="delete_news" value="<?=$news_item['uniq_id'];?>"><?=lang('PORTAL_act_del');?></button> 
<a class="btn btn-xs bg-orange btn-flat" href="feed?<?=$news_item['uniq_id'];?>&edit_feed"><?=lang('PORTAL_act_edit');?></a>
 </div>
<?php }?>

                </small>
                </div>
                </div>










<?php
}
}
}
if (!$hn) {



?>


<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_news');?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-newspaper-o"></i> </h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                <div class="">


 <ul class="products-list product-list-in-box ">


<?php
$news_arr=get_news_array();

foreach ($news_arr as $n) {
  # code...

?>



                    <li class="item">
                      
                      <div class="product-info" style="margin-left:0px;">
                        <a href="feed?<?=$n['uniq_id'];?>" class="product-title">
                        <h4><?=$n['subj'];?> </h4>

                       </a>
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
                  <a href="feed?new_feed" class="btn btn-default btn-block"><?=lang('PORTAL_news_create');?></a>
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
include "footer.inc.php";
?>