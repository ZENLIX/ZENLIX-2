<?php

function get_post_val_by_hash($id, $in) {
    
    //val.id
    global $CONF;
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT ' . $in . ' FROM portal_posts where uniq_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    
    $fior = $stmt->fetch(PDO::FETCH_NUM);
    
    return $fior[0];
}

function view_admin_menu($post_hash) {
    $post_id = get_post_val_by_hash($post_hash, 'id');
?>
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo lang('PORTAL_admin_menu'); ?></h3>

                </div><!-- /.box-header -->
                <div class="box-body">
   <div class="row">



            <div class="col-md-6">
<?php echo view_post_buttons_status($post_id); ?>
            </div><!-- /.col -->
            <div class="col-md-6">

<div class="btn-group-vertical ">
<button class="btn btn-xs bg-maroon main-post-del" value="<?php echo $post_hash; ?>"><?php echo lang('PORTAL_act_del'); ?></button> 
<button class="btn btn-xs bg-orange btn-flat main-post-edit" value="<?php echo $post_hash; ?>"><?php echo lang('PORTAL_act_edit'); ?></button>
 </div>


            </div>


<div class="col-md-12">
<br>
<?php echo lang('HELPER_cat'); ?>:<br>
<div class="btn-group-vertical ">
<button class="btn btn-xs btn-default make_cat_type" option="1" value="<?php echo $post_hash; ?>"><?php echo lang('PORTAL_idea_one'); ?></button> 
<button class="btn btn-xs btn-default make_cat_type" option="2" value="<?php echo $post_hash; ?>"><?php echo lang('PORTAL_trouble_one'); ?></button>
<button class="btn btn-xs btn-default make_cat_type" option="3" value="<?php echo $post_hash; ?>"><?php echo lang('PORTAL_question_one'); ?></button>
<button class="btn btn-xs btn-default make_cat_type" option="4" value="<?php echo $post_hash; ?>"><?php echo lang('PORTAL_thank_one'); ?></button>



 </div>

</div>



          </div>

                </div><!-- /.box-body -->

              </div>
  <?php
}

function view_maybe_block($post_hash) {
    global $dbConnection;
?>
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo lang('PORTAL_maybe_theme'); ?></h3>

                </div><!-- /.box-header -->
                <div class="box-body">
   <div class="row">
            <div class="col-md-12">
              


<?php
    $t = get_post_val_by_hash($post_hash, 'subj');
    
    $ex = explode(" ", $t);
    $empty = false;
    
    foreach ($ex as $value) {
        $stmt = $dbConnection->prepare("SELECT * from portal_posts where (portal_posts.subj like :t) and (portal_posts.uniq_id!=:u) limit 3");
        $stmt->execute(array(
            ':t' => '%' . $value . '%',
            ':u' => $post_hash
        ));
        $result = $stmt->fetchAll();
        echo "<ul>";
        foreach ($result as $row) {
            
            echo "<li style='list-style:none;'>" . get_cat_icon($row['type']) . " <a href=\"" . $CONF['hostname'] . "thread?" . $row['uniq_id'] . "\">" . $row['subj'] . "</a></li>";
            // code...
            
        }
        echo "</ul>";
    }
    
    foreach ($ex as $value) {
        $stmt = $dbConnection->prepare("SELECT * from portal_manual_cat where (name like :t) limit 3");
        $stmt->execute(array(
            ':t' => '%' . $value . '%'
        ));
        $result = $stmt->fetchAll();
        echo "<ul>";
        foreach ($result as $row) {
            
            echo "<li style='list-style:none;'><i class=\"fa fa-graduation-cap\"></i> <a href=\"" . $CONF['hostname'] . "manual?" . $row['uniq_id'] . "\">" . $row['name'] . "</a></li>";
            // code...
            
        }
        echo "</ul>";
    }
?>
            </div><!-- /.col -->
            
          </div>

                </div><!-- /.box-body -->

              </div>
<?php
}

