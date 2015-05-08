

<?php


$CONF['title_header']=get_conf_param('name_of_firm')." - SUPPORT CENTER";

include "head.inc.php";


include "navbar.inc.php";
?>
<div class="content-wrapper">
<section class="content">


<?php 
 if (get_conf_param('portal_msg_status') == "true") {

switch (get_conf_param('portal_msg_type')) {
  case 'info':
    $ic="callout-info";
    $ic1="fa-info";
    break;
      case 'warning':
    $ic="callout-warning";
    $ic1="fa-warning";
    break;
      case 'danger':
    $ic="callout-danger";
    $ic1="fa-danger";
    break;
  
  default:
    $ic="callout-info";
    $ic1="fa-info";
    break;
}

?>
<div class="pad margin no-print">
          <div class="callout <?=$ic;?>" style="margin-bottom: 0!important;">                        
            <h4><i class="fa <?=$ic1;?>"></i> <?=get_conf_param('portal_msg_title');?></h4>
            <?=get_conf_param('portal_msg_text');?>
          </div>
        </div>
<?php
}
?>


<section class="invoice">
          <!-- title row -->
          <div class="row">



<div class="col-md-9">



<div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li class="active"><a class="text-success" href="#tab_1" data-toggle="tab" aria-expanded="true"> <i class="fa fa-lightbulb-o"></i> <?=lang('PORTAL_idea_one');?></a></li>
                  <li class=""><a class="text-red" href="#tab_2" data-toggle="tab" aria-expanded="false"><i class="fa fa-exclamation-triangle"></i> <?=lang('PORTAL_trouble_one');?></a></li>
                  <li class=""><a class="text-primary"  href="#tab_3" data-toggle="tab" aria-expanded="false"><i class="fa fa-question-circle"></i> <?=lang('PORTAL_question_one');?></a></li>
                  <li class=""><a class="text-orange" href="#tab_4" data-toggle="tab" aria-expanded="false"><i class="fa fa-heart"></i> <?=lang('PORTAL_thank_one');?></a></li>
                  
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
                    <div class="input-group input-group-lg">
                    <input type="text" class="form-control" id="text_idea" placeholder="<?=lang('PORTAL_idea_you');?>">
                    <span class="input-group-btn">
                      <button id="make_new_post_idea" class="btn btn-info btn-flat" type="button"><?=lang('PORTAL_next');?></button>
                    </span>
                  </div>
                  </div><!-- /.tab-pane -->
                  <div class="tab-pane fade" id="tab_2">
                    <div class="input-group input-group-lg">
                    <input type="text" class="form-control" id="text_problem" placeholder="<?=lang('PORTAL_trouble_you');?>">
                    <span class="input-group-btn">
                      <button id="make_new_post_problem" class="btn btn-info btn-flat" type="button"><?=lang('PORTAL_next');?></button>
                    </span>
                  </div>
                  </div><!-- /.tab-pane -->
                                    <div class="tab-pane fade" id="tab_3">
                    <div class="input-group input-group-lg">
                    <input type="text" class="form-control" id="text_quest" placeholder="<?=lang('PORTAL_question_you');?>">
                    <span class="input-group-btn">
                      <button id="make_new_post_quest" class="btn btn-info btn-flat" type="button"><?=lang('PORTAL_next');?></button>
                    </span>
                  </div>
                  </div><!-- /.tab-pane -->
                                    <div class="tab-pane fade" id="tab_4">
                    <div class="input-group input-group-lg">
                    <input type="text" class="form-control" id="text_review" placeholder="<?=lang('PORTAL_thank_you');?>">
                    <span class="input-group-btn">
                      <button id="make_new_post_review" class="btn btn-info btn-flat" type="button"><?=lang('PORTAL_next');?></button>
                    </span>
                  </div>
                  </div><!-- /.tab-pane -->
                </div><!-- /.tab-content -->
                
              </div>

<div class="box box-default" id="maybe" style="display:none;">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_maybe');?></h3>

                </div><!-- /.box-header -->

<div class="box-body">

<div class="row">
<div class="col-md-6" id="maybe_res">
  
