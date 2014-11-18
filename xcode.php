<?php

$arr = array(
'version' => '2.9', 
'msg' => 'New version 2.7 is available!<br> You can update it!',
'change_log'=>'Закрыты старые баги | Добавлена возможность обновления системы | И много другое',
'from_version'=>'2.0,2.1,2.2',
'files_list'=>'/some.php|/some2.php|/updates/some.php'

);



print json_encode($arr);

?>