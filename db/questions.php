<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 02.07.15
 * Time: 23:21
 */

include('db.php');
$catid = $_REQUEST['catid'];
$con = mysql_connect($host, $user, $passwd);
if(!$con) { die('{"status":false,"message":"Error connection to db"}'); }
mysql_select_db($db);
$query = 'select * from questions where catid = ' . $catid;
//$query = 'select * from questions where catid = "' . $catid . '" order by rand() limit 1';
if($result = mysql_query($query)) {
    $array = array();
    while($r = mysql_fetch_assoc($result)) {
        array_push($array, $r);
    }
    echo '{"status":true,"result":'.json_encode($array).'}';
} else { echo '{"status":false,"message":"Error executing query to db"}'; }
mysql_close($con);
?>