
<?php
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(0);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <title>ZENLIX install</title>


</head>



<link rel="stylesheet" href="js/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="js/bootstrap/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="css/jquery-ui.min.css">

<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">

<style type="text/css" media="all">
    .chosen-rtl .chosen-drop { left: -9000px; }
    /* Space out content a bit */
body {
  padding-top: 20px;
  padding-bottom: 20px;
}

/* Everything but the jumbotron gets side spacing for mobile first views */
.header,
.marketing,
.footer {
  padding-right: 15px;
  padding-left: 15px;
}

/* Custom page header */
.header {
  border-bottom: 1px solid #e5e5e5;
}
/* Make the masthead heading the same height as the navigation */
.header h3 {
  padding-bottom: 19px;
  margin-top: 0;
  margin-bottom: 0;
  line-height: 40px;
}

/* Custom page footer */
.footer {
  padding-top: 19px;
  color: #777;
  border-top: 1px solid #e5e5e5;
}

/* Customize container */
@media (min-width: 768px) {
  .container {
    max-width: 730px;
  }
}
.container-narrow > hr {
  margin: 30px 0;
}

/* Main marketing message and sign up button */
.jumbotron {
  text-align: center;
  border-bottom: 1px solid #e5e5e5;
}
.jumbotron .btn {
  padding: 14px 24px;
  font-size: 21px;
}

/* Supporting marketing content */
.marketing {
  margin: 40px 0;
}
.marketing p + h4 {
  margin-top: 28px;
}

/* Responsive: Portrait tablets and up */
@media screen and (min-width: 768px) {
  /* Remove the padding we set earlier */
  .header,
  .marketing,
  .footer {
    padding-right: 0;
    padding-left: 0;
  }
  /* Space out the masthead */
  .header {
    margin-bottom: 30px;
  }
  /* Remove the bottom border on the jumbotron for visual effect */
  .jumbotron {
    border-bottom: 0;
  }
}

</style>

<body>

