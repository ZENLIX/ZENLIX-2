<?php
function lang_en($phrase){
    static $lang = array(


    	);
    return isset($lang[$phrase]) ? $lang[$phrase] : 'undefined';
}
?>