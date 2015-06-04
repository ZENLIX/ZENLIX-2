<?php
 
function get_privs() {
    $val_status = 'GUEST';
    

if (isset($_SESSION['helpdesk_user_id'],$_SESSION['code'])) {

    if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
        $val_status = 'USER';
    } 
    else if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
        $val_status = 'CLIENT';
    }
}
else {
    $val_status='GUEST';
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
    



if (!isset($_POST['remember_me'])) {
    $_POST['remember_me']=NULL;
}


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
//                        $_SESSION['helpdesk_user_login'] = $row['login'];
//                        $_SESSION['helpdesk_user_fio'] = $row['fio'];
//                        $_SESSION['helpdesk_user_type'] = "user";
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
    


//if ()


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
        include 'app/models/auth.php';
    }
    
    /////////////////END IF LOGIN-AUTH FORM SUBMITTED/////////////////////////
    
}

function indexAction() {
    
    //echo "ok";
    
    global $dbConnection, $CONF, $CONF_MAIL;
    $privs = get_privs();
    $portalStatus = get_portal_status();
    
    if ($privs == "GUEST") {
        
        if ($portalStatus) {
            require 'app/main_portal/models/index.php';
        } 
        else if (!$portalStatus) {
            require 'app/models/auth.php';
        }
    } 
    else if (($privs == "USER")) {
        
        if ($portalStatus) {
            require 'app/main_portal/models/index.php';
        } 
        else if (!$portalStatus) {
            require ("app/models/dashboard.php");
        }
    } 
    else if (($privs == "CLIENT")) {
        
        if ($portalStatus) {
            require 'app/main_portal/models/index.php';
        } 
        else if (!$portalStatus) {
            require ("app/models/client.dashboard.php");
        }
    }
}

function dashboardAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "GUEST") {
        include ('app/models/auth.php');
    } 
    else if (($privs == "CLIENT")) {
        require 'app/models/client.dashboard.php';
    } 
    else if (($privs == "USER")) {
        require 'app/models/dashboard.php';
    }
}

//class regi extends Controller  {

function registerAction() {
    global $dbConnection, $CONF;
    
    $privs = get_privs();
    $portalStatus = get_portal_status();
    
    if ($privs == "GUEST") {
        
        if ($portalStatus) {
            require ('app/main_portal/models/register.php');
        } 
        else if (!$portalStatus) {
            require ('app/models/register.php');
        }
    } 
    else if ($privs == "USER") {
        include ('app/models/404.inc.php');
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    }
}

function forgotAction() {
    global $dbConnection, $CONF;
    
    $privs = get_privs();
    $portalStatus = get_portal_status();
    
    if ($privs == "GUEST") {
        
        require ('app/models/forgot.php');
    } 
    else if ($privs == "USER") {
        include ('app/models/404.inc.php');
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
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
            include 'app/models/auth.php';
        } 
        else if ($portalStatus) {
            include 'app/main_portal/models/auth.php';
        }
    }
}

function listAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    $portalStatus = get_portal_status();
    
    if ($privs == "GUEST") {
        
        require 'app/models/auth.php';
    } 
    else if (($privs == "CLIENT")) {
        require ("app/models/client.list.php");
    } 
    else if (($privs == "USER")) {
        require ("app/models/list.php");
    }
}

function ticketAction() {
    
    global $dbConnection, $CONF;
    $privs = get_privs();
    
    if ($privs == "USER") {
        include 'app/models/ticket.php';
    } 
    else if ($privs == "CLIENT") {
        include 'app/models/client.ticket.php';
    } 
    else {
        include 'app/models/auth.php';
    }
}

function createAction() {
    
    global $dbConnection, $CONF;
    $privs = get_privs();
    
    if ($privs == "USER") {
        include 'app/models/new.php';
    } 
    else if ($privs == "CLIENT") {
        include 'app/models/client.new.php';
    } 
    else {
        include 'app/models/auth.php';
    }
}

function view_userAction() {
    
    global $dbConnection, $CONF;
    $privs = get_privs();
    
    if ($privs == "USER") {
        include 'app/models/view_user.php';
    } 
    else if ($privs == "CLIENT") {
        include 'app/models/client.view_user.php';
    } 
    else {
        include 'app/models/auth.php';
    }
}

function profileAction() {
    
    global $dbConnection, $CONF;
    $privs = get_privs();
    
    if ($privs == "USER") {
        include 'app/models/profile.php';
    } 
    else if ($privs == "CLIENT") {
        include 'app/models/client.profile.php';
    } 
    else {
        include 'app/models/auth.php';
    }
}

