<?php

function get_privs() {
    $val_status = 'GUEST';
    
    if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
        $val_status = 'USER';
    } 
    else if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
        $val_status = 'CLIENT';
    }
    
    return $val_status;
}

function get_portal_status() {
    global $CONF;
    $main_portal = $CONF['main_portal'];
    $r = false;
    if ($main_portal == "true") {
        $r = true;
    }
    
    return $r;
}

function logoutAction() {
    global $dbConnection, $CONF;
    session_destroy();
    unset($_SESSION);
    session_unset();
    setcookie('authhash_uid', "");
    setcookie('authhash_code', "");
    unset($_COOKIE['authhash_uid']);
    unset($_COOKIE['authhash_code']);
    session_regenerate_id();
    
    //$_SESSION['z.times']=1;
    header("Location: " . $CONF['real_hostname']);
}

function auth() {
    
    //echo "ok!";
    //echo $_POST['login'];
    global $dbConnection, $CONF;
    
    /////////////////START IF LOGIN-AUTH FORM SUBMITTED/////////////////////////
    
    //echo($_COOKIE['authhash_code']);
    $rq = 0;
    if (isset($_POST['login']) && isset($_POST['password'])) {
        
        $rq = 1;
        
        if ($_SESSION['z.times'] < 5) {
            
            $req_url = $_POST['req_url'];
            $rm = $_POST['remember_me'];
            
            $login = ($_POST['login']);
            $password = md5($_POST['password']);
            
            //LDAP-auth
            if (get_user_authtype($login)) {
                if (ldap_auth($login, $_POST['password'])) {
                    
                    $stmt = $dbConnection->prepare('SELECT id,login,fio from users where login=:login AND status=1');
                    $stmt->execute(array(
                        ':login' => $login
                    ));
                    if ($stmt->rowCount() == 1) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        $_SESSION['helpdesk_user_id'] = $row['id'];
                        $_SESSION['helpdesk_user_login'] = $row['login'];
                        $_SESSION['helpdesk_user_fio'] = $row['fio'];
                        $_SESSION['helpdesk_user_type'] = "user";
                        $_SESSION['zenlix.session_id'] = md5(time());
                        $_SESSION['code'] = $_POST['password'];
                        unset($_SESSION['z.times']);
                        unset($_SESSION['z.times_lt']);
                        if ($rm == "1") {
                            
                            //UPDATE USERS set=password_ad
                            //password_ad encode
                            
                            // setcookie('authhash_uid', $_SESSION['helpdesk_user_id'], time()+60*60*24*7);
                            // setcookie('authhash_code', $_SESSION['code'], time()+60*60*24*7);
                            
                            
                        }
                    }
                } 
                else {
                    $va = "error";
                }
            }
            
            //SYSTEM auth
            else if (get_user_authtype($login) == false) {
                
                $stmt = $dbConnection->prepare('SELECT id,login,fio from users where login=:login AND pass=:pass AND status=1');
                $stmt->execute(array(
                    ':login' => $login,
                    ':pass' => $password
                ));
                
                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $_SESSION['helpdesk_user_id'] = $row['id'];
                    $_SESSION['helpdesk_user_login'] = $row['login'];
                    $_SESSION['helpdesk_user_fio'] = $row['fio'];
                    $_SESSION['helpdesk_user_type'] = "user";
                    $_SESSION['zenlix.session_id'] = md5(time());
                    $_SESSION['code'] = md5($password);
                    unset($_SESSION['z.times']);
                    unset($_SESSION['z.times_lt']);
                    if ($rm == "1") {
                        
                        setcookie('authhash_uid', $_SESSION['helpdesk_user_id'], time() + 60 * 60 * 24 * 7);
                        setcookie('authhash_code', $_SESSION['code'], time() + 60 * 60 * 24 * 7);
                    }
                } 
                else {
                    $va = 'error';
                }
            }
        }
        
        if ($va == 'error') {
            
            if (!isset($_SESSION['z.times'])) {
                $_SESSION['z.times'] = 1;
                $_SESSION['z.times_lt'] = time();
            } 
            else if (isset($_SESSION['z.times'])) {
                $_SESSION['z.times']++;
                $_SESSION['z.times_lt'] = time();
            }
            
            //$_SESSION['z.error_code']=md5(time());
            //$_SESSION['z.times']++;
            
            
        }
    }
    
    if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code']) || validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
        
        $url = parse_url($CONF['hostname']);
        if ($rq == 1) {
            header("Location: " . site_proto() . $_SERVER['HTTP_HOST'] . $req_url);
        }
        
        /*
            if ($rq == 1) {
                if ($CONF['main_portal'] == "false") {
                header("Location: ".site_proto(). $url['host'] ."". $req_url);
            }
            else if ($CONF['main_portal'] == "true") {
                header("Location: " . site_proto() . get_conf_param('hostname') . "/dashboard");
            }
            }
        */
    } 
    else {
        include 'inc/auth.php';
    }
    
    /////////////////END IF LOGIN-AUTH FORM SUBMITTED/////////////////////////
    
}

