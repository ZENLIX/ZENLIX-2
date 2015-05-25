<?php



if (isset($_GET['mode'])) {
    $mode = ($_GET['mode']);



if ($mode == "download_file") {
$hn=$_GET['file'];
//echo $hn;

 $stmt = $dbConnection->prepare('SELECT original_name,file_type,file_ext, file_size from post_files where file_hash=:file_hash LIMIT 1');
    $stmt->execute(array(':file_hash' => $hn));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $original_name=$row['original_name'];
    $file_type=$row['file_type'];
    $file_ext=$row['file_ext'];
    $file_size=$row['file_size'];
    //echo($original_name." ".$file_type);
    
    
    
    //echo $original_name;
    if (file_exists("upload_files/".$hn.".".$file_ext)) {
      header("Content-Type: ".$file_type);
      header("Content-Disposition:  attachment; filename=\"" . $original_name . "\";" );
      header("Content-Transfer-Encoding:  binary");

      header('Content-Length: ' . $file_size);
      ob_clean();
      flush();
      readfile("upload_files/".$hn.".".$file_ext);
      exit;
          }
}




}






if (isset($_POST['mode'])) {
    $validate = false;
    if ((validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) || (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code']))) {
        $validate = true;
    }
    
    $mode = ($_POST['mode']);
    
    if ($validate == true) {
        if ($mode == "delete_post_file") {
            $uniq_code = $_POST['uniq_code'];
            
            $stmt = $dbConnection->prepare("SELECT *
                            from post_files where file_hash=:id");
            $stmt->execute(array(
                ':id' => $uniq_code
            ));
            $result = $stmt->fetchAll();
            
            if (!empty($result)) {
                foreach ($result as $row) {
                    
                    unlink(realpath(dirname(dirname(dirname(__FILE__)))) . "/upload_files/" . $row['file_hash'] . "." . $row['file_ext']);
                }
            }
            $stmt = $dbConnection->prepare('delete from post_files where file_hash=:id');
            $stmt->execute(array(
                ':id' => $uniq_code
            ));
        }
        
        if ($mode == "add_comment") {
            
            $validator = new GUMP();
            
            //$_POST = $validator->sanitize($_POST);
            
            $rules = array(
                'msg' => 'required|min_len,15'
            );
            $filters = array(
                'msg' => 'trim|basic_tags'
            );
            
            $validator->set_field_name(array(
                "msg" => lang('PORTAL_msg')
            ));
            
            GUMP::set_field_name("msg", lang('PORTAL_msg'));
            
            $_POST = $validator->filter($_POST, $filters);
            
            $validated = $validator->validate($_POST, $rules);
            
            if ($validated === true) {
                $check_error = true;
                
                //$check_error = true;
                
                if ($_POST['type'] == "true") {
                    $of = 1;
                } 
                else if ($_POST['type'] == "false") {
                    $of = 0;
                }
                
                $stmt = $dbConnection->prepare('insert into post_comments 
        (comment_text,
         dt,
         user_id,
         official, 
         p_id,
         uniq_hash
         ) values 
        (   :msg,
            :dt,
            :uid,
            :official,
            :p_id,
            :uniq_id)');
                $stmt->execute(array(
                    ':uniq_id' => $_POST['ch'],
                    ':msg' => $_POST['msg'],
                    ':dt' => $CONF['now_dt'],
                    ':uid' => $_SESSION['helpdesk_user_id'],
                    ':official' => $of,
                    ':p_id' => get_post_val_by_hash($_POST['ph'], 'id')
                ));
                send_notification_portal('portal_post_comment', get_post_val_by_hash($_POST['ph'], 'id'));
                
                verify_uploaded_files($_POST['ch']);
            } 
            else {
                
                //print_r($is_valid);
                $check_error = false;
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($validator->get_readable_errors(false) as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
            }
            
            //$msg="fakse";
            $results[] = array(
                'check_error' => $check_error,
                'msg' => $msg
            );
            print json_encode($results);
        }
        
        //set_post_cat
        
        if ($mode == "set_post_cat") {
            
            //print_r($_POST);
            
            $stmt = $dbConnection->prepare('update portal_posts set type=:s where uniq_id=:hn');
            $stmt->execute(array(
                ':hn' => $_POST['post_hash'],
                ':s' => $_POST['type']
            ));
        }
        
        if ($mode == "set_post_status") {
            
            //print_r($_POST);
            
            $stmt = $dbConnection->prepare('update portal_posts set status=:s where uniq_id=:hn');
            $stmt->execute(array(
                ':hn' => $_POST['post_hash'],
                ':s' => $_POST['val']
            ));
        }
        if ($mode == "save_manual_item") {
            
            $stmt = $dbConnection->prepare('UPDATE portal_manual_cat set name=:t where id=:el_id');
            $stmt->execute(array(
                ':t' => $_POST['value'],
                ':el_id' => $_POST['pk']
            ));
        }
        
        if ($mode == "save_todo") {
            
            $stmt = $dbConnection->prepare('UPDATE portal_todo set name=:t where id=:el_id');
            $stmt->execute(array(
                ':t' => $_POST['value'],
                ':el_id' => $_POST['pk']
            ));
        }
        
        if ($mode == "save_manual_item_qa") {
            
            $stmt = $dbConnection->prepare('UPDATE portal_manual_qa set question=:t where id=:el_id');
            $stmt->execute(array(
                ':t' => $_POST['value'],
                ':el_id' => $_POST['pk']
            ));
        }
        
        if ($mode == "items_view") {
            $stmt = $dbConnection->prepare('INSERT into portal_manual_cat (name, parent_id, sort_id, uniq_id, dt) 
                values (:n,:p,:s,:u,:dt)');
            $stmt->execute(array(
                ':n' => 'new item',
                ':p' => '0',
                ':s' => '100',
                ':u' => md5(time()) ,
                ':dt' => $CONF['now_dt']
            ));
            showMenu_manual();
        }
        
        if ($mode == "items_qa_view") {
            $stmt = $dbConnection->prepare('INSERT into portal_manual_qa (question, parent_id, sort_id, uniq_id, dt) 
                values (:n,:p,:s,:u,:dt)');
            $stmt->execute(array(
                ':n' => 'new item',
                ':p' => '0',
                ':s' => '100',
                ':u' => md5(time()) ,
                ':dt' => $CONF['now_dt']
            ));
            showMenu_qa();
        }
        
        if ($mode == "items_todo_view") {
            $stmt = $dbConnection->prepare('INSERT into portal_todo (name, parent_id, sort_id, uniq_id, dt) 
                values (:n,:p,:s,:u,:dt)');
            $stmt->execute(array(
                ':n' => 'new item',
                ':p' => '0',
                ':s' => '100',
                ':u' => md5(time()) ,
                ':dt' => $CONF['now_dt']
            ));
            showMenu_todo();
        }
        
        if ($mode == "change_todo_success") {
            
            if ($_POST['name'] == "true") {
                $h = 1;
            } 
            else if ($_POST['name'] == "false") {
                $h = 0;
            }
            
            $stmt = $dbConnection->prepare('UPDATE portal_todo set is_success=:t where id=:el_id');
            $stmt->execute(array(
                ':t' => $h,
                ':el_id' => $_POST['hash']
            ));
        }
        
        if ($mode == "change_manual_cat_main") {
            
            if ($_POST['name'] == "true") {
                $h = 1;
            } 
            else if ($_POST['name'] == "false") {
                $h = 0;
            }
            
            $stmt = $dbConnection->prepare('UPDATE portal_manual_cat set main=:t where id=:el_id');
            $stmt->execute(array(
                ':t' => $h,
                ':el_id' => $_POST['hash']
            ));
        }
        
        if ($mode == "change_manual_cat_type") {
            
            if ($_POST['name'] == "true") {
                $h = 1;
            } 
            else if ($_POST['name'] == "false") {
                $h = 0;
            }
            
            $stmt = $dbConnection->prepare('UPDATE portal_manual_cat set type=:t where id=:el_id');
            $stmt->execute(array(
                ':t' => $h,
                ':el_id' => $_POST['hash']
            ));
        }
        
        if ($mode == "helper_qa_del") {
            
            $stmt = $dbConnection->prepare('UPDATE portal_manual_qa set parent_id=:t where parent_id=:el_id');
            $stmt->execute(array(
                ':t' => '0',
                ':el_id' => $_POST['id']
            ));
            
            $stmt = $dbConnection->prepare('delete from portal_manual_qa where id=:n');
            $stmt->execute(array(
                ':n' => $_POST['id']
            ));
            
            showMenu_qa();
        }
        
        if ($mode == "helper_item_del") {
            
            $stmt = $dbConnection->prepare('UPDATE portal_manual_cat set parent_id=:t where parent_id=:el_id');
            $stmt->execute(array(
                ':t' => '0',
                ':el_id' => $_POST['id']
            ));
            
            $stmt = $dbConnection->prepare('delete from portal_manual_cat where id=:n');
            $stmt->execute(array(
                ':n' => $_POST['id']
            ));
            
            showMenu_manual();
        }
        
        if ($mode == "todo_item_del") {
            
            $stmt = $dbConnection->prepare('UPDATE portal_todo set parent_id=:t where parent_id=:el_id');
            $stmt->execute(array(
                ':t' => '0',
                ':el_id' => $_POST['id']
            ));
            
            $stmt = $dbConnection->prepare('delete from portal_todo where id=:n');
            $stmt->execute(array(
                ':n' => $_POST['id']
            ));
            
            showMenu_todo();
        }
        
        if ($mode == "sort_units_manual_qa") {
            $list = $_POST['list'];
            
            echo $list;
            
            $orderlist = explode('&', $list);
            
            $n = 0;
            foreach ($orderlist as $order) {
                
                $a = explode("=", $order);
                
                //echo $a[0];
                
                $b = explode("[", $a['0']);
                
                $с = substr($b[1], 0, -1);
                
                //?
                $rest = substr($b[1], 0, -1);
                
                //echo $a[1];
                //echo "ID:".$rest."  Parent:".$a[1]."  Pos:".$n."                              ////";
                if ($a[1] == "null") {
                    $a[1] = get_max_helper_parent();
                }
                echo "parent_id=" . $a[1] . " where id=" . $rest . ";\r\n";
                
                $stmt = $dbConnection->prepare('UPDATE portal_manual_qa set sort_id=:s_id,parent_id=:p_id where id=:el_id');
                $stmt->execute(array(
                    ':s_id' => $n,
                    ':p_id' => $a[1],
                    ':el_id' => $rest
                ));
                
                $n++;
            }
        }
        
        if ($mode == "sort_todo") {
            $list = $_POST['list'];
            
            echo $list;
            
            $orderlist = explode('&', $list);
            
            $n = 0;
            foreach ($orderlist as $order) {
                
                $a = explode("=", $order);
                
                //echo $a[0];
                
                $b = explode("[", $a['0']);
                
                $с = substr($b[1], 0, -1);
                
                //?
                $rest = substr($b[1], 0, -1);
                
                //echo $a[1];
                //echo "ID:".$rest."  Parent:".$a[1]."  Pos:".$n."                              ////";
                if ($a[1] == "null") {
                    $a[1] = get_max_helper_parent();
                }
                echo "parent_id=" . $a[1] . " where id=" . $rest . ";\r\n";
                
                $stmt = $dbConnection->prepare('UPDATE portal_todo set sort_id=:s_id,parent_id=:p_id where id=:el_id');
                $stmt->execute(array(
                    ':s_id' => $n,
                    ':p_id' => $a[1],
                    ':el_id' => $rest
                ));
                
                $n++;
            }
        }
        
        if ($mode == "sort_units_manual") {
            $list = $_POST['list'];
            
            echo $list;
            
            $orderlist = explode('&', $list);
            
            $n = 0;
            foreach ($orderlist as $order) {
                
                $a = explode("=", $order);
                
                //echo $a[0];
                
                $b = explode("[", $a['0']);
                
                $с = substr($b[1], 0, -1);
                
                //?
                $rest = substr($b[1], 0, -1);
                
                //echo $a[1];
                //echo "ID:".$rest."  Parent:".$a[1]."  Pos:".$n."                              ////";
                if ($a[1] == "null") {
                    $a[1] = get_max_helper_parent();
                }
                echo "parent_id=" . $a[1] . " where id=" . $rest . ";\r\n";
                
                $stmt = $dbConnection->prepare('UPDATE portal_manual_cat set sort_id=:s_id,parent_id=:p_id where id=:el_id');
                $stmt->execute(array(
                    ':s_id' => $n,
                    ':p_id' => $a[1],
                    ':el_id' => $rest
                ));
                
                $n++;
            }
        }
        
        if ($mode == "del_version") {
            $v = $_POST['news_hash'];
            
            //$idp=get_post_val_by_hash($v, 'id');
            
            $stmt1 = $dbConnection->prepare('delete from portal_versions where uniq_id=:u');
            $stmt1->execute(array(
                ':u' => $v
            ));
        }
        if ($mode == "del_manual") {
            $v = $_POST['news_hash'];
            
            //$idp=get_post_val_by_hash($v, 'id');
            
            $stmt1 = $dbConnection->prepare('delete from portal_manual where uniq_id=:u');
            $stmt1->execute(array(
                ':u' => $v
            ));
        }
        
        if ($mode == "del_news") {
            $v = $_POST['news_hash'];
            
            //$idp=get_post_val_by_hash($v, 'id');
            
            $stmt1 = $dbConnection->prepare('delete from portal_news where uniq_id=:u');
            $stmt1->execute(array(
                ':u' => $v
            ));
        }
        
        if ($mode == "del_post") {
            
            $v = $_POST['post_hash'];
            
            $idp = get_post_val_by_hash($v, 'id');
            
            $stmt1 = $dbConnection->prepare('delete from portal_posts where uniq_id=:u');
            $stmt1->execute(array(
                ':u' => $v
            ));
            
            $stmt = $dbConnection->prepare("SELECT *
                            from post_files where post_hash=:id");
            $stmt->execute(array(
                ':id' => $v
            ));
            $result = $stmt->fetchAll();
            
            if (!empty($result)) {
                foreach ($result as $row) {
                    
                    unlink(realpath(dirname(dirname(dirname(__FILE__)))) . "/upload_files/" . $row['file_hash'] . "." . $row['file_ext']);
                }
            }
            $stmt2 = $dbConnection->prepare('delete from post_files where post_hash=:id');
            $stmt2->execute(array(
                ':id' => $v
            ));
            
            $stmt3 = $dbConnection->prepare("SELECT *
                            from post_comments where p_id=:id");
            $stmt3->execute(array(
                ':id' => $idp
            ));
            $result2 = $stmt3->fetchAll();
            foreach ($result2 as $row1) {
                
                $stmtf = $dbConnection->prepare("SELECT *
                            from post_files where post_hash=:id");
                $stmtf->execute(array(
                    ':id' => $row1['uniq_hash']
                ));
                $resultf = $stmtf->fetchAll();
                foreach ($resultf as $rowf) {
                    unlink(realpath(dirname(dirname(dirname(__FILE__)))) . "/upload_files/" . $rowf['file_hash'] . "." . $rowf['file_ext']);
                }
                
                $stmts = $dbConnection->prepare('delete from post_files where post_hash=:id');
                $stmts->execute(array(
                    ':id' => $row['uniq_hash']
                ));
            }
            
            /*
            найти все комменты
            удалить все файлы
            удалить комменты
            
            найти все файлы
            удалить
            
            удалить пост
            
            */
        }
        
        if ($mode == "delete_file") {
            $v = $_POST['file_hash'];
            
            $stmt = $dbConnection->prepare("SELECT *
                            from post_files where file_hash=:id");
            $stmt->execute(array(
                ':id' => $v
            ));
            $result = $stmt->fetchAll();
            
            if (!empty($result)) {
                foreach ($result as $row) {
                    
                    unlink(realpath(dirname(dirname(dirname(__FILE__)))) . "/upload_files/" . $row['file_hash'] . "." . $row['file_ext']);
                }
            }
            $stmt = $dbConnection->prepare('delete from post_files where file_hash=:id');
            $stmt->execute(array(
                ':id' => $v
            ));
        }
        
        if ($mode == "del_comment") {
            $v = $_POST['post_hash'];
            
            $stmt1 = $dbConnection->prepare('delete from post_comments where uniq_hash=:u');
            $stmt1->execute(array(
                ':u' => $v
            ));
            
            $stmt = $dbConnection->prepare("SELECT *
                            from post_files where post_hash=:id");
            $stmt->execute(array(
                ':id' => $v
            ));
            $result = $stmt->fetchAll();
            
            if (!empty($result)) {
                foreach ($result as $row) {
                    
                    unlink(realpath(dirname(dirname(dirname(__FILE__)))) . "/upload_files/" . $row['file_hash'] . "." . $row['file_ext']);
                }
            }
            $stmt = $dbConnection->prepare('delete from post_files where post_hash=:id');
            $stmt->execute(array(
                ':id' => $v
            ));
        }
    }
    
    if ($mode == "get_orig_comment") {
        $v = $_POST['post_hash'];
        
        $stmt1 = $dbConnection->prepare('SELECT comment_text from post_comments where uniq_hash=:u');
        $stmt1->execute(array(
            ':u' => $v
        ));
        
        $tt = $stmt1->fetch(PDO::FETCH_ASSOC);
        
        echo $tt['comment_text'];
    }
    
    if ($mode == "get_orig_post") {
        $v = $_POST['post_hash'];
        
        $stmt1 = $dbConnection->prepare('SELECT msg from portal_posts where uniq_id=:u');
        $stmt1->execute(array(
            ':u' => $v
        ));
        
        $tt = $stmt1->fetch(PDO::FETCH_ASSOC);
        
        echo $tt['msg'];
    }
    
    if ($validate == true) {
        if ($mode == "update_post") {
            $v = $_POST['post_hash'];
            
            $stmt1 = $dbConnection->prepare('update portal_posts set msg=:m where uniq_id=:u');
            $stmt1->execute(array(
                ':u' => $v,
                ':m' => $_POST['msg']
            ));
            
            echo $_POST['msg'];
        }
        
        if ($mode == "update_comment") {
            $v = $_POST['post_hash'];
            
            $stmt1 = $dbConnection->prepare('update post_comments set comment_text=:m where uniq_hash=:u');
            $stmt1->execute(array(
                ':u' => $v,
                ':m' => $_POST['msg']
            ));
            
            echo $_POST['msg'];
        }
        
        if ($mode == "set_post_like") {
            
            $rate = get_post_val_by_hash($_POST['post_hash'], 'rates');
            
            //print_r($_POST);
            if ($_POST['val'] == "like") {
                $rate = $rate + 1;
                $p = 2;
            } 
            else if ($_POST['val'] == "dislike") {
                $rate = $rate - 1;
                $p = 1;
            }
            
            $stmt1 = $dbConnection->prepare('SELECT * from post_likes where post_id=:tm and user_id=:u');
            $stmt1->execute(array(
                ':tm' => get_post_val_by_hash($_POST['post_hash'], 'id') ,
                ':u' => $_SESSION['helpdesk_user_id']
            ));
            
            $tt = $stmt1->fetch(PDO::FETCH_ASSOC);
            
            if ($tt['likes']) {
                
                if ($tt['likes'] == "2") {
                    $rate = $rate - 1;
                } 
                else if ($tt['likes'] == "1") {
                    $rate = $rate + 1;
                }
                
                $stmt = $dbConnection->prepare('update portal_posts set rates=:s where uniq_id=:hn');
                $stmt->execute(array(
                    ':hn' => $_POST['post_hash'],
                    ':s' => $rate
                ));
                
                $stmt2 = $dbConnection->prepare('update post_likes set likes=:s where post_id=:hn');
                $stmt2->execute(array(
                    ':hn' => get_post_val_by_hash($_POST['post_hash'], 'id') ,
                    ':s' => $p
                ));
            } 
            else if (!$tt['likes']) {
                
                $stmt = $dbConnection->prepare('update portal_posts set rates=:s where uniq_id=:hn');
                $stmt->execute(array(
                    ':hn' => $_POST['post_hash'],
                    ':s' => $rate
                ));
                
                $stmt = $dbConnection->prepare('insert into post_likes 
        (user_id, post_id, likes) values 
        (:user_id, :post_id, :likes)');
                $stmt->execute(array(
                    ':user_id' => $_SESSION['helpdesk_user_id'],
                    ':post_id' => get_post_val_by_hash($_POST['post_hash'], 'id') ,
                    ':likes' => $p
                ));
            }
        }
        
        if ($mode == "edit_version") {
            
            $validator = new GUMP();
            
            //$_POST = $validator->sanitize($_POST);
            
            $rules = array(
                'subj' => 'required',
                'msg' => 'required',
                'title' => 'required'
            );
            $filters = array(
                'subj' => 'trim|sanitize_string',
                'title' => 'trim|sanitize_string'
            );
            
            $validator->set_field_name(array(
                "subj" => lang('NEW_subj')
            ));
            
            GUMP::set_field_name("subj", lang('NEW_subj'));
            
            $_POST = $validator->filter($_POST, $filters);
            
            $validated = $validator->validate($_POST, $rules);
            
            if ($validated === true) {
                $check_error = true;
                $stmt = $dbConnection->prepare('update portal_versions 
        set subj=:subj, msg=:msg, title=:title, author_id=:uid where uniq_id=:uniq_id');
                $stmt->execute(array(
                    ':subj' => $_POST['subj'],
                    ':msg' => $_POST['msg'],
                    ':title' => $_POST['title'],
                    ':uniq_id' => $_POST['hn'],
                    ':uid' => $_SESSION['helpdesk_user_id']
                ));
            } 
            else {
                
                //print_r($is_valid);
                $check_error = false;
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($validator->get_readable_errors(false) as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
            }
            $results[] = array(
                'check_error' => $check_error,
                'msg' => $msg,
                'm2' => $_POST['msg']
            );
            print json_encode($results);
        }
        
        if ($mode == "edit_news") {
            
            $validator = new GUMP();
            
            //$_POST = $validator->sanitize($_POST);
            
            $rules = array(
                'subj' => 'required',
                'msg' => 'required',
                'title' => 'required'
            );
            $filters = array(
                'subj' => 'trim|sanitize_string',
                'title' => 'trim|sanitize_string'
            );
            
            $validator->set_field_name(array(
                "subj" => lang('NEW_subj')
            ));
            
            GUMP::set_field_name("subj", lang('NEW_subj'));
            
            $_POST = $validator->filter($_POST, $filters);
            
            $validated = $validator->validate($_POST, $rules);
            
            if ($validated === true) {
                $check_error = true;
                $stmt = $dbConnection->prepare('update portal_news 
        set subj=:subj, msg=:msg, title=:title, author_id=:uid where uniq_id=:uniq_id');
                $stmt->execute(array(
                    ':subj' => $_POST['subj'],
                    ':msg' => $_POST['msg'],
                    ':title' => $_POST['title'],
                    ':uniq_id' => $_POST['hn'],
                    ':uid' => $_SESSION['helpdesk_user_id']
                ));
            } 
            else {
                
                //print_r($is_valid);
                $check_error = false;
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($validator->get_readable_errors(false) as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
            }
            $results[] = array(
                'check_error' => $check_error,
                'msg' => $msg,
                'm2' => $_POST['msg']
            );
            print json_encode($results);
        }
        
        if ($mode == "add_version") {
            
            $validator = new GUMP();
            
            //$_POST = $validator->sanitize($_POST);
            
            $rules = array(
                'subj' => 'required',
                'msg' => 'required',
                'title' => 'required'
            );
            $filters = array(
                'subj' => 'trim|sanitize_string',
                'title' => 'trim|sanitize_string'
            );
            
            $validator->set_field_name(array(
                "subj" => lang('NEW_subj')
            ));
            
            GUMP::set_field_name("subj", lang('NEW_subj'));
            
            $_POST = $validator->filter($_POST, $filters);
            
            $validated = $validator->validate($_POST, $rules);
            
            if ($validated === true) {
                $check_error = true;
                $stmt = $dbConnection->prepare('insert into portal_versions 
        (subj, msg, title, uniq_id, dt, author_id) values 
        (:subj, :msg, :title, :uniq_id, :dt, :uid)');
                $stmt->execute(array(
                    ':subj' => $_POST['subj'],
                    ':msg' => $_POST['msg'],
                    ':title' => $_POST['title'],
                    ':uniq_id' => $_POST['hn'],
                    ':dt' => $CONF['now_dt'],
                    ':uid' => $_SESSION['helpdesk_user_id']
                ));
            } 
            else {
                
                //print_r($is_valid);
                $check_error = false;
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($validator->get_readable_errors(false) as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
            }
            $results[] = array(
                'check_error' => $check_error,
                'msg' => $msg,
                'm2' => $_POST['msg']
            );
            print json_encode($results);
        }
        
        if ($mode == "add_news") {
            
            $validator = new GUMP();
            
            //$_POST = $validator->sanitize($_POST);
            
            $rules = array(
                'subj' => 'required',
                'msg' => 'required',
                'title' => 'required'
            );
            $filters = array(
                'subj' => 'trim|sanitize_string',
                'title' => 'trim|sanitize_string'
            );
            
            $validator->set_field_name(array(
                "subj" => lang('NEW_subj')
            ));
            
            GUMP::set_field_name("subj", lang('NEW_subj'));
            
            $_POST = $validator->filter($_POST, $filters);
            
            $validated = $validator->validate($_POST, $rules);
            
            if ($validated === true) {
                $check_error = true;
                $stmt = $dbConnection->prepare('insert into portal_news 
        (subj, msg, title, uniq_id, dt, author_id) values 
        (:subj, :msg, :title, :uniq_id, :dt, :uid)');
                $stmt->execute(array(
                    ':subj' => $_POST['subj'],
                    ':msg' => $_POST['msg'],
                    ':title' => $_POST['title'],
                    ':uniq_id' => $_POST['hn'],
                    ':dt' => $CONF['now_dt'],
                    ':uid' => $_SESSION['helpdesk_user_id']
                ));
            } 
            else {
                
                //print_r($is_valid);
                $check_error = false;
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($validator->get_readable_errors(false) as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
            }
            $results[] = array(
                'check_error' => $check_error,
                'msg' => $msg,
                'm2' => $_POST['msg']
            );
            print json_encode($results);
        }
        
        if ($mode == "conf_edit_version_banner") {
            
            update_val_by_key("portal_box_version_n", $_POST['portal_box_version_n']);
            update_val_by_key("portal_box_version_text", $_POST['portal_box_version_text']);
            update_val_by_key("portal_box_version_icon", $_POST['portal_box_version_icon']);
?>
                <div class="alert alert-success">
                    <?php
            echo lang('PROFILE_msg_ok'); ?>
                </div>
        <?php
        }
        
        if ($mode == "add_post") {
            
            $validator = new GUMP();
            
            //$_POST = $validator->sanitize($_POST);
            
            $rules = array(
                'subj' => 'required',
                'msg' => 'required|min_len,15'
            );
            $filters = array(
                'subj' => 'trim|sanitize_string',
                'msg' => 'trim'
            );
            
            $validator->set_field_name(array(
                "subj" => lang('NEW_subj')
            ));
            
            GUMP::set_field_name("subj", lang('NEW_subj'));
            GUMP::set_field_name("msg", lang('PORTAL_msg'));
            
            $_POST = $validator->filter($_POST, $filters);
            
            $validated = $validator->validate($_POST, $rules);
            
            if ($validated === true) {
                $check_error = true;
                $stmt = $dbConnection->prepare('insert into portal_posts 
        (subj, msg, type, uniq_id, dt, author_id) values 
        (:subj, :msg, :type, :uniq_id, :dt, :uid)');
                $stmt->execute(array(
                    ':subj' => $_POST['subj'],
                    ':msg' => $_POST['msg'],
                    ':type' => $_POST['type'],
                    ':uniq_id' => $_POST['hn'],
                    ':dt' => $CONF['now_dt'],
                    ':uid' => $_SESSION['helpdesk_user_id']
                ));
                send_notification_portal('portal_post_new', get_post_val_by_hash($_POST['hn'], 'id'));
                verify_uploaded_files($_POST['hn']);
            } 
            else {
                
                //print_r($is_valid);
                $check_error = false;
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($validator->get_readable_errors(false) as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
            }
            $results[] = array(
                'check_error' => $check_error,
                'msg' => $msg,
                'm2' => $_POST['msg']
            );
            print json_encode($results);
        }
        
        if ($mode == "edit_manual_qa") {
            
            $validator = new GUMP();
            
            //$_POST = $validator->sanitize($_POST);
            
            $rules = array(
                'subj' => 'required',
                'msg' => 'required'
            );
            $filters = array(
                'subj' => 'trim|sanitize_string',
                
                //'title'=>'trim|sanitize_string'
                
            );
            
            $validator->set_field_name(array(
                "subj" => lang('NEW_subj')
            ));
            
            GUMP::set_field_name("subj", lang('NEW_subj'));
            
            $_POST = $validator->filter($_POST, $filters);
            
            $validated = $validator->validate($_POST, $rules);
            
            if ($validated === true) {
                $check_error = true;
                $stmt = $dbConnection->prepare('update portal_manual_qa
        set question=:subj, answer=:msg, author_id=:uid where uniq_id=:uniq_id');
                $stmt->execute(array(
                    ':subj' => $_POST['subj'],
                    ':msg' => $_POST['msg'],
                    
                    //':title' => $_POST['title'],
                    ':uniq_id' => $_POST['hn'],
                    ':uid' => $_SESSION['helpdesk_user_id']
                ));
            } 
            else {
                
                //print_r($is_valid);
                $check_error = false;
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($validator->get_readable_errors(false) as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
            }
            $results[] = array(
                'check_error' => $check_error,
                'msg' => $msg,
                'm2' => $_POST['msg']
            );
            print json_encode($results);
        }
        
        if ($mode == "edit_manual") {
            
            $validator = new GUMP();
            
            //$_POST = $validator->sanitize($_POST);
            
            $rules = array(
                'subj' => 'required',
                'msg' => 'required'
            );
            $filters = array(
                'subj' => 'trim|sanitize_string',
                
                //'title'=>'trim|sanitize_string'
                
            );
            
            $validator->set_field_name(array(
                "subj" => lang('NEW_subj')
            ));
            
            GUMP::set_field_name("subj", lang('NEW_subj'));
            
            $_POST = $validator->filter($_POST, $filters);
            
            $validated = $validator->validate($_POST, $rules);
            
            if ($validated === true) {
                $check_error = true;
                $stmt = $dbConnection->prepare('update portal_manual_cat
        set name=:subj, msg=:msg, author_id=:uid where uniq_id=:uniq_id');
                $stmt->execute(array(
                    ':subj' => $_POST['subj'],
                    ':msg' => $_POST['msg'],
                    
                    //':title' => $_POST['title'],
                    ':uniq_id' => $_POST['hn'],
                    ':uid' => $_SESSION['helpdesk_user_id']
                ));
            } 
            else {
                
                //print_r($is_valid);
                $check_error = false;
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($validator->get_readable_errors(false) as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
            }
            $results[] = array(
                'check_error' => $check_error,
                'msg' => $msg,
                'm2' => $_POST['msg']
            );
            print json_encode($results);
        }
        
        if ($mode == "add_manual") {
            
            $validator = new GUMP();
            
            //$_POST = $validator->sanitize($_POST);
            
            $rules = array(
                'subj' => 'required',
                'msg' => 'required'
            );
            $filters = array(
                'subj' => 'trim|sanitize_string'
            );
            
            $validator->set_field_name(array(
                "subj" => lang('NEW_subj')
            ));
            
            GUMP::set_field_name("subj", lang('NEW_subj'));
            
            $_POST = $validator->filter($_POST, $filters);
            
            $validated = $validator->validate($_POST, $rules);
            
            if ($validated === true) {
                $check_error = true;
                $stmt = $dbConnection->prepare('insert into portal_manual 
        (subj, msg, cat_id, uniq_id, dt, author_id) values 
        (:subj, :msg, :type, :uniq_id, :dt, :uid)');
                $stmt->execute(array(
                    ':subj' => $_POST['subj'],
                    ':msg' => $_POST['msg'],
                    ':type' => $_POST['type'],
                    ':uniq_id' => $_POST['hn'],
                    ':dt' => $CONF['now_dt'],
                    ':uid' => $_SESSION['helpdesk_user_id']
                ));
            } 
            else {
                
                //print_r($is_valid);
                $check_error = false;
                $msg.= "<div class=\"callout callout-danger\"><p><ul>";
                foreach ($validator->get_readable_errors(false) as $key => $value) {
                    $msg.= "<li>" . $value . "</li>";
                }
                $msg.= "</ul></p></div>";
            }
            $results[] = array(
                'check_error' => $check_error,
                'msg' => $msg,
                'm2' => $_POST['msg']
            );
            print json_encode($results);
        }
        




        if ($mode == "upload_post_file") {
            //echo "ok";
            $base = dirname(dirname(dirname(__FILE__)));
            $output_dir = $base."/upload_files/";
            //echo  $output_dir;
            $hn = $_POST['post_hash'];
            $maxsize = get_conf_param('file_size');
            
            $good_files = explode("|", get_conf_param('file_types'));
            
            $acceptable = $good_files;
            
            if (isset($_FILES["myfile"])) {
                $ret = array();
                
                $error = $_FILES["myfile"]["error"];
                $flag = false;
                
                //You need to handle  both cases
                //If Any browser does not support serializing of multiple files using FormData()
                if (!is_array($_FILES["myfile"]["name"]))
                 //single file
                {
                    $fileName = $_FILES["myfile"]["name"];
                    $filetype = $_FILES["myfile"]["type"];
                    $filesize = $_FILES["myfile"]["size"];
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    if ($_FILES["myfile"]["size"] > $maxsize) {
                        $flag = true;
                        $msg = lang('PORTAL_file_big');
                    }
                    if ((!in_array($ext, $acceptable)) && (!empty($_FILES["myfile"]["type"]))) {
                        $flag = true;
                        $msg = lang('PORTAL_file_ext');
                    }
                    
                    if ($flag == false) {
                        
                        $fhash = randomhash();
                        
                        //$ext = pathinfo($fileName, PATHINFO_EXTENSION);
                        $fileName_norm = $fhash . "." . $ext;
                        
                        move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir . $fileName_norm);
                        
                        $stmt = $dbConnection->prepare('insert into post_files 
        (post_hash, original_name, file_hash, file_type, file_size, file_ext, p_type, is_tmp) values 
        (:post_hash, :original_name, :file_hash, :file_type, :file_size, :file_ext, :p_type, :is_tmp)');
                        $stmt->execute(array(
                            ':post_hash' => $hn,
                            ':original_name' => $fileName,
                            ':file_hash' => $fhash,
                            ':file_type' => $filetype,
                            ':file_size' => $filesize,
                            ':file_ext' => $ext,
                            ':p_type' => $_POST['type'],
                            ':is_tmp' => $_POST['is_tmp']
                        ));
                    }
                    
                    //{msg: "Upload limit reached", status: "error", code: "403"}
                    
                    if ($flag == false) {
                        $status = "ok";
                    } 
                    else if ($flag == true) {
                        $status = "error";
                    }
                    
                    $results[] = array(
                        'uniq_code' => $fhash,
                        'code' => 501,
                        'status' => $status,
                        'msg' => $msg
                    );
                    
                    print json_encode($results);
                }
            }
        }
    }
    
    if ($mode == "new_post_check") {
        
        $validator = new GUMP();
        $_POST = $validator->sanitize($_POST);
        $filters = array(
            'text_idea' => 'sanitize_string|trim'
        );
        
        $_POST = $validator->filter($_POST, $filters);
        $ts = $_POST['text_idea'];
        
        $ex = explode(" ", $ts);
        
        $t = 0;
        
        foreach ($ex as $value) {
            // code...
            $stmt = $dbConnection->prepare("SELECT * from portal_manual_cat where (portal_manual_cat.name like :t2)");
            $stmt->execute(array(
                ':t2' => '%' . $value . '%'
            ));
            $result = $stmt->fetchAll();
            if (!empty($result)) {
                $t++;
            }
            
            $stmt2 = $dbConnection->prepare("SELECT * from portal_posts where (portal_posts.subj like :t)");
            $stmt2->execute(array(
                ':t' => '%' . $value . '%'
            ));
            $result2 = $stmt2->fetchAll();
            if (!empty($result2)) {
                $t++;
            }
        }
        $_SESSION['zenlix_portal_post'] = $ts;
        if ($t != 0) {
            $cs = true;
        } 
        else if ($t == 0) {
            
            $cs = false;
        }
        
        $results[] = array(
            'check_state' => $cs
        );
        print json_encode($results);
    }
    
    //get_res_post_check
    
    if ($mode == "get_res_post_check") {
        $validator = new GUMP();
        $_POST = $validator->sanitize($_POST);
        $filters = array(
            'text_idea' => 'sanitize_string|trim'
        );
        
        $_POST = $validator->filter($_POST, $filters);
        $t = $_POST['text_idea'];
        $ex = explode(" ", $t);
        
        foreach ($ex as $value) {
            $stmt = $dbConnection->prepare("SELECT * from portal_posts where (portal_posts.subj like :t) limit 3");
            $stmt->execute(array(
                ':t' => '%' . $value . '%'
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
        
        /*
        $stmt = $dbConnection->prepare("SELECT * from portal_posts where (subj like :t)");
                $stmt->execute(array(
                    ':t' => '%' . $t . '%'
                ));
                $result = $stmt->fetchAll();
        
        foreach ($result as $row) {
        echo $row['subj'];
        # code...
        }*/
    }
}
?>