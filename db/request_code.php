<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 03.07.15
 * Time: 22:39
 */
include('db.php');
$code = $_REQUEST['code'];

$con = mysql_connect($host, $user, $passwd);
if(!$con) { die('{"status":false,"message":"Error connection to db"}'); }
mysql_select_db($db);
$query = 'select * from registration where code = "' . $code . '"';
if($result = mysql_query($query)) {
    if(mysql_num_rows($result) == 1) {
        $res = mysql_fetch_assoc($result);
        echo '{"status":true,"code":' . $res['code'] . ',"code_id":"' . $res['id'] . '","email":"' . $res['email'] . '"}';
    } else {
        echo '{"status":false,"message":"Code not found"}';
    }
} else { echo '{"status":false,"message":"Error executing query to db"}'; }
mysql_close($con);
?>