function helperAction() {
    
    global $dbConnection, $CONF;
    $privs = get_privs();
    
    if ($privs == "USER") {
        include 'app/models/helper.php';
    } 
    else if ($privs == "CLIENT") {
        include 'app/models/client.helper.php';
    } 
    else {
        include 'app/models/auth.php';
    }
}

function statsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/stats.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function notesAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/notes.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function usersAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/users.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function helpAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/help.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function depsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/deps.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function approveAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/approve.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function unitsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/units.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function posadaAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/posada.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function subjAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/subj.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function userinfoAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/userinfo.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function perfAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/perf.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function filesAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/files.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function newsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/news.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function clientsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/clients.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function all_statsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/all_stats.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function user_statsAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/user_stats.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function sla_repAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/sla_rep.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function schedulerAction() {
    global $dbConnection, $CONF,$CONF_MAIL;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/scheduler.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function messagesAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/messages.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.messages.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function print_ticketAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/print_ticket.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function calendarAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/calendar.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function portalAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/portal.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function mailersAction() {
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "USER") {
        include 'app/models/mailers.php';
    } 
    else if ($privs == "CLIENT") {
        include ('app/models/client.404.inc.php');
    } 
    else if ($privs == "GUEST") {
        include 'app/models/auth.php';
    }
}

function manualAction() {
    global $dbConnection, $CONF;
    $portal = get_portal_status();
    $privs = get_privs();
    if ($portal) {
        include 'app/main_portal/models/manual.php';
    } 
    else {
        if ($privs == "USER") {
            include 'app/models/404.inc.php';
        } 
        else if ($privs == "CLIENT") {
            include ('app/models/client.404.inc.php');
        } 
        else if ($privs == "GUEST") {
            include 'app/models/auth.php';
        }
    }
}

function versionAction() {
    global $dbConnection, $CONF;
    $portal = get_portal_status();
    $privs = get_privs();
    if ($portal) {
        include 'app/main_portal/models/version.php';
    } 
    else {
        if ($privs == "USER") {
            include 'app/models/404.inc.php';
        } 
        else if ($privs == "CLIENT") {
            include ('app/models/client.404.inc.php');
        } 
        else if ($privs == "GUEST") {
            include 'app/models/auth.php';
        }
    }
}

function feedAction() {
    global $dbConnection, $CONF;
    $portal = get_portal_status();
    $privs = get_privs();
    if ($portal) {
        include 'app/main_portal/models/feed.php';
    } 
    else {
        if ($privs == "USER") {
            include 'app/models/404.inc.php';
        } 
        else if ($privs == "CLIENT") {
            include ('app/models/client.404.inc.php');
        } 
        else if ($privs == "GUEST") {
            include 'app/models/auth.php';
        }
    }
}

function catAction() {
    global $dbConnection, $CONF;
    $portal = get_portal_status();
    $privs = get_privs();
    if ($portal) {
        include 'app/main_portal/models/cat.php';
    } 
    else {
        if ($privs == "USER") {
            include 'app/models/404.inc.php';
        } 
        else if ($privs == "CLIENT") {
            include ('app/models/client.404.inc.php');
        } 
        else if ($privs == "GUEST") {
            include 'app/models/auth.php';
        }
    }
}

function new_postAction() {
    global $dbConnection, $CONF;
    $portal = get_portal_status();
    $privs = get_privs();
    if ($portal) {
        
        if ($privs == "USER") {
            include 'app/main_portal/models/new_post.php';
        } 
        else if ($privs == "CLIENT") {
            include 'app/main_portal/models/new_post.php';
        } 
        else if ($privs == "GUEST") {
            include 'app/main_portal/models/auth.php';
        }
    }
    if (!$portal) {
        if ($privs == "USER") {
            include 'app/models/404.inc.php';
        } 
        else if ($privs == "CLIENT") {
            include ('app/models/client.404.inc.php');
        } 
        else if ($privs == "GUEST") {
            include 'app/models/auth.php';
        }
    }
}

function postAction() {
    global $dbConnection, $CONF;
    $portal = get_portal_status();
    $privs = get_privs();
    if ($portal) {
        include 'app/main_portal/models/post.php';
    } 
    else {
        if ($privs == "USER") {
            include 'app/models/404.inc.php';
        } 
        else if ($privs == "CLIENT") {
            include ('app/models/client.404.inc.php');
        } 
        else if ($privs == "GUEST") {
            include 'app/models/auth.php';
        }
    }
}
?>