function get_count_post($type, $status) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT count(id) as cou from portal_posts where type=:t and status=:s');
    $stmt->execute(array(
        ':t' => $type,
        ':s' => $status
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($tt['cou'] != 0) {
        $r = "<span class=\"badge\">" . $tt['cou'] . "</span>";
    } 
    else if ($tt['cou'] == 0) {
        $r = "";
    }
    
    return $r;
}

function get_count_news() {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT count(id) as cou from portal_news');
    $stmt->execute();
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $tt['cou'];
}

function view_top_news_bar() {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT * FROM portal_news order by dt desc limit 3');
    $stmt->execute();
    $res1 = $stmt->fetchAll();
?>
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo lang('PORTAL_news'); ?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-newspaper-o"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                <div class="">


 <ul class="products-list product-list-in-box ">


 <?php
    foreach ($res1 as $k) {
        // code...
        
        
?>
                    <li class="item">
                      
                      <div class="product-info" style="margin-left:0px;">
                        <a href="feed?<?php echo $k['uniq_id']; ?>" class="product-title"><?php echo $k['subj']; ?> </a>
                        <span class="product-description">
                         <small> <?php echo $k['title']; ?></small>
                        </span>
                        <small class="text-muted pull-right">
                        <i class="fa fa-clock-o"></i> <time id="d" datetime="<?php echo $k['dt']; ?>"></time>
                        </small>
                      </div>
                    </li><!-- /.item -->
                            <?php
    }
?>           
                    <li><a href="feed"><small class="text-muted"><?php echo lang('PORTAL_all_news'); ?> (<?php echo get_count_news(); ?>)</small></a></li>
                  </ul>

                </div><!-- /.footer -->
                </div>
                </div>
  <?php
}

function view_release_bar() {
?>
<div class="small-box bg-maroon">
                <div class="inner">
                  <h3><?php echo get_conf_param('portal_box_version_n'); ?></h3>
                  <p>
                  <?php echo get_conf_param('portal_box_version_text'); ?>
                  </p>
                </div>
                <div class="icon">
                  <span class="<?php echo get_conf_param('portal_box_version_icon'); ?>"></span>
                </div>
                <a href="version" class="small-box-footer">
                  <?php echo lang('PORTAL_more'); ?> <i class="fa fa-arrow-circle-right"></i>
                </a>
              </div>
<?php
}

function view_stat_cat() {
?>
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo lang('PORTAL_stat'); ?></h3>

                </div><!-- /.box-header -->
                <div class="box-body">
                  






<div class="row">
            <div class="col-md-12">
              <div class="info-box bg-green">
               <a href="cat?1" style="color:white;"> <span class="info-box-icon"> <i class="fa fa-lightbulb-o"></i></span></a>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo lang('PORTAL_idea'); ?></span>
                  <span class="info-box-number"><?php echo get_total_posts_by_type('1'); ?></span>
                  <div class="progress">
                    <div class="progress-bar" style="width: 70%"></div>
                  </div>
                  <span class="progress-description">
                    <?php echo lang('PORTAL_idea_stat'); ?>
                  </span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-12">
              <div class="info-box bg-red">
                <a href="cat?2" style="color:white;"><span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span></a>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo lang('PORTAL_trouble'); ?></span>
                  <span class="info-box-number"><?php echo get_total_posts_by_type('2'); ?></span>
                  <div class="progress">
                    <div class="progress-bar" style="width: 70%"></div>
                  </div>
                  <span class="progress-description">
                    <?php echo lang('PORTAL_trouble_stat'); ?>
                  </span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-12">
              <div class="info-box bg-blue">
                <a href="cat?3" style="color:white;"><span class="info-box-icon"><i class="fa fa-question-circle"></i></span></a>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo lang('PORTAL_question'); ?></span>
                  <span class="info-box-number"><?php echo get_total_posts_by_type('3'); ?></span>
                  <div class="progress">
                    <div class="progress-bar" style="width: 70%"></div>
                  </div>
                  <span class="progress-description">
                    <?php echo lang('PORTAL_question_stat'); ?>
                  </span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-12">
              <div class="info-box bg-orange">
               <a href="cat?4" style="color:white;"> <span class="info-box-icon"><i class="fa fa-heart"></i></span></a>
                <div class="info-box-content">
                  <span class="info-box-text"><?php echo lang('PORTAL_thank'); ?></span>
                  <span class="info-box-number"><?php echo get_total_posts_by_type('4'); ?></span>
                  <div class="progress">
                    <div class="progress-bar" style="width: 70%"></div>
                  </div>
                  <span class="progress-description">
                    <?php echo lang('PORTAL_thank_stat'); ?>
                  </span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
          </div>






                </div><!-- /.box-body -->

              </div>
  <?php
}

function view_likes_button($post_id) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT * from post_likes where post_id=:tm and user_id=:u');
    $stmt->execute(array(
        ':tm' => $post_id,
        ':u' => $_SESSION['helpdesk_user_id']
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($tt['likes']) {
        $v = $tt['likes'];
        
        if ($v == "2") {
            $s['like'] = "disabled";
        } 
        else if ($v == "1") {
            $s['dislike'] = "disabled";
        }
    } 
    else if (!$tt['likes']) {
        $s['like'] = "";
        $s['dislike'] = "";
    }
    if (check_validate() == true) {
?>
<div class="btn-group">
                          <button type="button" id="do_like" value="like" class="btn btn-xs btn-success <?php echo $s['like']; ?>"><i class="fa fa-thumbs-o-up"></i> <?php echo lang('PORTAL_post_like'); ?></button>
                          <button type="button" id="do_like" value="dislike" class="btn btn-xs btn-danger <?php echo $s['dislike']; ?>"><i class="fa fa-thumbs-o-down"></i> <?php echo lang('PORTAL_post_dislike'); ?></button>
                        </div>
<?php
    }
}

function get_total_pages_comments($cat) {
    global $dbConnection;
    $perpage = '10';
    
    $cat = get_post_val_by_hash($cat, 'id');
    $res = $dbConnection->prepare("SELECT count(*) from post_comments where p_id=:c and official=0");
    $res->execute(array(
        ':c' => $cat
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    $count = $count[0];
    
    if ($count <> 0) {
        $pages_count = ceil($count / $perpage);
        return $pages_count;
    } 
    else {
        $pages_count = 0;
        return $pages_count;
    }
    
    return $count;
}

function get_total_pages_posts_status($cat, $status) {
    global $dbConnection;
    $perpage = '10';
    if (!$cat) {
        $cat = 1;
    }
    
    $res = $dbConnection->prepare("SELECT count(*) from portal_posts where parent_id=0 and type=:t and status=:s");
    $res->execute(array(
        ':t' => $cat,
        ':s' => $status
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    $count = $count[0];
    
    if ($count <> 0) {
        $pages_count = ceil($count / $perpage);
        return $pages_count;
    } 
    else {
        $pages_count = 0;
        return $pages_count;
    }
    
    return $count;
}

function get_total_pages_posts($cat) {
    global $dbConnection;
    $perpage = '10';
    if (!$cat) {
        $cat = 1;
    }
    
    $res = $dbConnection->prepare("SELECT count(*) from portal_posts where parent_id=0 and type=:t");
    $res->execute(array(
        ':t' => $cat
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    $count = $count[0];
    
    if ($count <> 0) {
        $pages_count = ceil($count / $perpage);
        return $pages_count;
    } 
    else {
        $pages_count = 0;
        return $pages_count;
    }
    
    return $count;
}

function get_main_manual() {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT id, name, main,type, uniq_id, parent_id from portal_manual_cat where parent_id=0 and main=1 order by sort_id ASC');
    $stmt->execute();
    $re = $stmt->fetchAll();
    $i = 0;
    foreach ($re as $row) {
?>
<div class="col-md-6">
<p style='margin-bottom:5px;'>
<strong >
<i class="fa fa-graduation-cap"></i>
<?php
        if ($row['type'] == "0") {
            echo "<a href=\"manual?" . $row['uniq_id'] . "\">" . $row['name'] . "</a>";
        } 
        else if ($row['type'] == "1") {
            echo $row['name'];
        }
?></strong>
                                            </p>
<?php echo show_all_manual_main($row['id']); ?>
</div>
<?php
        $i++;
        
        if (($i == 2) || ($i == 4)) {
            echo "<div class=\"col-md-12\"></div>";
        }
    }
}

function show_all_manual_main($level = 0) {
    global $dbConnection;
    
    //$result = mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$level);
    
    $stmt = $dbConnection->prepare('SELECT id, name, main,type, uniq_id, parent_id from portal_manual_cat where parent_id=:p_id and main=1 order by sort_id ASC');
    $stmt->execute(array(
        ':p_id' => $level
    ));
    $re = $stmt->fetchAll();
    
    if ($level != 0) {
        echo "<ul>";
    } 
    else if ($level == 0) {
        echo "<ul>";
    }
    
    // while ($node = mysql_fetch_array($result)) {
    foreach ($re as $row) {
        
        if (($row['parent_id'] == "0") && (($row['type'] == "1"))) {
            echo "<br>";
        }
        
        //echo "<li id=\"list-".$row['id']."\"><div>".$row['name'];
        
        if ($row['parent_id'] != "0") {
            echo "<li style=\"list-style:none; padding: 3px 0 3px 0;\"><i class=\"fa fa-file-text-o\"></i> ";
        }
?>
                                        
                                            
       
                                            <?php
        if ($row['type'] == "0") {
            echo "<a href=\"manual?" . $row['uniq_id'] . "\">" . $row['name'] . "</a>";
        } 
        else if ($row['type'] == "1") {
            echo $row['name'];
        }
?>
                                                
                                            

                                            
                                       



                                        
        <?php
        
        //$hasChild = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$node['ID'])) != null;
        
        $stmt2 = $dbConnection->prepare('SELECT id, name from portal_manual_cat where parent_id=:p_id');
        $stmt2->execute(array(
            ':p_id' => $row['id']
        ));
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        //$hasChild=$row2['parent_id'];
        
        if ($row2) {
            show_all_manual_main($row['id']);
        }
        echo "</li>";
    }
    echo "</ul>";
}

function show_all_manual($level = 0) {
    global $dbConnection;
    
    //$result = mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$level);
    
    $stmt = $dbConnection->prepare('SELECT id, name, main,type, uniq_id, parent_id from portal_manual_cat where parent_id=:p_id order by sort_id ASC');
    $stmt->execute(array(
        ':p_id' => $level
    ));
    $re = $stmt->fetchAll();
    
    if ($level != 0) {
        echo "<ul>";
    } 
    else if ($level == 0) {
        echo "<ul style=\"padding-left: 5px;\">";
    }
    
    // while ($node = mysql_fetch_array($result)) {
    foreach ($re as $row) {
        
        if (($row['parent_id'] == "0") && (($row['type'] == "1"))) {
            echo "<i class=\"fa fa-graduation-cap\"></i>";
        }
        
        //echo "<li id=\"list-".$row['id']."\"><div>".$row['name'];
        
        if ($row['parent_id'] != "0") {
            echo "<li style=\"list-style:none; padding: 3px 0 3px 0;\"><i class=\"fa fa-file-text-o\"></i>";
        }
?>
                                        
                                           
       
                                            <?php
        if ($row['type'] == "0") {
            echo "<a href=\"manual?" . $row['uniq_id'] . "\">" . $row['name'] . "</a>";
        } 
        else if ($row['type'] == "1") {
            echo "<strong>" . $row['name'] . "</strong>";
        }
?>
                                                
                                            

                                            
                                        



                                        
        <?php
        
        //$hasChild = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$node['ID'])) != null;
        
        $stmt2 = $dbConnection->prepare('SELECT id, name from portal_manual_cat where parent_id=:p_id');
        $stmt2->execute(array(
            ':p_id' => $row['id']
        ));
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        //$hasChild=$row2['parent_id'];
        
        if ($row2) {
            show_all_manual($row['id']);
        }
        echo "</li>";
    }
    echo "</ul>";
}

function get_main_todo() {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT id, name, uniq_id, parent_id, is_success from portal_todo where parent_id=0 order by sort_id ASC');
    $stmt->execute();
    $re = $stmt->fetchAll();
    $i = 0;
    echo "<ul class=\"list-group\">";
    foreach ($re as $row) {
        
        if ($row['is_success'] == "1") {
            $style_act = "list-group-item-success";
            $style_icon = "fa-check-circle";
        } 
        else if ($row['is_success'] == "0") {
            $style_act = "";
            $style_icon = "fa-clock-o";
        }
?>

  <li class="list-group-item <?php echo $style_act; ?>" style="padding: 3px 10px;">
  <span class="pull-right " style="
    padding-left: 10px;
"><i style='font-size:15px;' class="fa <?php echo $style_icon; ?>"></i></span>
<div style='font-size:12px;'><?php echo $row['name']; ?></div>

                    </li>                        


<?php
        $i++;
    }
    echo "</ul>";
}

function showMenu_todo($level = 0) {
    global $dbConnection;
    
    //$result = mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$level);
    
    $stmt = $dbConnection->prepare('SELECT id, name, uniq_id, is_success from portal_todo where parent_id=:p_id order by sort_id ASC');
    $stmt->execute(array(
        ':p_id' => $level
    ));
    $re = $stmt->fetchAll();
    
    if ($level != 0) {
        echo "<ul>";
    } 
    else if ($level == 0) {
        echo "<ul class=\"todo-list sortable\" style=\"margin: 0px;\">";
    }
    
    // while ($node = mysql_fetch_array($result)) {
    foreach ($re as $row) {
        
        if ($row['is_success'] == "1") {
            $c1 = "checked";
        } 
        else if ($row['is_success'] == "0") {
            $c1 = "";
        }
        
        //echo "<li id=\"list-".$row['id']."\"><div>".$row['name'];
        
?>
                                        <li id="list_<?php echo $row['id']; ?>">
                                            <div>
                                            <!-- drag handle -->
                                            <span class="handle ui-sortable-handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <!-- checkbox -->
                                            
                                            <!-- todo text -->
                                            <span class="text" id="val_<?php echo $row['id']; ?>">
                                        <a href="#" data-pk="<?php echo $row['id']; ?>" data-url="portal_action" id="edit_item_todo" data-type="text" style="width:400px;">
                                                <?php echo $row['name']; ?>
                                            </a> 
                                            </span>

                                            <!-- General tools such as edit or delete-->
                                            <span class="tools">
                                         <label style="padding-right:20px;">
                    <input id="make_todo_success" name="" value="<?php echo $row['id']; ?>" type="checkbox" <?php echo $c1; ?>> <small><?php echo lang('PORTAL_success'); ?></small>
                </label>    
                   <!--i id="edit_todo" value="<?php echo $row['uniq_id']; ?>" class="fa fa-pencil-square-o"></i-->
                 
                
            <i id="del_item_todo" value="<?php echo $row['id'] ?>" class="fa fa-trash-o"></i>
                                              


                                                
                                            </span>
                                        </div>
                                        
        <?php
        
        //$hasChild = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$node['ID'])) != null;
        
        $stmt2 = $dbConnection->prepare('SELECT id, name from portal_todo where parent_id=:p_id');
        $stmt2->execute(array(
            ':p_id' => $row['id']
        ));
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        //$hasChild=$row2['parent_id'];
        
        if ($row2) {
            showMenu_todo($row['id']);
        }
        echo "</li>";
    }
    echo "</ul>";
}

function showMenu_qa($level = 0) {
    global $dbConnection;
    
    //$result = mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$level);
    
    $stmt = $dbConnection->prepare('SELECT id, question, uniq_id from portal_manual_qa where parent_id=:p_id order by sort_id ASC');
    $stmt->execute(array(
        ':p_id' => $level
    ));
    $re = $stmt->fetchAll();
    
    if ($level != 0) {
        echo "<ul>";
    } 
    else if ($level == 0) {
        echo "<ul class=\"todo-list sortable\" style=\"margin: 0px;\">";
    }
    
    // while ($node = mysql_fetch_array($result)) {
    foreach ($re as $row) {
        
        //echo "<li id=\"list-".$row['id']."\"><div>".$row['name'];
        
?>
                                        <li id="list_<?php echo $row['id']; ?>">
                                            <div>
                                            <!-- drag handle -->
                                            <span class="handle ui-sortable-handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <!-- checkbox -->
                                            
                                            <!-- todo text -->
                                            <span class="text" id="val_<?php echo $row['id']; ?>">
                                        <a href="#" data-pk="<?php echo $row['id']; ?>" data-url="portal_action" id="edit_item_qa" data-type="text" class="">
                                                <?php echo $row['question']; ?>
                                            </a> 
                                            </span>

                                            <!-- General tools such as edit or delete-->
                                            <span class="tools">
                                             
                   
                 
                <i id="edit_manual_qa" value="<?php echo $row['uniq_id']; ?>" class="fa fa-pencil-square-o"></i>
            <i id="del_item_qa" value="<?php echo $row['id'] ?>" class="fa fa-trash-o"></i>
                                              


                                                
                                            </span>
                                        </div>
                                        
        <?php
        
        //$hasChild = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$node['ID'])) != null;
        
        $stmt2 = $dbConnection->prepare('SELECT id, question from portal_manual_qa where parent_id=:p_id');
        $stmt2->execute(array(
            ':p_id' => $row['id']
        ));
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        //$hasChild=$row2['parent_id'];
        
        if ($row2) {
            showMenu_qa($row['id']);
        }
        echo "</li>";
    }
    echo "</ul>";
}

function show_qa_manual() {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT question,answer, uniq_id from portal_manual_qa order by sort_id ASC');
    $stmt->execute();
    $re = $stmt->fetchAll();
?>

<div class="box-group" id="accordion">
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->

                    <?php
    $i = 0;
    foreach ($re as $row) {
        $fel = "";
        if ($i == 0) {
            $fel = "in";
        }
?>
                    <div class="panel box box-default">
                      <div class="box-header with-border">
                        <h4 class="box-title">
                          <a data-toggle="collapse" data-parent="#accordion" href="#collapse_<?php echo $row['uniq_id']; ?>">
                            <?php echo $row['question']; ?>
                          </a>
                        </h4>
                        <div class="box-tools pull-right">
                        <small><a href="manual?qa=<?php echo $row['uniq_id']; ?>"><i class="fa fa-link"></i> <?php echo lang('PORTAL_adr'); ?> </a></small>
                        </div>
                      </div>
                      <div id="collapse_<?php echo $row['uniq_id']; ?>" class="panel-collapse collapse <?php echo $fel; ?>">
                        <div class="box-body">
                          <?php echo $row['answer']; ?>
                        </div>
                      </div>
                    </div>
                    <?php
        $i++;
    } ?>
                    
                  </div>
<?php
}

function showMenu_manual($level = 0) {
    global $dbConnection;
    
    //$result = mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$level);
    
    $stmt = $dbConnection->prepare('SELECT id, name, main,type, uniq_id from portal_manual_cat where parent_id=:p_id order by sort_id ASC');
    $stmt->execute(array(
        ':p_id' => $level
    ));
    $re = $stmt->fetchAll();
    
    if ($level != 0) {
        echo "<ul>";
    } 
    else if ($level == 0) {
        echo "<ul class=\"todo-list sortable\" style=\"margin: 0px;\">";
    }
    
    // while ($node = mysql_fetch_array($result)) {
    foreach ($re as $row) {
        
        if ($row['main'] == "1") {
            $c = "checked";
        } 
        else if ($row['main'] == "0") {
            $c = "";
        }
        
        if ($row['type'] == "1") {
            $c1 = "checked";
        } 
        else if ($row['type'] == "0") {
            $c1 = "";
        }
        
        //echo "<li id=\"list-".$row['id']."\"><div>".$row['name'];
        
?>
                                        <li id="list_<?php echo $row['id']; ?>">
                                            <div>
                                            <!-- drag handle -->
                                            <span class="handle ui-sortable-handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <!-- checkbox -->
                                            
                                            <!-- todo text -->
                                            <span class="text" id="val_<?php echo $row['id']; ?>">
                                        <a href="#" data-pk="<?php echo $row['id']; ?>" data-url="portal_action" id="edit_item" data-type="text" class="">
                                                <?php echo $row['name']; ?>
                                            </a> 
                                            </span>

                                            <!-- General tools such as edit or delete-->
                                            <span class="tools">
                                             
                   <label style="padding-right:20px;">
                    <input id="make_cat_manual" name="" value="<?php echo $row['id']; ?>" type="checkbox" <?php echo $c1; ?>> <small><?php echo lang('PORTAL_cat'); ?></small>
                </label>


                <label style="padding-right:20px;">
                    <input id="make_main_manual" name="" value="<?php echo $row['id']; ?>" type="checkbox" <?php echo $c; ?>> <small><?php echo lang('PORTAL_on_main'); ?></small>
                </label>
                <i id="open_link" value="<?php echo $row['uniq_id']; ?>" class="fa fa-external-link"></i> 
                <i id="edit_manual_cat" value="<?php echo $row['uniq_id']; ?>" class="fa fa-pencil-square-o"></i>
            <i id="del_item" value="<?php echo $row['id'] ?>" class="fa fa-trash-o"></i>
                                              


                                                
                                            </span>
                                        </div>
                                        
        <?php
        
        //$hasChild = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$node['ID'])) != null;
        
        $stmt2 = $dbConnection->prepare('SELECT id, name from portal_manual_cat where parent_id=:p_id');
        $stmt2->execute(array(
            ':p_id' => $row['id']
        ));
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        //$hasChild=$row2['parent_id'];
        
        if ($row2) {
            showMenu_manual($row['id']);
        }
        echo "</li>";
    }
    echo "</ul>";
}

function get_portal_helper_cat() {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT * FROM portal_manual_cat order by sort_id ASC');
    $stmt->execute();
    $res1 = $stmt->fetchAll();
    return $res1;
}

//get_qa_arr

function get_qa_obj($uniq_id) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT * from portal_manual_qa where uniq_id=:id');
    $stmt->execute(array(
        ':id' => $uniq_id
    ));
    $res1 = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res1;
}

function count_items_manual($id) {
    global $dbConnection;
    
    $res = $dbConnection->prepare('SELECT count(*) from portal_manual where cat_id=:id');
    $res->execute(array(
        ':id' => $id
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    $res_m = $count[0];
    
    return $res_m;
}
function get_cat_icon($id) {
    global $dbConnection;
    switch ($id) {
        case '1':
            $icon = '<i class="fa fa-lightbulb-o"></i>';
            
            break;

        case '2':
            $icon = '<i class="fa fa-exclamation-triangle"></i>';
            
            break;

        case '3':
            $icon = '<i class="fa fa-question-circle"></i>';
            break;

        case '4':
            $icon = '<i class="fa fa-heart"></i>';
            break;

        default:
            // code...
            break;
    }
    
    return $icon;
}

function get_total_posts_by_type($type) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT count(id) as cou from portal_posts where type=:tm and parent_id=0');
    $stmt->execute(array(
        ':tm' => $type
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $tt['cou'];
}

function get_count_comments($id) {
    global $dbConnection;
    
    $id2 = get_post_val_by_hash($id, 'id');
    $stmt = $dbConnection->prepare('SELECT count(id) as cou from post_comments where p_id=:tm');
    $stmt->execute(array(
        ':tm' => $id2
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $tt['cou'];
}

function view_post_buttons_status($id) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT status, type from portal_posts where id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($tt['type'] == "1") {
        
        if ($tt['status'] == "0") {
            $status['0'] = "active";
        } 
        else if ($tt['status'] == "1") {
            $status['1'] = "active";
        } 
        else if ($tt['status'] == "2") {
            $status['2'] = "active";
        } 
        else if ($tt['status'] == "3") {
            $status['3'] = "active";
        } 
        else if ($tt['status'] == "4") {
            $status['4'] = "active";
        }
        
        //echo $tt['status'];
        
?>
              <div class="btn-group-vertical btn-block btn-xs">
                          <button type="button" id="make_post_status" value="0" class="btn <?php echo $status['0']; ?> btn-default btn-xs"><?php echo lang('PORTAL_status_1'); ?></button>
                          <button type="button" id="make_post_status" value="1" class="btn <?php echo $status['1']; ?> btn-warning btn-xs"><?php echo lang('PORTAL_status_2'); ?></button>
                          <button type="button" id="make_post_status" value="2" class="btn <?php echo $status['2']; ?> btn-success btn-xs"><?php echo lang('PORTAL_status_3'); ?></button>
                          <button type="button" id="make_post_status" value="3" class="btn <?php echo $status['3']; ?> btn-danger btn-xs"><?php echo lang('PORTAL_status_4'); ?></button>
                          <button type="button" id="make_post_status" value="4" class="btn <?php echo $status['4']; ?> btn-primary btn-xs"><?php echo lang('PORTAL_status_5'); ?></button>
                        </div>
    <?php
    } 
    else if ($tt['type'] == "2") {
        
        if ($tt['status'] == "0") {
            $status['0'] = "active";
        } 
        else if ($tt['status'] == "1") {
            $status['1'] = "active";
        } 
        else if ($tt['status'] == "2") {
            $status['2'] = "active";
        } 
        else if ($tt['status'] == "3") {
            $status['3'] = "active";
        } 
        else if ($tt['status'] == "4") {
            $status['4'] = "active";
        }
        
        //echo $tt['status'];
        
?>

       


              <div class="btn-group-vertical btn-block btn-xs">
                          <button type="button" id="make_post_status" value="0" class="btn <?php echo $status['0']; ?> btn-default btn-xs"><?php echo lang('PORTAL_status_1'); ?></button>
                          <button type="button" id="make_post_status" value="1" class="btn <?php echo $status['1']; ?> btn-warning btn-xs"><?php echo lang('PORTAL_status_2'); ?></button>
                          <button type="button" id="make_post_status" value="2" class="btn <?php echo $status['2']; ?> btn-success btn-xs"><?php echo lang('PORTAL_status_6'); ?></button>
                          <button type="button" id="make_post_status" value="3" class="btn <?php echo $status['3']; ?> btn-danger btn-xs"><?php echo lang('PORTAL_status_7'); ?></button>
                          <button type="button" id="make_post_status" value="4" class="btn <?php echo $status['4']; ?> btn-primary btn-xs"><?php echo lang('PORTAL_status_5'); ?></button>
                        </div>
    <?php
    }
}

function check_validate() {
    $validate = false;
    if ((validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) || (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code']))) {
        $validate = true;
    }
    
    return $validate;
}

function get_version_array() {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT * FROM portal_versions order by dt desc');
    $stmt->execute();
    $res1 = $stmt->fetchAll();
    
    return $res1;
}

function get_version_info($u) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT * FROM portal_versions where uniq_id=:u');
    $stmt->execute(array(
        ':u' => $u
    ));
    $res1 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $res1;
}

function get_manual_info($u) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT * FROM portal_manual_cat where uniq_id=:u');
    $stmt->execute(array(
        ':u' => $u
    ));
    $res1 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $res1;
}

function get_news_array() {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT * FROM portal_news order by dt desc limit 20');
    $stmt->execute();
    $res1 = $stmt->fetchAll();
    
    return $res1;
}

function get_news_info($u) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT * FROM portal_news where uniq_id=:u');
    $stmt->execute(array(
        ':u' => $u
    ));
    $res1 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $res1;
}
function view_attach_files($id, $type) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT * FROM post_files where post_hash=:tid');
    $stmt->execute(array(
        ':tid' => $id
    ));
    $res1 = $stmt->fetchAll();
    if (!empty($res1)) {
?>
                    
                        <div class="well" style="padding:5px;">
                            <table class="table table-hover" style="margin-bottom: 0px;">
                                    <tbody>
                                <?php
        foreach ($res1 as $r) {
            
            $fts = array(
                'image/jpeg',
                'image/gif',
                'image/png'
            );
            
            if (in_array($r['file_type'], $fts)) {
                
                $ct = ' <a class=\'fancybox\' href=\'' . $CONF['hostname'] . 'upload_files/' . $r['file_hash'] . '.' . $r['file_ext'] . '\'>' . $r['original_name'] . '</a> ';
            } 
            else {
                $ct = $r['original_name'];
            }
?>
                                    
                                    
                                    
                    <tr>
                        <td style="width:20px;"><small><?php
            echo get_file_icon($r['file_hash']); ?></small></td>
                        <td><small><?php
            echo $ct; ?></small><small> (<?php
            echo round(($r['file_size'] / (1024 * 1024)) , 2); ?> Mb)</small></td>
                        <td class="pull-right">

<?php
            if (check_validate() == false) {
                echo "<small class='text-muted'>" . lang('PORTAL_auth_failed') . "</small>";
            }
            if (check_validate() == true) {
?><div class="btn-group ">
                        <a href='<?php
                echo $CONF['hostname']; ?>portal_action?download_file&file=<?php
                echo $r['file_hash']; ?>' class="btn btn-sm btn-default"><?php echo lang('PORTAL_download'); ?></a>
                        <?php
                if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
                        <a id="delete_file" value="<?php
                    echo $r['file_hash']; ?>" class="btn btn-sm btn-default"><?php echo lang('PORTAL_delete'); ?></a>
                        <?php
                } ?> 
                        </div>
<?php
            } ?>

                        </td>
                    </tr>
<?php
        } ?>
                                    </tbody>
                            </table>
</div>
                        


                <?php
    }
}

function verify_uploaded_files($uniq_id) {
    global $dbConnection;
    
    //make_new_post_data
    $stmt = $dbConnection->prepare('update post_files set is_tmp=1 where post_hash=:hn');
    $stmt->execute(array(
        ':hn' => $uniq_id
    ));
    
    $stmt2 = $dbConnection->prepare('SELECT * FROM post_files WHERE is_tmp=0');
    $stmt2->execute();
    $result = $stmt2->fetchAll();
    if (!empty($result)) {
        
        foreach ($result as $row) {
            
            $stmt = $dbConnection->prepare("delete FROM post_files where is_tmp=0");
            $stmt->execute();
            unlink(realpath(dirname(__FILE__)) . "/upload_files/" . $row['file_hash'] . "." . $row['file_ext']);
        }
    }
}

function get_official_comments($id) {
    global $dbConnection;
    $id = get_post_val_by_hash($id, 'id');
    $stmt = $dbConnection->prepare('SELECT * from post_comments where p_id=:tm and official=1');
    $stmt->execute(array(
        ':tm' => $id
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($tt['id']) {
        $r = lang('PORTAL_official') . " <strong>" . nameshort(name_of_user_ret_nolink($tt['user_id'])) . "</strong> в <time id=\"c\" datetime=\"" . $tt['dt'] . "\"></time>";
    } 
    else {
        $r = lang('PORTAL_no_official');
    }
    return $r;
}
function get_post_rate_post($id) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT rates FROM portal_posts where uniq_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    $rate = "";
    
    if ($post['rates'] > 0) {
        $rate = "<span class=\"text-green\"><i style=\"
    font-size: 15px;\" class=\"fa fa-thumbs-o-up\"></i> " . $post['rates'] . " " . lang('PORTAL_liked_count') . "</span>";
    } 
    else if ($post['rates'] < 0) {
        
        $r = $post['rates'];
        
        $rate = "<span class=\"text-red\"><i style=\"
    font-size: 15px;\" class=\"fa fa-thumbs-o-down\"></i> " . abs($post['rates']) . " " . lang('PORTAL_disliked_count') . " </span>";
    }
    
    return $rate;
}

function get_post_rate($id) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT rates FROM portal_posts where uniq_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    $rate = "";
    
    if ($post['rates'] > 0) {
        $rate = "<span class=\"pull-right text-green\" style=\"
    font-size: 18px;
\">" . $post['rates'] . " <i style=\"
    font-size: 15px;\" class=\"fa fa-thumbs-o-up\"></i></span>";
    } 
    else if ($post['rates'] < 0) {
        
        $r = $post['rates'];
        
        $rate = "<span class=\"pull-right text-red\" style=\"
    font-size: 18px;
\">" . abs($post['rates']) . " <i style=\"
    font-size: 15px;\" class=\"fa fa-thumbs-o-down\"></i></span>";
    }
    
    return $rate;
}

function get_post_status($id, $v) {
    global $dbConnection;
    
    if ($v) {
        $c = "";
    } 
    else if (!$v) {
        $c = " ●";
    }
    
    $stmt = $dbConnection->prepare('SELECT status,type FROM portal_posts where uniq_id=:id and parent_id=0');
    $stmt->execute(array(
        ':id' => $id
    ));
    
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    $status = "";
    if ($post['type'] == 1) {
        if ($post['status'] == "0") {
            $status = '<span class="label label-default">' . lang('PORTAL_status_1') . '</span>';
        } 
        else if ($post['status'] == "1") {
            $status = '<span class="label label-warning">' . lang('PORTAL_status_2') . '</span>';
        } 
        else if ($post['status'] == "2") {
            $status = '<span class="label label-success">' . lang('PORTAL_status_3') . '</span>';
        } 
        else if ($post['status'] == "3") {
            $status = '<span class="label label-danger">' . lang('PORTAL_status_4') . '</span>';
        } 
        else if ($post['status'] == "4") {
            $status = '<span class="label label-primary">' . lang('PORTAL_status_5') . '</span>';
        }
        
        $status = $status . $c;
    } 
    else if ($post['type'] == 2) {
        if ($post['status'] == "0") {
            $status = '<span class="label label-default">' . lang('PORTAL_status_1') . '</span>';
        } 
        else if ($post['status'] == "1") {
            $status = '<span class="label label-warning">' . lang('PORTAL_status_2') . '</span>';
        } 
        else if ($post['status'] == "2") {
            $status = '<span class="label label-success">' . lang('PORTAL_status_6') . '</span>';
        } 
        else if ($post['status'] == "3") {
            $status = '<span class="label label-danger">' . lang('PORTAL_status_7') . '</span>';
        } 
        else if ($post['status'] == "4") {
            $status = '<span class="label label-primary">' . lang('PORTAL_status_5') . '</span>';
        }
        $status = $status . $c;
    }
    
    return $status;
}
?>