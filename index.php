<?php
session_start();

include_once ("conf.php");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (!isset($CONF_DB)) {
    include "sys/install.php";
    exit(0);
}


include_once ("functions.inc.php");

include_once ('library/AltoRouter/AltoRouter.php');
include_once ("app/core/route_actions.php");
include_once ("app/core/route_lists.php");

?>