</div>
<div class="col-md-6"><button class="btn btn-block btn-info btn-lg" id="new_post_page" value="1"><?=lang('PORTAL_now_new_post');?></button></div>
</div>

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


<div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title"><a class="text-success" href="<?=$CONF['hostname']."cat&1";?>"><?=lang('PORTAL_idea');?></a></h3>
                                    <div class="box-tools pull-right">
                    <h4> <i class="fa fa-lightbulb-o"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                  


                <div class="box-footer no-padding">

<?php 
            $stmt = $dbConnection->prepare('SELECT *
FROM portal_posts p 
    LEFT JOIN post_comments c on c.p_id = p.id
    where p.type=1
GROUP BY p.id
ORDER BY COALESCE(GREATEST(p.dt, MAX(c.dt)), p.dt) DESC
limit 3');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
            if (!empty($res1)) {

              ?>


                  <ul class="nav nav-pills nav-stacked">



<?php

 foreach ($res1 as $r) {
?>
<li>
 <a href="<?=$CONF['hostname']."thread&".$r['uniq_id'];?>">
                    <strong style="
    font-size: 16px;
"><i class="fa fa-lightbulb-o"></i> <?=$r['subj'];?> </strong>

                    

<?=get_post_rate($r['uniq_id']);?>


<br>
                    <small class="text-muted">
                    <?=get_post_status($r['uniq_id']);?>  <!--● Чат поддержки-->  <?=lang('PORTAL_comments');?>: <?=get_count_comments($r['uniq_id']);?> ● <?=get_official_comments($r['uniq_id']);?></small><!--small class="pull-right text-muted">рейтинг</small-->
                    </a></li>






                    

<?php
}


?>


                    <li>

                   

                    <li><small class="text-muted"><a href="<?=$CONF['hostname']."cat&1";?>" class="text-muted"><?=lang('PORTAL_idea_all');?> (<?=get_total_posts_by_type('1');?>)</a></small></li>
                  </ul>

                  <?php  }
else if (empty($res1)) {

?>

<div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">

                    <center><?php
                    echo lang('MSG_no_records'); ?></center></div>


<?php }
                  ?>
                </div><!-- /.footer -->



                </div><!-- /.box-body -->
              </div>

<div class="box box-danger">
                <div class="box-header with-border">
                  <h3 class="box-title"><a class="text-danger" href="<?=$CONF['hostname']."cat&2";?>"><?=lang('PORTAL_trouble');?></a></h3>
                  <div class="box-tools pull-right">
                    <h4> <i class="fa fa-exclamation-triangle"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                                 <div class="box-footer no-padding">

<?php 
            $stmt = $dbConnection->prepare('SELECT *
FROM portal_posts p 
    LEFT JOIN post_comments c on c.p_id = p.id
    where p.type=2
GROUP BY p.id
ORDER BY COALESCE(GREATEST(p.dt, MAX(c.dt)), p.dt) DESC
limit 3');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
            if (!empty($res1)) {

              ?>


                  <ul class="nav nav-pills nav-stacked">



<?php

 foreach ($res1 as $r) {
?>
 <li>
 <a href="<?=$CONF['hostname']."thread&".$r['uniq_id'];?>">
                    <strong style="
    font-size: 16px;
"><i class="fa fa-exclamation-triangle"></i> <?=$r['subj'];?> </strong>

                    

<?=get_post_rate($r['uniq_id']);?>


<br>
                    <small class="text-muted">
                    <?=get_post_status($r['uniq_id']);?>  <!--● Чат поддержки--> <?=lang('PORTAL_comments');?>: <?=get_count_comments($r['uniq_id']);?> ● <?=get_official_comments($r['uniq_id']);?></small><!--small class="pull-right text-muted">рейтинг</small-->
                    </a></li>





                    

<?php
}


?>


                    <li>

                   

                    <li><small class="text-muted"><a href="<?=$CONF['hostname']."cat&2";?>" class="text-muted"><?=lang('PORTAL_trouble_all');?> (<?=get_total_posts_by_type('2');?>)</a></small></li>
                  </ul>

                  <?php  }
else if (empty($res1)) {

?>

<div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">

                    <center><?php
                    echo lang('MSG_no_records'); ?></center></div>


<?php }
                  ?>
                </div><!-- /.footer -->
                </div><!-- /.box-body -->
              </div>


<div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title"><a class="text-info" href="<?=$CONF['hostname']."cat&3";?>"><?=lang('PORTAL_question');?></a></h3>
                                    <div class="box-tools pull-right">
                    <h4> <i class="fa fa-question-circle"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                                   <div class="box-footer no-padding">

<?php 
            $stmt = $dbConnection->prepare('SELECT *
FROM portal_posts p 
    LEFT JOIN post_comments c on c.p_id = p.id
    where p.type=3
GROUP BY p.id
ORDER BY COALESCE(GREATEST(p.dt, MAX(c.dt)), p.dt) DESC
limit 3');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
            if (!empty($res1)) {

              ?>


                  <ul class="nav nav-pills nav-stacked">



<?php

 foreach ($res1 as $r) {
?>

                    <li>
 <a href="<?=$CONF['hostname']."thread&".$r['uniq_id'];?>">
                    <strong style="
    font-size: 16px;
"><i class="fa fa-question-circle"></i> <?=$r['subj'];?> </strong>

                    

<?=get_post_rate($r['uniq_id']);?>


<br>
                    <small class="text-muted">
                    <?=lang('PORTAL_comments');?>: <?=get_count_comments($r['uniq_id']);?> ● <?=get_official_comments($r['uniq_id']);?></small><!--small class="pull-right text-muted">рейтинг</small-->
                    </a></li>

<?php
}


?>


                    <li>

                   

                    <li><small class="text-muted"><a href="<?=$CONF['hostname']."cat&3";?>" class="text-muted"><?=lang('PORTAL_question_all');?> (<?=get_total_posts_by_type('3');?>)</a></small></li>
                  </ul>

                  <?php  }
else if (empty($res1)) {

?>

<div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">

                    <center><?php
                    echo lang('MSG_no_records'); ?></center></div>


<?php }
                  ?>
                </div><!-- /.footer -->
                </div><!-- /.box-body -->
              </div>

<div class="box box-warning">
                <div class="box-header with-border">
                  <h3 class="box-title"><a class="text-warning" href="<?=$CONF['hostname']."cat&4";?>"><?=lang('PORTAL_thank');?></a></h3>
                   <div class="box-tools pull-right">
                    <h4> <i class="fa fa-heart"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                   <div class="box-footer no-padding">

<?php 
            $stmt = $dbConnection->prepare('SELECT *
FROM portal_posts p 
    LEFT JOIN post_comments c on c.p_id = p.id
    where p.type=4
GROUP BY p.id
ORDER BY COALESCE(GREATEST(p.dt, MAX(c.dt)), p.dt) DESC
limit 3');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
            if (!empty($res1)) {

              ?>


                  <ul class="nav nav-pills nav-stacked">



<?php

 foreach ($res1 as $r) {
?>

                    <li>
 <a href="<?=$CONF['hostname']."thread&".$r['uniq_id'];?>">
                    <strong style="
    font-size: 16px;
"><i class="fa fa-heart"></i> <?=$r['subj'];?> </strong>

                    

<?=get_post_rate($r['uniq_id']);?>


<br>
                    <small class="text-muted">
                     <?=lang('PORTAL_comments');?>: <?=get_count_comments($r['uniq_id']);?> ● <?=get_official_comments($r['uniq_id']);?></small><!--small class="pull-right text-muted">рейтинг</small-->
                    </a></li>

<?php
}


?>


                    <li>

                   

                    <li><small class="text-muted"><a href="<?=$CONF['hostname']."cat&4";?>" class="text-muted"><?=lang('PORTAL_thank_all');?> (<?=get_total_posts_by_type('4');?>)</a></small></li>
                  </ul>

                  <?php  }
else if (empty($res1)) {

?>

<div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">

                    <center><?php
                    echo lang('MSG_no_records'); ?></center></div>


<?php }
                  ?>
                </div><!-- /.footer -->

                </div><!-- /.box-body -->
              </div>


</div>



<div class="col-md-3">

<?=view_release_bar();?>




<?=view_top_news_bar();?>




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