<?php
if (isset($_POST['mode'])) {
?>

		<div class="container" id="content">
		<div class="page-header">
  <h1><img src="img/ZENLIX_small.png"> ZENLIX <small>installation system (3 step from 3)</small></h1>
</div>
		<div class="row">
		
		<div class="col-md-12">
		<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Installation result</h3>
  </div>
  <div class="panel-body">

<?php
// Name of the file
$filename = realpath(dirname(dirname(__FILE__))).'/DB/HD.db.sql';
$fileconf = realpath(dirname(dirname(__FILE__))).'/conf.php';
$nodeserver = realpath(dirname(dirname(__FILE__))).'/nodejs/server.js';
// MySQL host
$mysql_host = $_POST['host'];
// MySQL username
$mysql_username = $_POST['username'];
// MySQL password
$mysql_password = $_POST['password'];
// Database name
$mysql_database = $_POST['db'];

$nodeport=$_POST['nodeport'];
// Connect to MySQL server
mysql_connect($mysql_host, $mysql_username, $mysql_password) or die('Error connecting to MySQL server: ' . mysql_error());
// Select database
mysql_select_db($mysql_database) or die('Error selecting MySQL database: ' . mysql_error());

// Temporary variable, used to store current query
$templine = '';
// Read in entire file
$lines = file($filename);
// Loop through each line
foreach ($lines as $line)
{
// Skip it if it's a comment
if (substr($line, 0, 2) == '--' || $line == '')
    continue;

// Add this line to the current segment
$templine .= $line;
// If it has a semicolon at the end, it's the end of the query
if (substr(trim($line), -1, 1) == ';')
{
    // Perform the query
    mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
    // Reset temp variable to empty
    $templine = '';
}
}


$current .= "<?php\n";
$current .= "########################################\n";
$current .= "#	ZENLIX - configuration file\n";
$current .= "#	ZENLIX (c) 2015\n";
$current .= "#	support@zenlix.com\n";
$current .= "########################################\n";

$current .= "//Access information to MySQL database\n";
$current .= '$CONF_DB'." = array (\n";
$current .= "	'host'      => '".$mysql_host."', \n";
$current .= "	'username'  => '".$mysql_username."',\n";
$current .= "	'password'	=> '".$mysql_password."',\n";
$current .= "	'db_name'	  => '".$mysql_database."'\n";
$current .= ");\n";

$current .= "//System configuration variables and some options\n";
$current .= '$CONF_HD'." = array (\n";
$current .= "	'debug_mode'	=> false\n";
$current .= ");\n";


$current .= "?>\n";
file_put_contents($fileconf, $current);


$node_params.="var mysql = require('mysql'); \n var db = mysql.createConnection({\n";
$node_params.="host: '".$mysql_host."',\n";
$node_params.="user: '".$mysql_username."',\n";
$node_params.="password: '".$mysql_password."',\n";
$node_params.="database: '".$mysql_database."'});\n";
$node_params.="var io = require('/usr/local/lib/node_modules/socket.io').listen(".$nodeport.");\n";
//var io = require('/usr/local/lib/node_modules/socket.io').listen(8080);
$node_params.=file_get_contents($nodeserver);
file_put_contents($nodeserver, $node_params);





$pos = strrpos($_SERVER['REQUEST_URI'], '/');
$sys_url= $_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'], 0, $pos + 1);
$lc=substr($sys_url, -1);
if ($lc == "/") { $c=substr($sys_url, 0, -1); }
//$p="http://".$c.":".$nodeport."/";
$p="http://".$c."/";

mysql_query("update perf set value='$sys_url' where param='hostname'") or die("Invalid query: " . mysql_error());
mysql_query("update perf set value='$p' where param='node_port'") or die("Invalid query: " . mysql_error());
?>
<h2>Congratulations on the successful installation!</h2>
<p>
You can log in at: <a href="http://<?=$sys_url;?>"><?=$sys_url;?></a>,<br> login: <strong>system</strong> & password: <strong>1234</strong>.
</p>

<br>


<a href="http://<?=$sys_url;?>" class="btn btn-success btn-lg btn-block">Log In</a>


  </div>
		</div>
		</div>
		</div>
		</div>

<?php
}
else if (!isset($_POST['mode'])) {
if (isset($_GET['mode'])) {
		if ($_GET['mode'] == 'db_install' ) { 








      //echo "last= ".$p;



      ?>
		
		<div class="container" id="content">
		<div class="page-header">
  <h1><img src="img/ZENLIX_small.png"> ZENLIX <small>prepare to install (2 step from 3)</small></h1>
</div>
		<div class="row">
		
		<div class="col-md-12">
		<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">DB connection preferences</h3>
  </div>
  <div class="panel-body">

<form class="form-horizontal" role="form" action="index.php" method="post">
    
    <div class="form-group">
    <label for="host" class="col-sm-4 control-label"><small>Host MySQL-server</small></label>
    <div class="col-sm-8">
<input type="text" class="form-control input-sm" id="host" name="host" placeholder="ex. localhost" value="">

   </div>
  </div>

    <div class="form-group">
    <label for="username" class="col-sm-4 control-label"><small>Login</small></label>
    <div class="col-sm-8">
<input type="text" class="form-control input-sm" id="username" name="username" placeholder="ex. zenlix_user" value="">

   </div>
  </div>
  
  
  
      <div class="form-group">
    <label for="password" class="col-sm-4 control-label"><small>Password</small></label>
    <div class="col-sm-8">
<input type="password" class="form-control input-sm" id="password" name="password" placeholder="ex. pass" value="">

   </div>
  </div>
  
  
        <div class="form-group">
    <label for="db" class="col-sm-4 control-label"><small>DB name</small></label>
    <div class="col-sm-8">
<input type="text" class="form-control input-sm" id="db" name="db" placeholder="ex. zenlix_db" value="">

   </div>
  </div>
  
  
          <div class="form-group">
    <label for="nodeport" class="col-sm-4 control-label"><small>NODE-JS PORT</small></label>
    <div class="col-sm-8">
<input type="text" class="form-control input-sm" id="nodeport" name="nodeport" placeholder="ex. 3001" value="">

   </div>
  </div>
  

<center>
<input type="hidden" name="mode" value="1">
<button class="btn btn-lg btn-success" href="" role="button"><i class="fa fa-chevron-circle-right"></i> Install</button>
</center>
</form>


  </div>
		</div>
		</div>
		</div>
		</div>


		
		<?php
		
		}
	if ($_GET['mode'] == 'check_install' ) { 
		
		?>
		<div class="container" id="content">
		<div class="page-header">
  <h1><img src="img/ZENLIX_small.png"> ZENLIX <small>prepare to install (1 step from 3)</small></h1>
</div>
		<div class="row">
		
		<div class="col-md-12">
		<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Checking...</h3>
  </div>
  <div class="panel-body">
    <table class="table">

<tbody>

            <tr>
                <td>PHP short_open_tag</td>
                <td width="100px;">
	                
	                <?php if(ini_get('short_open_tag') == false) {?> 
	                <span class="label label-danger">not active</span>
	                <div class="alert alert-danger" role="alert">PHP-error: <em>short_open_tag</em> must be enable in your php configuration. <br> Details: <a href="http://php.net//manual/ru/language.basic-syntax.phptags.php">http://php.net//manual/ru/language.basic-syntax.phptags.php</a></div>
	                 <?php  } ?>
	                <?php if(ini_get('short_open_tag') == true) {?><span class="label label-success">Success</span> <?php } ?>
                </td>
            </tr>
            
            <tr>
                <td>File .htaccess</td>
                <td width="100px;">
	                <?php
    $filename=realpath(dirname(dirname(__FILE__)))."/.htaccess";
    if (!file_exists($filename)) { ?>
    <span class="label label-danger">file not found</span>
    <div class="alert alert-danger" role="alert">
    
    In directory <?=realpath(dirname(dirname(__FILE__)))?> must create .htaccess with content:
    <code>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?page=$1

    </code>
    
    </div>
    <?php } 
	    if (file_exists($filename)) {
	    
    ?>
    <span class="label label-success">Success</span>
    <?php } ?>
    
                </td>
            </tr>
            
            <tr>
                <td>PDO check</td>
                <td width="100px;">
	                <?php if (defined('PDO::ATTR_DRIVER_NAME')) {?>
<span class="label label-success">Success</span>
<?php } if (!defined('PDO::ATTR_DRIVER_NAME')) {?>
    <span class="label label-danger">not active</span>
	        <?php } ?>            
	                
                </td>
            </tr>










             <tr>
                <td>File of configuration DB</td>
                <td width="100px;">
	                <?php
    $filename=realpath(dirname(dirname(__FILE__)))."/conf.php";
    if (!is_writable($filename)) { ?>
    <span class="label label-danger">not active</span>
    <div class="alert alert-danger" role="alert">You must create conf.php file in root web-directory or permission-error: <em><?=$filename?></em> is not writable. <br> Add access to write.</a>
    </div>
    <?php } if (is_writable($filename)) {?>
    <span class="label label-success">Success</span>
    <?php } ?>
                </td>
            </tr>
            
            
            <tr>
                <td>File uploads directory</td>
                <td width="100px;">
	                <?php
    $filename=realpath(dirname(dirname(__FILE__)))."/upload_files/";
    if (!is_writable($filename)) { ?>
    <span class="label label-danger">not active</span>
    <div class="alert alert-danger" role="alert">Permission-error: <em><?=$filename?></em> is not writable. <br> Add access to write.</a></div>
    <?php } if (is_writable($filename)) {?>
    <span class="label label-success">Success</span>
    <?php } ?>
                </td>
            </tr>
            
            <tr>
                <td>File uploads user_content directory</td>
                <td width="100px;">
	                <?php
    $filename=realpath(dirname(dirname(__FILE__)))."/upload_files/user_content";
    if (!is_writable($filename)) { ?>
    <span class="label label-danger">not active</span>
    <div class="alert alert-danger" role="alert">Permission-error: <em><?=$filename?></em> is not writable. <br> Add access to write.</a></div>
    <?php } if (is_writable($filename)) {?>
    <span class="label label-success">Success</span>
    <?php } ?>
                </td>
            </tr>
            
            
                        <tr>
                <td>File uploads avatars directory</td>
                <td width="100px;">
                  <?php
    $filename=realpath(dirname(dirname(__FILE__)))."/upload_files/avatars";
    if (!is_writable($filename)) { ?>
    <span class="label label-danger">not active</span>
    <div class="alert alert-danger" role="alert">Permission-error: <em><?=$filename?></em> is not writable. <br> Add access to write.</a></div>
    <?php } if (is_writable($filename)) {?>
    <span class="label label-success">Success</span>
    <?php } ?>
                </td>
            </tr>           



             <tr>
                <td>Cache directory</td>
                <td width="100px;">
                  <?php
    $filename=realpath(dirname(dirname(__FILE__)))."/inc/cache";
    if (!is_writable($filename)) { ?>
    <span class="label label-danger">not active</span>
    <div class="alert alert-danger" role="alert">Permission-error: <em><?=$filename?></em> is not writable. <br> Add access to write.</a></div>
    <?php } if (is_writable($filename)) {?>
    <span class="label label-success">Success</span>
    <?php } ?>
                </td>
            </tr>           
            
</tbody>
            

</table>
<center>
<a class="btn btn-lg btn-success" href="index.php?mode=db_install" role="button"><i class="fa fa-chevron-circle-right"></i>  Next</a>
</center>

  </div>
</div>
		</div>
		
		</div>
		</div>
		<?php
	}
	
	
	
	
	
}
else if (!isset($_GET['mode'])) {
?>
<div class="container" id="content">
      
<style type="text/css">
  pre {
    white-space: pre-wrap;       /* CSS 3 */
    white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
    white-space: -pre-wrap;      /* Opera 4-6 */
    white-space: -o-pre-wrap;    /* Opera 7 */
    word-wrap: break-word;       /* Internet Explorer 5.5+ */
}
</style>
      <div class="jumbotron">
      <img src="img/ZENLIX.png">
        <h1>ZENLIX </h1>
        <p class="lead">ZENLIX web ticket system for accounting tasks </p>
        
        
        
      </div>      


<center>LICENSE AGREEMENTS</center>
         <pre>
        Copyright (c) 2014 YAROSLAV SNISAR

This license authorizes persons who have received a copy of this software and associated documentation files (hereinafter referred to as "Software"), to use the Software, namely the right to use, copy, modify, add copies of the Software. Is strictly prohibited publication, distribute, sublicense, and / or sell copies of the Software, as well as persons to whom the Software is furnished.
All of the above, the rules regarding rights are subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT, BUT NOT LIMITED TO. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIMS FOR DAMAGES OR OTHER REQUIREMENTS, INCLUDING UNDER CONTRACT, TORT OR ANY SITUATION ARISING FROM THE USE OF THE SOFTWARE OR OTHER ACTION WITH THE SOFTWARE.
        </pre>


<p><a class="btn btn-lg btn-success btn-block" href="index.php?mode=check_install" role="button">I agree with license & start install</a></p>


    </div>

<?php } }?>

<div id="footer" style="  ">
    <div class="container" style=" padding: 20px; ">
        <div class="col-md-8">
            <p class="text-muted credit"><small>Designed by <a href="mailto:info@zenlix.com">ZENLIX</a> (с) 2015.</p>
            </small>
        </div>

        <div class="col-md-4">

        </div>
    </div>
</div>
<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/bootstrap/js/bootstrap.min.js"></script>

<script src="js/jquery-ui-1.10.4.custom.min.js"></script>

</body>
</html>
