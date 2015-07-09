<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 05.07.15
 * Time: 23:15
 */
include('db.php');
$reg = $_REQUEST['reg'];
$catid = $_REQUEST['catid'];
$answer = $_REQUEST['result'];

if(!is_null($reg) && !is_null($catid) && !is_null($answer)) {
    $con = mysql_connect($host, $user, $passwd);
    if(!$con) { die('{"status":false,"message":"Error connection to db"}'); }
    mysql_select_db($db);
    $query = 'select * from registration where id = "' . $reg . '"';
    if($result = mysql_query($query)) {
        if(mysql_num_rows($result) == 1) {
            $query = "insert into results values(0, '$reg', $catid, ".($answer=='false'?"0, 1":"1, 0").")";
            if(mysql_query($query)) {
                echo '{"status":true,"message":"'.$answer.'"}';
            } else {
                echo '{"status":false,"message":"Error saving statistic"}';
            };
        } else {
            echo '{"status":false,"message":"Code not found"}';
        }
    } else { echo '{"status":false,"message":"Error executing query"}'; }
    mysql_close($con);
} else {
    echo '{"status":false,"message":"not enough of parameters"}';
}
?>