

<?php

include "head.inc.php";


include "navbar.inc.php";
$rkeys = array_keys($_GET);


$hn = $rkeys[1];

if (!$hn) {
  $hn=1;
}







switch ($hn) {
  case '1':
    $t=lang('PORTAL_idea');
    $s="box-success";
    break;
    case '2':
    $t=lang('PORTAL_trouble');
    $s="box-danger";
    break;
      case '3':
    $t=lang('PORTAL_question');
    $s="box-info";
    break;
      case '4':
    $t=lang('PORTAL_thank');
    $s="box-warning";
    break;
  default:
    # code...
    break;
}

if (!$_GET['p']) {
  $p=1;
}
else if ($_GET['p']) {
  $p=$_GET['p'];
}


        $page = ($p);
        $perpage = '10';
        $start_pos = ($page - 1) * $perpage;

?>
<div class="content-wrapper">
<section class="content">





<section class="invoice">
          <!-- title row -->
          <div class="row">



<div class="col-md-9">







<div class="box <?=$s;?>">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=$t;?></h3>
                                    <div class="box-tools pull-right">
                    <h4> <?=get_cat_icon($hn);?></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                  


                <div class="box-footer no-padding">

<?php 

if (isset($_GET['status'])) {
            $stmt = $dbConnection->prepare('SELECT *
FROM portal_posts p 
    LEFT JOIN post_comments c on c.p_id = p.id
    where p.type=:t and p.status=:s
GROUP BY p.id
ORDER BY COALESCE(GREATEST(p.dt, MAX(c.dt)), p.dt) DESC
limit :start_pos, :perpage');
            $stmt->execute(array(':start_pos' => $start_pos, ':perpage' => $perpage, ':t'=> $hn, ':s'=>$_GET['status']));
}

else if (!isset($_GET['status'])) {

            $stmt = $dbConnection->prepare('SELECT *
FROM portal_posts p 
    LEFT JOIN post_comments c on c.p_id = p.id
    where p.type=:t
GROUP BY p.id
ORDER BY COALESCE(GREATEST(p.dt, MAX(c.dt)), p.dt) DESC
limit :start_pos, :perpage');
            $stmt->execute(array(':start_pos' => $start_pos, ':perpage' => $perpage, ':t'=> $hn));
          }






            $res1 = $stmt->fetchAll();
            if (!empty($res1)) {

              ?>


                  <ul class="nav nav-pills nav-stacked">



<?php

 foreach ($res1 as $r) {




?>

                    <li>
 <a href="<?=$CONF['hostname']."thread?".$r['uniq_id'];?>">
                    <strong style="
    font-size: 16px;
"><?=get_cat_icon($r['type']);?> <?=$r['subj'];?> </strong>

                    

<?=get_post_rate($r['uniq_id']);?>


<br>
                    <small class="text-muted">
                     <?=get_post_status($r['uniq_id']);?>  <?=lang('PORTAL_comments');?>: <?=get_count_comments($r['uniq_id']);?> ● <?=get_official_comments($r['uniq_id']);?></small><!--small class="pull-right text-muted">рейтинг</small-->
                    </a></li>

<?php
}


?>


                    <li>

                   
<br>
                    <li class="pull-right">


<ul id="cat_post" class="pagination pagination-sm pull-right no-margin "></ul>



                    </li>
                  </ul>
          

<?php

?>
          
            <input type="hidden" id="curent_page" value="<?=$p;?>">
            <input type="hidden" id="cur_page" value="<?=$p;?>">
            <input type="hidden" id="cat" value="<?=$hn;?>">

<?php











if (isset($_GET['status'])) { ?>
<input type="hidden" id="total_pages" value="<?=get_total_pages_posts_status($hn,$_GET['status']);?>">
<input type="hidden" id="st_str" value="&status=<?=$_GET['status'];?>">
<?php

}
else if (!isset($_GET['status'])) { ?>
<input type="hidden" id="total_pages" value="<?php
        echo get_total_pages_posts($hn); ?>">
        <input type="hidden" id="st_str" value="">
<?php
  
}

?>
            



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
  <style type="text/css">
.active-text-cat {
  color: white;
}

  </style>
<?php



$status_label['def']="active";

if (isset($_GET['status'])) {

  switch ($_GET['status']) {
    case '0':
      $status_label['0']="active";
      $status_text['0']="active-text-cat";
      $status_label['def']="";
      break;
          case '1':
      $status_label['1']="active";
      $status_text['1']="active-text-cat";
      $status_label['def']="";
      break;
          case '2':
      $status_label['2']="active";
      $status_text['2']="active-text-cat";
      $status_label['def']="";
      break;
          case '3':
      $status_label['3']="active";
      $status_text['3']="active-text-cat";
      $status_label['def']="";
      break;
          case '4':
      $status_label['4']="active";
      $status_text['4']="active-text-cat";
      $status_label['def']="";
      break;
    
    default:
      $status_label['def']="active";
      break;
  }

}

if ($hn == "1") {






  ?>

<div class="list-group">
  <a href="cat?1" class="list-group-item <?=$status_label['def'];?>">
 
    Все идеи
  </a>
  <a href="cat?1&status=0" class="list-group-item <?=$status_label['0'];?>"><?=get_count_post('1', '0');?><span class=""><?=lang('PORTAL_status_1');?></span></a>
  <a href="cat?1&status=1" class="list-group-item <?=$status_label['1'];?>"><?=get_count_post('1', '1');?><span class="text-warning <?=$status_text['1'];?>"><?=lang('PORTAL_status_2');?></span></a>
  <a href="cat?1&status=2" class="list-group-item <?=$status_label['2'];?>"><?=get_count_post('1', '2');?><span class="text-success <?=$status_text['2'];?>"><?=lang('PORTAL_status_3');?></span></a>
  <a href="cat?1&status=3" class="list-group-item <?=$status_label['3'];?>"><?=get_count_post('1', '3');?><span class="text-danger <?=$status_text['3'];?>"><?=lang('PORTAL_status_4');?></span></a>
    <a href="cat?1&status=4" class="list-group-item <?=$status_label['4'];?>"><?=get_count_post('1', '4');?><span class="text-primary <?=$status_text['4'];?>"><?=lang('PORTAL_status_5');?></span></a>
</div>
  <?php
}

else if ( $hn == "2" ) {

  ?>
<div class="list-group">
  <a href="cat?2" class="list-group-item <?=$status_label['def'];?>">
  
    Все проблемы
  </a>
  <a href="cat?2&status=0" class="list-group-item <?=$status_label['0'];?>"><?=get_count_post('2', '0');?><span class=""><?=lang('PORTAL_status_1');?></span></a>
  <a href="cat?2&status=1" class="list-group-item <?=$status_label['1'];?>"><?=get_count_post('2', '1');?><span class="text-warning <?=$status_text['1'];?>"><?=lang('PORTAL_status_2');?></span></a>
  <a href="cat?2&status=2" class="list-group-item <?=$status_label['2'];?>"><?=get_count_post('2', '2');?><span class="text-success <?=$status_text['2'];?>"><?=lang('PORTAL_status_6');?></span></a>
  <a href="cat?2&status=3" class="list-group-item <?=$status_label['3'];?>"><?=get_count_post('2', '3');?><span class="text-danger <?=$status_text['3'];?>"><?=lang('PORTAL_status_7');?></span></a>
    <a href="cat?2&status=4" class="list-group-item <?=$status_label['4'];?>"><?=get_count_post('2', '4');?><span class="text-primary <?=$status_text['4'];?>"><?=lang('PORTAL_status_5');?></span></a>
</div>
  <?php
}

?>




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