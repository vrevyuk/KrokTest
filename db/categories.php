<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 02.07.15
 * Time: 23:21
 */

include('db.php');

$con = mysql_connect($host, $user, $passwd);
if(!$con) { die('{"status":false,"message":"Error connection to db"}'); }
mysql_select_db($db);
$query = 'select * from categories';
if($result = mysql_query($query)) {
    $array = array();
    while($r = mysql_fetch_assoc($result)) {
        array_push($array, $r);
    }
} else { echo '{"status":false,"message":"Error executing query to db"}'; }
echo '{"status":true,"result":'.json_encode($array).'}';
mysql_close($con);
?>
