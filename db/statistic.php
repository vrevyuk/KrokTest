<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 09.07.15
 * Time: 22:20
 */
include('db.php');

$regID = $_REQUEST['reg_id'];

$con = mysql_connect($host, $user, $passwd);
if(!$con) { die('{"status":false,"message":"Error connection to db"}'); }
mysql_select_db($db);
$query = "SELECT c.id, c.catname, SUM( r.success ) AS success, SUM( r.failed ) AS failed
          FROM results AS r, categories AS c
          WHERE r.registration = $regID
          AND r.categories = c.id
          GROUP BY r.categories";
//echo $query;
if($result = mysql_query($query)) {
    $array = array();
    while($r = mysql_fetch_assoc($result)) {
        array_push($array, $r);
    }
    echo '{"status":true,"result":'.json_encode($array).'}';
} else { echo '{"status":false,"message":"Error executing query to db"}'; }
mysql_close($con);
?>