function indexAction() {
    
    //echo "ok";
    
    global $dbConnection, $CONF;
    $privs = get_privs();
    $portalStatus = get_portal_status();
    
    if ($privs == "GUEST") {
        
        if ($portalStatus) {
            require 'inc/main_portal/index.php';
        } 
        else if (!$portalStatus) {
            require 'inc/auth.php';
        }
    } 
    else if (($privs == "USER")) {
        
        if ($portalStatus) {
            require 'inc/main_portal/index.php';
        } 
        else if (!$portalStatus) {
            require ("inc/dashboard.php");
        }
    } 
    else if (($privs == "CLIENT")) {
        
        if ($portalStatus) {
            require 'inc/main_portal/index.php';
        } 
        else if (!$portalStatus) {
            require ("inc/client.dashboard.php");
        }
    }
}

function dashboardAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "GUEST") {
        include ('inc/auth.php');
    } 
    else if (($privs == "CLIENT")) {
        require 'inc/client.dashboard.php';
    } 
    else if (($privs == "USER")) {
        require 'inc/dashboard.php';
    }
}

//class regi extends Controller  {

function registerAction() {
    global $dbConnection, $CONF;
    
    $privs = get_privs();
    $portalStatus = get_portal_status();
    
    if ($privs == "GUEST") {
        
        if ($portalStatus) {
            require ('inc/main_portal/register.php');
        } 
        else if (!$portalStatus) {
            require ('inc/register.php');
        }
    } 
    else if ($privs == "USER") {
        include ('inc/404.inc.php');
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    }
}

function forgotAction() {
    global $dbConnection, $CONF;
    
    $privs = get_privs();
    $portalStatus = get_portal_status();
    
    if ($privs == "GUEST") {
        
        require ('inc/forgot.php');
    } 
    else if ($privs == "USER") {
        include ('inc/404.inc.php');
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    }
}

//}

function auth_get() {
    global $dbConnection, $CONF;
    
    $privs = get_privs();
    $portalStatus = get_portal_status();
    if ($privs == "CLIENT") {
        header("Location: " . site_proto() . $_SERVER['HTTP_HOST'] . $CONF['hostname']);
    } 
    else if ($privs == "USER") {
        header("Location: " . site_proto() . $_SERVER['HTTP_HOST'] . $CONF['hostname'] . "dashboard");
    } 
    else {
        
        if (!$portalStatus) {
            include 'inc/auth.php';
        } 
        else if ($portalStatus) {
            include 'inc/main_portal/auth.php';
        }
    }
}

function listAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    $portalStatus = get_portal_status();
    
    if ($privs == "GUEST") {
        
        require 'inc/auth.php';
    } 
    else if (($privs == "CLIENT")) {
        require ("inc/client.list.php");
    } 
    else if (($privs == "USER")) {
        require ("inc/list.php");
    }
}

function ticketAction() {
    
    global $dbConnection, $CONF;
    $privs = get_privs();
    
    if ($privs == "USER") {
        include 'inc/ticket.php';
    } 
    else if ($privs == "CLIENT") {
        include 'inc/client.ticket.php';
    } 
    else {
        include 'inc/auth.php';
    }
}

function createAction() {
    
    global $dbConnection, $CONF;
    $privs = get_privs();
    
    if ($privs == "USER") {
        include 'inc/new.php';
    } 
    else if ($privs == "CLIENT") {
        include 'inc/client.new.php';
    } 
    else {
        include 'inc/auth.php';
    }
}

function view_userAction() {
    
    global $dbConnection, $CONF;
    $privs = get_privs();
    
    if ($privs == "USER") {
        include 'inc/view_user.php';
    } 
    else if ($privs == "CLIENT") {
        include 'inc/client.view_user.php';
    } 
    else {
        include 'inc/auth.php';
    }
}

