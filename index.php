<?php
session_start();

include_once ("conf.php");

if (isset($CONF_DB)) {
    
    include ("functions.inc.php");
    
    if (isset($_GET['logout'])) {
        session_destroy();
        unset($_SESSION);
        session_unset();
        setcookie('authhash_uid', "");
        setcookie('authhash_code', "");
        unset($_COOKIE['authhash_uid']);
        unset($_COOKIE['authhash_code']);
        session_regenerate_id();
        header("Location: " . $CONF['hostname']);
    }
    
    if ($_GET['page'] == "register") {
        include ('inc/register.php');
    } else if ($_GET['page'] == "forgot") {
        include ('inc/forgot.php');
    } else {
        
        //echo($_COOKIE['authhash_code']);
        $rq = 0;
        if (isset($_POST['login']) && isset($_POST['password'])) {
            
            $rq = 1;
            $req_url = $_POST['req_url'];
            $rm = $_POST['remember_me'];
            
            $login = ($_POST['login']);
            $password = md5($_POST['password']);
            
            //LDAP-auth
            if (get_user_authtype($login)) {
                if (ldap_auth($login, $_POST['password'])) {
                    
                    $stmt = $dbConnection->prepare('SELECT id,login,fio from users where login=:login AND status=1');
                    $stmt->execute(array(':login' => $login));
                    if ($stmt->rowCount() == 1) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        $_SESSION['helpdesk_user_id'] = $row['id'];
                        $_SESSION['helpdesk_user_login'] = $row['login'];
                        $_SESSION['helpdesk_user_fio'] = $row['fio'];
                        $_SESSION['helpdesk_user_type'] = "user";
                        $_SESSION['code'] = $_POST['password'];
                        if ($rm == "1") {
                            
                            //UPDATE USERS set=password_ad
                            //password_ad encode
                            
                            // setcookie('authhash_uid', $_SESSION['helpdesk_user_id'], time()+60*60*24*7);
                            // setcookie('authhash_code', $_SESSION['code'], time()+60*60*24*7);
                            
                        }
                    }
                } else {
                    $va = "error";
                }
            }
            
            //SYSTEM auth
            else if (get_user_authtype($login) == false) {
                
                $stmt = $dbConnection->prepare('SELECT id,login,fio from users where login=:login AND pass=:pass AND status=1');
                $stmt->execute(array(':login' => $login, ':pass' => $password));
                
                /*$stmt_cli = $dbConnection->prepare('SELECT id,login,fio from clients where login=:login AND pass=:pass AND login_status=1');
                $stmt_cli->execute(array(':login' => $login, ':pass' => $password));
                */
                
                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $_SESSION['helpdesk_user_id'] = $row['id'];
                    $_SESSION['helpdesk_user_login'] = $row['login'];
                    $_SESSION['helpdesk_user_fio'] = $row['fio'];
                    $_SESSION['helpdesk_user_type'] = "user";
                    $_SESSION['code'] = md5($password);
                    if ($rm == "1") {
                        
                        setcookie('authhash_uid', $_SESSION['helpdesk_user_id'], time() + 60 * 60 * 24 * 7);
                        setcookie('authhash_code', $_SESSION['code'], time() + 60 * 60 * 24 * 7);
                    }
                }
                
                /* else if ($stmt_cli -> rowCount() == 1) {
                $rowcli = $stmt_cli->fetch(PDO::FETCH_ASSOC);
                
                $_SESSION['helpdesk_user_id'] = $rowcli['id'];
                $_SESSION['helpdesk_user_login'] = $rowcli['login'];
                $_SESSION['helpdesk_user_fio'] = $rowcli['fio'];
                $_SESSION['helpdesk_user_type'] = "client";
                $_SESSION['code'] = md5($password);
                if ($rm == "1") {
                
                setcookie('authhash_uid', $_SESSION['helpdesk_user_id'], time()+60*60*24*7);
                setcookie('authhash_code', $_SESSION['code'], time()+60*60*24*7);
                }
                
                }*/
                
                else {
                    $va = 'error';
                }
            }
        }
        
        //if (isset($_SESSION['code']) ) {
        if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
            $url = parse_url($CONF['hostname']);
            
            if ($rq == 1) {
                header("Location: http://" . $url['host'] . $req_url);
            }
            if ($rq == 0) {
                
                if (!isset($_GET['page'])) {
                    
                    include ("inc/head.inc.php");
                    include ("inc/navbar.inc.php");
                    include ("inc/dashboard.php");
                    include ("inc/footer.inc.php");
                }
                
                if (isset($_GET['page'])) {
                    
                    switch ($_GET['page']) {
                        case 'create':
                            include ('inc/new.php');
                            break;

                        case 'list':
                            include ('inc/list.php');
                            break;

                        case 'stats':
                            include ('inc/stats.php');
                            break;

                        case 'helper':
                            include ('inc/helper.php');
                            break;

                        case 'notes':
                            include ('inc/notes.php');
                            break;

                        case 'profile':
                            include ('inc/profile.php');
                            break;

                        case 'help':
                            include ('inc/help.php');
                            break;

                        case 'users':
                            include ('inc/users.php');
                            break;

                        case 'deps':
                            include ('inc/deps.php');
                            break;

                        case 'approve':
                            include ('inc/approve.php');
                            break;

                        case 'posada':
                            include ('inc/posada.php');
                            break;

                        case 'units':
                            include ('inc/units.php');
                            break;

                        case 'subj':
                            include ('inc/subj.php');
                            break;

                        case 'ticket':
                            include ('inc/ticket.php');
                            break;

                        case 'view_user':
                            include ('inc/view_user.php');
                            break;

                        case 'userinfo':
                            include ('inc/userinfo.php');
                            break;

                        case 'config':
                            include ('inc/perf.php');
                            break;

                        case 'files':
                            include ('inc/files.php');
                            break;

                        case 'news':
                            include ('inc/news.php');
                            break;

                        case 'clients':
                            include ('inc/clients.php');
                            break;

                        case 'main_stats':
                            include ('inc/all_stats.php');
                            break;

                        case 'user_stats':
                            include ('inc/user_stats.php');
                            break;
                            
                        case 'scheduler':
                            include ('inc/scheduler.php');
                            break;

                        case 'messages':
                            include ('inc/messages.php');
                            break;

                        case 'print_ticket':
                            include ('inc/print_ticket.php');
                            break;

                        default:
                            include ('404.php');
                    }
                }
            }
        } else if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
            $url = parse_url($CONF['hostname']);
            
            if ($rq == 1) {
                header("Location: http://" . $url['host'] . $req_url);
            }
            if ($rq == 0) {
                
                if (!isset($_GET['page'])) {
                    
                    include ("inc/head.inc.php");
                    include ("inc/client.navbar.inc.php");
                     //client
                    include ("inc/client.dashboard.php");
                     //client
                    include ("inc/footer.inc.php");
                }
                
                if (isset($_GET['page'])) {
                    
                    switch ($_GET['page']) {
                        case 'create':
                            include ('inc/client.new.php');
                            break;

                        case 'list':
                            include ('inc/client.list.php');
                            break;

                        case 'ticket':
                            include ('inc/client.ticket.php');
                            break;

                        case 'helper':
                            include ('inc/client.helper.php');
                            break;

                        case 'profile':
                            include ('inc/client.profile.php');
                            break;

                        default:
                            include ('404.php');
                    }
                }
            }
        } else {
            include ("inc/head.inc.php");
            include 'inc/auth.php';
        }
    }
} else {
    include "sys/install.php";
}

//ob_end_flush();

?>