function profileAction() {
    
    global $dbConnection, $CONF;
    $privs = get_privs();
    
    if ($privs == "USER") {
        include 'inc/profile.php';
    } 
    else if ($privs == "CLIENT") {
        include 'inc/client.profile.php';
    } 
    else {
        include 'inc/auth.php';
    }
}

function helperAction() {
    
    global $dbConnection, $CONF;
    $privs = get_privs();
    
    if ($privs == "USER") {
        include 'inc/helper.php';
    } 
    else if ($privs == "CLIENT") {
        include 'inc/client.helper.php';
    } 
    else {
        include 'inc/auth.php';
    }
}

function statsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/stats.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function notesAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/notes.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function usersAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/users.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function helpAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/help.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function depsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/deps.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function approveAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/approve.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function unitsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/units.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function posadaAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/posada.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function subjAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/subj.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function userinfoAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/userinfo.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function perfAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/perf.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function filesAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/files.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function newsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/news.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function clientsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/clients.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function all_statsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/all_stats.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function user_statsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/user_stats.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function sla_repAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/sla_rep.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function schedulerAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/scheduler.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function messagesAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/messages.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function print_ticketAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/print_ticket.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function calendarAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/calendar.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function portalAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/portal.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function mailersAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'inc/mailers.php';
    } 
    else if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'inc/auth.php';
    }
}

function manualAction() {
    global $dbConnection, $CONF;
    $portal = get_portal_status();
    $privs = get_privs();
    if ($portal) {
        include 'inc/main_portal/manual.php';
    } 
    else {
        if ($privs == "USER") {
            include 'inc/404.inc.php';
        } 
        else if ($privs == "CLIENT") {
            include ('inc/client.404.inc.php');
        } 
        else if ($privs == "GUEST") {
            include 'inc/auth.php';
        }
    }
}

function versionAction() {
    global $dbConnection, $CONF;
    $portal = get_portal_status();
    $privs = get_privs();
    if ($portal) {
        include 'inc/main_portal/version.php';
    } 
    else {
        if ($privs == "USER") {
            include 'inc/404.inc.php';
        } 
        else if ($privs == "CLIENT") {
            include ('inc/client.404.inc.php');
        } 
        else if ($privs == "GUEST") {
            include 'inc/auth.php';
        }
    }
}

function feedAction() {
    global $dbConnection, $CONF;
    $portal = get_portal_status();
    $privs = get_privs();
    if ($portal) {
        include 'inc/main_portal/feed.php';
    } 
    else {
        if ($privs == "USER") {
            include 'inc/404.inc.php';
        } 
        else if ($privs == "CLIENT") {
            include ('inc/client.404.inc.php');
        } 
        else if ($privs == "GUEST") {
            include 'inc/auth.php';
        }
    }
}

function catAction() {
    global $dbConnection, $CONF;
    $portal = get_portal_status();
    $privs = get_privs();
    if ($portal) {
        include 'inc/main_portal/cat.php';
    } 
    else {
        if ($privs == "USER") {
            include 'inc/404.inc.php';
        } 
        else if ($privs == "CLIENT") {
            include ('inc/client.404.inc.php');
        } 
        else if ($privs == "GUEST") {
            include 'inc/auth.php';
        }
    }
}

function new_postAction() {
    global $dbConnection, $CONF;
    $portal = get_portal_status();
    $privs = get_privs();
    if ($portal) {
        
        if ($privs == "USER") {
            include 'inc/main_portal/new_post.php';
        } 
        else if ($privs == "CLIENT") {
            include 'inc/main_portal/new_post.php';
        } 
        else if ($privs == "GUEST") {
            include 'inc/main_portal/auth.php';
        }
    }
    if (!$portal) {
        if ($privs == "USER") {
            include 'inc/404.inc.php';
        } 
        else if ($privs == "CLIENT") {
            include ('inc/client.404.inc.php');
        } 
        else if ($privs == "GUEST") {
            include 'inc/auth.php';
        }
    }
}

function postAction() {
    global $dbConnection, $CONF;
    $portal = get_portal_status();
    $privs = get_privs();
    if ($portal) {
        include 'inc/main_portal/post.php';
    } 
    else {
        if ($privs == "USER") {
            include 'inc/404.inc.php';
        } 
        else if ($privs == "CLIENT") {
            include ('inc/client.404.inc.php');
        } 
        else if ($privs == "GUEST") {
            include 'inc/auth.php';
        }
    }
}
?>